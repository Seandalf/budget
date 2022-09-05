<script setup>
import { computed, reactive, watch } from "vue";
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import { userHasPermission, isEmpty, capitalise } from "@/helpers";
import moment from "moment";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import CheckboxInput from "@/Components/Input/CheckboxInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import NumericInput from "@/Components/Input/NumericInput.vue";
import CurrencyInput from "@/Components/Input/CurrencyInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => {},
    },
    validation: Object,
});

const form = reactive(props.modelValue);

const timePeriodOptions = computed(() => {
    const timePeriods = usePage().props.value.timePeriods;
    let options = [];
    let periods = [];
    const validPeriods = timePeriods.filter(
        (period) =>
            period.name === "day" ||
            period.name === "week" ||
            period.name === "month" ||
            period.name === "quarter" ||
            period.name === "year"
    );

    for (const period in validPeriods) {
        periods.push(validPeriods[period].id);
    }

    for (const period in timePeriods) {
        const current = timePeriods[period];
        const capital = capitalise(timePeriods[period].name);

        options.push({
            name: periods.includes(current.id)
                ? `Every X ${capital}(s)`
                : capital,
            value: timePeriods[period].id,
        });
    }

    return options;
});

const currencyOptions = computed(() => {
    const currencies = usePage().props.value.currencies;
    let options = [];

    for (const currency in currencies) {
        options.push({
            name: currencies[currency].name,
            value: currencies[currency].id,
        });
    }

    return options;
});

const selectedTimePeriodName = computed(() => {
    if (isEmpty(form.time_period_id)) {
        return null;
    }

    const timePeriods = usePage().props.value.timePeriods;
    const timePeriod = timePeriods.find(
        (period) => period.id === form.time_period_id
    );

    switch (timePeriod.name) {
        case "daily":
        case "day":
            return "Day(s)";
        case "weekly":
        case "week":
            return "Week(s)";
        case "monthly":
        case "month":
            return "Month(s)";
        case "quarterly":
        case "quarter":
            return "Quarter(s)";
        case "yearly":
        case "year":
            return "Year(s)";
    }
});

const showCustomInterval = computed(() => {
    const timePeriods = usePage().props.value.timePeriods;
    let periods = [];
    const validPeriods = timePeriods.filter(
        (period) =>
            period.name === "day" ||
            period.name === "week" ||
            period.name === "month" ||
            period.name === "quarter" ||
            period.name === "year"
    );

    for (const period in validPeriods) {
        periods.push(validPeriods[period].id);
    }

    return periods.includes(form.time_period_id);
});

watch(form, (val) => {
    emit("update:modelValue", val);
});
</script>

<template>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <TextInput
            v-model="form.name"
            label="What should we call it?"
            placeholder="Enter budget name..."
            :validate="validation.name"
        />

        <TextInput
            v-model="form.description"
            label="How should we describe it?"
            placeholder="Enter budget description..."
            :validate="validation.description"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <SelectInput
            v-model="form.time_period_id"
            label="How often should it occur?"
            placeholder="Choose frequency..."
            :validate="validation.time_period_id"
            :options="timePeriodOptions"
            :class="{ 'xl:col-span-2': !showCustomInterval }"
        />

        <NumericInput
            v-if="showCustomInterval"
            v-model="form.time_period_amount"
            :label="`How many ${selectedTimePeriodName.toLowerCase()}?`"
            :validate="validation.time_period_amount"
            :disabled="isEmpty(form.time_period_id)"
            :placeholder="`Enter ${selectedTimePeriodName.toLowerCase()}(s) between...`"
            :suffix="selectedTimePeriodName"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <SelectInput
            v-model="form.currency_id"
            label="What currency will it be in?"
            placeholder="Choose budget currency..."
            :validate="validation.currency_id"
            :options="currencyOptions"
        />

        <CurrencyInput
            v-model="form.opening_balance"
            label="What is the opening balance?"
            placeholder="Enter opening balance..."
            :validate="validation.opening_balance"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <NumericInput
            v-model="form.future_intervals"
            label="How far into the future?"
            :validate="validation.future_intervals"
            :disabled="isEmpty(form.time_period_id)"
            :placeholder="`Enter number of ${
                !isEmpty(selectedTimePeriodName)
                    ? `${selectedTimePeriodName}(s)`
                    : 'intervals'
            } into the future...`"
            :suffix="selectedTimePeriodName"
        />

        <DateInput
            v-model="form.starts_at"
            label="When should it begin?"
            placeholder="Choose start date..."
            :validate="validation.starts_at"
        />
    </div>
</template>
