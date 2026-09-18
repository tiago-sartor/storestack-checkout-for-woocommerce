/**
 * global.d.ts
 * Ambient type declarations for globals injected by WordPress/WooCommerce.
 */

// jQuery — loaded by WordPress core before our scripts run.
declare const jQuery: ((...args: unknown[]) => JQueryInstance) & {
    (selector: unknown): JQueryInstance;
};

interface JQueryInstance {
    on(event: string, handler: (...args: unknown[]) => void): JQueryInstance;
    trigger(event: string): JQueryInstance;
    [key: string]: unknown;
}

// wc_checkout_params — localised by WooCommerce classic checkout.
declare const wc_checkout_params: {
    ajax_url: string;
    apply_coupon_nonce?: string;
    [key: string]: string | undefined;
};

