<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraTransaction\Database\Factories\TransactionFeeFactory;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $transaction_id
 * @property int $amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class TransactionFee extends Model
{
    use BelongsToTransaction;
    /** @use HasFactory<TransactionFeeFactory> */
    use HasFactory;
    use LogsActivity;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'             => 'integer',
        'transaction_id' => 'integer',
        'amount'         => 'integer',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'transaction_id',
        'amount',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logExcept(['id']);
    }
}
