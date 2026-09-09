<script setup>
import { Link } from '@inertiajs/vue3';
import { useModalStore } from '@/store/notification';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faLock } from '@fortawesome/free-solid-svg-icons';

defineProps({
    feature: {
        type: String,
        default: 'Fitur',
    },
});

const modalStore = useModalStore();
</script>

<template>
    <div class="text-center">
        <div
            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 mb-4"
        >
            <FontAwesomeIcon
                :icon="faLock"
                size="2x"
                class="h-20 w-20 text-orange-600"
            />
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-2">Fitur Terkunci</h3>

        <p class="text-sm text-gray-500 mb-6">
            Maaf, fitur ini tidak tersedia pada paket langganan Anda saat ini.
            Silakan tingkatkan paket Anda untuk menikmati fitur ini dan fitur
            eksklusif lainnya.
        </p>

        <div class="flex flex-col gap-2">
            <Link
                :href="route('settings.billing.index')"
                class="btn btn-main justify-center"
                @click="modalStore.close()"
            >
                Tingkatkan Langganan
            </Link>

            <button
                type="button"
                class="btn btn-slate-400 justify-center"
                @click="modalStore.close()"
            >
                Nanti Saja
            </button>
        </div>
    </div>
</template>
