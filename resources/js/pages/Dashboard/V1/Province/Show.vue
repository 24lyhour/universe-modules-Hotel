<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { ArrowLeft, Edit, Trash2, MapPin, Globe, Hash, Eye } from 'lucide-vue-next';
import type { Province } from '../../../../types';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Provinces', href: '/dashboard/hotel-provinces' },
            { title: 'View', href: '#' },
        ] }, () => page),
});

interface Props {
    province: Province;
}

const props = defineProps<Props>();

const statusBadgeVariant = computed(() => {
    return props.province.is_active ? 'default' : 'secondary';
});

const statusText = computed(() => {
    return props.province.is_active ? 'Active' : 'Inactive';
});
</script>

<template>
    <Head :title="province.name" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/dashboard/hotel-provinces">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ province.name }}</h1>
                    <p class="text-sm text-muted-foreground">{{ province.name_kh }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child>
                    <Link :href="`/dashboard/hotel-provinces/${province.uuid}/edit`">
                        <Edit class="mr-2 h-4 w-4" />
                        Edit
                    </Link>
                </Button>
                <Button variant="destructive" as-child>
                    <Link :href="`/dashboard/hotel-provinces/${province.uuid}/delete`">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Link>
                </Button>
            </div>
        </div>

        <Separator />

        <!-- Main Content Grid -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Logo Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm">Province Logo</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col items-center gap-4">
                        <div v-if="province.logo_url" class="w-full">
                            <img
                                :src="province.logo_url"
                                :alt="`${province.name} logo`"
                                class="h-48 w-full rounded-lg border object-cover"
                            />
                        </div>
                        <div v-else class="flex h-48 w-full items-center justify-center rounded-lg border-2 border-dashed border-muted-foreground/25">
                            <MapPin class="h-8 w-8 text-muted-foreground" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Province Information -->
            <Card class="md:col-span-2">
                <CardHeader>
                    <CardTitle class="text-sm">Province Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- Code -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Hash class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm font-medium text-muted-foreground">Code</span>
                            </div>
                            <code class="rounded bg-muted px-2 py-1 text-sm font-mono">{{ province.code }}</code>
                        </div>

                        <!-- Region -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <MapPin class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm font-medium text-muted-foreground">Region</span>
                            </div>
                            <p class="text-sm font-medium">{{ province.region || '-' }}</p>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <Eye class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm font-medium text-muted-foreground">Status</span>
                            </div>
                            <Badge :variant="statusBadgeVariant">{{ statusText }}</Badge>
                        </div>

                        <!-- Sort Order -->
                        <div class="space-y-2">
                            <span class="text-sm font-medium text-muted-foreground">Sort Order</span>
                            <p class="text-sm font-medium">{{ province.sort_order }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Location Card -->
        <Card>
            <CardHeader>
                <CardTitle class="text-sm">Location Coordinates</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <span class="text-sm font-medium text-muted-foreground">Latitude</span>
                        <p class="font-mono text-sm">{{ province.latitude || '-' }}</p>
                    </div>
                    <div class="space-y-2">
                        <span class="text-sm font-medium text-muted-foreground">Longitude</span>
                        <p class="font-mono text-sm">{{ province.longitude || '-' }}</p>
                    </div>
                    <div v-if="province.latitude && province.longitude" class="md:col-span-2">
                        <a
                            :href="`https://maps.google.com/?q=${province.latitude},${province.longitude}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm text-primary hover:underline"
                        >
                            <Globe class="h-4 w-4" />
                            View on Google Maps
                        </a>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Metadata Card -->
        <Card>
            <CardHeader>
                <CardTitle class="text-sm">Metadata</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <span class="text-sm font-medium text-muted-foreground">UUID</span>
                        <p class="break-all font-mono text-xs text-muted-foreground">{{ province.uuid }}</p>
                    </div>
                    <div class="space-y-2">
                        <span class="text-sm font-medium text-muted-foreground">Hotels Count</span>
                        <p class="text-sm font-medium">{{ province.hotels_count || 0 }}</p>
                    </div>
                    <div class="space-y-2">
                        <span class="text-sm font-medium text-muted-foreground">Created At</span>
                        <p class="text-sm text-muted-foreground">{{ new Date(province.created_at).toLocaleString() }}</p>
                    </div>
                    <div class="space-y-2">
                        <span class="text-sm font-medium text-muted-foreground">Updated At</span>
                        <p class="text-sm text-muted-foreground">{{ new Date(province.updated_at).toLocaleString() }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
