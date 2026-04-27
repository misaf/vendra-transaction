<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;
use Misaf\VendraUser\Database\Factories\UserFactory;
use Misaf\VendraUser\Traits\BelongsToUser;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $transaction_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class TransactionTransfer extends Model
{
    use BelongsToTransaction;
    use BelongsToUser;
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use LogsActivity;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'             => 'integer',
        'transaction_id' => 'integer',
        'user_id'        => 'integer',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'transaction_id',
        'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logExcept(['id']);
    }
}
