<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraTransaction\Database\Factories\TransactionMetadataFactory;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $transaction_id
 * @property string $key_name
 * @property string $key_value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class TransactionMetadata extends Model
{
    use BelongsToTransaction;
    /** @use HasFactory<TransactionMetadataFactory> */
    use HasFactory;
    use LogsActivity;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'             => 'integer',
        'transaction_id' => 'integer',
        'key_name'       => 'string',
        'key_value'      => 'string',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'transaction_id',
        'key_name',
        'key_value',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logExcept(['id']);
    }
}
