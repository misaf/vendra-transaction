<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Misaf\VendraSupport\Concerns\AuthorizesCreateAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesDeleteAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesSandboxMode;
use Misaf\VendraSupport\Concerns\AuthorizesUpdateAbilities;
use Misaf\VendraSupport\Concerns\AuthorizesViewAbilities;
use Misaf\VendraSupport\Concerns\ResolvesPolicyPermissions;
use Misaf\VendraTransaction\Enums\WalletPolicyEnum;

/**
 * Limits are managed inline on their wallet, so they are governed by the
 * wallet permissions rather than their own set.
 */
final class TransactionLimitPolicy
{
    use AuthorizesCreateAbilities;
    use AuthorizesDeleteAbilities;
    use AuthorizesSandboxMode;
    use AuthorizesUpdateAbilities;
    use AuthorizesViewAbilities;
    use ResolvesPolicyPermissions;

    protected static function permissionEnum(): string
    {
        return WalletPolicyEnum::class;
    }
}
