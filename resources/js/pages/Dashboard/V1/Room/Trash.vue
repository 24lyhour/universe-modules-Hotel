<script setup lang="ts">
import { h, ref, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Room, PaginatedResponse } from '../../../../types';
import { Button } from '@/components/ui/button';
import { TableReusable, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { RotateCcw, Trash2, BedDouble, Database, ArrowLeft } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string } | null;
    rooms: PaginatedResponse<Room>;
}>();

const isStandalone = computed(() => !props.hotel);

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '/dashboard/hotel-rooms' }, { title: 'Trash', href: '#' }] }, () => page),
});

const selectedUuids = ref<(string | number)[]>([]);

const basePath = computed(() =>
    props.hotel ? `/dashboard/hotels/${props.hotel.uuid}/rooms` : '/dashboard/hotel-rooms'
);

const trashPath = computed(() => isStandalone.value ? '/dashboard/hotel-rooms/trash' : `${basePath.value}/trash`);

const columns = computed<TableColumn<Room>[]>(() => {
    const cols: TableColumn<Room>[] = [
        { key: 'name', label: 'Room' },
    ];
    if (isStandalone.value) {
        cols.push({ key: 'hotel', label: 'Hotel' });
    }
    cols.push(
        { key: 'room_type', label: 'Type' },
        { key: 'price', label: 'Price', align: 'right' },
        { key: 'deleted_at', label: 'Deleted At' },
    );
    return cols;
});

const actions = computed<TableAction<Room>[]>(() => [
    {
        label: 'Restore',
        icon: RotateCcw,
        onClick: (room) => {
            const restorePath = isStandalone.value
                ? `/dashboard/hotel-rooms/${room.uuid}/restore`
                : `${basePath.value}/${room.uuid}/restore`;
            router.put(restorePath, {}, {
                preserveScroll: true,
                onSuccess: () => toast.success(`"${room.name}" restored.`),
            });
        },
    },
    {
        label: 'Delete Permanently',
        icon: Trash2,
        onClick: (room) => {
            const deletePath = isStandalone.value
                ? `/dashboard/hotel-rooms/${room.uuid}/force-delete`
                : `${basePath.value}/${room.uuid}/force-delete`;
            router.delete(deletePath, {
                preserveScroll: true,
                onSuccess: () => toast.success(`"${room.name}" permanently deleted.`),
            });
        },
        variant: 'destructive',
        separator: true,
    },
]);

const pagination = computed<PaginationData>(() => ({
    current_page: props.rooms.meta.current_page,
    last_page: props.rooms.meta.last_page,
    per_page: props.rooms.meta.per_page,
    total: props.rooms.meta.total,
}));

const handlePageChange = (page: number) => {
    router.get(trashPath.value, { page }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get(trashPath.value, { per_page: perPage }, { preserveState: true });
};

const handleBulkRestore = () => {
    router.put(`${basePath.value}/bulk-restore`, { uuids: selectedUuids.value }, {
        onSuccess: () => {
            toast.success(`${selectedUuids.value.length} rooms restored.`);
            selectedUuids.value = [];
        },
    });
};

const formatCurrency = (value: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head :title="hotel ? `${hotel.name} - Room Trash` : 'Room Trash'" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link v-if="hotel" :href="basePath">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Room Trash</h1>
                    <p class="text-muted-foreground">{{ hotel ? hotel.name : 'Deleted rooms' }}</p>
                </div>
            </div>
            <div v-if="isStandalone" class="flex items-center gap-2">
                <ButtonGroup>
                    <Button variant="outline" as-child>
                        <Link href="/dashboard/hotel-rooms">
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
        </div>

        <!-- Table -->
        <TableReusable
            :data="rooms.data"
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
                <Button v-if="selectedUuids.length > 0" variant="outline" size="sm" @click="handleBulkRestore">
                    <RotateCcw class="mr-2 h-4 w-4" />
                    Restore {{ selectedUuids.length }} Selected
                </Button>
            </template>

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
                <span v-if="item.hotel" class="text-sm">{{ item.hotel.name }}</span>
                <span v-else class="text-muted-foreground">-</span>
            </template>

            <template #cell-room_type="{ item }">
                <span class="capitalize">{{ item.room_type || '-' }}</span>
            </template>

            <template #cell-price="{ item }">
                <span class="font-medium">{{ formatCurrency(item.price) }}</span>
            </template>

            <template #cell-deleted_at="{ item }">
                <span class="text-sm text-muted-foreground">{{ formatDate(item.deleted_at) }}</span>
            </template>
        </TableReusable>
    </div>
</template>
