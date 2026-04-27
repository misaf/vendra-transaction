<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    case Approved = 'approved';
    case Declined = 'declined';
    case Failed = 'failed';
    case Pending = 'pending';
    case Review = 'review';
    case Processing = 'processing';

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
            self::Approved   => Color::Green,
            self::Declined   => Color::Red,
            self::Failed     => Color::Rose,
            self::Pending    => Color::Yellow,
            self::Review     => Color::Indigo,
            self::Processing => Color::Blue,
        };
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::Approved   => 'heroicon-o-check-circle',
            self::Declined   => 'heroicon-o-x-circle',
            self::Failed     => 'heroicon-o-exclamation-circle',
            self::Pending    => 'heroicon-o-clock',
            self::Review     => 'heroicon-o-eye',
            self::Processing => 'heroicon-o-arrow-path',
        };
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Approved   => __('transaction::transaction_status_enum.approved'),
            self::Declined   => __('transaction::transaction_status_enum.declined'),
            self::Failed     => __('transaction::transaction_status_enum.failed'),
            self::Pending    => __('transaction::transaction_status_enum.pending'),
            self::Review     => __('transaction::transaction_status_enum.review'),
            self::Processing => __('transaction::transaction_status_enum.processing'),
        };
    }
}
