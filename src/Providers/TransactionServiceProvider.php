<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Providers;

use Filament\Panel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Misaf\VendraTransaction\Listeners\TransactionTransferSubscriber;
use Misaf\VendraTransaction\Listeners\WithdrawalLimitSubscriber;
use Misaf\VendraTransaction\Services\TransactionService;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

final class TransactionServiceProvider extends ServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('vendra-transaction')
            ->hasTranslations()
            ->hasMigrations([
                'create_transactions_table'
            ])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->askToStarRepoOnGitHub('misaf/vendra-transaction');
            });
    }

    public function packageRegistered(): void
    {
        $this->app->bind('transaction-service', fn(Application $app) => new TransactionService());

        Panel::configureUsing(function (Panel $panel): void {
            if ('admin' !== $panel->getId()) {
                return;
            }

            // $panel->plugin(TransactionPlugin::make());
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Vendra Transaction', fn() => ['Version' => 'dev-master']);

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
