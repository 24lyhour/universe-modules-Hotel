<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import HotelForm from '../../../../Components/Dashboard/V1/HotelForm.vue';
import type { HotelCategory, Province, StatusOption, HotelFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Create', href: '#' }] }, () => page),
});

const props = defineProps<{
    categories: HotelCategory[];
    provinces: Province[];
    statuses: StatusOption[];
}>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => { if (!val) { close(); redirect(); } },
});

const form = useForm<HotelFormData>({
    name: '',
    description: '',
    address: '',
    city: '',
    country: 'Cambodia',
    province_id: null,
    provinces: '',
    phone: '',
    email: '',
    website: '',
    star_rating: 3,
    price_level: '',
    min_price: null,
    max_price: null,
    currency: 'USD',
    hotel_category_id: null,
    logo_url: '',
    featured_image: '',
    total_rooms: 0,
    total_floors: 0,
    gallery: [],
    amenities: [],
    latitude: null,
    longitude: null,
    status: 'active',
    is_featured: false,
});

const isFormInvalid = computed(() => !form.name?.trim());

const handleSubmit = () => {
    form.post('/dashboard/hotels', {
        onSuccess: () => { close(); redirect(); },
    });
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create Hotel"
        description="Add a new hotel property"
        mode="create"
        size="2xl"
        submit-text="Create Hotel"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
    >
        <HotelForm v-model="form" mode="create" :categories="categories" :provinces="provinces" :statuses="statuses" />
    </ModalForm>
</template>
