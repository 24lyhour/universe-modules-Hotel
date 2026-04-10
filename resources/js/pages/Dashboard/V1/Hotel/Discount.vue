<script setup lang="ts">
import { h, computed, ref, watch, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import { ModalForm } from '@/components/shared';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import { Button } from '@/components/ui/button';
import { DollarSign, Percent, Hotel as HotelIcon, BedDouble, CheckCheck } from 'lucide-vue-next';
import type { Hotel, Room } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Discount', href: '#' }] }, () => page),
});

const props = defineProps<{ hotel: Hotel }>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

// Normalize rooms — may come as array or { data: [] } from resource collection
const hotelRooms = computed<Room[]>(() => {
    const r = props.hotel.rooms;
    if (Array.isArray(r)) return r;
    if (r && typeof r === 'object' && 'data' in r) return (r as any).data;
    return [];
});

// Track which rooms are selected for discount
const selectedRoomUuids = ref<Set<string>>(new Set());

// Initialize selected rooms from current data
watch(hotelRooms, (newRooms) => {
    if (newRooms.length > 0 && selectedRoomUuids.value.size === 0) {
        newRooms.forEach((r: Room) => {
            if (r.discount_price !== null) {
                selectedRoomUuids.value.add(r.uuid);
            }
        });
        selectedRoomUuids.value = new Set(selectedRoomUuids.value);
    }
}, { immediate: true });

const form = useForm({
    discount_price: props.hotel.discount_price,
    discount_percentage: props.hotel.discount_percentage,
    room_discounts: [] as { uuid: string; discount_price: number | null }[],
});

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency: props.hotel.currency || 'USD' }).format(value);

const rooms = hotelRooms;

const toggleRoom = (uuid: string) => {
    if (selectedRoomUuids.value.has(uuid)) {
        selectedRoomUuids.value.delete(uuid);
    } else {
        selectedRoomUuids.value.add(uuid);
    }
    // Trigger reactivity
    selectedRoomUuids.value = new Set(selectedRoomUuids.value);
};

const selectAllRooms = () => {
    rooms.value.forEach((r: Room) => selectedRoomUuids.value.add(r.uuid));
    selectedRoomUuids.value = new Set(selectedRoomUuids.value);
};

const deselectAllRooms = () => {
    selectedRoomUuids.value = new Set();
};

const allSelected = computed(() => rooms.value.length > 0 && rooms.value.every((r: Room) => selectedRoomUuids.value.has(r.uuid)));

const getRoomDiscountPrice = (room: Room): number | null => {
    if (!form.discount_percentage || !room.price) return null;
    return Math.round(room.price * (1 - form.discount_percentage / 100) * 100) / 100;
};

// Auto-calculate percentage when discount_price changes
watch(() => form.discount_price, (newDiscount) => {
    if (newDiscount && props.hotel.min_price && newDiscount < props.hotel.min_price) {
        form.discount_percentage = Math.round(((props.hotel.min_price - newDiscount) / props.hotel.min_price) * 100);
    } else if (!newDiscount) {
        form.discount_percentage = null;
    }
});

// Auto-calculate discount_price when percentage changes
watch(() => form.discount_percentage, (newPercentage) => {
    if (newPercentage && props.hotel.min_price) {
        form.discount_price = Math.round((props.hotel.min_price * (1 - newPercentage / 100)) * 100) / 100;
    } else if (!newPercentage) {
        form.discount_price = null;
    }
});

const handleSubmit = () => {
    // Build room discounts for selected rooms
    form.room_discounts = rooms.value.map((room: Room) => ({
        uuid: room.uuid,
        discount_price: selectedRoomUuids.value.has(room.uuid)
            ? getRoomDiscountPrice(room)
            : null,
    }));

    form.patch(`/dashboard/hotels/${props.hotel.uuid}/discount`, {
        onSuccess: () => { close(); redirect(); },
    });
};

