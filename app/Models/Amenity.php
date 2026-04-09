<?php

namespace Modules\Hotel\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Amenity extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'hotel_amenities';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'icon',
        'group',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Amenity $amenity) {
            if (empty($amenity->uuid)) {
                $amenity->uuid = (string) Str::uuid();
            }
            if (empty($amenity->slug)) {
                $amenity->slug = Str::slug($amenity->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_amenity_hotel', 'amenity_id', 'hotel_id')
            ->withTimestamps();
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
