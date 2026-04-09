<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import RoomForm from '../../../../Components/Dashboard/V1/RoomForm.vue';
import type { Room, StatusOption, RoomFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '#' }, { title: 'Edit', href: '#' }] }, () => page),
});

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string };
    room: Room;
    statuses: StatusOption[];
}>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<RoomFormData>({
    name: props.room.name, total_room: props.room.total_room, room_type: props.room.room_type ?? '',
    room_number: props.room.room_number ?? '', description: props.room.description ?? '',
    price: props.room.price, discount_price: props.room.discount_price,
    capacity: props.room.capacity, bed_type: props.room.bed_type ?? '', bed_count: props.room.bed_count,
    bathroom_count: props.room.bathroom_count, room_size: props.room.room_size ?? '', view: props.room.view ?? '',
    amenities: props.room.amenities ?? [], images: props.room.images ?? [],
    is_available: props.room.is_available, sort_order: props.room.sort_order, status: props.room.status,
});

const isFormInvalid = computed(() => !form.name?.trim() || form.price === null);

const handleSubmit = () => {
    form.put(`/dashboard/hotels/${props.hotel.uuid}/rooms/${props.room.uuid}`, { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" :title="`Edit ${room.name}`" description="Update room details" mode="edit" size="xl" submit-text="Save Changes" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <RoomForm v-model="form" mode="edit" :statuses="statuses" />
    </ModalForm>
</template>
