<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import HotelForm from '../../../../Components/Dashboard/V1/HotelForm.vue';
import type { Hotel, HotelCategory, Province, StatusOption, HotelFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Edit', href: '#' }] }, () => page),
});

const props = defineProps<{
    hotel: Hotel;
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
    name: props.hotel.name,
    description: props.hotel.description ?? '',
    address: props.hotel.address,
    city: props.hotel.city,
    country: props.hotel.country,
    province_id: props.hotel.province?.id ?? null,
    provinces: props.hotel.provinces ?? '',
    phone: props.hotel.phone ?? '',
    email: props.hotel.email ?? '',
    website: props.hotel.website ?? '',
    star_rating: props.hotel.star_rating,
    price_level: props.hotel.price_level ?? '',
    min_price: props.hotel.min_price,
    max_price: props.hotel.max_price,
    currency: props.hotel.currency,
    hotel_category_id: props.hotel.category?.id ?? null,
    logo_url: props.hotel.logo_url ?? '',
    featured_image: props.hotel.featured_image ?? '',
    total_rooms: props.hotel.total_rooms,
    total_floors: props.hotel.total_floors,
    gallery: props.hotel.gallery ?? [],
    amenities: props.hotel.amenities ?? [],
    latitude: props.hotel.latitude,
    longitude: props.hotel.longitude,
    status: props.hotel.status,
    is_featured: props.hotel.is_featured,
});

const isFormInvalid = computed(() => !form.name?.trim());

const handleSubmit = () => {
    form.put(`/dashboard/hotels/${props.hotel.uuid}`, {
        onSuccess: () => { close(); redirect(); },
    });
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Edit Hotel"
        :description="`Editing ${hotel.name}`"
        mode="edit"
        size="2xl"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
    >
        <HotelForm
            v-model="form"
            mode="edit"
            :categories="categories"
            :provinces="provinces"
            :statuses="statuses"
            :price-info="{
                min_price: hotel.min_price,
                max_price: hotel.max_price,
                discount_price: hotel.discount_price,
                discount_percentage: hotel.discount_percentage,
                currency: hotel.currency,
            }"
        />
    </ModalForm>
</template>
