<script setup>
import { onMounted, ref, computed } from "vue";
import helpers from "@/helpers";

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
    type: {
        type: String,
        default: "text",
    },
    validate: {
        type: Object,
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

const onInput = (event) => {
    emit("update:modelValue", event.target.value);
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
</script>

<template>
    <div>
        <label
            v-if="label"
            :for="uniqueName"
            class="text-sm block font-semibold leading-6 text-gray-900"
        >
            {{ label }}
        </label>
        <input
            :id="uniqueName"
            :type="type"
            ref="input"
            class="mt-2 appearance-none border-0 text-slate-900 bg-white rounded-md block w-full px-3 shadow-sm sm:text-sm focus:outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500 ring-1 ring-slate-200"
            :class="{
                'ring-red-500':
                    hasValidate && validate.$dirty && validate.$invalid,
            }"
            @input="onInput"
            @blur="touchValidate"
        />
        <p
            v-if="hasValidate && validate.$dirty && validate.$invalid"
            class="text-xs text-red-500 mt-2 ml-1"
        >
            {{ validate.$errors[0].$message }}
        </p>
    </div>
</template>
