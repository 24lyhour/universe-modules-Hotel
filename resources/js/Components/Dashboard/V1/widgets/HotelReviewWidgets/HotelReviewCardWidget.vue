<script setup lang="ts">
import { computed } from 'vue';
import { Star, MessageSquare, Heart, Eye, Pencil, Hotel as HotelIcon, CheckCircle, XCircle, ThumbsUp, Trash2 } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { useTranslation } from '@/composables/useTranslation';

const { __ } = useTranslation();

import type { HotelReview } from '@hotel/types';

const props = defineProps<{
    review: HotelReview;
}>();

const emit = defineEmits<{
    (e: 'view', review: HotelReview): void;
    (e: 'toggleActive', review: HotelReview, isActive: boolean): void;
    (e: 'delete', review: HotelReview): void;
    (e: 'reply', review: HotelReview): void;
}>();

const stars = computed(() => {
    return Array.from({ length: 5 }, (_, i) => i < props.review.rating);
});

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const displayName = computed(() => props.review.guest_name || props.review.customer?.name || __('Anonymous Guest'));
</script>

<template>
    <Card class="overflow-hidden hover:shadow-md transition-all duration-200 border-l-4" 
        :class="review.is_active ? 'border-l-emerald-500' : 'border-l-red-500'">
        <CardContent class="p-6">
            <div class="flex gap-4">
                <!-- Avatar -->
                <Avatar class="h-14 w-14 shrink-0 border-2 border-muted">
                    <AvatarImage v-if="review.customer?.avatar" :src="review.customer.avatar" />
                    <AvatarFallback class="bg-primary/5 text-primary text-lg font-bold">
                        {{ getInitials(displayName) }}
                    </AvatarFallback>
                </Avatar>

                <!-- Content -->
                <div class="flex-1 min-w-0 space-y-3">
                    <!-- Header -->
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-lg leading-none">
                                    {{ displayName }}
                                </h3>
                                <Badge v-if="review.is_verified" variant="secondary" class="h-5 text-[10px] px-1.5 py-0">
                                    {{ __('Verified') }}
                                </Badge>
                                <Badge :variant="review.is_active ? 'default' : 'destructive'" class="h-5 text-[10px] px-1.5 py-0 capitalize">
                                    {{ review.is_active ? __('Active') : __('Inactive') }}
                                </Badge>
                            </div>
                            <p v-if="review.guest_email" class="text-xs text-muted-foreground">{{ review.guest_email }}</p>
                        </div>

                        <div class="flex flex-col items-end gap-1">
                            <div class="flex items-center gap-0.5">
                                <Star
                                    v-for="(filled, index) in stars"
                                    :key="index"
                                    class="h-4 w-4"
                                    :class="filled ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200 dark:text-gray-700'"
                                />
                            </div>
                            <span class="text-xs text-muted-foreground tabular-nums">{{ formatDate(review.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Hotel Badge -->
                    <div v-if="review.hotel" class="flex items-center gap-2">
                        <Badge variant="outline" class="bg-primary/5 text-primary border-primary/20 flex gap-1.5 hover:bg-primary/10 transition-colors">
                            <HotelIcon class="h-3 w-3" />
                            {{ review.hotel.name }}
                        </Badge>
                        <Badge v-if="review.is_recommend" variant="secondary" class="bg-emerald-50 text-emerald-700 border-emerald-100 flex gap-1.5">
                            <ThumbsUp class="h-3 w-3" />
                            {{ __('Recommends') }}
                        </Badge>
                    </div>

                    <!-- Comment -->
                    <div class="relative group">
                        <p v-if="review.comment" class="text-sm text-foreground/90 leading-relaxed italic pr-6 h-auto" v-html="review.comment"></p>
                        <p v-else class="text-sm text-muted-foreground italic">
                            {{ __('No comment provided') }}
                        </p>
                    </div>

                    <!-- Images -->
                    <div v-if="review.images && review.images.length" class="flex flex-wrap gap-2 mt-2">
                        <div v-for="(img, i) in review.images.slice(0, 4)" :key="i" class="relative group aspect-square h-16 w-16 overflow-hidden rounded-md border border-muted hover:border-primary transition-colors cursor-pointer" @click="emit('view', review)">
                            <img :src="img" alt="Review Photo" class="h-full w-full object-cover transition-transform group-hover:scale-110" />
                            <div v-if="i === 3 && review.images.length > 4" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-xs font-bold">
                                +{{ review.images.length - 4 }}
                            </div>
                        </div>
                    </div>

                    <!-- Reply -->
                    <div v-if="review.reply" class="mt-4 p-4 bg-muted/30 rounded-lg border-l-2 border-muted relative">
                        <MessageSquare class="absolute right-4 top-4 h-4 w-4 text-muted-foreground/30" />
                        <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-1">{{ __('Management Reply') }}</p>
                        <p class="text-sm text-foreground/80 leading-relaxed" v-html="review.reply"></p>
                        <p v-if="review.replied_at" class="text-[10px] text-muted-foreground mt-2">{{ __('Replied on') }} {{ formatDate(review.replied_at) }}</p>
                    </div>

                    <Separator class="mt-4" />

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <Button variant="outline" size="sm" class="h-8 gap-1.5" @click="emit('view', review)">
                            <Eye class="h-3.5 w-3.5" />
                            {{ __('View Detailed') }}
                        </Button>
                        
                        <Button v-if="!review.reply" variant="outline" size="sm" class="h-8 gap-1.5 text-primary border-primary/20 hover:bg-primary/5" @click="emit('reply', review)">
                            <MessageSquare class="h-3.5 w-3.5" />
                            {{ __('Reply') }}
                        </Button>
                        <Button v-else variant="ghost" size="sm" class="h-8 gap-1.5" @click="emit('reply', review)">
                            <Pencil class="h-3.5 w-3.5" />
                            {{ __('Edit Reply') }}
                        </Button>

                        <div class="flex items-center gap-1 ml-auto">
                            <Button v-if="!review.is_active" variant="ghost" size="icon" class="h-8 w-8 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50" @click="emit('toggleActive', review, true)">
                                <CheckCircle class="h-4 w-4" />
                            </Button>
                            <Button v-if="review.is_active" variant="ghost" size="icon" class="h-8 w-8 text-red-600 hover:text-red-700 hover:bg-red-50" @click="emit('toggleActive', review, false)">
                                <XCircle class="h-4 w-4" />
                            </Button>
                            <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/5" @click="emit('delete', review)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
