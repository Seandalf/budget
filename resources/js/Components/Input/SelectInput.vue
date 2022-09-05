<script setup>
import { onMounted, ref, computed, reactive } from "vue";
import { isEmpty } from "@/helpers";

import SelectOption from "@/Classes/SelectOption";
import SelectDropdown from "@/Components/Input/SelectDropdown.vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    modelValue: {
        type: [String, Number],
        default: null,
    },
    validate: {
        type: Object,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array[SelectOption],
        default: () => [],
    },
    multiselect: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: null,
    },
});

const isValidateProvided = () => {
    return (
        props.validate !== null &&
        props.validate.hasOwnProperty("$dirty") &&
        props.validate.hasOwnProperty("$invalid")
    );
};

const hasValidate = computed(() => {
    return isValidateProvided();
});

const input = ref(null);
const settings = reactive({
    selectedOptions: [],
    selectedValues: [],
    showDropdown: false,
});

const touchValidate = () => {
    if (isValidateProvided()) {
        props.validate.$touch();
    }
};

const toggleDropdown = () => {
    settings.showDropdown = !settings.showDropdown;
};

const optionSelected = (event) => {
    if (!props.multiselect) {
        settings.selectedOptions = [];
        settings.selectedValues = [];
    }
    settings.selectedOptions.push(event);
    settings.selectedValues.push(event.value);

    emit(
        "update:modelValue",
        props.multiselect ? settings.selectedValues : settings.selectedValues[0]
    );
};

const removeOption = (index) => {
    settings.selectedOptions.splice(index, 1);
    settings.selectedValues.splice(index, 1);

    if (settings.selectedValues.length === 0) {
        emit("update:modelValue", null);
    } else {
        emit(
            "update:modelValue",
            props.multiselect
                ? settings.selectedValues
                : settings.selectedValues[0]
        );
    }
};

const uniqueName = computed(() => {
    return (Math.random() + 1).toString(36).substring(7);
});
</script>

<template>
    <div class="relative overflow-visible">
        <label
            v-if="label"
            :for="uniqueName"
            class="text-sm block font-semibold leading-6 text-gray-900"
        >
            {{ label }}
            <span
                v-if="hasValidate && validate.hasOwnProperty('required')"
                class="text-red-500 font-bold"
            >
                *
            </span>
        </label>

        <div
            class="flex items-center divide-x divide-slate-200 mt-2"
            @click="toggleDropdown"
        >
            <div
                class="py-[6px] overflow-x-clip border-0 text-slate-900 bg-white rounded-md rounded-r-none block w-full px-3 shadow-sm sm:text-sm focus:outline-none ring-1 ring-slate-200 h-9 cursor-pointer relative"
                :class="{
                    'rounded-b-none': settings.showDropdown,
                    'bg-slate-50 pointer-events-none': disabled,
                }"
            >
                <p
                    v-if="
                        !isEmpty(placeholder) &&
                        settings.selectedOptions.length === 0
                    "
                    class="text-[#94A3B8] mt-[3px] truncate"
                >
                    {{ placeholder }}
                </p>

                <div class="flex items-center gap-1 flex-1">
                    <div
                        v-for="(option, index) in settings.selectedOptions"
                        :key="`selected-option-${index}-${option.value}`"
                        class="flex items-center gap-1 px-2 bg-slate-100 text-xs py-[3px] text-slate-600 border-slate-200/80 border rounded"
                        @click.prevent.stop
                    >
                        <button
                            class="text-primary-500 hover:text-primary-300 flex-0"
                            @click="removeOption(index)"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="w-3 h-3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>

                        <p class="truncate text-ellipsis">{{ option.name }}</p>
                    </div>
                </div>
            </div>

            <button
                class="flex-0 h-9 transition ease-in-out px-2 rounded-r-md ring-1 ring-slate-200 hover:bg-primary-500 hover:text-white hover:ring-primary-600"
                :class="{
                    'bg-primary-500 text-white ring-primary-600 hover:bg-primary-400 hover:ring-primary-500 rounded-b-none':
                        settings.showDropdown,
                    'text-slate-500 bg-white': !settings.showDropdown,
                    'bg-slate-50 pointer-events-none': disabled,
                }"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="w-4 h-4 transition-transform ease-in-out duration-300"
                    :class="{ 'rotate-180': settings.showDropdown }"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                    />
                </svg>
            </button>
        </div>

        <SelectDropdown
            v-if="settings.showDropdown"
            :options="options"
            v-on:selected="optionSelected"
            v-on:close="toggleDropdown"
            :multiselect="multiselect"
        />

        <p
            v-if="hasValidate && validate.$dirty && validate.$invalid"
            class="text-xs text-red-500 mt-2 ml-1"
        >
            {{ validate.$errors[0].$message }}
        </p>
    </div>
</template>
