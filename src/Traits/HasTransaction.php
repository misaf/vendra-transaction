<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Misaf\VendraTransaction\Models\Transaction;

trait HasTransaction
{
    use HasTransactionCheck;
    use HasTransactionFee;
    use HasTransactionLimit;
    use HasTransactionMetadata;
    use HasTransactionTransfer;

    /**
     * @return HasOne<Transaction, $this>
     */
    public function latestTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    /**
     * @return HasOne<Transaction, $this>
     */
    public function oldestTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->oldestOfMany();
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
