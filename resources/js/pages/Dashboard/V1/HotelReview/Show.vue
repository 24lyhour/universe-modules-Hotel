<script setup lang="ts">
import { h, ref, type VNode } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { HotelReview } from '../../../../types';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { ArrowLeft, Star, CheckCircle, XCircle, Clock, MessageSquare, ThumbsUp, Hotel as HotelIcon } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

defineOptions({
    layout: (h_: typeof h, page: VNode) =>
        h_(AppLayout, { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }, { title: 'Reviews', href: '/dashboard/hotel-reviews' }, { title: 'Detail', href: '#' }] }, () => page),
});

const props = defineProps<{ review: HotelReview }>();

const replyForm = useForm({ reply: props.review.reply ?? '' });
const showReplyForm = ref(!props.review.reply);

const handleReply = () => {
    replyForm.patch(`/dashboard/hotel-reviews/${props.review.uuid}/reply`, {
        onSuccess: () => { showReplyForm.value = false; toast.success('Reply sent.'); },
    });
};

const handleStatus = (status: string) => {
    router.patch(`/dashboard/hotel-reviews/${props.review.uuid}/status`, { status }, {
        preserveScroll: true,
        onSuccess: () => toast.success(`Review ${status}.`),
    });
};

const formatDate = (date: string) => new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

const getStatusVariant = (status: string) => {
    switch (status) { case 'approved': return 'default'; case 'rejected': return 'destructive'; case 'pending': return 'outline'; default: return 'outline'; }
};
</script>

<template>
    <Head :title="`Review - ${review.guest_name || 'Anonymous'}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link href="/dashboard/hotel-reviews">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Review Detail</h1>
                    <p class="text-muted-foreground">by {{ review.guest_name || review.user?.name || 'Anonymous' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <Button v-if="review.status !== 'approved'" variant="default" size="sm" @click="handleStatus('approved')">
                    <CheckCircle class="mr-2 h-4 w-4" />Approve
                </Button>
                <Button v-if="review.status !== 'rejected'" variant="outline" size="sm" @click="handleStatus('rejected')">
                    <XCircle class="mr-2 h-4 w-4" />Reject
                </Button>
                <Button v-if="review.status !== 'pending'" variant="outline" size="sm" @click="handleStatus('pending')">
                    <Clock class="mr-2 h-4 w-4" />Set Pending
                </Button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Review Content -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle>Review</CardTitle>
                            <div class="flex items-center gap-2">
                                <Badge :variant="getStatusVariant(review.status)">{{ review.status }}</Badge>
                                <Badge v-if="review.is_verified" variant="default">Verified</Badge>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Rating -->
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-0.5">
                                <Star v-for="n in review.rating" :key="n" class="h-5 w-5 fill-yellow-400 text-yellow-400" />
                                <Star v-for="n in (5 - review.rating)" :key="`e-${n}`" class="h-5 w-5 text-muted-foreground/30" />
                            </div>
                            <span class="text-sm text-muted-foreground">{{ review.rating }}/5</span>
                            <Badge v-if="review.is_recommend" variant="outline" class="ml-2">
                                <ThumbsUp class="mr-1 h-3 w-3" /> Recommends
                            </Badge>
                        </div>

                        <Separator />

                        <!-- Comment -->
                        <div v-if="review.comment">
                            <h4 class="text-sm font-medium text-muted-foreground mb-2">Comment</h4>
                            <div class="prose prose-sm max-w-none" v-html="review.comment" />
                        </div>
                        <p v-else class="text-muted-foreground">No comment provided.</p>

                        <!-- Images -->
                        <div v-if="review.images && review.images.length">
                            <h4 class="text-sm font-medium text-muted-foreground mb-2">Photos</h4>
                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                                <div v-for="(img, i) in review.images" :key="i" class="aspect-square overflow-hidden rounded-lg bg-muted">
                                    <img :src="img" :alt="`Photo ${i + 1}`" class="h-full w-full object-cover" />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Reply -->
                <Card>
                    <CardHeader>
                        <CardTitle>
                            <div class="flex items-center gap-2">
                                <MessageSquare class="h-5 w-5" />
                                Hotel Reply
                            </div>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <!-- Existing reply -->
                        <div v-if="review.reply && !showReplyForm" class="space-y-3">
                            <div class="prose prose-sm max-w-none rounded-lg bg-muted/50 p-4" v-html="review.reply" />
                            <p v-if="review.replied_at" class="text-xs text-muted-foreground">Replied {{ formatDate(review.replied_at) }}</p>
                            <Button variant="outline" size="sm" @click="showReplyForm = true">Edit Reply</Button>
                        </div>

                        <!-- Reply form -->
                        <div v-else class="space-y-4">
                            <div class="space-y-2">
                                <Label>Your Reply</Label>
                                <TiptapEditor v-model="replyForm.reply" placeholder="Write your reply..." min-height="150px" max-height="300px" />
                                <p v-if="replyForm.errors.reply" class="text-sm text-destructive">{{ replyForm.errors.reply }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Button :loading="replyForm.processing" :disabled="!replyForm.reply?.trim()" @click="handleReply">
                                    Send Reply
                                </Button>
                                <Button v-if="review.reply" variant="outline" @click="showReplyForm = false">Cancel</Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Guest Info -->
                <Card>
                    <CardHeader><CardTitle>Guest Info</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Name</span>
                            <span class="font-medium">{{ review.guest_name || review.user?.name || 'Anonymous' }}</span>
                        </div>
                        <div v-if="review.guest_email" class="flex justify-between">
                            <span class="text-muted-foreground">Email</span>
                            <span>{{ review.guest_email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Verified</span>
                            <Badge :variant="review.is_verified ? 'default' : 'secondary'">{{ review.is_verified ? 'Yes' : 'No' }}</Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Helpful</span>
                            <span>{{ review.helpful_count }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Hotel -->
                <Card v-if="review.hotel">
                    <CardHeader><CardTitle>Hotel</CardTitle></CardHeader>
                    <CardContent>
                        <Link :href="`/dashboard/hotels/${review.hotel.uuid}`" class="flex items-center gap-3 hover:bg-muted/50 rounded-lg p-2 -m-2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <HotelIcon class="h-5 w-5 text-primary" />
                            </div>
                            <span class="font-medium text-sm">{{ review.hotel.name }}</span>
                        </Link>
                    </CardContent>
                </Card>

                <!-- Dates -->
                <Card>
                    <CardHeader><CardTitle>Info</CardTitle></CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Created</span>
                            <span>{{ formatDate(review.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Updated</span>
                            <span>{{ formatDate(review.updated_at) }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
