<script setup lang="ts">
import { type InertiaForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import type { HotelCategoryFormData } from '../../../types';

interface Props {
    mode?: 'create' | 'edit';
}

withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const model = defineModel<InertiaForm<HotelCategoryFormData>>({ required: true });
</script>

<template>
    <div class="grid gap-4">
        <div>
            <Label for="name">Name *</Label>
            <Input id="name" v-model="model.name" placeholder="Category name" :class="{ 'border-destructive': model.errors.name }" />
            <p v-if="model.errors.name" class="mt-1 text-sm text-destructive">{{ model.errors.name }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <Label for="icon">Icon</Label>
                <Input id="icon" v-model="model.icon" placeholder="e.g. Hotel, Building" />
            </div>
            <div>
                <Label for="group">Group</Label>
                <Input id="group" v-model="model.group" placeholder="e.g. accommodation" />
            </div>
        </div>

        <div>
            <Label for="description">Description</Label>
            <Textarea id="description" v-model="model.description" placeholder="Category description" rows="2" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <Label for="sort_order">Sort Order</Label>
                <Input id="sort_order" v-model.number="model.sort_order" type="number" min="0" placeholder="0" />
            </div>
            <div class="flex items-center gap-2 pt-6">
                <Checkbox id="is_active" :checked="model.is_active" @update:checked="(val: boolean) => (model.is_active = val)" />
                <Label for="is_active" class="cursor-pointer">Active</Label>
            </div>
        </div>
    </div>
</template>
