<template>
    <a
        :href="to"
        class="sidebar-item-expand overflow-hidden rounded-lg w-full"
        :class="{ active: isActive || isSubMenuOpen }"
    >
        <button type="button" @click="toggleSubMenu">
            <fa :icon="icon" class="w-[20px]"></fa>
            <div class="grow text-left">{{ name }}</div>
            <fa
                icon="fa-chevron-down"
                class="transition-transform duration-200"
                :class="{ 'rotate-180': isSubMenuOpen }"
            />
        </button>

        <!-- Animated Submenu -->
        <transition name="submenu">
            <div v-if="isSubMenuOpen" class="gap-2">
                <a href="#" class="sidebar-item">
                    <div class="w-[20px]"></div>
                    Test
                </a>
                <a href="#" class="sidebar-item">
                    <div class="w-[20px]"></div>
                    Security</a
                >
                <a href="#" class="sidebar-item">
                    <div class="w-[20px]"></div>
                    Notifications</a
                >
            </div>
        </transition>
    </a>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    to: String,
    icon: String,
    name: String,
    isActive: Boolean,
});

const isSubMenuOpen = ref(false);

const toggleSubMenu = () => {
    isSubMenuOpen.value = !isSubMenuOpen.value;
    console.log(isSubMenuOpen.value);
};
</script>

<style scoped>
/* Custom transition classes for submenu */
.submenu-enter-active,
.submenu-leave-active {
    transition: max-height 0.3s ease, opacity 0.3s ease;
}
.submenu-enter-from,
.submenu-leave-to {
    max-height: 0;
    opacity: 0;
}
.submenu-enter-to,
.submenu-leave-from {
    max-height: 500px; /* or max-h-screen, depends on expected size */
    opacity: 1;
}
</style>
