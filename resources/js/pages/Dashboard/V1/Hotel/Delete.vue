<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { toast } from 'vue-sonner';
import { ModalForm } from '@/components/shared';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { AlertTriangle, Hotel } from 'lucide-vue-next';
import type { Hotel as HotelType } from '../../../../types';

const props = defineProps<{ hotel: HotelType }>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const confirmed = ref(false);

const form = useForm({
    confirmed: false,
});

watch(confirmed, () => {
    form.confirmed = confirmed.value;
});

const canSubmit = computed(() => confirmed.value === true);

const handleSubmit = () => {
    form.delete(`/dashboard/hotels/${props.hotel.uuid}`, {
        onSuccess: () => {
            toast.success('Hotel deleted successfully.');
            setTimeout(() => {
                close();
                redirect();
            }, 100);
        },
    });
};

const handleCancel = () => {
    close();
    redirect();
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Delete Hotel"
        description="This action cannot be undone"
        mode="delete"
        size="md"
        submit-text="Delete Hotel"
        :loading="form.processing"
        :disabled="!canSubmit"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Hotel Info -->
            <div class="flex items-center gap-4 p-4 rounded-lg border bg-muted/30">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <Hotel class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <p class="font-medium">{{ hotel.name }}</p>
                    <p v-if="hotel.city" class="text-sm text-muted-foreground">
                        {{ hotel.city }}, {{ hotel.country }}
                    </p>
                </div>
            </div>

            <!-- Warning Banner -->
            <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <AlertTriangle class="mt-0.5 h-5 w-5 text-destructive" />
                <div class="space-y-1">
                    <p class="text-sm font-medium text-destructive">
                        You are about to delete this hotel
                    </p>
                    <p class="text-sm text-muted-foreground">
                        <strong>{{ hotel.name }}</strong> and all its data will be moved to trash.
                    </p>
                    <p v-if="hotel.rooms_count && hotel.rooms_count > 0" class="text-sm text-destructive">
                        Warning: This hotel has <strong>{{ hotel.rooms_count }}</strong> room(s) assigned to it.
                    </p>
                </div>
            </div>

            <!-- Confirmation Checkbox -->
            <div class="flex items-start space-x-3 rounded-lg border p-4">
                <Checkbox
                    id="confirmed"
                    v-model="confirmed"
                />
                <div class="space-y-1">
                    <Label for="confirmed" class="cursor-pointer font-medium">
                        I confirm this deletion
                    </Label>
                    <p class="text-sm text-muted-foreground">
                        I understand that this action cannot be undone.
                    </p>
                </div>
            </div>

            <p v-if="form.errors.confirmed" class="text-sm text-destructive">
                {{ form.errors.confirmed }}
            </p>
        </div>
    </ModalForm>
</template>
