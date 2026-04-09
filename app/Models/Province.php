<?php

namespace Modules\Hotel\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Province extends Model
{
    use HasUuid;

    protected $table = 'provinces';

    protected $fillable = [
        'uuid',
        'name',
        'name_kh',
        'code',
        'region',
        'latitude',
        'longitude',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Province $province) {
            if (empty($province->uuid)) {
                $province->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
