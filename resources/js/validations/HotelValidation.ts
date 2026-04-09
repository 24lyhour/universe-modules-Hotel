import { z } from 'zod';

export const hotelSchema = z.object({
    name: z.string().min(1, 'Hotel name is required').max(255, 'Name must be less than 255 characters'),
    description: z.string().nullable().optional(),
    address: z.string().min(1, 'Address is required').max(500, 'Address must be less than 500 characters'),
    city: z.string().min(1, 'City is required').max(100, 'City must be less than 100 characters'),
    country: z.string().min(1, 'Country is required').max(100, 'Country must be less than 100 characters'),
    province_id: z.union([z.string(), z.number()]).refine(val => val !== '' && val !== null && val !== undefined, {
        message: 'Please select a province',
    }),
    provinces: z.string().nullable().optional(),
    phone: z.string().min(1, 'Phone number is required').max(20, 'Phone must be less than 20 characters'),
    email: z.string().email('Please enter a valid email').max(255).nullable().optional().or(z.literal('')),
    website: z.string().url('Please enter a valid URL').max(255).nullable().optional().or(z.literal('')),
    star_rating: z.coerce.number().min(1, 'Star rating must be at least 1').max(5, 'Star rating cannot exceed 5'),
    price_level: z.string().min(1, 'Please select a price level'),
    min_price: z.coerce.number().min(0, 'Min price cannot be negative').nullable().optional(),
    max_price: z.coerce.number().min(0, 'Max price cannot be negative').nullable().optional(),
    currency: z.string().max(10).nullable().optional(),
    hotel_category_id: z.union([z.string(), z.number()]).refine(val => val !== '' && val !== null && val !== undefined, {
        message: 'Please select a category',
    }),
    logo_url: z.string().max(255).nullable().optional().or(z.literal('')),
    featured_image: z.string().max(255).nullable().optional().or(z.literal('')),
    total_rooms: z.coerce.number().min(0, 'Total rooms cannot be negative').nullable().optional(),
    total_floors: z.coerce.number().min(0, 'Total floors cannot be negative').nullable().optional(),
    gallery: z.array(z.string()).nullable().optional(),
    amenities: z.array(z.string()).nullable().optional(),
    latitude: z.coerce.number().min(-90).max(90).nullable().optional(),
    longitude: z.coerce.number().min(-180).max(180).nullable().optional(),
    status: z.string().min(1, 'Please select a status'),
    is_featured: z.boolean(),
});

export type HotelSchemaType = z.infer<typeof hotelSchema>;
