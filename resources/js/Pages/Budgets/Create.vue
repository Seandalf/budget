<script setup>
import { computed, reactive, watch } from "vue";
import { Head, usePage, useForm } from "@inertiajs/inertia-vue3";
import { userHasPermission, isEmpty, capitalise } from "@/helpers";
import useVuelidate from "@vuelidate/core";
import { required, helpers, maxLength } from "@vuelidate/validators";
import { useToast } from "vue-toastification";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import FormSection from "@/Components/Forms/FormSection.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ToggleInput from "@/Components/Input/ToggleInput.vue";

import CreateBudgetForm from "@/Forms/Budgets/Create.vue";
import CreateRecurringTransactionForm from "@/Forms/RecurringTransactions/Create.vue";
import CreateSalaryForm from "@/Forms/RecurringTransactions/Salary.vue";
import CreateCategoryForm from "@/Forms/Categories/Create.vue";
import CreatePayeeForm from "@/Forms/Payees/Create.vue";
import ToastText from "@/Components/ToastText.vue";

const form = useForm({
    name: null,
    description: null,
    opening_balance: null,
    future_intervals: null,
    currency_id: null,
    time_period_id: null,
    time_period_amount: null,
    starts_at: null,
});

const rules = {
    name: {
        required: helpers.withMessage("This field is required", required),
        maxLength: maxLength(50),
    },
    description: {
        maxLength: maxLength(200),
    },
    opening_balance: {
        required: helpers.withMessage("This field is required", required),
    },
    future_intervals: {
        required: helpers.withMessage("This field is required", required),
    },
    currency_id: {
        required: helpers.withMessage("This field is required", required),
    },
    time_period_id: {
        required: helpers.withMessage("This field is required", required),
    },
    starts_at: {
        required: helpers.withMessage("This field is required", required),
    },
};

const v$ = useVuelidate(rules, form);
const toast = useToast();

const timePeriods = usePage().props.value.timePeriods;
const currencies = usePage().props.value.currencies;

const additional = reactive({
    recurringTransactions: [],
    salaryTransactions: [],
    categories: [],
    payees: [],
});

const settings = reactive({
    noTransactions: false,
    noCategories: false,
    noPayees: false,
    includeSalary: false,
    budgetId: null,
});

const processing = reactive({
    budget: false,
    recurringTransactions: false,
    salaryTransactions: false,
    categories: false,
    payees: false,
});

const hasBudgetLimit = computed(() => {
    return (
        usePage().props.value.auth.total_budgets === 1 &&
        !userHasPermission("multiple-budgets")
    );
});

const addRecurringTransaction = () => {
    additional.recurringTransactions.unshift({
        id: (Math.random() + 1).toString(36).substring(7),
        name: null,
        description: null,
        amount: null,
        recurring_transaction_type: null,
        transaction_type: null,
        active: true,
        budget_id: null,
        category_id: null,
        payee_id: null,
        time_period_id: null,
        time_period_amount: null,
        starts_at: null,
        ends_at: null,
        expanded: true,
    });
};

const addSalary = () => {
    additional.salaryTransactions.unshift({
        id: (Math.random() + 1).toString(36).substring(7),
        name: null,
        description: null,
        amount: null,
        recurring_transaction_type: 1,
        transaction_type: 1,
        active: true,
        budget_id: null,
        category_id: null,
        payee_id: null,
        time_period_id: null,
        time_period_amount: null,
        starts_at: null,
        ends_at: null,
        expanded: true,
    });
};

const addCategory = (event) => {
    let found = false;

    if (!isEmpty(additional.categories.find((c) => c.name === event.name))) {
        found = true;
    }

    if (
        !isEmpty(
            usePage().props.value.categories.find((c) => c.name === event.name)
        )
    ) {
        found = true;
    }

    if (!found) {
        additional.categories.push({
            id: (Math.random() + 1).toString(36).substring(7),
            name: event.name,
            type: event.type,
        });
    } else {
        toast.error({
            component: ToastText,
            props: {
                type: "error",
                message: "A category with that name already exists!",
            },
        });
    }
};

