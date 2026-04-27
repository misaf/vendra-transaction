<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraUser\Models\User;

final class TransactionService
{
    private const TOKEN_CHARACTERS = '123456789';

    private const TOKEN_LENGTH = 20;

    public function generateToken(): string
    {
        return mb_substr(str_shuffle(str_repeat(self::TOKEN_CHARACTERS, self::TOKEN_LENGTH)), 0, self::TOKEN_LENGTH);
    }

    public function getFormattedAmount(int $amount, string $transactionType): int
    {
        $transactionTypeEnum = TransactionTypeEnum::from($transactionType);

        return TransactionTypeEnum::Withdrawal === $transactionTypeEnum
            ? -abs($amount)
            : abs($amount);
    }

    public function updateTransactionStatus(Transaction $transaction, TransactionStatusEnum $newStatus): bool
    {
        $transaction->loadMissing('user:id,username');

        return DB::transaction(function () use ($transaction, $newStatus): bool {
            $lockedTransaction = Transaction::lockForUpdate()->find($transaction->id);

            $type = $transaction->transaction_type->value;
            $token = $transaction->token;
            $username = $transaction->user->username ?? 'unknown';
            $status = $newStatus->value;

            $logPrefix = '[TransactionStatusUpdate]';

            if ( ! $lockedTransaction) {
                Log::info("{$logPrefix} [{$type}] Token: {$token}, User: {$username} - Transaction no longer exists. Skipping update.");

                return false;
            }

            if ($lockedTransaction->status === $newStatus) {
                Log::info("{$logPrefix} [{$type}] Token: {$token}, User: {$username} - Status already '{$status}'. Skipping update.");

                return false;
            }

            $affectedRows = $lockedTransaction->update(['status' => $newStatus]);

            if ( ! $affectedRows) {
                Log::warning("{$logPrefix} [{$type}] Token: {$token}, User: {$username} - Failed to update to status '{$status}'.");

                return false;
            }

            Log::info("{$logPrefix} [{$type}] Token: {$token}, User: {$username} - Successfully updated to status '{$status}'.");

            return true;
        }, 5);
    }

    public function isApproved(Transaction $transaction): bool
    {
        return TransactionStatusEnum::Approved === $transaction->status;
    }

    public function isDeclined(Transaction $transaction): bool
    {
        return TransactionStatusEnum::Declined === $transaction->status;
    }

    public function isFailed(Transaction $transaction): bool
    {
        return TransactionStatusEnum::Failed === $transaction->status;
    }

    public function isPending(Transaction $transaction): bool
    {
        return TransactionStatusEnum::Pending === $transaction->status;
    }

    public function isReview(Transaction $transaction): bool
    {
        return TransactionStatusEnum::Review === $transaction->status;
    }

    public function isProcessing(Transaction $transaction): bool
    {
        return TransactionStatusEnum::Processing === $transaction->status;
    }

    public function isDeposit(Transaction $transaction): bool
    {
        return TransactionTypeEnum::Deposit === $transaction->transaction_type;
    }

    public function isWithdrawal(Transaction $transaction): bool
    {
        return TransactionTypeEnum::Withdrawal === $transaction->transaction_type;
    }

    public function isCommission(Transaction $transaction): bool
    {
        return TransactionTypeEnum::Commission === $transaction->transaction_type;
    }

    public function isBonus(Transaction $transaction): bool
    {
        return TransactionTypeEnum::Bonus === $transaction->transaction_type;
    }

    public function isTransfer(Transaction $transaction): bool
    {
        return TransactionTypeEnum::Transfer === $transaction->transaction_type;
    }

    public function sumDeposits(User $user): int
    {
        return (int) $user->transactions()
            ->deposit()
            ->approved()
            ->sum('amount');
    }

    public function sumWithdrawals(User $user): int
    {
        return abs((int) $user->transactions()
            ->withdrawal()
            ->approved()
            ->sum('amount'));
    }

    public function sumCommissions(User $user): int
    {
        return abs((int) $user->transactions()
            ->commission()
            ->approved()
            ->sum('amount'));
    }

    public function sumBonuses(User $user): int
    {
        return abs((int) $user->transactions()
            ->bonus()
            ->approved()
            ->sum('amount'));
    }

    public function hasAnyActiveTransactionGateway(): bool
    {
        return (bool) TransactionGateway::query()
            ->whereJsonContainsLocale('slug', app()->getLocale(), 'internal-transactions', '<>')
            ->where('status', true)
            ->exists();
    }

    public function hasActiveTransactionGateway(string $slug): bool
    {
        return (bool) TransactionGateway::query()
            ->whereJsonContainsLocale('slug', app()->getLocale(), $slug, '=')
            ->where('status', true)
            ->exists();
    }

    public function getTransactionGateway(string $transactionGateway): TransactionGateway
    {
        $locale = app()->getLocale();

        $result = TransactionGateway::query()
            ->whereJsonContains('slug', [$locale => $transactionGateway])
            ->where('status', true)
            ->first();

        if ( ! $result) {
            throw new Exception('No active transaction gateway found.');
        }

        return $result;
    }

    public function isInternalTransaction(Transaction $transaction): bool
    {
        $internalGateway = TransactionService::getTransactionGateway('internal-transactions');

        return $internalGateway->is($transaction->transactionGateway);
    }

    /**
     * @param  array<string, mixed>  $metadatas
     */
    public function createTransaction(string $transactionGateway, User $user, TransactionTypeEnum $transactionType, int $amount, TransactionStatusEnum $status, array $metadatas = [], ?string $token = null): Transaction
    {
        $transactionGatewayId = $this->getTransactionGateway($transactionGateway)->id;

        return DB::transaction(function () use ($transactionGatewayId, $transactionType, $user, $token, $amount, $status, $metadatas): Transaction {
            $attributes = [
                'transaction_gateway_id' => $transactionGatewayId,
                'user_id'                => $user->id,
                'transaction_type'       => $transactionType,
                'amount'                 => $amount,
                'status'                 => $status,
            ];

            if ( ! empty($token)) {
                $attributes['token'] = $token;
            }

            $transaction = Transaction::create($attributes);

            if ( ! empty($metadatas)) {
                $this->createTransactionMetadatas($transaction, $metadatas);
            }

            return $transaction;
        });
    }

    /**
     * @param  array<string, mixed>  $metadatas
     */
    public function createTransactionMetadatas(Transaction $transaction, array $metadatas): void
    {
        $transaction->transactionMetadatas()->createMany($this->buildTransactionMetadatas($metadatas));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<array<string, mixed>>
     */
    private function buildTransactionMetadatas(array $data): array
    {
        return array_map(
            fn($key) => [
                'key_name'  => $key,
                'key_value' => $data[$key] ?? '',
            ],
            array_keys($data),
        );
    }
}
