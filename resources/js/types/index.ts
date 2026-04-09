export interface Hotel {
    id: number;
    uuid: string;
    name: string;
    slug: string;
    description: string | null;
    address: string;
    city: string;
    country: string;
    provinces: string | null;
    phone: string | null;
    email: string | null;
    website: string | null;
    star_rating: number;
    price_level: string | null;
    price_per_night: number;
    discount_price: number | null;
    effective_price: number;
    discount_percentage: number | null;
    is_on_sale: boolean;
    currency: string;
    featured_image: string | null;
    total_rooms: number;
    total_floors: number;
    gallery: string[];
    amenities: string[];
    latitude: number | null;
    longitude: number | null;
    status: string;
    is_featured: boolean;
    category: HotelCategory | null;
    province: Province | null;
    user: { id: number; name: string } | null;
    created_by: { id: number; name: string } | null;
    rooms_count: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface HotelFormData {
    name: string;
    description: string;
    address: string;
    city: string;
    country: string;
    province_id: number | null;
    provinces: string;
    phone: string;
    email: string;
    website: string;
    star_rating: number;
    price_level: string;
    price_per_night: number | null;
    discount_price: number | null;
    currency: string;
    hotel_category_id: number | null;
    featured_image: string;
    total_rooms: number;
    total_floors: number;
    gallery: string[];
    amenities: string[];
    latitude: number | null;
    longitude: number | null;
    status: string;
    is_featured: boolean;
}

export interface Room {
    id: number;
    uuid: string;
    name: string;
    total_room: number;
    room_type: string | null;
    room_number: string | null;
    description: string | null;
    price: number;
    discount_price: number | null;
    capacity: number;
    bed_type: string | null;
    bed_count: number;
    bathroom_count: number;
    room_size: string | null;
    view: string | null;
    amenities: string[];
    images: string[];
    is_available: boolean;
    sort_order: number;
    status: string;
    hotel: { id: number; uuid: string; name: string } | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface RoomFormData {
    name: string;
    total_room: number;
    room_type: string;
    room_number: string;
    description: string;
    price: number | null;
    discount_price: number | null;
    capacity: number;
    bed_type: string;
    bed_count: number;
    bathroom_count: number;
    room_size: string;
    view: string;
    amenities: string[];
    images: string[];
    is_available: boolean;
    sort_order: number;
    status: string;
}

export interface HotelCategory {
    id: number;
    uuid: string;
    name: string;
    slug: string;
    icon: string | null;
    group: string | null;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    hotels_count: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface HotelCategoryFormData {
    name: string;
    icon: string;
    group: string;
    description: string;
    is_active: boolean;
    sort_order: number;
}

export interface Amenity {
    id: number;
    uuid: string;
    name: string;
    slug: string;
    icon: string | null;
    group: string | null;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    hotels_count: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface AmenityFormData {
    name: string;
    icon: string;
    group: string;
    description: string;
    is_active: boolean;
    sort_order: number;
}

export interface Province {
    id: number;
    uuid: string;
    name: string;
    name_kh: string | null;
    code: string;
    region: string | null;
    latitude: number | null;
    longitude: number | null;
    is_active: boolean;
    sort_order: number;
    hotels_count: number;
}

export interface StatusOption {
    value: string;
    label: string;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
    links: Record<string, string | null>;
}

export interface HotelStats {
    total: number;
    active: number;
    inactive: number;
    featured: number;
    trashed: number;
}
