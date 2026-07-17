<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

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

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Approved   => Heroicon::OutlinedCheckCircle,
            self::Declined   => Heroicon::OutlinedXCircle,
            self::Failed     => Heroicon::OutlinedExclamationCircle,
            self::Pending    => Heroicon::OutlinedClock,
            self::Review     => Heroicon::OutlinedEye,
            self::Processing => Heroicon::OutlinedArrowPath,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Approved   => __('vendra-transaction::enums.transaction_status_approved'),
            self::Declined   => __('vendra-transaction::enums.transaction_status_declined'),
            self::Failed     => __('vendra-transaction::enums.transaction_status_failed'),
            self::Pending    => __('vendra-transaction::enums.transaction_status_pending'),
            self::Review     => __('vendra-transaction::enums.transaction_status_review'),
            self::Processing => __('vendra-transaction::enums.transaction_status_processing'),
        };
    }
}
