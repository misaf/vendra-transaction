<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages;

use Filament\Resources\Pages\ViewRecord;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\TransactionResource;

final class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function getBreadcrumb(): string
    {
        return self::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb') . ' ' . __('navigation.transaction');
    }
}
