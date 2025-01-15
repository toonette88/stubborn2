import 'bootstrap/dist/css/bootstrap.min.css'
import './bootstrap.js';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import { Tooltip, Popover } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // Activer les tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));

    // Activer les popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].forEach(popoverTriggerEl => new Popover(popoverTriggerEl));
});


