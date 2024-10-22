<template>
    <div class="flex flex-col md:flex-row gap-2 justify-between">
        <div v-if="from && to && total" class="text-xs text-neutral-muted">
            Showing
            <span class="font-semibold">{{ from }} - {{ to }}</span>
            from
            <span class="font-semibold">{{ total }}</span>
            data
        </div>
        <div class="flex gap-1.5 items-center">
            <Link
                v-for="link in links"
                :id="link.id"
                class="btn btn-neutral-main"
                :href="link.url"
                :class="{
                    active: link.active,
                }"
                v-html="link.label"
            />
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    from: Number,
    to: Number,
    total: Number,
    links: Array,
});
</script>
