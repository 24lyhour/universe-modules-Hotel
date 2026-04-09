<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import { ModalForm } from '@/components/shared';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Provinces', href: '/dashboard/hotel-provinces' }, { title: 'Create', href: '#' }] }, () => page),
});

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm({
    name: '',
    name_kh: '',
    code: '',
    region: '',
    latitude: null as number | null,
    longitude: null as number | null,
    is_active: true,
    sort_order: 0,
});

const isFormInvalid = computed(() => !form.name?.trim() || !form.code?.trim());

const handleSubmit = () => {
    form.post('/dashboard/hotel-provinces', { onSuccess: () => { close(); redirect(); } });
};
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Create Province" description="Add a new province" mode="create" size="lg" submit-text="Create Province" :loading="form.processing" :disabled="isFormInvalid" @submit="handleSubmit">
        <div class="space-y-6">
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium">Province Information</h3>
                    <p class="text-sm text-muted-foreground">Enter province details</p>
                </div>
                <Separator />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="name">Name (English) <span class="text-destructive">*</span></Label>
                        <Input id="name" v-model="form.name" placeholder="e.g. Phnom Penh" />
                        <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="name_kh">Name (Khmer)</Label>
                        <Input id="name_kh" v-model="form.name_kh" placeholder="e.g. ភ្នំពេញ" />
                        <p v-if="form.errors.name_kh" class="text-sm text-destructive">{{ form.errors.name_kh }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="code">Code <span class="text-destructive">*</span></Label>
                        <Input id="code" v-model="form.code" placeholder="e.g. PP" />
                        <p v-if="form.errors.code" class="text-sm text-destructive">{{ form.errors.code }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="region">Region</Label>
                        <Input id="region" v-model="form.region" placeholder="e.g. Central" />
                        <p v-if="form.errors.region" class="text-sm text-destructive">{{ form.errors.region }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="latitude">Latitude</Label>
                        <Input id="latitude" :model-value="form.latitude ?? undefined" @update:model-value="(v: any) => (form.latitude = v !== '' ? Number(v) : null)" type="number" step="0.0000001" placeholder="e.g. 11.5564" />
                        <p v-if="form.errors.latitude" class="text-sm text-destructive">{{ form.errors.latitude }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="longitude">Longitude</Label>
                        <Input id="longitude" :model-value="form.longitude ?? undefined" @update:model-value="(v: any) => (form.longitude = v !== '' ? Number(v) : null)" type="number" step="0.0000001" placeholder="e.g. 104.9282" />
                        <p v-if="form.errors.longitude" class="text-sm text-destructive">{{ form.errors.longitude }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="sort_order">Sort Order</Label>
                        <Input id="sort_order" v-model.number="form.sort_order" type="number" min="0" placeholder="0" />
                    </div>

                    <div class="space-y-2">
                        <Label for="is_active">Status</Label>
                        <div class="flex items-center space-x-2 pt-2">
                            <Switch id="is_active" :checked="form.is_active" @update:checked="form.is_active = $event" />
                            <Label for="is_active" class="font-normal">{{ form.is_active ? 'Active' : 'Inactive' }}</Label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModalForm>
</template>
