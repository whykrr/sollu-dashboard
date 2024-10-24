<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto floating-scrollbar relative w-full">
            <TopBar>
                {{ header }}
            </TopBar>

            <div class="p-4 pt-0">
                <slot></slot>
            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import TopBar from "@/Components/TopBar.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const header = computed(() => page.props.breadcrumbs[0].label);
</script>
