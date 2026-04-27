<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Misaf\VendraTransaction\Models\TransactionMetadata;

trait HasTransactionMetadata
{
    /**
     * @return HasOne<TransactionMetadata, $this>
     */
    public function latestTransactionMetadata(): HasOne
    {
        return $this->hasOne(TransactionMetadata::class)->latestOfMany();
    }

    /**
     * @return HasOne<TransactionMetadata, $this>
     */
    public function oldestTransactionMetadata(): HasOne
    {
        return $this->hasOne(TransactionMetadata::class)->oldestOfMany();
    }

    /**
     * @return HasMany<TransactionMetadata, $this>
     */
    public function transactionMetadatas(): HasMany
    {
        return $this->hasMany(TransactionMetadata::class);
    }
}
