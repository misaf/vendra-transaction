<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Misaf\VendraSupport\Support\CurrencyIntegration;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Exceptions\TransactionLimitExceededException;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraTransaction\Models\TransactionLimit;
use Misaf\VendraTransaction\Models\Wallet;
use RuntimeException;

final class TransactionService
{
    public const string INTERNAL_GATEWAY_SLUG = 'internal-transactions';

    private const string TOKEN_CHARACTERS = '123456789';

    private const int TOKEN_LENGTH = 20;

    public function generateToken(): string
    {
        $maxIndex = mb_strlen(self::TOKEN_CHARACTERS) - 1;
        $token = '';

        for ($i = 0; $i < self::TOKEN_LENGTH; $i++) {
            $token .= self::TOKEN_CHARACTERS[random_int(0, $maxIndex)];
        }

        return $token;
    }

    /**
     * The wallet holding the given user's balance in the given currency,
     * created on first use.
     */
    public function walletFor(Model $user, string $currencyCode): Wallet
    {
        return Wallet::query()->firstOrCreate([
            'user_id'       => $user->getKey(),
            'currency_code' => Str::upper($currencyCode),
        ]);
    }

    /**
     * The wallet for the given user in the application's default currency,
     * created on first use.
     */
    public function defaultWalletFor(Model $user): Wallet
    {
        return $this->walletFor($user, CurrencyIntegration::defaultCode());
    }

    public function hasAnyActiveTransactionGateway(): bool
    {
        return TransactionGateway::query()
            ->enabled()
            ->where('slug', '<>', self::INTERNAL_GATEWAY_SLUG)
            ->exists();
    }

    public function hasActiveTransactionGateway(string $slug): bool
    {
        return TransactionGateway::query()
            ->enabled()
            ->where('slug', $slug)
            ->exists();
    }

    public function getTransactionGateway(string $slug): TransactionGateway
    {
        $gateway = TransactionGateway::query()
            ->enabled()
            ->where('slug', $slug)
            ->first();

        if (null === $gateway) {
            throw new RuntimeException("No active transaction gateway found for slug [{$slug}].");
        }

        return $gateway;
    }

    public function isInternalTransaction(Transaction $transaction): bool
    {
        return $this->getTransactionGateway(self::INTERNAL_GATEWAY_SLUG)->is($transaction->transactionGateway);
    }

    /**
     * Creates a pending transaction against a wallet, together with its
     * optional fee and metadata rows. Settlement into the ledger only
     * happens later, on the transition to Approved.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function createTransaction(
        TransactionGateway|string $transactionGateway,
        Wallet $wallet,
        TransactionTypeEnum $transactionType,
        int $amount,
        array $metadata = [],
        ?int $fee = null,
        ?Wallet $counterpartyWallet = null,
        ?string $token = null,
    ): Transaction {
        $gateway = $transactionGateway instanceof TransactionGateway
            ? $transactionGateway
            : $this->getTransactionGateway($transactionGateway);

        $this->assertWithinLimit($wallet, $transactionType, $amount);

        return DB::transaction(function () use ($gateway, $wallet, $transactionType, $amount, $metadata, $fee, $counterpartyWallet, $token): Transaction {
            $attributes = [
                'wallet_id'              => $wallet->id,
                'transaction_gateway_id' => $gateway->id,
                'counterparty_wallet_id' => $counterpartyWallet?->id,
                'transaction_type'       => $transactionType,
                'amount'                 => abs($amount),
            ];

            if ( ! empty($token)) {
                $attributes['token'] = $token;
            }

            $transaction = Transaction::create($attributes);

            if (null !== $fee && $fee > 0) {
                $transaction->transactionFee()->create(['amount' => $fee]);
            }

            if ( ! empty($metadata)) {
                $this->createTransactionMetadata($transaction, $metadata);
            }

            return $transaction;
        });
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function createTransactionMetadata(Transaction $transaction, array $metadata): void
    {
        $transaction->transactionMetadatas()->createMany(array_map(
            fn(string $key): array => [
                'key_name'  => $key,
                'key_value' => (string) ($metadata[$key] ?? ''),
            ],
            array_keys($metadata),
        ));
    }

    public function limitFor(Wallet $wallet, TransactionTypeEnum $transactionType): ?TransactionLimit
    {
        return $wallet->transactionLimits()
            ->where('transaction_type', $transactionType)
            ->first();
    }

    private function assertWithinLimit(Wallet $wallet, TransactionTypeEnum $transactionType, int $amount): void
    {
        $limit = $this->limitFor($wallet, $transactionType);

        if (null !== $limit && abs($amount) > $limit->amount) {
            throw TransactionLimitExceededException::forWallet($wallet->id, $transactionType, abs($amount), $limit->amount);
        }
    }
}
