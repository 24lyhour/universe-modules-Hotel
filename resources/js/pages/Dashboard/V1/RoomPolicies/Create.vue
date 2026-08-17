<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import RoomPolicyForm from '../../../../Components/Dashboard/V1/RoomPolicyForm.vue';
import type { RoomPolicyFormData } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Room Policies', href: '/dashboard/hotel-room-policies' }, { title: 'Create', href: '#' }] }, () => page),
});

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm<RoomPolicyFormData>({ title: '', icon: '', description: '', is_active: true, sort_order: 0 });
const isFormInvalid = computed(() => !form.title?.trim());

const handleSubmit = () => {
    form.post('/dashboard/hotel-room-policies', { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Create Room Policy" description="Add a new room policy" mode="create" size="lg" submit-text="Create Policy" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <RoomPolicyForm v-model="form" mode="create" />
    </ModalForm>
</template>
