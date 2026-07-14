<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use Misaf\VendraSupport\Filament\Navigation\NavigationGroup;

final class TransactionsCluster extends Cluster
{
    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'transactions';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): string
    {
        return NavigationGroup::Sales->getLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('vendra-transaction::navigation.transaction');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('vendra-transaction::navigation.transaction');
    }
}
