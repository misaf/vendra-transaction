<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Observers;

use RuntimeException;

/**
 * Enforces ledger immutability.
 *
 * Synchronous by necessity: these hooks exist to abort the write, and an
 * observer that ran on the queue would be handed a row that had already
 * changed.
 */
final class LedgerEntryObserver
{
    public function updating(): never
    {
        throw new RuntimeException('Ledger entries are immutable and cannot be updated.');
    }

    public function deleting(): never
    {
        throw new RuntimeException('Ledger entries are immutable and cannot be deleted.');
    }
}
