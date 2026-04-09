<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Hotel, HotelStats, HotelCategory, StatusOption, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import StatsCard from '@/components/shared/StatsCard/StatsCard.vue';
import TableReusable from '@/components/shared/TableReusable/TableReusable.vue';
import type { TableColumn, TableAction } from '@/components/shared/TableReusable/TableReusable.vue';
import { Plus, Eye, Pencil, Trash2, Star, Hotel as HotelIcon, Search, X, Archive } from 'lucide-vue-next';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }] }, () => page),
});

const props = defineProps<{
    hotels: PaginatedResponse<Hotel>;
    stats: HotelStats;
    filters: Record<string, string>;
    categories: HotelCategory[];
    statuses: StatusOption[];
}>();

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const cityFilter = ref(props.filters.city || '');
const selectedUuids = ref<(string | number)[]>([]);

const columns: TableColumn<Hotel>[] = [
    { key: 'name', label: 'Hotel', width: '25%' },
    { key: 'city', label: 'City' },
    { key: 'star_rating', label: 'Stars', align: 'center' },
    { key: 'price_per_night', label: 'Price/Night', align: 'right' },
    { key: 'status', label: 'Status' },
    { key: 'rooms_count', label: 'Rooms', align: 'center' },
    { key: 'created_at', label: 'Created' },
];

const tableActions: TableAction<Hotel>[] = [
    { label: 'View', icon: Eye, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}`) },
    { label: 'Edit', icon: Pencil, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}/edit`) },
    { label: 'Delete', icon: Trash2, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}/confirm-delete`), variant: 'destructive', separator: true },
];

const hasActiveFilters = computed(() => !!(searchQuery.value || statusFilter.value || cityFilter.value));

const applyFilters = () => {
    router.get('/dashboard/hotels', {
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
        city: cityFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    cityFilter.value = '';
    router.get('/dashboard/hotels', {}, { preserveState: true, preserveScroll: true });
};

const handlePageChange = (page: number) => {
    router.get('/dashboard/hotels', { ...props.filters, page }, { preserveState: true, preserveScroll: true });
};

const formatCurrency = (value: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
const formatDate = (date: string) => new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'active': return 'default';
        case 'inactive': return 'secondary';
        case 'draft': return 'outline';
        default: return 'outline';
    }
};
</script>

<template>
    

    <Head title="Hotels" />

     <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Hotels</h1>
                <p class="text-muted-foreground">Manage your hotel properties</p>
            </div>
            <div class="flex gap-2">
                <Link href="/dashboard/hotels/trash">
                    <Button variant="outline" size="sm">
                        <Archive class="mr-2 h-4 w-4" />
                        Trash
                    </Button>
                </Link>
                <Link href="/dashboard/hotels/create">
                    <Button size="sm">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Hotel
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatsCard title="Total Hotels" :value="stats.total" :icon="HotelIcon" />
            <StatsCard title="Active" :value="stats.active" :icon="HotelIcon" variant="success" />
            <StatsCard title="Featured" :value="stats.featured" :icon="Star" variant="info" />
            <StatsCard title="Trashed" :value="stats.trashed" :icon="Trash2" variant="danger" />
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Search hotels..." class="pl-9" @keyup.enter="applyFilters" />
            </div>
            <Select v-model="statusFilter" @update:model-value="applyFilters">
                <SelectTrigger class="w-[140px]">
                    <SelectValue placeholder="Status" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="">All</SelectItem>
                    <SelectItem v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</SelectItem>
                </SelectContent>
            </Select>
            <Button v-if="hasActiveFilters" variant="ghost" size="sm" @click="clearFilters">
                <X class="mr-1 h-4 w-4" />
                Clear
            </Button>
        </div>

        <!-- Table -->
        <TableReusable
            v-model:selected="selectedUuids"
            :data="hotels.data"
            :columns="columns"
            :actions="tableActions"
            :pagination="hotels.meta"
            :selectable="true"
            select-key="uuid"
            @page-change="handlePageChange"
        >
            <template #cell-name="{ item }">
                <div class="flex items-center gap-3">
                    <div v-if="item.featured_image" class="h-10 w-10 overflow-hidden rounded-md bg-muted">
                        <img :src="item.featured_image" :alt="item.name" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <Link :href="`/dashboard/hotels/${item.uuid}`" class="font-medium hover:underline">
                            {{ item.name }}
                        </Link>
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

            <template #cell-price_per_night="{ item }">
                <div>
                    <span class="font-medium">{{ formatCurrency(item.effective_price) }}</span>
                    <span v-if="item.is_on_sale" class="ml-1 text-xs text-muted-foreground line-through">{{ formatCurrency(item.price_per_night) }}</span>
                </div>
            </template>

            <template #cell-status="{ item }">
                <Badge :variant="getStatusVariant(item.status)">{{ item.status }}</Badge>
            </template>

            <template #cell-created_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.created_at) }}</span>
            </template>

            <template #bulk-actions>
                <Button v-if="selectedUuids.length" variant="destructive" size="sm" @click="router.visit(`/dashboard/hotels/bulk-delete?${selectedUuids.map(u => `uuids[]=${u}`).join('&')}`)">
                    Delete Selected ({{ selectedUuids.length }})
                </Button>
            </template>
        </TableReusable>
    </div>

</template>
