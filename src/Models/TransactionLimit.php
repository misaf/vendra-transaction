<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraSupport\Contracts\ShouldLogActivity;
use Misaf\VendraTransaction\Database\Factories\TransactionLimitFactory;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraUser\Traits\BelongsToUser;

/**
 * @property int $id
 * @property int $user_id
 * @property TransactionTypeEnum $transaction_type
 * @property int $amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['user_id', 'transaction_type', 'amount'])]
#[UseFactory(TransactionLimitFactory::class)]
final class TransactionLimit extends Model implements ShouldLogActivity
{
    use BelongsToUser;

    /** @use HasFactory<TransactionLimitFactory> */
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
            'id'               => 'integer',
            'user_id'          => 'integer',
            'transaction_type' => TransactionTypeEnum::class,
            'amount'           => 'integer',
        ];
    }

    /**
     * @var list<string>
     */

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeDeposit(Builder $builder): void
    {
        $builder->where('transaction_type', TransactionTypeEnum::Deposit);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeWithdrawal(Builder $builder): void
    {
        $builder->where('transaction_type', TransactionTypeEnum::Withdrawal);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeCommission(Builder $builder): void
    {
        $builder->where('transaction_type', TransactionTypeEnum::Commission);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeBonus(Builder $builder): void
    {
        $builder->where('transaction_type', TransactionTypeEnum::Bonus);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeTransfer(Builder $builder): void
    {
        $builder->where('transaction_type', TransactionTypeEnum::Transfer);
    }
}
