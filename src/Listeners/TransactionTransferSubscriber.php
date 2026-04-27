<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Listeners;

use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraUser\Models\User;

final class TransactionTransferSubscriber implements ShouldQueueAfterCommit
{
    use InteractsWithQueue;

    public function transactionUpdated(Transaction $transaction): void
    {
        $isTransferApproved = TransactionService::isTransfer($transaction) && TransactionService::isApproved($transaction);
        if ( ! $isTransferApproved) {
            return;
        }

        $transactionTransfers = $transaction->transactionTransfers;
        foreach ($transactionTransfers as $transactionTransfer) {
            if ( ! ($transactionTransfer->user instanceof User)) {
                continue;
            }

            TransactionService::createTransaction(
                transactionGateway: 'internal-transactions',
                user: $transactionTransfer->user,
                transactionType: TransactionTypeEnum::Transfer,
                amount: abs($transaction->amount),
                status: TransactionStatusEnum::Pending,
            );
        }
    }

    /**
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            'eloquent.updated: ' . Transaction::class => 'transactionUpdated',
        ];
    }
}
