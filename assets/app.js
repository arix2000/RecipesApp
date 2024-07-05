/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import './javascript/pagination'

document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    console.log(window.location.pathname)
    if (!path.includes('/search') && !path.includes('/recipe/') && !path.includes('recipe/edit')) {
        localStorage.clear();
    }
});
