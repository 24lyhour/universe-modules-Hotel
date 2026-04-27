<?php

namespace Modules\Hotel\Http\Middleware;

use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;

class DashboardMiddlewareHandle
{
    protected static bool $registered = false;

    public function handle(Request $request, Closure $next)
    {
        if ($request->is('dashboard', 'dashboard/*')) {
            $this->registerMenuItems();
        }

        return $next($request);
    }

    protected function registerMenuItems(): void
    {
        if (static::$registered) {
            return;
        }

        MenuService::addMenuItem(
            menu: 'primary',
            id: 'hotel',
            title: __('Hotel'),
            url: '/dashboard/hotels',
            icon: 'Hotel',
            order: 100,
            route: 'hotel.*'
        );

        MenuService::addSubmenuItem('primary', 'hotel', __('Hotels'), '/dashboard/hotels', 10, null, 'hotel.hotels.*', 'Hotel');
        MenuService::addSubmenuItem('primary', 'hotel', __('Categories'), '/dashboard/hotel-categories', 15, null, 'hotel.categories.*', 'LayoutGrid');
        MenuService::addSubmenuItem('primary', 'hotel', __('Amenities'), '/dashboard/hotel-amenities', 20, null, 'hotel.amenities.*', 'Sparkles');
        MenuService::addSubmenuItem('primary', 'hotel', __('Rooms'), '/dashboard/hotel-rooms', 25, null, 'hotel.rooms.*', 'BedDouble');
        MenuService::addSubmenuItem('primary', 'hotel', __('Reviews'), '/dashboard/hotel-reviews', 30, null, 'hotel.reviews.*', 'Star');
        MenuService::addSubmenuItem('primary', 'hotel', __('Provinces'), '/dashboard/hotel-provinces', 35, null, 'hotel.provinces.*', 'MapPin');

        static::$registered = true;
    }
}
