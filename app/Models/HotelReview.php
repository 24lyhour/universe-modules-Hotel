<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class HotelReview extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'hotel_reviews';

    protected $fillable = [
        'uuid',
        'hotel_id',
        'customer_id',
        'guest_name',
        'guest_email',
        'rating',
        'comment',
        'reply',
        'replied_at',
        'images',
        'is_recommend',
        'is_verified',
        'is_active',
        'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_recommend' => 'boolean',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'rating' => 'integer',
            'helpful_count' => 'integer',
            'replied_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (HotelReview $review) {
            if (empty($review->uuid)) {
                $review->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Models\Customer::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    // Helpers

    public function hasReply(): bool
    {
        return !empty($this->reply);
    }

    public function addReply(string $reply): void
    {
        $this->update([
            'reply' => $reply,
            'replied_at' => now(),
        ]);
    }
}
