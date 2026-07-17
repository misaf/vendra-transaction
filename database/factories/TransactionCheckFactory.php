<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionCheck;

/**
 * @extends Factory<TransactionCheck>
 */
#[UseModel(TransactionCheck::class)]
final class TransactionCheckFactory extends Factory
{
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

    public function forTransaction(Transaction $transaction): static
    {
        return $this->state(fn(): array => [
            'transaction_id' => $transaction->id,
        ]);
    }
}
