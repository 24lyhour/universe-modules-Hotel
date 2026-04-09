<?php

namespace Modules\Hotel\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'bathroom_count',
        'room_size',
        'view',
        'amenities',
        'images',
        'is_available',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'images' => 'array',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'capacity' => 'integer',
            'bed_count' => 'integer',
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

        static::created(function (Room $room) {
            $room->hotel?->syncPricesFromRooms();
        });

        static::updated(function (Room $room) {
            if ($room->isDirty(['price', 'discount_price'])) {
                $room->hotel?->syncPricesFromRooms();
            }
        });

        static::deleted(function (Room $room) {
            $room->hotel?->syncPricesFromRooms();
        });

        static::restored(function (Room $room) {
            $room->hotel?->syncPricesFromRooms();
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

    // Scopes

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
