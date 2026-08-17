<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import type { InertiaForm } from '@inertiajs/vue3';
import type { RoomPolicyFormData } from '../../../types';
import TiptapEditor from '@/components/TiptapEditor.vue';

interface Props {
    mode?: 'create' | 'edit';
}

withDefaults(defineProps<Props>(), { mode: 'create' });

const model = defineModel<InertiaForm<RoomPolicyFormData>>({ required: true });

const isActive = computed({
    get: () => model.value.is_active,
    set: (value: boolean) => {
        model.value.is_active = value;
    },
});
</script>

<template>
    <div class="space-y-6">
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Policy Details</h3>
                <p class="text-sm text-muted-foreground">
                    {{ mode === 'create' ? 'Enter the room policy details' : 'Update the room policy details' }}
                </p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="title">Title <span class="text-destructive">*</span></Label>
                    <Input
                        id="title"
                        v-model="model.title"
                        type="text"
                        placeholder="e.g. Check-in / Check-out"
                    />
                    <p v-if="model.errors.title" class="text-sm text-destructive">
                        {{ model.errors.title }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="icon">Icon</Label>
                    <Input
                        id="icon"
                        v-model="model.icon"
                        type="text"
                        placeholder="e.g. Clock, PawPrint, XCircle"
                    />
                    <p v-if="model.errors.icon" class="text-sm text-destructive">
                        {{ model.errors.icon }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="sort_order">Sort Order</Label>
                    <Input
                        id="sort_order"
                        v-model.number="model.sort_order"
                        type="number"
                        min="0"
                        placeholder="0"
                    />
                    <p v-if="model.errors.sort_order" class="text-sm text-destructive">
                        {{ model.errors.sort_order }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="status">Status <span class="text-destructive">*</span></Label>
                    <div class="flex items-center space-x-2 pt-2">
                        <Switch id="status" v-model="isActive" />
                        <Label for="status" class="font-normal">
                            {{ isActive ? 'Active' : 'Inactive' }}
                        </Label>
                    </div>
                    <p v-if="model.errors.is_active" class="text-sm text-destructive">
                        {{ model.errors.is_active }}
                    </p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="description">Description</Label>
                    <TiptapEditor
                        v-model="model.description"
                        placeholder="Describe this policy (e.g. Check-in: 2:00 PM — Check-out: 12:00 PM)"
                        min-height="180px"
                        max-height="360px"
                    />
                    <p v-if="model.errors.description" class="text-sm text-destructive">
                        {{ model.errors.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
