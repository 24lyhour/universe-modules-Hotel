<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Room, StatusOption, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { TableReusable, StatsCard, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Plus, Pencil, Trash2, Search, ArrowLeft, BedDouble, X, CheckCircle, Wrench, Database } from 'lucide-vue-next';

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string } | null;
    rooms: PaginatedResponse<Room>;
    filters: Record<string, string>;
    statuses: StatusOption[];
    hotels?: { id: number; uuid: string; name: string }[];
    stats?: {
        total: number;
        active: number;
        available: number;
        maintenance: number;
        trashed: number;
    };
}>();

const isStandalone = computed(() => !props.hotel);

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '#' }] }, () => page),
});

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const selectedUuids = ref<(string | number)[]>([]);
const basePath = computed(() =>
    props.hotel ? `/dashboard/hotels/${props.hotel.uuid}/rooms` : '/dashboard/hotel-rooms'
);

const hasActiveFilters = computed(() => !!(searchQuery.value || (statusFilter.value && statusFilter.value !== 'all')));

const columns = computed<TableColumn<Room>[]>(() => {
    const cols: TableColumn<Room>[] = [
        { key: 'name', label: 'Room', width: '25%' },
    ];
    if (isStandalone.value) {
        cols.push({ key: 'hotel', label: 'Hotel' });
    }
    cols.push(
        { key: 'room_type', label: 'Type' },
        { key: 'total_room', label: 'Qty', align: 'center' },
        { key: 'price', label: 'Price', align: 'right' },
        { key: 'capacity', label: 'Capacity', align: 'center' },
        { key: 'bed_type', label: 'Bed' },
        { key: 'is_available', label: 'Available' },
        { key: 'status', label: 'Status' },
    );
    return cols;
});

const tableActions = computed<TableAction<Room>[]>(() => {
    if (props.hotel) {
        return [
            { label: 'Edit', icon: Pencil, onClick: (item) => router.visit(`${basePath.value}/${item.uuid}/edit`) },
            { label: 'Delete', icon: Trash2, onClick: (item) => router.delete(`${basePath.value}/${item.uuid}`), variant: 'destructive', separator: true },
        ];
    }
    return [
        { label: 'Edit', icon: Pencil, onClick: (item) => router.visit(`/dashboard/hotels/${item.hotel?.uuid}/rooms/${item.uuid}/edit`) },
        { label: 'Delete', icon: Trash2, onClick: (item) => router.delete(`/dashboard/hotels/${item.hotel?.uuid}/rooms/${item.uuid}`), variant: 'destructive', separator: true },
    ];
});

const pagination = computed<PaginationData>(() => ({
    current_page: props.rooms.meta.current_page,
    last_page: props.rooms.meta.last_page,
    per_page: props.rooms.meta.per_page,
    total: props.rooms.meta.total,
}));

const formatCurrency = (value: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);

const applyFilters = () => {
    router.get(basePath.value, {
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    router.get(basePath.value, {}, { preserveState: true, preserveScroll: true });
};

const handlePageChange = (page: number) => {
    router.get(basePath.value, {
        page,
        per_page: pagination.value.per_page,
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get(basePath.value, {
        per_page: perPage,
        search: searchQuery.value || undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, { preserveState: true });
};
</script>

<template>
    <Head :title="hotel ? `${hotel.name} - Rooms` : 'All Rooms'" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link v-if="hotel" :href="`/dashboard/hotels/${hotel.uuid}`">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Rooms</h1>
                    <p class="text-muted-foreground">{{ hotel ? hotel.name : 'Manage all hotel rooms' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <ButtonGroup v-if="isStandalone">
                    <Button variant="default">
                        <Database class="mr-2 h-4 w-4" />
                        All
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/dashboard/hotel-rooms/trash">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Trash
                        </Link>
                    </Button>
                </ButtonGroup>
                <Button as-child>
                    <Link :href="hotel ? `${basePath}/create` : '/dashboard/hotel-rooms/create'">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Room
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Stats -->
        <div v-if="stats && isStandalone" class="grid gap-4 md:grid-cols-5">
            <StatsCard
                title="Total Rooms"
                :value="stats.total"
                :icon="BedDouble"
            />
            <StatsCard
                title="Active"
                :value="stats.active"
                :icon="CheckCircle"
                variant="success"
            />
            <StatsCard
                title="Available"
                :value="stats.available"
                :icon="BedDouble"
                variant="info"
            />
            <StatsCard
                title="Maintenance"
                :value="stats.maintenance"
                :icon="Wrench"
                variant="warning"
            />
            <StatsCard
                title="In Trash"
                :value="stats.trashed ?? 0"
                :icon="Trash2"
                variant="destructive"
            />
        </div>

        <!-- Filters -->
        <div class="flex items-center gap-4">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Search rooms..." class="pl-9" @keyup.enter="applyFilters" />
            </div>
            <Select v-model="statusFilter" @update:model-value="applyFilters">
                <SelectTrigger class="w-[150px]">
                    <SelectValue placeholder="Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Status</SelectItem>
                    <SelectItem v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</SelectItem>
                </SelectContent>
            </Select>
            <Button v-if="hasActiveFilters" variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground" @click="clearFilters">
                <X class="mr-1 h-4 w-4" />
                Clear Filters
            </Button>
        </div>

        <!-- Table -->
        <TableReusable
            :data="rooms.data"
            :columns="columns"
            :actions="tableActions"
            :pagination="pagination"
            :searchable="false"
            :selectable="true"
            select-key="uuid"
            v-model:selected="selectedUuids"
            @page-change="handlePageChange"
            @per-page-change="handlePerPageChange"
        >
            <template #cell-name="{ item }">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                        <BedDouble class="h-4 w-4 text-primary" />
                    </div>
                    <div>
                        <span class="font-medium">{{ item.name }}</span>
                        <p v-if="item.room_number" class="text-xs text-muted-foreground">#{{ item.room_number }}</p>
                    </div>
                </div>
            </template>
            <template #cell-hotel="{ item }">
                <Link v-if="item.hotel" :href="`/dashboard/hotels/${item.hotel.uuid}`" class="text-sm hover:underline">
                    {{ item.hotel.name }}
                </Link>
                <span v-else class="text-muted-foreground">-</span>
            </template>
            <template #cell-price="{ item }">
                <div>
                    <span class="font-medium">{{ formatCurrency(item.price) }}</span>
                    <span v-if="item.discount_price" class="ml-1 text-xs text-muted-foreground line-through">{{ formatCurrency(item.discount_price) }}</span>
                </div>
            </template>
            <template #cell-room_type="{ item }">
                <span class="capitalize">{{ item.room_type || '-' }}</span>
            </template>
            <template #cell-bed_type="{ item }">
                <span class="capitalize">{{ item.bed_type || '-' }}</span>
            </template>
            <template #cell-is_available="{ item }">
                <Badge :variant="item.is_available ? 'default' : 'secondary'">{{ item.is_available ? 'Yes' : 'No' }}</Badge>
            </template>
            <template #cell-status="{ item }">
                <Badge :variant="item.status === 'active' ? 'default' : item.status === 'maintenance' ? 'outline' : 'secondary'">{{ item.status }}</Badge>
            </template>
        </TableReusable>
    </div>
</template>
