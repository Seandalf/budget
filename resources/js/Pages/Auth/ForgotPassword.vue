<script setup>
import { Head, useForm } from "@inertiajs/inertia-vue3";
import useVuelidate from "@vuelidate/core";
import { required, email, helpers } from "@vuelidate/validators";

import GuestLayout from "@/Layouts/Guest.vue";
import Alert from "@/Components/Alert.vue";
import Button from "@/Components/Button.vue";
import Errors from "@/Components/Errors.vue";
import TextInput from "@/Components/Input/TextInput.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const rules = {
    email: {
        required: helpers.withMessage("This field is required", required),
        email: helpers.withMessage("Please enter a valid email address", email),
    },
};

const v$ = useVuelidate(rules, form);

const submit = () => {
    form.post(route("auth.password.email"), {
        onFinish: (e) => {
            console.log(e);
        },
    });
};
</script>

<template>
    <Head title="Forgot Password" />

    <GuestLayout>
        <h3
            class="text-xl font-bold font-title text-center text-slate-700 mb-3"
        >
            Forgot your password
        </h3>
        <p class="mb-6 tracking-wide text-center text-sm text-slate-500">
            Please enter the email address of your account below. If the email
            matches an account, we'll email you a link to reset your password.
        </p>

        <Errors v-if="form.errors" :errors="form.errors" />
        <Alert v-if="status" type="success">
            {{ status }}
        </Alert>
        <form @submit.prevent="submit">
            <TextInput
                type="email"
                label="Email"
                v-model="form.email"
                :validate="v$.email"
            />

            <Button
                label="Reset Password"
                buttonStyle="secondary"
                :disabled="!v$.$anyDirty || v$.$invalid || form.processing"
                :loading="form.processing"
                class="mt-6"
                fullWidth
            />
        </form>

        <hr class="mt-6 mb-4" />

        <Button
            label="Back to login"
            :href="route('auth.login.create')"
            type="link"
            icon="arrow"
            linkStyle
        />
    </GuestLayout>
</template>
