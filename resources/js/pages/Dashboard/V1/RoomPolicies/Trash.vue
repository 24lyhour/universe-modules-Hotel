<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { RoomPolicy, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { TableReusable, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { RotateCcw, Trash2, ClipboardList, Database } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    policies: PaginatedResponse<RoomPolicy>;
}>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Room Policies', href: '/dashboard/hotel-room-policies' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);
const basePath = '/dashboard/hotel-room-policies';

const columns: TableColumn<RoomPolicy>[] = [
    { key: 'title', label: 'Policy Title' },
    { key: 'icon', label: 'Icon' },
    { key: 'deleted_at', label: 'Deleted At' },
];

const actions: TableAction<RoomPolicy>[] = [
    {
        label: 'Restore',
        icon: RotateCcw,
        onClick: (policy) => router.put(`${basePath}/${policy.uuid}/restore`, {}, {
            preserveScroll: true,
            onSuccess: () => toast.success(`"${policy.title}" restored.`),
        }),
    },
    {
        label: 'Delete Permanently',
        icon: Trash2,
        onClick: (policy) => router.delete(`${basePath}/${policy.uuid}/force-delete`, {
            preserveScroll: true,
            onSuccess: () => toast.success(`"${policy.title}" permanently deleted.`),
        }),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.policies.meta.current_page,
    last_page: props.policies.meta.last_page,
    per_page: props.policies.meta.per_page,
    total: props.policies.meta.total,
}));

const handlePageChange = (page: number) => {
    router.get(`${basePath}/trash`, { page }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get(`${basePath}/trash`, { per_page: perPage }, { preserveState: true });
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Room Policy Trash" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Room Policy Trash</h1>
                    <p class="text-muted-foreground">Deleted room policies</p>
                </div>
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
            :data="policies.data"
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
            <template #cell-title="{ item }">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                        <ClipboardList class="h-4 w-4 text-primary" />
                    </div>
                    <span class="font-medium">{{ item.title }}</span>
                </div>
            </template>

            <template #cell-icon="{ item }">
                <code v-if="item.icon" class="rounded bg-muted px-2 py-1 text-xs font-mono">{{ item.icon }}</code>
                <span v-else class="text-muted-foreground">-</span>
            </template>

            <template #cell-deleted_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.deleted_at) }}</span>
            </template>
        </TableReusable>
    </div>
</template>
