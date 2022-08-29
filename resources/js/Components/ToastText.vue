<script setup>
import ErrorIcon from "@/Components/ToastIcons/Error.vue";
import WarningIcon from "@/Components/ToastIcons/Warning.vue";
import SuccessIcon from "@/Components/ToastIcons/Success.vue";
import InfoIcon from "@/Components/ToastIcons/Info.vue";

defineProps({
    type: String,
    message: String,
});
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="flex-0">
            <ErrorIcon v-if="type === 'error'" />
            <WarningIcon v-else-if="type === 'warning'" />
            <SuccessIcon v-else-if="type === 'success'" />
            <InfoIcon v-else />
        </div>

        <div class="flex-1">
            <p
                class="text-sm text-slate-700 font-medium font-sans leading-loose"
            >
                {{ message }}
            </p>
        </div>
    </div>
</template>
