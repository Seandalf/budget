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
    saveLabel: {
        type: String,
        default: "Save",
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
                class="w-full h-full md:h-min md:w-2/3 m-auto bg-white z-50 md:rounded-lg shadow-lg p-6"
            >
                <div
                    class="flex items-center gap-4 p-4 border-b border-gray-100"
                >
                    <div class="flex-1">
                        <h3 class="font-bold text-xl md:text-2xl">
                            <slot name="header" />
                        </h3>
                    </div>

                    <div class="flex-0">
                        <button
                            class="p-1 rounded-full text-gray-800 hover:text-red-600 hover:bg-red-50"
                            @click="closeModal"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="py-8 px-2 border-b border-gray-100">
                    <slot name="body" />
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <Button
                        label="Cancel"
                        buttonStyle="tertiary"
                        @click="closeModal"
                    />

                    <Button
                        :label="saveLabel"
                        buttonStyle="success"
                        @click="saveModal"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
