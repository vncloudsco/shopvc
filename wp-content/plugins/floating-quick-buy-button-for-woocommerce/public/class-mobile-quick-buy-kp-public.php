<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://kraftpixel.com/plugins/
 * @since      1.0.0
 *
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/public
 * @author     KraftPixel <info+plugins@kraftpixel.com>
 */
class Mobile_Quick_Buy_KP_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mobile_Quick_Buy_KP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mobile_Quick_Buy_KP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mobile-quick-buy-kp-public.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mobile_Quick_Buy_KP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mobile_Quick_Buy_KP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mobile-quick-buy-kp-public.js', array( 'jquery' ), $this->version, false );
		}

	}

	public function add_quick_buy_btn() {
		if ( class_exists( 'WooCommerce' ) ) {
			global $product; // WooCommerce Global product variable
//			$product_id = $product->get_id(); // current product id of woocommerce
			$mobile_quick_buy_kp_options = get_option( MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY );
			$select_button_intention_0 = $mobile_quick_buy_kp_options['select_button_intention_0']; // Select Button Intention
			$select_both_7 = $mobile_quick_buy_kp_options['select_both_7']; // Select Button Intention
			$background_color_1 = $mobile_quick_buy_kp_options['background_color_1']; // Background Color
			$text_color_2 = $mobile_quick_buy_kp_options['text_color_2']; // Text Color
			$button_text_3 = $mobile_quick_buy_kp_options['button_text_3']; // Button Text
			$button_visibility_4 = $mobile_quick_buy_kp_options['button_visibility_4']; // Button Visibility
			$enter_top_offset_height_in_number_5 = $mobile_quick_buy_kp_options['enter_top_offset_height_in_number_5']; // Enter top offset height in number
			$custom_css_6 = $mobile_quick_buy_kp_options['custom_css_6']; // Custom CSS
			$checkout_url = wc_get_checkout_url();

			$cart_add_to_cart = '&#xf217;';
			$cart_quick_buy = '&#xe800;';

			if ( $select_button_intention_0 == 'quick-buy') {
				$cart_icon = $cart_quick_buy;
				$extra_class_name = ' quick-buy ';
			}else {
				$cart_icon = $cart_add_to_cart;
				$extra_class_name = ' add-to-cart ';
			}

			if ( $button_visibility_4 == 'on-scroll' ) {
				$extra_class_name .= ' on-scroll';
			}else {
				$extra_class_name .= ' all-time';
			}

			if ( $select_both_7 ) {
				$extra_class_name .= ' both-active';
			}

			$button_container .= sprintf('
				<div class="mobile-quick-buy-kp-sticky %s" style="background: %s;" data-btn-intention="%s" data-checkout-url="%s" data-button-visibility="%s" data-top-offset="%s" data-show-both="%s">',
				esc_attr($extra_class_name),
				esc_attr($background_color_1),
				esc_attr($select_button_intention_0),
				esc_attr($checkout_url),
				esc_attr($button_visibility_4),
				esc_attr($enter_top_offset_height_in_number_5),
				esc_attr($select_both_7)
			);
			if ( $select_both_7 ) {
				$button_container .= sprintf(
					'<a href="/?add-to-cart=%s" class="mobile-quick-buy btn-add-to-cart" style="color: %s;"><i class="kp-icon">%s</i>Add to cart</a>',
					esc_attr($product_id),
					esc_attr($text_color_2),
					esc_attr($cart_add_to_cart)
				);
				$button_container .= sprintf(
					'<a href="/?add-to-cart=%s" class="mobile-quick-buy btn-quick-buy" style="color: %s;"><i class="kp-icon">%s</i>Buy Now</a>',
					esc_attr($product_id),
					esc_attr($text_color_2),
					esc_attr($cart_quick_buy)
				);
			}else {
				$button_container .= sprintf(
					'<a href="/?add-to-cart=%s" class="mobile-quick-buy" style="color: %s;"><i class="kp-icon">%s</i>%s</a>',
					esc_attr($product_id),
					esc_attr($text_color_2),
					esc_attr($cart_icon),
					esc_html( $button_text_3)
				);
			}

			$button_container .= '</div>';
			if ( $custom_css_6 ) {
				echo '<style type="text/css">'.$custom_css_6.'</style>';
			}




			echo $button_container;
		}

	}


}