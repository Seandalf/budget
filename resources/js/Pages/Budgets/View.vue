<script setup>
import { computed, onMounted, reactive } from "vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { userHasPermission, convertToCurrency, isEmpty } from "@/helpers";
import moment from "moment";
import { useToast } from "vue-toastification";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Button from "@/Components/Button.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ToastText from "@/Components/ToastText.vue";

import IntervalView from "./Components/IntervalView.vue";
import OverviewView from "./Components/Overview.vue";

const budget = usePage().props.value.budget;

const data = reactive({
    intervals: [],
});

const settings = reactive({
    loading: false,
    mode: "interval",
});

const toast = useToast();

const fetchCategoriesByInterval = () => {
    settings.loading = true;
    axios
        .get(route("api.intervals.index", usePage().props.value.budget.id))
        .then((res) => {
            data.intervals = res.data.data;
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
            settings.loading = false;
        });
};

const selectedInterval = computed(() => {
    if (data.intervals.length === 0) {
        return null;
    }

    return data.intervals.find((i) => i.statistics.is_current === true);
});

onMounted(() => {
    fetchCategoriesByInterval();
});
</script>

<template>
    <Head title="View Budgets" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold font-title text-2xl text-gray-800 leading-tight"
            >
                View Budget:
                <span class="text-primary-500">{{ budget.name }}</span>
                <span
                    class="text-sm tracking-wide font-normal font-sans text-slate-400 ml-2"
                >
                </span>
            </h2>
        </template>

        <LoadingSpinner
            :loading="settings.loading"
            label="Loading budget..."
        ></LoadingSpinner>

        <div class="flex items-center gap-4">
            <div class="flex-1 flex items-center">
                <Button
                    v-if="settings.mode === 'interval'"
                    label="Budget Overview"
                    buttonStyle="black"
                    outline
                    @click="settings.mode = 'overview'"
                />
            </div>

            <div
                v-if="settings.mode === 'interval'"
                class="flex-1 flex items-center justify-end gap-4"
            >
                <Button
                    v-if="!selectedInterval.statistics.is_first"
                    label=""
                    buttonStyle="white"
                    icon="left-chevron"
                    @click="previousInterval"
                />

                <Button
                    label=""
                    buttonStyle="white"
                    icon="right-chevron"
                    @click="nextInterval"
                />
            </div>
        </div>

        <div class="mt-8">
            <IntervalView
                v-if="
                    settings.mode === 'interval' && data.intervals.length !== 0
                "
                :interval="selectedInterval"
            />

            <OverviewView
                v-if="
                    settings.mode === 'overview' && data.intervals.length !== 0
                "
                :intervals="data.intervals"
            />
        </div>
    </AuthenticatedLayout>
</template>
