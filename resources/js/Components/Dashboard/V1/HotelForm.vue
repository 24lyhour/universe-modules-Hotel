<script setup lang="ts">
import { type InertiaForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { Separator } from '@/components/ui/separator';
import { ImageUpload } from '@/components/shared';
import type { HotelFormData, HotelCategory, Province, StatusOption } from '../../../types';
import { computed } from 'vue';

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
                    <Textarea id="description" v-model="model.description" placeholder="Hotel description" rows="3" />
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
                <p class="text-sm text-muted-foreground">Hotel address and coordinates</p>
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

                <div class="space-y-2">
                    <Label for="latitude">Latitude</Label>
                    <Input id="latitude" :model-value="model.latitude ?? undefined" @update:model-value="(v: any) => (model.latitude = v ? Number(v) : null)" type="number" step="0.0000001" placeholder="e.g. 11.5564" />
                </div>

                <div class="space-y-2">
                    <Label for="longitude">Longitude</Label>
                    <Input id="longitude" :model-value="model.longitude ?? undefined" @update:model-value="(v: any) => (model.longitude = v ? Number(v) : null)" type="number" step="0.0000001" placeholder="e.g. 104.9282" />
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
                    <Label for="phone">Phone</Label>
                    <Input id="phone" v-model="model.phone" placeholder="+855 ..." />
                </div>
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="model.email" type="email" placeholder="hotel@example.com" />
                </div>
                <div class="space-y-2">
                    <Label for="website">Website</Label>
                    <Input id="website" v-model="model.website" placeholder="https://..." />
                </div>
            </div>
        </div>

        <!-- Pricing & Rating -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Pricing & Rating</h3>
                <p class="text-sm text-muted-foreground">Set hotel pricing and star rating</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-4">
                <div class="space-y-2">
                    <Label for="star_rating">Star Rating <span class="text-destructive">*</span></Label>
                    <Select :model-value="model.star_rating?.toString()" @update:model-value="(v: string) => (model.star_rating = Number(v))">
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
                    <Label for="price_per_night">Price / Night <span class="text-destructive">*</span></Label>
                    <Input id="price_per_night" :model-value="model.price_per_night ?? undefined" @update:model-value="(v: any) => (model.price_per_night = v !== '' ? Number(v) : null)" type="number" step="0.01" min="0" placeholder="0.00" />
                    <p v-if="model.errors.price_per_night" class="text-sm text-destructive">{{ model.errors.price_per_night }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="discount_price">Discount Price</Label>
                    <Input id="discount_price" :model-value="model.discount_price ?? undefined" @update:model-value="(v: any) => (model.discount_price = v !== '' ? Number(v) : null)" type="number" step="0.01" min="0" placeholder="0.00" />
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
            </div>
        </div>

        <!-- Property Details -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Property Details</h3>
                <p class="text-sm text-muted-foreground">Rooms, floors, and property level</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-2">
                    <Label for="total_rooms">Total Rooms</Label>
                    <Input id="total_rooms" v-model.number="model.total_rooms" type="number" min="0" placeholder="0" />
                </div>
                <div class="space-y-2">
                    <Label for="total_floors">Total Floors</Label>
                    <Input id="total_floors" v-model.number="model.total_floors" type="number" min="0" placeholder="0" />
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

        <!-- Featured -->
        <div class="flex items-center gap-2">
            <Checkbox id="is_featured" :checked="model.is_featured" @update:checked="(val: boolean) => (model.is_featured = val)" />
            <Label for="is_featured" class="cursor-pointer">Featured Hotel</Label>
        </div>
    </div>
</template>
