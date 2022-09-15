<script setup>
import { convertToCurrency } from "@/Helpers";

import MainButton from "@/Components/Button.vue";

const props = defineProps({
    categories: {
        type: [Array, Object],
    },
    currency: {
        type: String,
        default: "AUD",
    },
});
</script>

<template>
    <div class="p-8 bg-white rounded-lg shadow border border-slate-50">
        <div class="flex items-center border-b border-slate-200 pb-6">
            <div class="flex-1 flex items-center">
                <h3 class="font-bold text-xl flex-0">Your Income</h3>

                <p
                    class="bg-slate-200 text-primary-500 font-semibold py-1 pl-2 pr-2.5 rounded-full text-xs ml-3"
                >
                    {{ Object.keys(categories.income.items).length }}
                </p>
            </div>

            <div class="flex-0 font-semibold tracking-wide text-green-500">
                {{ convertToCurrency(categories.income.total, currency) }}
            </div>
        </div>

        <div class="flex items-center w-full items-stretch">
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-left px-2 py-3 flex-0 w-[170px]"
            >
                Category
            </div>
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-center px-2 py-3 flex-1"
            >
                Budget
            </div>
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-center px-2 py-3 flex-1"
            >
                Actual
            </div>
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-left px-2 py-3 flex-0 w-[90px]"
            ></div>
        </div>

        <template
            v-for="(category, title) in categories.income.items"
            :key="`transaction-row-income-${title}`"
        >
            <div class="flex items-center w-full items-stretch">
                <div
                    class="border-b border-slate-200 text-primary-500 font-bold text-left px-2 py-3 font-title flex-0 w-[170px]"
                >
                    <p>{{ title }}</p>
                    <p class="text-xs font-medium text-slate-400">
                        {{ category.items.length }} item{{
                            category.items.length !== 1 ? "s" : ""
                        }}
                    </p>
                </div>

                <div
                    class="border-b border-slate-200 text-slate-600 text-center px-2 py-4 flex-1 pt-5"
                >
                    {{ convertToCurrency(category.budget, currency) }}
                </div>

                <div
                    class="border-b border-slate-200 font-semibold text-secondary-500 text-center px-2 py-4 flex-1 pt-5"
                >
                    {{ convertToCurrency(category.actual, currency) }}
                </div>

                <div
                    class="border-b border-slate-200 text-slate-600 text-center px-2 py-3 flex justify-end gap-2 flex-0 w-[90px]"
                >
                    <button
                        class="bg-slate-100 p-2 rounded-lg hover:bg-slate-200 group disabled:pointer-events-none disabled:opacity-50"
                        @click="category.view = !category.view"
                        :disabled="category.items.length === 0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-4 h-4 transition-transform ease-in-out duration-300"
                            :class="{ 'rotate-180': category.view }"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>

                    <button
                        class="bg-slate-100 p-2 rounded-lg hover:bg-slate-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="category.view" class="bg-slate-50 w-full p-4">
                <table class="w-full rounded">
                    <tr>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-left"
                        >
                            Name
                        </th>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-center"
                        >
                            Budget
                        </th>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-center"
                        >
                            Actual
                        </th>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-center"
                        ></th>
                    </tr>

                    <tr
                        v-for="(item, index) in category.items"
                        :key="`t-table-income-row-${index}`"
                    >
                        <td
                            class="bg-white border border-slate-100 text-primary-500 font-medium px-3 py-2 text-left"
                        >
                            {{ item.name }}
                        </td>
                        <td
                            class="bg-white border border-slate-100 text-slate-500 px-3 py-2 text-center"
                        >
                            {{ convertToCurrency(item.budget, currency) }}
                        </td>
                        <td
                            class="bg-white border border-slate-100 text-primary-500 font-semibold px-3 py-2 text-center"
                        >
                            {{ convertToCurrency(item.actual, currency) }}
                        </td>
                        <td
                            class="bg-white border border-slate-100 text-slate-500 p-2 text-center w-[35px]"
                        >
                            <MainButton
                                label="View/Edit"
                                buttonStyle="black"
                                linkStyle
                            />
                        </td>
                    </tr>
                </table>
            </div>
        </template>
    </div>

    <div class="p-8 bg-white rounded-lg shadow border border-slate-50 mt-8">
        <div class="flex items-center border-b border-slate-200 pb-6">
            <div class="flex-1 flex items-center">
                <h3 class="font-bold text-xl flex-0">Your Expenditure</h3>

                <p
                    class="bg-slate-200 text-primary-500 font-semibold py-1 pl-2 pr-2.5 rounded-full text-xs ml-3"
                >
                    {{ Object.keys(categories.expenditure.items).length }}
                </p>
            </div>

            <div class="flex-0 font-semibold tracking-wide text-tertiary-500">
                {{ convertToCurrency(categories.expenditure.total, currency) }}
            </div>
        </div>

        <div class="flex items-center w-full items-stretch">
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-left px-2 py-3 flex-0 w-[170px]"
            >
                Category
            </div>
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-center px-2 py-3 flex-1"
            >
                Budget
            </div>
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-center px-2 py-3 flex-1"
            >
                Actual
            </div>
            <div
                class="bg-slate-100 border-b border-slate-200 text-slate-600 font-medium text-left px-2 py-3 flex-0 w-[90px]"
            ></div>
        </div>

        <template
            v-for="(category, title) in categories.expenditure.items"
            :key="`transaction-row-income-${title}`"
        >
            <div class="flex items-center w-full items-stretch">
                <div
                    class="border-b border-slate-200 text-primary-500 font-bold text-left px-2 py-3 font-title flex-0 w-[170px]"
                >
                    <p>{{ title }}</p>
                    <p class="text-xs font-medium text-slate-400">
                        {{ category.items.length }} item{{
                            category.items.length !== 1 ? "s" : ""
                        }}
                    </p>
                </div>

                <div
                    class="border-b border-slate-200 text-slate-600 text-center px-2 py-4 flex-1 pt-5"
                >
                    {{ convertToCurrency(category.budget, currency) }}
                </div>

                <div
                    class="border-b border-slate-200 font-semibold text-secondary-500 text-center px-2 py-4 flex-1 pt-5"
                >
                    {{ convertToCurrency(category.actual, currency) }}
                </div>

                <div
                    class="border-b border-slate-200 text-slate-600 text-center px-2 py-3 flex justify-end gap-2 flex-0 w-[90px]"
                >
                    <button
                        class="bg-slate-100 p-2 rounded-lg hover:bg-slate-200 group disabled:pointer-events-none disabled:opacity-50"
                        @click="category.view = !category.view"
                        :disabled="category.items.length === 0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-4 h-4 transition-transform ease-in-out duration-300"
                            :class="{ 'rotate-180': category.view }"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>

                    <button
                        class="bg-slate-100 p-2 rounded-lg hover:bg-slate-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="w-4 h-4"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="category.view" class="bg-slate-50 w-full p-4">
                <table class="w-full rounded">
                    <tr>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-left"
                        >
                            Name
                        </th>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-center"
                        >
                            Budget
                        </th>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-center"
                        >
                            Actual
                        </th>
                        <th
                            class="bg-primary-500 border border-primary-500 text-white font-medium p-2 text-center"
                        ></th>
                    </tr>

                    <tr
                        v-for="(item, index) in category.items"
                        :key="`t-table-income-row-${index}`"
                    >
                        <td
                            class="bg-white border border-slate-100 text-primary-500 font-medium px-3 py-2 text-left"
                        >
                            {{ item.name }}
                        </td>
                        <td
                            class="bg-white border border-slate-100 text-slate-500 px-3 py-2 text-center"
                        >
                            {{ convertToCurrency(item.budget, currency) }}
                        </td>
                        <td
                            class="bg-white border border-slate-100 text-primary-500 font-semibold px-3 py-2 text-center"
                        >
                            {{ convertToCurrency(item.actual, currency) }}
                        </td>
                        <td
                            class="bg-white border border-slate-100 text-slate-500 p-2 text-center w-[35px]"
                        >
                            <MainButton
                                label="View/Edit"
                                buttonStyle="black"
                                linkStyle
                            />
                        </td>
                    </tr>
                </table>
            </div>
        </template>
    </div>
</template>
