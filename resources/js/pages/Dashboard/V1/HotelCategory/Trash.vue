<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { HotelCategory, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { TableReusable, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { RotateCcw, Trash2, LayoutGrid, Database } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    categories: PaginatedResponse<HotelCategory>;
}>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Categories', href: '/dashboard/hotel-categories' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);
const basePath = '/dashboard/hotel-categories';

const columns: TableColumn<HotelCategory>[] = [
    { key: 'name', label: 'Category Name' },
    { key: 'icon', label: 'Icon' },
    { key: 'group', label: 'Group' },
    { key: 'deleted_at', label: 'Deleted At' },
];

const actions: TableAction<HotelCategory>[] = [
    {
        label: 'Restore',
        icon: RotateCcw,
        onClick: (cat) => router.put(`${basePath}/${cat.uuid}/restore`, {}, {
            preserveScroll: true,
            onSuccess: () => toast.success(`"${cat.name}" restored.`),
        }),
    },
    {
        label: 'Delete Permanently',
        icon: Trash2,
        onClick: (cat) => router.delete(`${basePath}/${cat.uuid}/force-delete`, {
            preserveScroll: true,
            onSuccess: () => toast.success(`"${cat.name}" permanently deleted.`),
        }),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.categories.meta.current_page,
    last_page: props.categories.meta.last_page,
    per_page: props.categories.meta.per_page,
    total: props.categories.meta.total,
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
            toast.success(`${selectedUuids.value.length} categories restored.`);
            selectedUuids.value = [];
        },
    });
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Category Trash" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Category Trash</h1>
                <p class="text-muted-foreground">Deleted hotel categories</p>
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
            </div>
        </div>

        <!-- Table -->
        <TableReusable
            :data="categories.data"
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
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                        <LayoutGrid class="h-4 w-4 text-primary" />
                    </div>
                    <span class="font-medium">{{ item.name }}</span>
                </div>
            </template>

            <template #cell-icon="{ item }">
                <code v-if="item.icon" class="rounded bg-muted px-2 py-1 text-xs font-mono">{{ item.icon }}</code>
                <span v-else class="text-muted-foreground">-</span>
            </template>

            <template #cell-group="{ item }">
                <span class="text-muted-foreground">{{ item.group || '-' }}</span>
            </template>

            <template #cell-deleted_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.deleted_at) }}</span>
            </template>
        </TableReusable>
    </div>
</template>
