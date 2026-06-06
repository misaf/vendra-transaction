<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraActivityLog\Concerns\HasDefaultActivityLogOptions;
use Misaf\VendraTransaction\Database\Factories\TransactionMetadataFactory;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $transaction_id
 * @property string $key_name
 * @property string $key_value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['transaction_id', 'key_name', 'key_value'])]
final class TransactionMetadata extends Model
{
    use BelongsToTransaction;
    use HasDefaultActivityLogOptions;
    /** @use HasFactory<TransactionMetadataFactory> */
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
            'key_name'       => 'string',
            'key_value'      => 'string',
        ];
    }

    /**
     * @var list<string>
     */

}
