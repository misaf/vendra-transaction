<?php

declare(strict_types=1);

use Misaf\VendraCurrency\Models\Currency;
use Misaf\VendraTenant\Models\Tenant;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Database\Factories\TransactionLimitFactory;
use Misaf\VendraTransaction\Database\Factories\WalletFactory;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Exceptions\TransactionLimitExceededException;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\States\Pending;
use Misaf\VendraTransaction\Support\TransactionUsers;

beforeEach(function (): void {
    Tenant::factory()->enabled()->create()->makeCurrent();
});

it('creates a pending transaction with fee and metadata', function (): void {
    $gateway = TransactionGatewayFactory::new()->enabled()->create(['slug' => 'shetab']);
    $wallet = WalletFactory::new()->create();

    $transaction = TransactionService::createTransaction(
        transactionGateway: 'shetab',
        wallet: $wallet,
        transactionType: TransactionTypeEnum::Deposit,
        amount: 5_000,
        metadata: ['reference' => 'abc-123'],
        fee: 250,
    );

    expect($transaction->status)->toBeInstanceOf(Pending::class)
        ->and($transaction->transaction_gateway_id)->toBe($gateway->id)
        ->and($transaction->amount)->toBe(5_000)
        ->and($transaction->transactionFee->amount)->toBe(250)
        ->and($transaction->transactionMetadatas()->pluck('key_value', 'key_name')->all())->toBe(['reference' => 'abc-123'])
        ->and($wallet->fresh()->balance)->toBe(0);
});

it('enforces the per-wallet transaction limit at creation', function (): void {
    TransactionGatewayFactory::new()->enabled()->create(['slug' => 'shetab']);
    $wallet = WalletFactory::new()->create();
    TransactionLimitFactory::new()->forWallet($wallet)->ofType(TransactionTypeEnum::Withdrawal)->create(['amount' => 1_000]);

    expect(fn() => TransactionService::createTransaction(
        transactionGateway: 'shetab',
        wallet: $wallet,
        transactionType: TransactionTypeEnum::Withdrawal,
        amount: 1_001,
    ))->toThrow(TransactionLimitExceededException::class);
});

it('resolves gateways by slug and ignores disabled ones', function (): void {
    TransactionGatewayFactory::new()->disabled()->create(['slug' => 'coinpayments']);
    TransactionGatewayFactory::new()->internal()->create();

    expect(TransactionService::hasActiveTransactionGateway('coinpayments'))->toBeFalse()
        ->and(TransactionService::hasActiveTransactionGateway('internal-transactions'))->toBeTrue()
        ->and(TransactionService::hasAnyActiveTransactionGateway())->toBeFalse()
        ->and(fn() => TransactionService::getTransactionGateway('coinpayments'))->toThrow(RuntimeException::class);
});

it('provisions one wallet per user and currency', function (): void {
    $user = TransactionUsers::model()::factory()->create();
    $currency = Currency::factory()->create();

    $wallet = TransactionService::walletFor($user, $currency);
    $again = TransactionService::walletFor($user, $currency);

    expect($again->is($wallet))->toBeTrue()
        ->and($user->wallets()->count())->toBe(1);
});
