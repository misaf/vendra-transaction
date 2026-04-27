<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Actions;

use Exception;
use Illuminate\Support\Facades\DB;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraUser\Models\User;
use Spatie\QueueableAction\QueueableAction;

final class TransactionTransfersAction
{
    use QueueableAction;

    public function execute(User $sourceUser, User $destinationUser, int $amount): void
    {
        $this->checkDifferentUsers($sourceUser, $destinationUser);
        $this->validateAmount($amount);

        DB::transaction(function () use ($sourceUser, $destinationUser, $amount): void {
            $transaction = $this->createTransaction($sourceUser, $destinationUser, $amount);
            $this->createTransactionTransfer($transaction, $destinationUser);
        }, 5);
    }

    private function checkDifferentUsers(User $sourceUser, User $destinationUser): void
    {
        if ($sourceUser->is($destinationUser)) {
            throw new Exception('Source and destination users must be different.');
        }
    }

    private function validateAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('The transfer amount must be greater than zero.');
        }
    }

    private function createTransaction(User $sourceUser, User $destinationUser, int $amount): Transaction
    {
        $transaction = TransactionService::createTransaction(
            transactionGateway: 'internal-transactions',
            user: $sourceUser,
            transactionType: TransactionTypeEnum::Transfer,
            amount: -abs($amount),
            status: TransactionStatusEnum::Pending,
            metadatas: collect($destinationUser)->only('username')->toArray(),
        );

        return $transaction;
    }

    private function createTransactionTransfer(Transaction $transaction, User $destinationUser): void
    {
        $transaction->transactionTransfers()->create([
            'user_id' => $destinationUser->id,
        ]);
    }
}
