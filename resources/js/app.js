import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import Toast from "vue-toastification";
import VCalendar from "v-calendar";
import Cleave from "vue-cleave-component";
import SmartTable from "vuejs-smart-table";
import "../css/toastification.css";
import "v-calendar/dist/style.css";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Toast, {
                position: "bottom-right",
                icon: false,
                timeout: 6000,
            })
            .use(VCalendar, {})
            .use(Cleave)
            .use(SmartTable)
            .mount(el);
    },
});

InertiaProgress.init({ color: "#5778A3" });
