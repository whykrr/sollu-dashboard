<template>
    <div class="flex flex-col gap-4">
        <Card title="Button Group">
            <div class="btn-group">
                <button class="btn btn-main">Button</button>
                <button class="btn btn-secondary">Button</button>
                <button class="btn btn-success">Button</button>
            </div>
        </Card>

        <Card title="Button Group Outline">
            <div class="btn-group">
                <button class="btn btn-outline-main">Button</button>
                <button class="btn btn-outline-main">Button</button>
                <button class="btn btn-outline-main">Button</button>
            </div>
        </Card>

        <Card title="Button Checkbox">
            <div class="btn-group">
                <input
                    type="checkbox"
                    class="btn-check peer"
                    name="button"
                    id="button1"
                />
                <label class="btn btn-outline-main" for="button1"
                    >Checkbox</label
                >

                <input
                    type="checkbox"
                    class="btn-check peer"
                    name="button"
                    id="button2"
                />
                <label class="btn btn-outline-main" for="button2"
                    >Checkbox</label
                >

                <input
                    type="checkbox"
                    class="btn-check peer"
                    name="button"
                    id="button3"
                />
                <label class="btn btn-outline-main" for="button3"
                    >Checkbox</label
                >
            </div>
        </Card>
        <Card title="Button Radio">
            <div class="btn-group">
                <input
                    type="radio"
                    class="btn-check peer"
                    name="radio-btn"
                    id="radio1"
                />
                <label class="btn btn-outline-main" for="radio1">Radio</label>

                <input
                    type="radio"
                    class="btn-check peer"
                    name="radio-btn"
                    id="radio2"
                />
                <label class="btn btn-outline-main" for="radio2">Radio</label>

                <input
                    type="radio"
                    class="btn-check peer"
                    name="radio-btn"
                    id="radio3"
                />
                <label class="btn btn-outline-main" for="radio3">Radio</label>
            </div>
        </Card>

        <Card title="Button Vertical">
            <div class="btn-group-vertical">
                <input
                    type="radio"
                    class="btn-check peer"
                    name="radio-btn-v"
                    id="vertical1"
                />
                <label class="btn btn-outline-main" for="vertical1"
                    >Radio</label
                >

                <input
                    type="radio"
                    class="btn-check peer"
                    name="radio-btn-v"
                    id="vertical2"
                />
                <label class="btn btn-outline-main" for="vertical2"
                    >Radio</label
                >

                <input
                    type="radio"
                    class="btn-check peer"
                    name="radio-btn-v"
                    id="vertical3"
                />
                <label class="btn btn-outline-main" for="vertical3"
                    >Radio</label
                >
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/UI/Card.vue";
</script>
