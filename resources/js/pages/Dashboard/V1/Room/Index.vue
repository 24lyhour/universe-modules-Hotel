<script setup lang="ts">
import { h, ref, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Room, StatusOption, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import TableReusable from '@/components/shared/TableReusable/TableReusable.vue';
import type { TableColumn, TableAction } from '@/components/shared/TableReusable/TableReusable.vue';
import { Plus, Pencil, Trash2, Search, ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string };
    rooms: PaginatedResponse<Room>;
    filters: Record<string, string>;
    statuses: StatusOption[];
}>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '#' }] }, () => page),
});

const searchQuery = ref(props.filters.search || '');
const basePath = `/dashboard/hotels/${props.hotel.uuid}/rooms`;

const columns: TableColumn<Room>[] = [
    { key: 'name', label: 'Room', width: '25%' },
    { key: 'room_type', label: 'Type' },
    { key: 'total_room', label: 'Qty', align: 'center' },
    { key: 'price', label: 'Price', align: 'right' },
    { key: 'capacity', label: 'Capacity', align: 'center' },
    { key: 'bed_type', label: 'Bed' },
    { key: 'is_available', label: 'Available' },
    { key: 'status', label: 'Status' },
];

const tableActions: TableAction<Room>[] = [
    { label: 'Edit', icon: Pencil, onClick: (item) => router.visit(`${basePath}/${item.uuid}/edit`) },
    { label: 'Delete', icon: Trash2, onClick: (item) => router.delete(`${basePath}/${item.uuid}`), variant: 'destructive', separator: true },
];

const formatCurrency = (value: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);

const applyFilters = () => {
    router.get(basePath, { search: searchQuery.value || undefined }, { preserveState: true, preserveScroll: true });
};

const handlePageChange = (page: number) => {
    router.get(basePath, { ...props.filters, page }, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head :title="`${hotel.name} - Rooms`" />
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="`/dashboard/hotels/${hotel.uuid}`">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Rooms</h1>
                    <p class="text-muted-foreground">{{ hotel.name }}</p>
                </div>
            </div>
            <Link :href="`${basePath}/create`">
                <Button size="sm"><Plus class="mr-2 h-4 w-4" />Add Room</Button>
            </Link>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Search rooms..." class="pl-9" @keyup.enter="applyFilters" />
            </div>
        </div>

        <TableReusable :data="rooms.data" :columns="columns" :actions="tableActions" :pagination="rooms.meta" @page-change="handlePageChange">
            <template #cell-price="{ item }">
                <span class="font-medium">{{ formatCurrency(item.price) }}</span>
                <span v-if="item.discount_price" class="ml-1 text-xs text-muted-foreground line-through">{{ formatCurrency(item.discount_price) }}</span>
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
