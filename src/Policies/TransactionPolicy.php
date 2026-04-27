<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraUser\Models\User;

final class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create-transaction');
    }

    /**
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->can('delete-transaction');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete-any-transaction');
    }

    /**
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->can('force-delete-transaction');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force-delete-any-transaction');
    }

    /**
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function replicate(User $user, Transaction $transaction): bool
    {
        return $user->can('replicate-transaction');
    }

    /**
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->can('restore-transaction');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore-any-transaction');
    }

    /**
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->can('update-transaction');
    }

    /**
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->can('view-transaction');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-transaction');
    }
}
