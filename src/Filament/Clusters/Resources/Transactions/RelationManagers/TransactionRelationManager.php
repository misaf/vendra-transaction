<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\RelationManagers;

use Exception;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Facades\TransactionService;
use Misaf\VendraTransaction\Filament\Clusters\Resources\Transactions\TransactionResource;

final class TransactionRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

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

    public static function getBadge(Model $ownerRecord, string $pageClass): string
    {
        return (string) Number::format($ownerRecord->transactions()->count());
    }

    public function form(Schema $schema): Schema
    {
        return TransactionResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TransactionResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $transactionGateway = TransactionService::getTransactionGateway('internal-transactions');

                        if ( ! $transactionGateway || ! isset($transactionGateway->id)) {
                            throw new Exception('Transaction gateway not found for internal-transactions.');
                        }

                        $data['user_id'] = $this->getOwnerRecord()->getKey();
                        $data['transaction_gateway_id'] = $transactionGateway->id;
                        $data['amount'] = TransactionService::getFormattedAmount((int) $data['amount'], $data['transaction_type']);
                        $data['status'] = TransactionStatusEnum::Pending;

                        return $data;
                    }),
            ]);
    }
}
