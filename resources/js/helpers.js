import { usePage } from "@inertiajs/inertia-vue3";

export function isEmpty(obj) {
    return (
        obj === "undefined" ||
        obj === null ||
        typeof obj === "undefined" ||
        obj === ""
    );
}

export function userHasPermission(permission) {
    const permissions = usePage().props.value.auth.permissions;

    if (typeof permission === "object") {
        return permissions.some((p) => permission.includes(p));
    }

    return permissions.includes(permission);
}

export function capitalise(string) {
    return string[0].toUpperCase() + string.substring(1);
}
