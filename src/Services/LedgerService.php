<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Exceptions\InsufficientBalanceException;
use Misaf\VendraTransaction\Models\LedgerEntry;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\Wallet;

/**
 * Posts immutable ledger entries and keeps the cached wallet balance in
 * lockstep: every entry records the signed amount and the resulting
 * balance, written together with the wallet row under a pessimistic lock.
 */
final class LedgerService
{
    public function post(Wallet $wallet, int $amount, ?Model $source = null): LedgerEntry
    {
        return DB::transaction(function () use ($wallet, $amount, $source): LedgerEntry {
            /** @var Wallet $locked */
            $locked = Wallet::query()->lockForUpdate()->findOrFail($wallet->getKey());

            $balanceAfter = $locked->balance + $amount;

            if ($balanceAfter < 0) {
                throw InsufficientBalanceException::forWallet($locked->id, $locked->balance, $amount);
            }

            $entry = new LedgerEntry([
                'amount'        => $amount,
                'balance_after' => $balanceAfter,
            ]);
            $entry->wallet()->associate($locked);

            if ($source instanceof Model) {
                $entry->source()->associate($source);
            }

            $entry->save();

            $locked->forceFill(['balance' => $balanceAfter])->save();
            $wallet->setAttribute('balance', $balanceAfter);

            return $entry;
        });
    }

    /**
     * Settles an approved transaction into the ledger: the signed principal
     * against the source wallet, the mirrored credit on the counterparty
     * wallet for transfers, and any fee as its own negative entry.
     */
    public function settle(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $transaction->loadMissing(['wallet', 'counterpartyWallet', 'transactionFee']);

            $signedAmount = $transaction->transaction_type->ledgerSign() * $transaction->amount;

            $this->post($transaction->wallet, $signedAmount, $transaction);

            if (TransactionTypeEnum::Transfer === $transaction->transaction_type && null !== $transaction->counterpartyWallet) {
                $this->post($transaction->counterpartyWallet, $transaction->amount, $transaction);
            }

            if (null !== $transaction->transactionFee && $transaction->transactionFee->amount > 0) {
                $this->post($transaction->wallet, -$transaction->transactionFee->amount, $transaction->transactionFee);
            }
        });
    }
}
