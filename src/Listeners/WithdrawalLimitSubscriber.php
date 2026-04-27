<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Models\Transaction;
use Misaf\VendraTransaction\Models\TransactionLimit;
use Misaf\VendraUser\Models\User;
use Misaf\VendraUserRake\Events\UserRakeIncreasedEvent;

final class WithdrawalLimitSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    private const DEFAULT_MULTIPLIER = 0.6;

    private const OVERDRAW_MULTIPLIER = 0.4;

    private const LOWEST_MULTIPLIER = 0.1;

    /**
     * @var int[]
     */
    private array $specialUserIds = [];

    public function handleRakeIncrease(UserRakeIncreasedEvent $event): void
    {
        $userId = $event->userId;
        $user = User::findOrFail($userId);

        $deposit = TransactionService::sumDeposits($user);
        if ($deposit <= 0) {
            return;
        }

        $withdrawal = TransactionService::sumWithdrawals($user);

        $multiplier = match (true) {
            // 1st arm: if fraud OR special user → lowest
            $this->isFraud($user),
            $this->isSpecialUser($userId) => self::LOWEST_MULTIPLIER,

            // 2nd arm: if withdrawal > deposit → overdraw rate
            $withdrawal > $deposit => self::OVERDRAW_MULTIPLIER,

            // fallback: default rate
            default => self::DEFAULT_MULTIPLIER,
        };

        $amountToAdd = $event->amount * $multiplier;

        $this->getLimit($userId)
            ->increment('amount', $amountToAdd);
    }

    private function isSpecialUser(int $userId): bool
    {
        return in_array($userId, $this->specialUserIds, true);
    }

    private function isFraud(User $user): bool
    {
        return $user->hasTag('fraud');
    }

    public function transactionUpdated(Transaction $transaction): void
    {
        $limit = $this->getLimit($transaction->user_id);
        $amount = abs((int) $transaction->amount);

        switch ($transaction->transaction_type) {
            case TransactionTypeEnum::Deposit:
                $this->applyDeposit($transaction, $limit, $amount);
                break;

            case TransactionTypeEnum::Withdrawal:
                $this->applyWithdrawal($transaction, $limit, $amount);
                break;

            case TransactionTypeEnum::Bonus:
                $this->applyBonus($transaction, $limit);
                break;

            default:
                // ignore other types
        }
    }

    private function getLimit(int $userId): TransactionLimit
    {
        return TransactionLimit::firstOrCreate(
            [
                'user_id'          => $userId,
                'transaction_type' => TransactionTypeEnum::Withdrawal,
            ],
            ['amount' => 0],
        );
    }

    private function applyDeposit(Transaction $transaction, TransactionLimit $limit, int $amount): void
    {
        if ( ! TransactionService::isApproved($transaction)) {
            return;
        }

        $limit->increment('amount', $amount * 0.2);
    }

    private function applyWithdrawal(Transaction $transaction, TransactionLimit $limit, int $amount): void
    {
        if (TransactionService::isReview($transaction)) {
            $limit->decrement('amount', $amount);
        } elseif (TransactionService::isDeclined($transaction)) {
            $limit->increment('amount', $amount);
        }
    }

    private function applyBonus(Transaction $transaction, TransactionLimit $limit): void
    {
        if ( ! TransactionService::isApproved($transaction)) {
            return;
        }

        if ($transaction->hasTag('Top Hand')) {
            return;
        }

        if ($transaction->hasTag('Top Rake')) {
            return;
        }

        $limit->update(['amount' => 0]);
    }

    /**
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            'eloquent.updated: ' . Transaction::class => 'transactionUpdated',
        ];
    }
}
