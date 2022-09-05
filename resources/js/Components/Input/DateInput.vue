<script setup>
import { onMounted, ref, computed, watch, reactive } from "vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    modelValue: {
        type: String,
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
const selectedDate = reactive({ date: null });

const onInput = (event) => {
    emit("update:modelValue", selectedDate.date);
};

const touchValidate = () => {
    if (isValidateProvided()) {
        props.validate.$touch();
    }
};

const uniqueName = computed(() => {
    return (Math.random() + 1).toString(36).substring(7);
});

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

watch(selectedDate, () => {
    touchValidate();
    onInput();
});
</script>

<template>
    <div>
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

        <v-date-picker
            v-model="selectedDate.date"
            :model-config="{ type: 'string', mask: 'D MMM YYYY' }"
        >
            <template v-slot="{ inputValue, inputEvents }">
                <input
                    type="text"
                    :id="uniqueName"
                    ref="input"
                    :disabled="disabled"
                    :placeholder="placeholder"
                    class="mt-2 h-9 appearance-none border-0 text-slate-900 bg-white rounded-md block w-full px-3 shadow-sm sm:text-sm focus:outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500 ring-1 ring-slate-200"
                    :class="{
                        'ring-red-500':
                            hasValidate && validate.$dirty && validate.$invalid,
                    }"
                    :value="selectedDate.date"
                    v-on="inputEvents"
                    @input="onInput"
                    @blur="touchValidate"
                />
            </template>
        </v-date-picker>

        <p
            v-if="hasValidate && validate.$dirty && validate.$invalid"
            class="text-xs text-red-500 mt-2 ml-1"
        >
            {{ validate.$errors[0].$message }}
        </p>
    </div>
</template>
