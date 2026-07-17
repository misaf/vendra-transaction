<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Misaf\VendraPermission\Tests\Support\PermissionModuleTestContext;
use Misaf\VendraTransaction\Database\Factories\TransactionFactory;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\ListTransactionGateways;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\ListTransactions;
use Misaf\VendraUser\Database\Factories\UserFactory;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    PermissionModuleTestContext::setUpFilamentAdminContext();

    Filament::getPanel('admin')->plugin(
        SpatieTranslatablePlugin::make()->defaultLocales(['en', 'de']),
    );
});

it('renders the transactions table with its records', function (): void {
    $transactionGateway = TransactionGatewayFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $transaction = TransactionFactory::new()->deposit()->createOne([
        'transaction_gateway_id' => $transactionGateway->id,
        'user_id'                => $user->id,
    ]);

    livewire(ListTransactions::class)
        ->assertOk()
        ->call('loadTable')
        ->assertCanSeeTableRecords([$transaction]);
});

it('renders the transaction gateways table with its records', function (): void {
    $transactionGateway = TransactionGatewayFactory::new()->createOne();

    livewire(ListTransactionGateways::class)
        ->assertOk()
        ->call('loadTable')
        ->assertCanSeeTableRecords([$transactionGateway]);
});
