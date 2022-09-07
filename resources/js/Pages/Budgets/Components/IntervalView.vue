<script setup>
const props = defineProps({
    interval: {
        type: Object,
        default: () => {},
    },
});
</script>

<template>
    <div>
        {{ interval }}
    </div>
</template>
