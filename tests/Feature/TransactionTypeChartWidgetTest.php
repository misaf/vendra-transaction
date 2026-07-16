<?php

declare(strict_types=1);

use Misaf\VendraTenant\Database\Factories\TenantFactory;
use Misaf\VendraTransaction\Database\Factories\TransactionFactory;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Enums\TransactionStatusEnum;
use Misaf\VendraTransaction\Enums\TransactionTypeEnum;
use Misaf\VendraTransaction\Filament\Widgets\TransactionTypeChartWidget;
use Misaf\VendraUser\Database\Factories\UserFactory;

it('charts seven days of approved deposit withdrawal and bonus trends in distinct colors', function (): void {
    app()->setLocale('en');
    $this->freezeTime();

    TenantFactory::new()->enabled()->createOne()->makeCurrent();

    $transactionGateway = TransactionGatewayFactory::new()->createOne();
    $user = UserFactory::new()->createOne();
    $relationships = [
        'transaction_gateway_id' => $transactionGateway->id,
        'user_id'                => $user->id,
    ];

    TransactionFactory::new()->deposit()->createOne([
        ...$relationships,
        'amount'     => 125,
        'status'     => TransactionStatusEnum::Approved,
        'created_at' => now()->subDays(6),
    ]);

    TransactionFactory::new()->deposit()->createOne([
        ...$relationships,
        'amount'     => 50,
        'status'     => TransactionStatusEnum::Approved,
        'created_at' => now(),
    ]);

    TransactionFactory::new()->withdrawal()->createOne([
        ...$relationships,
        'amount'     => 75,
        'status'     => TransactionStatusEnum::Approved,
        'created_at' => now()->subDays(3),
    ]);

    TransactionFactory::new()->bonus()->createOne([
        ...$relationships,
        'amount'     => 25,
        'status'     => TransactionStatusEnum::Approved,
        'created_at' => now(),
    ]);

    TransactionFactory::new()->deposit()->createOne([
        ...$relationships,
        'amount'     => 500,
        'status'     => TransactionStatusEnum::Pending,
        'created_at' => now(),
    ]);

    $widget = app(TransactionTypeChartWidget::class);
    $data = (new ReflectionMethod($widget, 'getData'))->invoke($widget);
    $type = (new ReflectionMethod($widget, 'getType'))->invoke($widget);

    expect($type)->toBe('bar')
        ->and($widget->getHeading())->toBe('Seven-Day Transaction Trends')
        ->and($data['labels'])->toBe(collect(range(6, 0))
        ->map(static fn(int $daysAgo): string => now()->subDays($daysAgo)->translatedFormat('D'))
        ->all())
        ->and($data['datasets'])->toHaveCount(3)
        ->and($data['datasets'][0])->toMatchArray([
            'label'           => TransactionTypeEnum::Deposit->getLabel(),
            'data'            => [125, 0, 0, 0, 0, 0, 50],
            'backgroundColor' => '#22c55e',
        ])
        ->and($data['datasets'][1])->toMatchArray([
            'label'           => TransactionTypeEnum::Withdrawal->getLabel(),
            'data'            => [0, 0, 0, 75, 0, 0, 0],
            'backgroundColor' => '#ef4444',
        ])
        ->and($data['datasets'][2])->toMatchArray([
            'label'           => TransactionTypeEnum::Bonus->getLabel(),
            'data'            => [0, 0, 0, 0, 0, 0, 25],
            'backgroundColor' => '#a855f7',
        ]);
});
