<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionFee;

/**
 * @extends Factory<TransactionFee>
 */
#[UseModel(TransactionFee::class)]
final class TransactionFeeFactory extends Factory
{
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

    public function forTransaction(Transaction $transaction): static
    {
        return $this->state(fn(): array => [
            'transaction_id' => $transaction->id,
        ]);
    }
}
