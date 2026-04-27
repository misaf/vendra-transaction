<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages;

use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\TransactionGatewayResource;

final class CreateTransactionGateway extends CreateRecord
{
    use Translatable;

    protected static string $resource = TransactionGatewayResource::class;

    public function getBreadcrumb(): string
    {
        return self::$breadcrumb ?? __('filament-panels::resources/pages/create-record.breadcrumb') . ' ' . __('navigation.transaction_gateway');
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
