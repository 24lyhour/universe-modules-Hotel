<script setup lang="ts">
import { computed } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Link } from '@inertiajs/vue3';
import {
    ChartContainer,
    ChartCrosshair,
    ChartTooltip,
} from '@/components/ui/chart';
import { Donut } from '@unovis/ts';
import {
    VisXYContainer,
    VisGroupedBar,
    VisAxis,
    VisSingleContainer,
    VisDonut,
} from '@unovis/vue';
import {
    Hotel,
    BedDouble,
    Star,
    MapPin,
    TrendingUp,
    Building2,
} from 'lucide-vue-next';
import type { ChartConfig } from '@/components/ui/chart';

export interface HotelWidgetStats {
    total: number;
    active: number;
    inactive: number;
    featured: number;
    total_rooms: number;
    available_rooms: number;
    categories: number;
}

export interface RecentHotel {
    id: number;
    uuid: string;
    name: string;
    city: string;
    star_rating: number;
    price_per_night: number;
    status: string;
    is_featured: boolean;
    category: string | null;
    created_at: string;
}

export interface CityDistribution {
    city: string;
    count: number;
}

export interface HotelWidgetProps {
    stats: HotelWidgetStats;
    recentHotels: RecentHotel[];
    citiesDistribution: CityDistribution[];
    loading?: boolean;
}

const props = withDefaults(defineProps<HotelWidgetProps>(), {
    loading: false,
});

const formatCurrency = (num: number) =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 }).format(num);

const formatNumber = (num: number) => new Intl.NumberFormat().format(num);

const formatPercent = (num: number) => `${num.toFixed(1)}%`;

const activePercent = computed(() =>
    props.stats.total > 0 ? ((props.stats.active / props.stats.total) * 100).toFixed(1) : '0',
);

const roomAvailPercent = computed(() =>
    props.stats.total_rooms > 0 ? ((props.stats.available_rooms / props.stats.total_rooms) * 100).toFixed(1) : '0',
);

const getStatusVariant = (status: string): 'default' | 'secondary' | 'outline' => {
    switch (status) {
        case 'active': return 'default';
        case 'inactive': return 'secondary';
        default: return 'outline';
    }
};

// Donut chart config
const statusChartConfig: ChartConfig = {
    active: { label: 'Active', color: 'var(--chart-1)' },
    inactive: { label: 'Inactive', color: 'var(--chart-3)' },
    featured: { label: 'Featured', color: 'var(--chart-2)' },
};

const donutData = computed(() => [
    { status: 'active', label: 'Active', value: props.stats.active - props.stats.featured, fill: 'var(--color-active)' },
    { status: 'featured', label: 'Featured', value: props.stats.featured, fill: 'var(--color-featured)' },
    { status: 'inactive', label: 'Inactive', value: props.stats.inactive, fill: 'var(--color-inactive)' },
]);

