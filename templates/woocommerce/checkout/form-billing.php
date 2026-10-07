<?php

/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

declare(strict_types=1);
defined('ABSPATH') || exit;

$fields = $checkout->get_checkout_fields('billing');
// Sort fields by priority
uasort($fields, function ($a, $b) {
    return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
});
?>

<div class="relative woocommerce-billing-fields">

    <?php if (file_exists(SSCFW_PLUGIN_PATH . 'templates/woocommerce/components/loading-spinner.php')) {
        wc_get_template('components/loading-spinner.php');
    } ?>

    <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

    <div class="woocommerce-billing-fields__field-wrapper space-y-8">

        <!-- Contact Information -->
        <div class="contact-info-section">
            <div class="flex items-baseline justify-between mb-4">
                <h2 class="text-lg font-semibold tracking-tight">
                    <?php esc_html_e('Contact Information', 'woocommerce'); ?>
                </h2>

                <?php wc_get_template('checkout/form-login-cta.php'); ?>
            </div>

            <div class="checkout-fields-flow flex flex-wrap gap-3">
                <?php
                $contact_field_keys = ['billing_email', 'billing_first_name', 'billing_last_name'];

                foreach ($contact_field_keys as $key) {
                    if (! isset($fields[$key])) {
                        continue;
                    }

                    $field = $fields[$key];
                    $field['placeholder'] = '';
                    $field['label_class'] = [];
                    $field['class'][] = 'checkout-form-field';

                    woocommerce_form_field($key, $field, $checkout->get_value($key) ?? '');

                    // Unset contact fields so they are not rendered again in the billing section below.
                    unset($fields[$key]);
                }
                ?>
            </div>

        </div>

        <!-- Billing / Address Information -->
        <div class="billing-address-section">
            <h2 class="text-lg font-semibold tracking-tight mb-4">
                <?php esc_html_e('Billing address', 'woocommerce'); ?>
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

    </div>

    <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>