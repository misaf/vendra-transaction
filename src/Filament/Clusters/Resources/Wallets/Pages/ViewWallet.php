<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Pages;

use Filament\Resources\Pages\ViewRecord;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\WalletResource;

final class ViewWallet extends ViewRecord
{
    protected static string $resource = WalletResource::class;
}
