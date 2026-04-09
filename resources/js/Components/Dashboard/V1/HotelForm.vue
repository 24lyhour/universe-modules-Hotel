<script setup lang="ts">
import { type InertiaForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { ImageUpload, GeofenceMap } from '@/components/shared';
import type { HotelFormData, HotelCategory, Province, StatusOption } from '../../../types';
import { computed } from 'vue';
import TiptapEditor from '@/components/TiptapEditor.vue';

interface Props {
    mode?: 'create' | 'edit';
    categories?: HotelCategory[];
    provinces?: Province[];
    statuses?: StatusOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    categories: () => [],
    provinces: () => [],
    statuses: () => [],
});

const model = defineModel<InertiaForm<HotelFormData>>({ required: true });

const categoryIdString = computed({
    get: () => model.value.hotel_category_id?.toString() ?? '',
    set: (val: string) => {
        model.value.hotel_category_id = val ? Number(val) : null;
    },
});

const provinceIdString = computed({
    get: () => model.value.province_id?.toString() ?? '',
    set: (val: string) => {
        model.value.province_id = val ? Number(val) : null;
    },
});

const starRatingString = computed({
    get: () => model.value.star_rating?.toString() ?? '3',
    set: (val: string) => {
        model.value.star_rating = Number(val);
    },
});

const logoImages = computed({
    get: () => model.value.logo_url ? [model.value.logo_url] : [],
    set: (val: string[]) => {
        model.value.logo_url = val.length > 0 ? val[0] : '';
    },
});

const featuredImages = computed({
    get: () => model.value.featured_image ? [model.value.featured_image] : [],
    set: (val: string[]) => {
        model.value.featured_image = val.length > 0 ? val[0] : '';
    },
});

const isActive = computed({
    get: () => model.value.is_featured,
    set: (value: boolean) => {
        model.value.is_featured = value;
    },
});

const mapLatitude = computed(() => model.value.latitude != null ? Number(model.value.latitude) : null);
const mapLongitude = computed(() => model.value.longitude != null ? Number(model.value.longitude) : null);
</script>

