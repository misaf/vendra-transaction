<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\TransactionGatewayResource;

final class ViewTransactionGateway extends ViewRecord
{
    use Translatable;

    protected static string $resource = TransactionGatewayResource::class;

    public function getBreadcrumb(): string
    {
        return self::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb') . ' ' . __('navigation.transaction_gateway');
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
