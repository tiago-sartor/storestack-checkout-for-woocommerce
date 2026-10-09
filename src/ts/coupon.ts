/**
 * coupon.ts
 * AJAX coupon handler — submits coupon code to WooCommerce, injects notices,
 * and fires the standard WC checkout update event so totals refresh.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.checkout');
    const email = document.querySelector('input[name="billing_email"]');
    if (!form || !email) return;

    const applyCoupon = (code: string) => {
        handleCoupon('apply_coupon', code);
    };

    // Coupon removal is currently being controlled by the native WooCommerce jQuery script.
    const removeCoupon = (code: string) => {
        handleCoupon('remove_coupon', code);
    };

    const handleCoupon = (action: string, code: string) => {
        // Note: 'wc_checkout_params' is injected natively by WooCommerce itself.
        const data = new FormData();
        data.append('coupon_code', code);
        data.append('billing_email', email.value);
        data.append('security', wc_checkout_params[action + '_nonce']);

        fetch(wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', action), {
            method: 'POST',
            body: data
        })
            .then(response => response.text())
            .then(data => {
                document.querySelectorAll('.woocommerce-error, .woocommerce-message, .is-error, .is-success, .checkout-inline-error-message').forEach(el => el.remove());
                if (data) {
                    form.insertAdjacentHTML('beforebegin', data);
                    // Dispatch custom events instead of jQuery triggers
                    document.body.dispatchEvent(new CustomEvent('applied_coupon_in_checkout', { detail: code }));
                    document.body.dispatchEvent(new CustomEvent('update_checkout', { detail: { update_shipping_method: false } }));
                }
            });
    }
}



/**
 * Inject a WooCommerce-style notice into the checkout notices container.
 * Mirrors what WooCommerce does natively when a coupon is applied.
 */
function injectNotice(type: 'error' | 'success', message: string): void {
        // Remove old coupon notices first.
        document
            .querySelectorAll('.woocommerce-coupon-notice')
            .forEach((el) => el.remove());

        const list = document.createElement('ul');
        list.className = `woocommerce-${type === 'success' ? 'message' : 'error'} woocommerce-coupon-notice`;
        list.setAttribute('role', 'alert');
        const item = document.createElement('li');
        item.textContent = message;
        list.appendChild(item);

        // Insert above the form or at the top of the checkout wrapper.
        const form = document.querySelector<HTMLFormElement>('form.woocommerce-checkout');
        if (form && form.parentElement) {
            form.parentElement.insertBefore(list, form);
        }
    }

/**
 * Trigger WooCommerce's checkout update cycle so totals and order review
 * sections are refreshed after a coupon is applied / removed.
 */
function triggerCheckoutUpdate(): void {
        const body = document.body as HTMLBodyElement & { dispatchEvent: typeof document.dispatchEvent };
        body.dispatchEvent(new Event('update_checkout'));

        // Also fire the jQuery-based event that WooCommerce classic checkout uses.
        if (typeof jQuery !== 'undefined') {
            (jQuery as any)(document.body).trigger('update_checkout');
        }
    }

/**
 * Apply a coupon via WooCommerce AJAX.
 */
async function applyCoupon(couponCode: string): Promise<void> {
        const code = couponCode.trim();
        if (!code) return;

        const button = document.getElementById('apply_coupon_btn') as HTMLButtonElement | null;
        if (button) button.disabled = true;

        try {
            const params = new URLSearchParams({
                action: 'apply_coupon',
                coupon_code: code,
                security: wc_checkout_params.apply_coupon_nonce ?? '',
            });

            const response = await fetch(wc_checkout_params.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body: params.toString(),
            });

            const html = await response.text();

            // WooCommerce returns HTML notices directly.
            const temp = document.createElement('div');
            temp.innerHTML = html;

            const noticeEl = temp.querySelector('.woocommerce-error, .woocommerce-message');
            if (noticeEl) {
                const type = noticeEl.classList.contains('woocommerce-error') ? 'error' : 'success';
                injectNotice(type, noticeEl.textContent?.trim() ?? '');
            }

            triggerCheckoutUpdate();

            // Clear the coupon input on success.
            if (!noticeEl?.classList.contains('woocommerce-error')) {
                const input = document.getElementById('coupon_code') as HTMLInputElement | null;
                if (input) input.value = '';
            }
        } catch {
            injectNotice('error', 'An error occurred. Please try again.');
        } finally {
            if (button) button.disabled = false;
        }
    }

/**
 * Initialise coupon AJAX interactions.
 * Wires up the Apply button click and Enter keydown on the coupon input.
 */
export function initCoupon(): void {
    const input = document.getElementById('coupon_code') as HTMLInputElement | null;
    const button = document.getElementById('apply_coupon_btn') as HTMLButtonElement | null;

    if (!input || !button) return;

    button.addEventListener('click', (e) => {
        e.preventDefault();
        void applyCoupon(input.value);
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            void applyCoupon(input.value);
        }
    });
}
