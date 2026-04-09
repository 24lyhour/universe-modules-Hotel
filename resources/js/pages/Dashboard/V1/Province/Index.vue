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
    Plus, MapPin, CheckCircle, XCircle, Search, Pencil, Trash2, Database, X,
} from 'lucide-vue-next';
import type { Province, PaginatedResponse } from '../../../../types';

defineOptions({
    layout: (h: (type: unknown, props: unknown, children: unknown) => VNode, page: VNode) =>
        h(AppLayout, { breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Hotels', href: '/dashboard/hotels' },
            { title: 'Provinces', href: '/dashboard/hotel-provinces' },
        ] }, () => page),
});

interface Props {
    provinces: PaginatedResponse<Province>;
    filters: Record<string, string>;
    stats?: {
        total: number;
        active: number;
        inactive: number;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || 'all');

const hasActiveFilters = computed(() => {
    return !!(
        search.value ||
        (statusFilter.value !== 'all' && statusFilter.value !== '')
    );
});

const columns: TableColumn<Province>[] = [
    { key: 'name', label: 'Province' },
    { key: 'code', label: 'Code' },
    { key: 'region', label: 'Region' },
    { key: 'hotels_count', label: 'Hotels', align: 'center' },
    { key: 'is_active', label: 'Status' },
];

const actions: TableAction<Province>[] = [
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (province) => router.visit(`/dashboard/hotel-provinces/${province.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (province) => router.visit(`/dashboard/hotel-provinces/${province.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.provinces.meta.current_page,
    last_page: props.provinces.meta.last_page,
    per_page: props.provinces.meta.per_page,
    total: props.provinces.meta.total,
}));

const handlePageChange = (page: number) => {
    router.get('/dashboard/hotel-provinces', {
        page,
        per_page: pagination.value.per_page,
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/hotel-provinces', {
        per_page: perPage,
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/hotel-provinces', {
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

watch(statusFilter, () => {
    router.get('/dashboard/hotel-provinces', {
        search: search.value || undefined,
        is_active: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
});

const handleClearFilters = () => {
    search.value = '';
    statusFilter.value = 'all';
    router.get('/dashboard/hotel-provinces', {}, { preserveState: true, preserveScroll: true });
};

const handleStatusToggle = (province: Province, _newStatus: boolean) => {
    router.patch(`/dashboard/hotel-provinces/${province.uuid}/toggle-active`, {}, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Provinces" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Provinces</h1>
                <p class="text-muted-foreground">Manage hotel provinces / locations</p>
            </div>
            <Button as-child>
                <Link href="/dashboard/hotel-provinces/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Province
                </Link>
            </Button>
        </div>

        <!-- Stats -->
        <div v-if="stats" class="grid gap-4 md:grid-cols-3">
            <StatsCard
                title="Total Provinces"
                :value="stats.total"
                :icon="MapPin"
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
        </div>

        <!-- Main Content -->
        <div class="flex flex-col gap-4">
            <!-- Filters -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Search provinces..."
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
                :data="props.provinces.data"
                :columns="columns"
                :actions="actions"
                :pagination="pagination"
                :searchable="false"
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            >
                <template #cell-name="{ item }">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                            <MapPin class="h-4 w-4 text-primary" />
                        </div>
                        <div>
                            <span class="font-medium">{{ item.name }}</span>
                            <p v-if="item.name_kh" class="text-xs text-muted-foreground">{{ item.name_kh }}</p>
                        </div>
                    </div>
                </template>

                <template #cell-code="{ item }">
                    <code class="rounded bg-muted px-2 py-1 text-xs font-mono">{{ item.code }}</code>
                </template>

                <template #cell-region="{ item }">
                    <span class="text-muted-foreground">{{ item.region || '-' }}</span>
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
