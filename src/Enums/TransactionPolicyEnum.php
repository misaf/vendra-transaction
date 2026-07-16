<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

enum TransactionPolicyEnum: string
{
    case Create = 'create-transaction';
    case Delete = 'delete-transaction';
    case DeleteAny = 'delete-any-transaction';
    case ForceDelete = 'force-delete-transaction';
    case ForceDeleteAny = 'force-delete-any-transaction';
    case Replicate = 'replicate-transaction';
    case Restore = 'restore-transaction';
    case RestoreAny = 'restore-any-transaction';
    case Update = 'update-transaction';
    case View = 'view-transaction';
    case ViewAny = 'view-any-transaction';
}
