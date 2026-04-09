<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import HotelCategoryForm from '../../../../Components/Dashboard/V1/HotelCategoryForm.vue';
import type { HotelCategoryFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotel Categories', href: '/dashboard/hotel-categories' }, { title: 'Create', href: '#' }] }, () => page),
});

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<HotelCategoryFormData>({ name: '', icon: '', group: '', description: '', is_active: true, sort_order: 0 });
const isFormInvalid = computed(() => !form.name?.trim());

const handleSubmit = () => {
    form.post('/dashboard/hotel-categories', { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Create Category" description="Add a new hotel category" mode="create" size="lg" submit-text="Create Category" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <HotelCategoryForm v-model="form" mode="create" />
    </ModalForm>
</template>
