<script setup lang="ts">
import { computed, ref, watch } from 'vue';
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
    Plus, LayoutGrid, CheckCircle, XCircle, Search, Eye, Pencil, Trash2, Database, X,
    Hotel, Building2, Briefcase, Home, Castle, Tent, Warehouse,
    type LucideIcon,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { HotelCategory, PaginatedResponse } from '../../../../types';

// Icon map — matches Lucide icon names stored in the `icon` field
const iconMap: Record<string, LucideIcon> = {
    Hotel, Building2, Briefcase, Home, Castle, Tent, Warehouse, LayoutGrid,
};

const getCategoryIcon = (iconName: string | null): LucideIcon => {
    if (!iconName) return LayoutGrid;
    return iconMap[iconName] || LayoutGrid;
};

interface Props {
    categories: PaginatedResponse<HotelCategory>;
    filters: Record<string, string>;
    stats?: {
        total: number;
        active: number;
        inactive: number;
        trashed: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Hotels', href: '/dashboard/hotels' },
    { title: 'Categories', href: '/dashboard/hotel-categories' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || 'all');
const selectedUuids = ref<(string | number)[]>([]);

const hasActiveFilters = computed(() => {
    return !!(
        search.value ||
        (statusFilter.value !== 'all' && statusFilter.value !== '')
    );
});

const columns: TableColumn<HotelCategory>[] = [
    {
        key: 'name',
        label: 'Category Name',
    },
    {
        key: 'icon',
        label: 'Icon',
    },
    {
        key: 'group',
        label: 'Group',
        render: (cat) => cat.group || '-',
    },
    {
        key: 'hotels_count',
        label: 'Hotels',
        align: 'center',
    },
    {
        key: 'sort_order',
        label: 'Sort Order',
        render: (cat) => cat.sort_order.toString(),
    },
    {
        key: 'is_active',
        label: 'Status',
    },
];

const actions: TableAction<HotelCategory>[] = [
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (cat) => router.visit(`/dashboard/hotel-categories/${cat.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (cat) => router.visit(`/dashboard/hotel-categories/${cat.uuid}/delete`),
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
    router.get('/dashboard/hotel-categories', {
        page,
        per_page: pagination.value.per_page,
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/hotel-categories', {
        per_page: perPage,
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/hotel-categories', {
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

watch(statusFilter, () => {
    router.get('/dashboard/hotel-categories', {
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
});

const handleClearFilters = () => {
    search.value = '';
    statusFilter.value = 'all';
    router.get('/dashboard/hotel-categories', {}, { preserveState: true, preserveScroll: true });
};

const handleStatusToggle = (cat: HotelCategory, _newStatus: boolean) => {
    router.patch(`/dashboard/hotel-categories/${cat.uuid}/toggle-active`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/hotel-categories/bulk-delete?${params.toString()}`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Hotel Categories" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Hotel Categories</h1>
                    <p class="text-muted-foreground">Manage hotel property types</p>
                </div>
                <div class="flex items-center gap-2">
                    <ButtonGroup>
                        <Button variant="default">
                            <Database class="mr-2 h-4 w-4" />
                            All
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/dashboard/hotel-categories/trash">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Trash
                            </Link>
                        </Button>
                    </ButtonGroup>
                    <Button as-child>
                        <Link href="/dashboard/hotel-categories/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Category
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats -->
            <div v-if="stats" class="grid gap-4 md:grid-cols-4">
                <StatsCard
                    title="Total Categories"
                    :value="stats.total"
                    :icon="LayoutGrid"
                />
                <StatsCard
                    title="Active"
                    :value="stats.active"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    title="Inactive"
                    :value="stats.inactive"
                    :icon="XCircle"
                    variant="warning"
                />
                <StatsCard
                    title="In Trash"
                    :value="stats.trashed ?? 0"
                    :icon="Trash2"
                    variant="destructive"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <!-- Filters -->
                <div class="flex items-center gap-4">
                    <div class="relative flex-1 max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search categories..."
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
                    :data="props.categories.data"
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

                    <!-- Name with icon -->
                    <template #cell-name="{ item }">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                                <component :is="getCategoryIcon(item.icon)" class="h-4 w-4 text-primary" />
                            </div>
                            <span class="font-medium">{{ item.name }}</span>
                        </div>
                    </template>

                    <!-- Icon column: show rendered icon + name -->
                    <template #cell-icon="{ item }">
                        <div v-if="item.icon" class="flex items-center gap-2">
                            <component :is="getCategoryIcon(item.icon)" class="h-4 w-4 text-muted-foreground" />
                            <code class="rounded bg-muted px-2 py-1 text-xs font-mono">{{ item.icon }}</code>
                        </div>
                        <span v-else class="text-muted-foreground">-</span>
                    </template>

                    <!-- Status with toggle switch -->
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
    </AppLayout>
</template>
