<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class Pending extends TransactionState
{
    public static string $name = 'pending';

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return Color::Amber;
    }

    public function getIcon(): Heroicon
    {
        return Heroicon::OutlinedClock;
    }

    public function getLabel(): string
    {
        return __('vendra-transaction::enums.transaction_status_pending');
    }
}
