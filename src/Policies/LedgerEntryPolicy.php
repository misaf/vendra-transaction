<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Misaf\VendraSupport\Concerns\AuthorizesSandboxMode;
use Misaf\VendraSupport\Concerns\AuthorizesViewAbilities;
use Misaf\VendraSupport\Concerns\ResolvesPolicyPermissions;
use Misaf\VendraTransaction\Enums\WalletPolicyEnum;

/**
 * Ledger entries are immutable and only ever rendered inside their wallet,
 * so viewing them is governed by the wallet permissions and no write
 * abilities exist.
 */
final class LedgerEntryPolicy
{
    use AuthorizesSandboxMode;
    use AuthorizesViewAbilities;
    use ResolvesPolicyPermissions;

    protected static function permissionEnum(): string
    {
        return WalletPolicyEnum::class;
    }
}
