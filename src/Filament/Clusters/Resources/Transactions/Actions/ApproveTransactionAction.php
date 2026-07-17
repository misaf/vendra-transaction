<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;

final class ApproveTransactionAction
{
    public static function make(): Action
    {
        return Action::make(TransactionStatusEnum::Approved->value)
            ->action(function (Transaction $record): void {
                TransactionService::updateTransactionStatus($record, TransactionStatusEnum::Processing);
            })
            ->icon(Heroicon::Pencil)
            ->label(__('vendra-transaction::messages.approve_transaction'))
            ->requiresConfirmation()
            ->visible(function (Transaction $record): bool {
                return TransactionService::isWithdrawal($record)
                    && TransactionService::isReview($record);
            });
    }
}
