<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Resolves the host application's user model so the module can reference
 * wallet owners without depending on a concrete user package.
 */
final class TransactionUsers
{
    /**
     * @return class-string<Model>
     */
    public static function model(): string
    {
        /** @var class-string<Model> */
        return Config::string('auth.providers.users.model');
    }
}
