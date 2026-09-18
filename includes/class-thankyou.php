<?php

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined('ABSPATH') || exit;


class ThankYou
{
    public function __construct() {
        // Replace the default WooCommerce thank you template with the custom one
        add_filter('woocommerce_locate_template', [$this, 'locate_template'], 10, 3);
    }

    public function locate_template($template, $template_name, $template_path) {
        if ($template_name === 'checkout/thankyou.php') {
            $custom_template = SSCFW_PLUGIN_PATH . 'templates/woocommerce/checkout/thankyou.php';
            if (!file_exists($custom_template)) {
                $custom_template = SSCFW_PLUGIN_PATH . 'templates/checkout/thankyou.php';
            }
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
}
