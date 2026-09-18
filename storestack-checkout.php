<?php
/**
 * Plugin Name:          StoreStack Checkout for WooCommerce
 * Plugin URI:           https://github.com/StoreStack/storestack-checkout-for-woocommerce
 * Description:          StoreStack Checkout and Thank You Page for WooCommerce is a plugin that allows you to replace the default WooCommerce checkout with a more modern and streamlined experience.
 * Version:              1.0.0
 * Author:               StoreStack
 * Author URI:           https://github.com/StoreStack
 * License:              GPLv3 or later
 * License URI:          https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:          storestack-checkout-for-woocommerce
 * Requires at least:    6.7
 * Tested up to:         7.1
 * Requires Plugins:     woocommerce
 * WC requires at least: 10.0
 * WC tested up to:      11.1
 * Requires PHP:         8.2
 */

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined('ABSPATH') || exit;


class Loader
{
    private static ?self $instance = null;

    public function __construct()
    {
        add_action('before_woocommerce_init', [$this, 'declare_wc_support']);
        add_action('plugins_loaded', [$this, 'init']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public static function run(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function define_constants(): void
    {
        define('SSCFW_PLUGIN_VERSION', '1.0.0');
        define('SSCFW_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('SSCFW_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    private function load_classes(): void
    {
        $includes_dir = SSCFW_PLUGIN_PATH . 'includes/';

        require_once $includes_dir . 'class-checkout.php';
        new Checkout();

        require_once $includes_dir . 'class-thankyou.php';
        new ThankYou();

        require_once $includes_dir . 'class-styles.php';
        new Styles();
    }

    public function init(): void
    {
        $this->define_constants();
        $this->load_classes();

        $installed_version = get_option('sscfw_checkout_plugin_version');

        if ($installed_version !== SSCFW_PLUGIN_VERSION) {
            update_option('sscfw_checkout_plugin_version', SSCFW_PLUGIN_VERSION);
        }
    }

    public function enqueue_admin_scripts(): void
    {
        if ( is_admin() && ! wp_doing_ajax() ) {
            wp_enqueue_style('storestack-checkout-for-woocommerce-admin', SSCFW_PLUGIN_URL . 'assets/css/admin.css', array(), SSCFW_PLUGIN_VERSION);
            wp_enqueue_script('storestack-checkout-for-woocommerce-admin', SSCFW_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), SSCFW_PLUGIN_VERSION, true);
        }
    }

    public function declare_wc_support(): void
    {
        if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        }
    }
}


/**
 * Run the plugin
 */
Loader::run();
