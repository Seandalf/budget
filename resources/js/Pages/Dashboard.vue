<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/inertia-vue3";
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
    </AuthenticatedLayout>
</template>
