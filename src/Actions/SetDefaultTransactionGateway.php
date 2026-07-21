<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Actions;

use Illuminate\Support\Facades\DB;
use Misaf\VendraTransaction\Models\TransactionGateway;

final class SetDefaultTransactionGateway
{
    public function execute(TransactionGateway $gateway): void
    {
        DB::transaction(function () use ($gateway): void {
            TransactionGateway::query()
                ->lockForUpdate()
                ->get(['id']);

            $gateway->update([
                'status'     => true,
                'is_default' => true,
            ]);
        });
    }
}
