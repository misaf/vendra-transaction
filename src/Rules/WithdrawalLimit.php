<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Misaf\VendraUser\Models\User;

final class WithdrawalLimit implements ValidationRule
{
    public function __construct(public User $user) {}

    /**
     * {@inheritDoc}
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isWithdrawalRakeRequirementNotMet($value)) {
            $fail(__('transaction::validation.isWithdrawalRakeRequirementNotMet'));
        }
    }

    private function isWithdrawalRakeRequirementNotMet(mixed $value): bool
    {
        $amount = $this->user->transactionLimits()->withdrawal()->value('amount') ?? 0;

        return $value > $amount;
    }
}
