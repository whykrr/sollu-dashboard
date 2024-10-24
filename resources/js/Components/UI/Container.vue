<template>
    <div
        class="bg-neutral-lighter dark:bg-neutral-darker p-4 rounded-lg shadow-md"
    >
        <div class="flex flex-col md:flex-row justify-between gap-2">
            <h2 class="text-lg font-semibold">{{ title }}</h2>
            <div class="flex flex-row gap-2">
                <slot name="buttons" />
            </div>
        </div>
        <slot />
    </div>
</template>

<script setup>
defineProps({
    title: String,
});
</script>
