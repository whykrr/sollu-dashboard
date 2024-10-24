<template>
    <div class="flex flex-col md:grid md:grid-cols-2 lg:grid-cols-4 gap-2 mb-4">
        <Widget
            title="Number of Students"
            icon="fa-person"
            class="bg-main dark:bg-main-light"
        >
            <p class="text-md">2,468</p>
        </Widget>

        <Widget
            title="Number of Teachers"
            icon="fa-users"
            class="bg-clay dark:bg-clay-light"
        >
            <p class="text-md">245</p>
        </Widget>

        <Widget
            title="Number of Employees"
            icon="fa-user-group"
            class="bg-turquoise dark:bg-turquoise-light"
        >
            <p class="text-md">526</p>
        </Widget>

        <Widget
            title="Total Revenue"
            icon="fa-dollar-sign"
            class="bg-brown dark:bg-brown-light"
        >
            <p class="text-md">$2,324,468</p>
        </Widget>
    </div>

    <div class="grid grid-flow-row lg:grid-cols-4 gap-4 mb-4">
        <div class="col-span-4 lg:col-span-2">
            <Container title="Charts">
                <template #buttons> </template>
            </Container>
        </div>
        <div class="col-span-2 lg:col-span-1">
            <Container title="Pie">
                <template #buttons> </template>
            </Container>
        </div>
        <div class="col-span-2 lg:col-span-1">
            <Container title="Bar">
                <template #buttons> </template>
            </Container>
        </div>
    </div>

    <div class="flex flex-col">
        <Container title="Data List">
            <template #buttons>
                <button class="btn-primary text-sm">
                    <fa icon="fa-plus" />
                    Create
                </button>
                <button class="btn-warning text-sm">
                    <fa icon="fa-file" />
                    Draft
                </button>
                <button class="btn-danger text-sm">
                    <fa icon="fa-trash" />
                    Trash
                </button>
            </template>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <Search
                        label="Search"
                        placeholder="Search"
                        name="search"
                        id="search"
                    />
                </div>
                <div class="col-span-2">
                    <div class="flex flex-row gap-3 justify-end">
                        <div class="text-sm">
                            <span class="mr-2 font-bold">Order</span>
                            <select
                                name=""
                                id=""
                                class="input sm:text-sm text-dark dark:text-light border-r-[0.5px] rounded-r-none"
                            >
                                <option>Name</option>
                                <option>Class</option>
                                <option>Subject</option>
                            </select>
                            <select
                                name=""
                                id=""
                                class="input sm:text-sm text-dark dark:text-light border-l-[0.5px] rounded-l-none"
                            >
                                <option>Asc</option>
                                <option>Desc</option>
                            </select>
                        </div>
                        <div class="text-sm">
                            <span class="mr-2 font-bold">Status</span>
                            <select
                                name=""
                                id=""
                                class="input sm:text-sm text-dark dark:text-light"
                            >
                                <option>Active</option>
                                <option>Not Active</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-row gap-4 justify-end">
                <DataViewSwitcher @is-list="updateView" />
            </div>
            <ListView v-if="listView" />
            <GridView v-else />
            <Pagination :links="pagination" :from="11" :to="20" :total="200" />
        </Container>
    </div>
</template>

<script setup>
import Search from "@/Components/Form/Search.vue";
import Container from "@/Components/UI/Container.vue";
import Pagination from "@/Components/UI/Pagination.vue";
import ListView from "@/Pages/Dashboard/Components/ListView.vue";
import Widget from "@/Components/UI/Widget.vue";
import { ref } from "vue";
import DataViewSwitcher from "./Components/DataViewSwitcher.vue";
import GridView from "./Components/GridView.vue";

const listView = ref(true);

const updateView = (value) => {
    listView.value = value;
};

const pagination = [
    {
        url: "http://learning-vilt.test/realtor/listing?deleted=0&page=1",
        label: "&laquo; Previous",
        active: false,
    },
    {
        url: "http://sollu-cms.test",
        label: "1",
        active: true,
    },
    {
        url: "http://learning-vilt.test/realtor/listing?deleted=0&page=2",
        label: "2",
        active: false,
    },
    {
        url: "http://learning-vilt.test/realtor/listing?deleted=0&page=2",
        label: "Next &raquo;",
        active: false,
    },
];
</script>
