<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Misaf\VendraTransaction\Models\TransactionGateway;

final class TransactionGatewayPolicy
{
    use HandlesAuthorization;

    public function create(Authorizable $user): bool
    {
        return $user->can('create-transaction-gateway');
    }

    public function delete(Authorizable $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('delete-transaction-gateway');
    }

    public function deleteAny(Authorizable $user): bool
    {
        return $user->can('delete-any-transaction-gateway');
    }

    public function forceDelete(Authorizable $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('force-delete-transaction-gateway');
    }

    public function forceDeleteAny(Authorizable $user): bool
    {
        return $user->can('force-delete-any-transaction-gateway');
    }

    public function replicate(Authorizable $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('replicate-transaction-gateway');
    }

    public function restore(Authorizable $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('restore-transaction-gateway');
    }

    public function restoreAny(Authorizable $user): bool
    {
        return $user->can('restore-any-transaction-gateway');
    }

    public function update(Authorizable $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('update-transaction-gateway');
    }

    public function view(Authorizable $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('view-transaction-gateway');
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can('view-any-transaction-gateway');
    }
}
