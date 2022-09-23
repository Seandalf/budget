<script setup>
import { computed, reactive, onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { isEmpty } from "@/helpers";
import { useToast } from "vue-toastification";

import Button from "@/Components/Button.vue";
import TextInput from "@/Components/Input/TextInput.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ToastText from "@/Components/ToastText.vue";
import ConfirmModal from "@/Components/ConfirmModal.vue";
import Modal from "@/Components/Modal.vue";

const toast = useToast();

const data = reactive({
    payees: [],
    selectedBudget: null,
    loading: false,
    confirmDelete: false,
    payeeToDelete: null,
    payeeToEdit: null,
});

const payeeForm = reactive({
    name: null,
});

const fetchPayees = () => {
    data.loading = true;
    axios
        .get(route("api.payees.index", data.selectedBudget.id))
        .then((res) => {
            data.payees = res.data.data;
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

const onOpenAddPayee = () => {
    payeeForm.name = null;
    data.categoryMode = "add";
    data.modalOpen = true;
};

const openEditModal = (payee) => {
    data.payeeToEdit = payee;
    payeeForm.name = payee.name;
    data.categoryMode = "edit";
    data.modalOpen = true;
};

const onModalClose = () => {
    data.modalOpen = false;
};

const onSave = () => {
    if (data.categoryMode === "add") {
        createPayee();
    }

    if (data.categoryMode === "edit") {
        editPayee();
    }
};

const onOpenConfirmModal = (payee) => {
    data.payeeToDelete = payee;

    data.confirmDelete = true;
};

const onCloseConfirmModal = () => {
    data.confirmDelete = false;
};

const deletePayee = () => {
    axios
        .delete(route("api.payees.delete", data.payeeToDelete.id))
        .then((res) => {
            toast.success({
                component: ToastText,
                props: {
                    type: "success",
                    message: `Deleted ${data.payeeToDelete.name} payee!`,
                },
            });
        })
        .catch((e) => {
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't delete ${data.payeeToDelete.name} payee`,
                },
            });
        })
        .finally(() => {
            data.confirmDelete = false;
            data.payeeToDelete = null;
            fetchPayees();
        });
};

const editPayee = () => {
    axios
        .patch(route("api.payees.update", data.payeeToEdit.id), payeeForm)
        .then((res) => {
            toast.success({
                component: ToastText,
                props: {
                    type: "success",
                    message: `Updated ${payeeForm.name} category!`,
                },
            });

            payeeForm.name = null;

            data.modalOpen = false;

            fetchPayees();
        })
        .catch((e) => {
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't create ${payeeForm.name} category`,
                },
            });
        });
};

const createPayee = () => {
    payeeForm.budget_id = data.selectedBudget.id;
    axios
        .put(route("api.payees.create"), payeeForm)
        .then((res) => {
            toast.success({
                component: ToastText,
                props: {
                    type: "success",
                    message: `Added ${payeeForm.name} category!`,
                },
            });

            payeeForm.name = null;

            data.modalOpen = false;

            fetchPayees();
        })
        .catch((e) => {
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't create ${payeeForm.name} category`,
                },
            });
        });
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
        fetchPayees();
    }
});
</script>

<template>
    <Head title="View Payees" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                All Payees
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                    See all your payees for your budget
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
                    label="Add Payee"
                    buttonStyle="secondary"
                    class="inline-block"
                    icon="plus"
                    @click="onOpenAddPayee"
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
                    ></th>
                </tr>
                <tr
                    v-for="(payee, index) in data.payees"
                    :key="`payee-row-${index}`"
                >
                    <td
                        class="border border-slate-200 bg-white font-bold text-primary-500 px-3 py-2"
                    >
                        {{ payee.name }}
                    </td>
                    <td
                        class="border border-slate-200 bg-white px-3 py-2 text-right justify-end w-44"
                    >
                        <Button
                            label="Edit"
                            buttonStyle="black"
                            class="inline-block"
                            linkStyle
                            @click="openEditModal(payee)"
                        />

                        <Button
                            label="Delete"
                            buttonStyle="tertiary"
                            class="inline-block"
                            linkStyle
                            @click="onOpenConfirmModal(payee)"
                        />
                    </td>
                </tr>
            </table>
        </div>

        <Modal
            :isOpen="data.modalOpen"
            v-on:closed="onModalClose"
            v-on:success="onSave"
        >
            <template #header>
                <template v-if="data.categoryMode === 'add'">
                    Add New Payee
                </template>

                <template v-else> Edit Payee </template>
            </template>

            <template #body>
                <TextInput
                    v-model="payeeForm.name"
                    label="What should we call
                this payee?"
                    placeholder="Give me a name!"
                />
            </template>
        </Modal>

        <ConfirmModal
            :isOpen="data.confirmDelete"
            v-on:closed="onCloseConfirmModal"
            v-on:success="deletePayee"
            :message="`
        <p class='mb-3'>
            Are you sure you want to delete
            <span class='font-bold'>${data.payeeToDelete?.name}</span>?
        </p>
        <p>This action cannot be undone.</p>
        `"
        />
    </AuthenticatedLayout>
</template>
