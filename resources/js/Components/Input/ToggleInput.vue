<script setup>
import { ref, computed } from "vue";

const emit = defineEmits(["update:checked", "update:modelValue"]);

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    modelValue: {
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);

const uniqueName = computed(() => {
    return (Math.random() + 1).toString(36).substring(7);
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit("update:checked", val);
        emit("update:modelValue", val);
    },
});
</script>

<template>
    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
        <span
            v-if="label"
            class="ml-2 font-semibold leading-6 text-gray-900 flex-1"
        >
            {{ label }}
        </span>

        <label
            :for="uniqueName"
            class="inline-flex relative items-center cursor-pointer flex-0"
        >
            <input
                :id="uniqueName"
                type="checkbox"
                :value="modelValue"
                v-model="proxyChecked"
                ref="input"
                :disabled="disabled"
                class="sr-only peer"
                :checked="proxyChecked"
            />

            <div
                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-secondary-300 dark:peer-focus:ring-secondary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-secondary-500"
            ></div>
        </label>
    </div>
</template>
