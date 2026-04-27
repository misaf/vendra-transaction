<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionTransfer;
use Misaf\VendraUser\Models\User;

/**
 * @extends Factory<TransactionTransfer>
 */
final class TransactionTransferFactory extends Factory
{
    /**
     * @var class-string<TransactionTransfer>
     */
    protected $model = TransactionTransfer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'user_id'        => User::factory(),
        ];
    }

    /**
     * @param User $user
     * @return static
     */
    public function forUser(User $user): static
    {
        return $this->state(fn(): array => [
            'user_id' => $user->id,
        ]);
    }
}
