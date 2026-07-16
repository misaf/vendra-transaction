<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Misaf\VendraSupport\Contracts\ShouldLogActivity;
use Misaf\VendraSupport\Traits\BelongsToTenant;
use Misaf\VendraSupport\Traits\HasOptionalTags;
use Misaf\VendraTransaction\Database\Factories\TransactionFactory;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Traits\BelongsToTransactionGateway;
use Misaf\VendraTransaction\Traits\HasTransactionCheck;
use Misaf\VendraTransaction\Traits\HasTransactionFee;
use Misaf\VendraTransaction\Traits\HasTransactionMetadata;
use Misaf\VendraTransaction\Traits\HasTransactionTransfer;
use Misaf\VendraUser\Traits\BelongsToUser;
use Spatie\Tags\HasTags;

/**
 * @property int $id
 * @property int $tenant_id
 * @property int $transaction_gateway_id
 * @property int $user_id
 * @property TransactionTypeEnum $transaction_type
 * @property string $token
 * @property int $amount
 * @property TransactionStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['transaction_gateway_id', 'user_id', 'transaction_type', 'token', 'amount', 'status'])]
#[Hidden(['tenant_id'])]
#[UseFactory(TransactionFactory::class)]
final class Transaction extends Model implements ShouldLogActivity
{
    use BelongsToTenant;
    use BelongsToTransactionGateway;
    use BelongsToUser;

    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    use HasOptionalTags, HasTags {
        HasOptionalTags::tags insteadof HasTags;
    }
    use HasTransactionCheck;
    use HasTransactionFee;
    use HasTransactionMetadata;
    use HasTransactionTransfer;
    use SoftDeletes;

    public const string TAG_TYPE = 'transaction';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id'                     => 'integer',
            'tenant_id'              => 'integer',
            'transaction_gateway_id' => 'integer',
            'user_id'                => 'integer',
            'transaction_type'       => TransactionTypeEnum::class,
            'token'                  => 'string',
            'amount'                 => 'integer',
            'status'                 => TransactionStatusEnum::class,
        ];
    }

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

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeApproved(Builder $builder): void
    {
        $builder->where('status', TransactionStatusEnum::Approved);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeDeclined(Builder $builder): void
    {
        $builder->where('status', TransactionStatusEnum::Declined);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeFailed(Builder $builder): void
    {
        $builder->where('status', TransactionStatusEnum::Failed);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopePending(Builder $builder): void
    {
        $builder->where('status', TransactionStatusEnum::Pending);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeReview(Builder $builder): void
    {
        $builder->where('status', TransactionStatusEnum::Review);
    }

    /**
     * @param  Builder<self>  $builder
     */
    public function scopeProcessing(Builder $builder): void
    {
        $builder->where('status', TransactionStatusEnum::Processing);
    }

    protected function tagType(): string
    {
        return self::TAG_TYPE;
    }

    protected static function booted(): void
    {
        self::creating(function (self $transaction): void {
            $transaction->token = TransactionService::generateToken();
        });
    }
}
