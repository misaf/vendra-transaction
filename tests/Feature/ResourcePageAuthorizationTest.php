<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Misaf\VendraPermission\Tests\Support\PermissionModuleTestContext;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\CreateTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\EditTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\ViewTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\CreateTransaction;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    PermissionModuleTestContext::setUpFilamentAdminContext();

    Filament::getPanel('admin')->plugin(
        SpatieTranslatablePlugin::make()->defaultLocales(['en', 'de']),
    );
});

it('renders the create transaction page under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    livewire(CreateTransaction::class)
        ->assertOk();
});


it('renders the create transaction gateway page under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    livewire(CreateTransactionGateway::class)
        ->assertOk();
});

it('renders the edit transaction gateway page under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    $gateway = TransactionGatewayFactory::new()->createOne();

    livewire(EditTransactionGateway::class, ['record' => $gateway->getKey()])
        ->assertOk();
});

it('renders the view transaction gateway page under strict authorization', function (): void {
    Filament::getPanel('admin')->strictAuthorization();

    $gateway = TransactionGatewayFactory::new()->createOne();

    livewire(ViewTransactionGateway::class, ['record' => $gateway->getKey()])
        ->assertOk();
});
