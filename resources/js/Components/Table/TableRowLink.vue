<template>
    <tr
        class="hover:bg-slate-100 dark:hover:bg-slate-100/10"
        @click="navigate()"
    >
        <slot></slot>
    </tr>
</template>

<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    to: String,
});

const navigate = () => {
    router.get(props.to);
};
</script>
