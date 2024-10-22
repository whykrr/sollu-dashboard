<template>
    <div
        class="flex rounded-md bg-light dark:bg-dark border-1 border-gray-300 dark:border-gray-600 focus-within:border-1 focus-within:border-main dark:focus-within:border-main-light sm:max-w-md"
    >
        <span
            class="flex select-none items-center pl-3 pr-1 text-gray-500 sm:text-sm"
        >
            <fa icon="fa-search" />
        </span>
        <input
            :value="modelValue"
            type="text"
            :name
            :id
            class="input-search sm:text-sm text-dark dark:text-light"
            :placeholder
        />
    </div>
</template>

<script setup>
defineProps({
    modelValue: String,
    name: String,
    label: String,
    id: String,
    placeholder: String,
});
</script>
