<script setup>
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { convertToCurrency } from "@/Helpers";

import Button from "@/Components/Button.vue";

const hasBudgets = computed(() => {
    return usePage().props.value.auth.total_budgets > 0;
});

const currentBudget = usePage().props.value.budgets[0];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                Dashboard
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                    An overview of your budgets
                </span>
            </h2>
        </template>

        <div v-if="!hasBudgets" class="text-center w-full">
            <img
                src="/img/splash/undraw_blank_canvas.svg"
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
                        label="Create budget"
                        :href="route('web.budgets.create')"
                        type="link"
                        buttonStyle="black"
                        icon="arrow"
                    />
                </div>
            </div>
        </div>

        <div v-else class="w-full">
            <div class="grid grid-cols-3 gap-8">
                <div class="bg-white shadow rounded-xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex-0">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="w-8 h-8 text-green-500"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>

                        <div
                            class="flex-1 font-bold text-lg font-title text-slate-600"
                        >
                            Current Balance
                        </div>
                    </div>

                    <div
                        class="font-money font-bold text-xl text-slate-700 mt-4"
                    >
                        {{
                            convertToCurrency(
                                currentBudget.opening_balance,
                                currentBudget.currency.shortcode
                            )
                        }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
