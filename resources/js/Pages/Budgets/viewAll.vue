<script setup>
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { userHasPermission } from "@/helpers";

import Button from "@/Components/Button.vue";
import FormSection from "@/Components/Forms/FormSection.vue";

const hasBudgetLimit = computed(() => {
    return (
        usePage().props.value.auth.total_budgets === 1 &&
        !userHasPermission("multple-budgets")
    );
});

const budgets = computed(() => usePage().props.value.budgets);
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
    </AuthenticatedLayout>
</template>