// City bar chart config
const cityChartConfig: ChartConfig = {
    count: { label: 'Hotels', color: 'var(--chart-1)' },
};
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold tracking-tight">Hotel Overview</h2>
            <p class="text-sm text-muted-foreground">Property management at a glance</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Total Hotels</CardTitle>
                    <Hotel class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.total }}</div>
                    <p class="text-xs text-muted-foreground">{{ activePercent }}% active</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Active Hotels</CardTitle>
                    <TrendingUp class="h-4 w-4 text-green-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-green-600">{{ stats.active }}</div>
                    <p class="text-xs text-muted-foreground">{{ stats.inactive }} inactive</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Total Rooms</CardTitle>
                    <BedDouble class="h-4 w-4 text-blue-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-blue-600">{{ stats.total_rooms }}</div>
                    <p class="text-xs text-muted-foreground">{{ roomAvailPercent }}% available</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Featured</CardTitle>
                    <Star class="h-4 w-4 text-yellow-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-yellow-600">{{ stats.featured }}</div>
                    <p class="text-xs text-muted-foreground">{{ stats.categories }} categories</p>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Hotel Status Donut Chart -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Hotel class="h-5 w-5" />
                        Hotel Status Distribution
                    </CardTitle>
                    <CardDescription>Breakdown by hotel status</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-6 lg:grid-cols-2">
                        <ChartContainer
                            :config="statusChartConfig"
                            class="h-[200px]"
                            :style="{
                                '--vis-donut-central-label-font-size': 'var(--text-2xl)',
                                '--vis-donut-central-label-font-weight': 'var(--font-weight-bold)',
                                '--vis-donut-central-label-text-color': 'var(--foreground)',
                                '--vis-donut-central-sub-label-text-color': 'var(--muted-foreground)',
                            }"
                        >
                            <VisSingleContainer :data="donutData" :margin="{ top: 10, bottom: 10 }">
                                <VisDonut
                                    :value="(d: any) => d.value"
                                    :color="(d: any) => statusChartConfig[d.status as keyof typeof statusChartConfig]?.color"
                                    :arc-width="40"
                                    :pad-angle="0.02"
                                    :corner-radius="4"
                                    :central-label="stats.total.toLocaleString()"
                                    central-sub-label="Hotels"
                                />
                                <ChartTooltip
                                    :triggers="{
                                        [Donut.selectors.segment]: (d: any) => `<div class='border-border/50 bg-background min-w-32 rounded-lg border px-2.5 py-1.5 text-xs shadow-xl'><div class='flex items-center gap-2'><span class='h-2 w-2 rounded-full' style='background-color: ${statusChartConfig[d.status as keyof typeof statusChartConfig]?.color}'></span><span class='font-medium'>${d.label}</span></div><div class='text-muted-foreground'>${d.value.toLocaleString()} hotels</div></div>`,
                                    }"
                                />
                            </VisSingleContainer>
                        </ChartContainer>

                        <!-- Legend -->
                        <div class="flex flex-col justify-center space-y-3">
                            <div
                                v-for="(item, index) in donutData"
                                :key="item.label"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="h-3 w-3 rounded-full"
                                        :class="{
                                            'bg-chart-1': index === 0,
                                            'bg-chart-2': index === 1,
                                            'bg-chart-3': index === 2,
                                        }"
                                    ></span>
                                    <span class="text-sm">{{ item.label }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ formatNumber(item.value) }}</span>
                                    <Badge variant="outline" class="text-xs">
                                        {{ formatPercent((item.value / stats.total) * 100) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Cities Distribution Bar Chart -->
            <Card v-if="citiesDistribution.length">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MapPin class="h-5 w-5" />
                        Hotels by City
                    </CardTitle>
                    <CardDescription>Top cities with hotels</CardDescription>
                </CardHeader>
                <CardContent>
                    <ChartContainer :config="cityChartConfig" class="h-[200px]" cursor>
                        <VisXYContainer :data="citiesDistribution" :margin="{ left: -24 }" :y-domain="[0, undefined]">
                            <VisGroupedBar
                                :x="(_: CityDistribution, i: number) => i"
                                :y="(d: CityDistribution) => d.count"
                                :color="cityChartConfig.count.color"
                                :bar-padding="0.1"
                                :rounded-corners="4"
                            />
                            <VisAxis
                                type="x"
                                :tick-line="false"
                                :domain-line="false"
                                :grid-line="false"
                                :tick-format="(i: number) => citiesDistribution[i]?.city || ''"
                            />
                            <VisAxis
                                type="y"
                                :num-ticks="3"
                                :tick-line="false"
                                :domain-line="false"
                            />
                            <ChartTooltip />
                            <ChartCrosshair
                                :template="(d: CityDistribution) => `<div class='border-border/50 bg-background min-w-32 rounded-lg border px-2.5 py-1.5 text-xs shadow-xl'><div class='font-medium'>${d.city}</div><div class='text-muted-foreground'>${d.count.toLocaleString()} hotels</div></div>`"
                                color="#0000"
                            />
                        </VisXYContainer>
                    </ChartContainer>
                </CardContent>
            </Card>
        </div>

        <!-- Recent Hotels -->
        <Card v-if="recentHotels.length">
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Building2 class="h-5 w-5" />
                    Recent Hotels
                </CardTitle>
                <CardDescription>Latest hotel additions</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <Link
                        v-for="hotel in recentHotels"
                        :key="hotel.id"
                        :href="`/dashboard/hotels/${hotel.uuid}`"
                        class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ hotel.name }}</span>
                                <Badge v-if="hotel.is_featured" variant="default" class="text-xs">Featured</Badge>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                <span class="flex items-center gap-1">
                                    <MapPin class="h-3 w-3" />{{ hotel.city }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Star class="h-3 w-3 fill-yellow-400 text-yellow-400" />{{ hotel.star_rating }}
                                </span>
                                <span v-if="hotel.category">{{ hotel.category }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium">{{ formatCurrency(hotel.price_per_night) }}</div>
                            <Badge :variant="getStatusVariant(hotel.status)" class="text-xs">{{ hotel.status }}</Badge>
                        </div>
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
