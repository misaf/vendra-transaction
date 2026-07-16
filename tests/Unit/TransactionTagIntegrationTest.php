<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Tests\Unit;

use Misaf\VendraSupport\Contracts\TagResolver;
use Misaf\VendraSupport\Support\EloquentTagResolver;
use Misaf\VendraSupport\Support\TagRelationship;
use Misaf\VendraTransaction\Models\Transaction;
use Spatie\Tags\HasTags;

it('uses the shared typed tag relationship with explicit pivot keys', function (): void {
    app()->instance(
        TagResolver::class,
        new EloquentTagResolver(new TagRelationship(TransactionTestTag::class)),
    );

    $relation = (new Transaction())->tags();

    expect(class_uses_recursive(Transaction::class))->toContain(HasTags::class)
        ->and($relation->getRelated())->toBeInstanceOf(TransactionTestTag::class)
        ->and($relation->getTable())->toBe('taggables')
        ->and($relation->getForeignPivotKeyName())->toBe('taggable_id')
        ->and($relation->getRelatedPivotKeyName())->toBe('tag_id')
        ->and($relation->toBase()->wheres)->toContainEqual([
            'type'     => 'Basic',
            'column'   => 'tags.type',
            'operator' => '=',
            'value'    => Transaction::TAG_TYPE,
            'boolean'  => 'and',
        ]);
});