const addPayee = (event) => {
    let found = false;

    if (!isEmpty(additional.payees.find((p) => p.name === event.name))) {
        found = true;
    }

    if (
        !isEmpty(
            usePage().props.value.payees.find((p) => p.name === event.name)
        )
    ) {
        found = true;
    }

    if (!found) {
        additional.payees.push({
            id: (Math.random() + 1).toString(36).substring(7),
            name: event.name,
        });
    } else {
        toast.error({
            component: ToastText,
            props: {
                type: "error",
                message: "A payee with that name already exists!",
            },
        });
    }
};

const removeTransaction = (index) => {
    additional.recurringTransactions.splice(index, 1);
};

const removeSalary = (index) => {
    additional.salaryTransactions.splice(index, 1);
};

const removeCategory = (index) => {
    additional.categories.splice(index, 1);
};

const removePayee = (index) => {
    additional.payees.splice(index, 1);
};

const getSortedOptions = (existingOptions, newOptions) => {
    let options = [];

    for (const index in existingOptions) {
        options.push({
            name: existingOptions[index].name,
            value: existingOptions[index].id,
        });
    }

    for (const index in newOptions) {
        options.push({
            name: newOptions[index].name,
            value: newOptions[index].id,
        });
    }

    options.sort(function (a, b) {
        if (a.name < b.name) {
            return -1;
        }

        if (a.name > b.name) {
            return 1;
        }

        return 0;
    });

    return options;
};

const submitBudget = () => {
    processing.budget = true;
    processing.recurringTransactions = true;
    processing.salaryTransactions = true;
    processing.categories = true;
    processing.payees = true;

    axios
        .put(route("api.budgets.create"), form)
        .then((res) => {
            const budgetId = res.data.data.id;

            if (!budgetId) {
                throw new Error("No budget set!");
            }

            settings.budgetId = budgetId;

            processCategories();
            processPayees();

            setTimeout(() => {
                processRecurringTransactions();
                processSalaryTransactions();
            }, 1000);
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
            processing.budget = false;
        });
};

const createPayee = (payee) => {
    return axios
        .put(route("api.payees.create"), payee)
        .then((res) => {
            const payeeId = res.data.data.id;

            if (!payeeId) {
                throw new Error("No payee ID found");
            }

            let transactions = additional.recurringTransactions.filter(
                (t) => t.payee_id === payee.id
            );

            transactions.forEach((transaction) => {
                transaction.payee_id = payeeId;
            });

            transactions = additional.salaryTransactions.filter(
                (t) => t.payee_id === payee.id
            );

            transactions.forEach((transaction) => {
                transaction.payee_id = payeeId;
            });

            payee.id = payeeId;
        })
        .catch((e) => {
            console.log(e);
            toast.error({
                component: ToastText,
                props: {
                    type: "error",
                    message: `Couldn't create ${payee.name} payee`,
                },
            });
        })
        .finally(() => {
            return true;
        });
};

const processRecurringTransactions = async () => {
    const unprocessedCategories = additional.categories.filter(
        (c) => !isNaN(c.id)
    );
    const unprocessedPayees = additional.payees.filter((p) => !isNaN(p.id));

    if (unprocessedCategories.length === 0 && unprocessedPayees.length === 0) {
        setTimeout(() => {
            processRecurringTransactions();
        }, 1000);
        return false;
    }

    if (additional.recurringTransactions.length > 0) {
        processing.recurringTransactions = true;

        let i = 1;
        additional.recurringTransactions.forEach(async (transaction) => {
            transaction.budget_id = settings.budgetId;
            axios
                .put(route("api.recurring-transactions.create"), transaction)
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
                    if (i === additional.recurringTransactions.length) {
                        processing.recurringTransactions = false;
                    } else {
                        i++;
                    }
                });
        });
    } else {
        processing.recurringTransactions = false;
    }
};

const processSalaryTransactions = async () => {
    const unprocessedCategories = additional.categories.filter(
        (c) => !isNaN(c.id)
    );
    const unprocessedPayees = additional.payees.filter((p) => !isNaN(p.id));

    if (unprocessedCategories.length === 0 && unprocessedPayees.length === 0) {
        setTimeout(() => {
            processSalaryTransactions();
        }, 1000);
        return false;
    }

    if (additional.salaryTransactions.length > 0) {
        processing.salaryTransactions = true;

        let i = 1;
        additional.salaryTransactions.forEach(async (transaction) => {
            transaction.budget_id = settings.budgetId;
            axios
                .put(route("api.recurring-transactions.create"), transaction)
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
                    if (i === additional.salaryTransactions.length) {
                        processing.salaryTransactions = false;
                    } else {
                        i++;
                    }
                });
        });
    } else {
        processing.salaryTransactions = false;
    }
};

