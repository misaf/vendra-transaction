<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Misaf\VendraTransaction\Services\WalletResolver
 *
 * @mixin \Misaf\VendraTransaction\Services\WalletResolver
 */
final class WalletResolver extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Misaf\VendraTransaction\Services\WalletResolver::class;
    }
}
