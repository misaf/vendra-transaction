<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraUser\Models\User;

final class TransactionGatewayPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->can('create-transaction-gateway');
    }

    public function delete(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('delete-transaction-gateway');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete-any-transaction-gateway');
    }

    public function forceDelete(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('force-delete-transaction-gateway');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force-delete-any-transaction-gateway');
    }

    public function replicate(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('replicate-transaction-gateway');
    }

    public function restore(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('restore-transaction-gateway');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore-any-transaction-gateway');
    }

    public function update(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('update-transaction-gateway');
    }

    public function view(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('view-transaction-gateway');
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view-any-transaction-gateway');
    }
}
