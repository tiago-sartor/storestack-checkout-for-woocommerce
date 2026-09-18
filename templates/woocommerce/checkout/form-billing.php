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

		$email_label = $email_field['label'] ?? __('Email address', 'woocommerce');
		$email_input_type = $email_field['type'] ?? 'email';
		$email_input_value = $checkout->get_value($email_key) ?? '';
		$email_required = $email_field['required'] ?? true;
		$email_autocomplete = $email_field['autocomplete'] ?? 'email';
		?>
		<div class="contact-information">
			<div class="flex items-baseline justify-between mb-4">
				<h2 class="text-lg font-semibold text-neutral-900 tracking-tight">
					<?php esc_html_e('Contact', 'woocommerce'); ?>
				</h2>
				<?php if (!is_user_logged_in() && ($checkout->is_registration_enabled() || 'yes' === get_option('woocommerce_enable_checkout_login_reminder'))) : ?>
					<div class="text-xs sm:text-sm">
						<span class="text-neutral-500"><?php esc_html_e('Have an account?', 'woocommerce'); ?></span>
						<button data-open-login-dialog type="button" class="font-medium text-neutral-900 underline hover:text-neutral-600 transition-colors ml-1 cursor-pointer">
							<?php esc_html_e('Log in', 'woocommerce'); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>

			<div
				class="form-floating-field"
				id="<?php echo esc_attr($email_key) . '_field'; ?>">
				<label for="<?php echo esc_attr($email_key); ?>">
					<?php echo esc_html($email_label); ?>
				</label>
				<input
					type="<?php echo esc_attr($email_input_type); ?>"
					class="size-full text-sm text-neutral-900 bg-transparent focus:outline-none transition-all"
					name="<?php echo esc_attr($email_key); ?>"
					id="<?php echo esc_attr($email_key); ?>"
					value="<?php echo esc_attr($email_input_value); ?>"
					placeholder=" "
					<?php if ($email_required) echo 'aria-required="true"'; ?>
					<?php if ($email_autocomplete) echo 'autocomplete="' . esc_attr($email_autocomplete) . '"'; ?> />
			</div>
			<p class="field-error mt-1.5 text-xs text-red-600 font-medium" data-error-for="<?php echo esc_attr($email_key); ?>" hidden aria-hidden="true"></p>
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
					$label = $field['label'] ?? '';
					$input_type = $field['type'] ?? 'text';
					$input_value = $checkout->get_value($key) ?? '';
					$required = $field['required'] ?? false;
					$autocomplete = $field['autocomplete'] ?? false;

					// If the field is CPF, CNPJ, IE, or COMPANY, display as required.
					if (in_array($key, ['billing_persontype', 'billing_cpf', 'billing_cnpj', 'billing_ie', 'billing_company'], true)) {
						$required = true;
					}

					// Full width fields
					$is_full_width = in_array($key, ['billing_address_1', 'billing_address_2', 'billing_company'], true);
				?>
					<div id="<?php echo esc_attr($key) . '_field'; ?>" class="<?php echo $is_full_width ? 'sm:col-span-2' : ''; ?>">
						<div class="form-floating-field">

							<label for="<?php echo esc_attr($key); ?>">
								<?php echo esc_html($label); ?><?php if (!$required) echo ' (' . esc_html__('optional', 'woocommerce') . ')'; ?>
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

	</div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>