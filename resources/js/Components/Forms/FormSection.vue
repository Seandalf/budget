<script setup>
import { isEmpty } from "@/helpers";

import Button from "@/Components/Button.vue";

defineProps({
    heading: String,
    description: String,
    hasSave: {
        type: Boolean,
        default: false,
    },
    onSave: {
        type: Function,
        default: () => {},
    },
});
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="col-span-1 py-2">
            <h6
                v-if="!isEmpty(heading)"
                class="font-title text-lg text-secondary-500 font-bold"
            >
                {{ heading }}
            </h6>
            <p v-if="!isEmpty(description)" class="text-slate-500">
                {{ description }}
            </p>
        </div>

        <div class="col-span-1 lg:col-span-2">
            <div class="w-full rounded-lg shadow bg-white overflow-hidden">
                <div class="p-6">
                    <slot />
                </div>

                <div
                    v-if="hasSave"
                    class="border-t border-slate-100 bg-slate-50 p-4 flex justify-end"
                >
                    <Button
                        label="Save"
                        buttonStyle="success"
                        @click="onSave"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
