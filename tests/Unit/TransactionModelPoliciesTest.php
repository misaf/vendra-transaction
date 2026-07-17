<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\SoftDeletes;
use Misaf\VendraSupport\Traits\BelongsToTenant;
use Misaf\VendraTransaction\Enums\TransactionGatewayPolicyEnum;
use Misaf\VendraTransaction\Enums\TransactionPolicyEnum;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionGateway;

it('applies shared tenant ownership and soft deletes to transaction models', function (): void {
    expect(class_uses_recursive(Transaction::class))->toContain(BelongsToTenant::class, SoftDeletes::class)
        ->and(class_uses_recursive(TransactionGateway::class))->toContain(BelongsToTenant::class, SoftDeletes::class);
});

it('defines translatable fields on the transaction gateway model', function (): void {
    expect((new TransactionGateway())->translatable)->toBe(['name', 'description', 'slug']);
});

it('hides the tenant association from transaction serialization', function (): void {
    expect((new Transaction())->getHidden())->toContain('tenant_id')
        ->and((new TransactionGateway())->getHidden())->toContain('tenant_id');
});

it('defines policy permissions for the transaction resource', function (): void {
    $permissions = array_column(TransactionPolicyEnum::cases(), 'value');

    expect($permissions)->toHaveCount(11);
});

it('defines policy permissions for the transaction gateway resource', function (): void {
    $permissions = array_column(TransactionGatewayPolicyEnum::cases(), 'value');

    expect($permissions)->toHaveCount(12);
});

it('uses kebab-case permission names scoped per model', function (): void {
    $transactionPermissions = array_column(TransactionPolicyEnum::cases(), 'value');
    $gatewayPermissions = array_column(TransactionGatewayPolicyEnum::cases(), 'value');

    expect($transactionPermissions)->toHaveCount(count(array_unique($transactionPermissions)))
        ->each->toMatch('/^[a-z]+(-[a-z]+)*$/');

    expect($gatewayPermissions)->toHaveCount(count(array_unique($gatewayPermissions)))
        ->each->toMatch('/^[a-z]+(-[a-z]+)*$/');
});
