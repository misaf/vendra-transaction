<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;

final class WithdrawalInfoAction
{
    public static function make(): Action
    {
        return Action::make('withdrawal-info')
            ->icon(Heroicon::Eye)
            ->label(__('vendra-transaction::messages.transaction_information'))
            ->requiresConfirmation()
            ->slideOver()
            ->modalDescription(function () {
                return __('vendra-transaction::messages.transaction_direct_gateway_description');
            })
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->visible(function (Transaction $record): bool {
                return TransactionService::isWithdrawal($record)
                    && TransactionService::isApproved($record);
            });
    }
}
