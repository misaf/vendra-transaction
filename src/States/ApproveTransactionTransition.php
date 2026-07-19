<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Illuminate\Support\Facades\DB;
use Misaf\VendraTransaction\Events\TransactionApproved;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Services\LedgerService;
use Spatie\ModelStates\Transition;

/**
 * Approval settles the transaction into the ledger atomically with the
 * state change: entries, cached balances, and the status column commit
 * together or not at all.
 */
final class ApproveTransactionTransition extends Transition
{
    public function __construct(private Transaction $transaction) {}

    public function handle(): Transaction
    {
        DB::transaction(function (): void {
            app(LedgerService::class)->settle($this->transaction);

            $this->transaction->status = new Approved($this->transaction);
            $this->transaction->save();
        });

        TransactionApproved::dispatch($this->transaction);

        return $this->transaction;
    }
}
