<?php

/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
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

<div x-init="window.shippingFields = $data" x-data="fieldValidation(<?php echo esc_js(json_encode(array_keys($fields))); ?>)" class="relative woocommerce-shipping-fields">
	<?php if (true === WC()->cart->needs_shipping_address()) : ?>

		<div id="ship-to-different-address">
			<label class="flex items-center justify-start gap-3 my-4 cursor-pointer">
				<input id="ship-to-different-address-checkbox" class="size-5 rounded-sm" <?php checked(apply_filters('woocommerce_ship_to_different_address_checked', 'shipping' === get_option('woocommerce_ship_to_destination') ? 1 : 0), 1); ?> type="checkbox" name="ship_to_different_address" value="1" />
				<span class="text-sm"><?php esc_html_e('Ship to a different address?', 'woocommerce'); ?></span>
			</label>
		</div>

		<div class="relative mb-8 shipping_address">

			<?php wc_get_template('components/loading-spinner.php') ?>

			<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">

				<h2 class="mb-4 text-lg font-medium">Informações de entrega</h2>

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<?php
					// Note: The fields should have been already sorted by priority.
					foreach ($fields as $key => $field) {
						$label = $field['label'] ?? '';
						$input_type = $field['type'] ?? 'text';
						$input_value = $checkout->get_value($key) ?? '';
						$required = $field['required'] ?? false;
						$autocomplete = $field['autocomplete'] ?? false;
					?>
						<div id="<?php echo esc_attr($key) . '_field'; ?>">
							<div
								x-init="if ($refs.<?php echo esc_attr($key); ?>.value !== '') active = true" x-data="{ active: false }"
								class="relative flex items-center justify-start h-13 px-3 py-1 border border-neutral-500 rounded-sm bg-white">

								<label :class="active ? 'top-1 text-xs text-neutral-800' : 'text-sm text-neutral-500'" class="absolute pointer-events-none transition-all ease-in-out duration-200" for="<?php echo esc_attr($key); ?>">
									<?php echo esc_html($label); ?><?php if (!$required) echo ' (opcional)'; ?>
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
										x-ref="<?php echo esc_attr($key); ?>"
										x-model.fill="<?php echo esc_attr($key); ?>"
										class="appearance-none cursor-pointer absolute inset-0 px-3 pt-5.5 pb-1 focus:outline-none"
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
									<svg class="size-5 absolute top-1/2 -translate-y-1/2 right-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
									</svg>

								<?php else : ?>

									<input
										x-ref="<?php echo esc_attr($key); ?>"
										x-model.fill="<?php echo esc_attr($key); ?>"
										x-effect="<?php echo esc_attr($key); ?>; $dispatch('runcheck')"
										@focus="active = true"
										@blur="$dispatch('runcheck')"
										@runcheck="
											active = $el.value !== '';							
											$el.value = formatTextInput($el.value);
											<?php if ($required) : ?>
											validateField('<?php echo esc_js($key); ?>','<?php echo esc_js($label); ?>');
											<?php endif; ?>
										"
										type="<?php echo esc_attr($input_type); ?>"
										:class="active ? 'pt-4.5' : ''"
										class="size-full focus:outline-none"
										name="<?php echo esc_attr($key); ?>"
										id="<?php echo esc_attr($key); ?>"
										value="<?php echo esc_attr($input_value); ?>"
										<?php if ($required) echo 'aria-required="true"'; ?>
										<?php if ($autocomplete) echo 'autocomplete="' . esc_attr($autocomplete) . '"'; ?> />

								<?php endif; ?>

							</div>
							<p class="mt-1 text-xs text-red-700" :aria-hidden="errors.<?php echo esc_attr($key); ?> ? 'false' : 'true'" x-show="errors.<?php echo esc_attr($key); ?>" x-text="errors.<?php echo esc_attr($key); ?>"></p>
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