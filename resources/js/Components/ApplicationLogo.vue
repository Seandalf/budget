<script setup>
import { computed } from "vue";

const props = defineProps({
    color: {
        type: String,
        default: "primary",
    },
});
</script>

<template>
    <h1
        class="font-logo text-6xl tracking-tight"
        :class="{
            'text-primary': color !== 'white',
            'text-white': color === 'white',
        }"
    >
        Open Budget
    </h1>
</template>
