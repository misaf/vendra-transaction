<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->laravel();

arch('the transaction module derives tenancy from the support layer, never a concrete tenant provider')
    ->expect('Misaf\VendraTransaction')
    ->not->toUse('Misaf\VendraTenant');

arch('the transaction module resolves users from configuration, never a concrete user module')
    ->expect('Misaf\VendraTransaction')
    ->not->toUse('Misaf\VendraUser');

arch('the transaction module derives currency support from the support layer, never the currency module')
    ->expect('Misaf\VendraTransaction')
    ->not->toUse('Misaf\VendraCurrency');
