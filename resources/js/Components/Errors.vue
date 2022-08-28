<script setup>
defineProps({
    errors: {
        type: [Array, Object],
        default: [],
    },
});
</script>

<template>
    <div
        v-for="(error, index) in errors"
        :key="`error-${index}`"
        class="rounded p-4 bg-red-100 text-red-600 text-sm font-medium mb-6"
    >
        {{ error }}
    </div>
</template>
