<script setup>
import { Head, useForm } from "@inertiajs/inertia-vue3";
import useVuelidate from "@vuelidate/core";
import { required, email, alpha, helpers } from "@vuelidate/validators";

import GuestLayout from "@/Layouts/Guest.vue";
import Button from "@/Components/Button.vue";
import Errors from "@/Components/Errors.vue";
import TextInput from "@/Components/Input/TextInput.vue";

const form = useForm({
    first_name: "",
    last_name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const rules = {
    first_name: {
        required: helpers.withMessage("This field is required", required),
        alpha: helpers.withMessage(
            "This field must only contain letters",
            alpha
        ),
    },
    last_name: {
        required: helpers.withMessage("This field is required", required),
        alpha: helpers.withMessage(
            "This field must only contain letters",
            alpha
        ),
    },
    email: {
        required: helpers.withMessage("This field is required", required),
        email: helpers.withMessage("Please enter a valid email address", email),
    },
    password: {
        required: helpers.withMessage("This field is required", required),
    },
    password_confirmation: {
        required: helpers.withMessage("This field is required", required),
    },
};

const v$ = useVuelidate(rules, form);

const submit = () => {
    form.post(route("auth.register.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Register" />

    <GuestLayout>
        <h3
            class="text-xl font-bold font-title text-center text-slate-700 mb-6"
        >
            Create your account
        </h3>

        <Errors v-if="form.errors" :errors="form.errors" />
        <form @submit.prevent="submit">
            <TextInput
                type="text"
                label="First name"
                v-model="form.first_name"
                :validate="v$.first_name"
            />

            <TextInput
                type="text"
                label="Last name"
                class="mt-4"
                v-model="form.last_name"
                :validate="v$.last_name"
            />

            <TextInput
                type="email"
                label="Email"
                class="mt-4"
                v-model="form.email"
                :validate="v$.email"
            />

            <TextInput
                type="password"
                label="Password"
                class="mt-4"
                v-model="form.password"
                :validate="v$.password"
            />

            <TextInput
                type="password"
                label="Confirm password"
                class="mt-4"
                v-model="form.password_confirmation"
                :validate="v$.password_confirmation"
            />

            <Button
                label="Create account"
                buttonStyle="secondary"
                :disabled="!v$.$anyDirty || v$.$invalid || form.processing"
                :loading="form.processing"
                class="mt-6"
                fullWidth
            />
        </form>

        <hr class="my-6" />

        <div class="flex items-center gap-4">
            <div class="flex-1">
                <p class="text-slate-600 text-sm tracking-wide text-right">
                    Already have an account?
                </p>
            </div>
            <div class="flex-0">
                <Button
                    label="Login"
                    :href="route('auth.login.create')"
                    type="link"
                    icon="arrow"
                    outline
                />
            </div>
        </div>
    </GuestLayout>
</template>
