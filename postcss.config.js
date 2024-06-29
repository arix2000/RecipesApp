let tailwindcss = require("tailwindcss")

/** @type {import('tailwindcss').Config} */
module.exports = {
  plugins: [
    tailwindcss('./tailwind.config.js'),
    require('postcss-import'),
    require('autoprefixer')
  ],
}