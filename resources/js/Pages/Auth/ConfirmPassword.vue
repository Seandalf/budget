<script setup>
import { Head, useForm } from "@inertiajs/inertia-vue3";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirm Password" />
</template>
