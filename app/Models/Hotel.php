<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'star_rating',
        'price_per_night',
        'featured_image',
        'gallery',
        'amenities',
        'status',
    ];

    protected $casts = [
        'gallery' => 'array',
        'amenities' => 'array',
        'price_per_night' => 'decimal:2',
        'star_rating' => 'integer',
    ];

    /**
     * Get the user that owns the hotel.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active hotels.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive hotels.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to filter by city.
     */
    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope a query to filter by star rating.
     */
    public function scopeByStarRating($query, int $rating)
    {
        return $query->where('star_rating', '>=', $rating);
    }
}
