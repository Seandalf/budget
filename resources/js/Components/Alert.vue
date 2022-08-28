<script setup>
defineProps({
    type: {
        type: String,
        default: "info",
    },
});
</script>

<template>
    <div
        class="rounded p-4 text-sm font-medium mb-6"
        :class="{
            'bg-red-100 text-red-600': type === 'error',
            'bg-green-100 text-green-600': type === 'success',
        }"
    >
        <slot />
    </div>
</template>
