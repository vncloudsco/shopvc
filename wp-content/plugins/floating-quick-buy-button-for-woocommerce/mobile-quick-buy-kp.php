<?php

/**
 *
 * @link              https://kraftpixel.com/plugins/
 * @since             1.0.0
 * @package           Mobile_Quick_Buy_KP
 *
 * @wordpress-plugin
 * Plugin Name:       Floating Quick Buy Button for WooCommerce
 * Plugin URI:        https://kraftpixel.com/plugins/mobile-quick-buy-woocommerce
 * Description:       The floating buy button plugin for WooCommerce to help improve e-commerce conversions on mobile. Plugin developed with love at KraftPixel.
 * Version:           1.1.0
 * Author:            KraftPixel
 * Author URI:        https://kraftpixel.com/plugins/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mobile-quick-buy-kp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MQBK_MOBILE_QUICK_BUY_KP_VERSION', '1.0.0' );
define( 'MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY', 'mobile_quick_buy_kp_option_name' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mobile-quick-buy-kp-activator.php
 */
function activate_mobile_quick_buy_kp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-quick-buy-kp-activator.php';
	Mobile_Quick_Buy_KP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mobile-quick-buy-kp-deactivator.php
 */
function deactivate_mobile_quick_buy_kp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobile-quick-buy-kp-deactivator.php';
	Mobile_Quick_Buy_KP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mobile_quick_buy_kp' );
register_deactivation_hook( __FILE__, 'deactivate_mobile_quick_buy_kp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mobile-quick-buy-kp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mobile_quick_buy_kp() {

	$plugin = new Mobile_Quick_Buy_KP();
	$plugin->run();

}
run_mobile_quick_buy_kp();