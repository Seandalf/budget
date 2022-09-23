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

const data = reactive({
    num: 1,
});

const form = reactive({
    name: null,
    type: null,
});

const rules = {
    name: {
        required: helpers.withMessage("This field is required", required),
        maxLength: maxLength(50),
    },
    type: {
        required,
    },
};

const v$ = useVuelidate(rules, form);

const addCategory = () => {
    emit("add", form);
};

const resetForm = () => {
    form.name = null;
    form.type = null;
    data.num++;
    v$.value.$reset();
};

defineExpose({
    addCategory,
    resetForm,
    form,
});
</script>

<template>
    <div
        class="grid grid-cols-1 xl:grid-cols-2 gap-6"
        :key="`create-category-form-${data.num}`"
    >
        <TextInput
            v-model="form.name"
            label="What should we call this category?"
            placeholder="Give me a name!"
            :validate="v$.name"
        />

        <SelectInput
            v-model="form.type"
            label="Is this income or expenditure?"
            placeholder="Am I given or received?"
            :validate="v$.transaction_type"
            :options="[
                { name: 'Income', value: 1 },
                { name: 'Expenditure', value: 2 },
            ]"
        />
    </div>

    <div v-if="hasSave" class="flex w-full gap-4 mt-4">
        <Button
            label="Add this category"
            buttonStyle="success"
            @click="addCategory"
            :disabled="!v$.$anyDirty || v$.$invalid"
            icon="plus"
        />
    </div>
</template>
