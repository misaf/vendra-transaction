<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Misaf\VendraSupport\Support\TagIntegration;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\ApproveTransactionAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\DeclineTransactionAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\DepositInfoAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\TagTransactionAction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Actions\WithdrawalInfoAction;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row')
                    ->label('#')
                    ->rowIndex()->sortable(['id']),

                TextColumn::make('user.username')
                    ->alignCenter()
                    ->label(__('vendra-user::navigation.user'))
                    ->searchable(),

                TextColumn::make('transactionGateway.name')
                    ->label(__('vendra-transaction::navigation.transaction_gateway')),

                TextColumn::make('transaction_type')
                    ->badge()
                    ->label(__('vendra-transaction::attributes.transaction_type')),

                TextColumn::make('token')
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage(__('vendra-transaction::messages.token_copied'))
                    ->copyMessageDuration(1500)
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->formatStateUsing(fn(string $state): string => Str::of($state)->split(4)->implode(' '))
                    ->label(__('vendra-transaction::attributes.token'))
                    ->searchable(isGlobal: true),

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

                ...TagIntegration::isAvailable() ? [
                    SpatieTagsColumn::make('tags')
                        ->label(__('vendra-support::attributes.tags'))
                        ->type(Transaction::TAG_TYPE)
                        ->toggleable(),
                ] : [],

                TextColumn::make('created_at')
                    ->alignCenter()
                    ->badge()
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.created_at'))
                    ->sinceTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->when(
                        app()->isLocale('fa'),
                        fn(TextColumn $column) => $column->jalaliDateTime('Y-m-d H:i', latinNumbers: true),
                        fn(TextColumn $column) => $column->dateTime('Y-m-d H:i')
                    ),

                TextColumn::make('updated_at')
                    ->alignCenter()
                    ->badge()
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.updated_at'))
                    ->sinceTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->when(
                        app()->isLocale('fa'),
                        fn(TextColumn $column) => $column->jalaliDateTime('Y-m-d H:i', latinNumbers: true),
                        fn(TextColumn $column) => $column->dateTime('Y-m-d H:i')
                    ),
            ])
            ->filters(
                [
                    TrashedFilter::make(),
                    QueryBuilder::make()
                        ->constraints([
                            RelationshipConstraint::make('transactionGateway')
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->preload()
                                        ->searchable()
                                        ->titleAttribute('name'),
                                ),

                            RelationshipConstraint::make('user')
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->preload()
                                        ->searchable()
                                        ->titleAttribute('username'),
                                ),

                            SelectConstraint::make('transaction_type')
                                ->label(__('vendra-transaction::attributes.transaction_type'))
                                ->multiple()
                                ->options(TransactionTypeEnum::class),

                            TextConstraint::make('token')
                                ->label(__('vendra-transaction::attributes.token')),

                            NumberConstraint::make('amount')
                                ->label(__('vendra-transaction::attributes.amount')),

                            SelectConstraint::make('status')
                                ->label(__('vendra-transaction::attributes.status'))
                                ->multiple()
                                ->options(TransactionStatusEnum::class),

                            DateConstraint::make('created_at')
                                ->label(__('vendra-transaction::attributes.created_at')),

                            DateConstraint::make('updated_at')
                                ->label(__('vendra-transaction::attributes.updated_at')),
                        ]),
                ],
                layout: FiltersLayout::AboveContentCollapsible,
            )
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    DepositInfoAction::make(),

                    WithdrawalInfoAction::make(),

                    ApproveTransactionAction::make(),

                    DeclineTransactionAction::make(),

                    TagTransactionAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(column: 'id', direction: 'desc');
    }
}