const processCategories = () => {
    if (additional.categories.length > 0) {
        processing.categories = true;

        let i = 1;
        additional.categories.forEach((category) => {
            category.budget_id = settings.budgetId;
            axios
                .put(route("api.categories.create"), category)
                .then((res) => {
                    const categoryId = res.data.data.id;

                    if (!categoryId) {
                        throw new Error("No category ID found");
                    }

                    let transactions = additional.recurringTransactions.filter(
                        (t) => t.category_id === category.id
                    );

                    transactions.forEach((transaction) => {
                        transaction.category_id = categoryId;
                    });

                    transactions = additional.salaryTransactions.filter(
                        (t) => t.category_id === category.id
                    );

                    transactions.forEach((transaction) => {
                        transaction.category_id = categoryId;
                    });

                    category.id = categoryId;
                })
                .catch((e) => {
                    toast.error({
                        component: ToastText,
                        props: {
                            type: "error",
                            message: `Couldn't create ${category.name} category`,
                        },
                    });
                })
                .finally(() => {
                    if (i === additional.categories.length) {
                        processing.categories = false;
                    } else {
                        i++;
                    }
                });
        });
    } else {
        processing.categories = false;
    }
};

const processPayees = () => {
    if (additional.payees.length > 0) {
        processing.payees = true;

        let i = 1;
        additional.payees.forEach((payee) => {
            payee.budget_id = settings.budgetId;
            createPayee(payee);

            if (i === additional.payees.length) {
                processing.payees = false;
            } else {
                i++;
            }
        });

        return true;
    } else {
        processing.payees = false;
    }
};

const incomeCategoryOptions = computed(() => {
    const existingCategories = usePage().props.value.categories.filter(
        (category) => category.type === 1
    );
    const newCategories = additional.categories.filter(
        (category) => category.type === 1
    );

    return getSortedOptions(existingCategories, newCategories);
});

const expenditureCategoryOptions = computed(() => {
    const existingCategories = usePage().props.value.categories.filter(
        (category) => category.type === 2
    );
    const newCategories = additional.categories.filter(
        (category) => category.type === 2
    );

    return getSortedOptions(existingCategories, newCategories);
});

const payeeOptions = computed(() => {
    return getSortedOptions(usePage().props.value.payees, additional.payees);
});

const canAddTransaction = computed(() => {
    for (const index in additional.recurringTransactions) {
        if (additional.recurringTransactions[index].expanded) {
            return false;
        }
    }

    for (const index in additional.salaryTransactions) {
        if (additional.salaryTransactions[index].expanded) {
            return false;
        }
    }

    return true;
});

const currencySymbol = computed(() => {
    const currency = currencies.find((c) => c.id === form.currency_id);

    if (isEmpty(currency)) {
        return "";
    }

    return currency.symbol;
});
</script>

