<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { HotelCategory, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import TableReusable from '@/components/shared/TableReusable/TableReusable.vue';
import type { TableColumn, TableAction } from '@/components/shared/TableReusable/TableReusable.vue';
import { Plus, Pencil, Trash2, Search, X, Archive } from 'lucide-vue-next';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotel Categories', href: '/dashboard/hotel-categories' }] }, () => page),
});

const props = defineProps<{
    categories: PaginatedResponse<HotelCategory>;
    filters: Record<string, string>;
}>();

const searchQuery = ref(props.filters.search || '');

const columns: TableColumn<HotelCategory>[] = [
    { key: 'name', label: 'Name', width: '30%' },
    { key: 'icon', label: 'Icon' },
    { key: 'group', label: 'Group' },
    { key: 'hotels_count', label: 'Hotels', align: 'center' },
    { key: 'is_active', label: 'Status' },
    { key: 'sort_order', label: 'Order', align: 'center' },
];

const tableActions: TableAction<HotelCategory>[] = [
    { label: 'Edit', icon: Pencil, onClick: (item) => router.visit(`/dashboard/hotel-categories/${item.uuid}/edit`) },
    { label: 'Delete', icon: Trash2, onClick: (item) => router.delete(`/dashboard/hotel-categories/${item.uuid}`), variant: 'destructive', separator: true },
];

const applyFilters = () => {
    router.get('/dashboard/hotel-categories', { search: searchQuery.value || undefined }, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => { searchQuery.value = ''; router.get('/dashboard/hotel-categories', {}, { preserveState: true, preserveScroll: true }); };

const handlePageChange = (page: number) => {
    router.get('/dashboard/hotel-categories', { ...props.filters, page }, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head title="Hotel Categories" />
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Hotel Categories</h1>
                <p class="text-muted-foreground">Manage hotel property types</p>
            </div>
            <div class="flex gap-2">
                <Link href="/dashboard/hotel-categories/trash">
                    <Button variant="outline" size="sm"><Archive class="mr-2 h-4 w-4" />Trash</Button>
                </Link>
                <Link href="/dashboard/hotel-categories/create">
                    <Button size="sm"><Plus class="mr-2 h-4 w-4" />Add Category</Button>
                </Link>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Search categories..." class="pl-9" @keyup.enter="applyFilters" />
            </div>
            <Button v-if="searchQuery" variant="ghost" size="sm" @click="clearFilters"><X class="mr-1 h-4 w-4" />Clear</Button>
        </div>

        <TableReusable :data="categories.data" :columns="columns" :actions="tableActions" :pagination="categories.meta" @page-change="handlePageChange">
            <template #cell-is_active="{ item }">
                <Badge :variant="item.is_active ? 'default' : 'secondary'">{{ item.is_active ? 'Active' : 'Inactive' }}</Badge>
            </template>
            <template #cell-icon="{ item }">
                <span class="text-muted-foreground">{{ item.icon || '-' }}</span>
            </template>
            <template #cell-group="{ item }">
                <span class="text-muted-foreground">{{ item.group || '-' }}</span>
            </template>
        </TableReusable>
    </div>
</template>
