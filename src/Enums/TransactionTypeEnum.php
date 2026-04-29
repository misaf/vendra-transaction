<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

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

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::Deposit    => 'heroicon-o-currency-dollar',
            self::Withdrawal => 'heroicon-o-currency-dollar',
            self::Commission => 'heroicon-o-currency-dollar',
            self::Bonus      => 'heroicon-o-trophy',
            self::Transfer   => 'heroicon-o-trophy',
        };
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Deposit    => __('vendra-transaction::transaction_type_enum.deposit'),
            self::Withdrawal => __('vendra-transaction::transaction_type_enum.withdrawal'),
            self::Commission => __('vendra-transaction::transaction_type_enum.commission'),
            self::Bonus      => __('vendra-transaction::transaction_type_enum.bonus'),
            self::Transfer   => __('vendra-transaction::transaction_type_enum.transfer'),
        };
    }
}
