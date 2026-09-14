<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;
?>

<div class="mt-10 mb-8">
	<h1 class="text-4xl tracking-wide" aria-label="<?php the_title(); ?>"><?php the_title(); ?></h1>
</div>

<?php
/* WooCommerce Notices */
woocommerce_output_all_notices();
/* Customer Login */
wc_get_template('checkout/form-login.php', array('checkout' => WC()->checkout()));

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
	return;
}
?>

<form x-data="couponHandler()" x-on:keydown.enter.prevent class="checkout woocommerce-checkout" name="checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" method="post" enctype="multipart/form-data" aria-label="<?php echo esc_attr__('Checkout', 'woocommerce'); ?>">

	<div class="relative grid grid-cols-1 lg:grid-cols-[3fr_2fr] lg:items-start lg:gap-[8%]">

		<div class="mb-8">
			<?php if ($checkout->get_checkout_fields()) :
				/**
				 * Hooked: wc_get_pay_buttons()
				 */
				do_action('woocommerce_checkout_before_customer_details'); ?>

				<div id="customer_details">
					<div id="billing_details">
						<?php do_action('woocommerce_checkout_billing'); ?>
					</div>

					<div id="shipping_details">
						<?php do_action('woocommerce_checkout_shipping'); ?>
					</div>
				</div>

			<?php endif; ?>

			<div class="woocommerce-additional-fields">
				<?php wc_get_template('checkout/additional-fields.php', array('checkout' => WC()->checkout())); ?>
			</div>

			<div>
				<?php woocommerce_checkout_payment() ?>
			</div>
		</div>


		<div
			x-data="{ open: false, isMobile: window.innerWidth < 1024, total: '' }"
			x-init="if (!isMobile) open = true"
			class="order-first lg:order-last mb-8">
			<div x-on:click="open = !open" class="relative flex items-center justify-between mb-4 p-4 border border-neutral-300 rounded-md bg-neutral-50 lg:p-0 lg:border-0 lg:bg-transparent">
				<div class="flex items-center justify-start gap-2">
					<h3 class="text-base lg:text-lg font-medium" id="order_review_heading">Resumo do pedido</h3>
					<span x-bind:class="open ? 'rotate-180' : ''" class="transition-transform duration-200 lg:hidden!">
						<svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
						</svg>
					</span>
				</div>
				<span class="flex-1 text-right font-semibold whitespace-nowrap lg:hidden!" x-html="total"></span>
			</div>

			<div x-collapse.duration.300ms x-show="isMobile ? open : true" id="order_review" class="woocommerce-checkout-review-order">
				<?php wc_get_template('checkout/review-order.php'); ?>
			</div>
		</div>

	</div>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>