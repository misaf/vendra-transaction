<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class Approved extends TransactionState
{
    public static string $name = 'approved';

    public function isFinal(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return Color::Green;
    }

    public function getIcon(): Heroicon
    {
        return Heroicon::OutlinedCheckCircle;
    }

    public function getLabel(): string
    {
        return __('vendra-transaction::enums.transaction_status_approved');
    }
}
