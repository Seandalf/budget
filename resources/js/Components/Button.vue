<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

import RightArrow from "@/Components/ButtonIcons/RightArrow.vue";
import DownArrow from "@/Components/ButtonIcons/DownArrow.vue";

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
    noUnderline: {
        type: Boolean,
        default: false,
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
        "rounded-lg text-sm tracking-wide py-2.5 px-4 border transition ease-in-out flex items-center justify-center group";
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
            case "success":
                classList +=
                    " font-semibold bg-transparent hover:bg-green-600 text-green-600 hover:text-white border-green-500/20 hover:border-green-600";
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
                    " bg-transparent text-slate-600 hover:text-slate-700 border-transparent";
                break;
            case "secondary":
                classList +=
                    " bg-transparent text-secondary-500 hover:text-secondary-600 border-transparent";
                break;
            case "tertiary":
                classList +=
                    " bg-transparent text-tertiary-500 hover:text-tertiary-600 border-transparent";
                break;
            case "tertiary":
                classList +=
                    " bg-transparent text-green-500 hover:text-green-600 border-transparent";
                break;
            default:
                classList +=
                    " bg-transparent text-primary-500 hover:text-primary-600 border-transparent";
                break;
        }

        if (!props.noUnderline) {
            classList += " hover:underline";
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
            case "success":
                classList +=
                    " font-semibold bg-green-500 hover:bg-green-600 text-white border-green-500 hover:border-green-600";
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

                <RightArrow v-if="icon === 'arrow'" />
                <DownArrow v-if="icon === 'down-arrow'" />
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

                <RightArrow v-if="icon === 'arrow'" />
                <DownArrow v-if="icon === 'down-arrow'" />
            </template>
        </Link>
    </div>
</template>
