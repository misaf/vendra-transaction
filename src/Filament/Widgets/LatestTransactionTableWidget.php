<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Widgets;

use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;
use Misaf\VendraTransaction\Models\Transaction;

final class LatestTransactionTableWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 1;
    }

    public static function isDiscovered(): bool
    {
        return true;
    }

    public static function canView(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('vendra-transaction::widgets.latest_transaction_table'))
            ->query(Transaction::take(10))
            ->columns([
                TextColumn::make('transactionGateway.name')
                    ->label(__('vendra-transaction::navigation.transaction_gateway')),

                TextColumn::make('transaction_type')
                    ->badge()
                    ->label(__('vendra-transaction::attributes.transaction_type')),

                TextColumn::make('user.username')
                    ->label(__('vendra-user::attributes.username')),

                TextColumn::make('token')
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage(__('vendra-transaction::messages.token_copied'))
                    ->copyMessageDuration(1500)
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->formatStateUsing(function (string $state): string {
                        return Str::of($state)->split(4)->implode(' ');
                    })
                    ->label(__('vendra-transaction::attributes.token')),

                TextColumn::make('amount')
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage(__('vendra-transaction::messages.amount_copied'))
                    ->copyMessageDuration(1500)
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.amount'))
                    ->numeric(locale: 'en', maxDecimalPlaces: 0),

                TextColumn::make('status')
                    ->alignStart()
                    ->label(__('vendra-transaction::attributes.status')),

                SpatieTagsColumn::make('tags')
                    ->label(__('vendra-tagger::navigation.tag')),

                TextColumn::make('created_at')
                    ->label(__('vendra-transaction::attributes.status')),
            ])
            ->paginated(false)
            ->searchable(false);
    }
}
