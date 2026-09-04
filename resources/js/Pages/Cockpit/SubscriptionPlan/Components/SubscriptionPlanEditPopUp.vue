<template>
    <div v-if="loading" class="flex justify-center items-center h-48">
        <div class="animate-pulse flex flex-col items-center gap-2">
            <div class="w-8 h-8 border-4 border-main border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-neutral-500">Memuat data paket...</span>
        </div>
    </div>

    <form v-else class="flex flex-col gap-2" @submit.prevent="submit">
        <div class="bg-white border border-slate-200 rounded-lg p-3 flex flex-col gap-2">
            <h4 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-1">Informasi Dasar Paket</h4>
            
            <TextField
                v-model="form.name"
                label="Nama Paket"
                placeholder="cth. Paket Pro"
                :feedback="form.errors.name"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <NumberField
                    v-model="form.price_per_outlet"
                    label="Harga per Outlet / Bulan"
                    placeholder="cth. 129000"
                    :feedback="form.errors.price_per_outlet"
                />

                <NumberField
                    v-model="form.yearly_discount_percent"
                    label="Diskon Tahunan (%)"
                    placeholder="cth. 20"
                    :feedback="form.errors.yearly_discount_percent"
                />
            </div>

            <NumberField
                v-model="form.max_outlet"
                label="Batas Maksimal Outlet (Kosongkan jika Unlimited)"
                placeholder="cth. 10"
                :feedback="form.errors.max_outlet"
            />

            <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-200">
                <div>
                    <div class="text-sm font-medium text-slate-800">Status Paket</div>
                    <div class="text-xs text-slate-500">Paket nonaktif tidak dapat dipilih untuk langganan baru oleh merchant</div>
                </div>
                <Switch v-model="form.is_active" :labeling="form.is_active ? 'Aktif' : 'Nonaktif'" />
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-lg p-3 flex flex-col gap-2">
            <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                <h4 class="text-sm font-bold text-slate-800">Daftar Fitur Paket</h4>
                <button
                    type="button"
                    class="btn btn-outline-main btn-xs"
                    @click="addFeature"
                >
                    <FontAwesomeIcon :icon="faPlus" class="mr-1" />
                    Tambah Fitur
                </button>
            </div>

            <div v-if="form.features && form.features.length" class="space-y-2">
                <div
                    v-for="(feature, index) in form.features"
                    :key="index"
                    class="flex items-start gap-2 p-2 bg-slate-50 border border-slate-200 rounded-lg relative"
                >
                    <div class="flex-1 space-y-2">
                        <TextField
                            v-model="feature.title"
                            placeholder="Judul Fitur (cth. Multi Outlet)"
                            :feedback="form.errors[`features.${index}.title`]"
                        />
                        <TextField
                            v-model="feature.detail"
                            placeholder="Detail Fitur (cth. Kelola banyak outlet)"
                            :feedback="form.errors[`features.${index}.detail`]"
                        />
                    </div>
                    <button
                        type="button"
                        class="text-danger hover:text-danger/80 p-2 text-sm transition-colors"
                        title="Hapus Fitur"
                        @click="removeFeature(index)"
                    >
                        <FontAwesomeIcon :icon="faTrash" />
                    </button>
                </div>
            </div>

            <div v-else class="text-center py-4 text-xs text-slate-400 border border-dashed border-slate-200 rounded-lg">
                Belum ada fitur ditambahkan untuk paket ini.
            </div>
        </div>

        <Teleport v-if="isMounted" to="#popUpFooter">
            <div class="flex justify-end gap-2 w-full">
                <button
                    type="button"
                    class="btn btn-outline-slate-400"
                    :disabled="form.processing"
                    @click="close"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="btn btn-main"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>
        </Teleport>
    </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextField from '@/Components/Form/TextField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import Switch from '@/Components/Form/Switch.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faPlus, faTrash } from '@fortawesome/free-solid-svg-icons';
import { usePopUpStore } from '@/store/popup';
import axios from 'axios';

const props = defineProps({
    planId: {
        type: String,
        required: true,
    },
});

const popUpStore = usePopUpStore();
const isMounted = ref(false);
const loading = ref(true);

const form = useForm({
    name: '',
    price_per_outlet: 0,
    yearly_discount_percent: 0,
    max_outlet: null,
    is_active: true,
    features: [],
});

onMounted(async () => {
    isMounted.value = true;
    try {
        const response = await axios.get(route('cockpit.subscription-plans.show', props.planId));
        const data = response.data;
        form.name = data.name;
        form.price_per_outlet = Number(data.price_per_outlet) || 0;
        form.yearly_discount_percent = Number(data.yearly_discount_percent) || 0;
        form.max_outlet = data.max_outlet !== null ? Number(data.max_outlet) : null;
        form.is_active = Boolean(data.is_active);
        form.features = Array.isArray(data.features) ? data.features.map(f => ({ ...f })) : [];
    } catch (err) {
        console.error('Failed to load plan details:', err);
    } finally {
        loading.value = false;
    }
});

const addFeature = () => {
    if (!form.features) {
        form.features = [];
    }
    form.features.push({
        title: '',
        detail: '',
    });
};

const removeFeature = (index) => {
    form.features.splice(index, 1);
};

const close = () => {
    popUpStore.close();
};

const submit = () => {
    form.put(route('cockpit.subscription-plans.update', props.planId), {
        preserveScroll: true,
        onSuccess: () => {
            popUpStore.close();
        },
    });
};
</script>
