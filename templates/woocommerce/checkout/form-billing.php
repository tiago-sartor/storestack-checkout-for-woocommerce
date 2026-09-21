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
        <?php
        $email_key = 'billing_email';
        $email_field = $fields[$email_key] ?? [];
        $email_field['class'][] = 'checkout-form-field';
        $email_input_value = $checkout->get_value($email_key) ?? '';
        ?>

        <div class="contact-information">
            <div class="flex items-baseline justify-between mb-4">
                <h2 class="text-lg font-semibold text-neutral-900 tracking-tight">
                    <?php esc_html_e('Contact', 'woocommerce'); ?>
                </h2>
                <?php if (!is_user_logged_in() && ($checkout->is_registration_enabled() || 'yes' === get_option('woocommerce_enable_checkout_login_reminder'))) : ?>
                    <div class="flex gap-2 text-xs sm:text-sm">
                        <span class="text-neutral-600"><?php esc_html_e('Have an account?', 'woocommerce'); ?></span>
                        <button data-open-login-dialog type="button" class="cursor-pointer font-medium text-neutral-900 underline transition-colors hover:text-neutral-600">
                            <?php esc_html_e('Log in', 'woocommerce'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <?php woocommerce_form_field($email_key, $email_field, $email_input_value); ?>
                <p class="field-error mt-1.5 text-xs text-red-600 font-medium" data-error-for="<?php echo esc_attr($email_key); ?>" hidden aria-hidden="true"></p>
            </div>

        </div>

        <!-- Billing / Address Information -->
        <div class="billing-address-section">
            <h2 class="text-lg font-semibold text-neutral-900 tracking-tight mb-4">
                <?php esc_html_e('Billing address', 'woocommerce'); ?>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <?php
                // Unset email field as it is already rendered above.
                unset($fields['billing_email']);

                foreach ($fields as $key => $field) {
                    $field['placeholder'] = '';
                    $field['label_class'] = [];
                    $field['class'][] = 'checkout-form-field';
                ?>
                    <div>
                        <?php woocommerce_form_field($key, $field, $checkout->get_value($key) ?? ''); ?>
                        <p class="field-error mt-1.5 text-xs text-red-600 font-medium" data-error-for="<?php echo esc_attr($key); ?>" hidden aria-hidden="true"></p>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

    </div>

    <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>