<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers;

use App\Tables\Columns\CreatedAtTextColumn;
use App\Tables\Columns\UpdatedAtTextColumn;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

final class TransactionMetadataRelationManager extends RelationManager
{
    protected static string $relationship = 'transactionMetadatas';

    public static function getModelLabel(): string
    {
        return __('navigation.transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.transaction');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('navigation.transaction');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('transaction_id')
                ->columnSpanFull()
                ->label(__('model.transaction'))
                ->native(false)
                ->preload()
                ->relationship('transaction', 'token')
                ->required()
                ->searchable(),

            TextInput::make('key_name')
                ->label(__('transaction_metadata.key_name'))
                ->required(),

            TextInput::make('key_value')
                ->label(__('transaction_metadata.key_value'))
                ->required(),
        ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading(__('model.transaction'))
            ->columns([
                TextColumn::make('key_name')
                    ->alignStart()
                    ->label(__('transaction_metadata.key_name')),

                TextColumn::make('key_value')
                    ->alignStart()
                    ->copyable()
                    ->copyMessage(__('vendra-transaction::messages.value_copied'))
                    ->copyMessageDuration(1500)
                    ->label(__('transaction_metadata.key_value')),

                CreatedAtTextColumn::make('created_at'),
                UpdatedAtTextColumn::make('updated_at'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->groups([
                Group::make('key_name')
                    ->collapsible()
                    ->label(__('transaction_metadata.key_name')),
                Group::make('key_value')
                    ->collapsible()
                    ->label(__('transaction_metadata.key_value')),
            ]);
    }
}
