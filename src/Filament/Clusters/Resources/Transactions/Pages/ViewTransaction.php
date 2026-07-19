<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages;

use Filament\Resources\Pages\ViewRecord;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\ApproveTransactionAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\DeclineTransactionAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\FailTransactionAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\TransactionResource;

final class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApproveTransactionAction::make(),
            DeclineTransactionAction::make(),
            FailTransactionAction::make(),
        ];
    }
}
