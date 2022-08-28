<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import helpers from "@/helpers";

import { MoonLoader } from "vue-spinner/dist/vue-spinner.min.js";

const props = defineProps({
    label: {
        type: String,
        default: "Submit",
    },
    buttonStyle: {
        type: String,
        default: "primary",
    },
    type: {
        type: String,
        default: "button",
    },
    fullWidth: {
        type: Boolean,
        default: false,
    },
    outline: {
        type: Boolean,
        default: false,
    },
    linkStyle: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    href: {
        type: String,
        default: null,
    },
    icon: {
        type: String,
        default: null,
    },
});

const spinnerColor = computed(() => {
    if (props.outline || props.linkStyle) {
        return "#000000";
    }

    return "#FFFFFF";
});

const classes = computed(() => {
    const defaultClasses =
        "rounded-lg text-sm tracking-wide py-2.5 px-4 border transition ease-in-out flex items-center justify-center";
    let classList = defaultClasses;

    if (props.outline) {
        switch (props.buttonStyle) {
            case "black":
                classList +=
                    " font-semibold bg-transparent hover:bg-slate-700 text-slate-600 hover:text-white border-slate-900/10 hover:border-slate-900/20";
                break;
            case "secondary":
                classList +=
                    " font-semibold bg-transparent hover:bg-secondary-600 text-secondary-600 hover:text-white border-secondary-500/20 hover:border-secondary-600";
                break;
            case "tertiary":
                classList +=
                    " font-semibold bg-transparent hover:bg-tertiary-600 text-tertiary-600 hover:text-white border-tertiary-500/20 hover:border-tertiary-600";
                break;
            default:
                classList +=
                    " font-semibold bg-transparent text-primary-600 border-primary-500/20 hover:border-primary-500";
                break;
        }
    } else if (props.linkStyle) {
        switch (props.buttonStyle) {
            case "black":
                classList +=
                    " bg-transparent text-slate-600 hover:text-slate-700 hover:underline border-transparent";
                break;
            case "secondary":
                classList +=
                    " bg-transparent text-secondary-500 hover:text-secondary-600 hover:underline border-transparent";
                break;
            case "tertiary":
                classList +=
                    " bg-transparent text-tertiary-500 hover:text-tertiary-600 hover:underline border-transparent";
                break;
            default:
                classList +=
                    " bg-transparent text-primary-500 hover:text-primary-600 hover:underline border-transparent";
                break;
        }
    } else {
        switch (props.buttonStyle) {
            case "black":
                classList +=
                    " font-semibold bg-slate-900 hover:bg-slate-700 text-white border-slate-900 hover:border-slate-700";
                break;
            case "secondary":
                classList +=
                    " font-semibold bg-secondary-500 hover:bg-secondary-600 text-white border-secondary-500 hover:border-secondary-600";
                break;
            case "tertiary":
                classList +=
                    " font-semibold bg-tertiary-500 hover:bg-tertiary-600 text-white border-tertiary-500 hover:border-tertiary-600";
                break;
            default:
                classList +=
                    " font-semibold bg-primary-500 hover:bg-primary-600 text-white border-primary-500 hover:border-primary-600";
                break;
        }
    }

    if (props.fullWidth) {
        classList += " w-full";
    }

    if (props.disabled) {
        classList += " pointer-events-none opacity-25";
    }

    return classList;
});
</script>

<template>
    <div>
        <button v-if="type === 'button'" :class="classes">
            <template v-if="loading">
                <MoonLoader :color="spinnerColor" size="20px"></MoonLoader>
            </template>
            <template v-else>
                <p>
                    {{ label }}
                </p>
                <svg
                    v-if="icon === 'arrow'"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.5"
                    stroke="currentColor"
                    class="w-3 h-3 inline-block ml-2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                    />
                </svg>
            </template>
        </button>
        <Link
            v-if="type === 'link'"
            :class="`${classes} cursor-pointer block text-center`"
            :href="href"
        >
            <template v-if="loading">
                <MoonLoader :color="spinnerColor" size="20px"></MoonLoader>
            </template>
            <template v-else>
                <p>
                    {{ label }}
                </p>

                <svg
                    v-if="icon === 'arrow'"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2.5"
                    stroke="currentColor"
                    class="w-3 h-3 inline-block ml-2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"
                    />
                </svg>
            </template>
        </Link>
    </div>
</template>
