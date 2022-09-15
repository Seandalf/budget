<script setup>
import moment from "moment";

import Button from "@/Components/Button.vue";
import IntervalStatistics from "./IntervalStatistics.vue";
import IntervalTable from "./IntervalTable.vue";

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
        <h4 class="font-bold text-slate-700 text-lg">
            Period:
            <span class="text-secondary-500">{{
                moment(interval.starts_at).format("Do MMM YYYY")
            }}</span>
            <span class="text-sm mx-2 text-slate-400">to</span>
            <span class="text-secondary-500">{{
                moment(interval.ends_at).format("Do MMM YYYY")
            }}</span>
        </h4>
        <p
            v-if="interval.statistics.is_current"
            class="mt-1 uppercase tracking-wide font-medium text-slate-500 text-xs"
        >
            {{ moment().to(moment(interval.ends_at), true) }}
            remaining
        </p>
    </div>

    <div class="mt-8">
        <IntervalStatistics :interval="interval" />
    </div>

    <div class="mt-8">
        <IntervalTable :categories="interval.category_breakdown" />
    </div>
</template>
