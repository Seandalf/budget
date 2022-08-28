<script setup>
import Alert from "@/Components/Alert.vue";

defineProps({
    errors: {
        type: [Array, Object],
        default: [],
    },
});
</script>

<template>
    <div v-for="(error, index) in errors" :key="`error-${index}`">
        <Alert type="error">
            {{ error }}
        </Alert>
    </div>
</template>
