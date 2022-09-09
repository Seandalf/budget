<script setup>
import { computed, onMounted, reactive } from "vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import { userHasPermission, convertToCurrency, isEmpty } from "@/helpers";
import moment from "moment";
import { useToast } from "vue-toastification";

import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ToastText from "@/Components/ToastText.vue";

import IntervalView from "./Components/IntervalView.vue";
import OverviewView from "./Components/Overview.vue";

const budget = usePage().props.value.budget;

const data = reactive({
    intervals: [],
    selectedInterval: null,
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

const changeInterval = (direction) => {
    const currentIndex = data.intervals.indexOf(selectedInterval.value);
    let nextIndex = currentIndex;

    if (direction === "next" && nextIndex !== data.intervals.length - 1) {
        nextIndex++;
    }

    if (direction === "previous" && currentIndex > 0) {
        nextIndex--;
    }

    data.selectedInterval = data.intervals[nextIndex];
};

const selectedInterval = computed(() => {
    if (data.intervals.length === 0) {
        return null;
    }
    data.intervals.find((i) => i.statistics.is_current === true);

    if (!isEmpty(data.selectedInterval)) {
        return data.selectedInterval;
    }

    return data.intervals.find((i) => i.statistics.is_current === true);
});

const lastIntervalId = computed(() => {
    return data.intervals[data.intervals.length - 1].id;
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

        <div class="mt-8">
            <IntervalView
                v-if="
                    settings.mode === 'interval' && data.intervals.length !== 0
                "
                :interval="selectedInterval"
                :lastInterval="selectedInterval.id === lastIntervalId"
                v-on:overview="settings.mode = 'overview'"
                v-on:previousInterval="changeInterval('previous')"
                v-on:nextInterval="changeInterval('next')"
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
