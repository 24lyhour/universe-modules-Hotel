<script setup lang="ts">
import { computed } from 'vue';
import { type InertiaForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { ImageUpload } from '@/components/shared';
import type { RoomFormData, StatusOption } from '../../../types';
import TiptapEditor from '@/components/TiptapEditor.vue';

interface Props {
    mode?: 'create' | 'edit';
    statuses?: StatusOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    statuses: () => [],
});

const model = defineModel<InertiaForm<RoomFormData>>({ required: true });

const isAvailable = computed({
    get: () => model.value.is_available,
    set: (value: boolean) => {
        model.value.is_available = value;
    },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Room Information -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Room Information</h3>
                <p class="text-sm text-muted-foreground">
                    {{ mode === 'create' ? 'Enter room details' : 'Update room details' }}
                </p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="name">Room Name <span class="text-destructive">*</span></Label>
                    <Input id="name" v-model="model.name" placeholder="e.g. Deluxe King" />
                    <p v-if="model.errors.name" class="text-sm text-destructive">{{ model.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="room_type">Room Type</Label>
                    <Select v-model="model.room_type">
                        <SelectTrigger>
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="standard">Standard</SelectItem>
                            <SelectItem value="superior">Superior</SelectItem>
                            <SelectItem value="deluxe">Deluxe</SelectItem>
                            <SelectItem value="suite">Suite</SelectItem>
                            <SelectItem value="family">Family</SelectItem>
                            <SelectItem value="villa">Villa</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="room_number">Room Number</Label>
                    <Input id="room_number" v-model="model.room_number" placeholder="e.g. 101" />
                </div>

                <div class="space-y-2">
                    <Label for="total_room">Total Rooms</Label>
                    <Input id="total_room" v-model.number="model.total_room" type="number" min="1" placeholder="1" />
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

                <div class="space-y-2">
                    <Label for="sort_order">Sort Order</Label>
                    <Input id="sort_order" v-model.number="model.sort_order" type="number" min="0" placeholder="0" />
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="description">Description</Label>
                    <TiptapEditor v-model="model.description" placeholder="Room description" min-height="150px" max-height="300px" />
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Images</h3>
                <p class="text-sm text-muted-foreground">Room photos</p>
            </div>
            <Separator />
            <ImageUpload v-model="model.images" label="Room Images" :multiple="true" :max-files="10" />
            <p v-if="model.errors.images" class="text-sm text-destructive">{{ model.errors.images }}</p>
        </div>

        <!-- Pricing -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Pricing</h3>
                <p class="text-sm text-muted-foreground">Room price and discount</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="price">Price <span class="text-destructive">*</span></Label>
                    <Input id="price" :model-value="model.price ?? undefined" @update:model-value="(v: any) => (model.price = v !== '' ? Number(v) : null)" type="number" step="0.01" min="0" placeholder="0.00" />
                    <p v-if="model.errors.price" class="text-sm text-destructive">{{ model.errors.price }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="discount_price">Discount Price</Label>
                    <Input id="discount_price" :model-value="model.discount_price ?? undefined" @update:model-value="(v: any) => (model.discount_price = v !== '' ? Number(v) : null)" type="number" step="0.01" min="0" placeholder="0.00" />
                </div>
            </div>
        </div>

        <!-- Room Details -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Room Details</h3>
                <p class="text-sm text-muted-foreground">Capacity, bed, and size information</p>
            </div>
            <Separator />
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-2">
                    <Label for="capacity">Capacity</Label>
                    <Input id="capacity" v-model.number="model.capacity" type="number" min="1" placeholder="2" />
                </div>

                <div class="space-y-2">
                    <Label for="bed_type">Bed Type</Label>
                    <Select v-model="model.bed_type">
                        <SelectTrigger>
                            <SelectValue placeholder="Select bed" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="single">Single</SelectItem>
                            <SelectItem value="double">Double</SelectItem>
                            <SelectItem value="queen">Queen</SelectItem>
                            <SelectItem value="king">King</SelectItem>
                            <SelectItem value="twin">Twin</SelectItem>
                            <SelectItem value="bunk">Bunk</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label for="bed_count">Bed Count</Label>
                    <Input id="bed_count" v-model.number="model.bed_count" type="number" min="1" placeholder="1" />
                </div>

                <div class="space-y-2">
                    <Label for="bathroom_count">Bathrooms</Label>
                    <Input id="bathroom_count" v-model.number="model.bathroom_count" type="number" min="0" placeholder="1" />
                </div>

                <div class="space-y-2">
                    <Label for="room_size">Room Size</Label>
                    <Input id="room_size" v-model="model.room_size" placeholder="e.g. 35 sqm" />
                </div>

                <div class="space-y-2">
                    <Label for="view">View</Label>
                    <Input id="view" v-model="model.view" placeholder="e.g. Sea View" />
                </div>
            </div>
        </div>

        <!-- Availability -->
        <div class="space-y-2">
            <Label for="is_available">Availability</Label>
            <div class="flex items-center space-x-2 pt-1">
                <Switch id="is_available" :checked="isAvailable" @update:checked="isAvailable = $event" />
                <Label for="is_available" class="font-normal">
                    {{ isAvailable ? 'Available for booking' : 'Not available' }}
                </Label>
            </div>
        </div>
    </div>
</template>
