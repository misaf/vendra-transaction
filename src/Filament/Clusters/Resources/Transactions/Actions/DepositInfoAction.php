<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class DepositInfoAction
{
    public static function make(): Action
    {
        return Action::make('deposit-info')
            ->icon(Heroicon::Eye)
            ->label(__('vendra-transaction::messages.purchase_information'))
            ->requiresConfirmation()
            ->slideOver()
            ->modalDescription(function () {
                return __('vendra-transaction::messages.transaction_direct_gateway_description');
            })
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }
}
