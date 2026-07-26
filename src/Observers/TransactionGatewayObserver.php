<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Observers;

use Misaf\VendraTransaction\Models\TransactionGateway;

final class TransactionGatewayObserver
{
    public function creating(TransactionGateway $gateway): void
    {
        if ( ! $gateway->active) {
            $gateway->is_default = false;

            return;
        }

        if ( ! TransactionGateway::query()->enabled()->exists()) {
            $gateway->is_default = true;
        }
    }

    public function saving(TransactionGateway $gateway): void
    {
        if ( ! $gateway->active) {
            $gateway->is_default = false;

            return;
        }

        if ($gateway->is_default) {
            TransactionGateway::query()
                ->where('is_default', true)
                ->whereKeyNot($gateway->getKey())
                ->update(['is_default' => false]);

            return;
        }

        if ($gateway->exists && true === $gateway->getOriginal('is_default')) {
            $hasAnotherDefault = TransactionGateway::query()
                ->enabled()
                ->where('is_default', true)
                ->whereKeyNot($gateway->getKey())
                ->exists();

            if ( ! $hasAnotherDefault) {
                $gateway->is_default = true;
            }
        }
    }

    public function saved(TransactionGateway $gateway): void
    {
        if ($gateway->wasChanged(['active', 'is_default'])) {
            $this->ensureEnabledDefault();
        }
    }

    public function deleted(TransactionGateway $gateway): void
    {
        if ( ! $gateway->is_default) {
            return;
        }

        if ($gateway->exists) {
            $gateway->forceFill(['is_default' => false])->saveQuietly();
        }

        $this->ensureEnabledDefault();
    }

    private function ensureEnabledDefault(): void
    {
        if (TransactionGateway::query()->enabled()->where('is_default', true)->exists()) {
            return;
        }

        TransactionGateway::query()
            ->enabled()
            ->ordered()
            ->first()
            ?->update(['is_default' => true]);
    }
}
