<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraActivityLog\Concerns\HasDefaultActivityLogOptions;
use Misaf\VendraTransaction\Database\Factories\TransactionCheckFactory;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $transaction_id
 * @property int $attempt_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['transaction_id', 'attempt_count'])]
#[UseFactory(TransactionCheckFactory::class)]
final class TransactionCheck extends Model
{
    use BelongsToTransaction;
    use HasDefaultActivityLogOptions;
    /** @use HasFactory<TransactionCheckFactory> */
    use HasFactory;
    use LogsActivity;

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
            'attempt_count'  => 'integer',
        ];
    }

    /**
     * @var list<string>
     */

}
