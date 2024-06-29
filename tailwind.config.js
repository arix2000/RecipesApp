/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./assets/**/*.{vue,js,ts,jsx,tsx}",
        "./templates/**/*.{html,twig}"
    ],
    theme: {
        extend: {
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

