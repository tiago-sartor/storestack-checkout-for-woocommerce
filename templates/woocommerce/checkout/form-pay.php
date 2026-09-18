<?php

/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.2.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;

/* WooCommerce Notices */
woocommerce_output_all_notices();
?>

<form id="order_review" method="post" class="pb-16">

    <div class="relative my-8 grid grid-cols-1 lg:grid-cols-12 lg:items-start lg:gap-10 xl:gap-14">

        <!-- Payment & Customer (Left Column) -->
        <div class="lg:col-span-7 space-y-6">
            <?php wc_get_template('order/order-details-customer.php', ['order' => $order]); ?>

            <?php
            /**
             * Triggered from within the checkout/form-pay.php template, immediately before the payment section.
             *
             * @since 8.2.0
             */
            do_action('woocommerce_pay_order_before_payment');
            ?>

            <div id="payment" class="woocommerce-checkout-payment pt-2">
                <?php if ($order->needs_payment()) : ?>
                    <div class="mb-4">
                        <h2 class="text-lg font-semibold text-neutral-900 tracking-tight">
                            <?php esc_html_e('Payment', 'woocommerce'); ?>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            <?php echo esc_html__('All transactions are secure and encrypted.', 'woocommerce'); ?>
                        </p>
                    </div>

                    <ul class="wc_payment_methods payment_methods methods rounded-xl border border-neutral-300 divide-y divide-neutral-200 overflow-hidden bg-white shadow-2xs mb-6">
                        <?php
                        if (! empty($available_gateways)) {
                            foreach ($available_gateways as $gateway) {
                                wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                            }
                        } else {
                            echo '<li class="p-6 text-sm text-neutral-500 bg-neutral-50">';
                            wc_print_notice(apply_filters('woocommerce_no_available_payment_methods_message', esc_html__('Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')), 'notice');
                            echo '</li>';
                        }
                        ?>
                    </ul>
                <?php endif; ?>

                <div class="form-row">
                    <input type="hidden" name="woocommerce_pay" value="1" />

                    <?php wc_get_template('checkout/terms.php'); ?>

                    <?php do_action('woocommerce_pay_order_before_submit'); ?>

                    <div class="mt-8 pt-4 border-t border-neutral-200/60">
                        <?php echo apply_filters('woocommerce_pay_order_button_html', '<button type="submit" class="storestack-checkout-button-primary w-full sm:w-auto min-w-56 px-8 py-4 rounded-lg text-base font-semibold text-white bg-neutral-900 hover:bg-neutral-800 active:bg-black transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer button alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); ?>
                    </div>

                    <?php do_action('woocommerce_pay_order_after_submit'); ?>

                    <?php wp_nonce_field('woocommerce-pay', 'woocommerce-pay-nonce'); ?>
                </div>
            </div>
        </div>

        <!-- Order Summary (Right Column) -->
        <div class="order-first lg:order-last lg:col-span-5 mb-8 lg:mb-0">
            <div class="lg:sticky lg:top-8 bg-neutral-50 border border-neutral-200/90 rounded-2xl p-5 sm:p-7 shadow-xs">
                <h3 class="text-base font-semibold text-neutral-900 mb-6 pb-4 border-b border-neutral-200/70">
                    <?php esc_html_e('Order summary', 'woocommerce'); ?>
                </h3>
                <?php wc_get_template('order/order-details.php', ['order' => $order]); ?>
            </div>
        </div>

    </div>
</form>