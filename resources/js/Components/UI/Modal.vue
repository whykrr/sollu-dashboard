<template>
    <div v-if="isVisible" class="modal">
        <div
            class="modal-container"
            :class="{
                'translate-y-0 opacity-100': showModal,
                '-translate-y-12 opacity-0': !showModal,
            }"
        >
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="text-lg font-bold">{{ title }}</h2>
                <button
                    id="closeModalBtn"
                    class="text-gray-500 hover:text-gray-700 focus:outline-none"
                    @click="closeModal"
                >
                    ✖
                </button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <slot></slot>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <slot name="footer"></slot>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";

defineProps({
    title: String,
});

const isVisible = ref(false);
const showModal = ref(false);

onMounted(() => {
    isVisible.value = true;
    setTimeout(() => {
        showModal.value = true;
    }, 50);
});

onBeforeUnmount(() => {
    isVisible.value = false;
});

const emit = defineEmits(["close"]);
const closeModal = () => {
    showModal.value = false;
    setTimeout(() => {
        isVisible.value = false;
        emit("close");
    }, 100);
};
</script>
