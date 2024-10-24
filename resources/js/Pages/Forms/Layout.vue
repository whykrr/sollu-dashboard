<template>
    <Card title="Data List">
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
        <Pagination :links="pagination" :from="11" :to="20" :total="200" />
    </Card>
</template>

<script setup>
import Search from "@/Components/Form/Search.vue";
import Card from "@/Components/UI/Card.vue";
import Pagination from "@/Components/UI/Pagination.vue";
import { ref } from "vue";

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
