<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Misaf\VendraTransaction\Models\TransactionCheck;

trait HasTransactionCheck
{
    /**
     * @return HasOne<TransactionCheck, $this>
     */
    public function latestTransactionCheck(): HasOne
    {
        return $this->hasOne(TransactionCheck::class)->latestOfMany();
    }

    /**
     * @return HasOne<TransactionCheck, $this>
     */
    public function oldestTransactionCheck(): HasOne
    {
        return $this->hasOne(TransactionCheck::class)->oldestOfMany();
    }

    /**
     * @return HasMany<TransactionCheck, $this>
     */
    public function transactionChecks(): HasMany
    {
        return $this->hasMany(TransactionCheck::class);
    }
}
