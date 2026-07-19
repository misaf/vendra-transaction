<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Misaf\VendraCurrency\Models\Currency;
use Misaf\VendraSupport\Contracts\ShouldLogActivity;
use Misaf\VendraSupport\Traits\BelongsToTenant;
use Misaf\VendraTransaction\Database\Factories\WalletFactory;
use Misaf\VendraTransaction\Support\TransactionUsers;

/**
 * A per-user, per-currency balance holder. The cached `balance` column is
 * only ever written through the ledger, which records every movement as an
 * immutable entry.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $user_id
 * @property int $currency_id
 * @property int $balance
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Currency $currency
 */
#[Fillable(['user_id', 'currency_id'])]
#[Hidden(['tenant_id'])]
#[UseFactory(WalletFactory::class)]
final class Wallet extends Model implements ShouldLogActivity
{
    use BelongsToTenant;

    /** @use HasFactory<WalletFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'tenant_id'   => 'integer',
            'user_id'     => 'integer',
            'currency_id' => 'integer',
            'balance'     => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(TransactionUsers::model(), 'user_id');
    }

    /**
     * @return BelongsTo<Currency, $this>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return HasMany<LedgerEntry, $this>
     */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<TransactionLimit, $this>
     */
    public function transactionLimits(): HasMany
    {
        return $this->hasMany(TransactionLimit::class);
    }
}
