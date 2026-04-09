<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import { ModalForm } from '@/components/shared';
import { SearchableSelect } from '@/components/shared';
import type { SearchableSelectOption } from '@/components/shared/SearchableSelect/SearchableSelect.vue';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import RoomForm from '../../../../Components/Dashboard/V1/RoomForm.vue';
import type { StatusOption, RoomFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '#' }, { title: 'Create', href: '#' }] }, () => page),
});

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string } | null;
    hotels?: { id: number; uuid: string; name: string }[];
    statuses: StatusOption[];
}>();

const isStandalone = computed(() => !props.hotel);

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<RoomFormData & { hotel_uuid: string }>({
    hotel_uuid: props.hotel?.uuid ?? '',
    name: '', total_room: 1, room_type: '', room_number: '', description: '', price: null, discount_price: null,
    capacity: 2, bed_type: '', bed_count: 1, bathroom_count: 1, room_size: '', view: '',
    amenities: [], images: [], is_available: true, sort_order: 0, status: 'active',
});

const hotelOptions = computed<SearchableSelectOption[]>(() =>
    (props.hotels ?? []).map(h => ({ value: h.uuid, label: h.name }))
);

const isFormInvalid = computed(() => !form.name?.trim() || form.price === null || (!props.hotel && !form.hotel_uuid));

const handleSubmit = () => {
    if (props.hotel) {
        form.post(`/dashboard/hotels/${props.hotel.uuid}/rooms`, { onSuccess: () => { close(); redirect(); } });
    } else {
        form.post('/dashboard/hotel-rooms', { onSuccess: () => { close(); redirect(); } });
    }
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Add Room" :description="hotel ? `Add a room to ${hotel.name}` : 'Add a new room'" mode="create" size="xl" submit-text="Create Room" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <!-- Hotel selector for standalone mode -->
        <div v-if="isStandalone" class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Select Hotel</h3>
                <p class="text-sm text-muted-foreground">Choose which hotel this room belongs to</p>
            </div>
            <Separator />
            <div class="space-y-2">
                <Label for="hotel_uuid">Hotel <span class="text-destructive">*</span></Label>
                <SearchableSelect
                    v-model="form.hotel_uuid"
                    :options="hotelOptions"
                    placeholder="Search and select hotel..."
                    search-placeholder="Search hotels..."
                    empty-message="No hotels found."
                />
                <p v-if="form.errors.hotel_uuid" class="text-sm text-destructive">{{ form.errors.hotel_uuid }}</p>
            </div>
        </div>

        <RoomForm v-model="form" mode="create" :statuses="statuses" />
    </ModalForm>
</template>
