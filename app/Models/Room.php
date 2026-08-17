<?php

namespace Modules\Hotel\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'hotel_rooms';

    protected $fillable = [
        'uuid',
        'hotel_id',
        'name',
        'total_room',
        'room_type',
        'room_number',
        'description',
        'price',
        'discount_price',
        'capacity',
        'bed_type',
        'bed_count',
        'room_available_count',
        'bathroom_count',
        'room_size',
        'view',
        'images',
        'is_available',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'capacity' => 'integer',
            'bed_count' => 'integer',
            'room_available_count' => 'integer',
            'bathroom_count' => 'integer',
            'total_room' => 'integer',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Room $room) {
            if (empty($room->uuid)) {
                $room->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomPolicies(): HasMany
    {
        return $this->hasMany(RoomPolicies::class);
    }

    /**
     * Amenities assigned to this room (from the hotel amenity catalog).
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity_room', 'room_id', 'amenity_id')
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(\Modules\Booking\Models\Booking::class);
    }

    public function roomRates(): HasMany
    {
        return $this->hasMany(\Modules\Booking\Models\RoomRate::class);
    }

    // Scopes

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Reviews left for this room.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(RoomReview::class);
    }

}