<template>
    <Head title="Create Budget" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                Create Budget
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                    Create a new budget
                </span>
            </h2>
        </template>

        <LoadingSpinner
            :loading="
                processing.budget ||
                processing.recurringTransactions ||
                processing.salaryTransactions ||
                processing.categories ||
                processing.payees
            "
            label="Creating budget..."
        ></LoadingSpinner>

        <div v-if="hasBudgetLimit" class="text-center w-full">
            <img
                src="/img/splash/undraw_warning.svg"
                class="w-full max-w-[200px] mx-auto"
            />

            <div class="flex flex-col items-center justify-center mt-6">
                <div class="flex-1">
                    <h6 class="font-title text-base font-bold text-primary-500">
                        Whoa there...looks like you already have a budget.
                    </h6>

                    <p class="font-medium text-slate-600 mt-1">
                        If you want to have multiple budgets, you'll need to
                        upgrade.
                    </p>
                </div>

                <div class="flex-0 mt-6">
                    <Button
                        label="Upgrade plan"
                        :href="route('web.budgets.create')"
                        type="link"
                        buttonStyle="black"
                        icon="arrow"
                    />

                    <Button
                        label="View Budget"
                        :href="route('web.budgets.create')"
                        type="link"
                        buttonStyle="primary"
                        icon="arrow"
                        outline
                        class="mt-2"
                    />
                </div>
            </div>
        </div>

        <div v-else class="w-full">
            <p class="w-full text-right text-xs text-slate-500 mb-3 pr-2">
                <span class="text-red-500 font-bold"> * </span> means the field
                is required
            </p>
            <FormSection
                heading="Basic Details"
                description="Tell us the basic details of your budget to get started"
            >
                <CreateBudgetForm v-model="form" :validation="v$" />
            </FormSection>

            <hr class="border-slate-200 my-8" />

            <FormSection
                heading="Income and Expenditure"
                description="Tell us the regular, recurring payments that you see in your budget. This could including things like your mortgage, groceries, salary, or streaming services"
            >
                <ToggleInput
                    label="I don't want to add any regular payments right now"
                    v-model="settings.noTransactions"
                />

                <template v-if="settings.noTransactions">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.2"
                        stroke="currentColor"
                        class="w-16 h-16 mx-auto mt-8 text-secondary-500"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z"
                        />
                    </svg>

                    <div class="flex flex-col items-center justify-center mt-2">
                        <div class="flex-1">
                            <h6
                                class="font-title text-base text-center font-bold text-primary-500"
                            >
                                Understood.
                            </h6>

                            <p class="font-medium text-slate-600 mt-1">
                                We won't add any regular payments.
                            </p>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="mt-6">
                        <ToggleInput
                            label="I want to include salaries in my budget"
                            v-model="settings.includeSalary"
                        />

                        <div v-show="settings.includeSalary" class="flex mt-6">
                            <Button
                                label="Add salary"
                                buttonStyle="secondary"
                                icon="plus"
                                @click="addSalary"
                                :disabled="!canAddTransaction"
                            />
                        </div>

                        <div
                            class="grid 3xl:grid-cols-3 xl:grid-cols-2 grid-cols-1 gap-6 mt-6"
                        >
                            <div
                                v-for="(
                                    transaction, index
                                ) in additional.salaryTransactions"
                                :key="`add-salary-form-${index}-${transaction.id}`"
                                class="p-6 rounded-lg bg-card-gray col-span-1"
                                :class="{
                                    '3xl:col-span-3 xl:col-span-2':
                                        transaction.expanded,
                                }"
                            >
                                <div v-show="transaction.expanded">
                                    <h6
                                        class="font-title font-bold mb-6 text-primary-500"
                                    >
                                        Add Salary
                                    </h6>
                                    <CreateSalaryForm
                                        v-model="
                                            additional.salaryTransactions[index]
                                        "
                                        :incomeCategoryOptions="
                                            incomeCategoryOptions
                                        "
                                        :payeeOptions="payeeOptions"
                                        v-on:delete="removeSalary(index)"
                                        hasSave
                                        hasDelete
                                    />
                                </div>

                                <div v-if="!transaction.expanded">
                                    <div
                                        class="flex items-center gap-4 pb-2 border-b border-[#E0E5F6]"
                                    >
                                        <div class="flex-1">
                                            <h6
                                                class="text-base font-bold text-primary-500 font-title"
                                            >
                                                {{ transaction.name }}
                                            </h6>
                                        </div>

                                        <div class="flex-0">
                                            <Button
                                                v-if="canAddTransaction"
                                                label="Edit"
                                                buttonStyle="secondary"
                                                @click="
                                                    transaction.expanded = true
                                                "
                                                linkStyle
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Amount
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                `${currencySymbol}${transaction.amount}`
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Frequency
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                !isEmpty(
                                                    transaction.time_period_amount
                                                )
                                                    ? `Every ${
                                                          transaction.time_period_amount
                                                      } ${
                                                          timePeriods.find(
                                                              (t) =>
                                                                  t.id ===
                                                                  transaction.time_period_id
                                                          ).name
                                                      }(s)`
                                                    : capitalise(
                                                          timePeriods.find(
                                                              (t) =>
                                                                  t.id ===
                                                                  transaction.time_period_id
                                                          ).name
                                                      )
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Type
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                transaction.transaction_type ==
                                                1
                                                    ? "Income"
                                                    : "Expenditure"
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Category
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                transaction.transaction_type ==
                                                1
                                                    ? incomeCategoryOptions.find(
                                                          (o) =>
                                                              o.value ===
                                                              transaction.category_id
                                                      )?.name
                                                    : expenditureCategoryOptions.find(
                                                          (o) =>
                                                              o.value ===
                                                              transaction.category_id
                                                      )?.name
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Starts
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{ transaction.starts_at }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Ends
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{ transaction.ends_at ?? "N/A" }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex mt-6">
                            <Button
                                label="Add regular payment"
                                buttonStyle="secondary"
                                icon="plus"
                                @click="addRecurringTransaction"
                                :disabled="!canAddTransaction"
                            />
                        </div>

                        <div
                            class="grid 3xl:grid-cols-3 xl:grid-cols-2 grid-cols-1 gap-6 mt-6"
                        >
                            <div
                                v-for="(
                                    transaction, index
                                ) in additional.recurringTransactions"
                                :key="`add-transaction-form-${index}-${transaction.id}`"
                                class="p-6 rounded-lg bg-card-gray col-span-1"
                                :class="{
                                    '3xl:col-span-3 xl:col-span-2':
                                        transaction.expanded,
                                }"
                            >
                                <div v-show="transaction.expanded">
                                    <h6
                                        class="font-title font-bold mb-6 text-primary-500"
                                    >
                                        Add Regular Payment
                                    </h6>
                                    <CreateRecurringTransactionForm
                                        v-model="
                                            additional.recurringTransactions[
                                                index
                                            ]
                                        "
                                        :incomeCategoryOptions="
                                            incomeCategoryOptions
                                        "
                                        :expenditureCategoryOptions="
                                            expenditureCategoryOptions
                                        "
                                        :payeeOptions="payeeOptions"
                                        v-on:delete="removeTransaction(index)"
                                        hasSave
                                        hasDelete
                                    />
                                </div>

                                <div v-if="!transaction.expanded">
                                    <div
                                        class="flex items-center gap-4 pb-2 border-b border-[#E0E5F6]"
                                    >
                                        <div class="flex-1">
                                            <h6
                                                class="text-base font-bold text-primary-500 font-title"
                                            >
                                                {{ transaction.name }}
                                            </h6>
                                        </div>

                                        <div class="flex-0">
                                            <Button
                                                v-if="canAddTransaction"
                                                label="Edit"
                                                buttonStyle="secondary"
                                                @click="
                                                    transaction.expanded = true
                                                "
                                                linkStyle
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Amount
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                `${currencySymbol}${transaction.amount}`
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Frequency
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                !isEmpty(
                                                    transaction.time_period_amount
                                                )
                                                    ? `Every ${
                                                          transaction.time_period_amount
                                                      } ${
                                                          timePeriods.find(
                                                              (t) =>
                                                                  t.id ===
                                                                  transaction.time_period_id
                                                          ).name
                                                      }(s)`
                                                    : capitalise(
                                                          timePeriods.find(
                                                              (t) =>
                                                                  t.id ===
                                                                  transaction.time_period_id
                                                          ).name
                                                      )
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Type
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                transaction.transaction_type ==
                                                1
                                                    ? "Income"
                                                    : "Expenditure"
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Category
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{
                                                transaction.transaction_type ==
                                                1
                                                    ? incomeCategoryOptions.find(
                                                          (o) =>
                                                              o.value ===
                                                              transaction.category_id
                                                      )?.name
                                                    : expenditureCategoryOptions.find(
                                                          (o) =>
                                                              o.value ===
                                                              transaction.category_id
                                                      )?.name
                                            }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Starts
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{ transaction.starts_at }}
                                        </div>
                                    </div>

                                    <div class="mt-6 flex items-center gap-4">
                                        <div class="flex-1 text-slate-500">
                                            Ends
                                        </div>

                                        <div
                                            class="flex-1 text-right font-bold text-primary-500"
                                        >
                                            {{ transaction.ends_at ?? "N/A" }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </FormSection>

            <hr class="border-slate-200 my-8" />

            <FormSection
                heading="Categories"
                description="Tell us any custom categories you'd like to sort the items on your budget into"
            >
                <ToggleInput
                    label="I don't want to add any categories right now"
                    v-model="settings.noCategories"
                />

                <template v-if="settings.noCategories">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.2"
                        stroke="currentColor"
                        class="w-16 h-16 mx-auto mt-8 text-secondary-500"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z"
                        />
                    </svg>

                    <div class="flex flex-col items-center justify-center mt-2">
                        <div class="flex-1">
                            <h6
                                class="font-title text-base text-center font-bold text-primary-500"
                            >
                                Understood.
                            </h6>

                            <p class="font-medium text-slate-600 mt-1">
                                We won't add any categories.
                            </p>
                        </div>
                    </div>
                </template>

                <div v-else class="mt-6">
                    <div class="p-6 rounded-lg bg-card-gray">
                        <CreateCategoryForm v-on:add="addCategory" hasSave />
                    </div>

                    <div
                        class="grid 2xl:grid-cols-3 xl:grid-cols-2 grid-cols-1 gap-4 mt-6"
                    >
                        <div
                            v-for="(category, index) in additional.categories"
                            :key="`new-category-${index}-${category.id}`"
                            class="rounded-xl p-4 border border-slate-100 shadow bg-white flex items-center gap-4"
                        >
                            <div
                                class="flex-1 border-l-4 pl-2"
                                :class="{
                                    'border-green-500': category.type === 1,
                                    'border-red-500': category.type === 2,
                                }"
                            >
                                <h6 class="font-bold font-title text-slate-600">
                                    {{ category.name }}
                                </h6>

                                <caption
                                    class="text-slate-400 text-xs font-semibold"
                                >
                                    {{
                                        category.type === 1
                                            ? "Income"
                                            : "Expenditure"
                                    }}
                                </caption>
                            </div>

                            <div class="flex-0">
                                <button
                                    class="text-primary-500 hover:text-primary-300 mt-1"
                                    @click="removeCategory(index)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </FormSection>

            <hr class="border-slate-200 my-8" />

            <FormSection
                heading="Payees"
                description="Tell us the people and businesses that you are either getting money from or giving money to"
            >
                <ToggleInput
                    label="I don't want to add any payees right now"
                    v-model="settings.noPayees"
                />

                <template v-if="settings.noPayees">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.2"
                        stroke="currentColor"
                        class="w-16 h-16 mx-auto mt-8 text-secondary-500"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z"
                        />
                    </svg>

                    <div class="flex flex-col items-center justify-center mt-2">
                        <div class="flex-1">
                            <h6
                                class="font-title text-base text-center font-bold text-primary-500"
                            >
                                Understood.
                            </h6>

                            <p class="font-medium text-slate-600 mt-1">
                                We won't add any payees.
                            </p>
                        </div>
                    </div>
                </template>

                <div v-else class="mt-6">
                    <div class="p-6 rounded-lg bg-card-gray">
                        <CreatePayeeForm v-on:add="addPayee" hasSave />
                    </div>

                    <div
                        class="grid 2xl:grid-cols-3 xl:grid-cols-2 grid-cols-1 gap-4 mt-6"
                    >
                        <div
                            v-for="(payee, index) in additional.payees"
                            :key="`new-category-${index}-${payee.id}`"
                            class="rounded-xl p-4 border border-slate-100 shadow bg-white flex items-center gap-4"
                        >
                            <div
                                class="flex-1 border-l-4 pl-2 border-secondary-500"
                            >
                                <h6 class="font-bold font-title text-slate-600">
                                    {{ payee.name }}
                                </h6>
                            </div>

                            <div class="flex-0">
                                <button
                                    class="text-primary-500 hover:text-primary-300 mt-1"
                                    @click="removePayee(index)"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </FormSection>

            <div class="flex justify-end">
                <Button
                    label="Create this budget"
                    buttonStyle="primary"
                    icon="arrow"
                    class="mt-8"
                    :disabled="!v$.$anyDirty || v$.$errors.length > 0"
                    @click="submitBudget"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
