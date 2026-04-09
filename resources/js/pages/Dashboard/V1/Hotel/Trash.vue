<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Hotel, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { TableReusable, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { RotateCcw, Trash2, Hotel as HotelIcon, Database, Star } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    hotels: PaginatedResponse<Hotel>;
}>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);
const basePath = '/dashboard/hotels';

const columns: TableColumn<Hotel>[] = [
    { key: 'name', label: 'Hotel', width: '25%' },
    { key: 'city', label: 'City' },
    { key: 'star_rating', label: 'Stars', align: 'center' },
    { key: 'status', label: 'Status' },
    { key: 'deleted_at', label: 'Deleted At' },
];

const actions: TableAction<Hotel>[] = [
    {
        label: 'Restore',
        icon: RotateCcw,
        onClick: (hotel) => router.put(`${basePath}/${hotel.uuid}/restore`, {}, {
            preserveScroll: true,
            onSuccess: () => toast.success(`"${hotel.name}" restored.`),
        }),
    },
    {
        label: 'Delete Permanently',
        icon: Trash2,
        onClick: (hotel) => router.delete(`${basePath}/${hotel.uuid}/force-delete`, {
            preserveScroll: true,
            onSuccess: () => toast.success(`"${hotel.name}" permanently deleted.`),
        }),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.hotels.meta.current_page,
    last_page: props.hotels.meta.last_page,
    per_page: props.hotels.meta.per_page,
    total: props.hotels.meta.total,
}));

const handlePageChange = (page: number) => {
    router.get(`${basePath}/trash`, { page }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get(`${basePath}/trash`, { per_page: perPage }, { preserveState: true });
};

const handleBulkRestore = () => {
    router.put(`${basePath}/bulk-restore`, { uuids: selectedUuids.value }, {
        onSuccess: () => {
            toast.success(`${selectedUuids.value.length} hotels restored.`);
            selectedUuids.value = [];
        },
    });
};

const handleEmptyTrash = () => {
    router.delete(`${basePath}/empty-trash`, {
        onSuccess: () => toast.success('Trash emptied.'),
    });
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'active': return 'default';
        case 'inactive': return 'secondary';
        case 'draft': return 'outline';
        default: return 'outline';
    }
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Hotel Trash" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Hotel Trash</h1>
                <p class="text-muted-foreground">Manage deleted hotels</p>
            </div>
            <div class="flex items-center gap-2">
                <ButtonGroup>
                    <Button variant="outline" as-child>
                        <Link :href="basePath">
                            <Database class="mr-2 h-4 w-4" />
                            All
                        </Link>
                    </Button>
                    <Button variant="default">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Trash
                    </Button>
                </ButtonGroup>
                <Button v-if="hotels.data.length" variant="destructive" size="sm" @click="handleEmptyTrash">
                    <Trash2 class="mr-2 h-4 w-4" />
                    Empty Trash
                </Button>
            </div>
        </div>

        <!-- Table -->
        <TableReusable
            :data="hotels.data"
            :columns="columns"
            :actions="actions"
            :pagination="pagination"
            :searchable="false"
            :selectable="true"
            select-key="uuid"
            v-model:selected="selectedUuids"
            @page-change="handlePageChange"
            @per-page-change="handlePerPageChange"
        >
            <template #bulk-actions>
                <Button v-if="selectedUuids.length > 0" variant="outline" size="sm" @click="handleBulkRestore">
                    <RotateCcw class="mr-2 h-4 w-4" />
                    Restore {{ selectedUuids.length }} Selected
                </Button>
            </template>

            <template #cell-name="{ item }">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 overflow-hidden rounded-md bg-muted shrink-0">
                        <img v-if="item.logo_url || item.featured_image" :src="(item.logo_url || item.featured_image)!" :alt="item.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center">
                            <HotelIcon class="h-5 w-5 text-muted-foreground" />
                        </div>
                    </div>
                    <div>
                        <span class="font-medium">{{ item.name }}</span>
                        <p v-if="item.category" class="text-xs text-muted-foreground">{{ item.category.name }}</p>
                    </div>
                </div>
            </template>

            <template #cell-star_rating="{ item }">
                <div class="flex items-center gap-1">
                    <Star class="h-3.5 w-3.5 fill-yellow-400 text-yellow-400" />
                    <span class="text-sm">{{ item.star_rating }}</span>
                </div>
            </template>

            <template #cell-status="{ item }">
                <Badge :variant="getStatusVariant(item.status)">{{ item.status }}</Badge>
            </template>

            <template #cell-deleted_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.deleted_at) }}</span>
            </template>
        </TableReusable>
    </div>
</template>
