<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Misaf\VendraTransaction\Services\TransactionService
 *
 * @mixin \Misaf\VendraTransaction\Services\TransactionService
 */
final class TransactionService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Misaf\VendraTransaction\Services\TransactionService::class;
    }
}
