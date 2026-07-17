<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Misaf\VendraTransaction\Models\TransactionGateway;

final class ReportAction
{
    public static function make(): Action
    {
        return Action::make('report')
            ->label(__('vendra-transaction::messages.reports'))
            ->action(fn(TransactionGateway $record) => $record->advance())
            ->modalContent(function () {
                return view('filament.admin.resources.transaction_gateways.pages.actions.report');
            })
            ->icon(Heroicon::ChartPie)
            ->slideOver()
            ->color('gray')
            ->modalSubmitAction(false);
    }
}
