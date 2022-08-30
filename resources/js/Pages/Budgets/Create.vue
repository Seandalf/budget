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

// TODO: Extract the budget form into the form folder. Just couldn't face
// doing it after literally building the whole component

const form = useForm({
    name: null,
    description: null,
    opening_balance: null,
    future_intervals: null,
    currency_id: null,
    time_period_id: null,
    time_period_amount: null,
    starts_at: null,
});

const additional = reactive({
    recurringTransactions: [],
});

const settings = reactive({
    noTransactions: false,
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
const toast = useToast();

const hasBudgetLimit = computed(() => {
    return (
        usePage().props.value.auth.total_budgets === 1 &&
        !userHasPermission("multple-budgets")
    );
});

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
    <Head title="Create Budget" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                Create Budget
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                    Create a new budget
                </span>
            </h2>
        </template>

        <div v-if="hasBudgetLimit" class="text-center w-full">
            <img
                src="/img/splash/undraw_warning.svg"
                class="w-full max-w-[200px] mx-auto"
            />

            <div class="flex flex-col items-center justify-center mt-6">
                <div class="flex-1">
                    <h6 class="font-title text-base font-bold text-primary-500">
                        Whoa there...looks like you already have a budget.
                    </h6>

                    <p class="font-medium text-slate-600 mt-1">
                        If you want to have multiple budgets, you'll need to
                        upgrade.
                    </p>
                </div>

                <div class="flex-0 mt-6">
                    <Button
                        label="Upgrade plan"
                        :href="route('web.budgets.create')"
                        type="link"
                        buttonStyle="black"
                        icon="arrow"
                    />

                    <Button
                        label="View Budget"
                        :href="route('web.budgets.create')"
                        type="link"
                        buttonStyle="primary"
                        icon="arrow"
                        outline
                        class="mt-2"
                    />
                </div>
            </div>
        </div>

        <div v-else class="w-full">
            <FormSection
                heading="Basic Details"
                description="Tell us the basic details of your budget to get started"
            >
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
            </FormSection>

            <hr class="border-slate-200 my-8" />

            <FormSection
                heading="Recurring Transactions"
                description="Tell us all about the most common transactions you see in your budget"
            >
                <ToggleInput
                    label="I don't want to add any transactions right now"
                    v-model="settings.noTransactions"
                />

                <template v-if="settings.noTransactions">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.2"
                        stroke="currentColor"
                        class="w-16 h-16 mx-auto mt-8 text-secondary-500"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z"
                        />
                    </svg>

                    <div class="flex flex-col items-center justify-center mt-2">
                        <div class="flex-1">
                            <h6
                                class="font-title text-base text-center font-bold text-primary-500"
                            >
                                Understood.
                            </h6>

                            <p class="font-medium text-slate-600 mt-1">
                                We won't add any transactions.
                            </p>
                        </div>
                    </div>
                </template>
            </FormSection>
        </div>
    </AuthenticatedLayout>
</template>
