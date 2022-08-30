<script setup>
import { computed, reactive } from "vue";
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import { userHasPermission, isEmpty, capitalise } from "@/helpers";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import FormSection from "@/Components/Forms/FormSection.vue";
import ToggleInput from "@/Components/Input/ToggleInput.vue";

import CreateBudgetForm from "@/Forms/Budgets/Create.vue";

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

const hasBudgetLimit = computed(() => {
    return (
        usePage().props.value.auth.total_budgets === 1 &&
        !userHasPermission("multple-budgets")
    );
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
                <CreateBudgetForm v-model="form" />
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
