<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Misaf\VendraCurrency\Models\Currency;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Models\TransactionGateway;
use Misaf\VendraTransaction\Support\TransactionUsers;

/**
 * The create form selects a user and a currency instead of a raw wallet;
 * the wallet is resolved (and provisioned on first use) from that pair in
 * `CreateTransaction::mutateFormDataBeforeCreate()`.
 */
final class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->columnSpan(['lg' => 1])
                    ->label(__('vendra-transaction::attributes.user'))
                    ->native(false)
                    ->options(fn(): array => self::userOptions())
                    ->required()
                    ->searchable(),

                Select::make('currency_id')
                    ->columnSpan(['lg' => 1])
                    ->label(__('vendra-transaction::attributes.currency'))
                    ->native(false)
                    ->options(
                        fn(): array => Currency::query()
                            ->where('status', true)
                            ->orderByDesc('is_default')
                            ->orderBy('position')
                            ->pluck('iso_code', 'id')
                            ->all(),
                    )
                    ->required()
                    ->searchable(),

                Select::make('transaction_gateway_id')
                    ->columnSpan(['lg' => 1])
                    ->getOptionLabelFromRecordUsing(fn(TransactionGateway $record): string => (string) $record->name)
                    ->label(__('vendra-transaction::attributes.transaction_gateway'))
                    ->native(false)
                    ->preload()
                    ->relationship('transactionGateway', modifyQueryUsing: fn($query) => $query->enabled())
                    ->required()
                    ->searchable(),

                Select::make('transaction_type')
                    ->columnSpan(['lg' => 1])
                    ->label(__('vendra-transaction::attributes.transaction_type'))
                    ->live()
                    ->native(false)
                    ->options(TransactionTypeEnum::class)
                    ->required(),

                Select::make('counterparty_user_id')
                    ->columnSpan(['lg' => 1])
                    ->different('user_id')
                    ->label(__('vendra-transaction::attributes.counterparty_wallet'))
                    ->native(false)
                    ->options(fn(): array => self::userOptions())
                    ->required(fn(Get $get): bool => self::isTransfer($get))
                    ->searchable()
                    ->visible(fn(Get $get): bool => self::isTransfer($get)),

                TextInput::make('amount')
                    ->columnSpan(['lg' => 1])
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->inputMode('numeric')
                    ->integer()
                    ->label(__('vendra-transaction::attributes.amount'))
                    ->minValue(1)
                    ->required(),
            ]);
    }

    /**
     * The type state may hold the raw value or the cast enum depending on
     * whether it was hydrated from user input or the model cast.
     */
    private static function isTransfer(Get $get): bool
    {
        $type = $get('transaction_type');

        if ( ! $type instanceof TransactionTypeEnum) {
            $type = TransactionTypeEnum::tryFrom((string) ($type ?? ''));
        }

        return TransactionTypeEnum::Transfer === $type;
    }

    /**
     * @return array<int, string>
     */
    private static function userOptions(): array
    {
        return TransactionUsers::model()::query()
            ->orderBy((new (TransactionUsers::model())())->getKeyName())
            ->get()
            ->mapWithKeys(fn(Model $user): array => [
                (int) $user->getKey() => (string) ($user->getAttribute('username')
                    ?? $user->getAttribute('name')
                    ?? "#{$user->getKey()}"),
            ])
            ->all();
    }
}
