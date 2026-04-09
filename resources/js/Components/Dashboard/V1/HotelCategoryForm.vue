<script setup lang="ts">
import { computed } from 'vue';
import { type InertiaForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import type { HotelCategoryFormData } from '../../../types';
import TiptapEditor from '@/components/TiptapEditor.vue';

interface Props {
    mode?: 'create' | 'edit';
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const model = defineModel<InertiaForm<HotelCategoryFormData>>({ required: true });

const isActive = computed({
    get: () => model.value.is_active,
    set: (value: boolean) => {
        model.value.is_active = value;
    },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Basic Information -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Basic Information</h3>
                <p class="text-sm text-muted-foreground">
                    {{ mode === 'create' ? 'Enter the category details' : 'Update the category details' }}
                </p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="name">Category Name <span class="text-destructive">*</span></Label>
                    <Input id="name" v-model="model.name" type="text" placeholder="Enter category name" />
                    <p v-if="model.errors.name" class="text-sm text-destructive">{{ model.errors.name }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="icon">Icon</Label>
                    <Input id="icon" v-model="model.icon" type="text" placeholder="e.g. Hotel, Building2" />
                    <p v-if="model.errors.icon" class="text-sm text-destructive">{{ model.errors.icon }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="group">Group</Label>
                    <Input id="group" v-model="model.group" type="text" placeholder="e.g. accommodation" />
                    <p v-if="model.errors.group" class="text-sm text-destructive">{{ model.errors.group }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="sort_order">Sort Order</Label>
                    <Input id="sort_order" v-model.number="model.sort_order" type="number" min="0" placeholder="0" />
                    <p v-if="model.errors.sort_order" class="text-sm text-destructive">{{ model.errors.sort_order }}</p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="description">Description</Label>
                    <TiptapEditor v-model="model.description" placeholder="Enter category description" min-height="200px" max-height="400px" />
                    <p v-if="model.errors.description" class="text-sm text-destructive">{{ model.errors.description }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="is_active">Status <span class="text-destructive">*</span></Label>
                    <div class="flex items-center space-x-2 pt-2">
                        <Switch id="is_active" v-model="isActive" />
                        <Label for="is_active" class="font-normal">
                            {{ isActive ? 'Active' : 'Inactive' }}
                        </Label>
                    </div>
                    <p v-if="model.errors.is_active" class="text-sm text-destructive">{{ model.errors.is_active }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
