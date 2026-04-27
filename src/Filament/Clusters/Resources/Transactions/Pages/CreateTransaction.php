<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages;

use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\TransactionResource;

final class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    public function getBreadcrumb(): string
    {
        return self::$breadcrumb ?? __('filament-panels::resources/pages/create-record.breadcrumb') . ' ' . __('navigation.transaction');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            $transactionGateway = TransactionService::getTransactionGateway('internal-transactions');
        } catch (Exception $e) {
            Notification::make()
                ->title(__('newsletter::notifications.retry.no_post.title'))
                ->body(__('newsletter::notifications.retry.no_post.body'))
                ->danger()
                ->send();

            throw new Halt($e->getMessage());
        }

        $data['transaction_gateway_id'] = $transactionGateway->id;
        $data['amount'] = TransactionService::getFormattedAmount((int) $data['amount'], $data['transaction_type']);
        $data['status'] = TransactionStatusEnum::Pending;

        return $data;
    }
}
