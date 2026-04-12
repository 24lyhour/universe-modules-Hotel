# 🏨 Hotel Booking API Documentation

## Base URL
```
http://your-domain.com/api/v1
```

---

## 📋 Table of Contents
1. [Hotel Endpoints](#hotel-endpoints)
2. [Review Endpoints](#review-endpoints)
3. [Reference Data](#reference-data)
4. [Search & Filters](#search--filters)
5. [Error Handling](#error-handling)

---

## 🏨 Hotel Endpoints

### 1. Get All Hotels with Filters
```
GET /hotels
```

**Query Parameters:**
- `search` (string) - Search by name, city, or address
- `city` (string) - Filter by city
- `province_id` (integer) - Filter by province
- `category` (string) - Filter by category slug
- `star_rating` (integer) - Filter by star rating (1-5)
- `min_price` (number) - Minimum price filter
- `max_price` (number) - Maximum price filter
- `is_featured` (boolean) - Show featured hotels only
- `amenities` (string) - Comma-separated amenity IDs
- `per_page` (integer, default: 15) - Items per page

**Example Request:**
```bash
GET /hotels?search=Phnom&province_id=1&min_price=50&max_price=200&per_page=10
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "uuid-here",
      "name": "Rosewood Phnom Penh",
      "slug": "rosewood-phnom-penh",
      "description": "Luxury 5-star hotel...",
      "address": "Vattanac Capital Tower",
      "city": "Phnom Penh",
      "country": "Cambodia",
      "province": {
        "id": 1,
        "uuid": "uuid",
        "name": "Phnom Penh",
        "name_kh": "ភ្នំពេញ",
        "code": "PP"
      },
      "phone": "+855 23 999 888",
      "email": "contact@rosewood.com",
      "website": "https://rosewood.com",
      "star_rating": 5,
      "price_level": "luxury",
      "min_price": 150,
      "max_price": 450,
      "discount_price": 180,
      "discount_percentage": 20,
      "currency": "USD",
      "logo_url": "https://...",
      "featured_image": "https://...",
      "total_rooms": 156,
      "total_floors": 18,
      "gallery": ["https://...", "https://..."],
      "amenities": ["WiFi", "Pool", "Spa", "Gym"],
      "latitude": 11.5564,
      "longitude": 104.9282,
      "status": "active",
      "is_featured": true,
      "category": {
        "id": 1,
        "uuid": "uuid",
        "name": "Luxury Hotels",
        "icon": "star"
      },
      "rooms_count": 156,
      "created_at": "2024-04-01T12:00:00Z",
      "updated_at": "2024-04-10T08:39:00Z"
    }
  ],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "path": "...",
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

---

### 2. Get Featured Hotels
```
GET /hotels/featured?limit=10
```

**Query Parameters:**
- `limit` (integer, default: 10) - Number of featured hotels to return

**Response:** Array of hotel objects (same structure as above)

---

### 3. Get Single Hotel with Full Details
```
GET /hotels/{hotel_id}
```

**Response:** Single hotel object with reviews and rooms loaded

---

### 4. Get Hotel Rooms
```
GET /hotels/{hotel_id}/rooms?per_page=15
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "uuid",
      "name": "Premier Suite",
      "room_type": "suite",
      "description": "Luxurious suite with city views",
      "price": 180.00,
      "discount_price": 150.00,
      "capacity": 2,
      "bed_type": "King Bed",
      "bed_count": 1,
      "room_available_count": 5,
      "bathroom_count": 1,
      "room_size": "35 m²",
      "view": "City View",
      "amenities": ["WiFi", "AC", "TV"],
      "images": ["https://...", "https://..."],
      "is_available": true,
      "created_at": "2024-04-01T12:00:00Z"
    }
  ],
  "meta": { ... }
}
```

---

### 5. Booking Home (All Home Data in One Call)
```
GET /hotels/booking-home
```

**Response:**
```json
{
  "data": {
    "popular_destinations": [
      {
        "id": 1,
        "name": "Phnom Penh",
        "name_kh": "ភ្នំពេញ",
        "code": "PP",
        "region": "Central",
        "latitude": 11.5564,
        "longitude": 104.9282,
        "hotels_count": 245
      }
    ],
    "featured_hotels": [ ... ],
    "recommended_hotels": [ ... ],
    "deals": [ ... ],
    "categories": [
      {
        "id": 1,
        "uuid": "uuid",
        "name": "Luxury Hotels",
        "slug": "luxury-hotels",
        "icon": "star",
        "hotels_count": 45
      }
    ]
  }
}
```

---

### 6. Search Hotels
```
GET /hotels/search?q=phnom&min_price=50&max_price=200&sort=price_low
```

**Query Parameters:**
- `q` (string) - Search keyword
- `province_id` (integer) - Filter by province
- `category_id` (integer) - Filter by category
- `min_price` (number) - Minimum price
- `max_price` (number) - Maximum price
- `amenities` (string) - Comma-separated amenity IDs
- `sort` (string) - Sort option: `recommended`, `price_low`, `price_high`, `rating`
- `per_page` (integer, default: 15)

**Response:** Paginated hotel collection

---

### 7. Get Cities with Counts
```
GET /hotels/cities
```

**Response:**
```json
{
  "data": [
    {
      "city": "Phnom Penh",
      "hotels_count": 245,
      "min_price": 20,
      "max_price": 450
    },
    {
      "city": "Siem Reap",
      "hotels_count": 180,
      "min_price": 15,
      "max_price": 350
    }
  ]
}
```

---

## ⭐ Review Endpoints

### 1. Get Hotel Reviews
```
GET /hotels/{hotel_id}/reviews?per_page=10
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "uuid",
      "rating": 5,
      "comment": "Excellent hotel, great service!",
      "status": "approved",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "created_at": "2024-04-05T10:30:00Z"
    }
  ],
  "meta": { ... }
}
```

---

### 2. Get Review Statistics
```
GET /hotels/{hotel_id}/reviews/stats
```

**Response:**
```json
{
  "data": {
    "total_reviews": 524,
    "average_rating": 4.8,
    "ratings_breakdown": {
      "5": 450,
      "4": 65,
      "3": 7,
      "2": 2,
      "1": 0
    }
  }
}
```

---

### 3. Submit a Review (Authenticated)
```
POST /hotels/{hotel_id}/reviews
Content-Type: application/json
Authorization: Bearer {token}

