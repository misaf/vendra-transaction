<?php

declare(strict_types=1);

namespace App\Filament\Admin\Clusters\Transactions;

use Filament\Clusters\Cluster;

final class TransactionsCluster extends Cluster
{
    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'transactions';

    public static function getNavigationGroup(): string
    {
        return __('navigation.billing_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('transaction::navigation.transaction');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('navigation.billing_management');
    }
}
