<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Misaf\VendraTenant\Models\Tenant;
use Misaf\VendraTransaction\Models\TransactionGateway;

/**
 * @extends Factory<TransactionGateway>
 */
final class TransactionGatewayFactory extends Factory
{
    /**
     * @var class-string<TransactionGateway>
     */
    protected $model = TransactionGateway::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id'   => Tenant::factory(),
            'name'        => fake()->unique()->sentence(3),
            'description' => fake()->text(),
            'slug'        => fn(array $attributes) => Str::slug($attributes['name']),
            'status'      => fake()->boolean(),
        ];
    }

    /**
     * @param Tenant $tenant
     * @return static
     */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn(): array => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
