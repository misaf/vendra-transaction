<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Exceptions;

use RuntimeException;

final class InsufficientBalanceException extends RuntimeException
{
    public static function forWallet(int $walletId, int $balance, int $amount): self
    {
        return new self(
            "Wallet [{$walletId}] balance [{$balance}] cannot absorb a ledger entry of [{$amount}].",
        );
    }
}
