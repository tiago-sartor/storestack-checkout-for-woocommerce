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

<!-- HTML5 <dialog> — open/close managed by ui.ts -->
<dialog id="checkout-login-dialog" class="transform transition-all transition-discrete duration-300 ease-in-out" aria-labelledby="login-dialog-title" aria-modal="true">

    <form class="fixed inset-x-4 top-1/2 mx-auto max-w-md -translate-y-1/2 rounded-2xl bg-white p-6 shadow-2xl woocommerce-form woocommerce-form-login login" method="post">

        <div class="flex items-center justify-between mb-6 pb-3 border-b border-neutral-100">
            <h3 id="login-dialog-title" class="text-lg font-semibold">
                <?php esc_html_e('Login', 'woocommerce'); ?>
            </h3>
            <button
                data-close-login-dialog
                type="button"
                class="p-1.5 -mr-1.5 text-neutral-400 hover:text-neutral-700 rounded-lg transition-colors cursor-pointer"
                aria-label="<?php esc_attr_e('Close', 'woocommerce'); ?>">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <?php do_action('woocommerce_login_form_start'); ?>

        <div class="checkout-fields-flow mb-8 flex flex-wrap gap-3">
            <p class="form-row form-row-wide checkout-form-field">
                <label for="username">
                    <?php esc_html_e('Email', 'woocommerce'); ?>
                </label>
                <input
                    class="size-full text-sm bg-transparent focus:outline-none transition-all"
                    type="text"
                    name="username"
                    id="username"
                    placeholder=""
                    autocomplete="username"
                    required
                    aria-required="true" />
            </p>

            <p class="form-row form-row-wide checkout-form-field">
                <label for="password">
                    <?php esc_html_e('Password', 'woocommerce'); ?>
                </label>
                <input
                    class="size-full text-sm bg-transparent focus:outline-none transition-all"
                    type="password"
                    name="password"
                    id="password"
                    placeholder=""
                    autocomplete="current-password"
                    required
                    aria-required="true" />
            </p>
        </div>

        <?php do_action('woocommerce_login_form'); ?>

        <div class="flex items-center justify-between mb-4">
            <label class="inline-flex cursor-pointer items-center gap-2 text-xs text-neutral-700 select-none">
                <input class="size-3.5 cursor-pointer rounded-md border-neutral-300 accent-neutral-900" name="rememberme" type="checkbox" id="rememberme" value="forever" />
                <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
            </label>

            <a class="text-xs font-medium text-neutral-600 underline hover:text-neutral-900" href="<?php echo esc_url(wp_lostpassword_url()); ?>">
                <?php esc_html_e('Lost your password?', 'woocommerce'); ?>
            </a>
        </div>

        <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
        <input type="hidden" name="redirect" value="<?php echo esc_url(wc_get_checkout_url()); ?>" />

        <button
            class="flex w-full cursor-pointer items-center justify-center rounded-lg bg-neutral-900 p-3 font-medium tracking-wide text-white shadow-sm transition-colors hover:bg-neutral-800 active:bg-black"
            type="submit"
            name="login"
            value="<?php esc_attr_e('Login', 'woocommerce'); ?>">
            <?php esc_html_e('Login', 'woocommerce'); ?>
        </button>

        <?php do_action('woocommerce_login_form_end'); ?>

    </form>

</dialog>