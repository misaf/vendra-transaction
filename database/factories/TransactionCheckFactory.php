<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionCheck;

/**
 * @extends Factory<TransactionCheck>
 */
final class TransactionCheckFactory extends Factory
{
    /**
     * @var class-string<TransactionCheck>
     */
    protected $model = TransactionCheck::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'attempt_count'  => fake()->randomElement([1, 2, 3]),
        ];
    }
}
