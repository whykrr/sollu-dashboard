<template>
    <div class="flex flex-col gap-4">
        <Card title="Button">
            <div class="flex flex-row gap-1">
                <button class="btn btn-main">Button</button>
                <button class="btn btn-secondary">Button</button>
                <button class="btn btn-success">Button</button>
                <button class="btn btn-info">Button</button>
                <button class="btn btn-warning">Button</button>
                <button class="btn btn-danger">Button</button>
                <a href="#" class="btn link">Button</a>
                <button class="btn" disabled>Button</button>
            </div>
        </Card>

        <Card title="Button Outline">
            <div class="flex flex-row gap-1">
                <button class="btn-outline btn-main">Button Outline</button>
                <button class="btn-outline btn-secondary">
                    Button Outline
                </button>
                <button class="btn-outline btn-success">Button Outline</button>
                <button class="btn-outline btn-info">Button Outline</button>
                <button class="btn-outline btn-warning">Button Outline</button>
                <button class="btn-outline btn-danger">Button Outline</button>
                <a href="#" class="btn-outline link">Button Outline</a>
                <button class="btn-outline" disabled>Button Outline</button>
            </div>
        </Card>

        <Card title="Button with Icon">
            <div class="flex flex-row gap-1 mb-2">
                <button class="btn btn-main">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn btn-secondary">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn btn-success">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn btn-info">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn btn-warning">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn btn-danger">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <a href="#" class="btn link">
                    <fa icon="fa-plus" />
                    Btn Icon
                </a>
                <button class="btn" disabled>
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
            </div>

            <div class="flex flex-row gap-1">
                <button class="btn-outline btn-main">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn-outline btn-secondary">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn-outline btn-success">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn-outline btn-info">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn-outline btn-warning">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <button class="btn-outline btn-danger">
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
                <a href="#" class="btn-outline link">
                    <fa icon="fa-plus" />
                    Btn Icon
                </a>
                <button class="btn-outline" disabled>
                    <fa icon="fa-plus" />
                    Btn Icon
                </button>
            </div>
        </Card>

        <Card title="Button Block">
            <div class="flex flex-col gap-2">
                <button class="btn btn-main block">Button</button>
                <button class="btn-outline btn-main block">Button</button>
                <button class="btn-outline btn-main block">
                    <fa icon="fa-plus" />
                    Button
                </button>
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/UI/Card.vue";
</script>
