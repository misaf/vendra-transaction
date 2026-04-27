<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Models\TransactionLimit;
use Misaf\VendraUser\Models\User;

/**
 * @extends Factory<TransactionLimit>
 */
final class TransactionLimitFactory extends Factory
{
    /**
     * @var class-string<TransactionLimit>
     */
    protected $model = TransactionLimit::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'transaction_type' => fake()->randomElement(TransactionTypeEnum::cases()),
            'amount'           => fake()->numberBetween(100, 200),
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

    public function deposit(): static
    {
        return $this->state(fn(): array => [
            'transaction_type' => TransactionTypeEnum::Deposit,
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn(): array => [
            'transaction_type' => TransactionTypeEnum::Withdrawal,
        ]);
    }

    public function commission(): static
    {
        return $this->state(fn(): array => [
            'transaction_type' => TransactionTypeEnum::Commission,
        ]);
    }

    public function bonus(): static
    {
        return $this->state(fn(): array => [
            'transaction_type' => TransactionTypeEnum::Bonus,
        ]);
    }

    public function transfer(): static
    {
        return $this->state(fn(): array => [
            'transaction_type' => TransactionTypeEnum::Transfer,
        ]);
    }
}
