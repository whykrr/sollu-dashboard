<template>
    <div class="flex flex-col gap-4">
        <Card title="Base Cards">
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa,
            accusamus amet. Unde culpa ad, expedita rem necessitatibus atque! Ex
            pariatur natus laboriosam sequi quisquam eos commodi modi enim ut
            dicta?
        </Card>
        <Card title="Cards with Buttons">
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
            <p>
                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa,
                accusamus amet. Unde culpa ad, expedita rem necessitatibus
                atque! Ex pariatur natus laboriosam sequi quisquam eos commodi
                modi enim ut dicta?
            </p>
        </Card>
    </div>
</template>

<script setup>
import Card from "@/Components/UI/Card.vue";
</script>
