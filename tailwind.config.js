/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./assets/**/*.{vue,js,ts,jsx,tsx}",
        "./templates/**/*.{html,twig}"
    ],
    theme: {
        extend: {
            // Add custom utilities
            lineClamp: {
                2: '2',
            }
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
    ],
}

