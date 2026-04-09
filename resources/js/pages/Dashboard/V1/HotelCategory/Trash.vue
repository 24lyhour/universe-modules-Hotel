<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { HotelCategory, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import TrashTable from '@/components/shared/TrashTable/TrashTable.vue';
import { ArrowLeft, RotateCcw } from 'lucide-vue-next';

const props = defineProps<{
    categories: PaginatedResponse<HotelCategory>;
}>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Categories', href: '/dashboard/hotel-categories' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);
const basePath = '/dashboard/hotel-categories';

const trashItems = computed(() =>
    props.categories.data.map((category) => ({
        ...category,
        display_name: category.name,
        type: 'Category',
    })),
);

const trashConfig = {
    entityLabel: 'Category',
    entityLabelPlural: 'Categories',
    restoreRoute: (uuid: string) => `${basePath}/${uuid}/restore`,
    forceDeleteRoute: (uuid: string) => `${basePath}/${uuid}/force-delete`,
    listRoute: `${basePath}/trash`,
};

const handleBulkRestore = () => {
    router.put(`${basePath}/bulk-restore`, { uuids: selectedUuids.value }, {
        onSuccess: () => { selectedUuids.value = []; },
    });
};
</script>

<template>
    <Head title="Category Trash" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="basePath">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Category Trash</h1>
                    <p class="text-muted-foreground">Deleted hotel categories</p>
                </div>
            </div>
        </div>

        <TrashTable
            v-model:selected="selectedUuids"
            :items="trashItems"
            :config="trashConfig"
            :pagination="categories.meta"
            :selectable="true"
            select-key="uuid"
            :show-type="false"
        >
            <template #bulk-actions>
                <Button v-if="selectedUuids.length" variant="outline" size="sm" @click="handleBulkRestore">
                    <RotateCcw class="mr-2 h-4 w-4" />
                    Restore Selected ({{ selectedUuids.length }})
                </Button>
            </template>
        </TrashTable>
    </div>
</template>
