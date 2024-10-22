<template>
    <div class="hidden sm:flex w-64 md:w-72 h-screen">
        <div
            class="bg-neutral-lighter dark:bg-neutral-darker text-neutral-600 dark:text-neutral-400 w-full flex flex-col justify-between shadow"
        >
            <div class="p-4 pb-6">
                <img
                    src="storage/img/logo-fit-color.png"
                    class="w-28"
                    alt="Sollu CMS"
                />
            </div>
            <div class="flex-1 overflow-y-auto floating-scrollbar">
                <nav class="flex flex-col gap-2 p-4 pt-0">
                    <SidebarItem
                        to="#"
                        icon="fa-home"
                        name="Dashboard"
                        :is-active="true"
                    />
                    <div class="p-0 font-bold text-md">Main</div>
                    <SidebarItem to="#" icon="fa-users" name="Users" />
                    <SidebarItem
                        to="#"
                        icon="fa-border-all"
                        name="Category"
                        :is-active="true"
                    />
                    <SidebarItem to="#" icon="fa-envelope" name="Contact Us" />
                    <SidebarItem to="#" icon="fa-cogs" name="Setting" />
                    <div class="p-0 font-bold text-md">Section</div>
                    <SidebarItem to="#" icon="fa-icons" name="Hero" />
                    <SidebarItemExpand to="#" icon="fa-icons" name="Gallery" />
                    <SidebarItem to="#" icon="fa-icons" name="About Us" />
                    <SidebarItem to="#" icon="fa-icons" name="Products" />
                    <SidebarItem to="#" icon="fa-icons" name="Portfolio" />
                    <SidebarItem to="#" icon="fa-icons" name="Blog" />
                    <SidebarItem to="#" icon="fa-icons" name="Testimonials" />
                </nav>
            </div>
            <div>
                <a href="#" class="sidebar-item">
                    <fa icon="fa-right-from-bracket"></fa>
                    <div>Log out</div>
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import SidebarItem from "@/Components/UI/SidebarItem.vue";
import SidebarItemExpand from "@/Components/UI/SidebarItemExpand.vue";
</script>
