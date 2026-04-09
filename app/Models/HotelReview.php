<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Hotel\Database\Factories\HotelReviewFactory;

class HotelReview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
            'uuid',
            'hotel_id',
            'rating',
            'reply',
            'is_recommend',
            'status',

    ];

    /**
     * protection 
     */
    protected $case = [
        'is_recommend' => 'boolean',
        'rating'       =>  'integer',
        'status'       => HotelReviewEnum::class,
    ];

    protected static function newFactory(): HotelReviewFactory
    {
        return HotelReviewFactory::new();
    }

    /**
     * belongsTo 
     */
    public function Hotel() : belongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * scope status
     */
     public function scopeActive($query)
    {
        return $query->where('status', HotelStatusEnum::Active);
    }

}
