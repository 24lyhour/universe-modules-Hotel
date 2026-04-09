<?php

namespace Modules\Hotel\Enums;

enum HotelStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Draft = 'draft';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Draft => 'Draft',
        };
    }

    public function labelKh(): string
    {
        return match ($this) {
            self::Active => 'សកម្ម',
            self::Inactive => 'អសកម្ម',
            self::Draft => 'សេចក្ដីព្រាង',
        };
    }

    public function variant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Draft => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Active => 'check-circle',
            self::Inactive => 'x-circle',
            self::Draft => 'pencil',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Inactive => 'red',
            self::Draft => 'yellow',
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
