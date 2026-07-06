<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionPolicy
{
    use HandlesAuthorization;

    public function create(Authorizable $user): bool
    {
        return $user->can('create-transaction');
    }

    public function delete(Authorizable $user, Transaction $transaction): bool
    {
        return $user->can('delete-transaction');
    }

    public function deleteAny(Authorizable $user): bool
    {
        return $user->can('delete-any-transaction');
    }

    public function forceDelete(Authorizable $user, Transaction $transaction): bool
    {
        return $user->can('force-delete-transaction');
    }

    public function forceDeleteAny(Authorizable $user): bool
    {
        return $user->can('force-delete-any-transaction');
    }

    public function replicate(Authorizable $user, Transaction $transaction): bool
    {
        return $user->can('replicate-transaction');
    }

    public function restore(Authorizable $user, Transaction $transaction): bool
    {
        return $user->can('restore-transaction');
    }

    public function restoreAny(Authorizable $user): bool
    {
        return $user->can('restore-any-transaction');
    }

    public function update(Authorizable $user, Transaction $transaction): bool
    {
        return $user->can('update-transaction');
    }

    public function view(Authorizable $user, Transaction $transaction): bool
    {
        return $user->can('view-transaction');
    }

    public function viewAny(Authorizable $user): bool
    {
        return $user->can('view-any-transaction');
    }
}
