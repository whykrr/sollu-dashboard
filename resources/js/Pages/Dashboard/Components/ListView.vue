<template>
    <TableResponsive>
        <TableHead>
            <TableRowHead class="text-left">
                <TableCells class="rounded-l-md">Name</TableCells>
                <TableCells>Class</TableCells>
                <TableCells>Status</TableCells>
                <TableCells>Status</TableCells>
                <TableCells>Subject</TableCells>
                <TableCells>Email</TableCells>
                <TableCells>Address</TableCells>
                <TableCells>Gender</TableCells>
                <TableCells class="rounded-r-md">Action</TableCells>
            </TableRowHead>
        </TableHead>
        <TableBody>
            <tr class="h-2"></tr>
            <TableRowLink
                v-for="i in data"
                :to="route('blank')"
                class="text-nowrap"
                :key="i.id"
            >
                <TableCells class="font-bold rounded-l-md">{{
                    i.name
                }}</TableCells>
                <TableCells class="text-gray">{{ i.class }}</TableCells>
                <TableCells>
                    <div v-if="i.isActive" class="label label-success">
                        Active
                    </div>
                    <div v-else class="label label-danger">Active</div>
                </TableCells>
                <TableCells>
                    <DotedLabel v-if="i.isActive" class="doted-label-success"
                        >Active</DotedLabel
                    >
                    <DotedLabel v-else class="doted-label-danger"
                        >Not Active</DotedLabel
                    >
                </TableCells>
                <TableCells class="text-gray">{{ i.subject }}</TableCells>
                <TableCells class="font-bold">{{ i.email }}</TableCells>
                <TableCells class="text-gray">{{ i.address }}</TableCells>

                <TableCells class="text-gray">{{ i.gender }}</TableCells>
                <TableCells class="rounded-r-md">
                    <button class="btn-danger text-sm">
                        <fa icon="fa-trash" />
                    </button>
                </TableCells>
            </TableRowLink>
        </TableBody>
    </TableResponsive>
</template>

<script setup>
import DotedLabel from "@/Components/Labels/DotedLabel.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import TableCells from "@/Components/Table/TableCells.vue";
import TableHead from "@/Components/Table/TableHead.vue";
import TableResponsive from "@/Components/Table/TableResponsive.vue";
import TableRowHead from "@/Components/Table/TableRowHead.vue";
import TableRowLink from "@/Components/Table/TableRowLink.vue";

const data = [
    {
        id: 1,
        name: "Morris Johnson",
        class: "Class 9",
        subject: "English",
        email: "example@site.com",
        address: "Samantha Street 27, District John",
        gender: "Male",
        isActive: true,
    },
    {
        id: 2,
        name: "Morris Johnson",
        class: "Class 2",
        subject: "Korea",
        email: "example@site.com",
        address: "Samantha Street 27, District John",
        gender: "Male",
        isActive: true,
    },
    {
        id: 3,
        name: "Morris Johnson",
        class: "Class 5",
        subject: "Thailand",
        email: "example@site.com",
        address: "Samantha Street 27, District John",
        gender: "Male",
        isActive: true,
    },
    {
        id: 4,
        name: "Morris Johnson",
        class: "Class 7",
        subject: "Japan",
        email: "example@site.com",
        address: "Samantha Street 27, District John",
        gender: "Male",
        isActive: true,
    },
    {
        id: 5,
        name: "Morris Johnson",
        class: "Class 10",
        subject: "Zimbabwe",
        email: "example@site.com",
        address: "Samantha Street 27, District John",
        gender: "Male",
        isActive: false,
    },
];
</script>
