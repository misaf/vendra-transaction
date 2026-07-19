<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Exceptions;

use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use RuntimeException;

final class TransactionLimitExceededException extends RuntimeException
{
    public static function forWallet(int $walletId, TransactionTypeEnum $transactionType, int $amount, int $limit): self
    {
        return new self(
            "Wallet [{$walletId}] {$transactionType->value} amount [{$amount}] exceeds the configured limit [{$limit}].",
        );
    }
}
