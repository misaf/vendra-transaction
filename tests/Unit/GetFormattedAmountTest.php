<?php

declare(strict_types=1);

use Misaf\VendraTransaction\Facades\TransactionService;

dataset('valid transactions', [
    // Valid cases for "withdrawal"
    ['withdrawal', 5000000, -5000000],
    ['withdrawal', 20000000, -20000000],
    ['withdrawal', 1, -1],
    ['withdrawal', -1, -1],

    // Valid cases for "deposit"
    ['deposit', 5000000, 5000000],
    ['deposit', 1, 1],
    ['deposit', -1, 1],

    // Valid cases for "commission"
    ['commission', 2000000, 2000000],
    ['commission', 1, 1],
    ['commission', -1, 1],

    // Valid cases for "bonus"
    ['bonus', 1000000, 1000000],
    ['bonus', 1, 1],
    ['bonus', -1, 1],
]);

it('returns correct formatted amount for valid transactions', function (string $transactionType, int $amount, int $expected): void {
    expect(TransactionService::getFormattedAmount($amount, $transactionType))->toBe($expected);
})->with('valid transactions');

dataset('invalid transaction types', [
    ['invalid_type'],  // Completely invalid type.
    ['WITHDRAWAL'],    // Incorrect case (valid enum backing value is "withdrawal").
    ['withdrawal123'], // Extra characters make it invalid.
    [''],              // Empty string is invalid.
]);

it('throws ValueError for invalid transaction types', function (string $transactionType): void {
    TransactionService::getFormattedAmount(5000000, $transactionType);
})
    ->with('invalid transaction types')
    ->throws(ValueError::class);

dataset('invalid amounts', [
    ['5000000'],    // string instead of int
    [5000000.0],    // float instead of int
    [null],         // null instead of int
    [true],         // boolean instead of int
]);

it('throws TypeError for invalid amounts', function (int $amount): void {
    // Using a valid transaction type ("deposit") to isolate the type check for the amount.
    TransactionService::getFormattedAmount($amount, 'deposit');
})
    ->with('invalid amounts')
    ->throws(TypeError::class);
