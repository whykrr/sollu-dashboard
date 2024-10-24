<template>
    <div class="bg-neutral-lighter dark:bg-neutral-darker p-4 rounded-lg">
        <div class="flex flex-col md:flex-row justify-between gap-2 mb-2">
            <h2 class="text-lg font-semibold">{{ title }}</h2>
            <div class="flex flex-row gap-2">
                <slot name="buttons" />
            </div>
        </div>
        <div class="flex-1">
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
});
</script>
