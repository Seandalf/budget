<script setup>
import { Head, useForm } from "@inertiajs/inertia-vue3";
import useVuelidate from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";

import GuestLayout from "@/Layouts/Guest.vue";
import Button from "@/Components/Button.vue";
import Errors from "@/Components/Errors.vue";
import TextInput from "@/Components/Input/TextInput.vue";

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const rules = {
    password: {
        required: helpers.withMessage("This field is required", required),
    },
    password_confirmation: {
        required: helpers.withMessage("This field is required", required),
    },
};

const v$ = useVuelidate(rules, form);

const submit = () => {
    form.post(route("auth.password.update"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <GuestLayout>
        <h3
            class="text-xl font-bold font-title text-center text-slate-700 mb-6"
        >
            Reset your password
        </h3>

        <Errors v-if="form.errors" :errors="form.errors" />
        <form @submit.prevent="submit">
            <TextInput
                type="password"
                label="New password"
                v-model="form.password"
                :validate="v$.password"
            />

            <TextInput
                type="password"
                label="Confirm password"
                v-model="form.password_confirmation"
                :validate="v$.password_confirmation"
            />>

            <Button
                label="Reset password"
                buttonStyle="secondary"
                :disabled="!v$.$anyDirty || v$.$invalid || form.processing"
                :loading="form.processing"
                class="mt-6"
                fullWidth
            />
        </form>
    </GuestLayout>
</template>
