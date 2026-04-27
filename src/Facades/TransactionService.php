<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Facades;

use Illuminate\Support\Facades\Facade;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraUser\Models\User;

/**
 * @method static string generateToken()
 * @method static int getFormattedAmount(int $amount, string $transactionType)
 * @method static bool updateTransactionStatus(Transaction $transaction, TransactionStatusEnum $newStatus)
 * @method static bool isApproved(Transaction $transaction)
 * @method static bool isDeclined(Transaction $transaction)
 * @method static bool isFailed(Transaction $transaction)
 * @method static bool isPending(Transaction $transaction)
 * @method static bool isReview(Transaction $transaction)
 * @method static bool isProcessing(Transaction $transaction)
 * @method static bool isDeposit(Transaction $transaction)
 * @method static bool isWithdrawal(Transaction $transaction)
 * @method static bool isCommission(Transaction $transaction)
 * @method static bool isBonus(Transaction $transaction)
 * @method static bool isTransfer(Transaction $transaction)
 * @method static int sumDeposits(User $user)
 * @method static int sumWithdrawals(User $user)
 * @method static int sumCommissions(User $user)
 * @method static int sumBonuses(User $user)
 * @method static bool hasAnyActiveTransactionGateway()
 * @method static bool hasActiveTransactionGateway(string $slug)
 * @method static TransactionGateway getTransactionGateway(string $transactionGateway)
 * @method static bool isInternalTransaction(Transaction $transaction)
 * @method static Transaction createTransaction(string $transactionGateway, User $user, TransactionTypeEnum $transactionType, int $amount, TransactionStatusEnum $status, array $metadatas = [], ?string $token = null)
 * @method static void createTransactionMetadatas(Transaction $transaction, array $metadatas)
 *
 * @see \Misaf\Transaction\Services\TransactionService
 */
final class TransactionService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'transaction-service';
    }
}
