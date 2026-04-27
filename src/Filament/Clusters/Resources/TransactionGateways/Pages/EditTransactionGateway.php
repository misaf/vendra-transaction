<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\TransactionGatewayResource;

final class EditTransactionGateway extends EditRecord
{
    use Translatable;

    protected static string $resource = TransactionGatewayResource::class;

    public function getBreadcrumb(): string
    {
        return self::$breadcrumb ?? __('filament-panels::resources/pages/edit-record.breadcrumb') . ' ' . __('navigation.transaction_gateway');
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
