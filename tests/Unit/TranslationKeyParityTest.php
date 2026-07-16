<?php

declare(strict_types=1);

use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;

it('has at least two available locales', function (): void {
    expect(__DIR__ . '/../../resources/lang')->toHaveAtLeastTwoLocales('vendra-transaction');
});

it('keeps translation files and keys in sync across locales', function (): void {
    expect(__DIR__ . '/../../resources/lang')->toHaveTranslationsInSync('vendra-transaction');
});

it('keeps translation file keys sorted', function (): void {
    expect(__DIR__ . '/../../resources/lang')->toHaveSortedTranslationKeys('vendra-transaction');
});

it('translates enum labels from the shared enum locale file', function (): void {
    app()->setLocale('en');

    expect(TransactionStatusEnum::Approved->getLabel())->toBe('Approved')
        ->and(TransactionStatusEnum::Processing->getLabel())->toBe('Processing')
        ->and(TransactionTypeEnum::Commission->getLabel())->toBe('Commission')
        ->and(TransactionTypeEnum::Withdrawal->getLabel())->toBe('Withdrawal');
});
