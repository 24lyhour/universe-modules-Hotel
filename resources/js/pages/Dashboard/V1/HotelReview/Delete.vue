<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { toast } from 'vue-sonner';
import { ModalForm } from '@/components/shared';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { AlertTriangle, Star, MessageSquare } from 'lucide-vue-next';
import type { HotelReview } from '../../../../types';

const props = defineProps<{ review: HotelReview }>();

const { show, close, redirect } = useModal();
const isOpen = computed({ get: () => show.value, set: (val: boolean) => { if (!val) { close(); redirect(); } } });

const confirmed = ref(false);
const form = useForm({ confirmed: false });

watch(confirmed, () => { form.confirmed = confirmed.value; });

const canSubmit = computed(() => confirmed.value === true);

const handleSubmit = () => {
    form.delete(`/dashboard/hotel-reviews/${props.review.uuid}`, {
        onSuccess: () => { toast.success('Review deleted.'); setTimeout(() => { close(); redirect(); }, 100); },
    });
};

const handleCancel = () => { close(); redirect(); };
</script>

<template>
    <ModalForm v-model:open="isOpen" title="Delete Review" description="This action cannot be undone" mode="delete" size="md" submit-text="Delete Review" :loading="form.processing" :disabled="!canSubmit" @submit="handleSubmit" @cancel="handleCancel">
        <div class="space-y-6">
            <div class="flex items-center gap-4 p-4 rounded-lg border bg-muted/30">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <MessageSquare class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <p class="font-medium">{{ review.guest_name || 'Anonymous' }}</p>
                    <div class="flex items-center gap-1">
                        <Star v-for="n in review.rating" :key="n" class="h-3 w-3 fill-yellow-400 text-yellow-400" />
                        <span class="text-xs text-muted-foreground ml-1">{{ review.hotel?.name }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <AlertTriangle class="mt-0.5 h-5 w-5 text-destructive" />
                <div class="space-y-1">
                    <p class="text-sm font-medium text-destructive">You are about to delete this review</p>
                    <p class="text-sm text-muted-foreground">This review will be moved to trash.</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 rounded-lg border p-4">
                <Checkbox id="confirmed" v-model="confirmed" />
                <div class="space-y-1">
                    <Label for="confirmed" class="cursor-pointer font-medium">I confirm this deletion</Label>
                    <p class="text-sm text-muted-foreground">I understand that this action cannot be undone.</p>
                </div>
            </div>
        </div>
    </ModalForm>
</template>
