/**
 * index.ts
 * Main entry point for assets/js/frontend.js
 * Initialises all core checkout UI modules on DOMContentLoaded.
 */

import { initCoupon } from './coupon';
import { initLoginDialog, preventEnterSubmit, initMobileTotalSync } from './ui';

document.addEventListener('DOMContentLoaded', () => {
    initLoginDialog();
    preventEnterSubmit();
    initMobileTotalSync();
    initCoupon();
});

