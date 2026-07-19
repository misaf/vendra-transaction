<?php

declare(strict_types=1);

use Misaf\VendraTenant\Models\Tenant;
use Misaf\VendraTransaction\Database\Factories\WalletFactory;
use Misaf\VendraTransaction\Exceptions\InsufficientBalanceException;
use Misaf\VendraTransaction\Services\LedgerService;

beforeEach(function (): void {
    Tenant::factory()->enabled()->create()->makeCurrent();
});

it('posts entries and keeps the cached wallet balance in lockstep', function (): void {
    $wallet = WalletFactory::new()->create();
    $ledger = app(LedgerService::class);

    $first = $ledger->post($wallet, 5_000);
    $second = $ledger->post($wallet, -2_000);

    expect($first->balance_after)->toBe(5_000)
        ->and($second->balance_after)->toBe(3_000)
        ->and($wallet->balance)->toBe(3_000)
        ->and($wallet->fresh()->balance)->toBe(3_000)
        ->and($wallet->ledgerEntries()->count())->toBe(2)
        ->and((int) $wallet->ledgerEntries()->sum('amount'))->toBe(3_000);
});

it('rejects entries that would drive the balance negative', function (): void {
    $wallet = WalletFactory::new()->create();
    $ledger = app(LedgerService::class);

    $ledger->post($wallet, 1_000);

    expect(fn() => $ledger->post($wallet, -1_001))
        ->toThrow(InsufficientBalanceException::class)
        ->and($wallet->fresh()->balance)->toBe(1_000)
        ->and($wallet->ledgerEntries()->count())->toBe(1);
});

it('refuses to mutate or delete posted ledger entries', function (): void {
    $wallet = WalletFactory::new()->create();
    $entry = app(LedgerService::class)->post($wallet, 1_000);

    expect(fn() => $entry->update(['amount' => 9_999]))->toThrow(RuntimeException::class)
        ->and(fn() => $entry->delete())->toThrow(RuntimeException::class);
});
