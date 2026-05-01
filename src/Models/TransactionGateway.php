<?php

declare(strict_types=1);

namespace Misaf\VendraTransaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Misaf\VendraTenant\Traits\BelongsToTenant;
use Misaf\VendraTransaction\Database\Factories\TransactionGatewayFactory;
use Misaf\VendraTransaction\Traits\HasTransaction;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
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
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
final class TransactionGateway extends Model implements HasMedia, Sortable
{
    use BelongsToTenant;
    /** @use HasFactory<TransactionGatewayFactory> */
    use HasFactory;
    use HasTransaction;
    use HasTranslatableSlug;
    use HasTranslations;
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    /**
     * @var array<int, string>
     */
    public array $translatable = ['name', 'description', 'slug'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'          => 'integer',
        'tenant_id'   => 'integer',
        'name'        => 'array',
        'description' => 'array',
        'slug'        => 'array',
        'position'    => 'integer',
        'status'      => 'boolean',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'position',
        'status',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'tenant_id',
    ];

    /**
     * @return MorphMany<Media, $this>
     */
    public function multimedia(): MorphMany
    {
        return $this->media();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb-table')
            ->width(48)
            ->format('webp');

        $this->addMediaConversion('small')
            ->width(300)
            ->format('webp');

        $this->addMediaConversion('medium')
            ->width(500)
            ->format('webp');

        $this->addMediaConversion('large')
            ->width(800)
            ->format('webp');

        $this->addMediaConversion('extra-large')
            ->width(1200)
            ->format('webp');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logExcept(['id']);
    }
}
