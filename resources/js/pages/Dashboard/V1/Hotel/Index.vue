<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Hotel, HotelStats, HotelCategory, Province, StatusOption, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Switch } from '@/components/ui/switch';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { TableReusable, StatsCard, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Plus, Eye, Pencil, Trash2, Star, Hotel as HotelIcon, Search, X, Database, CheckCircle, DollarSign } from 'lucide-vue-next';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }] }, () => page),
});

const props = defineProps<{
    hotels: PaginatedResponse<Hotel>;
    stats: HotelStats;
    filters: Record<string, string>;
    categories: HotelCategory[];
    provinces: Province[];
    statuses: StatusOption[];
}>();

const searchQuery = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const provinceFilter = ref(props.filters.province || 'all');
const categoryFilter = ref(props.filters.category || 'all');
const selectedUuids = ref<(string | number)[]>([]);

const columns: TableColumn<Hotel>[] = [
    { key: 'name', label: 'Hotel', width: '25%' },
    { key: 'city', label: 'City' },
    { key: 'star_rating', label: 'Stars', align: 'center' },
    { key: 'min_price', label: 'Price/Night', align: 'right' },
    { key: 'status', label: 'Status' },
    { key: 'is_featured', label: 'Featured' },
    { key: 'rooms_count', label: 'Rooms', align: 'center' },
    { key: 'created_at', label: 'Created' },
];

const tableActions: TableAction<Hotel>[] = [
    { label: 'View', icon: Eye, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}`) },
    { label: 'Edit', icon: Pencil, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}/edit`) },
    { label: 'Discount', icon: DollarSign, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}/discount`), separator: true },
    { label: 'Delete', icon: Trash2, onClick: (item) => router.visit(`/dashboard/hotels/${item.uuid}/confirm-delete`), variant: 'destructive' },
];

const hasActiveFilters = computed(() => !!(
    searchQuery.value ||
    (statusFilter.value && statusFilter.value !== 'all') ||
    (provinceFilter.value && provinceFilter.value !== 'all') ||
    (categoryFilter.value && categoryFilter.value !== 'all')
));

const pagination = computed<PaginationData>(() => ({
    current_page: props.hotels.meta.current_page,
    last_page: props.hotels.meta.last_page,
    per_page: props.hotels.meta.per_page,
    total: props.hotels.meta.total,
}));

const getFilterParams = () => ({
    search: searchQuery.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    province: provinceFilter.value !== 'all' ? provinceFilter.value : undefined,
    category: categoryFilter.value !== 'all' ? categoryFilter.value : undefined,
});

const applyFilters = () => {
    router.get('/dashboard/hotels', getFilterParams(), { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    provinceFilter.value = 'all';
    categoryFilter.value = 'all';
    router.get('/dashboard/hotels', {}, { preserveState: true, preserveScroll: true });
};

const handlePageChange = (page: number) => {
    router.get('/dashboard/hotels', { ...getFilterParams(), page, per_page: pagination.value.per_page }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/hotels', { ...getFilterParams(), per_page: perPage }, { preserveState: true });
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
            <div class="flex items-center gap-2">
                <ButtonGroup>
                    <Button variant="default">
                        <Database class="mr-2 h-4 w-4" />
                        All
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/dashboard/hotels/trash">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Trash
                        </Link>
                    </Button>
                </ButtonGroup>
                <Button as-child>
                    <Link href="/dashboard/hotels/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Hotel
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 md:grid-cols-4">
            <StatsCard title="Total Hotels" :value="stats.total" :icon="HotelIcon" />
            <StatsCard title="Active" :value="stats.active" :icon="CheckCircle" variant="success" />
            <StatsCard title="Featured" :value="stats.featured" :icon="Star" variant="info" />
            <StatsCard title="In Trash" :value="stats.trashed" :icon="Trash2" variant="destructive" />
        </div>

        <!-- Filters -->
        <div class="flex items-center gap-4">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Search hotels..." class="pl-9" @keyup.enter="applyFilters" />
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
            <Select v-model="provinceFilter" @update:model-value="applyFilters">
                <SelectTrigger class="w-[170px]">
                    <SelectValue placeholder="Province" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Provinces</SelectItem>
                    <SelectItem v-for="p in provinces" :key="p.id" :value="p.id.toString()">{{ p.name }}</SelectItem>
                </SelectContent>
            </Select>
            <Select v-model="categoryFilter" @update:model-value="applyFilters">
                <SelectTrigger class="w-[170px]">
                    <SelectValue placeholder="Category" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All Categories</SelectItem>
                    <SelectItem v-for="c in categories" :key="c.id" :value="c.id.toString()">{{ c.name }}</SelectItem>
                </SelectContent>
            </Select>
            <Button v-if="hasActiveFilters" variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground" @click="clearFilters">
                <X class="mr-1 h-4 w-4" />
                Clear Filters
            </Button>
        </div>

        <!-- Table -->
        <TableReusable
            :data="hotels.data"
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
            <template #bulk-actions>
                <Button v-if="selectedUuids.length > 0" variant="destructive" size="sm" @click="router.visit(`/dashboard/hotels/bulk-delete?${selectedUuids.map(u => `uuids[]=${u}`).join('&')}`)">
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete {{ selectedUuids.length }} Selected
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

            <template #cell-min_price="{ item }">
                <div v-if="item.min_price !== null">
                    <span class="font-medium">{{ formatCurrency(item.min_price) }}</span>
                    <span v-if="item.max_price && item.max_price !== item.min_price" class="text-xs text-muted-foreground"> - {{ formatCurrency(item.max_price) }}</span>
                </div>
                <span v-else class="text-muted-foreground text-sm">No rooms</span>
            </template>

            <template #cell-status="{ item }">
                <Badge :variant="getStatusVariant(item.status)">{{ item.status }}</Badge>
            </template>

            <template #cell-is_featured="{ item }">
                <div class="flex items-center gap-2" @click.stop>
                    <Switch
                        :model-value="item.is_featured"
                        @update:model-value="router.patch(`/dashboard/hotels/${item.uuid}/toggle-featured`, {}, { preserveState: true, preserveScroll: true })"
                    />
                    <span class="text-sm text-muted-foreground">
                        {{ item.is_featured ? 'Yes' : 'No' }}
                    </span>
                </div>
            </template>

            <template #cell-created_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.created_at) }}</span>
            </template>
        </TableReusable>
    </div>
</template>
