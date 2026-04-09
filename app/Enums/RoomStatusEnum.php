<?php

namespace Modules\Hotel\Enums;

enum RoomStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Maintenance => 'Maintenance',
        };
    }

    public function labelKh(): string
    {
        return match ($this) {
            self::Active => 'សកម្ម',
            self::Inactive => 'អសកម្ម',
            self::Maintenance => 'កំពុងថែទាំ',
        };
    }

    public function variant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Maintenance => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Active => 'check-circle',
            self::Inactive => 'x-circle',
            self::Maintenance => 'wrench',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Inactive => 'red',
            self::Maintenance => 'yellow',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $status) => [
            'value' => $status->value,
            'label' => $status->label(),
        ], self::cases());
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
