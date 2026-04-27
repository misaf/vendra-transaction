<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Misaf\VendraTransaction\Models\TransactionGateway;

trait BelongsToTransactionGateway
{
    /**
     * @return BelongsTo<TransactionGateway, $this>
     */
    public function transactionGateway(): BelongsTo
    {
        return $this->belongsTo(TransactionGateway::class);
    }
}
