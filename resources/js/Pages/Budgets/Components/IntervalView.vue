<script setup>
import moment from "moment";

import Button from "@/Components/Button.vue";
import IntervalStatistics from "./IntervalStatistics.vue";

const emit = defineEmits(["previousInterval", "nextInterval", "overview"]);
const props = defineProps({
    interval: {
        type: Object,
        default: () => {},
    },
    lastInterval: {
        type: Boolean,
        default: false,
    },
    currency: {
        type: String,
        default: "AUD",
    },
});
</script>

<template>
    <div class="flex items-center gap-4">
        <div class="flex-1 flex items-center">
            <Button
                label="Budget Overview"
                buttonStyle="black"
                outline
                @click="$emit('overview')"
            />
        </div>

        <div class="flex-1 flex items-center justify-end gap-4">
            <Button
                v-if="!interval.statistics.is_first"
                label="Previous Interval"
                buttonStyle="white"
                icon="left-chevron"
                @click="$emit('previousInterval')"
                labelSrOnly
            />

            <Button
                v-if="!lastInterval"
                label="Next Interval"
                buttonStyle="white"
                icon="right-chevron"
                @click="$emit('nextInterval')"
                labelSrOnly
            />
        </div>
    </div>

    <div class="mt-8">
        <h4 class="font-title font-bold text-slate-700 text-lg">
            Period starting:
            <span class="text-secondary-500">{{
                moment(interval.starts_at).format("Do MMM YYYY")
            }}</span>
        </h4>
        <p
            class="mt-1 uppercase tracking-wide font-medium text-slate-500 text-xs"
        >
            {{ moment(interval.starts_at).to(moment(interval.ends_at), true) }}
            remaining
        </p>
    </div>

    <div class="mt-6 grid grid-cols-4 gap-10">
        <div class="col-span-4 1.5xl:col-span-1">
            <IntervalStatistics :interval="interval" />
        </div>

        <div class="col-span-4 1.5xl:col-span-3">hi</div>
    </div>
</template>
