<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionWithdrawalOverviewWidget extends StatsOverviewWidget
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

    protected static ?int $sort = 5;

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

        $withdrawalTransactionStats = Trend::query(Transaction::query()->withdrawal()->approved()
            ->when($this->record, fn(Builder $builder) => $builder->where('user_id', $this->record->id)))
            ->between($startOfWeek, $endOfWeek)
            ->perDay()
            ->sum('amount');

        $totalWithdrawalAmount = (int) Transaction::query()->withdrawal()->approved()
            ->when($this->record, fn(Builder $builder) => $builder->where('user_id', $this->record->id))
            ->sum('amount');

        $transactionWithdrawal = Stat::make('withdrawal_transaction_stats', Number::format($totalWithdrawalAmount))
            ->label(__('transaction::widgets.withdrawal_transaction_stats'))
            ->description(__('transaction::widgets.withdrawal_transaction_stats_description'))
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart($withdrawalTransactionStats->map(fn(TrendValue $value) => $value->aggregate)->toArray())
            ->color('primary');

        return [$transactionWithdrawal];
    }
}
