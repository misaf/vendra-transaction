<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Misaf\VendraTransaction\Listeners\TransactionTransferSubscriber;
use Misaf\VendraTransaction\Listeners\WithdrawalLimitSubscriber;
use Misaf\VendraTransaction\Services\TransactionService;

final class TransactionServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('transaction-service', fn(Application $app) => new TransactionService());
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'transaction');

        $this->publishes([
            __DIR__ . '/../../resources/lang' => $this->app->langPath('vendor/transaction'),
        ], 'transaction-lang');

        Event::subscribe(TransactionTransferSubscriber::class);
        Event::subscribe(WithdrawalLimitSubscriber::class);
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            'transaction-service',
        ];
    }
}
