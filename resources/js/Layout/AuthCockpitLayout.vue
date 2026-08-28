<template>
    <div
        class="min-h-screen flex items-center justify-center bg-main/5 px-4 sm:px-6 lg:px-8 relative overflow-hidden select-none"
    >
        <div
            v-if="loading"
            class="fixed inset-0 flex items-center justify-center bg-gray-100/50 z-50"
        >
            <div class="spinner" />
        </div>

        <!-- Background Elements -->
        <div
            class="absolute -top-24 -left-24 w-96 h-96 bg-main/10 rounded-full blur-3xl"
        ></div>
        <div
            class="absolute -bottom-24 -right-24 w-96 h-96 bg-main/10 rounded-full blur-3xl"
        ></div>

        <slot />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const loading = ref(false);

router.on('start', () => (loading.value = true));
router.on('finish', () => (loading.value = false));
</script>
