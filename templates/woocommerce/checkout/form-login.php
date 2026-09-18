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

<!-- Optional top banner — triggers the dialog via data-open-login-dialog -->
<div class="woocommerce-form-login-toggle text-xs sm:text-sm text-neutral-600 mb-4 p-3.5 rounded-lg bg-neutral-50 border border-neutral-200/70 flex items-center justify-between">
	<span><?php esc_html_e('Returning customer?', 'woocommerce'); ?></span>
	<button data-open-login-dialog class="text-neutral-900 font-semibold underline hover:text-neutral-600 transition-colors cursor-pointer" type="button">
		<?php esc_html_e('Click here to login', 'woocommerce'); ?>
	</button>
</div>

<!-- Native HTML5 <dialog> — open/close managed by ui.ts -->
<dialog id="checkout-login-dialog" aria-labelledby="login-dialog-title" aria-modal="true">

	<form class="relative woocommerce-form woocommerce-form-login login" method="post">

		<div class="flex items-center justify-between mb-6 pb-3 border-b border-neutral-100">
			<h3 id="login-dialog-title" class="text-lg font-semibold text-neutral-900">
				<?php esc_html_e('Login', 'woocommerce'); ?>
			</h3>
			<button
				data-close-login-dialog
				type="button"
				class="p-1.5 -mr-1.5 text-neutral-400 hover:text-neutral-700 rounded-lg transition-colors cursor-pointer"
				aria-label="<?php esc_attr_e('Close', 'woocommerce'); ?>">
				<svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>

		<?php do_action('woocommerce_login_form_start'); ?>

		<div class="space-y-3.5 mb-5">
			<div class="form-floating-field">
				<label for="username">
					<?php esc_html_e('Username or email', 'woocommerce'); ?>
				</label>
				<input
					class="size-full text-sm text-neutral-900 bg-transparent focus:outline-none transition-all"
					type="text"
					name="username"
					id="username"
					placeholder=" "
					autocomplete="username"
					required
					aria-required="true" />
			</div>

			<div class="form-floating-field">
				<label for="password">
					<?php esc_html_e('Password', 'woocommerce'); ?>
				</label>
				<input
					class="size-full text-sm text-neutral-900 bg-transparent focus:outline-none transition-all"
					type="password"
					name="password"
					id="password"
					placeholder=" "
					autocomplete="current-password"
					required
					aria-required="true" />
			</div>
		</div>

		<?php do_action('woocommerce_login_form'); ?>

		<div class="flex items-center justify-between mb-6">
			<label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs text-neutral-700">
				<input class="size-4 rounded-md border-neutral-300 accent-neutral-900 cursor-pointer" name="rememberme" type="checkbox" id="rememberme" value="forever" />
				<span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
			</label>

			<a class="text-xs font-medium text-neutral-600 hover:text-neutral-900 underline" href="<?php echo esc_url(wp_lostpassword_url()); ?>">
				<?php esc_html_e('Lost your password?', 'woocommerce'); ?>
			</a>
		</div>

		<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url(wc_get_checkout_url()); ?>" />

		<button
			class="w-full py-3.5 rounded-lg text-sm font-semibold text-white bg-neutral-900 hover:bg-neutral-800 active:bg-black transition-colors shadow-sm flex items-center justify-center cursor-pointer"
			type="submit"
			name="login"
			value="<?php esc_attr_e('Login', 'woocommerce'); ?>">
			<?php esc_html_e('Login', 'woocommerce'); ?>
		</button>

		<?php do_action('woocommerce_login_form_end'); ?>

	</form>

</dialog>