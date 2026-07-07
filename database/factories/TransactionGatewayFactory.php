<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Misaf\VendraSupport\Support\TenantAwareness;
use Misaf\VendraTransaction\Models\TransactionGateway;

/**
 * @extends Factory<TransactionGateway>
 */
#[UseModel(TransactionGateway::class)]
final class TransactionGatewayFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->sentence(3),
            'description' => fake()->text(),
            'slug'        => fn(array $attributes) => Str::slug($attributes['name']),
            'status'      => fake()->boolean(),
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
}
