<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Misaf\VendraPermission\Tests\Support\PermissionModuleTestContext;
use Misaf\VendraTransaction\Database\Factories\TransactionFactory;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Database\Factories\WalletFactory;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\CreateTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\EditTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\ListTransactionGateways;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\ViewTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\CreateTransaction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\ListTransactions;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\ViewTransaction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Pages\ListWallets;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Pages\ViewWallet;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    PermissionModuleTestContext::setUpFilamentAdminContext();

    Filament::getPanel('admin')->plugin(
        SpatieTranslatablePlugin::make()->defaultLocales(['en', 'de']),
    );
});

it('renders the transaction pages under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    $transaction = TransactionFactory::new()->deposit()->create();

    livewire(ListTransactions::class)->assertOk();
    livewire(CreateTransaction::class)->assertOk();
    livewire(ViewTransaction::class, ['record' => $transaction->getKey()])->assertOk();
});

it('renders the transaction gateway pages under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    $gateway = TransactionGatewayFactory::new()->create();

    livewire(ListTransactionGateways::class)->assertOk();
    livewire(CreateTransactionGateway::class)->assertOk();
    livewire(ViewTransactionGateway::class, ['record' => $gateway->getKey()])->assertOk();
    livewire(EditTransactionGateway::class, ['record' => $gateway->getKey()])->assertOk();
});

it('renders the wallet pages under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    $wallet = WalletFactory::new()->create();

    livewire(ListWallets::class)->assertOk();
    livewire(ViewWallet::class, ['record' => $wallet->getKey()])->assertOk();
});
