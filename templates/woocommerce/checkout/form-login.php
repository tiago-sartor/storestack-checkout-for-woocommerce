<?php

/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
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

<div x-data="{open: false}" @keyup.escape.window="open = false">

	<div class="woocommerce-form-login-toggle text-sm font-medium mb-4">
		<span><?php esc_html_e('Returning customer?', 'woocommerce') ?></span>
		<button @click="open = true" class="text-gold-600 hover:text-gold-400 underline" type="button"><?php esc_html_e('Click here to login', 'woocommerce') ?></button>
	</div>

	<!-- Background Overlay -->
	<div
		x-cloak
		x-show="open"
		@click="open = false"
		x-transition.opacity.duration.500ms
		class="fixed inset-0 overflow-hidden bg-black/75 backdrop-blur-[2px] z-999"
		aria-hidden="true">
	</div>

	<!-- Login Form -->
	<div
		x-cloak
		x-show="open"
		x-transition.opacity.duration.500ms
		class="fixed top-1/2 -translate-y-1/2 inset-x-2 max-w-md mx-auto rounded-xl shadow-xl bg-white z-1000"
		:aria-hidden="open ? 'false' : 'true'">

		<form @click.outside="open = false" class="relative p-8 woocommerce-form woocommerce-form-login login" method="post">

			<div class="flex items-center justify-between mb-8">
				<h2 class="text-lg font-semibold">Login</h2>
				<button @click="open = false" type="button" class="flex items-center justify-center ml-3 text-neutral-400 hover:text-neutral-600">
					<span class="sr-only">Fechar</span>
					<svg class="size-6.5" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<?php do_action('woocommerce_login_form_start'); ?>

			<div
				x-data="{active: false}"
				class="relative flex items-center justify-start h-13 mt-6 mb-6 px-3 py-1 border border-neutral-500 rounded-sm bg-white">
				<label
					:class="active ? 'top-1 text-xs text-neutral-800' : 'text-sm text-neutral-500'"
					class="absolute pointer-events-none transition-all ease-in-out duration-200"
					for="username">
					E-mail
				</label>
				<input @focus="active = true" @blur="active = $el.value !== ''" :class="active ? 'pt-4.5' : ''" class="size-full focus:outline-none" type="text" name="username" id="username" autocomplete="username" required aria-required="true" />
			</div>
			<div
				x-data="{active: false}"
				class="relative flex items-center justify-start h-13 mb-3 px-3 py-1 border border-neutral-500 rounded-sm bg-white">
				<label
					:class="active ? 'top-1 text-xs text-neutral-800' : 'text-sm text-neutral-500'"
					class="absolute pointer-events-none transition-all ease-in-out duration-200"
					for="password">
					<?php esc_html_e('Password', 'woocommerce'); ?>
				</label>
				<input @focus="active = true" @blur="active = $el.value !== ''" :class="active ? 'pt-4.5' : ''" class="size-full focus:outline-none" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
			</div>

			<?php do_action('woocommerce_login_form'); ?>

			<div class="mb-8">
				<label class="flex items-center justify-start gap-2 cursor-pointer woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="cursor-pointer woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
					<span class="flex-1 text-sm text-left"><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
				</label>

				<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
				<input type="hidden" name="redirect" value="<?php echo esc_url(wc_get_checkout_url()); ?>" />

				<button class="woocommerce-form-login__submit flex items-center justify-center w-full mt-8 p-4 rounded-sm font-medium tracking-wide text-white bg-neutral-900 hover:bg-neutral-700 focus:outline-none" type="submit" name="login" value="<?php esc_attr_e('Login', 'woocommerce'); ?>"><?php esc_html_e('Login', 'woocommerce'); ?></button>
			</div>

			<div class="lost_password flex items-center justify-center">
				<a class="text-sm font-medium underline hover:text-gold-500" href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
			</div>

			<?php do_action('woocommerce_login_form_end'); ?>

		</form>
	</div>

</div>