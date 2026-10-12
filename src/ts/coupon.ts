/**
 * coupon.ts
 * AJAX coupon handler — submits coupon code to WooCommerce, injects notices,
 * and fires the standard WC checkout update event so totals refresh.
 */

export function initCoupon(): void {
    const form = document.querySelector<HTMLFormElement>('form.checkout');
    if (!form) return;

    form.addEventListener('click', (e) => {
        const target = e.target as HTMLButtonElement;
        if (target.id === 'apply_coupon') {
            e.preventDefault();
            const input = document.getElementById('coupon_code') as HTMLInputElement | null;
            if (input) {
                applyCoupon(input.value);
            }
        }
    });

    form.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const target = e.target as HTMLInputElement;
            if (target.id === 'coupon_code') {
                e.preventDefault();
                applyCoupon(target.value);
            }
        }
    });

    /**
     * Apply a coupon via WooCommerce AJAX.
     * @param code
     * @return void
     */
    const applyCoupon = (code: string): void => {
        handleCoupon('apply_coupon', code);
    };


    /**
     * Handle coupon application/removal via WooCommerce AJAX.
     * @param action
     * @param code
     * @return void
     */
    const handleCoupon = async (action: string, code: string): Promise<void> => {
        const email = document.getElementById('billing_email') as HTMLInputElement | null;
        const input = document.getElementById('coupon_code') as HTMLInputElement | null;
        const button = document.getElementById('apply_coupon') as HTMLButtonElement | null;

        const url = wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', action);
        const params = new URLSearchParams({
            coupon_code: code,
            billing_email: email?.value ?? '',
            security: wc_checkout_params[action + '_nonce'] ?? '',
        });

        try {
            if (button) button.disabled = true;

            const response = await fetch(url, {
                method: 'POST',
                body: params,
            });

            // WooCommerce returns HTML notices directly.
            const html = await response.text();

            document.querySelectorAll('.woocommerce-error, .woocommerce-message, .is-error, .is-success, .checkout-inline-error-message')
                .forEach(el => el.remove());

            if (html) {
                form.insertAdjacentHTML('afterbegin', html);
                document.body.dispatchEvent(new CustomEvent('applied_coupon_in_checkout', { detail: code }));
                document.body.dispatchEvent(new CustomEvent('update_checkout', { detail: { update_shipping_method: false } }));

                // Clear the input on success (usually success is a .woocommerce-message)
                if (html.includes('woocommerce-message')) {
                    if (input) input.value = '';
                }
            }
        } catch (error) {
            console.error('Coupon application failed:', error);
        } finally {
            if (button) button.disabled = false;
        }
    };
}
