<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers;

use App\Tables\Columns\CreatedAtTextColumn;
use App\Tables\Columns\UpdatedAtTextColumn;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;

final class TransactionLimitRelationManager extends RelationManager
{
    protected static string $relationship = 'transactionLimits';

    public static function getModelLabel(): string
    {
        return __('vendra-transaction::navigation.transaction_limit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vendra-transaction::navigation.transaction_limit');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('vendra-transaction::navigation.transaction_limit');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('transaction_type')
                    ->columnSpanFull()
                    ->label(__('form.category'))
                    ->native(false)
                    ->options(TransactionTypeEnum::class)
                    ->required(),

                TextInput::make('amount')
                    ->autocomplete(false)
                    ->columnSpanFull()
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.amount'))
                    ->minValue(1)
                    ->numeric()
                    ->required(),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge($this->getOwnerRecord()->transactions()->count()),

            TransactionTypeEnum::Deposit->value => Tab::make()
                ->badge(number_format($this->getOwnerRecord()->transactions()->deposit()->count()))
                ->label(TransactionTypeEnum::Deposit->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->deposit()),

            TransactionTypeEnum::Withdrawal->value => Tab::make()
                ->badge(number_format($this->getOwnerRecord()->transactions()->withdrawal()->count()))
                ->label(TransactionTypeEnum::Withdrawal->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->withdrawal()),

            TransactionTypeEnum::Withdrawal->value => Tab::make()
                ->badge(number_format($this->getOwnerRecord()->transactions()->withdrawal()->count()))
                ->label(TransactionTypeEnum::Withdrawal->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->withdrawal()),

            TransactionTypeEnum::Commission->value => Tab::make()
                ->badge(number_format($this->getOwnerRecord()->transactions()->commission()->count()))
                ->label(TransactionTypeEnum::Commission->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->commission()),

            TransactionTypeEnum::Transfer->value => Tab::make()
                ->badge(number_format($this->getOwnerRecord()->transactions()->transfer()->count()))
                ->label(TransactionTypeEnum::Transfer->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->transfer()),

            TransactionTypeEnum::Bonus->value => Tab::make()
                ->badge(number_format($this->getOwnerRecord()->transactions()->bonus()->count()))
                ->label(TransactionTypeEnum::Bonus->getLabel())
                ->modifyQueryUsing(fn(Builder $query) => $query->bonus()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_type')
                    ->badge()
                    ->label(__('vendra-transaction::attributes.transaction_type')),
                TextColumn::make('amount')
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage(__('Amount copied to clipboard'))
                    ->copyMessageDuration(1500)
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.amount'))
                    ->numeric(locale: 'en', maxDecimalPlaces: 0),
                CreatedAtTextColumn::make('created_at')
                    ->label(__('vendra-transaction::attributes.created_at')),
                UpdatedAtTextColumn::make('updated_at')
                    ->label(__('vendra-transaction::attributes.updated_at')),
            ])
            ->filters(
                [
                    QueryBuilder::make()
                        ->constraints([
                            SelectConstraint::make('transaction_type')
                                ->label(__('vendra-transaction::attributes.transaction_type'))
                                ->multiple()
                                ->options(TransactionTypeEnum::class),
                            NumberConstraint::make('amount')
                                ->label(__('vendra-transaction::attributes.amount')),
                            DateConstraint::make('created_at')
                                ->label(__('vendra-transaction::attributes.created_at')),
                            DateConstraint::make('updated_at')
                                ->label(__('vendra-transaction::attributes.updated_at')),
                        ]),
                ],
                layout: FiltersLayout::AboveContentCollapsible,
            )
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
