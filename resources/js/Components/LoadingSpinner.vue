<script setup>
import { MoonLoader } from "vue-spinner/dist/vue-spinner.min.js";
defineProps({
    label: {
        type: String,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div
        class="absolute inset-0 bg-white/[.8] z-50"
        :class="{ hidden: !loading }"
        @wheel.prevent
        @touchmove.prevent
        @scroll.prevent
    >
        <div class="w-full h-full flex items-center justify-center flex-col">
            <MoonLoader :loading="true" color="#1E3A8A"></MoonLoader>
            <p v-if="label" class="mt-3 text-sm font-title text-secondary-500">
                {{ label }}
            </p>
        </div>
    </div>
</template>
