<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import AmenityForm from '../../../../Components/Dashboard/V1/AmenityForm.vue';
import type { Amenity, AmenityFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Amenities', href: '/dashboard/hotel-amenities' }, { title: 'Edit', href: '#' }] }, () => page),
});

const props = defineProps<{ amenity: Amenity }>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<AmenityFormData>({
    name: props.amenity.name, icon: props.amenity.icon ?? '', group: props.amenity.group ?? '',
    description: props.amenity.description ?? '', is_active: props.amenity.is_active, sort_order: props.amenity.sort_order,
});

const isFormInvalid = computed(() => !form.name?.trim());

const handleSubmit = () => {
    form.put(`/dashboard/hotel-amenities/${props.amenity.uuid}`, { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" :title="`Edit ${amenity.name}`" description="Update amenity" mode="edit" size="lg" submit-text="Save Changes" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <AmenityForm v-model="form" mode="edit" />
    </ModalForm>
</template>
