<script setup lang="ts">
import { h, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Room } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Pencil, Trash2, ArrowLeft, BedDouble, DollarSign, Users, Bath, Maximize, Eye,
    Hotel as HotelIcon, Layers, Hash, CheckCircle,
} from 'lucide-vue-next';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Rooms', href: '#' }, { title: 'Detail', href: '#' }] }, () => page),
});

const props = defineProps<{
    hotel: { id: number; uuid: string; name: string };
    room: Room;
}>();

const formatCurrency = (value: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
const formatDate = (date: string) => new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

const getStatusVariant = (status: string) => {
    switch (status) { case 'active': return 'default'; case 'inactive': return 'secondary'; case 'maintenance': return 'outline'; default: return 'outline'; }
};
</script>

<template>
    <Head :title="`${room.name} - ${hotel.name}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="`/dashboard/hotels/${hotel.uuid}/rooms`">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 overflow-hidden rounded-lg bg-primary/10 shrink-0">
                        <img v-if="room.images && room.images.length" :src="room.images[0]" :alt="room.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center">
                            <BedDouble class="h-7 w-7 text-primary" />
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">{{ room.name }}</h1>
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <HotelIcon class="h-4 w-4" />
                            <span>{{ hotel.name }}</span>
                            <span v-if="room.room_number" class="text-xs">#{{ room.room_number }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <Link :href="`/dashboard/hotels/${hotel.uuid}/rooms/${room.uuid}/edit`">
                    <Button variant="outline" size="sm"><Pencil class="mr-2 h-4 w-4" />Edit</Button>
                </Link>
                <Button variant="destructive" size="sm" @click="router.delete(`/dashboard/hotels/${hotel.uuid}/rooms/${room.uuid}`)">
                    <Trash2 class="mr-2 h-4 w-4" />Delete
                </Button>
            </div>
        </div>

        <!-- Room Images -->
        <div v-if="room.images && room.images.length" class="overflow-hidden rounded-lg">
            <img :src="room.images[0]" :alt="room.name" class="h-[350px] w-full object-contain" />
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Details -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Room Details</CardTitle>
                            <div class="flex items-center gap-2">
                                <Badge :variant="getStatusVariant(room.status)">{{ room.status }}</Badge>
                                <Badge :variant="room.is_available ? 'default' : 'secondary'">
                                    {{ room.is_available ? 'Available' : 'Unavailable' }}
                                </Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="room.description">
                            <h4 class="text-sm font-medium text-muted-foreground mb-2">Description</h4>
                            <div class="prose prose-sm max-w-none" v-html="room.description" />
                        </div>

                        <Separator />

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="flex items-center gap-3 rounded-lg border p-3">
                                <BedDouble class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Room Type</p>
                                    <p class="text-sm font-medium capitalize">{{ room.room_type || 'Standard' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-lg border p-3">
                                <Users class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Capacity</p>
                                    <p class="text-sm font-medium">{{ room.capacity }} guests</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-lg border p-3">
                                <Layers class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Total Rooms</p>
                                    <p class="text-sm font-medium">{{ room.total_room }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-lg border p-3">
                                <CheckCircle class="h-5 w-5 text-green-500" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Available Count</p>
                                    <p class="text-sm font-medium">{{ room.room_available_count ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-lg border p-3">
                                <BedDouble class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Bed</p>
                                    <p class="text-sm font-medium capitalize">{{ room.bed_type || '-' }} x {{ room.bed_count }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 rounded-lg border p-3">
                                <Bath class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Bathrooms</p>
                                    <p class="text-sm font-medium">{{ room.bathroom_count }}</p>
                                </div>
                            </div>

                            <div v-if="room.room_size" class="flex items-center gap-3 rounded-lg border p-3">
                                <Maximize class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Room Size</p>
                                    <p class="text-sm font-medium">{{ room.room_size }}</p>
                                </div>
                            </div>

                            <div v-if="room.view" class="flex items-center gap-3 rounded-lg border p-3">
                                <Eye class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">View</p>
                                    <p class="text-sm font-medium">{{ room.view }}</p>
                                </div>
                            </div>

                            <div v-if="room.room_number" class="flex items-center gap-3 rounded-lg border p-3">
                                <Hash class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Room Number</p>
                                    <p class="text-sm font-medium">{{ room.room_number }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Gallery -->
                <Card v-if="room.images && room.images.length > 1">
                    <CardHeader><CardTitle>Gallery ({{ room.images.length }})</CardTitle></CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div v-for="(img, i) in room.images" :key="i" class="aspect-square overflow-hidden rounded-lg bg-muted">
                                <img :src="img" :alt="`Room ${i + 1}`" class="h-full w-full object-cover" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pricing -->
                <Card>
                    <CardHeader><CardTitle>Pricing</CardTitle></CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-2xl font-bold">{{ formatCurrency(room.price) }}</p>
                            <p class="text-sm text-muted-foreground">per night</p>
                        </div>
                        <div v-if="room.discount_price">
                            <p class="text-sm text-muted-foreground line-through">{{ formatCurrency(room.discount_price) }}</p>
                            <Badge variant="destructive">Discounted</Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Amenities -->
                <Card v-if="room.amenities && room.amenities.length">
                    <CardHeader><CardTitle>Amenities</CardTitle></CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="amenity in room.amenities" :key="amenity" variant="outline">
                                {{ amenity }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Info -->
                <Card>
                    <CardHeader><CardTitle>Info</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Hotel</span>
                            <Link :href="`/dashboard/hotels/${hotel.uuid}`" class="text-primary hover:underline">{{ hotel.name }}</Link>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Sort Order</span>
                            <span>{{ room.sort_order }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Created</span>
                            <span>{{ formatDate(room.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Updated</span>
                            <span>{{ formatDate(room.updated_at) }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
