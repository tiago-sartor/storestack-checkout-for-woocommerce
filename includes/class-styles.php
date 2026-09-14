<?php

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined('ABSPATH') || exit;

/**
 * Handles dynamic CSS rendering based on user-defined plugin settings.
 *
 * Generates and injects CSS custom properties (variables) and styling rules
 * based on colors and options configured in the plugin's settings.
 *
 * @since 1.0.0
 */
class Styles
{
    /**
     * Unified settings option key in wp_options.
     *
     * @var string
     */
    private const SETTINGS_KEY = 'sscfw_settings';   

    /**
     * Initialize style hooks.
     */
    public function __construct()
    {
        // Enqueue custom styles right after checkout styles are enqueued
        add_action( 'storestack_checkout_enqueue_scripts', [$this, 'enqueue_custom_styles'] );
    }

    /**
     * Retrieve all style settings merged with defaults.
     *
     * @return array<string, string> Merged style settings array.
     */
    public function get_settings(): array
    {
        $defaults = [
            'primary_color'       => '#171717',
            'primary_hover_color' => '#404040',
            'primary_text_color'  => '#ffffff',
            'accent_color'        => '#262626',
            'link_color'          => '#0084d1',
            'header_bg_color'     => '#171717',
            'header_text_color'   => '#ffffff',
            'footer_bg_color'     => '#fafafa',
            'footer_text_color'   => '#737373',
            'button_radius'       => '4px',
            'input_radius'        => '4px',
            'custom_css'          => '',
        ];

        $stored_settings = get_option(self::SETTINGS_KEY, []);

        if (!is_array($stored_settings)) {
            $stored_settings = [];
        }

        $settings = [];

        foreach ($defaults as $key => $default_value) {
            if (isset($stored_settings[$key]) && $stored_settings[$key] !== '') {
                $settings[$key] = $stored_settings[$key];
            } else {
                $settings[$key] = $default_value;
            }
        }

        /**
         * Filters the style settings before CSS generation.
         *
         * @since 1.0.0
         *
         * @param array $settings Merged style settings array.
         */
        return apply_filters('storestack_checkout_style_settings', $settings);
    }

