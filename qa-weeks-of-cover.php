<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name: QA Weeks Of Cover
 * Description: A system that allows you to track the availability of stock of your store, products and categories.
 * Plugin URI:  https://quickassortments.com/products/
 * Version:     1.0.0
 * Author:      QuickAssortments AB
 * Author URI:  https://quickassortments.com/
 * License:     GPL-2.0
 * Text Domain: qa-weeks-of-cover
 * Domain Path: /languages
 */

namespace QuickAssortments\WOC;

/**
 * Defining base constant
 */
defined( 'ABSPATH' ) || die;

if ( ! defined( 'QA_WOC_VERSION' ) ) {
	define( 'QA_WOC_VERSION', __( '1.0.0', 'qa-weeks-of-cover' ) );
}

if ( ! defined( 'QA_WOC_BASE_PATH' ) ) {
	define( 'QA_WOC_BASE_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'QA_WOC_BASE_URL' ) ) {
	define( 'QA_WOC_BASE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'QA_WOC_BASENAME' ) ) {
	define( 'QA_WOC_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'QA_WOC_PREFIX' ) ) {
	define( 'QA_WOC_PREFIX', 'qa_woc_' );
}

if ( ! defined( 'QA_WOC_DEBUG' ) ) {
	define( 'QA_WOC_DEBUG', false );
}

/**
 * Initialize a hook on plugin activation.
 *
 * @return void
 */
function activate() {
	do_action( 'qa_woc_plugin_activate' );
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Initialize a hook on plugin deactivation.
 *
 * @return void
 */
function deactivate() {
	do_action( 'qa_woc_plugin_deactivate' );
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );

/**
 * Initialize all the plugin things.
 *
 * @return array | bool | void
 * @throws \Throwable
 */
function initialize() {

	try {
		// Translation directory updated !
		load_plugin_textdomain(
			'qa-weeks-of-cover',
			true,
			basename( dirname( __FILE__ ) ) . '/languages'
		);

		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		/**
		 * Check if WooCommerce is active
		 **/
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action(
				'admin_notices',
				function () {
					$class   = 'notice notice-error is-dismissible';
					$message = __( 'Quick Assortments Error: <b>WooCommerce</b> isn\'t active.', 'qa-weeks-of-cover' );
					printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
				}
			);

			return false;

		}
		/**
		 * Check if QA Cost of Goods & Margins is active
		 **/
		if ( ! is_plugin_active( 'qa-cost-of-goods-margins/qa-cost-of-goods-margins.php' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action(
				'admin_notices',
				function () {
					$class   = 'notice notice-error is-dismissible';
					$message = __( 'Quick Assortments Error: <b>QA Cost of Goods & Margins</b> isn\'t active.', 'qa-weeks-of-cover' );
					printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
				}
			);

			return false;

		}

		/**
		 * Checking if vendor/autoload.php exists or not.
		 */
		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once __DIR__ . '/vendor/autoload.php';
		}

		/**
		 * Calling modules.
		 */
		// Columns initialization class
		$modules['columns'] = ( new Admin\Columns() )->init();
		// Columns initialization class
		$modules['fields'] = ( new Admin\Fields( '_qa_woc_' ) )->init();
		$modules           = apply_filters( 'qa_woc_modules', $modules );

		return (array) $modules;

	} catch ( \Throwable $throwable ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $throwable;
		}
		do_action( 'qa_woc_error', $throwable );
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\initialize' );
