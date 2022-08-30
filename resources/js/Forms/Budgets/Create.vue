<script setup>
import { computed, reactive, watch } from "vue";
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import useVuelidate from "@vuelidate/core";
import { userHasPermission, isEmpty, capitalise } from "@/helpers";
import {
    required,
    helpers,
    numeric,
    alphaNum,
    maxLength,
} from "@vuelidate/validators";
import { useToast } from "vue-toastification";
import moment from "moment";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import FormSection from "@/Components/Forms/FormSection.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import CheckboxInput from "@/Components/Input/CheckboxInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import NumericInput from "@/Components/Input/NumericInput.vue";
import CurrencyInput from "@/Components/Input/CurrencyInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import ToggleInput from "@/Components/Input/ToggleInput.vue";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => {},
    },
});

const rules = {
    name: {
        required: helpers.withMessage("This field is required", required),
        alphaNum,
        maxLength: maxLength(50),
    },
    description: {
        alphaNum,
        maxLength: maxLength(200),
    },
    opening_balance: {
        required: helpers.withMessage("This field is required", required),
        numeric,
    },
    future_intervals: {
        required: helpers.withMessage("This field is required", required),
        numeric,
    },
    currency_id: {
        required: helpers.withMessage("This field is required", required),
        numeric,
    },
    time_period_id: {
        required: helpers.withMessage("This field is required", required),
        numeric,
    },
    time_period_amount: {
        numeric,
    },
    starts_at: {
        required: helpers.withMessage("This field is required", required),
    },
};

const v$ = useVuelidate(rules, form);

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
    console.log(currencies);
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
</script>

<template>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <TextInput
            v-model="form.name"
            label="What should we call this budget?"
            placeholder="Give me a name!"
            :validate="v$.name"
        />

        <TextInput
            v-model="form.description"
            label="How should we describe this budget?"
            placeholder="Describe me to your friends"
            :validate="v$.description"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <SelectInput
            v-model="form.time_period_id"
            label="How often should this budget occur?"
            placeholder="How often do you want to see me?"
            :validate="v$.time_period_id"
            :options="timePeriodOptions"
            :class="{ 'xl:col-span-2': !showCustomInterval }"
        />

        <NumericInput
            v-if="showCustomInterval"
            v-model="form.time_period_amount"
            :label="`How many ${selectedTimePeriodName.toLowerCase()}?`"
            :validate="v$.time_period_amount"
            :disabled="isEmpty(form.time_period_id)"
            :placeholder="`Enter number of ${selectedTimePeriodName.toLowerCase()}...`"
            :suffix="selectedTimePeriodName"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <SelectInput
            v-model="form.currency_id"
            label="What currency will this budget be in?"
            placeholder="Show me the money (symbol)!"
            :validate="v$.currency_id"
            :options="currencyOptions"
            multiselect
        />

        <CurrencyInput
            v-model="form.opening_balance"
            label="What is the opening balance?"
            placeholder="How healthy is the bank?"
            :validate="v$.opening_balance"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <NumericInput
            v-model="form.future_intervals"
            :label="`How far into the future?`"
            :validate="v$.future_intervals"
            :disabled="isEmpty(form.time_period_id)"
            placeholder="How far forward are we looking?"
            :suffix="selectedTimePeriodName"
        />

        <DateInput
            v-model="form.starts_at"
            label="When should the budget begin?"
            placeholder="When do we get this show on the road?"
            :validate="v$.starts_at"
        />
    </div>
</template>
