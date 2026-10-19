<?php
/**
 * Checkout template and frontend behavior.
 *
 * @package StoreStackCheckoutForWooCommerce
 */

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined( 'ABSPATH' ) || exit;


/**
 * Class Checkout.
 *
 * Controls the custom WooCommerce checkout experience.
 */
class Checkout {
	/**
	 * Stylesheet handle to which dynamic CSS is attached.
	 *
	 * @var string
	 */
	public const STYLE_HANDLE = 'storestack-checkout';

	/**
	 * Register checkout filters and actions.
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'override_checkout_template' ), 9999 );
		add_filter( 'woocommerce_locate_template', array( $this, 'override_checkout_template_parts' ), 9999, 3 );

		add_filter( 'woocommerce_form_field', array( $this, 'add_error_message_to_form_field' ), 10, 2 );

		// Disable product variation attributes in product titles on the checkout page.
		add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );

		// Remove default WooCommerce sections from the checkout page.
		// This is done to prevent duplicate content and to allow our custom checkout template
		// to take full control of the layout and functionality.
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_output_all_notices' );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );

		// Keep an empty placeholder value so address-i18n does not fall back to the field label.
		add_filter( 'woocommerce_get_country_locale', array( $this, 'remove_placeholders_from_locales' ), 9999 );
		add_filter( 'woocommerce_get_country_locale_base', array( $this, 'remove_placeholders_from_base_locales' ), 9999 );

		add_action( 'wp_enqueue_scripts', array( $this, 'checkout_enqueue_scripts' ), 9999 );
	}


	/**
	 * Remove placeholders from localized checkout fields.
	 *
	 * @param array<string, mixed> $locales Country locale field definitions.
	 * @return array<string, mixed> Updated country locale field definitions.
	 */
	public function remove_placeholders_from_locales( array $locales ): array {
		foreach ( $locales as &$country_fields ) {
			if ( ! is_array( $country_fields ) ) {
				continue;
			}

			foreach ( $country_fields as &$field ) {
				if ( is_array( $field ) ) {
					$field['placeholder'] = '';
				}
			}
			unset( $field );
		}
		unset( $country_fields );

		return $locales;
	}

	/**
	 * Remove placeholders from base checkout fields.
	 *
	 * @param array<string, mixed> $fields Base checkout field definitions.
	 * @return array<string, mixed> Updated checkout field definitions.
	 */
	public function remove_placeholders_from_base_locales( array $fields ): array {
		foreach ( $fields as &$field ) {
			if ( is_array( $field ) ) {
				$field['placeholder'] = '';
			}
		}
		unset( $field );

		return $fields;
	}

	/**
	 * Determine whether the current request is an active checkout page.
	 *
	 * @return bool Whether the custom checkout template should be used.
	 */
	private function is_checkout_page(): bool {
		// Check if the current page is the WooCommerce checkout page, but not the "Order Received" (Thank You) or "Pay" endpoints.
		if ( ! is_checkout() || is_order_received_page() || is_checkout_pay_page() ) {
			return false;
		}

		// Also check if the cart instance exists and is not empty.
		if ( ! WC()->cart || WC()->cart->is_empty() ) { // @phpstan-ignore booleanNot.alwaysFalse (Intentional defensive check)
			return false;
		}

		return true;
	}

	/**
	 * Select the custom checkout page template when checkout is active.
	 *
	 * @param string $template Original template path.
	 * @return string Selected template path.
	 */
	public function override_checkout_template( string $template ): string {
		if ( ! $this->is_checkout_page() ) {
			return $template;
		}

		/**
		 * Filters the override template for the checkout page.
		 *
		 * @since 1.0.0
		 *
		 * @param string $override_template The path to the override template.
		 * @param string $template The original template path.
		 */
		$override_template = apply_filters( 'storestack_checkout_override_template', SSCFW_PLUGIN_PATH . 'templates/checkout.php', $template );

		return file_exists( $override_template ) ? $override_template : $template;
	}

