<script setup>
import { reactive, computed } from "vue";
import { Link, usePage } from "@inertiajs/inertia-vue3";
import { userHasPermission } from "@/helpers";

import Button from "@/Components/Button.vue";
import SidebarButton from "@/Components/SidebarButton.vue";

const user = usePage().props.value.auth.user;

const menus = reactive({
    mobile: {
        sidebar: {
            show: false,
        },
    },
});

const onClickProfileButton = () => {
    menus.full.profile.show = !menus.full.profile.show;
};

const hasBudgets = computed(() => {
    return usePage().props.value.auth.total_budgets > 0;
});
</script>

<template>
    <div class="text-sm">
        <div class="min-h-screen bg-primary-500 flex flex-col lg:flex-row">
            <!-- full sidebar -->
            <div
                class="flex-0 lg:w-72 p-6 pt-4 max-h-screen overflow-hidden lg:overflow-y-scroll transition ease-in-out relative"
                :class="{ 'h-20 lg:h-full': !menus.mobile.sidebar.show }"
            >
                <div
                    class="absolute h-10 w-10 rounded-lg bg-white/5 text-white hover:bg-white/10 p-1 mt-1 cursor-pointer block lg:hidden"
                    @click="
                        menus.mobile.sidebar.show = !menus.mobile.sidebar.show
                    "
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="w-8 h-8"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>

                <h1 class="font-logo text-4xl text-center text-white">
                    Open Budget
                </h1>

                <h6
                    class="text-white text-center mt-4 opacity-90 font-medium font-title hidden lg:block"
                >
                    Welcome, {{ user.first_name }}!
                </h6>

                <div class="mt-8 flex flex-col gap-1">
                    <SidebarButton
                        label="Dashboard"
                        :href="route('web.dashboard')"
                        :active="route().current('web.dashboard')"
                    />

                    <SidebarButton
                        label="Budgets"
                        :href="route('web.budgets.index')"
                        icon="currency"
                    />

                    <SidebarButton
                        v-if="hasBudgets"
                        label="Transactions"
                        :href="route('web.dashboard')"
                        icon="list"
                    />

                    <SidebarButton
                        v-if="hasBudgets"
                        label="Recurring Transactions"
                        :href="route('web.dashboard')"
                        icon="refresh"
                    />

                    <SidebarButton
                        v-if="hasBudgets"
                        label="Categories"
                        :href="route('web.dashboard')"
                        icon="tag"
                    />

                    <SidebarButton
                        v-if="hasBudgets"
                        label="Payees"
                        :href="route('web.dashboard')"
                        icon="person"
                    />

                    <template v-if="userHasPermission(['view-all-users'])">
                        <hr class="border-white/20 my-4" />

                        <SidebarButton
                            label="User Management"
                            :href="route('web.dashboard')"
                            icon="users"
                        />
                    </template>

                    <hr class="border-white/20 my-4" />

                    <SidebarButton
                        label="Account Settings"
                        :href="route('auth.logout')"
                        icon="settings"
                    />

                    <SidebarButton
                        label="Logout"
                        :href="route('auth.logout')"
                        method="post"
                        icon="logout"
                    />
                </div>
            </div>
            <!-- Page Content -->
            <main
                class="flex-1 bg-slate-100 rounded-t-xl lg:rounded-tr-none lg:rounded-l-xl shadow-[-5px_0px_15px_-3px_rgba(0,0,0,0.3)] p-10"
            >
                <div class="pb-4 border-b border-slate-200 mb-12 px-4">
                    <div>
                        <slot name="header" />
                    </div>
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
