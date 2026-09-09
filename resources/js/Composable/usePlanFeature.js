import { usePage } from '@inertiajs/vue3'
import { useModalStore } from '@/store/notification'
import FeatureLockedModal from '@/Components/Modals/FeatureLockedModal.vue'

export function usePlanFeature() {
    /**
     * Check if the current business has a specific feature.
     * @param {string} featureName - The name of the feature to check.
     * @returns {boolean}
     */
    const hasFeature = (featureName) => {
        const features = usePage().props.auth?.features || []
        return features.includes(featureName)
    }

    /**
     * Check if feature exists, if not, open the locked modal.
     * @param {string} featureName - The name of the feature to check.
     * @returns {boolean} - True if feature exists, False if locked (and opens modal).
     */
    const requireFeature = (featureName) => {
        if (hasFeature(featureName)) {
            return true
        }

        const modalStore = useModalStore()
        modalStore.open({
            component: FeatureLockedModal,
            props: {
                feature: featureName,
            },
            showFooter: false,
            title: 'Fitur Terkunci',
            size: 'max-w-md',
        })
        
        return false
    }

    return {
        hasFeature,
        requireFeature,
    }
}
