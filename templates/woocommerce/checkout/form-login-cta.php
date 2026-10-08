<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

declare(strict_types=1);

defined('ABSPATH') || exit;


$registration_at_checkout   = WC_Checkout::instance()->is_registration_enabled();
$login_reminder_at_checkout = 'yes' === get_option('woocommerce_enable_checkout_login_reminder');

if (is_user_logged_in()) {
    return;
}

if (!$registration_at_checkout && !$login_reminder_at_checkout) {
    return;
}
?>

<!-- Login Call-to-Action — triggers the dialog via data-open-login-dialog -->
<div class="woocommerce-form-login-toggle flex gap-2 text-xs sm:text-sm">
    <span class="text-neutral-600"><?php esc_html_e('Have an account?', 'woocommerce'); ?></span>
    <button data-open-login-dialog type="button" class="cursor-pointer font-medium underline transition-colors hover:text-neutral-600">
        <?php esc_html_e('Log in', 'woocommerce'); ?>
    </button>
</div>