<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName
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
 *
 * @package StoreStackCheckoutForWooCommerce
 */

declare(strict_types=1);

namespace StoreStackCheckoutForWooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Bootstraps the StoreStack Checkout plugin.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Register plugin lifecycle hooks.
	 */
	public function __construct() {
		add_action( 'before_woocommerce_init', array( $this, 'declare_wc_support' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Create and return the plugin loader singleton.
	 *
	 * @return self Plugin loader instance.
	 */
	public static function run(): self {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Define plugin constants.
	 */
	private function define_constants(): void {
		define( 'SSCFW_PLUGIN_VERSION', '1.0.0' );
		define( 'SSCFW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'SSCFW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Load plugin classes and register their hooks.
	 */
	private function load_classes(): void {
		$includes_dir = SSCFW_PLUGIN_PATH . 'includes/';

		require_once $includes_dir . 'class-checkout.php';
		new Checkout();

		require_once $includes_dir . 'class-thankyou.php';
		new ThankYou();

		require_once $includes_dir . 'class-styles.php';
		new Styles();
	}

	/**
	 * Initialize plugin classes and update the stored plugin version.
	 */
	public function init(): void {
		$this->define_constants();
		$this->load_classes();

		$installed_version = get_option( 'sscfw_checkout_plugin_version' );

		if ( SSCFW_PLUGIN_VERSION !== $installed_version ) {
			update_option( 'sscfw_checkout_plugin_version', SSCFW_PLUGIN_VERSION );
		}
	}

	/**
	 * Enqueue plugin-specific administration assets.
	 */
	public function enqueue_admin_scripts(): void {
		if ( is_admin() && ! wp_doing_ajax() ) {
			wp_enqueue_style( 'storestack-checkout-for-woocommerce-admin', SSCFW_PLUGIN_URL . 'assets/css/admin.css', array(), SSCFW_PLUGIN_VERSION );
			wp_enqueue_script( 'storestack-checkout-for-woocommerce-admin', SSCFW_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), SSCFW_PLUGIN_VERSION, true );
		}
	}

	/**
	 * Declare compatibility with WooCommerce custom order tables.
	 */
	public function declare_wc_support(): void {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}


/**
 * Run the plugin.
 */
Loader::run();
