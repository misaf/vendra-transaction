<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionDeclined implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(public Transaction $transaction) {}
}
