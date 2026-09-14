<?php

/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
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

<form id="order_review" method="post">

    <div class="relative my-10 grid grid-cols-1 lg:grid-cols-[3fr_2fr] lg:items-start lg:gap-[8%]">

        <div class="mb-8">
            <?php wc_get_template('order/order-details-customer.php', ['order' => $order]); ?>

            <?php
            /**
             * Triggered from within the checkout/form-pay.php template, immediately before the payment section.
             *
             * @since 8.2.0
             */
            do_action('woocommerce_pay_order_before_payment');
            ?>

            <div id="payment">
                <?php if ($order->needs_payment()) : ?>
                    <ul class="wc_payment_methods payment_methods methods space-y-2 my-8">
                        <?php
                        if (! empty($available_gateways)) {
                            foreach ($available_gateways as $gateway) {
                                wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                            }
                        } else {
                            echo '<li>';
                            wc_print_notice(apply_filters('woocommerce_no_available_payment_methods_message', esc_html__('Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')), 'notice'); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
                            echo '</li>';
                        }
                        ?>
                    </ul>
                <?php endif; ?>
                <div class="form-row">
                    <input type="hidden" name="woocommerce_pay" value="1" />

                    <?php wc_get_template('checkout/terms.php'); ?>

                    <?php do_action('woocommerce_pay_order_before_submit'); ?>

                    <?php echo apply_filters('woocommerce_pay_order_button_html', '<button type="submit" class="flex items-center justify-center w-full p-4 rounded-sm text-base font-semibold tracking-wide text-white bg-neutral-900 hover:bg-neutral-700 button alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); // @codingStandardsIgnoreLine 
                    ?>

                    <?php do_action('woocommerce_pay_order_after_submit'); ?>

                    <?php wp_nonce_field('woocommerce-pay', 'woocommerce-pay-nonce'); ?>
                </div>
            </div>
        </div>

        <div class="order-first lg:order-last mb-8">
            <?php wc_get_template('order/order-details.php', ['order' => $order]); ?>
        </div>
    </div>
</form>