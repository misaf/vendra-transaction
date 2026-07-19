<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Misaf\VendraCurrency\Models\Currency;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\TransactionResource;
use Misaf\VendraTransaction\Support\TransactionUsers;

final class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    /**
     * Resolves the selected user + currency pair into a wallet, provisioning
     * it on first use, so transactions can be created for users who have no
     * wallet yet.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currency = Currency::query()->findOrFail($data['currency_id']);

        $data['wallet_id'] = TransactionService::walletFor(
            TransactionUsers::model()::query()->findOrFail($data['user_id']),
            $currency,
        )->id;

        if ( ! empty($data['counterparty_user_id'])) {
            $data['counterparty_wallet_id'] = TransactionService::walletFor(
                TransactionUsers::model()::query()->findOrFail($data['counterparty_user_id']),
                $currency,
            )->id;
        }

        unset($data['user_id'], $data['currency_id'], $data['counterparty_user_id']);

        return $data;
    }
}
