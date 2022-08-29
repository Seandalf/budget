import { usePage } from "@inertiajs/inertia-vue3";

export function isEmpty(obj) {
    return obj === null || typeof obj === "undefined" || obj === "";
}

export function userHasPermission(permission) {
    const permissions = usePage().props.value.auth.permissions;

    if (typeof permission === "object") {
        return permissions.some((p) => permission.includes(p));
    }

    return permissions.includes(permission);
}
