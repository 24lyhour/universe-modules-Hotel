<?php

namespace Modules\Hotel\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Hotel\Database\Factories\RoomPoliciesFactory;

class RoomPolicies extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'hotel_room_policies';

    protected $fillable = [
        'uuid',
        'title',
        'icon',
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

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function newFactory(): RoomPoliciesFactory
    {
        return RoomPoliciesFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
