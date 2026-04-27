<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionFee;

/**
 * @extends Factory<TransactionFee>
 */
final class TransactionFeeFactory extends Factory
{
    /**
     * @var class-string<TransactionFee>
     */
    protected $model = TransactionFee::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'amount'         => fake()->randomNumber(5, true),
        ];
    }
}
