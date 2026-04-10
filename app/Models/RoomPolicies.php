<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Hotel\Database\Factories\RoomPoliciesFactory;

class RoomPolicies extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'title',
        'icon',
        'description',
        'is_active'
    ];

    /**
     * protection  
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * protecton for facotory
     */
    protected static function newFactory(): RoomPoliciesFactory
    {
        return RoomPoliciesFactory::new();
    }

    /**
     * hasMany room
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_id');
    }

    /**
     * scope status
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
