<script setup>
import { computed, reactive, watch } from "vue";
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import useVuelidate from "@vuelidate/core";
import { userHasPermission, isEmpty, capitalise } from "@/helpers";
import { required, helpers, maxLength } from "@vuelidate/validators";
import moment from "moment";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import CheckboxInput from "@/Components/Input/CheckboxInput.vue";
import DateInput from "@/Components/Input/DateInput.vue";
import NumericInput from "@/Components/Input/NumericInput.vue";
import CurrencyInput from "@/Components/Input/CurrencyInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";

const emit = defineEmits(["update:modelValue", "delete"]);
const props = defineProps({
    modelValue: {
        type: Object,
        default: () => {},
    },
    hasSave: {
        type: Boolean,
        default: false,
    },
    hasDelete: {
        type: Boolean,
        default: false,
    },
    incomeCategoryOptions: {
        type: Array,
        default: () => [],
    },
    payeeOptions: {
        type: Array,
        default: () => [],
    },
});

const form = computed({
    get() {
        return props.modelValue;
    },
    set(val) {
        console.log(val);
        emit("update:modelValue", val);
    },
});

const rules = {
    name: {
        required: helpers.withMessage("This field is required", required),
        maxLength: maxLength(50),
    },
    description: {
        maxLength: maxLength(200),
    },
    amount: {
        required: helpers.withMessage("This field is required", required),
    },
    recurring_transaction_type: {
        required: helpers.withMessage("This field is required", required),
    },
    transaction_type: {
        required: helpers.withMessage("This field is required", required),
    },
    category_id: {
        required: helpers.withMessage("This field is required", required),
    },
    time_period_id: {
        required: helpers.withMessage("This field is required", required),
    },
    starts_at: {
        required: helpers.withMessage("This field is required", required),
    },
};

const v$ = useVuelidate(rules, form);

const onSave = () => {
    let submit = JSON.parse(JSON.stringify(form.value));
    submit.expanded = false;
    emit("update:modelValue", submit);
};

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

const selectedTimePeriodName = computed(() => {
    if (isEmpty(form.value.time_period_id)) {
        return null;
    }

    const timePeriods = usePage().props.value.timePeriods;
    const timePeriod = timePeriods.find(
        (period) => period.id === form.value.time_period_id
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

    return periods.includes(form.value.time_period_id);
});
</script>

<template>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <TextInput
            v-model="form.name"
            label="What should we name it?"
            placeholder="Name your salary..."
            :validate="v$.name"
        />

        <TextInput
            v-model="form.description"
            label="How should we describe it?"
            placeholder="Describe your salary..."
            :validate="v$.description"
        />
    </div>
    <div class="grid grid-cols-2 xl:grid-cols-6 gap-6 mt-6">
        <CurrencyInput
            v-model="form.amount"
            label="How much is it?"
            placeholder="Enter salary amount..."
            :validate="v$.amount"
            :class="{
                'xl:col-span-3': !showCustomInterval,
                'xl:col-span-2': showCustomInterval,
            }"
        />
        <SelectInput
            v-model="form.time_period_id"
            label="How often is it paid?"
            placeholder="Choose frequency..."
            :validate="v$.time_period_id"
            :options="timePeriodOptions"
            :class="{
                'xl:col-span-3': !showCustomInterval,
                'xl:col-span-2': showCustomInterval,
            }"
        />

        <NumericInput
            v-if="showCustomInterval"
            v-model="form.time_period_amount"
            :label="`How many ${selectedTimePeriodName.toLowerCase()}?`"
            :validate="v$.time_period_amount"
            :disabled="isEmpty(form.time_period_id)"
            :placeholder="`Enter ${selectedTimePeriodName.toLowerCase()}(s) between...`"
            :suffix="selectedTimePeriodName"
            class="col-span-2"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <div>
            <SelectInput
                v-model="form.category_id"
                label="What category is it part of?"
                placeholder="Choose category..."
                :validate="v$.category_id"
                :options="incomeCategoryOptions"
                :disabled="
                    isEmpty(form.transaction_type) ||
                    incomeCategoryOptions.length === 0
                "
            />

            <p
                v-if="incomeCategoryOptions.length === 0"
                class="text-xs text-yellow-500 mt-2 ml-1"
            >
                No categories found, add some below!
            </p>

            <p v-else class="text-xs text-sky-500 mt-2 ml-1">
                You can add custom categories below!
            </p>
        </div>

        <div>
            <SelectInput
                v-model="form.payee_id"
                label="Who pays your salary?"
                placeholder="Choose payer..."
                :validate="v$.payee_id"
                :options="payeeOptions"
                :disabled="payeeOptions.length === 0"
            />

            <p
                v-if="payeeOptions.length === 0"
                class="text-xs text-yellow-500 mt-2 ml-1"
            >
                No payers found, add some below!
            </p>

            <p v-else class="text-xs text-sky-500 mt-2 ml-1">
                You can add custom payers below!
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <DateInput
            v-model="form.starts_at"
            label="When is the first payment?"
            placeholder="Choose first payment date..."
            :validate="v$.starts_at"
        />

        <DateInput
            v-model="form.ends_at"
            label="When is the last payment?"
            placeholder="Choose last payment date"
        />
    </div>

    <div class="flex w-full justify-end gap-4 mt-6">
        <Button
            v-if="hasDelete"
            label="Delete"
            buttonStyle="error"
            @click="$emit('delete')"
            fullWidth
            outline
        />

        <Button
            v-if="hasSave"
            label="Save"
            buttonStyle="success"
            @click="onSave"
            :disabled="!v$.$anyDirty || v$.$invalid"
        />
    </div>
</template>
