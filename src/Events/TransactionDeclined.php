<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionDeclined
{
    use Dispatchable;

    public function __construct(public Transaction $transaction) {}
}
