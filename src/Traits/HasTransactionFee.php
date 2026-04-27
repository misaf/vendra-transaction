<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Misaf\VendraTransaction\Models\TransactionFee;

trait HasTransactionFee
{
    /**
     * @return HasOne<TransactionFee, $this>
     */
    public function latestTransactionFee(): HasOne
    {
        return $this->hasOne(TransactionFee::class)->latestOfMany();
    }

    /**
     * @return HasOne<TransactionFee, $this>
     */
    public function oldestTransactionFee(): HasOne
    {
        return $this->hasOne(TransactionFee::class)->oldestOfMany();
    }

    /**
     * @return HasMany<TransactionFee, $this>
     */
    public function transactionFees(): HasMany
    {
        return $this->hasMany(TransactionFee::class);
    }
}
