<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Misaf\VendraTransaction\Models\Transaction;

trait BelongsToTransaction
{
    use BelongsToTransactionGateway;

    /**
     * @return BelongsTo<Transaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
