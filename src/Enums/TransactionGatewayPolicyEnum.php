<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

enum TransactionGatewayPolicyEnum: string
{
    case Create = 'create-transaction-gateway';
    case Delete = 'delete-transaction-gateway';
    case DeleteAny = 'delete-any-transaction-gateway';
    case ForceDelete = 'force-delete-transaction-gateway';
    case ForceDeleteAny = 'force-delete-any-transaction-gateway';
    case Reorder = 'reorder-transaction-gateway';
    case Restore = 'restore-transaction-gateway';
    case RestoreAny = 'restore-any-transaction-gateway';
    case Update = 'update-transaction-gateway';
    case View = 'view-transaction-gateway';
    case ViewAny = 'view-any-transaction-gateway';
}
