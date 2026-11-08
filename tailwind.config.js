import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', '"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Syne', '"Plus Jakarta Sans"', 'sans-serif'],
                serif: ['"Playfair Display"', 'Georgia', 'serif'],
            },
            letterSpacing: {
                tighter: '-0.05em',
                tightest: '-0.08em',
                widest: '0.25em',
            },
            colors: {
                brand: {
                    pink: {
                        50: '#fdf2f4',
                        100: '#fbe6eb',
                        200: '#f7d0d9',
                        300: '#f0aab9',
                        400: '#e57a94',
                        500: '#e06383', // Primary boutique rose pink
                        600: '#ca4367',
                        700: '#ab3252',
                        800: '#8e2d46',
                        900: '#772a3e',
                        DEFAULT: '#e06383',
                        light: '#fdf2f4',
                        dark: '#ab3252',
                    },
                    charcoal: {
                        50: '#f6f6f7',
                        100: '#e1e2e4',
                        200: '#c5c7cb',
                        300: '#a2a5ad',
                        400: '#7e838e',
                        500: '#646975',
                        600: '#4f535e',
                        700: '#3f434c',
                        800: '#27292f',
                        900: '#18191d',
                        DEFAULT: '#18191d',
                    },
                    cream: {
                        50: '#ffffff',
                        100: '#fdfcfb',
                        200: '#faf7f2',
                        300: '#f4ede4',
                        DEFAULT: '#faf7f2',
                    },
                    gold: {
                        DEFAULT: '#d4af37',
                        light: '#f5e7a9',
                    },
                },
            },
        },
    },

    plugins: [forms, daisyui],

    daisyui: {
        themes: ["light"],
        base: false,
        styled: true,
        utils: true,
        prefix: "",
        logs: false,
    },
};
