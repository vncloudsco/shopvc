<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://kraftpixel.com/plugins/
 * @since      1.0.0
 *
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mobile_Quick_Buy_KP
 * @subpackage Mobile_Quick_Buy_KP/admin
 * @author     KraftPixel <info+plugins@kraftpixel.com>
 */
class Mobile_Quick_Buy_KP_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mobile-quick-buy-kp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		// wp_enqueue_script( $this->plugin_name.'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js', array( 'jquery' ), $this->version, false );
		// wp_enqueue_script( $this->plugin_name.'iris', plugin_dir_url( __FILE__ ) . 'js/iris.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery-ui-core');
		wp_enqueue_script( 'iris' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mobile-quick-buy-kp-admin.js', array( 'jquery' ), $this->version, false );

	}


	public function mobile_quick_buy_kp_add_plugin_page() {
		add_options_page(
			'Mobile Quick Buy', // page_title
			'Mobile Quick Buy', // menu_title
			'manage_options', // capability
			'kraftpixel-mobile-quick-buy', // menu_slug
			array( $this, 'mobile_quick_buy_kp_create_admin_page' ) // function
		);
	}

	public function mobile_quick_buy_kp_create_admin_page() {
		$this->options = get_option( MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY ); ?>

		<div class="wrap">
			<div class="mobile_quick_buy">
			<h2>Mobile Quick Buy for WooCommerce By KraftPixel</h2>
			<p>These settings will be reflected on the product pages</p>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'mobile_quick_buy_kp_option_group' );
					do_settings_sections( 'mobile-quick-buy-kp-admin' );
					submit_button();
				?>
			</form>
				
			</div>
			<div class="mobile-quick-buy-preview">
				<img src="<?php echo plugin_dir_url( __FILE__ ).'images/mobile-frame-final.png'; ?>" width="300" alt="Mobile frame">
				<div id="preview_box"></div>
			</div>
		</div>
			
		</div>
	<?php }

	public function mobile_quick_buy_kp_page_init() {
		register_setting(
			'mobile_quick_buy_kp_option_group', // option_group
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY, // option_name
			array( $this, 'mobile_quick_buy_kp_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'mobile_quick_buy_kp_setting_section', // id
			'General Settings', // title
			array( $this, 'mobile_quick_buy_kp_section_info' ), // callback
			'mobile-quick-buy-kp-admin' // page
		);

		add_settings_field(
			'select_button_intention_0', // id
			'Select Button Intention', // title
			array( $this, 'select_button_intention_0_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);

		add_settings_field(
			'select_both_7', // id
			'Show Both Buttons', // title
			array( $this, 'select_both_7_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);

		add_settings_field(
			'background_color_1', // id
			'Background Color', // title
			array( $this, 'background_color_1_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);

		add_settings_field(
			'text_color_2', // id
			'Text Color', // title
			array( $this, 'text_color_2_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);
		add_settings_field(
			'button_text_3', // id
			'Button Text', // title
			array( $this, 'button_text_3_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);
		add_settings_field(
			'button_visibility_4', // id
			'Button Visibility', // title
			array( $this, 'button_visibility_4_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);
		add_settings_field(
			'enter_top_offset_height_in_number_5', // id
			'Enter top offset height <span class="mobile_quick_buy_tooltip">
				<span class="mobile_quick_buy_tooltip_text">Button will be visible when user scroll down when distance from top meets this value.</span>
			</span>', // title
			array( $this, 'enter_top_offset_height_in_number_5_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);

		add_settings_field(
			'custom_css_6', // id
			'Custom CSS', // title
			array( $this, 'custom_css_6_callback' ), // callback
			'mobile-quick-buy-kp-admin', // page
			'mobile_quick_buy_kp_setting_section' // section
		);		

	}

	public function mobile_quick_buy_kp_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['select_button_intention_0'] ) ) {
			$sanitary_values['select_button_intention_0'] = $input['select_button_intention_0'];
		}

		if ( isset( $input['background_color_1'] ) ) {
			$sanitary_values['background_color_1'] = sanitize_text_field( $input['background_color_1'] );
		}

		if ( isset( $input['text_color_2'] ) ) {
			$sanitary_values['text_color_2'] = sanitize_text_field( $input['text_color_2'] );
		}

		if ( isset( $input['button_text_3'] ) ) {
			$sanitary_values['button_text_3'] = sanitize_text_field( $input['button_text_3'] );
		}

		if ( isset( $input['button_visibility_4'] ) ) {
			$sanitary_values['button_visibility_4'] = $input['button_visibility_4'];
		}	

		if ( isset( $input['enter_top_offset_height_in_number_5'] ) ) {
			$sanitary_values['enter_top_offset_height_in_number_5'] = sanitize_text_field( $input['enter_top_offset_height_in_number_5'] );
		}

		if ( isset( $input['custom_css_6'] ) ) {
			$sanitary_values['custom_css_6'] = esc_textarea( $input['custom_css_6'] );
		}

		if ( isset( $input['select_both_7'] ) ) {
			$sanitary_values['select_both_7'] = $input['select_both_7'];
		}

		return $sanitary_values;
	}

	public function mobile_quick_buy_kp_section_info() {
		
	}

	public function select_button_intention_0_callback() {
		?> <select name="<?php echo MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY; ?>[select_button_intention_0]" id="select_button_intention_0">
			<?php $selected = (isset( $this->options['select_button_intention_0'] ) && $this->options['select_button_intention_0'] === 'add-to-cart') ? 'selected' : '' ; ?>
			<option value="add-to-cart" <?php echo $selected; ?>>Add to cart</option>
			<?php $selected = (isset( $this->options['select_button_intention_0'] ) && $this->options['select_button_intention_0'] === 'quick-buy') ? 'selected' : '' ; ?>
			<option value="quick-buy" <?php echo $selected; ?>>Quick buy</option>
		</select> <?php
	}

	public function background_color_1_callback() {
		printf(
			'<input class="regular-text color-picker" type="text" name="%s[background_color_1]" id="background_color_1" value="%s">',
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY,
			isset( $this->options['background_color_1'] ) ? esc_attr( $this->options['background_color_1']) : ''
		);
	}

	public function text_color_2_callback() {
		printf(
			'<input class="regular-text color-picker" type="text" name="%s[text_color_2]" id="text_color_2" value="%s">',
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY,
			isset( $this->options['text_color_2'] ) ? esc_attr( $this->options['text_color_2']) : ''
		);
	}

	public function button_text_3_callback() {
		printf(
			'<input class="regular-text" type="text" placeholder="e.g. Quick Buy" name="%s[button_text_3]" id="button_text_3" value="%s">',
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY,
			isset( $this->options['button_text_3'] ) ? esc_attr( $this->options['button_text_3']) : ''
		);
	}

	public function button_visibility_4_callback() {
		?> <select name="<?php echo MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY; ?>[button_visibility_4]" id="button_visibility_4">
			<?php $selected = (isset( $this->options['button_visibility_4'] ) && $this->options['button_visibility_4'] === 'all-time') ? 'selected' : '' ; ?>
			<option value="all-time" <?php echo $selected; ?>>All time</option>
			<?php $selected = (isset( $this->options['button_visibility_4'] ) && $this->options['button_visibility_4'] === 'on-scroll') ? 'selected' : '' ; ?>
			<option value="on-scroll" <?php echo $selected; ?>>On scroll</option>
		</select> <?php
	}

	public function enter_top_offset_height_in_number_5_callback() {
		printf(
			'<input class="regular-text" type="text" placeholder="just a number e.g 300" name="%s[enter_top_offset_height_in_number_5]" id="enter_top_offset_height_in_number_5" value="%s">',
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY,
			isset( $this->options['enter_top_offset_height_in_number_5'] ) ? esc_attr( $this->options['enter_top_offset_height_in_number_5']) : ''
		);
	}

	public function custom_css_6_callback() {
		printf(
			'<textarea class="large-text" rows="5" name="%s[custom_css_6]" id="custom_css_6">%s</textarea><div style="font-size: 12px;">Button container\'s CSS class : mobile-quick-buy-kp-sticky <br>Button\'s CSS class : mobile-quick-buy</div>',
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY,
			isset( $this->options['custom_css_6'] ) ? esc_attr( $this->options['custom_css_6']) : ''
		);
	}

	public function select_both_7_callback() {
		printf(
			'<input type="checkbox" name="%s[select_both_7]" id="select_both_7" value="select_both_7" %s>',
			MQBK_MOBILE_QUICK_BUY_KP_OPTIONS_KEY,
			( isset( $this->options['select_both_7'] ) && $this->options['select_both_7'] === 'select_both_7' ) ? 'checked' : ''
		);
	}


}