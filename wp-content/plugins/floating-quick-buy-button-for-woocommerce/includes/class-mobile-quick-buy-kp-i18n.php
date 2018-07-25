<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://kraftpixel.com/plugins/
 * @since      1.0.0
 *
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/includes
 * @author     KraftPixel <info+plugins@kraftpixel.com>
 */
class Mobile_Quick_Buy_KP_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mobile-quick-buy-kp',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
