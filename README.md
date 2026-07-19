# Vendra Transaction

Wallet and ledger based financial transactions for Vendra: per-user,
per-currency wallet balances, an immutable ledger, a state-machine
transaction lifecycle with multi-gateway support, and a Filament admin
cluster for transactions, gateways, and wallets.

## How it works

1. Each user holds one `Wallet` per currency (created on first use via
   `TransactionService::walletFor()` / `defaultWalletFor()`), with a cached
   `balance` in the currency's minor units.
2. The ledger is the single source of balance truth: every movement is an
   immutable `LedgerEntry` (signed amount, `balance_after` snapshot,
   polymorphic source) written by `LedgerService` together with the cached
   wallet balance under a row lock. Overdrafts throw
   `InsufficientBalanceException`.
3. A `Transaction` is a gateway-facing money movement created through
   `TransactionService::createTransaction()`, which enforces per-wallet
   `TransactionLimit`s and attaches optional fee and metadata rows. Its
   `amount` is always absolute; the ledger sign derives from the
   transaction type (deposit, withdrawal, commission, transfer, bonus).
4. The lifecycle is a Spatie model-states machine:
   `Pending → Processing → Review → Approved / Declined / Failed`, driven
   by `approve()`, `decline()`, `fail()`, `markProcessing()`, and
   `markReview()`. Settlement into the ledger happens exactly once, inside
   the transition to `Approved` — principal, mirrored transfer credit, and
   fee commit atomically with the status change.
5. Gateways are admin-managed labels (translatable name and description, a
   scalar `slug` lookup key, logo, sortable position); payment-processing
   logic lives in the host application. Internal movements use the
   `internal-transactions` gateway.

The module is decoupled from any concrete user package — the wallet owner
model resolves from `auth.providers.users.model` — and consumes currencies
through `misaf/vendra-currency`.

## Requirements

- PHP 8.3+
- Laravel 13
- Filament 5
- `misaf/vendra-support`
- `misaf/vendra-currency`
- `misaf/vendra-multimedia`

## Installation

```bash
composer require misaf/vendra-transaction
php artisan vendor:publish --tag=vendra-transaction-migrations
php artisan migrate
```

Optional translations publish:

```bash
php artisan vendor:publish --tag=vendra-transaction-translations
```

## Balance verification

Recompute cached wallet balances from the ledger and report (or repair)
drift:

```bash
php artisan vendra-transaction:verify-balances
php artisan vendra-transaction:verify-balances --repair
```

## Testing

```bash
composer test
```

## License

MIT. See [LICENSE](LICENSE).
