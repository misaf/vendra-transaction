<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Providers;

use Composer\InstalledVersions;

use Filament\Panel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Event;
use Misaf\VendraSupport\Filament\Concerns\ResolvesConfiguredPanels;
use Misaf\VendraTransaction\Listeners\TransactionTransferSubscriber;
use Misaf\VendraTransaction\Listeners\WithdrawalLimitSubscriber;
use Misaf\VendraTransaction\Services\TransactionService;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class TransactionServiceProvider extends PackageServiceProvider
{
    use ResolvesConfiguredPanels;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('vendra-transaction')
            ->hasTranslations()
            ->hasMigrations([
                'create_transactions_table',
            ])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->askToStarRepoOnGitHub('misaf/vendra-transaction');
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(TransactionService::class);

        Panel::configureUsing(function (Panel $panel): void {
            if ( ! $this->shouldRegisterOnPanel($panel->getId(), 'vendra-transaction')) {
                return;
            }

            $panel
                ->discoverResources(
                    in: __DIR__ . '/../Filament/Clusters/Resources',
                    for: 'Misaf\\VendraTransaction\\Filament\\Clusters\\Resources',
                )
                ->discoverWidgets(
                    in: __DIR__ . '/../Filament/Widgets',
                    for: 'Misaf\\VendraTransaction\\Filament\\Widgets',
                );
        });
    }

    public function packageBooted(): void
    {
        AboutCommand::add('Vendra Transaction', fn() => ['Version' => InstalledVersions::getPrettyVersion('misaf/vendra-transaction')]);

        Event::subscribe(TransactionTransferSubscriber::class);
        Event::subscribe(WithdrawalLimitSubscriber::class);
    }
}
