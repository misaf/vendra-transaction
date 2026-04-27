<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionMetadata;

/**
 * @extends Factory<TransactionMetadata>
 */
final class TransactionMetadataFactory extends Factory
{
    /**
     * @var class-string<TransactionMetadata>
     */
    protected $model = TransactionMetadata::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'key_name'       => fake()->shuffleString('abcdefghijklmnopqrstuvwxyz'),
            'key_value'      => fake()->shuffleString('abcdefghijklmnopqrstuvwxyz'),
        ];
    }
}
