---
name: vendra-transaction-development
description: "Create, modify, review, or test the Vendra Transaction package in packages/vendra-transaction. Use for Wallet, LedgerEntry, Transaction, TransactionGateway, fees, limits, metadata, the transaction state machine (States, ApproveTransactionTransition), LedgerService, TransactionService, verify-balances command, policies, Filament resources and widgets, migrations, translations, and package wiring."
---

# Vendra Transaction

## Workflow

## Translatable Persistence

- Making a persisted model field translatable is an explicit domain choice unless this package already requires it.
- Every field listed in a model's `$translatable` array must definitely use a JSON database column. Keep its model traits/casts, factories, validation, Filament locale UI, API serialization, and tests translation-aware.
- A field not listed in `$translatable` must use the appropriate scalar database type and must not use Spatie Translatable, translatable slug traits, locale switchers, translated callbacks, or translation-shaped array data.

- Register every table whose migration calls `TenantSchema::addTenantColumn()` with `TenantTableRegistry` in this package's service provider, preserving configured table names and connections, so `vendra-tenant:enable {tenant}` can retrofit schemas migrated before tenancy was enabled.

Always use this skill together with `laravel-best-practices` for Laravel PHP and `pest-testing` when tests are added or changed. Use `tailwindcss-development` only when editing Blade or Tailwind UI.

Before code changes, use Laravel Boost `application-info` and `search-docs` for the relevant packages. Prefer Boost database and browser tools over ad hoc debugging.

## Module Boundary

Treat `packages/vendra-transaction` as the source of the wallet/ledger financial domain and its Filament admin UI.

- Use namespace `Misaf\VendraTransaction`.
- Keep domain models, factories, policies, states, services, events, exceptions, console commands, Filament classes, migrations, translations, and tests inside this module.
- Keep cross-module dependencies explicit in `composer.json`; do not introduce a dependency without approval. The module depends on `misaf/vendra-currency`, `misaf/vendra-multimedia`, and `misaf/vendra-support` but must never import `Misaf\VendraUser` or `Misaf\VendraTenant`.

## Domain Standards

The ledger is the single source of balance truth.

- `Wallet` is one balance per user per currency; `balance` is a cache written only by `Services\LedgerService::post()` under `lockForUpdate`, together with an immutable `LedgerEntry` (`amount` signed, `balance_after` snapshot, polymorphic `source`). `LedgerEntry` throws on update/delete; never bypass the service.
- Transaction status is a Spatie model-states machine in `States\` (`Pending → Processing → Review → Approved/Declined/Failed`). `ApproveTransactionTransition` settles principal, transfer counterpart, and fee into the ledger atomically with the status write and dispatches `TransactionApproved`. Use `approve()`, `decline()`, `fail()`, `markProcessing()`, `markReview()`; never write the status column directly.
- `transactions.amount` is absolute minor units; ledger sign derives from `TransactionTypeEnum::ledgerSign()`. Transfers store `counterparty_wallet_id`; insufficient balance raises `InsufficientBalanceException` and rolls the whole approval back.
- Create transactions through `TransactionService::createTransaction()` (enforces per-wallet `TransactionLimit`s, attaches fee and metadata); provision wallets with `walletFor()` / `defaultWalletFor()`.
- Resolve the user model through `Support\TransactionUsers::model()`; the provider attaches the `wallets` relation to the configured auth model.
- Gateways are admin-managed labels: translatable `name`/`description`, scalar `slug` (lookup key; internal slug is `TransactionService::INTERNAL_GATEWAY_SLUG`), media logo, sortable position. No payment-processing logic here.
- Keep the module tenant-agnostic (`BelongsToTenant`, `TenantSchema`, registry registration for `transaction_gateways`, `wallets`, `transactions`); never reference `Misaf\VendraTenant`.
- Tag-consuming models must use `Misaf\VendraSupport\Traits\HasOptionalTags` as the single source of their `tags()` relationship and pivot metadata. Keep the package tag-agnostic: define a stable package-owned tag type, use `TagIntegration` for availability and UI integration, never import the concrete Vendra Tagger model/provider or define the relationship through Spatie `HasTags`, and list Tagger only under Composer `suggest`.

## Filament Standards

Keep every resource tree under `src/Filament/Clusters/Resources/` in the `SalesCluster`, with dedicated `Schemas/*Form.php` and `Tables/*Table.php` classes and state-transition actions under the resource `Actions/`. Any future resource without a cluster belongs under `src/Filament/Resources/` instead.

- Use Filament v5 namespaces and this module's translation keys (`vendra-transaction::attributes`, `::navigation`, `::messages`, `::widgets`).
- Transactions have list/create/view pages only (no edit); lifecycle changes go through the Approve/Decline/Fail actions, whose visibility follows `status->canTransitionTo()`.
- Wallets are read-only (list/view, `canCreate(): false`); the ledger relation manager stays read-only, and transaction limits are managed through their wallet relation manager.
- Gateway resources use the LaraZeus SpatieTranslatable concern with `LocaleSwitcher` on every page.
- Assign `$navigationSort` from the shared `NavigationPriority` cases (`Transactions`, `TransactionGateways`, `Wallets`); update `tests/Unit/AdminNavigationTest.php` when navigation changes.
- State classes implement the Filament `HasColor`/`HasIcon`/`HasLabel` contracts; drive badges from the state instance, not hardcoded maps.
- Prevent N+1 issues in tables and relation managers with eager loading, `withCount`, or computed state based on loaded relationships.

## Permissions

Policy enums are the permission source: `TransactionPolicyEnum`, `TransactionGatewayPolicyEnum` (includes `Reorder`), `WalletPolicyEnum`. Inline models reuse the aggregate's permissions: metadata via the transaction enum, ledger entries (view-only) and limits via the wallet enum. Compose the shared `Authorizes*Abilities` traits; keep case names matching ability names.

## Data And Localization

- The migration stub `create_transactions_table.php.stub` creates `transaction_gateways`, `wallets`, `ledger_entries`, `transactions`, `transaction_fees`, `transaction_metadata`, and `transaction_limits`. Keep the published app migration in sync with the stub.
- Keep factories tenant-safe and ledger-safe: wallet factories never set `balance`; post entries through `LedgerService` in tests. The `TransactionFactory` `approved()` state sets the column without settling — use `->create()->approve()` when ledger rows must exist.
- Update `en`, `de`, and `fa` together; keep translation keys sorted and in parity.

## Testing And Verification

- Cover ledger integrity (cached balance vs entries, immutability, overdraft rejection), state transitions and settlement, limits, service contracts, policy coverage, translation parity, and strict-authorization rendering of every page.
- Keep Pest architecture tests in `tests/ArchTest.php`: the `php`, `security`, and `laravel` presets plus `not->toUse('Misaf\VendraTenant')` and `not->toUse('Misaf\VendraUser')`.
- Run the suite from the host app: `php artisan test --compact --testsuite=vendra-transaction`. If PHP files changed, run `vendor/bin/pint --dirty --format agent`.
