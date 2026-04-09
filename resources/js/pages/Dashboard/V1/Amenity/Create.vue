<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import AmenityForm from '../../../../Components/Dashboard/V1/AmenityForm.vue';
import type { AmenityFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Amenities', href: '/dashboard/hotel-amenities' }, { title: 'Create', href: '#' }] }, () => page),
});

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<AmenityFormData>({ name: '', icon: '', group: '', description: '', is_active: true, sort_order: 0 });
const isFormInvalid = computed(() => !form.name?.trim());

const handleSubmit = () => {
    form.post('/dashboard/hotel-amenities', { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Create Amenity" description="Add a new amenity" mode="create" size="lg" submit-text="Create Amenity" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <AmenityForm v-model="form" mode="create" />
    </ModalForm>
</template>
