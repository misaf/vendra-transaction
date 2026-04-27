<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionTransferOverviewWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    /**
     * @var int|string|array<string, int|null>
     */
    protected int|string|array $columnSpan = [
        'sm' => 1,
    ];

    protected function getColumns(): int
    {
        return 1;
    }

    protected static ?int $sort = 6;

    public static function isDiscovered(): bool
    {
        return true;
    }

    public static function canView(): bool
    {
        return true;
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $startOfWeek = now()->startOfWeek(6);
        $endOfWeek = now()->endOfWeek();

        $transferTransactionStats = Trend::query(Transaction::query()->transfer()->approved()
            ->where('amount', '>', 0))
            ->between($startOfWeek, $endOfWeek)
            ->perDay()
            ->sum('amount');

        $totalTransferAmount = (int) Transaction::query()->transfer()->approved()
            ->where('amount', '>', 0)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->sum('amount');

        $transactionTransfer = Stat::make('transfer_transaction_stats', Number::format($totalTransferAmount))
            ->label(__('transaction::widgets.transfer_transaction_stats'))
            ->description(__('transaction::widgets.transfer_transaction_stats_description'))
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart($transferTransactionStats->map(fn(TrendValue $value) => $value->aggregate)->toArray())
            ->color('primary');

        return [$transactionTransfer];
    }
}
