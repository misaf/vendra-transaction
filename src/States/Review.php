<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\States;

use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

final class Review extends TransactionState
{
    public static string $name = 'review';

    /**
     * @return array<string>
     */
    public function getColor(): array
    {
        return Color::Purple;
    }

    public function getIcon(): Heroicon
    {
        return Heroicon::OutlinedEye;
    }

    public function getLabel(): string
    {
        return __('vendra-transaction::enums.transaction_status_review');
    }
}
