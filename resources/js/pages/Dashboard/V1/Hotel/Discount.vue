<script setup lang="ts">
import { h, computed, watch, type VNode } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import AppLayout from '@/layouts/AppLayout.vue';
import { ModalForm } from '@/components/shared';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { DollarSign, Percent, Hotel as HotelIcon } from 'lucide-vue-next';
import type { Hotel } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Discount', href: '#' }] }, () => page),
});

const props = defineProps<{ hotel: Hotel }>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const form = useForm({
    discount_price: props.hotel.discount_price,
    discount_percentage: props.hotel.discount_percentage,
});

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency: props.hotel.currency || 'USD' }).format(value);

// Auto-calculate percentage when discount_price changes
watch(() => form.discount_price, (newDiscount) => {
    if (newDiscount && props.hotel.min_price && newDiscount < props.hotel.min_price) {
        form.discount_percentage = Math.round(((props.hotel.min_price - newDiscount) / props.hotel.min_price) * 100);
    } else if (!newDiscount) {
        form.discount_percentage = null;
    }
});

// Auto-calculate discount_price when percentage changes
watch(() => form.discount_percentage, (newPercentage) => {
    if (newPercentage && props.hotel.min_price) {
        form.discount_price = Math.round((props.hotel.min_price * (1 - newPercentage / 100)) * 100) / 100;
    } else if (!newPercentage) {
        form.discount_price = null;
    }
});

const handleSubmit = () => {
    form.patch(`/dashboard/hotels/${props.hotel.uuid}/discount`, {
        onSuccess: () => { close(); redirect(); },
    });
};

const handleClearDiscount = () => {
    form.discount_price = null;
    form.discount_percentage = null;
    form.patch(`/dashboard/hotels/${props.hotel.uuid}/discount`, {
        onSuccess: () => { close(); redirect(); },
    });
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Hotel Discount"
        :description="`Set discount for ${hotel.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Discount"
        :loading="form.processing"
        @submit="handleSubmit"
    >
        <div class="space-y-6">
            <!-- Hotel Info -->
            <div class="flex items-center gap-4 p-4 rounded-lg border bg-muted/30">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <HotelIcon class="h-6 w-6 text-primary" />
                </div>
                <div class="flex-1">
                    <p class="font-medium">{{ hotel.name }}</p>
                    <p class="text-sm text-muted-foreground">{{ hotel.city }}, {{ hotel.country }}</p>
                </div>
                <div class="text-right">
                    <p v-if="hotel.min_price !== null" class="text-sm font-medium">
                        {{ formatCurrency(hotel.min_price) }}
                        <span v-if="hotel.max_price && hotel.max_price !== hotel.min_price" class="text-muted-foreground">
                            - {{ formatCurrency(hotel.max_price) }}
                        </span>
                    </p>
                    <p class="text-xs text-muted-foreground">current price range</p>
                </div>
            </div>

            <!-- Discount Form -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-medium">Discount Settings</h3>
                    <p class="text-sm text-muted-foreground">Set a discount price or percentage</p>
                </div>
                <Separator />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="discount_price">
                            <span class="flex items-center gap-1">
                                <DollarSign class="h-3.5 w-3.5" />
                                Discount Price
                            </span>
                        </Label>
                        <Input
                            id="discount_price"
                            :model-value="form.discount_price ?? undefined"
                            @update:model-value="(v: any) => (form.discount_price = v !== '' ? Number(v) : null)"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                        />
                        <p v-if="form.errors.discount_price" class="text-sm text-destructive">{{ form.errors.discount_price }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="discount_percentage">
                            <span class="flex items-center gap-1">
                                <Percent class="h-3.5 w-3.5" />
                                Discount Percentage
                            </span>
                        </Label>
                        <Input
                            id="discount_percentage"
                            :model-value="form.discount_percentage ?? undefined"
                            @update:model-value="(v: any) => (form.discount_percentage = v !== '' ? Number(v) : null)"
                            type="number"
                            min="0"
                            max="100"
                            placeholder="0"
                        />
                        <p v-if="form.errors.discount_percentage" class="text-sm text-destructive">{{ form.errors.discount_percentage }}</p>
                    </div>
                </div>

                <!-- Preview -->
                <div v-if="form.discount_price && hotel.min_price" class="rounded-lg border p-4 bg-muted/30">
                    <p class="text-xs text-muted-foreground mb-2">Preview</p>
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-destructive">{{ formatCurrency(form.discount_price) }}</span>
                        <span class="text-sm text-muted-foreground line-through">{{ formatCurrency(hotel.min_price) }}</span>
                        <Badge v-if="form.discount_percentage" variant="destructive">-{{ form.discount_percentage }}%</Badge>
                    </div>
                </div>

                <!-- Clear discount -->
                <button
                    v-if="hotel.discount_price"
                    type="button"
                    class="text-sm text-destructive hover:underline"
                    @click="handleClearDiscount"
                >
                    Remove discount
                </button>
            </div>
        </div>
    </ModalForm>
</template>
