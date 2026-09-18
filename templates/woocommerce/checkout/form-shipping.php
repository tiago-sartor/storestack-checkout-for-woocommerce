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

		<div id="ship-to-different-address" class="pt-2">
			<label class="inline-flex items-center gap-3 cursor-pointer select-none py-2 text-sm text-neutral-800 hover:text-neutral-950 transition-colors">
				<input
					id="ship-to-different-address-checkbox"
					class="size-4.5 rounded-md border-neutral-300 text-neutral-900 focus:ring-neutral-900 cursor-pointer accent-neutral-900"
					<?php checked(apply_filters('woocommerce_ship_to_different_address_checked', 'shipping' === get_option('woocommerce_ship_to_destination') ? 1 : 0), 1); ?>
					type="checkbox"
					name="ship_to_different_address"
					value="1" />
				<span class="font-medium"><?php esc_html_e('Ship to a different address?', 'woocommerce'); ?></span>
			</label>
		</div>

		<div class="relative mt-6 mb-8 shipping_address">

			<?php if (file_exists(SSCFW_PLUGIN_PATH . 'templates/woocommerce/components/loading-spinner.php')) {
				wc_get_template('components/loading-spinner.php');
			} ?>

			<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">

				<h2 class="text-lg font-semibold text-neutral-900 tracking-tight mb-4">
					<?php esc_html_e('Shipping address', 'woocommerce'); ?>
				</h2>

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
					<?php
					foreach ($fields as $key => $field) {
						$label = $field['label'] ?? '';
						$input_type = $field['type'] ?? 'text';
						$input_value = $checkout->get_value($key) ?? '';
						$required = $field['required'] ?? false;
						$autocomplete = $field['autocomplete'] ?? false;

						$is_full_width = in_array($key, ['shipping_address_1', 'shipping_address_2', 'shipping_company'], true);
					?>
						<div id="<?php echo esc_attr($key) . '_field'; ?>" class="<?php echo $is_full_width ? 'sm:col-span-2' : ''; ?>">
							<div class="form-floating-field">

								<label for="<?php echo esc_attr($key); ?>">
									<?php echo esc_html($label); ?><?php if (!$required) echo ' (' . esc_html__('optional', 'woocommerce') . ')'; ?>
								</label>

								<?php if ($input_type === 'country' || $input_type === 'state' || $input_type === 'select') :
									if ($input_type === 'country') $countries = WC()->countries->get_shipping_countries();
									if ($input_type === 'state') {
										$for_country = $field['country'] ?? WC()->checkout->get_value('shipping_country');
										$states = WC()->countries->get_states($for_country);
									}
									$field['options'] = $input_type === 'country' ? $countries : ($input_type === 'state' ? $states : ($field['options'] ?? []));
								?>
									<select
										class="appearance-none cursor-pointer absolute inset-0 size-full px-3.5 pt-4 text-sm text-neutral-900 bg-transparent focus:outline-none"
										name="<?php echo esc_attr($key); ?>"
										id="<?php echo esc_attr($key); ?>"
										<?php if ($required) echo 'aria-required="true"'; ?>
										<?php if ($autocomplete) echo 'autocomplete="' . esc_attr($autocomplete) . '"'; ?>>
										<?php foreach ($field['options'] as $option_key => $option_value) : ?>
											<option value="<?php echo esc_attr($option_key); ?>" <?php selected($input_value, $option_key); ?>>
												<?php echo esc_html($option_value); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<svg class="size-4 absolute top-1/2 -translate-y-1/2 right-3.5 text-neutral-500 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
									</svg>

								<?php else : ?>

									<input
										type="<?php echo esc_attr($input_type); ?>"
										class="size-full text-sm text-neutral-900 bg-transparent focus:outline-none transition-all"
										name="<?php echo esc_attr($key); ?>"
										id="<?php echo esc_attr($key); ?>"
										value="<?php echo esc_attr($input_value); ?>"
										placeholder=" "
										<?php if ($required) echo 'aria-required="true"'; ?>
										<?php if ($autocomplete) echo 'autocomplete="' . esc_attr($autocomplete) . '"'; ?> />

								<?php endif; ?>

							</div>
							<p class="field-error mt-1.5 text-xs text-red-600 font-medium" data-error-for="<?php echo esc_attr($key); ?>" hidden aria-hidden="true"></p>
						</div>
					<?php
					}
					?>
				</div>
			</div>

			<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

		</div>

	<?php endif; ?>

</div>