<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import RoomForm from '../../../../Components/Dashboard/V1/RoomForm.vue';
import type { StatusOption, RoomFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '#' }, { title: 'Create', href: '#' }] }, () => page),
});

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string };
    statuses: StatusOption[];
}>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<RoomFormData>({
    name: '', total_room: 1, room_type: '', room_number: '', description: '', price: null, discount_price: null,
    capacity: 2, bed_type: '', bed_count: 1, bathroom_count: 1, room_size: '', view: '',
    amenities: [], images: [], is_available: true, sort_order: 0, status: 'active',
});

const isFormInvalid = computed(() => !form.name?.trim() || form.price === null);

const handleSubmit = () => {
    form.post(`/dashboard/hotels/${props.hotel.uuid}/rooms`, { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Add Room" :description="`Add a room to ${hotel.name}`" mode="create" size="xl" submit-text="Create Room" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <RoomForm v-model="form" mode="create" :statuses="statuses" />
    </ModalForm>
</template>
