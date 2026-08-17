<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import RoomPolicyForm from '../../../../Components/Dashboard/V1/RoomPolicyForm.vue';
import type { RoomPolicy, RoomPolicyFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Room Policies', href: '/dashboard/hotel-room-policies' }, { title: 'Edit', href: '#' }] }, () => page),
});

const props = defineProps<{ policy: RoomPolicy }>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<RoomPolicyFormData>({
    title: props.policy.title, icon: props.policy.icon ?? '',
    description: props.policy.description ?? '', is_active: props.policy.is_active, sort_order: props.policy.sort_order,
});

const isFormInvalid = computed(() => !form.title?.trim());

const handleSubmit = () => {
    form.put(`/dashboard/hotel-room-policies/${props.policy.uuid}`, { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" :title="`Edit ${policy.title}`" description="Update room policy" mode="edit" size="lg" submit-text="Save Changes" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <RoomPolicyForm v-model="form" mode="edit" />
    </ModalForm>
</template>
