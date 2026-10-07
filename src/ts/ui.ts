/**
 * ui.ts
 * UI coordinator:
 *  - Opens / closes the native HTML5 <dialog id="checkout-login-dialog">
 *  - Prevents accidental form submissions on Enter (coupon input excluded)
 *  - Syncs the real-time order total shown in the mobile summary bar
 *    whenever WooCommerce fires the `updated_checkout` event.
 */

/**
 * Initialise the native <dialog> login modal.
 * Handles open triggers, close button, backdrop click, and ESC key
 * (ESC is natively handled by <dialog>, so we don't need to re-implement it).
 */
export function initLoginDialog(): void {
    const dialog = document.getElementById('checkout-login-dialog') as HTMLDialogElement | null;
    if (!dialog) return;

    // Open triggers: any element with [data-open-login-dialog].
    document.querySelectorAll<HTMLElement>('[data-open-login-dialog]').forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            dialog.showModal();
        });
    });

    // Close button inside the dialog.
    dialog.querySelectorAll<HTMLElement>('[data-close-login-dialog]').forEach((btn) => {
        btn.addEventListener('click', () => dialog.close());
    });

    // Close on backdrop click (click outside the dialog box).
    dialog.addEventListener('click', (e) => {
        // The dialog element itself fills the viewport; the inner box is a child.
        // If the click target is the <dialog> element (i.e., the backdrop area), close it.
        if (e.target === dialog) {
            dialog.close();
        }
    });
}

/**
 * Prevent unintentional form submission when the user presses Enter
 * anywhere in the checkout form, except on the coupon input (which is
 * handled by coupon.ts) and submit buttons.
 */
export function preventEnterSubmit(): void {
    const form = document.querySelector<HTMLFormElement>('form.woocommerce-checkout');
    if (!form) return;

    form.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;

        const target = e.target as HTMLElement;
        const tag = target.tagName.toLowerCase();
        const type = (target as HTMLInputElement).type?.toLowerCase();

        // Allow Enter on submit buttons and textareas (multi-line).
        if (tag === 'textarea' || type === 'submit' || type === 'button') return;

        // Allow Enter on the coupon code input — handled by coupon.ts.
        if ((target as HTMLInputElement).id === 'coupon_code') return;

        e.preventDefault();
    });
}

/**
 * Sync the mobile order summary total badge whenever WooCommerce updates
 * the checkout (e.g., after a coupon is applied or shipping is selected).
 *
 * WooCommerce fires the jQuery-based `updated_checkout` event on document.body
 * after every AJAX refresh of the order review table.
 */
export function initMobileTotalSync(): void {
    const mobileTotalEl = document.getElementById('checkout-mobile-total');
    if (!mobileTotalEl) return;

    const syncTotal = (): void => {
        // The order total is rendered inside .order-total .woocommerce-Price-amount
        const totalEl = document.querySelector<HTMLElement>(
            '.order-total .woocommerce-Price-amount'
        );
        if (totalEl) {
            mobileTotalEl.innerHTML = totalEl.outerHTML;
        }
    };

    // Listen via jQuery (WooCommerce classic checkout uses jQuery events).
    if (typeof jQuery !== 'undefined') {
        (jQuery as any)(document.body).on('updated_checkout', syncTotal);
    }

    // Also run once on load to populate the badge immediately.
    syncTotal();
}
