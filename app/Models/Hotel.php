<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Hotel\Enums\HotelStatusEnum;

class Hotel extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'hotels';

    protected $fillable = [
        'uuid',
        'user_id',
        'hotel_category_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'country',
        'province_id',
        'provinces',
        'phone',
        'email',
        'website',
        'star_rating',
        'price_level',
        'price_per_night',
        'discount_price',
        'currency',
        'featured_image',
        'total_rooms',
        'total_floors',
        'gallery',
        'amenities',
        'latitude',
        'longitude',
        'status',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'amenities' => 'array',
            'price_per_night' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'star_rating' => 'integer',
            'total_rooms' => 'integer',
            'total_floors' => 'integer',
            'is_featured' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'status' => HotelStatusEnum::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Hotel $hotel) {
            if (empty($hotel->uuid)) {
                $hotel->uuid = (string) Str::uuid();
            }
            if (empty($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });

        static::updating(function (Hotel $hotel) {
            if ($hotel->isDirty('name')) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(HotelCategory::class, 'hotel_category_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function hotelAmenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity_hotel', 'hotel_id', 'amenity_id')
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', HotelStatusEnum::Active);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', HotelStatusEnum::Inactive);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByStarRating($query, int $rating)
    {
        return $query->where('star_rating', '>=', $rating);
    }

    // Helpers

    public function isOnSale(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price_per_night;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->isOnSale()) {
            return null;
        }

        return (int) round((($this->price_per_night - $this->discount_price) / $this->price_per_night) * 100);
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->isOnSale() ? (float) $this->discount_price : (float) $this->price_per_night;
    }
}