{
  "rating": 5,
  "comment": "Amazing hotel with great views!"
}
```

**Response:**
```json
{
  "message": "Review submitted successfully",
  "data": {
    "id": 2,
    "uuid": "uuid",
    "rating": 5,
    "comment": "Amazing hotel with great views!",
    "status": "pending",
    "user": {
      "id": 1,
      "name": "John Doe"
    },
    "created_at": "2024-04-10T08:39:20Z"
  }
}
```

---

## 📚 Reference Data

### 1. Get All Provinces
```
GET /provinces
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "uuid",
      "name": "Phnom Penh",
      "name_kh": "ភ្នំពេញ",
      "code": "PP",
      "region": "Central",
      "latitude": 11.5564,
      "longitude": 104.9282,
      "hotels_count": 245
    }
  ]
}
```

---

### 2. Get Single Province with Hotels
```
GET /provinces/{province_id}
```

**Response:** Province object with loaded hotels array

---

### 3. Get All Amenities (Grouped)
```
GET /amenities
```

**Response:**
```json
{
  "data": [
    {
      "group": "Facilities",
      "amenities": [
        {
          "id": 1,
          "uuid": "uuid",
          "name": "WiFi",
          "slug": "wifi",
          "icon": "wifi",
          "group": "Facilities"
        }
      ]
    },
    {
      "group": "Recreation",
      "amenities": [
        {
          "id": 5,
          "uuid": "uuid",
          "name": "Pool",
          "slug": "pool",
          "icon": "pool",
          "group": "Recreation"
        }
      ]
    }
  ]
}
```

---

### 4. Get Hotel Categories
```
GET /hotels/categories
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "uuid",
      "name": "Luxury Hotels",
      "slug": "luxury-hotels",
      "icon": "star",
      "hotels_count": 45
    }
  ]
}
```

---

## 🔍 Search & Filters

### Common Filter Combinations

**1. Budget Hotels in Siem Reap**
```
GET /hotels?city=Siem Reap&max_price=100&sort=price_low
```

**2. Luxury Hotels with Pool & Spa**
```
GET /hotels?star_rating=5&amenities=1,2&sort=rating
```

**3. Hotels in Province with Amenities**
```
GET /hotels?province_id=1&amenities=wifi,pool,gym&sort=price_low
```

**4. Search & Filter Combined**
```
GET /hotels/search?q=beach&min_price=75&max_price=250&sort=rating
```

---

## ❌ Error Handling

### Common Errors

**404 - Not Found**
```json
{
  "message": "No query results found for model [Modules\\Hotel\\Models\\Hotel]",
  "exception": "ModelNotFoundException"
}
```

**422 - Validation Error**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "rating": ["The rating must be between 1 and 5."],
    "comment": ["The comment must be at least 10 characters."]
  }
}
```

**401 - Unauthorized (for review submission)**
```json
{
  "message": "Unauthenticated."
}
```

---

## 🚀 Flutter Integration Examples

### Using the APIs in Flutter (from your booking module):

```dart
// Fetch booking home data
Future<void> fetchBookingHome() async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/hotels/booking-home'),
  );
  // Parse response...
}

// Search with filters
Future<void> searchHotels({
  String? query,
  String? provinceId,
  String? minPrice,
  String? maxPrice,
}) async {
  final params = {
    if (query != null) 'q': query,
    if (provinceId != null) 'province_id': provinceId,
    if (minPrice != null) 'min_price': minPrice,
    if (maxPrice != null) 'max_price': maxPrice,
  };
  
  final uri = Uri.parse('$baseUrl/api/v1/hotels/search').replace(
    queryParameters: params,
  );
  
  final response = await http.get(uri);
  // Parse response...
}

// Get hotel details
Future<void> getHotelDetail(int hotelId) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/hotels/$hotelId'),
  );
  // Parse response...
}

// Get reviews with stats
Future<void> getHotelReviews(int hotelId) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/hotels/$hotelId/reviews/stats'),
  );
  // Parse response...
}
```

---

## 📝 Notes

- All endpoints return JSON responses
- Pagination uses standard Laravel pagination format
- Timestamps are in ISO 8601 format (UTC)
- Prices are in USD (or specified currency field)
- Amenities and gallery are JSON arrays
- Reviews are moderated (status: pending/approved/rejected)
- Authentication uses Laravel Sanctum token-based auth

---

## 🔐 Authentication

**For endpoints requiring authentication:**
- Include `Authorization: Bearer {token}` header
- Tokens are obtained via login endpoint
- Currently only review submission requires auth

---

**Last Updated:** April 10, 2024
