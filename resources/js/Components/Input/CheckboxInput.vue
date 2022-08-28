<script setup>
import { ref, computed } from "vue";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
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
    },
});
</script>

<template>
    <div class="block w-full">
        <input
            id="uniqueName"
            type="checkbox"
            :value="value"
            v-model="proxyChecked"
            ref="input"
            class="text-primary-500 rounded border-slate-300 ring-primary-500 outline-primary-500"
        />
        <label
            v-if="label"
            :for="uniqueName"
            class="ml-2 text-sm leading-6 text-slate-700"
        >
            {{ label }}
        </label>
    </div>
</template>
