<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraSupport\Contracts\ShouldLogActivity;
use Misaf\VendraTransaction\Database\Factories\TransactionTransferFactory;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;
use Misaf\VendraUser\Traits\BelongsToUser;

/**
 * @property int $id
 * @property int $transaction_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['transaction_id', 'user_id'])]
#[UseFactory(TransactionTransferFactory::class)]
final class TransactionTransfer extends Model implements ShouldLogActivity
{
    use BelongsToTransaction;
    use BelongsToUser;

    /** @use HasFactory<TransactionTransferFactory> */
    use HasFactory;


    /**
     * @var array<string, string>
     */
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id'             => 'integer',
            'transaction_id' => 'integer',
            'user_id'        => 'integer',
        ];
    }

    /**
     * @var list<string>
     */
}
