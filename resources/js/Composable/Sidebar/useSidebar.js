import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useMainSidebar } from './main'
import { useSettingSidebar } from './setting'

export function useSidebar() {
    const page = usePage()
    const { mainSidebars } = useMainSidebar()
    const { settingSidebars } = useSettingSidebar()

    const sidebars = computed(() => {
        const url = page.url || ''
        return url.startsWith('/settings') ? settingSidebars.value : mainSidebars.value
    })

    return {
        sidebars,
        mainSidebars,
        settingSidebars,
    }
}
