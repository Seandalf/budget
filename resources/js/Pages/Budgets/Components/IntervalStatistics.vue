<script setup>
import { convertToCurrency } from "@/Helpers";

defineProps({
    interval: {
        type: Object,
        default: () => {},
    },
    currency: {
        type: String,
        default: "AUD",
    },
});
</script>

<template>
    <div
        class="bg-white rounded-lg px-2 py-6 1.5xl:px-6 1.5xl:py-2 grid grid-cols-3 1.5xl:grid-cols-1 divide-x divide-y-0 1.5xl:divide-x-0 1.5xl:divide-y divide-slate-100"
    >
        <div class="px-4 1.5xl:px-0 1.5xl:py-6 flex items-center gap-4">
            <div class="flex-0">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-10 h-10 p-1 1.5xl:w-12 1.5xl:h-12 1.5xl:p-2 rounded-lg"
                    :class="{
                        ' text-lime-600 bg-lime-100':
                            interval.opening_balance >= 0,
                        ' text-tertiary-600 bg-red-100':
                            interval.opening_balance < 0,
                    }"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>

            <div class="flex-1">
                <h3
                    class="font-extrabold text-xl"
                    :class="{
                        'text-tertiary-600': interval.opening_balance < 0,
                        'text-slate-700': interval.opening_balance >= 0,
                    }"
                >
                    {{ convertToCurrency(interval.opening_balance, currency) }}
                </h3>
                <h6 class="text-primary-500 font-medium pl-1 font-title">
                    Opening Balance
                </h6>
            </div>
        </div>

        <div class="px-4 1.5xl:px-0 1.5xl:py-6 flex items-center gap-4">
            <div class="flex-0">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-10 h-10 p-1 1.5xl:w-12 1.5xl:h-12 1.5xl:p-2 rounded-lg"
                    :class="{
                        'text-cyan-600 bg-cyan-100':
                            interval.opening_balance >= 0,
                        'text-tertiary-600 bg-red-100':
                            interval.opening_balance < 0,
                    }"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"
                    />
                </svg>
            </div>

            <div class="flex-1">
                <h3
                    class="font-extrabold text-xl"
                    :class="{
                        'text-tertiary-600': interval.opening_balance < 0,
                        'text-slate-700': interval.opening_balance >= 0,
                    }"
                >
                    {{ convertToCurrency(interval.closing_balance, currency) }}
                </h3>
                <h6 class="text-primary-500 font-medium pl-1 font-title">
                    Closing Balance
                </h6>
            </div>
        </div>

        <div class="px-4 1.5xl:px-0 1.5xl:py-6 flex items-center gap-4">
            <div class="flex-0">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-10 h-10 p-1 1.5xl:w-12 1.5xl:h-12 1.5xl:p-2 rounded-lg"
                    :class="{
                        'text-indigo-500 bg-indigo-100':
                            interval.opening_balance >= 0,
                        'text-tertiary-600 bg-red-100':
                            interval.opening_balance < 0,
                    }"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"
                    />
                </svg>
            </div>

            <div class="flex-1">
                <h3
                    class="font-extrabold text-xl"
                    :class="{
                        'text-tertiary-600': interval.opening_balance < 0,
                        'text-slate-700': interval.opening_balance >= 0,
                    }"
                >
                    {{
                        convertToCurrency(
                            interval.statistics.current_bank_balance,
                            currency
                        )
                    }}
                </h3>
                <h6 class="text-primary-500 font-medium pl-1 font-title">
                    Bank Balance
                </h6>
            </div>
        </div>

        <div
            class="px-4 1.5xl:px-0 1.5xl:py-6 items-center gap-4 hidden 1.5xl:flex"
        >
            <div class="flex-0">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-10 h-10 p-1 1.5xl:w-12 1.5xl:h-12 1.5xl:p-2 text-purple-500 bg-purple-100 rounded-lg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>

            <div class="flex-1">
                <h3 class="text-slate-700 font-extrabold text-xl">
                    {{
                        convertToCurrency(
                            interval.statistics.remaining_income,
                            currency
                        )
                    }}
                </h3>
                <h6 class="text-primary-500 font-medium pl-1 font-title">
                    Rem. Income
                </h6>
            </div>
        </div>

        <div
            class="px-4 1.5xl:px-0 1.5xl:py-6 items-center gap-4 hidden 1.5xl:flex"
        >
            <div class="flex-0">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-10 h-10 p-1 1.5xl:w-12 1.5xl:h-12 1.5xl:p-2 text-pink-500 bg-pink-100 rounded-lg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3V15"
                    />
                </svg>
            </div>

            <div class="flex-1">
                <h3 class="text-slate-700 font-extrabold text-xl">
                    {{ convertToCurrency(123456, currency) }}
                </h3>
                <h6 class="text-primary-500 font-medium pl-1 font-title">
                    Rem. Expenses
                </h6>
            </div>
        </div>
    </div>
</template>
