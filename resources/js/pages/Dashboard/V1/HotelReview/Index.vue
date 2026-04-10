<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { HotelReview, HotelReviewStats, PaginatedResponse } from '@hotel/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ButtonGroup, Pagination } from '@/components/shared';
import { Search, X, Database, Trash2, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { HotelReviewStatsWidget, HotelReviewCardWidget } from '@hotel/Components/Dashboard/V1/widgets/HotelReviewWidgets';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Reviews', href: '/dashboard/hotel-reviews' }] }, () => page),
});

const props = defineProps<{
    reviews: PaginatedResponse<HotelReview>;
    filters: Record<string, string>;
    stats: HotelReviewStats;
    hotels: { id: number; uuid: string; name: string }[];
}>();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const ratingFilter = ref(props.filters.rating || 'all');
const hotelFilter = ref(props.filters.hotel || 'all');
const perPage = ref(props.reviews.meta.per_page || 10);

const hasActiveFilters = computed(() => !!(
    search.value ||
    (statusFilter.value && statusFilter.value !== 'all') ||
    (ratingFilter.value && ratingFilter.value !== 'all') ||
    (hotelFilter.value && hotelFilter.value !== 'all')
));

const pagination = computed(() => ({
    current_page: props.reviews.meta.current_page,
    last_page: props.reviews.meta.last_page,
    per_page: props.reviews.meta.per_page,
    total: props.reviews.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    rating: ratingFilter.value !== 'all' ? ratingFilter.value : undefined,
    hotel: hotelFilter.value !== 'all' ? hotelFilter.value : undefined,
    per_page: perPage.value,
});

const applyFilters = (overrides = {}) => {
    router.get('/dashboard/hotel-reviews', { ...getFilterParams(), ...overrides }, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    search.value = '';
    statusFilter.value = 'all';
    ratingFilter.value = 'all';
    hotelFilter.value = 'all';
    applyFilters({ page: 1 });
};

const handlePageChange = (page: number) => applyFilters({ page });
const handlePerPageChange = (val: any) => {
    perPage.value = Number(val);
    applyFilters({ page: 1 });
};

const handleStatusUpdate = (review: HotelReview, status: string) => {
    router.patch(`/dashboard/hotel-reviews/${review.uuid}/status`, { status }, {
        preserveScroll: true,
        onSuccess: () => toast.success(`Review ${status}`),
    });
};

const handleView = (review: HotelReview) => router.visit(`/dashboard/hotel-reviews/${review.uuid}`);
const handleReply = (review: HotelReview) => router.visit(`/dashboard/hotel-reviews/${review.uuid}`);
const handleDelete = (review: HotelReview) => router.visit(`/dashboard/hotel-reviews/${review.uuid}/delete`);

</script>

<template>
    <Head title="Hotel Reviews" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Hotel Reviews</h1>
                <p class="text-muted-foreground">Monitor and manage customer feedback across your hotels</p>
            </div>
            <ButtonGroup>
                <Button variant="default" size="sm" class="h-9">
                    <Database class="mr-2 h-4 w-4" />
                    All Reviews
                </Button>
                <Button variant="outline" size="sm" class="h-9" as-child>
                    <Link href="/dashboard/hotel-reviews/trash">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Archive
                    </Link>
                </Button>
            </ButtonGroup>
        </div>

        <!-- Stats Section -->
        <HotelReviewStatsWidget :stats="stats" />

        <!-- Filter Bar -->
        <div class="bg-card border rounded-xl p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative flex-1 min-w-[240px]">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Search by guest, comment or hotel..." class="pl-9 h-10 border-muted-foreground/20 focus-visible:ring-primary" @keyup.enter="applyFilters({ page: 1 })" />
                </div>
                
                <div class="flex items-center gap-2">
                    <Select v-model="statusFilter" @update:model-value="applyFilters({ page: 1 })">
                        <SelectTrigger class="w-[140px] h-10 border-muted-foreground/20">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            <SelectItem value="pending">Pending</SelectItem>
                            <SelectItem value="approved">Approved</SelectItem>
                            <SelectItem value="rejected">Rejected</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="ratingFilter" @update:model-value="applyFilters({ page: 1 })">
                        <SelectTrigger class="w-[130px] h-10 border-muted-foreground/20">
                            <SelectValue placeholder="Rating" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Ratings</SelectItem>
                            <SelectItem v-for="n in 5" :key="n" :value="n.toString()">{{ n }} Star{{ n > 1 ? 's' : '' }}</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="hotelFilter" @update:model-value="applyFilters({ page: 1 })">
                        <SelectTrigger class="w-[180px] h-10 border-muted-foreground/20">
                            <SelectValue placeholder="All Hotels" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Hotels</SelectItem>
                            <SelectItem v-for="h in hotels" :key="h.id" :value="h.id.toString()">{{ h.name }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <Button v-if="hasActiveFilters" variant="ghost" size="sm" class="text-muted-foreground hover:text-foreground h-10" @click="clearFilters">
                    <X class="mr-1 h-4 w-4" />
                    Reset
                </Button>
            </div>
        </div>

        <!-- Content List -->
        <div class="space-y-4">
            <template v-if="reviews.data.length > 0">
                <HotelReviewCardWidget 
                    v-for="review in reviews.data" 
                    :key="review.uuid" 
                    :review="review"
                    @view="handleView"
                    @reply="handleReply"
                    @approve="handleStatusUpdate(review, 'approved')"
                    @reject="handleStatusUpdate(review, 'rejected')"
                    @delete="handleDelete"
                />
            </template>
            <div v-else class="flex flex-col items-center justify-center py-20 bg-card border rounded-xl border-dashed">
                <Database class="h-12 w-12 text-muted-foreground/20 mb-4" />
                <h3 class="text-lg font-medium">No reviews found</h3>
                <p class="text-sm text-muted-foreground max-w-[300px] text-center mb-6">
                    Try adjusting your filters or search terms to find what you're looking for.
                </p>
                <Button v-if="hasActiveFilters" variant="outline" @click="clearFilters">Clear all filters</Button>
            </div>
        </div>

        <!-- Pagination Bar -->
        <div v-if="reviews.data.length > 0" class="flex flex-wrap items-center justify-between gap-4 border-t pt-6 mt-2">
            <div class="flex items-center gap-4 text-sm text-muted-foreground font-medium">
                <div class="flex items-center gap-2">
                    <span>Show</span>
                    <Select :model-value="perPage.toString()" @update:model-value="handlePerPageChange">
                        <SelectTrigger class="w-[80px] h-8 border-muted-foreground/20">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="10">10</SelectItem>
                            <SelectItem value="25">25</SelectItem>
                            <SelectItem value="50">50</SelectItem>
                        </SelectContent>
                    </Select>
                    <span>per page</span>
                </div>
                <Separator orientation="vertical" class="h-4" />
                <span>Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} - {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of {{ pagination.total }}</span>
            </div>

            <Pagination
                v-if="pagination.last_page > 1"
                :current-page="pagination.current_page"
                :last-page="pagination.last_page"
                @page-change="handlePageChange"
            />
        </div>
    </div>
</template>

