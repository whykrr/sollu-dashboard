<template>
    <MainPage>
        <template #header>
            <div class="flex flex-row justify-between gap-2">
                <div class="flex items-center gap-2">
                    <FilterSearch v-model="filterForm.search" placeholder="Search name, email, ID..." class="w-64" />
                    <FilterStatus v-model="filterForm.status" />
                </div>
            </div>
        </template>

        <Table
            :headers="tableHeaders"
            :data="businesses.data"
            :action="true"
            :sort="filters.sort"
            :sort-direction="filters.direction"
        >
            <template #owner_name="{ row }">
                {{ row.owner_name || '-' }}
            </template>
            <template #name="{ row }">
                <div>
                    <div class="font-medium text-neutral-800">
                        {{ row.name }}
                    </div>
                    <div class="text-xs text-neutral-500">{{ row.email }}</div>
                </div>
            </template>
            <template #outlets="{ row }">
                {{ row.outlets_count }} Outlets
            </template>
            <template #status="{ row }">
                <span
                    v-if="row.status === 'active'"
                    class="px-2 py-1 bg-success/10 text-success text-xs rounded-full font-medium"
                >
                    Active
                </span>
                <span
                    v-else
                    class="px-2 py-1 bg-danger/10 text-danger text-xs rounded-full font-medium"
                >
                    Suspended
                </span>
            </template>
            <template #created_at="{ row }">
                {{ new Date(row.created_at).toLocaleDateString() }}
            </template>
            <template #last_login_at="{ row }">
                <span v-if="row.users_max_last_login_at" class="text-xs text-neutral-600">
                    {{ new Date(row.users_max_last_login_at).toLocaleDateString() }}
                </span>
                <span v-else class="text-xs text-neutral-400">-</span>
            </template>
            <template #actions="{ row }">
                <button
                    class="btn btn-neutral-100 text-neutral-600 btn-sm"
                    title="View Detail"
                    @click="openDetail(row.id)"
                >
                    <FontAwesomeIcon :icon="faEye" />
                </button>
                <button
                    v-if="row.status === 'active'"
                    class="btn btn-danger/10 text-danger btn-sm"
                    title="Suspend"
                    @click="toggleStatus(row.id, 'suspended')"
                >
                    <FontAwesomeIcon :icon="faBan" />
                </button>
                <button
                    v-else
                    class="btn btn-success/10 text-success btn-sm"
                    title="Activate"
                    @click="toggleStatus(row.id, 'active')"
                >
                    <FontAwesomeIcon :icon="faCheck" />
                </button>
            </template>
        </Table>

        <template #footer>
            <Pagination
                :links="businesses.links"
                :from="businesses.from"
                :to="businesses.to"
                :total="businesses.total"
                :per-page="businesses.per_page"
            />
        </template>
    </MainPage>
</template>

<script setup>
import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

import MainPage from '@/Components/UI/MainPage.vue';
import Table from '@/Components/Tables/Table.vue';
import Pagination from '@/Components/Tables/Pagination.vue';
import FilterSearch from '@/Components/UI/Filter/FilterSearch.vue';
import FilterStatus from '@/Components/UI/Filter/FilterStatus.vue';
import BusinessDetailPopUp from './Components/BusinessDetailPopUp.vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faBan, faCheck } from '@fortawesome/free-solid-svg-icons';
import { usePopUpStore } from '@/store/popup';

const props = defineProps({
    businesses: Object,
    filters: Object,
});

const popUpStore = usePopUpStore();

const tableHeaders = [
    { field: 'name', label: 'Merchant', slot: 'name', sortable: true },
    { field: 'owner_name', label: 'Owner', slot: 'owner_name', sortable: true },
    { field: 'outlets', label: 'Outlets', slot: 'outlets' },
    { field: 'status', label: 'Status', slot: 'status', sortable: true },
    {
        field: 'created_at',
        label: 'Joined At',
        slot: 'created_at',
        sortable: true,
    },
    { field: 'last_login_at', label: 'Last Activity', slot: 'last_login_at' },
];

const filterForm = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const updateQuery = () => {
    const query = { ...route().params, ...filterForm };
    
    Object.keys(query).forEach((key) => {
        if (query[key] === '' || query[key] === null || query[key] === undefined) {
            delete query[key];
        }
    });

    query.page = 1;

    router.get(location.pathname, query, {
        preserveState: true,
        preserveScroll: true,
    });
};

watch(
    () => filterForm.search,
    debounce(() => {
        updateQuery();
    }, 500)
);

watch(
    () => filterForm.status,
    () => {
        updateQuery();
    }
);

const openDetail = (id) => {
    popUpStore.open({
        title: 'Detail Merchant',
        size: 'xl',
        component: BusinessDetailPopUp,
        props: { businessId: id },
    });
};

const toggleStatus = (id, newStatus) => {
    if (
        confirm(
            `Are you sure you want to change this merchant's status to ${newStatus}?`,
        )
    ) {
        router.post(
            route('cockpit.merchants.toggle-status', id),
            {
                status: newStatus,
            },
            {
                preserveScroll: true,
            },
        );
    }
};
</script>
