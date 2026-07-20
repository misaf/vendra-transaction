<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Livewire\Component as Livewire;
use Misaf\VendraSupport\Filament\Concerns\HasDefaultAvatarImageUrl;
use Misaf\VendraSupport\Filament\Concerns\InteractsWithTranslatedTableRecords;
use Misaf\VendraTransaction\Models\TransactionGateway;

final class TransactionGatewayTable
{
    use HasDefaultAvatarImageUrl;
    use InteractsWithTranslatedTableRecords;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row')
                    ->label('#')
                    ->rowIndex()
                    ->sortable(['id']),

                SpatieMediaLibraryImageColumn::make('image')
                    ->alignCenter()
                    ->collection(TransactionGateway::MEDIA_COLLECTION)
                    ->conversion('thumb-table')
                    ->defaultImageUrl(fn(TransactionGateway $record): string => static::defaultAvatarImageUrl($record->name))
                    ->extraImgAttributes(['class' => 'saturate-50', 'loading' => 'lazy'])
                    ->label(__('vendra-transaction::attributes.image'))
                    ->stacked(),

                TextColumn::make('name')
                    ->alignStart()
                    ->label(__('vendra-transaction::attributes.name'))
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('vendra-transaction::attributes.description'))
                    ->state(fn(TransactionGateway $record, Livewire $livewire): string => static::translatedAttribute($record, 'description', $livewire))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->alignStart()
                    ->label(__('vendra-transaction::attributes.slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('transactions_count')
                    ->alignCenter()
                    ->badge()
                    ->counts('transactions')
                    ->label(__('vendra-transaction::navigation.transactions')),

                ToggleColumn::make('status')
                    ->label(__('vendra-transaction::attributes.status'))
                    ->onIcon(Heroicon::Bolt),

                TextColumn::make('created_at')
                    ->alignCenter()
                    ->badge()
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.created_at'))
                    ->sinceTooltip()
                    ->when(
                        app()->isLocale('fa'),
                        fn(TextColumn $column) => $column->jalaliDateTime('Y-m-d H:i', latinNumbers: true),
                        fn(TextColumn $column) => $column->dateTime('Y-m-d H:i'),
                    ),

                TextColumn::make('updated_at')
                    ->alignCenter()
                    ->badge()
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('vendra-transaction::attributes.updated_at'))
                    ->sinceTooltip()
                    ->when(
                        app()->isLocale('fa'),
                        fn(TextColumn $column) => $column->jalaliDateTime('Y-m-d H:i', latinNumbers: true),
                        fn(TextColumn $column) => $column->dateTime('Y-m-d H:i'),
                    ),
            ])
            ->filters(
                [
                    QueryBuilder::make()
                        ->constraints([
                            TextConstraint::make('name')
                                ->label(__('vendra-transaction::attributes.name')),

                            TextConstraint::make('slug')
                                ->label(__('vendra-transaction::attributes.slug')),

                            BooleanConstraint::make('status')
                                ->label(__('vendra-transaction::attributes.status')),
                        ]),
                ],
                layout: FiltersLayout::AboveContentCollapsible,
            )
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make(),

                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(column: 'id', direction: 'desc')
            ->reorderable(column: 'position', direction: 'desc');
    }
}
