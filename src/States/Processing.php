<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class Processing extends TransactionState
{
    public static string $name = 'processing';

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return Color::Blue;
    }

    public function getIcon(): Heroicon
    {
        return Heroicon::OutlinedArrowPath;
    }

    public function getLabel(): string
    {
        return __('vendra-transaction::enums.transaction_status_processing');
    }
}
