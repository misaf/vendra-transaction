<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Misaf\VendraSupport\Capabilities\CurrencyIntegration;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraTransaction\Models\TransactionLimit;
use Misaf\VendraTransaction\Models\Wallet;
use RuntimeException;

/**
 * Read-side helpers for wallets, gateways, and transaction limits. Write
 * operations live in dedicated Actions (e.g. CreateTransactionAction,
 * SettleTransactionAction, PostLedgerEntryAction).
 */
final class TransactionService
{
    public const string INTERNAL_GATEWAY_SLUG = 'internal-transactions';

    private const string TOKEN_CHARACTERS = '123456789';

    private const int TOKEN_LENGTH = 20;

    public function generateToken(): string
    {
        $maxIndex = mb_strlen(self::TOKEN_CHARACTERS) - 1;
        $token = '';

        for ($i = 0; $i < self::TOKEN_LENGTH; $i++) {
            $token .= self::TOKEN_CHARACTERS[random_int(0, $maxIndex)];
        }

        return $token;
    }

    /**
     * The wallet holding the given user's balance in the given currency,
     * created on first use.
     */
    public function walletFor(Model $user, string $currencyCode): Wallet
    {
        return Wallet::query()->firstOrCreate([
            'user_id'       => $user->getKey(),
            'currency_code' => Str::upper($currencyCode),
        ]);
    }

    /**
     * The wallet for the given user in the application's default currency,
     * created on first use.
     */
    public function defaultWalletFor(Model $user): Wallet
    {
        return $this->walletFor($user, CurrencyIntegration::defaultCode());
    }

    public function hasAnyActiveTransactionGateway(): bool
    {
        return TransactionGateway::query()
            ->active()
            ->where('slug', '<>', self::INTERNAL_GATEWAY_SLUG)
            ->exists();
    }

    public function hasActiveTransactionGateway(string $slug): bool
    {
        return TransactionGateway::query()
            ->active()
            ->where('slug', $slug)
            ->exists();
    }

    public function getTransactionGateway(string $slug): TransactionGateway
    {
        $gateway = TransactionGateway::query()
            ->active()
            ->where('slug', $slug)
            ->first();

        if (null === $gateway) {
            throw new RuntimeException("No active transaction gateway found for slug [{$slug}].");
        }

        return $gateway;
    }

    public function isInternalTransaction(Transaction $transaction): bool
    {
        return $this->getTransactionGateway(self::INTERNAL_GATEWAY_SLUG)->is($transaction->transactionGateway);
    }

    public function limitFor(Wallet $wallet, TransactionTypeEnum $transactionType): ?TransactionLimit
    {
        return $wallet->transactionLimits()
            ->where('transaction_type', $transactionType)
            ->first();
    }
}
