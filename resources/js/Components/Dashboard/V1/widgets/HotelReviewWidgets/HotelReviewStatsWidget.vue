<script setup lang="ts">
import { computed } from 'vue';
import { Star, TrendingUp, MessageSquare, Clock, CheckCircle, XCircle } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { StatsCard } from '@/components/shared';
import { useTranslation } from '@/composables/useTranslation';

const { __ } = useTranslation();

import type { HotelReviewStats } from '@hotel/types';

const props = defineProps<{
    stats: HotelReviewStats;
}>();

const formatNumber = (num: number) => {
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'k';
    }
    return num.toString();
};

const ratingDistribution = computed(() => {
    const total = props.stats.total || 1;
    return [
        { stars: 5, count: props.stats['5_star'], color: 'bg-emerald-500', percentage: (props.stats['5_star'] / total) * 100 },
        { stars: 4, count: props.stats['4_star'], color: 'bg-purple-400', percentage: (props.stats['4_star'] / total) * 100 },
        { stars: 3, count: props.stats['3_star'], color: 'bg-yellow-400', percentage: (props.stats['3_star'] / total) * 100 },
        { stars: 2, count: props.stats['2_star'], color: 'bg-sky-400', percentage: (props.stats['2_star'] / total) * 100 },
        { stars: 1, count: props.stats['1_star'], color: 'bg-orange-400', percentage: (props.stats['1_star'] / total) * 100 },
    ];
});

const renderStars = (rating: number) => {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    return { fullStars, hasHalfStar, emptyStars: 5 - fullStars - (hasHalfStar ? 1 : 0) };
};

const stars = computed(() => renderStars(props.stats.average_rating));
</script>

<template>
    <div class="grid gap-4 md:grid-cols-4 mb-6">
        <StatsCard
            :title="__('Total Reviews')"
            :value="stats.total"
            :icon="MessageSquare"
            variant="info"
        />
        <StatsCard
            :title="__('Active')"
            :value="stats.active"
            :icon="CheckCircle"
            variant="success"
        />
        <StatsCard
            :title="__('Inactive')"
            :value="stats.inactive"
            :icon="XCircle"
            variant="destructive"
        />
        <StatsCard
            :title="__('Needs Reply')"
            :value="stats.pending_reply"
            :icon="Clock"
            variant="warning"
        />
    </div>

    <Card>
        <CardContent class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Average Rating -->
                <div class="flex flex-col items-center justify-center space-y-3 border-r pr-8">
                    <p class="text-sm font-medium text-muted-foreground">{{ __('Overall Rating') }}</p>
                    <div class="text-6xl font-black text-foreground">{{ stats.average_rating.toFixed(1) }}</div>
                    <div class="flex text-yellow-400">
                        <Star v-for="i in stars.fullStars" :key="'full-' + i" class="h-6 w-6 fill-current" />
                        <Star v-if="stars.hasHalfStar" class="h-6 w-6 fill-current opacity-50" />
                        <Star v-for="i in stars.emptyStars" :key="'empty-' + i" class="h-6 w-6 text-gray-200 dark:text-gray-700" />
                    </div>
                    <p class="text-sm text-muted-foreground">
                        {{ __('Based on') }} {{ stats.total }} {{ __('reviews') }}
                    </p>
                </div>

                <!-- Rating Distribution -->
                <div class="space-y-3">
                    <div v-for="item in ratingDistribution" :key="item.stars" class="flex items-center gap-3 text-sm">
                        <div class="flex items-center gap-1 w-10 shrink-0">
                            <span class="font-bold">{{ item.stars }}</span>
                            <Star class="h-3 w-3 text-yellow-400 fill-yellow-400" />
                        </div>
                        <div class="flex-1 h-2.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            <div
                                :class="item.color"
                                class="h-full rounded-full transition-all duration-500"
                                :style="{ width: item.percentage + '%' }"
                            />
                        </div>
                        <span class="w-12 text-right text-muted-foreground tabular-nums">{{ formatNumber(item.count) }}</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
