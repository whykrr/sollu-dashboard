<template>
    <div
        class="flex justify-between items-center p-4 sticky top-0 bg-neutral-200 dark:bg-neutral-900 z-10"
    >
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-1">
                <slot></slot>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="bg-transparent p-0 m-0 list-none text-sm">
                    <li class="inline-block">
                        <Link :href="route('dashboard')">
                            <fa icon="fa-home"></fa
                        ></Link>
                    </li>
                    <li
                        v-for="(crumb, index) in breadcrumbs"
                        :key="index"
                        class="inline-block before:content-['/'] before:px-2"
                    >
                        <Link :href="crumb.url" v-if="crumb.url">
                            {{ crumb.label }}
                        </Link>
                        <span v-else>{{ crumb.label }}</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="flex items-center space-x-3">
            <Search
                class="hidden lg:flex"
                label="Search"
                placeholder="Search"
                name="search"
                id="search"
            />
            <div class="nav-icon">
                <fa icon="fa-envelope"></fa>
                <span></span>
            </div>
            <div class="nav-icon">
                <fa icon="fa-bell"></fa>
                <span></span>
            </div>
            <div class="nav-account">
                <img
                    src="https://via.placeholder.com/30"
                    alt="Profile"
                    class="rounded-full"
                />
                <div>
                    <div class="text-lg font-bold">Jack Snyder</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        Super Admin
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Search from "@/Components/Form/Search.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const breadcrumbs = computed(() => page.props.breadcrumbs);
</script>
