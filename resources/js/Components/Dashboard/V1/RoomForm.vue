<script setup lang="ts">
import { type InertiaForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import type { RoomFormData, StatusOption } from '../../../types';

interface Props {
    mode?: 'create' | 'edit';
    statuses?: StatusOption[];
}

withDefaults(defineProps<Props>(), {
    mode: 'create',
    statuses: () => [],
});

const model = defineModel<InertiaForm<RoomFormData>>({ required: true });
</script>

<template>
    <div class="grid gap-6">
        <!-- Basic Info -->
        <div class="space-y-4">
            <h3 class="text-sm font-medium text-muted-foreground">Room Information</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <Label for="name">Room Name *</Label>
                    <Input id="name" v-model="model.name" placeholder="e.g. Deluxe King" :class="{ 'border-destructive': model.errors.name }" />
                    <p v-if="model.errors.name" class="mt-1 text-sm text-destructive">{{ model.errors.name }}</p>
                </div>
                <div>
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
                <div>
                    <Label for="room_number">Room Number</Label>
                    <Input id="room_number" v-model="model.room_number" placeholder="e.g. 101" />
                </div>
                <div>
                    <Label for="total_room">Total Rooms</Label>
                    <Input id="total_room" v-model.number="model.total_room" type="number" min="1" placeholder="1" />
                </div>
                <div>
                    <Label for="status">Status *</Label>
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
                <div class="sm:col-span-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" v-model="model.description" placeholder="Room description" rows="2" />
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="space-y-4">
            <h3 class="text-sm font-medium text-muted-foreground">Pricing</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <Label for="price">Price *</Label>
                    <Input id="price" :model-value="model.price ?? undefined" @update:model-value="(v: any) => (model.price = v !== '' ? Number(v) : null)" type="number" step="0.01" min="0" placeholder="0.00" :class="{ 'border-destructive': model.errors.price }" />
                    <p v-if="model.errors.price" class="mt-1 text-sm text-destructive">{{ model.errors.price }}</p>
                </div>
                <div>
                    <Label for="discount_price">Discount Price</Label>
                    <Input id="discount_price" :model-value="model.discount_price ?? undefined" @update:model-value="(v: any) => (model.discount_price = v !== '' ? Number(v) : null)" type="number" step="0.01" min="0" placeholder="0.00" />
                </div>
            </div>
        </div>

        <!-- Room Details -->
        <div class="space-y-4">
            <h3 class="text-sm font-medium text-muted-foreground">Room Details</h3>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <Label for="capacity">Capacity</Label>
                    <Input id="capacity" v-model.number="model.capacity" type="number" min="1" placeholder="2" />
                </div>
                <div>
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
                <div>
                    <Label for="bed_count">Bed Count</Label>
                    <Input id="bed_count" v-model.number="model.bed_count" type="number" min="1" placeholder="1" />
                </div>
                <div>
                    <Label for="bathroom_count">Bathrooms</Label>
                    <Input id="bathroom_count" v-model.number="model.bathroom_count" type="number" min="0" placeholder="1" />
                </div>
                <div>
                    <Label for="room_size">Room Size</Label>
                    <Input id="room_size" v-model="model.room_size" placeholder="e.g. 35 sqm" />
                </div>
                <div>
                    <Label for="view">View</Label>
                    <Input id="view" v-model="model.view" placeholder="e.g. Sea View" />
                </div>
            </div>
        </div>

        <!-- Availability -->
        <div class="flex items-center gap-2">
            <Checkbox id="is_available" :checked="model.is_available" @update:checked="(val: boolean) => (model.is_available = val)" />
            <Label for="is_available" class="cursor-pointer">Available for booking</Label>
        </div>
    </div>
</template>
