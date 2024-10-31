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
            <!-- <button
                type="button"
                @click="toggleDarkMode"
                class="nav-icon bg-dark text-light dark:bg-light dark:text-dark"
                title="Theme Toggle"
            >
                <fa icon="fa-circle-half-stroke"></fa>
            </button> -->
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
import { ref } from "vue";

const isDarkMode = ref(true);

// Check if the dark mode is already in localStorage or Session
if (sessionStorage.getItem("dark_mode") === "true") {
    isDarkMode.value = true;
    document.documentElement.classList.add("dark");
}

function toggleDarkMode() {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add("dark");
        sessionStorage.setItem("dark_mode", "true");
    } else {
        document.documentElement.classList.remove("dark");
        sessionStorage.setItem("dark_mode", "false");
    }
}
</script>
