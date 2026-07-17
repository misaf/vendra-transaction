<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers\TransactionRelationManager;

final class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('transaction_type')
                    ->columnSpanFull()
                    ->label(__('vendra-transaction::attributes.transaction_type'))
                    ->native(false)
                    ->options(TransactionTypeEnum::class)
                    ->required(),

                Select::make('user_id')
                    ->columnSpanFull()
                    ->label(__('vendra-user::attributes.username'))
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
                    ->label(__('vendra-transaction::attributes.amount'))
                    ->minValue(1)
                    ->numeric()
                    ->required(),
            ]);
    }
}
