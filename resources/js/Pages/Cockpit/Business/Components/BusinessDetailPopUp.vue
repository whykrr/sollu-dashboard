<template>
    <div v-if="loading" class="flex justify-center items-center h-48">
        <div class="animate-pulse flex flex-col items-center gap-2">
            <div class="w-8 h-8 border-4 border-main border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-neutral-500">Memuat detail...</span>
        </div>
    </div>
    
    <div v-else-if="business">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl shadow-sm border border-neutral-200/60 gap-4 mb-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-neutral-100 rounded-xl flex items-center justify-center text-2xl font-bold text-neutral-400 border border-neutral-200 overflow-hidden">
                    <img v-if="business.logo_url" :src="business.logo_url" alt="Logo" class="w-full h-full object-cover" />
                    <span v-else>{{ business.name.substring(0, 2).toUpperCase() }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-neutral-800">{{ business.name }}</h1>
                        <span v-if="business.status === 'active'" class="px-2 py-0.5 bg-success/10 text-success text-xs rounded-full font-medium">Active</span>
                        <span v-else class="px-2 py-0.5 bg-danger/10 text-danger text-xs rounded-full font-medium">Suspended</span>
                    </div>
                    <div class="text-sm text-neutral-500 mt-1">
                        ID: <span class="font-mono">{{ business.id.substring(0, 8) }}</span>
                        • Joined: {{ new Date(business.created_at).toLocaleDateString() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200/60 mb-4">
            <div class="border-b border-neutral-200 px-4">
                <div class="flex gap-6 overflow-x-auto hide-scrollbar">
                    <button class="px-1 py-3 text-sm font-medium border-b-2" :class="activeTab === 'overview' ? 'border-main text-main' : 'border-transparent text-neutral-500 hover:text-neutral-800'" @click="activeTab = 'overview'">Overview</button>
                    <button class="px-1 py-3 text-sm font-medium border-b-2" :class="activeTab === 'users' ? 'border-main text-main' : 'border-transparent text-neutral-500 hover:text-neutral-800'" @click="activeTab = 'users'">Users ({{ business.users_count }})</button>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'overview'" class="grid grid-cols-1 gap-4">
            <div class="bg-white border border-neutral-200/60 shadow-sm rounded-lg p-4">
                <h3 class="font-bold text-neutral-800 mb-4">Merchant Profile</h3>
                <div class="flex flex-col gap-3">
                    <div>
                        <div class="text-xs text-neutral-500">Business Name</div>
                        <div class="text-sm font-medium text-neutral-800">{{ business.name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-neutral-500">Owner Name</div>
                        <div class="text-sm font-medium text-neutral-800">{{ business.owner_name || '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-neutral-500">Email</div>
                        <div class="text-sm font-medium text-neutral-800">{{ business.email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-neutral-500">Phone</div>
                        <div class="text-sm font-medium text-neutral-800">{{ business.phone || '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-neutral-500">Address</div>
                        <div class="text-sm font-medium text-neutral-800">{{ business.address || '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-neutral-500">Business Type</div>
                        <div class="text-sm font-medium text-neutral-800">{{ business.type?.name || '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div class="bg-main/5 border border-main/20 rounded-lg p-4">
                    <h3 class="font-bold text-neutral-800 mb-2">Current Subscription</h3>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xl font-bold text-main">Trial / Free Plan</span>
                        <span class="text-xs font-medium bg-white px-2 py-1 rounded text-neutral-600 border border-neutral-200">Default</span>
                    </div>
                    <div class="text-sm text-neutral-600 mb-4">
                        Trial ends: {{ business.trial_end_at ? new Date(business.trial_end_at).toLocaleDateString() : 'Lifetime' }}
                    </div>
                </div>

                <div class="bg-white shadow-sm border border-neutral-200/60 rounded-lg p-4 flex gap-4">
                    <div class="flex-1 text-center border-r border-neutral-200">
                        <div class="text-2xl font-bold text-neutral-800">{{ business.outlets_count }}</div>
                        <div class="text-xs text-neutral-500">Active Outlets</div>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="text-2xl font-bold text-neutral-800">{{ business.users_count }}</div>
                        <div class="text-xs text-neutral-500">Registered Users</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="activeTab === 'users'" class="grid grid-cols-1 gap-4">
            <div class="bg-white border border-neutral-200/60 shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-left text-sm text-neutral-600">
                    <thead class="bg-neutral-50 text-neutral-700 text-xs uppercase border-b border-neutral-200">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        <tr v-for="user in business.users" :key="user.id" class="hover:bg-neutral-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-neutral-800">{{ user.name }}</div>
                                <div class="text-xs text-neutral-500">{{ user.email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span v-if="user.roles && user.roles.length" class="px-2 py-1 bg-neutral-100 text-neutral-600 text-xs rounded-full font-medium">
                                    {{ user.roles[0].name }}
                                </span>
                                <span v-else class="text-neutral-400">-</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a :href="route('cockpit.merchants.impersonate', { id: business.id, userId: user.id })" target="_blank" class="btn btn-outline-main btn-sm inline-flex items-center gap-2">
                                    <FontAwesomeIcon :icon="faRightToBracket" />
                                    Login as User
                                </a>
                            </td>
                        </tr>
                        <tr v-if="!business.users || business.users.length === 0">
                            <td colspan="3" class="px-4 py-8 text-center text-neutral-500">Tidak ada user ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Teleport v-if="isMounted" to="#popUpFooter">
            <button type="button" class="btn btn-slate-400" @click="close">Tutup</button>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faRightToBracket } from '@fortawesome/free-solid-svg-icons';
import { usePopUpStore } from '@/store/popup';
import axios from 'axios';

const props = defineProps({
    businessId: {
        type: String,
        required: true,
    },
});

const popUpStore = usePopUpStore();
const business = ref(null);
const loading = ref(true);
const activeTab = ref('overview');
const isMounted = ref(false);

onMounted(async () => {
    isMounted.value = true;
    try {
        const response = await axios.get(route('cockpit.merchants.show', props.businessId));
        business.value = response.data;
    } catch (error) {
        console.error('Failed to load business details:', error);
    } finally {
        loading.value = false;
    }
});

const close = () => {
    popUpStore.close();
};
</script>
