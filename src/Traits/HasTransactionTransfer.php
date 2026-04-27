<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Misaf\VendraTransaction\Models\TransactionTransfer;

trait HasTransactionTransfer
{
    /**
     * @return HasOne<TransactionTransfer, $this>
     */
    public function latestTransactionTransfer(): HasOne
    {
        return $this->hasOne(TransactionTransfer::class)->latestOfMany();
    }

    /**
     * @return HasOne<TransactionTransfer, $this>
     */
    public function oldestTransactionTransfer(): HasOne
    {
        return $this->hasOne(TransactionTransfer::class)->oldestOfMany();
    }

    /**
     * @return HasMany<TransactionTransfer, $this>
     */
    public function transactionTransfers(): HasMany
    {
        return $this->hasMany(TransactionTransfer::class);
    }
}
