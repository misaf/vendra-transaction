<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Misaf\VendraSupport\Support\TenantAwareness;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraUser\Models\User;

/**
 * @extends Factory<Transaction>
 */
#[UseModel(Transaction::class)]
final class TransactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_gateway_id' => TransactionGateway::factory(),
            'user_id'                => User::factory(),
            'transaction_type'       => fake()->randomElement(TransactionTypeEnum::cases()),
            'token'                  => TransactionService::generateToken(),
            'amount'                 => fake()->numberBetween(100, 200),
            'status'                 => fake()->randomElement(TransactionStatusEnum::cases()),
        ];
    }

    /**
     * No-op without a tenant provider, since there is no `tenant_id` column.
     */
    public function forTenant(Model|int $tenant): static
    {
        if ( ! TenantAwareness::enabled()) {
            return $this;
        }

        return $this->state(fn(): array => [
            'tenant_id' => $tenant instanceof Model ? $tenant->getKey() : $tenant,
        ]);
    }

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
