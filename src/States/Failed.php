<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class Failed extends TransactionState
{
    public static string $name = 'failed';

    public function isFinal(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return Color::Gray;
    }

    public function getIcon(): Heroicon
    {
        return Heroicon::OutlinedExclamationTriangle;
    }

    public function getLabel(): string
    {
        return __('vendra-transaction::enums.transaction_status_failed');
    }
}
