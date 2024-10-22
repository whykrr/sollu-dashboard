<template>
    <div class="flex justify-between items-center p-4 pb-0">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold">
            <slot></slot>
        </h1>
        <div class="flex items-center space-x-4">
            <Search
                class="hidden lg:flex"
                label="Search"
                placeholder="..."
                name="search"
                id="search"
            />
            <div
                class="flex flex-row items-center gap-2 p-2 px-3 bg-neutral-lighter dark:bg-neutral-darker rounded-xl shadow-md"
            >
                <img
                    src="https://via.placeholder.com/30"
                    alt="Profile"
                    class="rounded-full"
                />
                <span>Jack Snyder</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import Search from "@/Components/Form/Search.vue";
</script>
