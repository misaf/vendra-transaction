<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Misaf\VendraTransaction\Services\TransactionGatewayRegistry
 *
 * @mixin \Misaf\VendraTransaction\Services\TransactionGatewayRegistry
 */
final class TransactionGatewayRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Misaf\VendraTransaction\Services\TransactionGatewayRegistry::class;
    }
}
