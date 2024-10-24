<template>
    <li role="presentation">
        <button class="flex flex-row gap-1.5" type="button" role="tab">
            <fa v-if="icon" :icon />
            {{ label }}
        </button>
    </li>
</template>

<script setup>
defineProps({
    icon: String,
    label: String,
});
</script>
