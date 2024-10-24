<template>
    <div class="hidden sm:flex w-64 md:w-72 h-screen">
        <div
            class="bg-neutral-lighter dark:bg-neutral-darker text-neutral-600 dark:text-neutral-400 w-full flex flex-col justify-between shadow"
        >
            <div class="p-4 pb-6">
                <img
                    src="storage/img/logo-fit-color.png"
                    class="w-28"
                    alt="Sollu"
                />
            </div>
            <div class="flex-1 overflow-y-auto floating-scrollbar">
                <nav class="flex flex-col gap-1 p-4 pt-0">
                    <SidebarItem
                        :to="route('dashboard')"
                        icon="fa-home"
                        name="Dashboard"
                        :is-active="menuActive === 'dashboard'"
                    />

                    <div class="p-0 font-bold text-md">Theme</div>
                    <SidebarItem
                        :to="route('colors')"
                        icon="fa-droplet"
                        name="Colors"
                        :is-active="menuActive === 'colors'"
                    />

                    <SidebarItem
                        :to="route('typography')"
                        icon="fa-font"
                        name="Typography"
                        :is-active="menuActive === 'typography'"
                    />

                    <div class="p-0 font-bold text-md">Components</div>
                    <SidebarItemExpand
                        icon="fa-icons"
                        name="Base"
                        :is-active="menuActive.includes('base.')"
                    >
                        <SidebarItem
                            :to="route('base.cards')"
                            name="Cards"
                            :is-active="menuActive === 'base.cards'"
                        />

                        <SidebarItem
                            :to="route('base.navigation')"
                            name="Navigation & Tabs"
                            :is-active="menuActive === 'base.navigation'"
                        />

                        <SidebarItem
                            :to="route('base.placeholders')"
                            name="Placeholders"
                            :is-active="menuActive === 'base.placeholders'"
                        />

                        <SidebarItem
                            :to="route('base.spinners')"
                            name="Spinners"
                            :is-active="menuActive === 'base.spinners'"
                        />
                        <SidebarItem
                            :to="route('base.tables')"
                            name="Tables"
                            :is-active="menuActive === 'base.tables'"
                        />
                    </SidebarItemExpand>

                    <SidebarItemExpand
                        to="#"
                        icon="fa-arrow-pointer"
                        name="Buttons"
                        :is-active="menuActive.includes('buttons.')"
                    >
                        <SidebarItem
                            :to="route('buttons.buttons')"
                            name="Buttons"
                            :is-active="menuActive === 'buttons.buttons'"
                        />
                        <SidebarItem
                            :to="route('buttons.button-group')"
                            name="Button Group"
                            :is-active="menuActive === 'buttons.button-group'"
                        />
                    </SidebarItemExpand>

                    <SidebarItem
                        :to="route('charts')"
                        icon="fa-chart-line"
                        name="Charts"
                        :is-active="menuActive === 'charts'"
                    />

                    <SidebarItemExpand
                        icon="fa-align-left"
                        name="Forms"
                        :is-active="menuActive.includes('forms.')"
                    >
                        <SidebarItem
                            :to="route('forms.form-control')"
                            name="Form Control"
                            :is-active="menuActive === 'forms.form-control'"
                        />

                        <SidebarItem
                            :to="route('forms.select')"
                            name="Select"
                            :is-active="menuActive === 'forms.select'"
                        />

                        <SidebarItem
                            :to="route('forms.radio')"
                            name="Radio"
                            :is-active="menuActive === 'forms.radio'"
                        />

                        <SidebarItem
                            :to="route('forms.check')"
                            name="Check"
                            :is-active="menuActive === 'forms.check'"
                        />

                        <SidebarItem
                            :to="route('forms.input-group')"
                            name="Input Group"
                            :is-active="menuActive === 'forms.input-group'"
                        />

                        <SidebarItem
                            :to="route('forms.floating-label')"
                            name="Floating Label"
                            :is-active="menuActive === 'forms.floating-label'"
                        />

                        <SidebarItem
                            :to="route('forms.layout')"
                            name="Layout"
                            :is-active="menuActive === 'forms.layout'"
                        />

                        <SidebarItem
                            :to="route('forms.validation')"
                            name="Validation"
                            :is-active="menuActive === 'forms.validation'"
                        />
                    </SidebarItemExpand>

                    <SidebarItemExpand
                        to="#"
                        icon="fa-bell"
                        name="Notification"
                        :is-active="menuActive.includes('notification.')"
                    >
                        <SidebarItem
                            :to="route('notification.alert')"
                            name="Alert"
                            :is-active="menuActive === 'notification.alert'"
                        />

                        <SidebarItem
                            :to="route('notification.modal')"
                            name="Modal"
                            :is-active="menuActive === 'notification.modal'"
                        />

                        <SidebarItem
                            :to="route('notification.badge')"
                            name="Badge"
                            :is-active="menuActive === 'notification.badge'"
                        />

                        <SidebarItem
                            :to="route('notification.toast')"
                            name="Toast"
                            :is-active="menuActive === 'notification.toast'"
                        />
                    </SidebarItemExpand>

                    <SidebarItem
                        :to="route('widgets')"
                        icon="fa-tachograph-digital"
                        name="Widgets"
                        :is-active="menuActive === 'widgets'"
                    />

                    <div class="p-0 font-bold text-md">Extras</div>
                    <SidebarItemExpand icon="fa-file" name="Pages">
                        <SidebarItem :to="route('pages.login')" name="Login" />

                        <SidebarItem
                            :to="route('pages.register')"
                            name="Register"
                        />
                    </SidebarItemExpand>

                    <SidebarItemExpand
                        to="#"
                        icon="fa-file-circle-exclamation"
                        name="Error"
                    >
                        <SidebarItem
                            :to="route('error-pages.error-404')"
                            name="Error 404"
                        />

                        <SidebarItem
                            :to="route('error-pages.error-500')"
                            name="Error 500"
                        />
                    </SidebarItemExpand>
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
import { usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const page = usePage();
const menuActive = computed(() => page.props.menuActive);
</script>
