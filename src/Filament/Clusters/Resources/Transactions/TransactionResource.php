<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions;

use App\Filament\Admin\Clusters\Transactions\TransactionsCluster;
use App\Filament\Admin\Resources\Tags\Actions\AddTagAction;
use App\Tables\Columns\CreatedAtTextColumn;
use App\Tables\Columns\ModelLinkColumn;
use App\Tables\Columns\UpdatedAtTextColumn;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Clusters\Cluster;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\CreateTransaction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\ListTransactions;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Pages\ViewTransaction;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers\TransactionMetadataRelationManager;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers\TransactionRelationManager;
use Misaf\VendraTransaction\Models\Transaction;

final class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?int $navigationSort = 1;

    /**
     * @param  string|null  $recordTitleAttribute
     */
    protected static ?string $recordTitleAttribute = 'token';

    protected static ?string $slug = 'transactions';

    /**
     * @var class-string<Cluster>|null
     */
    protected static ?string $cluster = TransactionsCluster::class;

    public static function getBreadcrumb(): string
    {
        return __('navigation.transaction');
    }

    public static function getModelLabel(): string
    {
        return __('navigation.transaction');
    }

    public static function getNavigationGroup(): string
    {
        return __('navigation.transaction_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.transaction');
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'tags']);
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['token', 'tags.name'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('form.username')       => $record?->user?->username,
            __('form.status')         => $record->status,
            __('tag::navigation.tag') => new HtmlString("<span dir='ltr'>" . collect($record->tags->pluck('name'))
                ->map(fn($tag) => "#{$tag}")
                ->implode(' ') . '</span>'),
        ];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'view'   => ViewTransaction::route('/{record}'),
        ];
    }

    /**
     * @return array<class-string<RelationManager>|RelationGroup|RelationManagerConfiguration>
     */
    public static function getRelations(): array
    {
        return [
            TransactionMetadataRelationManager::class,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('transaction_type')
                    ->columnSpanFull()
                    ->label(__('form.category'))
                    ->native(false)
                    ->options(TransactionTypeEnum::class)
                    ->required(),

                Select::make('user_id')
                    ->columnSpanFull()
                    ->label(__('form.username'))
                    ->native(false)
                    ->preload()
                    ->relationship('user', 'username')
                    ->required()
                    ->searchable()
                    ->hiddenOn(TransactionRelationManager::class),

                TextInput::make('amount')
                    ->autocomplete(false)
                    ->columnSpanFull()
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->label(__('transaction::attributes.amount'))
                    ->minValue(1)
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row')
                    ->label('#')
                    ->rowIndex(),

                ModelLinkColumn::make('user.username')
                    ->alignCenter()
                    ->label(__('model.user'))
                    ->searchable(),

                TextColumn::make('transactionGateway.name')
                    ->label(__('model.transaction_gateway')),

                TextColumn::make('transaction_type')
                    ->badge()
                    ->label(__('transaction::attributes.transaction_type')),

                TextColumn::make('token')
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage(__('transaction::messages.token_copied'))
                    ->copyMessageDuration(1500)
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->formatStateUsing(fn(string $state): string => Str::of($state)->split(4)->implode(' '))
                    ->label(__('transaction::attributes.token'))
                    ->searchable(isGlobal: true),

                TextColumn::make('amount')
                    ->alignCenter()
                    ->copyable()
                    ->copyMessage(__('transaction::messages.amount_copied'))
                    ->copyMessageDuration(1500)
                    ->extraCellAttributes(['dir' => 'ltr'])
                    ->label(__('transaction::attributes.amount'))
                    ->numeric(locale: 'en', maxDecimalPlaces: 0),

                TextColumn::make('status')
                    ->alignStart()
                    ->label(__('transaction::attributes.status')),

                SpatieTagsColumn::make('tags')
                    ->label(__('tag::navigation.tag'))
                    ->action(AddTagAction::make())
                    ->toggleable(),

                CreatedAtTextColumn::make('created_at')
                    ->label(__('transaction::attributes.created_at')),

                UpdatedAtTextColumn::make('updated_at')
                    ->label(__('transaction::attributes.updated_at')),
            ])
            ->filters(
                [
                    TrashedFilter::make(),
                    QueryBuilder::make()
                        ->constraints([
                            SelectConstraint::make('transaction_type')
                                ->label(__('transaction::attributes.transaction_type'))
                                ->multiple()
                                ->options(TransactionTypeEnum::class),

                            TextConstraint::make('token')
                                ->label(__('transaction::attributes.token')),

                            NumberConstraint::make('amount')
                                ->label(__('transaction::attributes.amount')),

                            SelectConstraint::make('status')
                                ->label(__('transaction::attributes.status'))
                                ->multiple()
                                ->options(TransactionStatusEnum::class),

                            DateConstraint::make('created_at')
                                ->label(__('transaction::attributes.created_at')),

                            DateConstraint::make('updated_at')
                                ->label(__('transaction::attributes.updated_at')),
                        ]),
                ],
                layout: FiltersLayout::AboveContentCollapsible,
            )
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    Action::make('deposit-info')
                        ->icon('heroicon-s-eye')
                        ->label(__('اطلاعات خرید'))
                        ->requiresConfirmation()
                        ->slideOver()
                        ->modalDescription(function () {
                            return __('نمایش اطلاعات تراکنش - مسیر مستقیم درگاه');
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false),

                    Action::make('withdrawal-info')
                        ->icon('heroicon-s-eye')
                        ->label(__('اطلاعات تراکنش'))
                        ->requiresConfirmation()
                        ->slideOver()
                        ->modalDescription(function () {
                            return __('نمایش اطلاعات تراکنش - مسیر مستقیم درگاه');
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->visible(function (Transaction $record): bool {
                            return TransactionService::isWithdrawal($record)
                                && TransactionService::isApproved($record);
                        }),

                    Action::make(TransactionStatusEnum::Approved->value)
                        ->action(function (Transaction $record): void {
                            TransactionService::updateTransactionStatus($record, TransactionStatusEnum::Processing);
                        })
                        ->icon('heroicon-s-pencil')
                        ->label('تایید')
                        ->requiresConfirmation()
                        ->visible(function (Transaction $record): bool {
                            return TransactionService::isWithdrawal($record)
                                && TransactionService::isReview($record);
                        }),

                    Action::make(TransactionStatusEnum::Declined->value)
                        ->action(function (Transaction $record): void {
                            TransactionService::updateTransactionStatus($record, TransactionStatusEnum::Declined);
                        })
                        ->color(Color::Red)
                        ->icon('heroicon-s-no-symbol')
                        ->label('برگشت')
                        ->requiresConfirmation()
                        ->visible(function (Transaction $record): bool {
                            return TransactionService::isWithdrawal($record)
                                && TransactionService::isReview($record);
                        }),

                    Action::make('tag')
                        ->icon('heroicon-s-tag')
                        ->label(__('tag::navigation.tag'))
                        ->schema([
                            SpatieTagsInput::make('tags')
                                ->label(__('tag::navigation.tag'))
                                ->reorderable(),
                        ]),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