const handleClearDiscount = () => {
    form.discount_price = null;
    form.discount_percentage = null;
    form.room_discounts = rooms.value.map((room: Room) => ({
        uuid: room.uuid,
        discount_price: null,
    }));
    selectedRoomUuids.value = new Set();

    form.patch(`/dashboard/hotels/${props.hotel.uuid}/discount`, {
        onSuccess: () => { close(); redirect(); },
    });
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Hotel Discount"
        :description="`Set discount for ${hotel.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Discount"
        :loading="form.processing"
        @submit="handleSubmit"
    >
        <div class="space-y-6">
            <!-- Hotel Info -->
            <div class="flex items-center gap-4 p-4 rounded-lg border bg-muted/30">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <HotelIcon class="h-6 w-6 text-primary" />
                </div>
                <div class="flex-1">
                    <p class="font-medium">{{ hotel.name }}</p>
                    <p class="text-sm text-muted-foreground">{{ hotel.city }}, {{ hotel.country }}</p>
                </div>
                <div class="text-right">
                    <p v-if="hotel.min_price !== null" class="text-sm font-medium">
                        {{ formatCurrency(hotel.min_price) }}
                        <span v-if="hotel.max_price && hotel.max_price !== hotel.min_price" class="text-muted-foreground">
                            - {{ formatCurrency(hotel.max_price) }}
                        </span>
                    </p>
                    <p class="text-xs text-muted-foreground">current price range</p>
                </div>
            </div>

            <!-- Discount Form -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium">Discount Settings</h3>
                    <p class="text-sm text-muted-foreground">Set a discount price or percentage</p>
                </div>
                <Separator />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="discount_price">
                            <span class="flex items-center gap-1">
                                <DollarSign class="h-3.5 w-3.5" />
                                Discount Price
                            </span>
                        </Label>
                        <Input
                            id="discount_price"
                            :model-value="form.discount_price ?? undefined"
                            @update:model-value="(v: any) => (form.discount_price = v !== '' ? Number(v) : null)"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                        />
                        <p v-if="form.errors.discount_price" class="text-sm text-destructive">{{ form.errors.discount_price }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="discount_percentage">
                            <span class="flex items-center gap-1">
                                <Percent class="h-3.5 w-3.5" />
                                Discount Percentage
                            </span>
                        </Label>
                        <Input
                            id="discount_percentage"
                            :model-value="form.discount_percentage ?? undefined"
                            @update:model-value="(v: any) => (form.discount_percentage = v !== '' ? Number(v) : null)"
                            type="number"
                            min="0"
                            max="100"
                            placeholder="0"
                        />
                        <p v-if="form.errors.discount_percentage" class="text-sm text-destructive">{{ form.errors.discount_percentage }}</p>
                    </div>
                </div>

                <!-- Preview -->
                <div v-if="form.discount_price && hotel.min_price" class="rounded-lg border p-4 bg-muted/30">
                    <p class="text-xs text-muted-foreground mb-2">Preview</p>
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-destructive">{{ formatCurrency(form.discount_price) }}</span>
                        <span class="text-sm text-muted-foreground line-through">{{ formatCurrency(hotel.min_price) }}</span>
                        <Badge v-if="form.discount_percentage" variant="destructive">-{{ form.discount_percentage }}%</Badge>
                    </div>
                </div>
            </div>

            <!-- Room Selection -->
            <div v-if="rooms.length && form.discount_percentage" class="space-y-4">
                <Separator />
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium">Apply to Rooms</h3>
                        <p class="text-sm text-muted-foreground">
                            Select which rooms get the {{ form.discount_percentage }}% discount
                        </p>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="allSelected ? deselectAllRooms() : selectAllRooms()"
                    >
                        <CheckCheck class="mr-1.5 h-3.5 w-3.5" />
                        {{ allSelected ? 'Deselect All' : 'Select All' }}
                    </Button>
                </div>

                <div class="space-y-2 max-h-[280px] overflow-y-auto rounded-lg border p-2">
                    <div
                        v-for="room in rooms"
                        :key="room.uuid"
                        class="flex items-center gap-3 rounded-lg p-3 cursor-pointer transition-colors"
                        :class="selectedRoomUuids.has(room.uuid) ? 'bg-primary/5 border border-primary/20' : 'hover:bg-muted/50 border border-transparent'"
                        @click="toggleRoom(room.uuid)"
                    >
                        <Checkbox
                            :checked="selectedRoomUuids.has(room.uuid)"
                            @click.stop
                            @update:checked="toggleRoom(room.uuid)"
                        />
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-muted shrink-0">
                            <BedDouble class="h-4 w-4 text-muted-foreground" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ room.name }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ room.room_type || 'Standard' }}
                                <span v-if="room.capacity"> &middot; {{ room.capacity }} guests</span>
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <div v-if="selectedRoomUuids.has(room.uuid) && getRoomDiscountPrice(room)">
                                <span class="text-sm font-medium text-green-600">{{ formatCurrency(getRoomDiscountPrice(room)!) }}</span>
                                <span class="ml-1 text-xs text-muted-foreground line-through">{{ formatCurrency(room.price) }}</span>
                            </div>
                            <div v-else>
                                <span class="text-sm font-medium">{{ formatCurrency(room.price) }}</span>
                            </div>
                            <Badge v-if="room.discount_price && !selectedRoomUuids.has(room.uuid)" variant="outline" class="text-[10px] mt-0.5">
                                has own discount
                            </Badge>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-muted-foreground">
                    {{ selectedRoomUuids.size }} of {{ rooms.length }} rooms selected
                </p>
            </div>

            <!-- Clear discount -->
            <button
                v-if="hotel.discount_price"
                type="button"
                class="text-sm text-destructive hover:underline"
                @click="handleClearDiscount"
            >
                Remove all discounts
            </button>
        </div>
    </ModalForm>
</template>
