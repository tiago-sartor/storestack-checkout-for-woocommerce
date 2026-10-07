<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

declare(strict_types=1);
defined('ABSPATH') || exit;

$fields = $checkout->get_checkout_fields('shipping');
// Sort fields by priority
uasort($fields, function ($a, $b) {
    return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
});
?>

<div class="relative woocommerce-shipping-fields">
    <?php if (true === WC()->cart->needs_shipping_address()) : ?>

        <div id="ship-to-different-address">
            <label class="inline-flex items-center gap-3 cursor-pointer select-none my-4 text-sm hover:underline transition-colors">
                <input
                    id="ship-to-different-address-checkbox"
                    class="size-4.5 rounded-md border-neutral-300 focus:ring-neutral-900 cursor-pointer accent-neutral-900"
                    <?php checked(apply_filters('woocommerce_ship_to_different_address_checked', 'shipping' === get_option('woocommerce_ship_to_destination') ? 1 : 0), 1); ?>
                    type="checkbox"
                    name="ship_to_different_address"
                    value="1" />
                <span class="font-medium"><?php esc_html_e('Ship to a different address?', 'woocommerce'); ?></span>
            </label>
        </div>

        <div class="relative my-4 shipping_address">

            <?php if (file_exists(SSCFW_PLUGIN_PATH . 'templates/woocommerce/components/loading-spinner.php')) {
                wc_get_template('components/loading-spinner.php');
            } ?>

            <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

            <div class="woocommerce-shipping-fields__field-wrapper">

                <h2 class="text-lg font-semibold tracking-tight mb-4">
                    <?php esc_html_e('Shipping address', 'woocommerce'); ?>
                </h2>

                <?php $layout_class = class_exists('Extra_Checkout_Fields_For_Brazil') ? 'layout-brazilian' : 'layout-international'; ?>
                <div class="checkout-fields-flow flex flex-wrap gap-3 <?php echo esc_attr($layout_class); ?>">
                    <?php
                    foreach ($fields as $key => $field) {
                        $field['placeholder'] = '';
                        $field['label_class'] = [];
                        $field['class'][] = 'checkout-form-field';

                        woocommerce_form_field($key, $field, $checkout->get_value($key) ?? '');
                    }
                    ?>
                </div>
            </div>

            <?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

        </div>

    <?php endif; ?>

</div>