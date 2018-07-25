<?php

/**
 * Fired during plugin activation
 *
 * @link       https://kraftpixel.com/plugins/
 * @since      1.0.0
 *
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/includes
 * @author     KraftPixel <info+plugins@kraftpixel.com>
 */
class Mobile_Quick_Buy_KP_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( !class_exists( 'WooCommerce' ) ) {
			die("Failed to activate Mobile Quick Buy Plugin because WooCommerce is not installed or activated.");
		}
		if( !get_option(MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY) ) {
			update_option(MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY, (array)json_decode('{"select_button_intention_0":"quick-buy","background_color_1":"#dd9933","text_color_2":"#ffffff","button_text_3":"Buy Now","button_visibility_4":"all-time","enter_top_offset_height_in_number_5":"200","custom_css_6":""}'));
		}
	}

}