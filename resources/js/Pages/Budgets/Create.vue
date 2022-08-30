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

const testFunction = () => {
    console.log("wow");
};
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
                hello
            </FormSection>
        </div>
    </AuthenticatedLayout>
</template>
