<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

final class TransactionGatewayChart extends ChartWidget
{
    /**
     * @var int|string|array<string, int|null>
     */
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $startOfWeek = now()->startOfWeek(6);
        $dailyRakeData = $this->getWeeklyRakeData($startOfWeek);

        return [
            'datasets' => [
                [
                    'label' => __('vendra-transaction::messages.weekly_chart'),
                    'data'  => $dailyRakeData,
                ],
            ],
            'labels' => $this->getWeekdayLabels(),
        ];
    }

    /**
     * @param  Carbon  $startOfWeek
     */
    private function getWeeklyRakeData($startOfWeek): array
    {
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $data[] = Cache::store('asd')->get("asd-stats:daily:{$date}", 0);
        }

        return $data;
    }

    /**
     * @return array<int, string>
     */
    private function getWeekdayLabels(): array
    {
        return [
            __('vendra-transaction::messages.weekday_saturday'),
            __('vendra-transaction::messages.weekday_sunday'),
            __('vendra-transaction::messages.weekday_monday'),
            __('vendra-transaction::messages.weekday_tuesday'),
            __('vendra-transaction::messages.weekday_wednesday'),
            __('vendra-transaction::messages.weekday_thursday'),
            __('vendra-transaction::messages.weekday_friday'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
