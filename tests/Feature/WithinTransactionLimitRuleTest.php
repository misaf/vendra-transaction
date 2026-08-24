<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Validator;
use Misaf\VendraTransaction\Database\Factories\TransactionLimitFactory;
use Misaf\VendraTransaction\Database\Factories\WalletFactory;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Rules\WithinTransactionLimit;

beforeEach(function (): void {
    makeCurrentTestTenant();
});

it('passes amounts at or below the configured limit', function (): void {
    $wallet = WalletFactory::new()->create();
    TransactionLimitFactory::new()->forWallet($wallet)->ofType(TransactionTypeEnum::Withdrawal)->create(['amount' => 5_000]);

    $validator = Validator::make(
        ['amount' => 5_000],
        ['amount' => [new WithinTransactionLimit($wallet, TransactionTypeEnum::Withdrawal)]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails amounts above the configured limit', function (): void {
    $wallet = WalletFactory::new()->create();
    TransactionLimitFactory::new()->forWallet($wallet)->ofType(TransactionTypeEnum::Withdrawal)->create(['amount' => 5_000]);

    $validator = Validator::make(
        ['amount' => 5_001],
        ['amount' => [new WithinTransactionLimit($wallet, TransactionTypeEnum::Withdrawal)]],
    );

    expect($validator->fails())->toBeTrue();
});

it('only enforces the limit for its own transaction type', function (): void {
    $wallet = WalletFactory::new()->create();
    TransactionLimitFactory::new()->forWallet($wallet)->ofType(TransactionTypeEnum::Withdrawal)->create(['amount' => 5_000]);

    $validator = Validator::make(
        ['amount' => 9_000],
        ['amount' => [new WithinTransactionLimit($wallet, TransactionTypeEnum::Deposit)]],
    );

    expect($validator->passes())->toBeTrue();
});

it('ignores non-numeric values and leaves them to other rules', function (): void {
    $wallet = WalletFactory::new()->create();
    TransactionLimitFactory::new()->forWallet($wallet)->ofType(TransactionTypeEnum::Withdrawal)->create(['amount' => 5_000]);

    $validator = Validator::make(
        ['amount' => 'not-a-number'],
        ['amount' => [new WithinTransactionLimit($wallet, TransactionTypeEnum::Withdrawal)]],
    );

    expect($validator->passes())->toBeTrue();
});
