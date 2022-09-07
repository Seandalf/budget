<script setup>
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { userHasPermission, convertToCurrency } from "@/helpers";
import moment from "moment";

import Button from "@/Components/Button.vue";
import FormSection from "@/Components/Forms/FormSection.vue";

const now = new Date();

const hasBudgetLimit = computed(() => {
    return (
        usePage().props.value.auth.total_budgets === 1 &&
        !userHasPermission("multple-budgets")
    );
});

const budgets = computed(() => usePage().props.value.budgets);

const activeBudgets = computed(() =>
    usePage().props.value.budgets.filter((b) => b.active === true)
);

const inactiveBudgets = computed(() =>
    usePage().props.value.budgets.filter((b) => b.active === false)
);
</script>

<template>
    <Head title="View Budgets" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                All Budgets
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                    See all your budgets
                </span>
            </h2>
        </template>

        <div v-if="budgets.length === 0" class="text-center w-full">
            <img
                src="/img/splash/undraw_lost.svg"
                class="w-full max-w-[200px] mx-auto"
            />

            <div class="flex flex-col items-center justify-center gap-6 mt-6">
                <div class="flex-1">
                    <h6 class="font-title text-base font-bold text-primary-500">
                        Looks like you don't have any budgets.
                    </h6>

                    <p class="font-medium text-slate-600 mt-1">
                        Let's go ahead and get you started by creating a brand
                        new budget.
                    </p>
                </div>

                <div class="flex-0">
                    <Button
                        label="Create Budget"
                        :href="route('web.budgets.create')"
                        type="link"
                        buttonStyle="black"
                        icon="arrow"
                    />
                </div>
            </div>
        </div>

        <h4 class="text-lg font-bold text-primary-500 font-title">
            Active Budgets ({{ activeBudgets.length }})
        </h4>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
            <div
                v-for="(budget, index) in activeBudgets"
                :key="`overview-budget-card-${index}`"
                class="col-span-1 rounded-lg bg-white shadow p-6"
            >
                <div
                    class="flex items-center gap-4 pb-2 border-b border-[#E0E5F6]"
                >
                    <div class="flex-1">
                        <h6
                            class="text-base font-bold text-primary-500 font-title"
                        >
                            {{ budget.name }}
                        </h6>
                    </div>

                    <div class="flex-0">
                        <Button
                            label="Edit"
                            buttonStyle="secondary"
                            linkStyle
                        />
                    </div>
                </div>

                <div class="pb-6 border-b border-[#E0E5F6]">
                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex-1 text-slate-500">Opening Balance</div>

                        <div
                            class="flex-1 text-right font-bold text-primary-500"
                        >
                            {{
                                convertToCurrency(
                                    budget.opening_balance / 100,
                                    budget.currency.shortcode
                                )
                            }}
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex-1 text-slate-500">Current Balance</div>

                        <div
                            class="flex-1 text-right font-bold text-primary-500"
                        >
                            {{
                                convertToCurrency(
                                    budget.opening_balance / 100,
                                    budget.currency.shortcode
                                )
                            }}
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <div class="flex-1 text-slate-500">Current Period</div>

                        <div
                            class="flex-1 text-right font-bold text-primary-500"
                        >
                            {{
                                moment(budget.current_period.starts_at).format(
                                    "Do MMM YY"
                                )
                            }}
                            <span class="text-slate-400 text-xs">to</span>
                            {{
                                moment(budget.current_period.ends_at).format(
                                    "Do MMM YY"
                                )
                            }}
                        </div>
                    </div>
                </div>

                <Button
                    label="View Budget"
                    :href="route('web.budgets.view', budget.id)"
                    type="link"
                    buttonStyle="secondary"
                    class="mt-6"
                    icon="arrow"
                    fullWidth
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