    /**
     * Generate the CSS rules based on the user-defined settings.
     *
     * @return string
     */
    public function generate_css(): string
    {
        $settings = $this->get_settings();

        // Sanitize colors
        $primary_color       = sanitize_hex_color((string) $settings['primary_color']);
        $primary_hover_color = sanitize_hex_color((string) $settings['primary_hover_color']);
        $primary_text_color  = sanitize_hex_color((string) $settings['primary_text_color']);
        $accent_color        = sanitize_hex_color((string) $settings['accent_color']);
        $link_color          = sanitize_hex_color((string) $settings['link_color']);
        $header_bg_color     = sanitize_hex_color((string) $settings['header_bg_color']);
        $header_text_color   = sanitize_hex_color((string) $settings['header_text_color']);
        $footer_bg_color     = sanitize_hex_color((string) $settings['footer_bg_color'] );
        $footer_text_color   = sanitize_hex_color((string) $settings['footer_text_color'] );

        // Sanitize dimensions
        $button_radius = $this->sanitize_dimension((string) $settings['button_radius']);
        $input_radius  = $this->sanitize_dimension((string) $settings['input_radius']);

        // Sanitize custom CSS
        $custom_css = wp_strip_all_tags((string) $settings['custom_css']);

        ob_start();
        ?>
        :root {
            --sscfw-primary-color: <?php echo esc_attr($primary_color); ?>;
            --sscfw-primary-hover-color: <?php echo esc_attr($primary_hover_color); ?>;
            --sscfw-primary-text-color: <?php echo esc_attr($primary_text_color); ?>;
            --sscfw-accent-color: <?php echo esc_attr($accent_color); ?>;
            --sscfw-link-color: <?php echo esc_attr($link_color); ?>;
            --sscfw-header-bg: <?php echo esc_attr($header_bg_color); ?>;
            --sscfw-header-text-color: <?php echo esc_attr($header_text_color); ?>;
            --sscfw-footer-bg: <?php echo esc_attr($footer_bg_color); ?>;
            --sscfw-footer-text-color: <?php echo esc_attr($footer_text_color); ?>;
            --sscfw-button-radius: <?php echo esc_attr($button_radius); ?>;
            --sscfw-input-radius: <?php echo esc_attr($input_radius); ?>;
        }

        /* Header customization */
        .storestack-checkout-header {
            background-color: var(--sscfw-header-bg) !important;
            color: var(--sscfw-header-text-color) !important;
        }

        .storestack-checkout-header a,
        .storestack-checkout-header svg,
        .storestack-checkout-header span {
            color: var(--sscfw-header-text-color) !important;
        }

        /* Footer customization */
        footer.storestack-checkout-footer,
        footer.bg-neutral-50 {
            background-color: var(--sscfw-footer-bg) !important;
            color: var(--sscfw-footer-text-color) !important;
        }

        footer.bg-neutral-50 a,
        footer.bg-neutral-50 ul,
        footer.bg-neutral-50 div {
            color: var(--sscfw-footer-text-color);
        }

        /* Primary action button (Place Order / Submit) */
        .woocommerce-checkout #place_order,
        .woocommerce-checkout button[type="submit"].button.alt,
        .storestack-checkout-button-primary {
            background-color: var(--sscfw-primary-color) !important;
            color: var(--sscfw-primary-text-color) !important;
            border-radius: var(--sscfw-button-radius) !important;
        }

        .woocommerce-checkout #place_order:hover,
        .woocommerce-checkout button[type="submit"].button.alt:hover,
        .storestack-checkout-button-primary:hover {
            background-color: var(--sscfw-primary-hover-color) !important;
        }

        /* Form inputs and controls */
        .woocommerce-checkout input[type="text"],
        .woocommerce-checkout input[type="tel"],
        .woocommerce-checkout input[type="email"],
        .woocommerce-checkout select,
        .woocommerce-checkout textarea {
            border-radius: var(--sscfw-input-radius);
        }

        .woocommerce-checkout input[type="text"]:focus,
        .woocommerce-checkout input[type="tel"]:focus,
        .woocommerce-checkout input[type="email"]:focus,
        .woocommerce-checkout select:focus,
        .woocommerce-checkout textarea:focus {
            border-color: var(--sscfw-accent-color);
            outline-color: var(--sscfw-accent-color);
        }

        /* Links */
        .woocommerce-checkout a:not(.button),
        .woocommerce-terms-and-conditions-wrapper a {
            color: var(--sscfw-link-color);
        }

        .woocommerce-checkout a:not(.button):hover,
        .woocommerce-terms-and-conditions-wrapper a:hover {
            filter: brightness(0.8);
        }

        <?php if (!empty($custom_css)) : ?>
        /* User-defined Custom CSS */
        <?php echo $custom_css; ?>
        <?php endif; ?>
        
        <?php
        $css = (string) ob_get_clean();

        // Minify whitespace
        $css = trim( preg_replace( '/\s+/', ' ', $css ) ?? $css );

        /**
         * Filters the rendered CSS styles before output.
         *
         * Allows developers to customize or append to the generated CSS.
         *
         * @since 1.0.0
         *
         * @param string $css      Generated CSS rules.
         * @param array  $settings Style settings array.
         */
        return apply_filters('storestack_checkout_custom_styles', $css, $settings);
    }

    /**
     * Enqueue dynamic custom styles onto the plugin's stylesheet handle.
     */
    public function enqueue_custom_styles(): void
    {
        $css = $this->generate_css();

        if (!empty($css) && wp_style_is(Checkout::STYLE_HANDLE, 'enqueued')) {
            wp_add_inline_style(Checkout::STYLE_HANDLE, $css);
        }
    } 

    /**
     * Sanitize a CSS dimension value (e.g. '8px', '0.5rem', '50%').
     *
     * @param string $dimension Raw dimension string.
     * @param string $default   Default fallback dimension.
     * @return string
     */
    private function sanitize_dimension(string $dimension, string $default = ''): string
    {
        $dimension = trim($dimension);

        if (empty($dimension)) {
            return $default;
        }

        // Validate dimension with common CSS units
        if (preg_match('/^[0-9]+(\.[0-9]+)?(px|em|rem|%|vh|vw)$/i', $dimension)) {
            return esc_attr($dimension);
        }

        // If integer only, treat as pixels
        if (is_numeric($dimension)) {
            return absint($dimension) . 'px';
        }

        return $default;
    }
}
