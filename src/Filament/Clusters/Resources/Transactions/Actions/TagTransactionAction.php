<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Support\Icons\Heroicon;
use Misaf\VendraTransaction\Models\Transaction;

final class TagTransactionAction
{
    public static function make(): Action
    {
        return Action::make('tag')
            ->icon(Heroicon::Tag)
            ->label(__('vendra-tagger::navigation.tag'))
            ->schema([
                SpatieTagsInput::make('tags')
                    ->label(__('vendra-tagger::navigation.tag'))
                    ->type(Transaction::TAG_TYPE)
                    ->reorderable(),
            ]);
    }
}
