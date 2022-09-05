<script setup>
import { reactive, computed } from "vue";
import { isEmpty } from "@/helpers";

import TextInput from "@/Components/Input/TextInput.vue";

const emit = defineEmits(["selected", "close"]);
const props = defineProps({
    options: {
        type: Array,
        default: () => [],
    },
    multiselect: {
        type: Boolean,
        default: false,
    },
});

const randomNum = Math.random();

const settings = reactive({
    filter: null,
});

const selectOption = (option) => {
    emit("selected", option);

    if (!props.multiselect) {
        emit("close");
    }
};

const availableOptions = computed(() => {
    if (isEmpty(settings.filter)) {
        return props.options;
    }

    return props.options.filter((option) =>
        option.name.toLowerCase().includes(settings.filter.toLowerCase())
    );
});
</script>

<template>
    <div
        class="absolute w-full flex flex-col gap-2 bg-white ring-1 ring-slate-200 rounded-b py-2 pl-3 pr-1 overflow-y-scroll max-h-[150px] z-50"
    >
        <TextInput
            v-model="settings.filter"
            class="mb-2"
            placeholder="Search..."
            small
        />
        <button
            v-for="option in availableOptions"
            :key="`option-${option.name.replace(' ', '-')}-${
                option.value
            }-${randomNum}`"
            class="text-left py-1 px-1 rounded hover:bg-slate-50"
            @click="selectOption(option)"
        >
            {{ option.name }}
        </button>
    </div>
</template>
