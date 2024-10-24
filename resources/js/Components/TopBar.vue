<template>
    <div
        class="flex justify-between items-center p-4 sticky top-0 bg-neutral-200 dark:bg-neutral-900 z-10"
    >
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-1">
                <slot></slot>
            </h1>
            <Breadcrumbs />
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
import Breadcrumbs from "./UI/Breadcrumbs.vue";
</script>
