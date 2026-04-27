<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraUser\Models\User;

final class TransactionGatewayPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-transaction-gateway');
    }

    /**
     * @param User $user
     * @param TransactionGateway $transactionGateway
     * @return bool
     */
    public function delete(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('delete-transaction-gateway');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete-any-transaction-gateway');
    }

    /**
     * @param User $user
     * @param TransactionGateway $transactionGateway
     * @return bool
     */
    public function forceDelete(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('force-delete-transaction-gateway');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force-delete-any-transaction-gateway');
    }

    /**
     * @param User $user
     * @param TransactionGateway $transactionGateway
     * @return bool
     */
    public function replicate(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('replicate-transaction-gateway');
    }

    /**
     * @param User $user
     * @param TransactionGateway $transactionGateway
     * @return bool
     */
    public function restore(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('restore-transaction-gateway');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore-any-transaction-gateway');
    }

    /**
     * @param User $user
     * @param TransactionGateway $transactionGateway
     * @return bool
     */
    public function update(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('update-transaction-gateway');
    }

    /**
     * @param User $user
     * @param TransactionGateway $transactionGateway
     * @return bool
     */
    public function view(User $user, TransactionGateway $transactionGateway): bool
    {
        return $user->can('view-transaction-gateway');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-transaction-gateway');
    }
}
