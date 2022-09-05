<script setup>
import { onMounted, ref, computed, reactive } from "vue";

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
const values = reactive({ value: null });

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

const options = {
    numeral: true,
    numeralThousandsGroupStyle: "thousand",
};

const onlyNumber = (e) => {
    let keyCode = e.keyCode ? e.keyCode : e.which;
    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46 && keyCode !== 189) {
        // 46 is dot, 189 is -
        e.preventDefault();
    }
};
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
        <input
            :id="uniqueName"
            type="text"
            ref="input"
            v-model="values.value"
            :disabled="disabled"
            :placeholder="placeholder"
            class="mt-2 appearance-none h-9 border-0 text-slate-900 bg-white rounded-md block w-full px-3 shadow-sm sm:text-sm focus:outline-none placeholder:text-slate-400 focus:ring-2 focus:ring-primary-500 ring-1 ring-slate-200 disabled:bg-slate-50"
            :class="{
                'ring-red-500':
                    hasValidate && validate.$dirty && validate.$invalid,
            }"
            @input="onInput"
            @blur="touchValidate"
            @keypress="onlyNumber"
        />
        <p
            v-if="hasValidate && validate.$dirty && validate.$invalid"
            class="text-xs text-red-500 mt-2 ml-1"
        >
            {{ validate.$errors[0].$message }}
        </p>
    </div>
</template>
