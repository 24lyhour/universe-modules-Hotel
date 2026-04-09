<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import ModalForm from '@/components/shared/ModalReusable/ModalForm.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { AlertTriangle } from 'lucide-vue-next';
import type { Amenity } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Amenities', href: '/dashboard/hotel-amenities' }, { title: 'Delete', href: '#' }] }, () => page),
});

const props = defineProps<{ amenity: Amenity }>();
const { show, close, redirect } = useModal();
const confirmed = ref(false);
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });
const form = useForm({});

const handleSubmit = () => {
    form.delete(`/dashboard/hotel-amenities/${props.amenity.uuid}`, { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Delete Amenity" description="This action cannot be undone" mode="delete" size="md" submit-text="Delete Amenity" :loading="form.processing" :disabled="!confirmed" @submit="handleSubmit">
        <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
            <AlertTriangle class="mt-0.5 h-5 w-5 text-destructive" />
            <p class="text-sm font-medium text-destructive">You are about to delete "{{ amenity.name }}"</p>
        </div>
        <div class="flex items-start space-x-3 rounded-lg border p-4">
            <Checkbox id="confirmed" :checked="confirmed" @update:checked="(val: boolean) => (confirmed = val)" />
            <Label for="confirmed" class="cursor-pointer font-medium">I confirm this deletion</Label>
        </div>
    </ModalForm>
</template>
