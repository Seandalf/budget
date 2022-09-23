<script setup>
import { computed, reactive, onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { userHasPermission, convertToCurrency, isEmpty } from "@/helpers";
import moment from "moment";
import { useToast } from "vue-toastification";

import Button from "@/Components/Button.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import SelectInput from "@/Components/Input/SelectInput.vue";
import FormSection from "@/Components/Forms/FormSection.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import Modal from "@/Components/Modal.vue";
import ToastText from "@/Components/ToastText.vue";

import CreateCategoryForm from "@/Forms/Categories/Create.vue";

const now = new Date();
const toast = useToast();

const data = reactive({
    categories: [],
    selectedBudget: null,
    loading: false,
    modalOpen: false,
    confirmDelete: false,
    categoryToDelete: null,
    categoryMode: "add",
    increment: 1,
});

const categoryForm = reactive({
    name: null,
    type: null,
});

const fetchCategories = () => {
    data.loading = true;
    axios
        .get(route("api.categories.index", data.selectedBudget.id))
        .then((res) => {
            data.categories = res.data.data;
        })
        .catch((e) => {
            let errorMessage = "";

            if (
                !isEmpty(e.response) &&
                !isEmpty(e.response.data) &&
                !isEmpty(e.response.data.message)
            ) {
                errorMessage = e.response.data.message;
            } else if (!isEmpty(e.message)) {
                errorMessage = e.message;
            } else if (!isEmpty(e.data) && !isEmpty(e.data.message)) {
                errorMessage = e.data.message;
            } else {
                errorMessage = "Unknown Error";
            }

            toast.error({
                component: ToastText,
                props: { type: "error", message: errorMessage },
            });
        })
        .finally(() => {
            data.loading = false;
        });
};

const openAddModal = () => {
    categoryForm.name = null;
    categoryForm.type = null;
    data.categoryMode = "add";
    data.modalOpen = true;
};

const openEditModal = (category) => {
    console.log(category);
    data.categoryToEdit = category;

    categoryForm.name = category.name;
    categoryForm.type = category.type;

    data.categoryMode = "edit";
    data.modalOpen = true;
};

const onSaveCategory = () => {
    if (data.categoryMode === "add") {
        createNewCategory();
    }

    if (data.categoryMode === "edit") {
        submitEditCategory();
    }
};

const submitEditCategory = () => {
    axios
        .patch(
            route("api.categories.update", data.categoryToEdit.id),
            categoryForm
        )
        .then((res) => {
            toast.success({
                component: ToastText,
                props: {
                    type: "success",
                    message: `Updated ${categoryForm.name} category!`,
                },
            });

            categoryForm.name = null;
            categoryForm.type = null;

            data.modalOpen = false;

            fetchCategories();
        })
        .catch((e) => {
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't create ${category.name} category`,
                },
            });
        });
};

const createNewCategory = () => {
    categoryForm.budget_id = data.selectedBudget.id;
    axios
        .put(route("api.categories.create"), categoryForm)
        .then((res) => {
            toast.success({
                component: ToastText,
                props: {
                    type: "success",
                    message: `Added ${categoryForm.name} category!`,
                },
            });

            categoryForm.name = null;
            categoryForm.type = null;

            data.modalOpen = false;

            fetchCategories();
        })
        .catch((e) => {
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't create ${category.name} category`,
                },
            });
        });
};

const onModalClose = () => {
    data.modalOpen = false;
};

const onDeleteCategory = () => {
    axios
        .delete(route("api.categories.delete", data.categoryToDelete.id))
        .then((res) => {
            toast.success({
                component: ToastText,
                props: {
                    type: "success",
                    message: `Deleted ${data.categoryToDelete.name} category!`,
                },
            });
        })
        .catch((e) => {
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't delete ${data.categoryToDelete.name} category`,
                },
            });
        })
        .finally(() => {
            data.confirmDelete = false;
            data.categoryToDelete = null;
            fetchCategories();
        });
};

const onCancelDelete = () => {
    data.confirmDelete = false;
    data.categoryToDelete = null;
};

const openDeleteCategoryConfirm = (category) => {
    data.categoryToDelete = category;
    data.confirmDelete = true;
};

