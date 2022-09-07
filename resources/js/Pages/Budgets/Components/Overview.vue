<script setup>
const props = defineProps({
    intervals: {
        type: [Array, Object],
    },
});
</script>

<template>
    <div>
        {{ intervals }}
    </div>
</template>
