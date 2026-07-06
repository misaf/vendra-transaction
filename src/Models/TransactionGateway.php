<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Misaf\VendraMultimedia\Concerns\HasDefaultMediaConversions;
use Misaf\VendraSupport\Contracts\ShouldLogActivity;
use Misaf\VendraSupport\Traits\BelongsToTenant;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Traits\HasTransaction;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int $tenant_id
 * @property array<string, string> $name
 * @property array<string, string> $description
 * @property array<string, string> $slug
 * @property int $position
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'description', 'slug', 'position', 'status'])]
#[Hidden(['tenant_id'])]
#[UseFactory(TransactionGatewayFactory::class)]
final class TransactionGateway extends Model implements HasMedia, Sortable, ShouldLogActivity
{
    use BelongsToTenant;
    use HasDefaultMediaConversions, InteractsWithMedia {
        HasDefaultMediaConversions::registerMediaConversions insteadof InteractsWithMedia;
    }

    /** @use HasFactory<TransactionGatewayFactory> */
    use HasFactory;

    use HasTransaction;
    use HasTranslatableSlug;
    use HasTranslations;
    use SoftDeletes;
    use SortableTrait;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['name', 'description', 'slug'];

    /**
     * @var array<string, string>
     */
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'tenant_id'   => 'integer',
            'name'        => 'array',
            'description' => 'array',
            'slug'        => 'array',
            'position'    => 'integer',
            'status'      => 'boolean',
        ];
    }

    /**
     * @var list<string>
     */

    /**
     * @var list<string>
     */

    /**
     * @return MorphMany<Media, $this>
     */
    public function multimedia(): MorphMany
    {
        return $this->media();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }
}
