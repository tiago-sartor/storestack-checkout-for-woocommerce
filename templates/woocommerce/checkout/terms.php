<?php

/**
 * Checkout terms and conditions area.
 *
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

declare(strict_types=1);
defined('ABSPATH') || exit;

if (apply_filters('woocommerce_checkout_show_terms', true) && function_exists('wc_terms_and_conditions_checkbox_enabled')) {
	do_action('woocommerce_checkout_before_terms_and_conditions');
?>

	<div class="my-5 space-y-3 text-xs text-neutral-500 leading-relaxed woocommerce-terms-and-conditions-wrapper">
		<?php
		/**
		 * Terms and conditions hook used to inject content.
		 *
		 * @since 3.4.0.
		 * @hooked wc_checkout_privacy_policy_text() Shows custom privacy policy text. Priority 20.
		 * @hooked wc_terms_and_conditions_page_content() Shows t&c page content. Priority 30.
		 */
		do_action('woocommerce_checkout_terms_and_conditions');
		?>

		<?php if (wc_terms_and_conditions_checkbox_enabled()) : ?>
			<p class="form-row validate-required mt-3">
				<label class="inline-flex items-center gap-2.5 cursor-pointer select-none woocommerce-form__label woocommerce-form__label-for-checkbox checkbox text-neutral-700 hover:text-neutral-900">
					<input
						type="checkbox"
						class="size-4 border-neutral-300 accent-neutral-900 cursor-pointer woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
						name="terms"
						<?php checked(apply_filters('woocommerce_terms_is_checked_default', isset($_POST['terms'])), true); ?>
						id="terms" />
					<span class="woocommerce-terms-and-conditions-checkbox-text text-xs leading-normal">
						<?php wc_terms_and_conditions_checkbox_text(); ?>
						<abbr class="required text-red-600 no-underline" title="<?php esc_attr_e('required', 'woocommerce'); ?>">*</abbr>
					</span>
				</label>
				<input type="hidden" name="terms-field" value="1" />
			</p>
		<?php endif; ?>
	</div>

<?php
	do_action('woocommerce_checkout_after_terms_and_conditions');
}
