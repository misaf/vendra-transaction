<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Misaf\VendraSupport\Authorization\AuthorizesCreateAbilities;
use Misaf\VendraSupport\Authorization\AuthorizesDeleteAbilities;
use Misaf\VendraSupport\Authorization\AuthorizesSandboxMode;
use Misaf\VendraSupport\Authorization\AuthorizesUpdateAbilities;
use Misaf\VendraSupport\Authorization\AuthorizesViewAbilities;
use Misaf\VendraSupport\Authorization\ResolvesPolicyPermissions;
use Misaf\VendraTransaction\Enums\TransactionPolicyEnum;

/**
 * Metadata rows are managed inline on their transaction, so they are
 * governed by the transaction permissions rather than their own set.
 */
final class TransactionMetadataPolicy
{
    use AuthorizesCreateAbilities;
    use AuthorizesDeleteAbilities;
    use AuthorizesSandboxMode;
    use AuthorizesUpdateAbilities;
    use AuthorizesViewAbilities;
    use ResolvesPolicyPermissions;

    protected static function permissionEnum(): string
    {
        return TransactionPolicyEnum::class;
    }
}
