<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Misaf\VendraTransaction\Models\TransactionLimit;

trait HasTransactionLimit
{
    /**
     * @return HasOne<TransactionLimit, $this>
     */
    public function latestTransactionLimit(): HasOne
    {
        return $this->hasOne(TransactionLimit::class)->latestOfMany();
    }

    /**
     * @return HasOne<TransactionLimit, $this>
     */
    public function oldestTransactionLimit(): HasOne
    {
        return $this->hasOne(TransactionLimit::class)->oldestOfMany();
    }

    /**
     * @return HasMany<TransactionLimit, $this>
     */
    public function transactionLimits(): HasMany
    {
        return $this->hasMany(TransactionLimit::class);
    }
}
