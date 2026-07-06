<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Misaf\VendraSupport\Contracts\ShouldLogActivity;
use Misaf\VendraTransaction\Database\Factories\TransactionMetadataFactory;
use Misaf\VendraTransaction\Traits\BelongsToTransaction;

/**
 * @property int $id
 * @property int $transaction_id
 * @property string $key_name
 * @property string $key_value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['transaction_id', 'key_name', 'key_value'])]
#[UseFactory(TransactionMetadataFactory::class)]
final class TransactionMetadata extends Model implements ShouldLogActivity
{
    use BelongsToTransaction;

    /** @use HasFactory<TransactionMetadataFactory> */
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
            'key_name'       => 'string',
            'key_value'      => 'string',
        ];
    }

    /**
     * @var list<string>
     */
}
