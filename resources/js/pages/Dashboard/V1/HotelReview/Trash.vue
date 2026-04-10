<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { HotelReview, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { TableReusable, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { RotateCcw, Trash2, Star, Database, MessageSquare } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{ reviews: PaginatedResponse<HotelReview> }>();

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Reviews', href: '/dashboard/hotel-reviews' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);

const columns: TableColumn<HotelReview>[] = [
    { key: 'guest_name', label: 'Guest' },
    { key: 'hotel', label: 'Hotel' },
    { key: 'rating', label: 'Rating', align: 'center' },
    { key: 'status', label: 'Status' },
    { key: 'deleted_at', label: 'Deleted At' },
];

const actions: TableAction<HotelReview>[] = [
    { label: 'Restore', icon: RotateCcw, onClick: (item) => router.put(`/dashboard/hotel-reviews/${item.uuid}/restore`, {}, { preserveScroll: true, onSuccess: () => toast.success('Restored.') }) },
    { label: 'Delete Permanently', icon: Trash2, onClick: (item) => router.delete(`/dashboard/hotel-reviews/${item.uuid}/force-delete`, { preserveScroll: true, onSuccess: () => toast.success('Permanently deleted.') }), variant: 'destructive', separator: true },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.reviews.meta.current_page,
    last_page: props.reviews.meta.last_page,
    per_page: props.reviews.meta.per_page,
    total: props.reviews.meta.total,
}));

const handlePageChange = (page: number) => { router.get('/dashboard/hotel-reviews/trash', { page }, { preserveState: true }); };
const handlePerPageChange = (perPage: number) => { router.get('/dashboard/hotel-reviews/trash', { per_page: perPage }, { preserveState: true }); };

const getStatusVariant = (status: string) => {
    switch (status) { case 'approved': return 'default'; case 'rejected': return 'destructive'; default: return 'outline'; }
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Review Trash" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Review Trash</h1>
                <p class="text-muted-foreground">Deleted reviews</p>
            </div>
            <ButtonGroup>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/hotel-reviews">
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

        <TableReusable
            :data="reviews.data"
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
            <template #cell-guest_name="{ item }">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10">
                        <MessageSquare class="h-4 w-4 text-primary" />
                    </div>
                    <span class="font-medium">{{ item.guest_name || item.user?.name || 'Anonymous' }}</span>
                </div>
            </template>

            <template #cell-hotel="{ item }">
                <span v-if="item.hotel" class="text-sm">{{ item.hotel.name }}</span>
                <span v-else class="text-muted-foreground">-</span>
            </template>

            <template #cell-rating="{ item }">
                <div class="flex items-center gap-1">
                    <Star class="h-3.5 w-3.5 fill-yellow-400 text-yellow-400" />
                    <span class="text-sm">{{ item.rating }}</span>
                </div>
            </template>

            <template #cell-status="{ item }">
                <Badge :variant="getStatusVariant(item.status)">{{ item.status }}</Badge>
            </template>

            <template #cell-deleted_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.deleted_at) }}</span>
            </template>
        </TableReusable>
    </div>
</template>
