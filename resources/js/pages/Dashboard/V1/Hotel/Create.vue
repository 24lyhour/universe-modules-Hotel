<script setup lang="ts">
import { h, computed, watch, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import { ModalForm } from '@/components/shared';
import HotelForm from '../../../../Components/Dashboard/V1/HotelForm.vue';
import type { HotelCategory, Province, StatusOption, HotelFormData } from '../../../../types';
import { hotelSchema } from '../../../../validations/HotelValidation';
import { useFormValidation } from '@/composables/useFormValidation';

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
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

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

const { validateAndSubmit, createIsFormInvalid, validateForm } = useFormValidation(
    hotelSchema,
    ['name', 'address', 'city', 'country', 'phone', 'province_id', 'hotel_category_id', 'price_level', 'star_rating', 'status']
);

const getFormData = () => ({
    name: form.name,
    description: form.description || null,
    address: form.address,
    city: form.city,
    country: form.country,
    province_id: form.province_id,
    provinces: form.provinces || null,
    phone: form.phone,
    email: form.email || null,
    website: form.website || null,
    star_rating: form.star_rating,
    price_level: form.price_level,
    min_price: form.min_price,
    max_price: form.max_price,
    currency: form.currency,
    hotel_category_id: form.hotel_category_id,
    logo_url: form.logo_url || null,
    featured_image: form.featured_image || null,
    total_rooms: form.total_rooms,
    total_floors: form.total_floors,
    gallery: form.gallery,
    amenities: form.amenities,
    latitude: form.latitude,
    longitude: form.longitude,
    status: form.status,
    is_featured: form.is_featured,
});

watch(
    [() => form.name, () => form.address, () => form.city, () => form.phone, () => form.province_id, () => form.hotel_category_id, () => form.price_level],
    () => { validateForm(getFormData()); }
);

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/hotels', {
            onSuccess: () => { close(); redirect(); },
        });
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
