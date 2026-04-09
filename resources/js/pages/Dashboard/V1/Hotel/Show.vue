<script setup lang="ts">
import { h, computed, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Hotel } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { GeofenceMap } from '@/components/shared';
import {
    Pencil, Trash2, Star, MapPin, Phone, Mail, Globe, BedDouble, Building,
    ArrowLeft, Hotel as HotelIcon, DollarSign, Layers, CheckCircle, XCircle,
} from 'lucide-vue-next';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Hotels', href: '/dashboard/hotels' }, { title: 'Detail', href: '#' }] }, () => page),
});

const props = defineProps<{ hotel: Hotel }>();

const formatCurrency = (value: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
const formatDate = (date: string) => new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

const getStatusVariant = (status: string) => {
    switch (status) { case 'active': return 'default'; case 'inactive': return 'secondary'; case 'draft': return 'outline'; default: return 'outline'; }
};

const mapLatitude = computed(() => props.hotel.latitude != null ? Number(props.hotel.latitude) : null);
const mapLongitude = computed(() => props.hotel.longitude != null ? Number(props.hotel.longitude) : null);
const hasLocation = computed(() => mapLatitude.value !== null && mapLongitude.value !== null);
</script>

<template>
    <Head :title="hotel.name" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link href="/dashboard/hotels">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 overflow-hidden rounded-lg bg-muted shrink-0">
                        <img v-if="hotel.logo_url || hotel.featured_image" :src="(hotel.logo_url || hotel.featured_image)!" :alt="hotel.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center">
                            <HotelIcon class="h-7 w-7 text-muted-foreground" />
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">{{ hotel.name }}</h1>
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <MapPin class="h-4 w-4" />
                            <span>{{ hotel.city }}, {{ hotel.country }}</span>
                            <span v-if="hotel.province" class="text-xs">({{ hotel.province.name }})</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <Link :href="`/dashboard/hotels/${hotel.uuid}/edit`">
                    <Button variant="outline" size="sm"><Pencil class="mr-2 h-4 w-4" />Edit</Button>
                </Link>
                <Link :href="`/dashboard/hotels/${hotel.uuid}/rooms`">
                    <Button variant="outline" size="sm"><BedDouble class="mr-2 h-4 w-4" />Rooms</Button>
                </Link>
                <Button variant="destructive" size="sm" @click="router.visit(`/dashboard/hotels/${hotel.uuid}/confirm-delete`)">
                    <Trash2 class="mr-2 h-4 w-4" />Delete
                </Button>
            </div>
        </div>

        <!-- Featured Image -->
        <div v-if="hotel.featured_image" class="overflow-hidden rounded-lg bg-muted">
            <img :src="hotel.featured_image" :alt="hotel.name" class="max-h-[400px] w-full object-contain" />
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Details -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Hotel Details</CardTitle>
                            <div class="flex items-center gap-2">
                                <Badge :variant="getStatusVariant(hotel.status)">{{ hotel.status }}</Badge>
                                <Badge v-if="hotel.is_featured" variant="default">Featured</Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="hotel.description">
                            <h4 class="text-sm font-medium text-muted-foreground mb-2">Description</h4>
                            <div class="prose prose-sm max-w-none" v-html="hotel.description" />
                        </div>

                        <Separator />

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-1">Address</h4>
                                <p class="text-sm">{{ hotel.address }}</p>
                                <p class="text-sm text-muted-foreground">{{ hotel.city }}, {{ hotel.country }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-1">Category</h4>
                                <p class="text-sm">{{ hotel.category?.name || 'Uncategorized' }}</p>
                            </div>
                            <div v-if="hotel.province">
                                <h4 class="text-sm font-medium text-muted-foreground mb-1">Province</h4>
                                <p class="text-sm">{{ hotel.province.name }} ({{ hotel.province.name_kh }})</p>
                            </div>
                        </div>

                        <Separator />

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div v-if="hotel.phone" class="flex items-center gap-2">
                                <Phone class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">{{ hotel.phone }}</span>
                            </div>
                            <div v-if="hotel.email" class="flex items-center gap-2">
                                <Mail class="h-4 w-4 text-muted-foreground" />
                                <span class="text-sm">{{ hotel.email }}</span>
                            </div>
                            <div v-if="hotel.website" class="flex items-center gap-2">
                                <Globe class="h-4 w-4 text-muted-foreground" />
                                <a :href="hotel.website" target="_blank" class="text-sm text-primary hover:underline">{{ hotel.website }}</a>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Map -->
                <Card v-if="hasLocation">
                    <CardHeader><CardTitle>Location</CardTitle></CardHeader>
                    <CardContent>
                        <GeofenceMap
                            :latitude="mapLatitude"
                            :longitude="mapLongitude"
                            mode="location"
                            height="350px"
                            :readonly="true"
                        />
                        <div class="mt-3 flex items-center gap-4 p-3 rounded-lg bg-muted/50">
                            <div class="flex-1 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-muted-foreground">Latitude</p>
                                    <p class="font-mono text-sm">{{ Number(hotel.latitude).toFixed(6) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Longitude</p>
                                    <p class="font-mono text-sm">{{ Number(hotel.longitude).toFixed(6) }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Gallery -->
                <Card v-if="hotel.gallery && hotel.gallery.length">
                    <CardHeader><CardTitle>Gallery ({{ hotel.gallery.length }})</CardTitle></CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div v-for="(img, i) in hotel.gallery" :key="i" class="aspect-square overflow-hidden rounded-lg bg-muted">
                                <img :src="img" :alt="`Gallery ${i + 1}`" class="h-full w-full object-cover" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Rooms -->
                <Card v-if="hotel.rooms && hotel.rooms.length">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Rooms ({{ hotel.rooms.length }})</CardTitle>
                            <Link :href="`/dashboard/hotels/${hotel.uuid}/rooms`">
                                <Button variant="outline" size="sm">View All</Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="room in hotel.rooms" :key="room.uuid" class="flex items-center justify-between rounded-lg border p-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                        <BedDouble class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm">{{ room.name }}</p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ room.room_type || 'Standard' }}
                                            <span v-if="room.bed_type"> &middot; {{ room.bed_type }}</span>
                                            <span v-if="room.capacity"> &middot; {{ room.capacity }} guests</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-sm">{{ formatCurrency(room.price) }}</p>
                                    <Badge :variant="room.is_available ? 'default' : 'secondary'" class="text-xs">
                                        {{ room.is_available ? 'Available' : 'Unavailable' }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pricing & Rating -->
                <Card>
                    <CardHeader><CardTitle>Pricing & Rating</CardTitle></CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center gap-1">
                            <Star v-for="n in hotel.star_rating" :key="n" class="h-5 w-5 fill-yellow-400 text-yellow-400" />
                            <Star v-for="n in (5 - hotel.star_rating)" :key="`e-${n}`" class="h-5 w-5 text-muted-foreground/30" />
                            <span class="ml-2 text-sm text-muted-foreground">{{ hotel.star_rating }} Stars</span>
                        </div>
                        <div v-if="hotel.min_price !== null">
                            <p class="text-2xl font-bold">{{ formatCurrency(hotel.min_price) }}</p>
                            <p v-if="hotel.max_price && hotel.max_price !== hotel.min_price" class="text-sm text-muted-foreground">
                                to {{ formatCurrency(hotel.max_price) }}
                            </p>
                            <p class="text-sm text-muted-foreground">per night (from rooms)</p>
                            <div v-if="hotel.discount_price" class="mt-1">
                                <Badge variant="destructive">
                                    {{ formatCurrency(hotel.discount_price) }}
                                    <span v-if="hotel.discount_percentage"> (-{{ hotel.discount_percentage }}%)</span>
                                </Badge>
                            </div>
                        </div>
                        <div v-else>
                            <p class="text-lg text-muted-foreground">No rooms added yet</p>
                        </div>
                        <Separator />
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center gap-2">
                                <BedDouble class="h-4 w-4 text-muted-foreground" />
                                <span>{{ hotel.total_rooms }} rooms</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Layers class="h-4 w-4 text-muted-foreground" />
                                <span>{{ hotel.total_floors }} floors</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <DollarSign class="h-4 w-4 text-muted-foreground" />
                                <span>{{ hotel.currency }}</span>
                            </div>
                            <div v-if="hotel.price_level" class="flex items-center gap-2">
                                <Building class="h-4 w-4 text-muted-foreground" />
                                <span class="capitalize">{{ hotel.price_level }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Amenities -->
                <Card v-if="hotel.amenities && hotel.amenities.length">
                    <CardHeader><CardTitle>Amenities</CardTitle></CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="amenity in hotel.amenities" :key="amenity" variant="outline">
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
                            <span class="text-muted-foreground">Created</span>
                            <span>{{ formatDate(hotel.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Updated</span>
                            <span>{{ formatDate(hotel.updated_at) }}</span>
                        </div>
                        <div v-if="hotel.created_by" class="flex justify-between">
                            <span class="text-muted-foreground">Created by</span>
                            <span>{{ hotel.created_by.name }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
