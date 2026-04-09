<script setup lang="ts">
import { h, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Hotel } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Pencil, Trash2, Star, MapPin, Phone, Mail, Globe, BedDouble, Building, ArrowLeft } from 'lucide-vue-next';

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
</script>

<template>
    <Head :title="hotel.name" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link href="/dashboard/hotels">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ hotel.name }}</h1>
                    <div class="flex items-center gap-2 text-muted-foreground">
                        <MapPin class="h-4 w-4" />
                        <span>{{ hotel.city }}, {{ hotel.country }}</span>
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

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
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
                            <h4 class="text-sm font-medium text-muted-foreground mb-1">Description</h4>
                            <p class="text-sm">{{ hotel.description }}</p>
                        </div>

                        <Separator />

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-1">Address</h4>
                                <p class="text-sm">{{ hotel.address }}</p>
                                <p class="text-sm">{{ hotel.city }}, {{ hotel.provinces || '' }} {{ hotel.country }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-muted-foreground mb-1">Category</h4>
                                <p class="text-sm">{{ hotel.category?.name || 'Uncategorized' }}</p>
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

                <!-- Gallery -->
                <Card v-if="hotel.gallery && hotel.gallery.length">
                    <CardHeader><CardTitle>Gallery</CardTitle></CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div v-for="(img, i) in hotel.gallery" :key="i" class="aspect-square overflow-hidden rounded-lg bg-muted">
                                <img :src="img" :alt="`Gallery ${i + 1}`" class="h-full w-full object-cover" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <Card>
                    <CardHeader><CardTitle>Pricing & Rating</CardTitle></CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center gap-1">
                            <Star v-for="n in hotel.star_rating" :key="n" class="h-5 w-5 fill-yellow-400 text-yellow-400" />
                            <Star v-for="n in (5 - hotel.star_rating)" :key="`e-${n}`" class="h-5 w-5 text-muted-foreground/30" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold">{{ formatCurrency(hotel.effective_price) }}</p>
                            <p class="text-sm text-muted-foreground">per night</p>
                            <p v-if="hotel.is_on_sale" class="text-sm text-muted-foreground line-through">{{ formatCurrency(hotel.price_per_night) }}</p>
                            <Badge v-if="hotel.discount_percentage" variant="destructive" class="mt-1">-{{ hotel.discount_percentage }}%</Badge>
                        </div>
                        <Separator />
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="text-muted-foreground">Rooms:</span> {{ hotel.total_rooms }}</div>
                            <div><span class="text-muted-foreground">Floors:</span> {{ hotel.total_floors }}</div>
                            <div><span class="text-muted-foreground">Currency:</span> {{ hotel.currency }}</div>
                            <div v-if="hotel.price_level"><span class="text-muted-foreground">Level:</span> {{ hotel.price_level }}</div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Info</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div><span class="text-muted-foreground">Created:</span> {{ formatDate(hotel.created_at) }}</div>
                        <div><span class="text-muted-foreground">Updated:</span> {{ formatDate(hotel.updated_at) }}</div>
                        <div v-if="hotel.created_by"><span class="text-muted-foreground">By:</span> {{ hotel.created_by.name }}</div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
