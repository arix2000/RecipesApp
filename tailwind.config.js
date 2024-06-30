/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./assets/**/*.{vue,js,ts,jsx,tsx}",
        "./templates/**/*.{html,twig}"
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    light: '#A7F3D0', // light green
                    DEFAULT: '#34D399', // primary green
                    dark: '#059669', // dark green
                },
                secondary: {
                    light: '#D1FAE5', // light green
                    DEFAULT: '#10B981', // secondary green
                    dark: '#047857', // dark green
                },
                accent: {
                    light: '#6EE7B7', // accent green
                    DEFAULT: '#22C55E', // accent green
                    dark: '#065F46', // dark green
                },
            },
            keyframes: {
                shimmer: {
                    '0%': {backgroundPosition: '-1000px 0'},
                    '100%': {backgroundPosition: '1000px 0'},
                },
            },
            animation: {
                shimmer: 'shimmer 2s infinite linear',
            },
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
    ],
}

