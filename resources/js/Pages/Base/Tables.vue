<template>
    <Card title="Table">
        <TableResponsive>
            <TableHead class="bg-main/10">
                <TableCells class="rounded-l-md">Name</TableCells>
                <TableCells>Class</TableCells>
                <TableCells>Subject</TableCells>
                <TableCells>Email</TableCells>
                <TableCells>Address</TableCells>
                <TableCells>Gender</TableCells>
            </TableHead>
            <TableBody>
                <tr class="h-2"></tr>
                <TableRowLink
                    v-for="i in dataTable"
                    to="#"
                    class="text-nowrap"
                    :key="i.id"
                >
                    <TableCells class="font-bold rounded-l-md">{{
                        i.name
                    }}</TableCells>
                    <TableCells class="text-gray">{{ i.class }}</TableCells>
                    <TableCells class="text-gray">{{ i.subject }}</TableCells>
                    <TableCells class="font-bold">{{ i.email }}</TableCells>
                    <TableCells class="text-gray">{{ i.address }}</TableCells>

                    <TableCells class="text-gray">{{ i.gender }}</TableCells>
                </TableRowLink>
            </TableBody>
        </TableResponsive>
    </Card>
</template>
<script setup>
import TableBody from "@/Components/Table/TableBody.vue";
import TableCells from "@/Components/Table/TableCells.vue";
import TableHead from "@/Components/Table/TableHead.vue";
import TableResponsive from "@/Components/Table/TableResponsive.vue";
import TableRowLink from "@/Components/Table/TableRowLink.vue";
import Card from "@/Components/UI/Card.vue";
const dataTable = [
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
