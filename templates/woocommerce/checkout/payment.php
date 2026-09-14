<?php

/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
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

<div id="payment" class="woocommerce-checkout-payment">
    <?php if (WC()->cart && WC()->cart->needs_payment()) : ?>

        <h2 class="mb-4 text-lg font-medium">Pagamento</h2>

        <ul class="wc_payment_methods payment_methods methods space-y-2">
            <?php
            if (!empty($available_gateways)) {
                foreach ($available_gateways as $gateway) {
                    wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                }
            } else {
                echo '<li class="flex items-center justify-center rounded-md border border-neutral-300 bg-neutral-100 p-4">';
                wc_print_notice(apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce')), 'notice'); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
                echo '</li>';
            }
            ?>
        </ul>
    <?php endif; ?>

    <div class="form-row place-order">
        <noscript>
            <p>
                <?php
                /* translators: $1 and $2 opening and closing emphasis tags respectively */
                printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
                ?>
            </p><button type="submit" class="button alt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
        </noscript>

        <?php wc_get_template('checkout/terms.php'); ?>

        <?php do_action('woocommerce_review_order_before_submit'); ?>

        <div class="flex flex-col-reverse items-start justify-between gap-8 sm:flex-row sm:items-center mt-8">
            <a
                class="text-sm hover:underline hover:underline-offset-6"
                href="<?php echo esc_url(wc_get_cart_url()); ?>"
                role="button">
                <span aria-hidden="true">← </span>
                Retornar ao carrinho
            </a>
            <button
                class="flex items-center justify-center w-full sm:w-1/2 p-4 rounded-sm text-base font-semibold tracking-wide text-white bg-neutral-900 hover:bg-neutral-700"
                type="submit" name="woocommerce_checkout_place_order" id="place_order" value="<?php echo esc_attr($order_button_text) ?>" data-value="<?php echo esc_attr($order_button_text) ?>">
                <?php echo esc_html($order_button_text) ?>
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
