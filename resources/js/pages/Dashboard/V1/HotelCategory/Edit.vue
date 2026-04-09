<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import HotelCategoryForm from '../../../../Components/Dashboard/V1/HotelCategoryForm.vue';
import type { HotelCategory, HotelCategoryFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotel Categories', href: '/dashboard/hotel-categories' }, { title: 'Edit', href: '#' }] }, () => page),
});

const props = defineProps<{ category: HotelCategory }>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<HotelCategoryFormData>({
    name: props.category.name, icon: props.category.icon ?? '', group: props.category.group ?? '',
    description: props.category.description ?? '', is_active: props.category.is_active, sort_order: props.category.sort_order,
});

const isFormInvalid = computed(() => !form.name?.trim());

const handleSubmit = () => {
    form.put(`/dashboard/hotel-categories/${props.category.uuid}`, { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" :title="`Edit ${category.name}`" description="Update hotel category" mode="edit" size="lg" submit-text="Save Changes" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <HotelCategoryForm v-model="form" mode="edit" />
    </ModalForm>
</template>
