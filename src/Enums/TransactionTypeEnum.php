<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum TransactionTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case Commission = 'commission';
    case Bonus = 'bonus';
    case Transfer = 'transfer';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return match ($this) {
            self::Deposit    => Color::Green,
            self::Withdrawal => Color::Red,
            self::Commission => Color::Green,
            self::Bonus      => Color::Purple,
            self::Transfer   => Color::Blue,
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Deposit    => Heroicon::OutlinedCurrencyDollar,
            self::Withdrawal => Heroicon::OutlinedCurrencyDollar,
            self::Commission => Heroicon::OutlinedCurrencyDollar,
            self::Bonus      => Heroicon::OutlinedTrophy,
            self::Transfer   => Heroicon::OutlinedTrophy,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Deposit    => __('vendra-transaction::enums.transaction_type_deposit'),
            self::Withdrawal => __('vendra-transaction::enums.transaction_type_withdrawal'),
            self::Commission => __('vendra-transaction::enums.transaction_type_commission'),
            self::Bonus      => __('vendra-transaction::enums.transaction_type_bonus'),
            self::Transfer   => __('vendra-transaction::enums.transaction_type_transfer'),
        };
    }
}
