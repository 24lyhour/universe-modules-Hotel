<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Hotel, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import TrashTable from '@/components/shared/TrashTable/TrashTable.vue';
import { ArrowLeft, RotateCcw, Trash2 } from 'lucide-vue-next';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Trash', href: '#' }] }, () => page),
});

const props = defineProps<{ hotels: PaginatedResponse<Hotel> }>();

const selectedUuids = ref<(string | number)[]>([]);

const trashItems = computed(() =>
    props.hotels.data.map((hotel) => ({
        ...hotel,
        display_name: hotel.name,
        type: 'Hotel',
    })),
);

const trashConfig = {
    entityLabel: 'Hotel',
    entityLabelPlural: 'Hotels',
    restoreRoute: (uuid: string) => `/dashboard/hotels/${uuid}/restore`,
    forceDeleteRoute: (uuid: string) => `/dashboard/hotels/${uuid}/force-delete`,
    listRoute: '/dashboard/hotels/trash',
};

const handleBulkRestore = () => {
    router.put('/dashboard/hotels/bulk-restore', { uuids: selectedUuids.value }, {
        onSuccess: () => { selectedUuids.value = []; },
    });
};

const handleEmptyTrash = () => {
    router.delete('/dashboard/hotels/empty-trash');
};
</script>

<template>
    <Head title="Hotel Trash" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link href="/dashboard/hotels">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Hotel Trash</h1>
                    <p class="text-muted-foreground">Manage deleted hotels</p>
                </div>
            </div>
            <Button v-if="hotels.data.length" variant="destructive" size="sm" @click="handleEmptyTrash">
                <Trash2 class="mr-2 h-4 w-4" />
                Empty Trash
            </Button>
        </div>

        <TrashTable
            v-model:selected="selectedUuids"
            :items="trashItems"
            :config="trashConfig"
            :pagination="hotels.meta"
            :selectable="true"
            select-key="uuid"
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
