import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            // 1. Configurando as fontes da DWL
            fontFamily: {
                sans: ['"Open Sans"', ...defaultTheme.fontFamily.sans], // Fonte base do sistema
                serif: [
                    '"Libre Baskerville"',
                    ...defaultTheme.fontFamily.serif,
                ], // Fonte para os títulos
            },
            // 2. Configurando a paleta de cores da DWL
            colors: {
                dwl: {
                    darkblue: "#225378",
                    cyan: "#ACF0F2",
                    teal: "#179680", // Vamos usar essa como a cor primária dos botões
                    slate: "#70808F",
                    lightgreen: "#E7FCE7",
                },
            },
        },
    },

    plugins: [forms],
};
