<script setup lang="ts">
import { computed, ref, watch, type VNode } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Plus, ClipboardList, CheckCircle, XCircle, Search, Pencil, Trash2, Database, X,
} from 'lucide-vue-next';
import type { RoomPolicy, PaginatedResponse } from '../../../../types';

defineOptions({
    layout: (h: (type: unknown, props: unknown, children: unknown) => VNode, page: VNode) =>
        h(AppLayout, { breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Hotels', href: '/dashboard/hotels' },
            { title: 'Room Policies', href: '/dashboard/hotel-room-policies' },
        ] }, () => page),
});

interface Props {
    policies: PaginatedResponse<RoomPolicy>;
    filters: Record<string, string>;
    stats?: {
        total: number;
        active: number;
        inactive: number;
        trashed: number;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || 'all');
const selectedUuids = ref<(string | number)[]>([]);

const hasActiveFilters = computed(() => {
    return !!(
        search.value ||
        (statusFilter.value !== 'all' && statusFilter.value !== '')
    );
});

const columns: TableColumn<RoomPolicy>[] = [
    { key: 'title', label: 'Policy Title' },
    { key: 'icon', label: 'Icon' },
    { key: 'sort_order', label: 'Order', align: 'center' },
    { key: 'is_active', label: 'Status' },
];

const actions: TableAction<RoomPolicy>[] = [
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (policy) => router.visit(`/dashboard/hotel-room-policies/${policy.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (policy) => router.visit(`/dashboard/hotel-room-policies/${policy.uuid}/delete`),
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
    router.get('/dashboard/hotel-room-policies', {
        page,
        per_page: pagination.value.per_page,
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/hotel-room-policies', {
        per_page: perPage,
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/hotel-room-policies', {
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

watch(statusFilter, () => {
    router.get('/dashboard/hotel-room-policies', {
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
});

const handleClearFilters = () => {
    search.value = '';
    statusFilter.value = 'all';
    router.get('/dashboard/hotel-room-policies', {}, { preserveState: true, preserveScroll: true });
};

const handleStatusToggle = (policy: RoomPolicy, _newStatus: boolean) => {
    router.patch(`/dashboard/hotel-room-policies/${policy.uuid}/toggle-active`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/hotel-room-policies/bulk-delete?${params.toString()}`);
};
</script>

<template>
    <Head title="Room Policies" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Room Policies</h1>
                <p class="text-muted-foreground">Manage standard room policies</p>
            </div>
            <div class="flex items-center gap-2">
                <ButtonGroup>
                    <Button variant="default">
                        <Database class="mr-2 h-4 w-4" />
                        All
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/dashboard/hotel-room-policies/trash">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Trash
                        </Link>
                    </Button>
                </ButtonGroup>
                <Button as-child>
                    <Link href="/dashboard/hotel-room-policies/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Policy
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Stats -->
        <div v-if="stats" class="grid gap-4 md:grid-cols-4">
            <StatsCard title="Total Policies" :value="stats.total" :icon="ClipboardList" />
            <StatsCard title="Active" :value="stats.active" :icon="CheckCircle" variant="success" />
            <StatsCard title="Inactive" :value="stats.inactive" :icon="XCircle" variant="warning" />
            <StatsCard title="In Trash" :value="stats.trashed ?? 0" :icon="Trash2" variant="destructive" />
        </div>

        <!-- Main Content -->
        <div class="flex flex-col gap-4">
            <!-- Filters -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Search policies..."
                        class="pl-9"
                        @keyup.enter="handleSearch"
                    />
                </div>
                <Select v-model="statusFilter">
                    <SelectTrigger class="w-[150px]">
                        <SelectValue placeholder="Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All Status</SelectItem>
                        <SelectItem value="1">Active</SelectItem>
                        <SelectItem value="0">Inactive</SelectItem>
                    </SelectContent>
                </Select>
                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    size="sm"
                    class="text-muted-foreground hover:text-foreground"
                    @click="handleClearFilters"
                >
                    <X class="mr-1 h-4 w-4" />
                    Clear Filters
                </Button>
            </div>

            <!-- Table -->
            <TableReusable
                :data="props.policies.data"
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
                    <Button
                        v-if="selectedUuids.length > 0"
                        variant="destructive"
                        size="sm"
                        @click="openBulkDeleteDialog"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete {{ selectedUuids.length }} Selected
                    </Button>
                </template>

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

                <template #cell-is_active="{ item }">
                    <div class="flex items-center gap-2" @click.stop>
                        <Switch
                            :model-value="item.is_active"
                            @update:model-value="handleStatusToggle(item, $event)"
                        />
                        <span class="text-sm text-muted-foreground">
                            {{ item.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </template>
            </TableReusable>
        </div>
    </div>
</template>