const budgetOptions = computed(() => {
    const budgets = usePage().props.value.budgets;
    let options = [];

    for (const index in budgets) {
        options.push({
            name: budgets[index].name,
            value: budgets[index].id,
        });
    }

    return options;
});

onMounted(() => {
    const budgets = usePage().props.value.budgets;

    if (budgets.length === 1) {
        data.selectedBudget = budgets[0];
        fetchCategories();
    }
});
</script>

<template>
    <Head title="View Categories" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                All Categories
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                    See all your categories for each budget
                </span>
            </h2>
        </template>

        <LoadingSpinner
            :loading="data.loading"
            label="Loading categories..."
        ></LoadingSpinner>

        <div v-if="data.selectedBudget !== null">
            <div class="flex justify-end">
                <Button
                    label="Add Category"
                    buttonStyle="secondary"
                    class="inline-block"
                    icon="plus"
                    @click="openAddModal"
                />
            </div>

            <table
                class="overflow w-full rounded-xl shadow overflow-hidden mt-8"
            >
                <tr>
                    <th
                        class="border border-primary-500 bg-primary-500 text-white p-3 font-bold font-title text-left"
                    >
                        Name
                    </th>
                    <th
                        class="border border-primary-500 bg-primary-500 text-white p-3 font-bold font-title"
                    >
                        Type
                    </th>
                    <th
                        class="border border-primary-500 bg-primary-500 text-white p-3 font-bold font-title"
                    ></th>
                </tr>
                <tr
                    v-for="(category, index) in data.categories"
                    :key="`category-row-${index}`"
                >
                    <td
                        class="border border-slate-200 bg-white font-bold text-primary-500 px-3 py-2"
                    >
                        {{ category.name }}
                    </td>
                    <td
                        class="border border-slate-200 bg-white px-3 py-2 text-center"
                    >
                        <p
                            class="inline-block px-3 pt-[6px] pb-1 text-[11px] uppercase tracking-wider font-semibold rounded-full text-white"
                            :class="{
                                'bg-lime-500': category.type === 1,
                                'bg-rose-500': category.type === 2,
                            }"
                        >
                            {{ category.type === 1 ? "Income" : "Expenditure" }}
                        </p>
                    </td>
                    <td
                        class="border border-slate-200 bg-white px-3 py-2 text-right justify-end w-44"
                    >
                        <Button
                            label="Edit"
                            buttonStyle="black"
                            class="inline-block"
                            linkStyle
                            @click="openEditModal(category)"
                        />

                        <Button
                            label="Delete"
                            buttonStyle="tertiary"
                            class="inline-block"
                            linkStyle
                            @click="openDeleteCategoryConfirm(category)"
                        />
                    </td>
                </tr>
            </table>
        </div>

        <Modal
            :isOpen="data.modalOpen"
            v-on:closed="onModalClose"
            v-on:success="onSaveCategory"
            :key="`add-edit-modal-${data.num}`"
        >
            <template #header>
                <template v-if="data.categoryMode === 'add'">
                    Add New Category
                </template>

                <template v-else> Edit Category </template>
            </template>

            <template #body>
                <div
                    class="grid grid-cols-1 gap-6"
                    :class="{ 'xl:grid-cols-2': data.categoryMode === 'add' }"
                    :key="`test-${data.increment}`"
                >
                    <TextInput
                        v-model="categoryForm.name"
                        label="What should we call this category?"
                        placeholder="Give me a name!"
                    />

                    <SelectInput
                        v-if="data.categoryMode === 'add'"
                        v-model="categoryForm.type"
                        label="Is this income or expenditure?"
                        placeholder="Am I given or received?"
                        :options="[
                            { name: 'Income', value: 1 },
                            { name: 'Expenditure', value: 2 },
                        ]"
                    />
                </div>
            </template>
        </Modal>

        <ConfirmModal
            :isOpen="data.confirmDelete"
            v-on:closed="onCancelDelete"
            v-on:success="onDeleteCategory"
            :message="`
        <p class='mb-3'>
            Are you sure you want to delete
            <span class='font-bold'>${data.categoryToDelete?.name}</span>?
        </p>
        <p>
            This cannot be undone, and any existing transactions in this
            category will be moved to the default category.
        </p>
        `"
        />
    </AuthenticatedLayout>
</template>
