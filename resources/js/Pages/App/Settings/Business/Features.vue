<template>
    <MainPage>
        <template #header>
            <MainPageHeader title="Personalisasi Fitur" />
        </template>

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-800">Personalisasi Fitur Aplikasi</h2>
            <p class="text-sm text-slate-500 mt-1">
                Aktifkan atau nonaktifkan fitur sesuai dengan kebutuhan usaha Anda. Fitur yang ditandai dengan ikon gembok tidak tersedia pada paket langganan Anda saat ini.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-24">
            <div 
                v-for="(features, groupName) in featureGroups" 
                :key="groupName" 
                class="bg-white rounded-xl border border-slate-200 shadow-xs p-5"
            >
                <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-3 mb-4">
                    {{ groupName }}
                </h3>
                
                <div class="space-y-3">
                    <div 
                        v-for="feature in features" 
                        :key="feature.value" 
                        class="relative p-3.5 rounded-lg border transition-colors"
                        :class="[
                            isAvailable(feature.value) ? 'border-slate-100 bg-slate-50/50 hover:bg-slate-50' : 'border-slate-200 bg-slate-100/50'
                        ]"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium" :class="isAvailable(feature.value) ? 'text-slate-800' : 'text-slate-500'">
                                    {{ feature.label }}
                                </h4>
                                <p class="text-xs mt-1 leading-relaxed" :class="isAvailable(feature.value) ? 'text-slate-500' : 'text-slate-400'">
                                    {{ feature.description }}
                                </p>
                            </div>
                            
                            <div class="mt-0.5 shrink-0">
                                <Switch 
                                    :id="'feature-' + feature.value" 
                                    :model-value="form.features.includes(feature.value)"
                                    :disabled="!isAvailable(feature.value) || form.processing" 
                                    @update:model-value="toggleFeature(feature.value)"
                                />
                            </div>
                        </div>

                        <!-- Custom Lock Overlay if not available -->
                        <div 
                            v-if="!isAvailable(feature.value)" 
                            class="absolute inset-0 z-10 bg-white/50 backdrop-blur-[0.5px] rounded-[inherit] flex items-center justify-end pr-3 cursor-pointer"
                            @click="handleLockedClick(feature.value)"
                        >
                            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold shadow-sm bg-gradient-to-r from-main to-secondary hover:from-main-dark hover:to-secondary-dark text-white transition-all duration-200 hover:scale-105 active:scale-95">
                                <FontAwesomeIcon :icon="faLock" class="w-3 h-3" />
                                <span>{{ isSubscribed ? 'Tingkatkan Paket' : 'Langganan' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Action Bar -->
        <div class="flex justify-end sticky bottom-4 z-20 bg-white/90 backdrop-blur-xs p-4 rounded-xl border border-slate-200 shadow-sm mx-0 lg:mx-0">
            <button
                class="btn btn-main px-6 py-2.5 rounded-lg shadow-sm font-medium flex items-center gap-2"
                :disabled="form.processing"
                @click="save"
            >
                <FontAwesomeIcon :icon="faSave" />
                <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}</span>
            </button>
        </div>
        
    </MainPage>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faSave, faLock } from '@fortawesome/free-solid-svg-icons';

import MainPage from '@/Components/UI/MainPage.vue';
import MainPageHeader from '@/Components/UI/MainPage/MainPageHeader.vue';
import Switch from '@/Components/Form/Switch.vue';
import FeatureLockedModal from '@/Components/Modals/FeatureLockedModal.vue';
import { useModalStore } from '@/store/notification';

const props = defineProps({
    availableFeatures: Array,
    activeFeatures: Array,
    featureGroups: Object,
});

const page = usePage();
const auth = computed(() => page.props.auth);
const isSubscribed = computed(() => Boolean(auth.value?.subscription && auth.value?.subscription?.status === 'active'));

const form = useForm({
    features: [...props.activeFeatures],
});

const isAvailable = (featureValue) => {
    return props.availableFeatures.includes(featureValue);
};

const toggleFeature = (featureValue) => {
    if (!isAvailable(featureValue)) return;
    
    const index = form.features.indexOf(featureValue);
    if (index === -1) {
        form.features.push(featureValue);
    } else {
        form.features.splice(index, 1);
    }
};

const save = () => {
    form.put(route('settings.business.features.save'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const modalStore = useModalStore();

const handleLockedClick = (featureName) => {
    modalStore.open({
        component: FeatureLockedModal,
        props: {
            feature: featureName,
        },
        showFooter: false,
        title: 'Fitur Terkunci',
        size: 'max-w-md',
    });
};
</script>
