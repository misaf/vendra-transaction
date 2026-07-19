<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Misaf\VendraSupport\Filament\Clusters\SalesCluster;
use Misaf\VendraSupport\Filament\Navigation\NavigationPriority;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Pages\ListWallets;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Pages\ViewWallet;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\RelationManagers\LedgerEntriesRelationManager;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\RelationManagers\TransactionLimitsRelationManager;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Schemas\WalletInfolist;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Wallets\Tables\WalletTable;
use Misaf\VendraTransaction\Models\Wallet;

final class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static ?int $navigationSort = NavigationPriority::Wallets->value;

    protected static ?string $slug = 'wallets';

    protected static ?string $cluster = SalesCluster::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getBreadcrumb(): string
    {
        return __('vendra-transaction::navigation.wallet');
    }

    public static function getModelLabel(): string
    {
        return __('vendra-transaction::navigation.wallet');
    }

    public static function getNavigationLabel(): string
    {
        return __('vendra-transaction::navigation.wallets');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vendra-transaction::navigation.wallets');
    }

    public static function getRelations(): array
    {
        return [
            LedgerEntriesRelationManager::class,
            TransactionLimitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWallets::route('/'),
            'view'  => ViewWallet::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return WalletInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalletTable::configure($table);
    }
}
