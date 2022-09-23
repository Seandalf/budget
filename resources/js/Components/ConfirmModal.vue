<script setup>
import Button from "@/Components/Button.vue";

const emits = defineEmits(["closed", "success"]);

const props = defineProps({
    closeOnEsc: {
        type: Boolean,
        default: false,
    },
    closeOnOverlay: {
        type: Boolean,
        default: false,
    },
    showOverlay: {
        type: Boolean,
        default: true,
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
    message: {
        type: String,
        default: "Are you sure?",
    },
});

const onClickOverlay = () => {
    if (!props.closeOnOverlay) {
        return;
    }

    closeModal();
};

const closeModal = () => {
    emits("closed");
};

const saveModal = () => {
    emits("success");
};
</script>

<template>
    <div v-if="isOpen" class="z-40 fixed inset-0">
        <div class="w-full h-full relative -z-20 flex items-center">
            <div
                class="w-full h-full absolute -z-10"
                :class="{
                    'cursor-pointer': closeOnOverlay,
                    'bg-gray-800/20 backdrop-blur-sm': showOverlay,
                }"
                @click="onClickOverlay"
            ></div>
            <div
                class="w-full h-full md:h-min md:max-w-[400px] m-auto bg-white z-50 md:rounded-lg shadow-lg p-6"
            >
                <div
                    class="py-8 px-2 border-b border-gray-100 text-center"
                    v-html="message"
                ></div>

                <div class="mt-8 grid grid-cols-2 gap-4">
                    <Button
                        label="Cancel"
                        buttonStyle="tertiary"
                        @click="closeModal"
                        fullWidth
                    />

                    <Button
                        label="Confirm"
                        buttonStyle="success"
                        @click="saveModal"
                        fullWidth
                    />
                </div>
            </div>
        </div>
    </div>
</template>
