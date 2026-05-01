<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways;

use App\Filament\Admin\Clusters\Transactions\TransactionsCluster;
use App\Forms\Components\SlugTextInput;
use App\Forms\Components\StatusToggle;
use App\Forms\Components\TranslatableDescriptionTextarea;
use App\Tables\Columns\CreatedAtTextColumn;
use App\Tables\Columns\NameTextColumn;
use App\Tables\Columns\StatusToggleColumn;
use App\Tables\Columns\UpdatedAtTextColumn;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Clusters\Cluster;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Livewire\Component as Livewire;
use Misaf\VendraTenant\Models\Tenant;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\CreateTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\EditTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\ListTransactionGateways;
use Misaf\VendraTransaction\Filament\Clusters\Resources\TransactionGateways\Pages\ViewTransactionGateway;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers\TransactionRelationManager;
use Misaf\VendraTransaction\Models\TransactionGateway;

final class TransactionGatewayResource extends Resource
{
    use Translatable;

    protected static ?string $model = TransactionGateway::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'gateways';

    /**
     * @var class-string<Cluster>|null
     */
    protected static ?string $cluster = TransactionsCluster::class;

    public static function getBreadcrumb(): string
    {
        return __('navigation.transaction_gateway');
    }

    public static function getModelLabel(): string
    {
        return __('navigation.transaction_gateway');
    }

    public static function getNavigationGroup(): string
    {
        return __('navigation.transaction_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.transaction_gateway');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.transaction_gateway');
    }

    public static function getDefaultTranslatableLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListTransactionGateways::route('/'),
            'create' => CreateTransactionGateway::route('/create'),
            'view'   => ViewTransactionGateway::route('/{record}'),
            'edit'   => EditTransactionGateway::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            TransactionRelationManager::class,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                        if (($get('slug') ?? '') === Str::slug($old ?? '')) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->autofocus()
                    ->columnSpan(['lg' => 1])
                    ->label(__('form.name'))
                    ->live(onBlur: true)
                    ->required()
                    ->unique(
                        column: fn(Livewire $livewire) => 'name->' . $livewire->activeLocale,
                        modifyRuleUsing: function (Unique $rule): void {
                            $rule->where('tenant_id', Tenant::current()->id)
                                ->withoutTrashed();
                        },
                    ),

                SlugTextInput::make('slug'),
                TranslatableDescriptionTextarea::make('description'),

                SpatieMediaLibraryFileUpload::make('image')
                    ->columnSpanFull()
                    ->image()
                    ->label(__('form.image'))
                    ->panelLayout('grid')
                    ->responsiveImages(),

                StatusToggle::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row')
                    ->label('#')
                    ->rowIndex(),

                SpatieMediaLibraryImageColumn::make('image')
                    ->circular()
                    ->conversion('thumb-table')
                    ->defaultImageUrl(url('coin-payment/images/default.png'))
                    ->extraImgAttributes(['class' => 'saturate-50', 'loading' => 'lazy'])
                    ->label(__('form.image'))
                    ->stacked(),

                NameTextColumn::make('name'),
                StatusToggleColumn::make('status'),
                CreatedAtTextColumn::make('created_at'),
                UpdatedAtTextColumn::make('updated_at'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make()
                        ->hidden(function (TransactionGateway $record): bool {
                            return 'internal-transactions' === $record->getAttributeValue('slug');
                        }),

                    DeleteAction::make()
                        ->hidden(function (TransactionGateway $record): bool {
                            return 'internal-transactions' === $record->getAttributeValue('slug');
                        }),

                    Action::make('report')
                        ->label(__('گزارشات'))
                        ->action(fn(TransactionGateway $record) => $record->advance())
                        ->modalContent(function () {
                            return view('filament.admin.resources.transaction_gateways.pages.actions.report');
                        })
                        ->icon('heroicon-s-chart-pie')
                        ->slideOver()
                        ->color('gray')
                        ->modalSubmitAction(false),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginatedWhileReordering()
            ->reorderable('position');
    }
}
