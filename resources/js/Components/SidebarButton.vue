<script setup>
import { Link } from "@inertiajs/inertia-vue3";

import HomeIcon from "@/Components/SidebarIcons/Home.vue";
import CurrencyIcon from "@/Components/SidebarIcons/Currency.vue";
import TagIcon from "@/Components/SidebarIcons/Tag.vue";
import PersonIcon from "@/Components/SidebarIcons/Person.vue";
import ListIcon from "@/Components/SidebarIcons/List.vue";
import LogoutIcon from "@/Components/SidebarIcons/Logout.vue";
import RefreshIcon from "@/Components/SidebarIcons/Refresh.vue";
import SettingsIcon from "@/Components/SidebarIcons/Settings.vue";
import UsersIcon from "@/Components/SidebarIcons/Users.vue";

defineProps({
    icon: {
        type: String,
        default: "home",
    },
    label: {
        type: String,
        default: null,
    },
    href: {
        type: String,
        default: null,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="text-white text-sm hover:bg-white/10 block w-full px-4 py-3 rounded-lg tracking-wide transition ease-in-out flex items-center"
        :class="{ 'bg-white/10 pointer-events-none': active }"
    >
        <div class="flex-0 w-8">
            <HomeIcon v-if="icon === 'home'" />
            <CurrencyIcon v-if="icon === 'currency'" />
            <TagIcon v-if="icon === 'tag'" />
            <PersonIcon v-if="icon === 'person'" />
            <ListIcon v-if="icon === 'list'" />
            <LogoutIcon v-if="icon === 'logout'" />
            <RefreshIcon v-if="icon === 'refresh'" />
            <SettingsIcon v-if="icon === 'settings'" />
            <UsersIcon v-if="icon === 'users'" />
        </div>
        <div class="flex-1">
            {{ label }}
        </div>
    </Link>
</template>