	/**
	 * Select a custom WooCommerce template part during checkout.
	 *
	 * @param string $template      Original template path.
	 * @param string $template_name Template name relative to WooCommerce templates.
	 * @param string $template_path WooCommerce template directory.
	 * @return string Selected template path.
	 */
	public function override_checkout_template_parts( string $template, string $template_name, string $template_path ): string {
		if ( ! $this->is_checkout_page() || 'woocommerce/' !== $template_path ) {
			return $template;
		}

		/**
		 * Filters the override template part for the checkout page.
		 *
		 * @since 1.0.0
		 *
		 * @param string $override_template The path to the override template.
		 * @param string $template The original template path.
		 * @param string $template_name The name of the template.
		 * @param string $template_path The path to the template.
		 */
		$override_template = apply_filters(
			'storestack_checkout_override_template_part',
			SSCFW_PLUGIN_PATH . 'templates/woocommerce/' . $template_name,
			$template,
			$template_name,
			$template_path
		);

		return file_exists( $override_template ) ? $override_template : $template;
	}

	/**
	 * Append the inline validation message element to a checkout field.
	 *
	 * @param string $field Rendered form field markup.
	 * @param string $key   Checkout field key.
	 * @return string Updated form field markup.
	 */
	public function add_error_message_to_form_field( string $field, string $key ): string {
		// Only add error message for checkout form fields.
		if ( ! $this->is_checkout_page() ) {
			return $field;
		}

		// Add a <p> element for displaying error messages.
		$error_html = sprintf(
			'<span class="field-error mt-1.5 ml-0.5 block text-xs font-medium text-red-600" data-error-for="%1$s" hidden aria-hidden="true"></span>',
			esc_attr( $key )
		);

		// If the field wrapper ends with </p>, insert the error before it closes.
		if ( str_ends_with( rtrim( $field ), '</p>' ) ) {
			return substr_replace( rtrim( $field ), $error_html . '</p>', -4 );
		}

		// Fallback: append inside if wrapped in </div> or just append.
		if ( str_ends_with( rtrim( $field ), '</div>' ) ) {
			return substr_replace( rtrim( $field ), $error_html . '</div>', -6 );
		}

		return $field . $error_html;
	}

	/**
	 * Enqueue checkout assets and remove conflicting theme styles.
	 */
	public function checkout_enqueue_scripts(): void {
		if ( ! $this->is_checkout_page() ) {
			return;
		}

		// Remove theme-related CSS styles (plugin/gateway styles are preserved).
		$wp_styles = wp_styles();

		foreach ( $wp_styles->queue as $handle ) {
			if ( ! isset( $wp_styles->registered[ $handle ] ) ) {
				continue;
			}

			$src = $wp_styles->registered[ $handle ]->src;

			// 1. Check if the source file is located in the themes directory (this includes both parent and child themes)
			if ( ! empty( $src ) && str_contains( $src, '/wp-content/themes/' ) ) {
				wp_dequeue_style( $handle );
			}

			// 2. Block themes: 'global-styles' is generated by WP core from theme.json / customizer
			if ( in_array( $handle, array( 'global-styles', 'wp-block-library-theme' ), true ) ) {
				wp_dequeue_style( $handle );
			}
		}

		// Dequeue WooCommerce Checkout Block styles and scripts.
		wp_dequeue_script( 'wc-checkout-block' );

		// Ensure the classic checkout scripts load so AJAX cart calculation
		// and third-party payment gateways still function correctly.
		wp_enqueue_script( 'wc-checkout' );

		// Enqueue our CSS styles.
		wp_enqueue_style( self::STYLE_HANDLE, SSCFW_PLUGIN_URL . 'assets/css/frontend.css', array(), SSCFW_PLUGIN_VERSION );

		// Enqueue core checkout JS (dialog, coupon AJAX, Enter-key guard, mobile total sync).
		wp_enqueue_script(
			'storestack-checkout-bundle',
			SSCFW_PLUGIN_URL . 'assets/js/checkout.js',
			array( 'jquery' ),
			SSCFW_PLUGIN_VERSION,
			true
		);
		wp_localize_script(
			'storestack-checkout-bundle',
			'storeStackCheckoutValidation',
			array(
				'messages' => array(
					/* translators: %s: checkout field label. */
					'required' => __( '%s is required.', 'storestack-checkout-for-woocommerce' ),
					/* translators: %s: checkout field label. */
					'invalid'  => __( '%s is invalid.', 'storestack-checkout-for-woocommerce' ),
				),
			)
		);

		/**
		 * Fires after the checkout scripts are enqueued.
		 *
		 * Allows developers to enqueue additional scripts or styles for the checkout page.
		 *
		 * @since 1.0.0
		 */
		do_action( 'storestack_checkout_enqueue_scripts' );
	}
}
