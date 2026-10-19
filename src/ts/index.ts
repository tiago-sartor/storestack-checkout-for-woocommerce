/**
 * index.ts
 * Main entry point for assets/js/frontend.js
 * Initializes all core checkout UI modules on DOMContentLoaded.
 */

import { initCoupon } from './coupon';
import { initLoginDialog, preventEnterSubmit, initMobileTotalSync } from './ui';
import { initCheckoutValidation } from './validation';

document.addEventListener('DOMContentLoaded', () => {
    initCheckoutValidation();
    initLoginDialog();
    preventEnterSubmit();
    initMobileTotalSync();
    initCoupon();
});
