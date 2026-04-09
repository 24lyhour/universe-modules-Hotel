<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Amenity, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import TrashTable from '@/components/shared/TrashTable/TrashTable.vue';
import { ArrowLeft, RotateCcw } from 'lucide-vue-next';

const props = defineProps<{
    amenities: PaginatedResponse<Amenity>;
}>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Amenities', href: '/dashboard/hotel-amenities' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);
const basePath = '/dashboard/hotel-amenities';

const trashItems = computed(() =>
    props.amenities.data.map((amenity) => ({
        ...amenity,
        display_name: amenity.name,
        type: 'Amenity',
    })),
);

const trashConfig = {
    entityLabel: 'Amenity',
    entityLabelPlural: 'Amenities',
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
    <Head title="Amenity Trash" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="basePath">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Amenity Trash</h1>
                    <p class="text-muted-foreground">Deleted amenities</p>
                </div>
            </div>
        </div>

        <TrashTable
            v-model:selected="selectedUuids"
            :items="trashItems"
            :config="trashConfig"
            :pagination="amenities.meta"
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
