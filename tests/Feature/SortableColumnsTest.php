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

it('sorts the transactions table by every sortable column following the stored values', function (): void {
    $transactionGateway = TransactionGatewayFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $first = TransactionFactory::new()->deposit()->forGateway($transactionGateway)->forUser($user)->createOne();
    $second = TransactionFactory::new()->deposit()->forGateway($transactionGateway)->forUser($user)->createOne();

    expect(livewire(ListTransactions::class)->call('loadTable'))
        ->toSortByEverySortableColumn([$first, $second]);
});

it('sorts the transaction gateways table by every sortable column following the stored values', function (): void {
    $first = TransactionGatewayFactory::new()->createOne();
    $second = TransactionGatewayFactory::new()->createOne();

    expect(livewire(ListTransactionGateways::class)->call('loadTable'))
        ->toSortByEverySortableColumn([$first, $second]);
});
