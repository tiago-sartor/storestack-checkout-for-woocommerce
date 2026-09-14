<?php

/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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

$fields = $checkout->get_checkout_fields('billing');
// Sort fields by priority
uasort($fields, function ($a, $b) {
	return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
});
?>

<div x-init="window.billingFields = $data" x-data="fieldValidation(<?php echo esc_js(json_encode(array_keys($fields))); ?>)" class="relative woocommerce-billing-fields">

	<?php wc_get_template('components/loading-spinner.php') ?>

	<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$email_key = 'billing_email';
		$email_field = $fields[$email_key] ?? [];

		$email_label = $email_field['label'] ?? '';
		$email_input_type = $email_field['type'] ?? 'email';
		$email_input_value = $checkout->get_value($email_key) ?? '';
		$email_required = $email_field['required'] ?? false;
		$email_autocomplete = $email_field['autocomplete'] ?? false;
		?>
		<div class="mb-8">
			<h2 class="mb-4 text-lg font-medium">Informações de contato</h2>
			<div
				x-init="if ($refs.<?php echo esc_attr($email_key); ?>.value !== '') active = true" x-data="{ active: false }"
				class="relative flex items-center justify-start h-13 mt-3 px-3 py-1 border border-neutral-500 rounded-sm bg-white" id="<?php echo esc_attr($email_key) . '_field'; ?>">
				<label x-bind:class="active ? 'top-1 text-xs text-neutral-800' : 'text-sm text-neutral-500'" class="absolute pointer-events-none transition-all ease-in-out duration-200" for="<?php echo esc_attr($email_key); ?>">
					<?php echo esc_html($email_label); ?>
				</label>
				<input
					x-model.fill="<?php echo esc_attr($email_key); ?>"
					x-ref="<?php echo esc_attr($email_key); ?>"
					@focus="active = true"
					@blur="if ($el.value === '') active = false; validateField('<?php echo esc_js($email_key); ?>','<?php echo esc_js($email_label); ?>')"
					type="<?php echo esc_attr($email_input_type); ?>"
					x-bind:class="active ? 'pt-4.5' : ''"
					class="size-full text-base focus:outline-none"
					name="<?php echo esc_attr($email_key); ?>"
					id="<?php echo esc_attr($email_key); ?>"
					value="<?php echo esc_attr($email_input_value); ?>"
					<?php if ($email_required) echo 'aria-required="true"'; ?>
					<?php if ($email_autocomplete) echo 'autocomplete="' . esc_attr($email_autocomplete) . '"'; ?> />
			</div>
			<p class="mt-1 text-xs text-red-700" x-bind:aria-hidden="errors.<?php echo esc_attr($email_key); ?> ? 'false' : 'true'" x-show="errors.<?php echo esc_attr($email_key); ?>" x-text="errors.<?php echo esc_attr($email_key); ?>"></p>
		</div>

		<div>
			<h2 class="mb-4 text-lg font-medium">Informações de cobrança</h2>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<?php
				// Unset email field as it is already rendered above.
				unset($fields['billing_email']);
				// Loop through the remaining billing fields.
				// Note: The fields should have been already sorted by priority.
				foreach ($fields as $key => $field) {
					$label = $field['label'] ?? '';
					$input_type = $field['type'] ?? 'text';
					$input_value = $checkout->get_value($key) ?? '';
					$required = $field['required'] ?? false;
					$autocomplete = $field['autocomplete'] ?? false;
					// If the field is CPF, CNPJ, IE, or COMPANY, display as required.
					if (in_array($key, ['billing_persontype', 'billing_cpf', 'billing_cnpj', 'billing_ie', 'billing_company'], true)) {
						$required = true;
					}
				?>
					<div id="<?php echo esc_attr($key) . '_field'; ?>">
						<div
							x-init="if ($refs.<?php echo esc_attr($key); ?>.value !== '') active = true" x-data="{ active: false }"
							class="relative flex items-center justify-start h-13 px-3 py-1 border border-neutral-500 rounded-sm bg-white">

							<label x-bind:class="active ? 'top-1 text-xs text-neutral-800' : 'text-sm text-neutral-500'" class="absolute pointer-events-none transition-all ease-in-out duration-200" for="<?php echo esc_attr($key); ?>">
								<?php echo esc_html($label); ?><?php if (!$required) echo ' (opcional)'; ?>
							</label>

							<?php if ($input_type === 'country' || $input_type === 'state' || $input_type === 'select') :
								if ($input_type === 'country') $countries = WC()->countries->get_allowed_countries();
								if ($input_type === 'state') {
									$for_country = $field['country'] ?? WC()->checkout->get_value('billing_country');
									$states = WC()->countries->get_states($for_country);
								}
								$field['options'] = $input_type === 'country' ? $countries : ($input_type === 'state' ? $states : ($field['options'] ?? []));
							?>
								<select
									x-ref="<?php echo esc_attr($key); ?>"
									x-model.fill="<?php echo esc_attr($key); ?>"
									class="appearance-none cursor-pointer absolute inset-0 px-3 pt-5.5 pb-1 text-base focus:outline-none"
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
									x-bind:class="active ? 'pt-4.5' : ''"
									class="size-full text-base focus:outline-none"
									name="<?php echo esc_attr($key); ?>"
									id="<?php echo esc_attr($key); ?>"
									value="<?php echo esc_attr($input_value); ?>"
									<?php if ($required) echo 'aria-required="true"'; ?>
									<?php if ($autocomplete) echo 'autocomplete="' . esc_attr($autocomplete) . '"'; ?> />

							<?php endif; ?>

						</div>
						<p class="mt-1 text-xs text-red-700" x-bind:aria-hidden="errors.<?php echo esc_attr($key); ?> ? 'false' : 'true'" x-show="errors.<?php echo esc_attr($key); ?>" x-text="errors.<?php echo esc_attr($key); ?>"></p>
					</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>