<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Misaf\VendraSupport\Support\TenantSchema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::withoutForeignKeyConstraints(function (): void {
            $this->createTransactionGatewaysTable();
            $this->createWalletsTable();
            $this->createLedgerEntriesTable();
            $this->createTransactionsTable();
            $this->createTransactionFeesTable();
            $this->createTransactionMetadataTable();
            $this->createTransactionLimitsTable();
        });
    }

    private function createTransactionGatewaysTable(): void
    {
        Schema::create('transaction_gateways', function (Blueprint $table): void {
            $table->id();
            TenantSchema::addTenantColumn($table);
            $table->json('name');
            $table->json('description')
                ->nullable();
            $table->string('slug');
            $table->unsignedBigInteger('position');
            $table->boolean('status');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(TenantSchema::tenantIndex(['slug']));
            $table->index(TenantSchema::tenantIndex(['position']));
            $table->index(TenantSchema::tenantIndex(['status']));
        });
    }

    private function createWalletsTable(): void
    {
        Schema::create('wallets', function (Blueprint $table): void {
            $table->id();
            TenantSchema::addTenantColumn($table);
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('currency_id')
                ->constrained()
                ->restrictOnDelete();
            $table->bigInteger('balance')
                ->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(TenantSchema::tenantIndex(['user_id', 'currency_id']));
            $table->index(TenantSchema::tenantIndex(['currency_id']));
        });
    }

    private function createLedgerEntriesTable(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wallet_id')
                ->constrained()
                ->restrictOnDelete();
            $table->nullableMorphs('source');
            $table->bigInteger('amount');
            $table->bigInteger('balance_after');
            $table->timestampTz('created_at');

            $table->index(['wallet_id', 'created_at']);
        });
    }

    private function createTransactionsTable(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            TenantSchema::addTenantColumn($table);
            $table->foreignId('wallet_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('transaction_gateway_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('counterparty_wallet_id')
                ->nullable()
                ->constrained('wallets')
                ->restrictOnDelete();
            $table->string('transaction_type');
            $table->string('token');
            $table->unsignedBigInteger('amount');
            $table->string('status');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(TenantSchema::tenantIndex(['wallet_id']));
            $table->index(TenantSchema::tenantIndex(['transaction_gateway_id']));
            $table->index(TenantSchema::tenantIndex(['transaction_type']));
            $table->index(TenantSchema::tenantIndex(['token']));
            $table->index(TenantSchema::tenantIndex(['status']));
        });
    }

    private function createTransactionFeesTable(): void
    {
        Schema::create('transaction_fees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transaction_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->timestampsTz();

            $table->index(['transaction_id']);
        });
    }

    private function createTransactionMetadataTable(): void
    {
        Schema::create('transaction_metadata', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transaction_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('key_name');
            $table->string('key_value');
            $table->timestampsTz();

            $table->index(['transaction_id', 'key_name']);
        });
    }

    private function createTransactionLimitsTable(): void
    {
        Schema::create('transaction_limits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wallet_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('transaction_type');
            $table->unsignedBigInteger('amount');
            $table->timestampsTz();

            $table->unique(['wallet_id', 'transaction_type']);
        });
    }

    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function (): void {
            Schema::dropIfExists('transaction_limits');
            Schema::dropIfExists('transaction_metadata');
            Schema::dropIfExists('transaction_fees');
            Schema::dropIfExists('transactions');
            Schema::dropIfExists('ledger_entries');
            Schema::dropIfExists('wallets');
            Schema::dropIfExists('transaction_gateways');
        });
    }
};
