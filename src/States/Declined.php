<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class Declined extends TransactionState
{
    public static string $name = 'declined';

    public function isFinal(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return Color::Red;
    }

    public function getIcon(): Heroicon
    {
        return Heroicon::OutlinedXCircle;
    }

    public function getLabel(): string
    {
        return __('vendra-transaction::enums.transaction_status_declined');
    }
}