<template>
    <div class="space-y-6">
        <!-- Basic Info -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Basic Information</h3>
                <p class="text-sm text-muted-foreground">
                    {{ mode === 'create' ? 'Enter the hotel details' : 'Update the hotel details' }}
                </p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2 sm:col-span-2">
                    <Label for="name">Hotel Name <span class="text-destructive">*</span></Label>
                    <Input id="name" v-model="model.name" placeholder="Enter hotel name" />
                    <p v-if="model.errors.name" class="text-sm text-destructive">{{ model.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="category">Category</Label>
                    <Select v-model="categoryIdString">
                        <SelectTrigger>
                            <SelectValue placeholder="Select category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id.toString()">
                                {{ cat.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="status">Status <span class="text-destructive">*</span></Label>
                    <Select v-model="model.status">
                        <SelectTrigger>
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="s in statuses" :key="s.value" :value="s.value">
                                {{ s.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="description">Description</Label>
                    <TiptapEditor v-model="model.description" placeholder="Hotel description" min-height="200px" max-height="400px" />
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Images</h3>
                <p class="text-sm text-muted-foreground">Hotel logo, featured image, and gallery</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <ImageUpload v-model="logoImages" label="Logo" :multiple="false" :max-files="1" />
                    <p v-if="model.errors.logo_url" class="text-sm text-destructive">{{ model.errors.logo_url }}</p>
                </div>

                <div class="space-y-2">
                    <ImageUpload v-model="featuredImages" label="Featured Image" :multiple="false" :max-files="1" />
                    <p v-if="model.errors.featured_image" class="text-sm text-destructive">{{ model.errors.featured_image }}</p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <ImageUpload v-model="model.gallery" label="Gallery" :multiple="true" :max-files="10" />
                    <p v-if="model.errors.gallery" class="text-sm text-destructive">{{ model.errors.gallery }}</p>
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Location</h3>
                <p class="text-sm text-muted-foreground">Hotel address and map location</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2 sm:col-span-2">
                    <Label for="address">Address <span class="text-destructive">*</span></Label>
                    <Input id="address" v-model="model.address" placeholder="Full address" />
                    <p v-if="model.errors.address" class="text-sm text-destructive">{{ model.errors.address }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="city">City <span class="text-destructive">*</span></Label>
                    <Input id="city" v-model="model.city" placeholder="City" />
                    <p v-if="model.errors.city" class="text-sm text-destructive">{{ model.errors.city }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="province_id">Province</Label>
                    <Select v-model="provinceIdString">
                        <SelectTrigger>
                            <SelectValue placeholder="Select province" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="p in provinces" :key="p.id" :value="p.id.toString()">
                                {{ p.name }} ({{ p.name_kh }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="country">Country <span class="text-destructive">*</span></Label>
                    <Input id="country" v-model="model.country" placeholder="Country" />
                    <p v-if="model.errors.country" class="text-sm text-destructive">{{ model.errors.country }}</p>
                </div>
            </div>

            <!-- Map -->
            <div class="space-y-2">
                <Label>Pin Location on Map</Label>
                <GeofenceMap
                    :latitude="mapLatitude"
                    :longitude="mapLongitude"
                    mode="location"
                    height="400px"
                    @update:latitude="(val) => model.latitude = val"
                    @update:longitude="(val) => model.longitude = val"
                />
            </div>

            <div v-if="model.latitude && model.longitude" class="flex items-center gap-4 p-3 rounded-lg bg-muted/50">
                <div class="flex-1 grid grid-cols-2 gap-4">
                    <div>
                        <Label class="text-xs text-muted-foreground">Latitude</Label>
                        <p class="font-mono text-sm">{{ Number(model.latitude).toFixed(6) }}</p>
                    </div>
                    <div>
                        <Label class="text-xs text-muted-foreground">Longitude</Label>
                        <p class="font-mono text-sm">{{ Number(model.longitude).toFixed(6) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Contact</h3>
                <p class="text-sm text-muted-foreground">Hotel contact information</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-2">
                    <Label for="phone">Phone <span class="text-destructive">*</span></Label>
                    <Input id="phone" v-model="model.phone" type="tel" placeholder="+855 ..." />
                    <p v-if="model.errors.phone" class="text-sm text-destructive">{{ model.errors.phone }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="model.email" type="email" placeholder="hotel@example.com" />
                    <p v-if="model.errors.email" class="text-sm text-destructive">{{ model.errors.email }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="website">Website</Label>
                    <Input id="website" v-model="model.website" placeholder="https://..." />
                    <p v-if="model.errors.website" class="text-sm text-destructive">{{ model.errors.website }}</p>
                </div>
            </div>
        </div>

        <!-- Rating & Currency -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Rating & Currency</h3>
                <p class="text-sm text-muted-foreground">Star rating and currency (prices are set per room)</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-2">
                    <Label for="star_rating">Star Rating <span class="text-destructive">*</span></Label>
                    <Select v-model="starRatingString">
                        <SelectTrigger>
                            <SelectValue placeholder="Stars" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="n in 5" :key="n" :value="n.toString()">
                                {{ n }} {{ n === 1 ? 'Star' : 'Stars' }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="currency">Currency</Label>
                    <Select v-model="model.currency">
                        <SelectTrigger>
                            <SelectValue placeholder="Currency" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="USD">USD</SelectItem>
                            <SelectItem value="KHR">KHR</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="price_level">Price Level</Label>
                    <Select v-model="model.price_level">
                        <SelectTrigger>
                            <SelectValue placeholder="Select level" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="budget">Budget</SelectItem>
                            <SelectItem value="mid-range">Mid-Range</SelectItem>
                            <SelectItem value="luxury">Luxury</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>

        <!-- Property Details -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Property Details</h3>
                <p class="text-sm text-muted-foreground">Rooms and floors</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="total_rooms">Total Rooms</Label>
                    <Input id="total_rooms" v-model.number="model.total_rooms" type="number" min="0" placeholder="0" />
                </div>
                <div class="space-y-2">
                    <Label for="total_floors">Total Floors</Label>
                    <Input id="total_floors" v-model.number="model.total_floors" type="number" min="0" placeholder="0" />
                </div>
            </div>
        </div>

        <!-- Featured -->
        <div class="space-y-2">
            <Label for="is_featured">Featured Hotel</Label>
            <div class="flex items-center space-x-2 pt-1">
                <Switch id="is_featured" :checked="isActive" @update:checked="isActive = $event" />
                <Label for="is_featured" class="font-normal">
                    {{ isActive ? 'Featured' : 'Not Featured' }}
                </Label>
            </div>
        </div>
    </div>
</template>
