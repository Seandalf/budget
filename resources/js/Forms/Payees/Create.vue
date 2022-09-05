<script setup>
import { computed, reactive, watch } from "vue";
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import useVuelidate from "@vuelidate/core";
import { userHasPermission, isEmpty, capitalise } from "@/helpers";
import { required, helpers, maxLength } from "@vuelidate/validators";

import Button from "@/Components/Button.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";

const emit = defineEmits(["add"]);

const props = defineProps({
    hasSave: {
        type: Boolean,
        default: false,
    },
});

const form = reactive({
    name: null,
});

const rules = {
    name: {
        required: helpers.withMessage("This field is required", required),
        maxLength: maxLength(50),
    },
};

const v$ = useVuelidate(rules, form);

const addPayee = () => {
    emit("add", form);
};
</script>

<template>
    <div>
        <TextInput
            v-model="form.name"
            label="What should we call this payee?"
            placeholder="Give me a name!"
            :validate="v$.name"
        />
    </div>

    <div v-if="hasSave" class="flex w-full gap-4 mt-4">
        <Button
            label="Add this payee"
            buttonStyle="success"
            @click="addPayee"
            :disabled="!v$.$anyDirty || v$.$invalid"
            icon="plus"
        />
    </div>
</template>
