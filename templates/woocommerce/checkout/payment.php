<?php

/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;

if (! wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>

<div id="payment" class="woocommerce-checkout-payment pt-2">
    <?php if (WC()->cart && WC()->cart->needs_payment()) : ?>

        <div class="mb-4">
            <h2 class="text-lg font-semibold text-neutral-900 tracking-tight">
                <?php esc_html_e('Payment', 'woocommerce'); ?>
            </h2>
            <p class="text-xs text-neutral-500 mt-0.5">
                <?php echo esc_html__('All transactions are secure and encrypted.', 'woocommerce'); ?>
            </p>
        </div>

        <!-- Unified Payment Methods Container -->
        <ul class="wc_payment_methods payment_methods methods rounded-xl border border-neutral-300 divide-y divide-neutral-200 overflow-hidden bg-white shadow-2xs">
            <?php
            if (!empty($available_gateways)) {
                foreach ($available_gateways as $gateway) {
                    wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                }
            } else {
                echo '<li class="flex items-center justify-center p-6 text-sm text-neutral-500 bg-neutral-50">';
                wc_print_notice(apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce')), 'notice');
                echo '</li>';
            }
            ?>
        </ul>
    <?php endif; ?>

    <div class="form-row place-order mt-6">
        <noscript>
            <p>
                <?php
                printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
                ?>
            </p>
            <button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
        </noscript>

        <?php wc_get_template('checkout/terms.php'); ?>

        <?php do_action('woocommerce_review_order_before_submit'); ?>

        <!-- Action Row (Return to Cart & Pay Now) -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 mt-8 pt-4 border-t border-neutral-200/60">
            <a
                class="w-full sm:w-auto text-center sm:text-left text-sm font-medium text-neutral-600 hover:text-neutral-900 flex items-center justify-center sm:justify-start gap-1.5 py-2.5 transition-colors"
                href="<?php echo esc_url(wc_get_cart_url()); ?>"
                role="button">
                <svg class="size-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                <span><?php echo esc_html__('Return to cart', 'woocommerce'); ?></span>
            </a>

            <button
                class="storestack-checkout-button-primary w-full sm:w-auto min-w-56 px-8 py-4 text-base font-semibold transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                type="submit"
                name="woocommerce_checkout_place_order"
                id="place_order"
                value="<?php echo esc_attr($order_button_text); ?>"
                data-value="<?php echo esc_attr($order_button_text); ?>">
                <span><?php echo esc_html($order_button_text); ?></span>
            </button>
        </div>

        <?php do_action('woocommerce_review_order_after_submit'); ?>

        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
    </div>

</div>

<?php
if (! wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
