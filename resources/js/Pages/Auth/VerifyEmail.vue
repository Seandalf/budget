<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

import GuestLayout from "@/Layouts/Guest.vue";

const props = defineProps({
    status: String,
});

const form = useForm();

const submit = () => {
    form.post(route("auth.verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head title="Email Verification" />

    <GuestLayout>
        <h3
            class="text-xl font-bold font-title text-center text-slate-700 mb-3"
        >
            Verification email sent
        </h3>
        <p class="mb-6 tracking-wide text-center text-sm text-slate-500">
            A verification email has been sent to the address you provided.
            Please click the link in the email to verify.
        </p>
    </GuestLayout>
</template>
