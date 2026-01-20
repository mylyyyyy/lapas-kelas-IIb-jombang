import './bootstrap';

import Alpine from 'alpinejs';
import { initThreeHeroAnimation } from './three-hero-animation'; // Import the 3D animation function

window.Alpine = Alpine;

Alpine.start();

// Initialize the 3D animation when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initThreeHeroAnimation('hero-3d-canvas');
});

