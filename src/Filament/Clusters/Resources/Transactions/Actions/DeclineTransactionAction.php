<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions;

use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;

final class DeclineTransactionAction
{
    public static function make(): Action
    {
        return Action::make(TransactionStatusEnum::Declined->value)
            ->action(function (Transaction $record): void {
                TransactionService::updateTransactionStatus($record, TransactionStatusEnum::Declined);
            })
            ->color(Color::Red)
            ->icon(Heroicon::NoSymbol)
            ->label(__('vendra-transaction::messages.decline_transaction'))
            ->requiresConfirmation()
            ->visible(function (Transaction $record): bool {
                return TransactionService::isWithdrawal($record)
                    && TransactionService::isReview($record);
            });
    }
}
