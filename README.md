# Hotel Module

A Laravel module for managing hotels with repository pattern architecture.

## Structure

```
Hotel/
├── app/
│   ├── Contracts/
│   │   └── HotelRepositoryInterface.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── HotelController.php
│   │   └── Requests/
│   │       ├── StoreHotelRequest.php
│   │       └── UpdateHotelRequest.php
│   ├── Models/
│   │   └── Hotel.php
│   ├── Providers/
│   │   └── HotelServiceProvider.php
│   ├── Repositories/
│   │   └── HotelRepository.php
│   └── Services/
│       └── HotelService.php
├── config/
├── database/
│   └── migrations/
├── resources/
├── routes/
└── tests/
```

## Installation

1. Clone this repository into `Modules/Hotel` directory
2. Register the module in `config/modules.php`
3. Run migrations: `php artisan migrate`

## Architecture

This module follows the **Repository Pattern**:

- **Controller** → Handles HTTP requests
- **Service** → Contains business logic
- **Repository** → Data access layer
- **Model** → Eloquent ORM

## API

### HotelService Methods

- `getAllHotels()` - Get all hotels
- `getPaginatedHotels($perPage)` - Get paginated hotels
- `getHotelById($id)` - Get hotel by ID
- `getHotelBySlug($slug)` - Get hotel by slug
- `createHotel($data)` - Create a new hotel
- `updateHotel($id, $data)` - Update a hotel
- `deleteHotel($id)` - Delete a hotel
- `getActiveHotels($perPage)` - Get active hotels
- `getHotelsByCity($city, $perPage)` - Get hotels by city
- `getHotelsByStarRating($rating, $perPage)` - Get hotels by rating
- `searchHotels($query, $perPage)` - Search hotels
