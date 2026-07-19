<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

enum WalletPolicyEnum: string
{
    case Create = 'create-wallet';
    case Delete = 'delete-wallet';
    case DeleteAny = 'delete-any-wallet';
    case ForceDelete = 'force-delete-wallet';
    case ForceDeleteAny = 'force-delete-any-wallet';
    case Replicate = 'replicate-wallet';
    case Restore = 'restore-wallet';
    case RestoreAny = 'restore-any-wallet';
    case Update = 'update-wallet';
    case View = 'view-wallet';
    case ViewAny = 'view-any-wallet';
}
