const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            screens: {
                "3xl": "1750px",
            },
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                title: ["Sora", ...defaultTheme.fontFamily.sans],
                money: ["DM Mono", ...defaultTheme.fontFamily.mono],
                logo: ["Azedo", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // primary: {
                //     DEFAULT: "#5778A3",
                //     50: "#CDD7E4",
                //     100: "#C0CDDD",
                //     200: "#A5B7CF",
                //     300: "#8BA2C1",
                //     400: "#708DB3",
                //     500: "#5778A3",
                //     600: "#435D7E",
                //     700: "#30425A",
                //     800: "#1C2735",
                //     900: "#090C11",
                // },
                // primary: {
                //     DEFAULT: "#1E3A8A",
                //     50: "#7D97E3",
                //     100: "#6C8ADF",
                //     200: "#4B6FD8",
                //     300: "#2D56CD",
                //     400: "#2548AC",
                //     500: "#1E3A8A",
                //     600: "#14275C",
                //     700: "#0A132E",
                //     800: "#000000",
                //     900: "#000000",
                // },
                primary: {
                    DEFAULT: "#1E3A8A",
                    50: "#EEF5FB",
                    100: "#D1E3F5",
                    200: "#96BBE8",
                    300: "#5B8CDB",
                    400: "#2B5CC5",
                    500: "#1E3A8A",
                    600: "#192A71",
                    700: "#131C58",
                    800: "#0E113F",
                    900: "#080925",
                },
                secondary: {
                    DEFAULT: "#3B999B",
                    50: "#AEDFE0",
                    100: "#9FD9DA",
                    200: "#81CECF",
                    300: "#64C2C4",
                    400: "#46B6B9",
                    500: "#3B999B",
                    600: "#2C7172",
                    700: "#1C494A",
                    800: "#0D2121",
                    900: "#000000",
                },
                tertiary: {
                    DEFAULT: "#E92548",
                    50: "#FACCD4",
                    100: "#F8B9C4",
                    200: "#F494A5",
                    300: "#F06F86",
                    400: "#ED4A67",
                    500: "#E92548",
                    600: "#C21433",
                    700: "#8F0E25",
                    800: "#5C0918",
                    900: "#29040B",
                },
                "paper-white": {
                    DEFAULT: "#e3e4e6",
                },
                "card-gray": {
                    DEFAULT: "#F8F9FD",
                },
            },
        },
    },

    plugins: [require("@tailwindcss/forms")],
};
