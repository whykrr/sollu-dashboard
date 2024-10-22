<template>
    <a :href="to" class="sidebar-item rounded-lg" :class="{ active: isActive }">
        <fa :icon="icon" class="w-[20px]"></fa>
        <div>
            {{ name }}
        </div>
    </a>
</template>

<script setup>
defineProps({
    to: String,
    icon: String,
    name: String,
    isActive: Boolean,
});
</script>
