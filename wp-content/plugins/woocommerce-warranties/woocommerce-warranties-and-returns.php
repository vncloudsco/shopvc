<?php
/*
Plugin Name: Warranties and Returns for WooCommerce
Plugin URI: http://www.mihajlovicnenad.com/woocommerce-warranties-and-returns
Description: Warranties and Returns plugin for WooCommerce! - mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 3.1.3
Author URI: http://www.mihajlovicnenad.com
*/

	class WC_Warranties_And_Returns {

		public static $version;
		public static $dir;
		public static $path;
		public static $url_path;
		public static $settings;

		function __construct() {

			self::$version = '3.1.3';

			self::$dir = trailingslashit( dirname( __FILE__ ) );
			self::$path = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$url_path = plugins_url( trailingslashit( basename( self::$dir ) ) );

			add_action( 'init', array( $this, 'init_plugin' ) );

			if ( is_admin() ) {
				register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
				register_activation_hook( __FILE__, 'flush_rewrite_rules' );

				add_action( 'woocommerce_product_data_tabs', array( $this, 'wc_add_product_tab' ), 999 , 1 );
				add_action( 'woocommerce_product_data_panels', array( $this, 'wc_product_tab' ) );

				add_action( 'wcwar_warranty_pre_add_form_fields', array( &$this, 'war_add_presets' ), 10, 2 );
				add_action( 'wcwar_warranty_pre_edit_form_fields', array( &$this, 'war_edit_presets' ), 10, 2 );
				//add_filter( 'manage_edit-wcwar_warranty_pre_columns', array( &$this, 'war_preset_columns' ) );

				add_action( 'created_term', array( &$this, 'war_save_preset' ), 10, 3 );
				add_action( 'edit_term', array( &$this, 'war_save_preset' ), 10, 3 );
				add_action( 'admin_enqueue_scripts', array( &$this, 'war_admin_scripts' ) );

				add_action( 'add_meta_boxes', array( &$this, 'war_register_request_metabox' ) );
				add_action( 'save_post', array( &$this, 'war_save_request_metabox' ), 10, 2 );
				add_action( 'save_post', array( &$this, 'wc_product_save' ), 10, 3 );

				add_filter( 'woocommerce_hidden_order_itemmeta', array( &$this, 'war_hide_core_fileds' ), 10, 1 );
				add_action( 'woocommerce_admin_order_item_headers', array( &$this, 'war_items_warranty_column_header' ) );
				add_action( 'woocommerce_admin_order_item_values', array( &$this, 'war_items_warranty_column' ), 10, 3);

				add_filter( 'manage_edit-wcwar_warranty_req_columns', array( &$this, 'war_request_warranty_column_header' ) );
				add_action( 'manage_wcwar_warranty_req_posts_custom_column' , array( &$this, 'war_request_warranty_column' ), 10, 2 );

				add_action( 'pre_get_posts', array( &$this, 'hlp_request_order' ), 10, 1 );
				
				add_filter( 'woocommerce_screen_ids', array( &$this, 'wc_add_screen_ids' ), 10, 1 );

				add_filter( 'woocommerce_admin_order_data_after_order_details', array( &$this, 'wc_add_order_request_status' ), 10, 1 );

				add_action( 'wp_ajax_war_ajax_create', array( &$this, 'war_ajax_create' ) );

				add_action( 'wp_ajax_war_ajax_status', array( &$this, 'war_ajax_status' ) );
				add_action( 'wp_ajax_war_ajax_status_change', array( &$this, 'war_ajax_status_change' ) );

				add_action( 'wp_ajax_war_ajax_email_send', array( &$this, 'war_ajax_email_send' ) );

				add_action( 'wp_ajax_war_ajax_et_save', array( &$this, 'war_ajax_et_save' ) );
				add_action( 'wp_ajax_war_ajax_et_load', array( &$this, 'war_ajax_et_load' ) );
				add_action( 'wp_ajax_war_ajax_et_delete', array( &$this, 'war_ajax_et_delete' ) );

				add_action( 'admin_menu', array( &$this, 'war_pending_requests' ), 999 );

				add_action( 'admin_head', array( &$this, 'war_add_menu_icon_styles' ) );

				if ( get_option( 'wcwar_email_disable', 'no' ) == 'no' ) {
					add_action( 'woocommerce_email_after_order_table', array( &$this, 'war_email' ), 10, 3 );
				}

			}

			add_action( 'wp_enqueue_scripts', array( &$this, 'war_scripts' ) );

			add_filter( 'woocommerce_add_cart_item_data', array( &$this, 'wc_add_pa_warranty' ), 10, 3 );
			add_filter( 'woocommerce_add_cart_item', array( &$this, 'war_add_product_warranty' ), 10, 3 );

			add_filter('woocommerce_get_cart_item_from_session', array( &$this, 'wc_get_cart_item_from_session' ), 10, 3 );
			add_action( 'woocommerce_new_order_item', array( &$this, 'war_add_warranty_meta' ), 10, 3 );

			add_filter( 'woocommerce_cart_item_price', array( &$this, 'war_cart_price' ), 10, 3 ) ;
			add_action( 'woocommerce_after_cart_contents', array( &$this, 'war_cart_help' ) );

			add_filter( 'woocommerce_order_details_after_order_table', array( &$this, 'war_order' ), 999, 1 ) ;

			add_shortcode( 'wcwar_request', array( &$this, 'wcwar_sc_request' ) );

			add_filter( 'single_template', array( &$this, 'scr_view_request' ) );
			add_filter( 'comments_template', array( &$this, 'scr_comments' ) );

			$action = get_option( 'wcwar_single_action' , '' );

			if ( $action == '' ) {
				$action = 'woocommerce_after_add_to_cart_button';
			}

			if ( strpos( $action, ':' ) > 0 ) {
				$explode = explode( ':', $action );
				$curr_action = array(
					'action' => $explode[0],
					'priority' => intval( $explode[1] ) > -1 ? intval( $explode[1] ) : 10
				);
			}
			else {
				$curr_action = array(
					'action' => $action,
					'priority' => 10
				);
			}

			add_action( $curr_action['action'], array( &$this, 'war_product_warranty_output' ), $curr_action['priority'] );

			add_filter( 'mnthemes_add_meta_information_used', array( &$this, 'war_info' ) );

		}

		function war_info( $val ) {
			$val = array_merge( $val, array( 'Warranties and Returns for WooCommerce' ) );
			return $val;
		}


		function war_install() {

			if ( !get_option( 'war_settings_page' ) ) {
				$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id', '' );

				$curr_page = array(
					'post_title' => esc_html__( 'Request Warranty', 'wcwar'),
					'post_content' => '[wcwar_request]',
					'post_status' => 'publish',
					'post_type' => 'page',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_category' => array( 1 ),
					'post_parent' => $myaccount_page_id
				);

				$curr_created = wp_insert_post( $curr_page );

				update_option( 'war_settings_page', $curr_created );

			}
		}

		function init_plugin() {

			global $wpdb;

			self::$settings['wc_version'] = defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.4', '>=' ) ? '2.4' : '2.3';

			switch( self::$settings['wc_version'] ) {
				case '2.3' :
					add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'wc_add_variable_tab' ), 10, 3 );
				break;
				case '2.4' :
				default :
					add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'wc_add_variable_tab_24' ), 10, 3 );
					add_action( 'woocommerce_save_product_variation', array( &$this, 'wc_product_save_24' ), 10, 2 );
				break;
			}

			$curr_args = array(
				'hierarchical'          => false,
				'update_count_callback' => '_update_post_term_count',
				'labels' => array(
					'name'              => esc_html__( 'Warranty Presets', 'wcwar'),
					'singular_name'     => esc_html__( 'Warranty Preset', 'wcwar'),
					'search_items'      => esc_html__( 'Search Warranty Presets', 'wcwar'),
					'all_items'         => esc_html__( 'All Warranty Presets', 'wcwar'),
					'parent_item'       => esc_html__( 'Parent Warranty Preset', 'wcwar'),
					'parent_item_colon' => esc_html__( 'Parent Warranty Preset:', 'wcwar'),
					'edit_item'         => esc_html__( 'Edit Warranty Preset', 'wcwar'),
					'update_item'       => esc_html__( 'Update Warranty Preset', 'wcwar'),
					'add_new_item'      => esc_html__( 'Add New Warranty Preset', 'wcwar'),
					'new_item_name'     => esc_html__( 'New Warranty Name Preset', 'wcwar')
				),
				'show_ui'               => true,
				'show_in_nav_menus'     => true,
				'query_var'             => true,
				'rewrite'               => false,
			);

			register_taxonomy( 'wcwar_warranty_pre', array( 'product' ), $curr_args );

			$curr_args = array(
				'hierarchical'          => false,
				'update_count_callback' => '_update_post_term_count',
				'labels' => array(
					'name'              => esc_html__( 'Warranty Status', 'wcwar' ),
					'singular_name'     => esc_html__( 'Warranty Status', 'wcwar' ),
					'search_items'      => esc_html__( 'Search Warranty', 'wcwar' ),
					'all_items'         => esc_html__( 'All Warranty', 'wcwar' ),
					'parent_item'       => esc_html__( 'Parent Warranty', 'wcwar' ),
					'parent_item_colon' => esc_html__( 'Parent Warranty:', 'wcwar' ),
					'edit_item'         => esc_html__( 'Edit Warranty', 'wcwar' ),
					'update_item'       => esc_html__( 'Update Warranty', 'wcwar' ),
					'add_new_item'      => esc_html__( 'Add New Warranty', 'wcwar' ),
					'new_item_name'     => esc_html__( 'New Warranty Name', 'wcwar' )
				),
				'show_ui'               => false,
				'show_in_nav_menus'     => true,
				'query_var'             => true,
				'rewrite'               => false,
				'show_in_nav_menus'     => false,
				'show_in_rest'          => false,
				'show_tagcloud'         => false,
				'show_in_quick_edit'    => false,
			);

			register_taxonomy( 'wcwar_warranty', array( 'wcwar_warranty_req' ), $curr_args );

			$curr_args = array(
				'label'                 => esc_html__( 'Warranties and Returns', 'wcwar' ),
				'labels'                => array(
					'name'              => esc_html__( 'Warranty and Returns Requests', 'wcwar' ),
					'singular_name'     => esc_html__( 'Warranty and Return Request', 'wcwar' ),
					'all_items'         => esc_html__( 'All Requests', 'wcwar' ),
					'menu_name'         => esc_html__( 'Warranties and Returns', 'wcwar' ),
					'not_found'         => esc_html__( 'No requests found', 'wcwar' ),
					'edit_item'         => esc_html__( 'Edit Request', 'wcwar' ),
					'add_new_item'      => esc_html__( 'Create a Request', 'wcwar' ),
					'new_item_name'     => esc_html__( 'Create a Request', 'wcwar' ),
					'parent_item_colon' => esc_html__( 'Parent Request', 'wcwar' ),
					'view_item'         => esc_html__( 'View Request', 'wcwar' ),
					'search_items'      => esc_html__( 'Search Requests', 'wcwar' )
				),
				'public'                => true,
				'exclude_from_search'   => true,
				'publicly_queryable'    => true,
				'show_ui'               => true,
				'capability_type'       => 'post',
				'capabilities' => array(
					'create_posts' => true,
				),
				'map_meta_cap' => true,
				'hierarchical'          => true,
				'show_in_nav_menus'     => true,
				'menu_position'         => 56,
				'supports'              => array( 'title', 'editor', 'comments' ),
				'has_archive'           => false
			);

			register_post_type( 'wcwar_warranty_req', $curr_args );

			$curr_status = get_terms( 'wcwar_warranty', array( 'hide_empty' => false ) );

			if ( empty( $curr_status) ) {

				$curr_warranty_status = array(
					'new' => esc_html__( 'New', 'wc_warranty' ),
					'processing' => esc_html__( 'Processing', 'wc_warranty' ),
					'completed' => esc_html__( 'Completed', 'wc_warranty' ),
					'rejected' => esc_html__( 'Rejected', 'wc_warranty' )
				);

				$default_slugs = array();

				foreach ( $curr_warranty_status as $k =>$v ) {
					if ( !get_term_by( 'slug', $k, 'wcwar_warranty' ) ) {

						wp_insert_term(
							$v,
							'wcwar_warranty',
							array(
								'slug' => $k
							)
						);

					}
				}

			}

			$domain = 'wcwar';
			$dir = untrailingslashit( WP_LANG_DIR );
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			}
			else {
				load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
			}

		}

		function wc_add_product_tab( $product_data_tabs ) {

			$product_data_tabs['wcwar_tab'] = array(
				'label' => esc_html__( 'Warranties and Returns', 'wcwar'),
				'target' => 'wcwar_tab',
				'class' => 'wcwar_tab hide_if_external hide_if_variable'
			);
			return $product_data_tabs;

		}

		function wc_product_tab() {
			global $post;

			$curr_warranty = get_post_meta( $post->ID, '_wcwar_warranty', true );

			if ( empty( $curr_warranty ) ) {
				if ( ( $default_warranty = get_option( 'wcwar_default_warranty', '' ) ) == '' ) {
					$curr_warranty = array( 'type' => 'no_warranty' );
				}
				else {
					$curr_warranty = array(
						'type' => 'preset_warranty',
						'preset' => $default_warranty
					);
				}
			}


			$inline = '

				$(document).on("woocommerce_variations_loaded", "body", function() {

					$(".wcwar_metaboxes.paid_warranty_items").sortable({handle:"a.move_paid_warranty"});

				});

				$(".wcwar_metaboxes.paid_warranty_items").sortable({handle:"a.move_paid_warranty"});

				var wcwar_metabox = "<div class=\"wcwar_metabox\">\
					<a href=\"#\" class=\"move_paid_warranty\"><i class=\"wcwar-move\"></i></a>\
					<a href=\"#\" class=\"remove_paid_warranty\"><i class=\"wcwar-close\"></i></a>\
					<div class=\"options_group grouping\" data-group=\"included_warranty\">\
						<p class=\"form-field wcwar_qp_price\">\
							<label for=\"wcwar_qp_price\">' . esc_html__( 'Additional warranty price', 'wcwar') . '</label>\
							<input type=\"number\" class=\"option short wc_input_price\" name=\"wcwar_qp_price_single[]\" value=\"\" placeholder=\"\" step=\"any\">\
							<em>' . esc_html__( 'Enter additional price for this warranty.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_type\">\
							<label for=\"wcwar_qp_type\">' . esc_html__( 'Warranty period type', 'wcwar') . '</label>\
							<select name=\"wcwar_qp_type_single[]\" class=\"option select short\">\
								<option value=\"\">' . esc_html__( 'Not selected', 'wcwar') . '</option>\
								<option value=\"days\">' . esc_html__( 'Days', 'wcwar') . '</option>\
								<option value=\"weeks\">' . esc_html__( 'Weeks', 'wcwar') . '</option>\
								<option value=\"months\">' . esc_html__( 'Months', 'wcwar') . '</option>\
								<option value=\"years\">' . esc_html__( 'Years', 'wcwar') . '</option>\
								<option value=\"lifetime\">' . esc_html__( 'Lifetime', 'wcwar') . '</option>\
							</select>\
							<em>' . esc_html__( 'Select warranty period type.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_period\">\
							<label for=\"wcwar_qp_period\">' . esc_html__( 'Warranty period', 'wcwar') . '</label>\
							<input type=\"number\" class=\"option short\" name=\"wcwar_qp_period_single[]\" value=\"\" placeholder=\"\" step=\"any\">\
							<em>' . esc_html__( 'Enter warranty period.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_desc\">\
							<label for=\"wcwar_qp_desc\">' . esc_html__( 'Warranty description (optional)', 'wcwar') . '</label>\
							<textarea class=\"option short\" name=\"wcwar_qp_desc_single[]\" placeholder=\"\" step=\"any\"></textarea>\
							<em>' . esc_html__( 'Enter warranty description.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_thumb\">\
							<span class=\"thumb_preview\"></span>\
							<label for=\"wcwar_qp_thumb\">' . esc_html__( 'Warranty thumbnail (optional)', 'wcwar') . '</label>\
							<input type=\"hidden\" class=\"option short\" name=\"wcwar_qp_thumb_single[]\" value=\"\" placeholder=\"\" step=\"any\">\
							<button type=\"button\" class=\"option button add_wcwar_qp_thumb\">' . esc_html__( 'Add warranty thumbnail', 'wcwar') . '</button>\
						</p>\
					</div>\
				</div>";

				var wcwar_metabox_variable = "<div class=\"wcwar_metabox\">\
					<a href=\"#\" class=\"move_paid_warranty\"><i class=\"wcwar-move\"></i></a>\
					<a href=\"#\" class=\"remove_paid_warranty\"><i class=\"wcwar-close\"></i></a>\
					<div class=\"options_group grouping\" data-group=\"included_warranty\">\
						<p class=\"form-field wcwar_qp_price\">\
							<label for=\"wcwar_qp_price\">' . esc_html__( 'Additional warranty price', 'wcwar') . '</label>\
							<input type=\"number\" class=\"option short wc_input_price\" name=\"wcwar_qp_price[%][]\" value=\"\" placeholder=\"\" step=\"any\">\
							<em>' . esc_html__( 'Enter additional price for this warranty.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_type\">\
							<label for=\"wcwar_qp_type\">' . esc_html__( 'Warranty period type', 'wcwar') . '</label>\
							<select name=\"wcwar_qp_type[%][]\" class=\"option select short\">\
								<option value=\"\">' . esc_html__( 'Not selected', 'wcwar') . '</option>\
								<option value=\"days\">' . esc_html__( 'Days', 'wcwar') . '</option>\
								<option value=\"weeks\">' . esc_html__( 'Weeks', 'wcwar') . '</option>\
								<option value=\"months\">' . esc_html__( 'Months', 'wcwar') . '</option>\
								<option value=\"years\">' . esc_html__( 'Years', 'wcwar') . '</option>\
								<option value=\"lifetime\">' . esc_html__( 'Lifetime', 'wcwar') . '</option>\
							</select>\
							<em>' . esc_html__( 'Select warranty period type.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_period\">\
							<label for=\"wcwar_qp_period\">' . esc_html__( 'Warranty period', 'wcwar') . '</label>\
							<input type=\"number\" class=\"option short\" name=\"wcwar_qp_period[%][]\" value=\"\" placeholder=\"\" step=\"any\">\
							<em>' . esc_html__( 'Enter warranty period.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_desc\">\
							<label for=\"wcwar_qp_desc\">' . esc_html__( 'Warranty description (optional)', 'wcwar') . '</label>\
							<textarea class=\"option short\" name=\"wcwar_qp_desc[%][]\" placeholder=\"\" step=\"any\"></textarea>\
							<em>' . esc_html__( 'Enter warranty description.', 'wcwar' ) . '</em>\
						</p>\
						<p class=\"form-field wcwar_qp_thumb\">\
							<span class=\"thumb_preview\"></span>\
							<label for=\"wcwar_qp_thumb\">' . esc_html__( 'Warranty thumbnail (optional)', 'wcwar') . '</label>\
							<input type=\"hidden\" class=\"option short\" name=\"wcwar_qp_thumb[%][]\" value=\"\" placeholder=\"\" step=\"any\">\
							<button type=\"button\" class=\"option button add_wcwar_qp_thumb\">' . esc_html__( 'Add warranty thumbnail', 'wcwar') . '</button>\
						</p>\
					</div>\
				</div>";

				$(document).on("change", "#wcwar_type", function() {

					var curr = $("#wcwar_tab");
					curr.find(".options_group:not(.basic) .option").attr("disabled", "disabled").closest(".options_group").hide();

					if ( $(this).val() != "no_warranty") {
						curr.find(".options_group[data-group="+$(this).val()+"] .option").removeAttr("disabled").closest(".options_group").show();
						if ( $(this).val() == "preset_warranty" ) {
							curr.find(".paid_warranty_items .wcwar_metabox").remove();
						}
					}
					else {
						curr.find(".paid_warranty_items .wcwar_metabox").remove();
					}

				});

				$(document).on("change", ".wcwar_type", function() {

					if ( $(this).val() == "" ) {
						return;
					}

					var curr = $(this).closest(".wcwar_tab");
					curr.find(".options_group:not(.basic) .option").attr("disabled", "disabled").closest(".options_group").hide();

					if ( $(this).val() != "no_warranty") {
						curr.find(".options_group[data-group="+$(this).val()+"] .option").removeAttr("disabled").closest(".options_group").show();
						if ( $(this).val() == "preset_warranty" ) {
							curr.find(".paid_warranty_items .wcwar_metabox").remove();
						}
					}
					else {
						curr.find(".paid_warranty_items .wcwar_metabox").remove();
					}

				});

				$(document).on("change", "#wcwar_q_type", function() {

					var curr = $("#wcwar_tab");
					curr.find(".options_group:not(.basic):not(.preset):not(.type) .option").attr("disabled", "disabled").closest(".options_group").hide();

					if ( $(this).val() != "") {
						curr.find(".options_group[data-group="+$(this).val()+"] .option").removeAttr("disabled").closest(".options_group").show();
						if ( $(this).val() == "included_warranty" ) {
							curr.find(".paid_warranty_items .wcwar_metabox").remove();
						}
					}
					else {
						curr.find(".paid_warranty_items .wcwar_metabox").remove();
					}

				});

				$(document).on("change", ".wcwar_q_type", function() {
					if ( $(this).val() == "" ) {
						return;
					}

					var curr = $(this).closest(".wcwar_tab");
					curr.find(".options_group:not(.basic):not(.preset):not(.type) .option").attr("disabled", "disabled").closest(".options_group").hide();

					if ( $(this).val() != "") {
						curr.find(".options_group[data-group="+$(this).val()+"] .option").removeAttr("disabled").closest(".options_group").show();
						if ( $(this).val() == "included_warranty" ) {
							curr.find(".paid_warranty_items .wcwar_metabox").remove();
						}
					}
					else {
						curr.find(".paid_warranty_items .wcwar_metabox").remove();
					}

				});

				
				$(document).on("click", ".add_paid_warranty", function () {
					if ( $("#product-type").val() == "variable" ) {
						var curr = $(this).closest(".woocommerce_variation ").index();
						$(this).parent().prev().append(wcwar_metabox_variable.replace(/%/g, curr));
					}
					else {
						$(this).parent().prev().append(wcwar_metabox);
					}
					return false;
				});

				$(document).on("click", ".remove_paid_warranty", function () {
					$(this).parent().remove();
					return false;
				});

				$(document).on("click", ".add_wcwar_qi_thumb, .add_wcwar_qp_thumb", function () {

					var frame;
					var el = $(this);
					var curr = el.parent();

					if ( frame ) {
						frame.open();
						return;
					}

					frame = wp.media({
						title: el.data("choose"),
						button: {
							text: el.data("update"),
							close: false
						}
					});

					frame.on( "select", function() {

						var attachment = frame.state().get("selection").first();
						frame.close();
						curr.find("input:hidden").val(attachment.attributes.url);
						if ( attachment.attributes.type == "image" ) {
							curr.find(".thumb_preview").empty().hide().append("<img width=\"64\" height=\"auto\" src=\""+attachment.attributes.url+"\">").slideDown("fast");
						}

					});

					frame.open();

					return false;
				});


			';

			if ( function_exists('wc_enqueue_js') ) {
				wc_enqueue_js( $inline );
			} else {
				$woocommerce->add_inline_js( $inline );
			}

		?>
		<div id="wcwar_tab" class="panel woocommerce_options_panel">

			<div class="options_group grouping basic">
				<p class="form-field wcwar_type">
					<label for="wcwar_type"><?php esc_html_e( 'Select warranty type', 'wcwar' ); ?></label>
					<select id="wcwar_type" name="wcwar_type_single" class="option select short">
						<option value="no_warranty" <?php if ( $curr_warranty['type'] == 'no_warranty') echo 'selected'; ?>><?php esc_html_e( 'No warranty', 'wcwar' ); ?></option>
						<option value="preset_warranty" <?php if ( $curr_warranty['type'] == 'preset_warranty') echo 'selected'; ?>><?php esc_html_e( 'Preset warranty', 'wcwar' ); ?></option>
						<option value="quick_warranty" <?php if ( $curr_warranty['type'] == 'quick_warranty') echo 'selected'; ?>><?php esc_html_e( 'Quick warranty', 'wcwar' ); ?></option>
					</select>
					<em><?php esc_html_e( 'Select preset warranty or add quick product warranty.', 'wcwar' ); ?></em>
				</p>
			</div>

			<?php
				$curr_disable = ( $curr_warranty['type'] == 'preset_warranty' ? '' : ' disabled="disabled"' );
			?>
			<div class="options_group grouping preset" data-group="preset_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<p class="form-field wcwar_preset">
					<label for="wcwar_preset"><?php esc_html_e( 'Select warranty preset', 'wcwar' ); ?></label>
					<select id="wcwar_preset" name="wcwar_preset_single" class="option select short"<?php echo $curr_disable; ?>>
						<option value="" <?php if (isset( $curr_warranty['preset'] ) && $curr_warranty['preset'] == '') echo 'selected'; ?>><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
					<?php
						$presets = get_terms( 'wcwar_warranty_pre', array('hide_empty' => false) );

						foreach ( $presets as $preset ) {
					?>
						<option value="<?php echo $preset->term_id; ?>" <?php if (isset( $curr_warranty['preset'] ) && $curr_warranty['preset'] == $preset->term_id) echo 'selected'; ?>><?php echo $preset->name; ?></option>
					<?php
						}
					?>
					</select>
					<em><?php esc_html_e( 'Select warranty preset to use with the current product. Warranty presets can be set in Products &gt; Warranty Presets.', 'wcwar' ); ?></em>
				</p>
			</div>

			<?php
				$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' ? '' : ' disabled="disabled"' );
			?>
			<div class="options_group grouping type" data-group="quick_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<p class="form-field wcwar_q_type">
					<label for="wcwar_q_type"><?php esc_html_e( 'Select quick warranty type', 'wcwar' ); ?></label>
					<select id="wcwar_q_type" name="wcwar_q_type_single" class="option select short"<?php echo $curr_disable; ?>>
						<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
						<option value="included_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty') echo 'selected'; ?>><?php esc_html_e( 'Included warranty', 'wcwar' ); ?></option>
						<option value="paid_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty') echo 'selected'; ?>><?php esc_html_e( 'Paid warranty', 'wcwar' ); ?></option>
					</select>
					<em><?php esc_html_e( 'Warranties can be included or paid as an add-on. Choose your warranty type.', 'wcwar' ); ?></em>
				</p>
			</div>

			<?php
				$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty' ? '' : ' disabled="disabled"' );
			?>
			<div class="options_group grouping" data-group="included_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<p class="form-field wcwar_qi_type">
					<label for="wcwar_qi_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
					<select id="wcwar_qi_type" name="wcwar_qi_type_single" class="option select short"<?php echo $curr_disable; ?>>
						<?php
							$curr_options = ( isset( $curr_warranty['included_warranty'] ) ? $curr_warranty['included_warranty'] : array() );
						?>
						<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
						<option value="days" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
						<option value="weeks" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
						<option value="months" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
						<option value="years" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
						<option value="lifetime" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
					</select>
					<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
				</p>
				<p class="form-field wcwar_qi_period">
					<label for="wcwar_qi_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
					<input type="number" class="option short" name="wcwar_qi_period_single" id="wcwar_qi_period" value="<?php if (isset( $curr_options['period'] ) && $curr_options['period'] !== '') echo $curr_options['period']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
					<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
				</p>
				<p class="form-field wcwar_qi_desc">
					<label for="wcwar_qi_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
					<textarea class="option short" name="wcwar_qi_desc_single" id="wcwar_qi_desc" placeholder="" <?php echo $curr_disable; ?>><?php if (isset( $curr_options['desc'] ) && $curr_options['desc'] !== '') echo $curr_options['desc']; ?></textarea>
					<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
				</p>
				<p class="form-field wcwar_qi_thumb">
					<span class="thumb_preview"><?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo '<img width="64" height="auto" src="' . $curr_options['thumb'] . '" alt="' .esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
					<label for="wcwar_qi_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
					<input type="hidden" class="option short" name="wcwar_qi_thumb_single" id="wcwar_qi_thumb" value="<?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo $curr_options['thumb']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
					<button type="button" class="option button add_wcwar_qi_thumb"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
				</p>
			</div>

			<?php
				$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty' ? '' : ' disabled="disabled"' );
			?>
			<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<p class="form-field wcwar_qp_without_single">
					<label for="wcwar_qp_without_single"><?php esc_html_e( 'Add <em>No Warranty</em> option', 'wcwar' ); ?></label>
					<input type="checkbox" name="wcwar_qp_without_single" id="wcwar_qp_without_single" value="yes" <?php if (isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes') echo 'checked'; ?> class="option checkbox"<?php echo $curr_disable; ?> />
					<em><?php esc_html_e( 'To enable product purchases without a paid warranty check this option.', 'wcwar' ); ?></em>
				</p>
			</div>

			<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<div class="paid_warranty_items wcwar_metaboxes">
				<?php
					if ( isset( $curr_warranty['paid_warranty'] ) ) {

						foreach ( $curr_warranty['paid_warranty'] as $warranty ){ 

					?>
						<div class="wcwar_metabox">
							<a href="#" class="move_paid_warranty"><i class="wcwar-move"></i></a>
							<a href="#" class="remove_paid_warranty"><i class="wcwar-close"></i></a>
							<div class="options_group grouping" data-group="included_warranty">
								<p class="form-field wcwar_qp_price">
									<label for="wcwar_qp_price"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>
									<input type="number" class="option short wc_input_price" name="wcwar_qp_price_single[]" value="<?php if (isset( $warranty['price'] ) ) echo $warranty['price']; ?>" placeholder="" step="any">
									<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_type">
									<label for="wcwar_qp_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
									<select name="wcwar_qp_type_single[]" class="option select short">
										<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
										<option value="days" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
										<option value="weeks" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
										<option value="months" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
										<option value="years" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
										<option value="lifetime" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
									</select>
									<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_period">
									<label for="wcwar_qp_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
									<input type="number" class="option short" name="wcwar_qp_period_single[]" value="<?php if (isset( $warranty['period'] ) && $warranty['period'] !== '') echo $warranty['period']; ?>" placeholder="" step="any">
									<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_desc">
									<label for="wcwar_qp_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
									<textarea class="option short" name="wcwar_qp_desc_single[]" placeholder="" step="any"><?php if (isset( $warranty['desc'] ) && $warranty['desc'] !== '') echo $warranty['desc']; ?></textarea>
									<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_thumb">
									<span class="thumb_preview"><?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo '<img width="64" height="auto" src="' . $warranty['thumb'] . '" alt="' . esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
									<label for="wcwar_qp_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
									<input type="hidden" class="option short" name="wcwar_qp_thumb_single[]" value="<?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo $warranty['thumb']; ?>" placeholder="" step="any">
									<button type="button" class="option button add_wcwar_qp_thumb"><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
								</p>
							</div>
						</div>
					<?php
						}
					}
				?>
				</div>
				<p class="toolbar">
					<button type="button" class="option button button-primary add_paid_warranty"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add paid warranty', 'wcwar' ); ?></button>
				</p>
			</div>

		</div>
		<?php

		}

		function wc_add_variable_tab_24( $loop, $data, $variation ) {

			$curr_warranty = get_post_meta( $variation->ID, '_wcwar_warranty' );
			$curr_warranty = is_array( $curr_warranty ) && !empty( $curr_warranty ) ? maybe_unserialize( $curr_warranty[0] ) : array();

			if ( empty( $curr_warranty ) ) {
				if ( ( $default_warranty = get_option( 'wcwar_default_warranty', '' ) ) == '' ) {
					$curr_warranty = array( 'type' => 'no_warranty' );
				}
				else {
					$curr_warranty = array(
						'type' => 'preset_warranty',
						'preset' => $default_warranty
					);
				}
			}

		?>
			<div class="wcwar_tab">
				<div class="options_group basic">
			<?php
				woocommerce_wp_select(
					array(
						'id'          => 'wcwar_type[' . $loop . ']',
						'label'       => esc_html__( 'Select warranty type', 'wcwar' ),
						'description' => esc_html__( 'Select preset warranty or add quick product warranty.', 'wcwar' ),
						'value'       => $curr_warranty['type'],
						'class'       => 'wcwar_type option select short',
						'wrapper_class' => 'form-row form-row-first',
						'options' => array(
							'no_warranty'   => esc_html__( 'No warranty', 'wcwar' ),
							'preset_warranty'   => esc_html__( 'Preset warranty', 'wcwar' ),
							'quick_warranty' => esc_html__( 'Quick warranty', 'wcwar' )
						)
					)
				);
			?>
				</div>
			<?php

				$curr_disable = $curr_warranty['type'] == 'preset_warranty' ? array() : array( 'disabled' => 'disabled' );
				$presets = get_terms( 'wcwar_warranty_pre', array('hide_empty' => false) );
				$ready_presets = array(
					'' => esc_html__( 'Not selected', 'wcwar' )
				);

				foreach ( $presets as $preset ) {
					$ready_presets[$preset->term_id] = $preset->name;
				}
			?>
				<div class="options_group grouping preset" data-group="preset_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
			<?php
				woocommerce_wp_select(
					array(
						'id'          => 'wcwar_preset[' . $loop . ']',
						'label'       => esc_html__( 'Select warranty preset', 'wcwar' ),
						'description' => esc_html__( 'Select warranty preset to use with the current product. Warranty presets can be set in Products &gt; Warranty Presets.', 'wcwar' ),
						'value'       => isset( $curr_warranty['preset'] ) && $curr_warranty['preset'] !== '' ? $curr_warranty['preset'] : '',
						'class'       => 'wcwar_preset option select short',
						'wrapper_class' => 'form-row form-row-last',
						'custom_attributes' => $curr_disable,
						'options' => $ready_presets
					)
				);
			?>
				</div>
			<?php

				$curr_disable = $curr_warranty['type'] == 'quick_warranty' ? array() : array( 'disabled' => 'disabled' );
			?>
				<div class="options_group grouping type" data-group="quick_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
			<?php
				woocommerce_wp_select(
					array(
						'id'          => 'wcwar_q_type[' . $loop . ']',
						'label'       => esc_html__( 'Select quick warranty type', 'wcwar' ),
						'description' => esc_html__( 'Warranties can be included or paid as an add-on. Choose your warranty type.', 'wcwar' ),
						'value'       => isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ? $curr_warranty['quick'] : '',
						'class'       => 'wcwar_q_type option select short',
						'wrapper_class' => 'form-row form-row-last',
						'custom_attributes' => $curr_disable,
						'options' => array(
							'' => esc_html__( 'Not selected', 'wcwar' ),
							'included_warranty' => esc_html__( 'Included warranty', 'wcwar' ),
							'paid_warranty' => esc_html__( 'Paid warranty', 'wcwar' )
						)
					)
				);
			?>
				</div>
			<?php

				$curr_disable = $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty' ? array() : array( 'disabled' => 'disabled' );
				$curr_options = isset( $curr_warranty['included_warranty'] ) ? $curr_warranty['included_warranty'] : array();
			?>
				<div class="options_group grouping" data-group="included_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
			<?php
				woocommerce_wp_select(
					array(
						'id'          => 'wcwar_qi_type[' . $loop . ']',
						'label'       => esc_html__( 'Warranty period type', 'wcwar' ),
						'description' => esc_html__( 'Select warranty period type.', 'wcwar' ),
						'value'       => isset( $curr_options['type'] ) && $curr_options['type'] !== '' ? $curr_options['type'] : '',
						'class'       => 'wcwar_qi_type option select short',
						'wrapper_class' => 'form-row form-row-first',
						'custom_attributes' => $curr_disable,
						'options' => array(
							'' => esc_html__( 'Not selected', 'wcwar' ),
							'days' => esc_html__( 'Days', 'wcwar' ),
							'weeks' => esc_html__( 'Weeks', 'wcwar' ),
							'months' => esc_html__( 'Months', 'wcwar' ),
							'years' => esc_html__( 'Years', 'wcwar' ),
							'lifetime' => esc_html__( 'Lifetime', 'wcwar' )
						)
					)
				);

				woocommerce_wp_text_input(
					array( 
						'id'          => 'wcwar_qi_period[' .  $loop . ']',
						'label'       => esc_html__( 'Warranty period', 'wcwar' ),
						'description' => esc_html__( 'Enter warranty period.', 'wcwar' ),
						'class'       => 'wcwar_qi_period option short',
						'wrapper_class' => 'form-row form-row-last',
						'value'       => isset( $curr_options['period'] ) && $curr_options['period'] !== '' ? $curr_options['period'] : '',
						'custom_attributes' => array_merge( array( 'step' => 'any', 'min' => '0' ), $curr_disable )
					)
				);

				woocommerce_wp_textarea_input(
					array( 
						'id'          => 'wcwar_qi_desc[' .  $loop . ']',
						'label'       => esc_html__( 'Warranty description (optional)', 'wcwar' ),
						'placeholder' => '',
						'description' => esc_html__( 'Enter warranty description.', 'wcwar' ),
						'class'       => 'wcwar_qi_desc option short',
						'wrapper_class' => 'form-row form-row-first',
						'custom_attributes' => $curr_disable,
						'value'       => isset( $curr_options['desc'] ) && $curr_options['desc'] !== '' ? $curr_options['desc'] : ''
					)
				);

			?>
				<span class="thumb_preview"><?php echo isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '' ? '<img width="64" height="auto" src="' . $curr_options['thumb'] . '" alt="' . esc_attr( esc_html__( 'Thumbnail preview', 'wcwar' ) ) . '" />' : '<img width="64" height="auto" src="' . self::$url_path . 'assets/images/no_image.gif" alt="' . esc_attr( esc_html__( 'No image', 'wcwar' ) ) . '" />'; ?></span>
				<button type="button" class="option button add_wcwar_qi_thumb"<?php echo !empty( $curr_disable ) ? ' disabled="disabled"' : ''; ?>><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
			<?php

				woocommerce_wp_hidden_input(
					array(
						'id'          => 'wcwar_qi_thumb[' .  $loop . ']',
						'label'       => esc_html__( 'Warranty thumbnail (optional)', 'wcwar' ),
						'placeholder' => '',
						'description' => esc_html__( 'Add a warranty thumbnail.', 'wcwar' ),
						'class'       => 'wcwar_qi_thumb option short',
						'wrapper_class' => 'form-row form-row-last',
						'custom_attributes' => $curr_disable,
						'value'       => isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '' ? $curr_options['thumb'] : ''
					)
				);
			?>
				</div>
			<?php

				$curr_disable = $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty' ? array() : array( 'disabled' => 'disabled' );
			?>
				<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<?php
					woocommerce_wp_checkbox(
						array(
							'id'            => 'wcwar_qp_without[' .  $loop . ']',
							'label'         => esc_html__( 'Add <em>No Warranty</em> option', 'wcwar' ),
							'description'   => esc_html__( 'To enable product purchases without a paid warranty check this option.', 'wcwar' ),
							'class'         => 'wcwar_qp_without option short',
							'wrapper_class' => 'form-row form-row-full',
							'custom_attributes' => $curr_disable,
							'value'         => isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes' ? 'yes' : 'no'
						)
					);

				?>
					<div class="paid_warranty_items wcwar_metaboxes">
					<?php
						if ( isset( $curr_warranty['paid_warranty'] ) ) {

							foreach ( $curr_warranty['paid_warranty'] as $warranty ){
							?>
								<div class="wcwar_metabox">
									<a href="#" class="move_paid_warranty"><i class="wcwar-move"></i></a>
									<a href="#" class="remove_paid_warranty"><i class="wcwar-close"></i></a>
									<div class="options_group grouping" data-group="included_warranty">
										<p class="form-field wcwar_qp_price">
											<label for="wcwar_qp_price"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>
											<input type="number" class="option short wc_input_price" name="wcwar_qp_price[<?php echo $loop; ?>][]" value="<?php if (isset( $warranty['price'] ) ) echo $warranty['price']; ?>" placeholder="" step="any">
											<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_type">
											<label for="wcwar_qp_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
											<select name="wcwar_qp_type[<?php echo $loop; ?>][]" class="option select short">
												<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
												<option value="days" <?php if ( isset( $warranty['type'] ) && $warranty['type'] == 'days' ) echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
												<option value="weeks" <?php if ( isset( $warranty['type'] ) && $warranty['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
												<option value="months" <?php if ( isset( $warranty['type'] ) && $warranty['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
												<option value="years" <?php if ( isset( $warranty['type'] ) && $warranty['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
												<option value="lifetime" <?php if ( isset( $warranty['type'] ) && $warranty['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
											</select>
											<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_period">
											<label for="wcwar_qp_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
											<input type="number" class="option short" name="wcwar_qp_period[<?php echo $loop; ?>][]" value="<?php if (isset( $warranty['period'] ) && $warranty['period'] !== '') echo $warranty['period']; ?>" placeholder="" step="any">
											<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_desc">
											<label for="wcwar_qp_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
											<textarea class="option short" name="wcwar_qp_desc[<?php echo $loop; ?>][]" placeholder="" step="any"><?php if (isset( $warranty['desc'] ) && $warranty['desc'] !== '') echo $warranty['desc']; ?></textarea>
											<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_thumb">
											<span class="thumb_preview"><?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo '<img width="64" height="auto" src="' . $warranty['thumb'] . '" alt="' . esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
											<label for="wcwar_qp_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
											<input type="hidden" class="option short" name="wcwar_qp_thumb[<?php echo $loop; ?>][]" value="<?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo $warranty['thumb']; ?>" placeholder="" step="any">
											<button type="button" class="option button add_wcwar_qp_thumb"><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
										</p>
									</div>
								</div>
							<?php
							}
						}
					?>
					</div>
					<p class="toolbar">
						<button type="button" class="option button button-primary add_paid_warranty"<?php echo !empty( $curr_disable ) ? ' disabled="disabled"' : ''; ?>><?php esc_html_e( 'Add paid warranty', 'wcwar' ); ?></button>
					</p>
				</div>
			</div>
		<?php

		}

		function wc_add_variable_tab( $loop, $data, $variation ) {

			$curr_warranty = ( isset( $data['_wcwar_warranty'] ) ) ? maybe_unserialize( $data['_wcwar_warranty'][0] ) : array();

			if ( empty( $curr_warranty ) ) {
				if ( ( $default_warranty = get_option( 'wcwar_default_warranty', '' ) ) == '' ) {
					$curr_warranty = array( 'type' => 'no_warranty' );
				}
				else {
					$curr_warranty = array(
						'type' => 'preset_warranty',
						'preset' => $default_warranty
					);
				}
			}

		?>
			<tr class="panel woocommerce_variation_panel wcwar_tab">

				<td class="options_group grouping basic">
					<h3><?php esc_html_e( 'Item Warranties', 'wcwar' ); ?></h3>
					<p class="form-field wcwar_type">
						<label for="wcwar_type"><?php esc_html_e( 'Select warranty type', 'wcwar' ); ?></label>
						<select name="wcwar_type[<?php echo $loop; ?>]" class="wcwar_type option select short">
							<option value="no_warranty" <?php if ( $curr_warranty['type'] == 'no_warranty') echo 'selected'; ?>><?php esc_html_e( 'No warranty', 'wcwar' ); ?></option>
							<option value="preset_warranty" <?php if ( $curr_warranty['type'] == 'preset_warranty') echo 'selected'; ?>><?php esc_html_e( 'Preset warranty', 'wcwar' ); ?></option>
							<option value="quick_warranty" <?php if ( $curr_warranty['type'] == 'quick_warranty') echo 'selected'; ?>><?php esc_html_e( 'Quick warranty', 'wcwar' ); ?></option>
						</select>
						<em><?php esc_html_e( 'Select preset warranty or add quick product warranty.', 'wcwar' ); ?></em>
					</p>
				</td>

				<?php
					$curr_disable = ( $curr_warranty['type'] == 'preset_warranty' ? '' : ' disabled="disabled"' );
				?>
				<td class="options_group grouping preset" data-group="preset_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
					<p class="form-field wcwar_preset">
						<label for="wcwar_preset"><?php esc_html_e( 'Select warranty preset', 'wcwar' ); ?></label>
						<select name="wcwar_preset[<?php echo $loop; ?>]" class="wcwar_preset option select short"<?php echo $curr_disable; ?>>
							<option value="" <?php if (isset( $curr_warranty['preset'] ) && $curr_warranty['preset'] == '') echo 'selected'; ?>><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
						<?php
							$presets = get_terms( 'wcwar_warranty_pre', array('hide_empty' => false) );

							foreach ( $presets as $preset ) {
						?>
							<option value="<?php echo $preset->term_id; ?>" <?php if (isset( $curr_warranty['preset'] ) && $curr_warranty['preset'] == $preset->term_id) echo 'selected'; ?>><?php echo $preset->name; ?></option>
						<?php
							}
						?>
						</select>
						<em><?php esc_html_e( 'Select warranty preset to use with the current product. Warranty presets can be set in Products &gt; Warranty Presets.', 'wcwar' ); ?></em>
					</p>
				</td>

				<?php
					$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' ? '' : ' disabled="disabled"' );
				?>
				<td class="options_group grouping type" data-group="quick_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
					<p class="form-field wcwar_q_type">
						<label for="wcwar_q_type"><?php esc_html_e( 'Select quick warranty type', 'wcwar' ); ?></label>
						<select name="wcwar_q_type[<?php echo $loop; ?>]" class="wcwar_q_type option select short"<?php echo $curr_disable; ?>>
							<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
							<option value="included_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty') echo 'selected'; ?>><?php esc_html_e( 'Included warranty', 'wcwar' ); ?></option>
							<option value="paid_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty') echo 'selected'; ?>><?php esc_html_e( 'Paid warranty', 'wcwar' ); ?></option>
						</select>
						<em><?php esc_html_e( 'Warranties can be included or paid as an add-on. Choose your warranty type.', 'wcwar' ); ?></em>
					</p>
				</td>

				<?php
					$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty' ? '' : ' disabled="disabled"' );
				?>
				<td class="options_group grouping" data-group="included_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
					<p class="form-field wcwar_qi_type">
						<label for="wcwar_qi_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
						<select name="wcwar_qi_type[<?php echo $loop; ?>]" class="wcwar_qi_type option select short"<?php echo $curr_disable; ?>>
							<?php
								$curr_options = ( isset( $curr_warranty['included_warranty'] ) ? $curr_warranty['included_warranty'] : array() );
							?>
							<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
							<option value="days" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
							<option value="weeks" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
							<option value="months" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
							<option value="years" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
							<option value="lifetime" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
						</select>
						<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
					</p>
					<p class="form-field wcwar_qi_period">
						<label for="wcwar_qi_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
						<input type="number" class="wcwar_qi_period option short" name="wcwar_qi_period[<?php echo $loop; ?>]" value="<?php if (isset( $curr_options['period'] ) && $curr_options['period'] !== '') echo $curr_options['period']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
						<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
					</p>
					<p class="form-field wcwar_qi_desc">
						<label for="wcwar_qi_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
						<textarea class="wcwar_qi_desc option short" name="wcwar_qi_desc[<?php echo $loop; ?>]" placeholder="" step="any"<?php echo $curr_disable; ?>><?php if (isset( $curr_options['desc'] ) && $curr_options['desc'] !== '') echo $curr_options['desc']; ?></textarea>
						<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
					</p>
					<p class="form-field wcwar_qi_thumb">
						<span class="thumb_preview"><?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo '<img width="64" height="auto" src="' . $curr_options['thumb'] . '" alt="' .esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
						<label for="wcwar_qi_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
						<input type="hidden" class="wcwar_qi_thumb option short" name="wcwar_qi_thumb[<?php echo $loop; ?>]" value="<?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo $curr_options['thumb']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
						<button type="button" class="option button add_wcwar_qi_thumb"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
					</p>
				</td>

				<?php
					$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty' ? '' : ' disabled="disabled"' );
				?>
				<td class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
					<p class="form-field wcwar_qp_without">
						<label for="wcwar_qp_without"><?php esc_html_e( 'Add <em>No Warranty</em> option', 'wcwar' ); ?></label>
						<input type="checkbox" name="wcwar_qp_without[<?php echo $loop; ?>]" value="yes" <?php if (isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes') echo 'checked'; ?> class="wcwar_qp_without option checkbox"<?php echo $curr_disable; ?> />
						<em><?php esc_html_e( 'To enable product purchases without a paid warranty check this option.', 'wcwar' ); ?></em>
					</p>
				</td>

				<td class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
					<div class="paid_warranty_items wcwar_metaboxes">
					<?php
						if ( isset( $curr_warranty['paid_warranty'] ) ) {

							foreach ( $curr_warranty['paid_warranty'] as $warranty ){

						?>
							<div class="wcwar_metabox">
								<a href="#" class="move_paid_warranty"><i class="wcwar-move"></i></a>
								<a href="#" class="remove_paid_warranty"><i class="wcwar-close"></i></a>
								<div class="options_group grouping" data-group="included_warranty">
									<p class="form-field wcwar_qp_price">
										<label for="wcwar_qp_price"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>
										<input type="number" class="option short wc_input_price" name="wcwar_qp_price[<?php echo $loop; ?>][]" value="<?php if (isset( $warranty['price'] ) ) echo $warranty['price']; ?>" placeholder="" step="any">
										<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>
									</p>
									<p class="form-field wcwar_qp_type">
										<label for="wcwar_qp_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
										<select name="wcwar_qp_type[<?php echo $loop; ?>][]" class="option select short">
											<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
											<option value="days" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
											<option value="weeks" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
											<option value="months" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
											<option value="years" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
											<option value="lifetime" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
										</select>
										<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
									</p>
									<p class="form-field wcwar_qp_period">
										<label for="wcwar_qp_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
										<input type="number" class="option short" name="wcwar_qp_period[<?php echo $loop; ?>][]" value="<?php if (isset( $warranty['period'] ) && $warranty['period'] !== '') echo $warranty['period']; ?>" placeholder="" step="any">
										<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
									</p>
									<p class="form-field wcwar_qp_desc">
										<label for="wcwar_qp_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
										<textarea class="option short" name="wcwar_qp_desc[<?php echo $loop; ?>][]" placeholder="" step="any"><?php if (isset( $warranty['desc'] ) && $warranty['desc'] !== '') echo $warranty['desc']; ?></textarea>
										<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
									</p>
									<p class="form-field wcwar_qp_thumb">
										<span class="thumb_preview"><?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo '<img width="64" height="auto" src="' . $warranty['thumb'] . '" alt="' . esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
										<label for="wcwar_qp_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
										<input type="hidden" class="option short" name="wcwar_qp_thumb[<?php echo $loop; ?>][]" value="<?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo $warranty['thumb']; ?>" placeholder="" step="any">
										<button type="button" class="option button add_wcwar_qp_thumb"><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
									</p>
								</div>
							</div>
						<?php
							}
						}
					?>
					</div>
					<p class="toolbar">
						<button type="button" class="option button button-primary add_paid_warranty"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add paid warranty', 'wcwar' ); ?></button>
					</p>
				</td>

			</tr>
		<?php
		}

		function wc_product_save_24( $curr_id, $i ) {

			if ( isset( $_POST['wcwar_type'][$i] ) ) {
				if ( $_POST['wcwar_type'][$i] == 'preset_warranty' ) {
					$curr['type'] = 'preset_warranty';
					$curr['preset'] = ( isset( $_POST['wcwar_preset'][$i] ) && $_POST['wcwar_preset'][$i] !== '' ? $_POST['wcwar_preset'][$i] : '' );
				}
				else if ( $_POST['wcwar_type'][$i] == 'quick_warranty' ) {
					$curr['type'] = 'quick_warranty';
					if ( isset( $_POST['wcwar_q_type'][$i] ) ){
						if ( $_POST['wcwar_q_type'][$i] == 'included_warranty' ) {
							$curr['quick'] = 'included_warranty';
							$curr['included_warranty'] = array(
								'type' => ( isset( $_POST['wcwar_qi_type'][$i] ) && $_POST['wcwar_qi_type'][$i] !== '' ? $_POST['wcwar_qi_type'][$i] : '' ),
								'period' => ( isset( $_POST['wcwar_qi_period'][$i] ) && $_POST['wcwar_qi_period'][$i] !== '' ? $_POST['wcwar_qi_period'][$i] : '' ),
								'desc' => ( isset( $_POST['wcwar_qi_desc'][$i] ) && $_POST['wcwar_qi_desc'][$i] !== '' ? $_POST['wcwar_qi_desc'][$i] : '' ),
								'thumb' => ( isset( $_POST['wcwar_qi_thumb'][$i] ) && $_POST['wcwar_qi_thumb'][$i] !== '' ? $_POST['wcwar_qi_thumb'][$i] : '' )
							);
						}
						else if ( $_POST['wcwar_q_type'][$i] == 'paid_warranty' ) {
							$curr['quick'] = 'paid_warranty';

							$curr_free = ( isset( $_POST['wcwar_qp_without'][$i] ) && $_POST['wcwar_qp_without'][$i] == 'yes' ? 'yes' : 'no' );
							$curr_prices = ( isset( $_POST['wcwar_qp_price'][$i] ) && !empty( $_POST['wcwar_qp_price'][$i] ) ? $_POST['wcwar_qp_price'][$i] : array() );
							$curr_types = ( isset( $_POST['wcwar_qp_type'][$i] ) && !empty( $_POST['wcwar_qp_type'][$i] ) ? $_POST['wcwar_qp_type'][$i] : array() );
							$curr_periods = ( isset( $_POST['wcwar_qp_period'][$i] ) && !empty( $_POST['wcwar_qp_period'][$i] ) ? $_POST['wcwar_qp_period'][$i] : array() );
							$curr_descs = ( isset( $_POST['wcwar_qp_desc'][$i] ) && !empty( $_POST['wcwar_qp_desc'][$i] ) ? $_POST['wcwar_qp_desc'][$i] : array() );
							$curr_thumbs = ( isset( $_POST['wcwar_qp_thumb'][$i] ) && !empty( $_POST['wcwar_qp_thumb'][$i] ) ? $_POST['wcwar_qp_thumb'][$i] : array() );

							$curr['paid_no_warranty'] = $curr_free;
							for ( $n = 0; $n < count( $curr_types); $n++ ) {
								if (!isset( $curr_types[$n] ) || !isset( $curr_periods[$n] ) ) continue;

								$curr['paid_warranty'][] = array(
									'price' => $curr_prices[$n],
									'type' => $curr_types[$n],
									'period' => $curr_periods[$n],
									'desc' => ( isset( $curr_descs[$n] ) && $curr_descs[$n] !== '' ? $curr_descs[$n] : '' ),
									'thumb' => ( isset( $curr_thumbs[$n] ) && $curr_thumbs[$n] !== '' ? $curr_thumbs[$n] : '' )
								);
							}
						}
					}
				}
				else {
					$curr = array(
						'type' => 'no_warranty'
					);
				}

				update_post_meta( $curr_id, '_wcwar_warranty', $curr );

			}

		}

		function wc_product_save( $curr_id, $post, $update ) {

			$curr = array();

			if ( isset( $_POST['product-type'] ) && $_POST['product-type'] == 'variable' && self::$settings['wc_version'] == '2.3' ) {

				if ( !isset( $_POST['wcwar_type'] ) || !isset( $_POST['variable_post_id'] ) ) {
					return;
				}

				$types = $_POST['wcwar_type'];

				$count  = count( $types);

				$ids = $_POST['variable_post_id'];

				for ( $i = 0; $i < $count; $i++ ) {

					if ( isset( $_POST['wcwar_type'][$i] ) ) {
						if ( $_POST['wcwar_type'][$i] == 'preset_warranty' ) {
							$curr['type'] = 'preset_warranty';
							$curr['preset'] = ( isset( $_POST['wcwar_preset'][$i] ) && $_POST['wcwar_preset'][$i] !== '' ? $_POST['wcwar_preset'][$i] : '' );
						}
						else if ( $_POST['wcwar_type'][$i] == 'quick_warranty' ) {
							$curr['type'] = 'quick_warranty';
							if ( isset( $_POST['wcwar_q_type'][$i] ) ){
								if ( $_POST['wcwar_q_type'][$i] == 'included_warranty' ) {
									$curr['quick'] = 'included_warranty';
									$curr['included_warranty'] = array(
										'type' => ( isset( $_POST['wcwar_qi_type'][$i] ) && $_POST['wcwar_qi_type'][$i] !== '' ? $_POST['wcwar_qi_type'][$i] : '' ),
										'period' => ( isset( $_POST['wcwar_qi_period'][$i] ) && $_POST['wcwar_qi_period'][$i] !== '' ? $_POST['wcwar_qi_period'][$i] : '' ),
										'desc' => ( isset( $_POST['wcwar_qi_desc'][$i] ) && $_POST['wcwar_qi_desc'][$i] !== '' ? $_POST['wcwar_qi_desc'][$i] : '' ),
										'thumb' => ( isset( $_POST['wcwar_qi_thumb'][$i] ) && $_POST['wcwar_qi_thumb'][$i] !== '' ? $_POST['wcwar_qi_thumb'][$i] : '' )
									);
								}
								else if ( $_POST['wcwar_q_type'][$i] == 'paid_warranty' ) {
									$curr['quick'] = 'paid_warranty';

									$curr_free = ( isset( $_POST['wcwar_qp_without'][$i] ) && $_POST['wcwar_qp_without'][$i] == 'yes' ? 'yes' : 'no' );
									$curr_prices = ( isset( $_POST['wcwar_qp_price'][$i] ) && !empty( $_POST['wcwar_qp_price'][$i] ) ? $_POST['wcwar_qp_price'][$i] : array() );
									$curr_types = ( isset( $_POST['wcwar_qp_type'][$i] ) && !empty( $_POST['wcwar_qp_type'][$i] ) ? $_POST['wcwar_qp_type'][$i] : array() );
									$curr_periods = ( isset( $_POST['wcwar_qp_period'][$i] ) && !empty( $_POST['wcwar_qp_period'][$i] ) ? $_POST['wcwar_qp_period'][$i] : array() );
									$curr_descs = ( isset( $_POST['wcwar_qp_desc'][$i] ) && !empty( $_POST['wcwar_qp_desc'][$i] ) ? $_POST['wcwar_qp_desc'][$i] : array() );
									$curr_thumbs = ( isset( $_POST['wcwar_qp_thumb'][$i] ) && !empty( $_POST['wcwar_qp_thumb'][$i] ) ? $_POST['wcwar_qp_thumb'][$i] : array() );

									$curr['paid_no_warranty'] = $curr_free;
									for ( $n = 0; $n < count( $curr_types); $n++ ) {
										if (!isset( $curr_types[$n] ) || !isset( $curr_periods[$n] ) ) continue;

										$curr['paid_warranty'][] = array(
											'price' => $curr_prices[$n],
											'type' => $curr_types[$n],
											'period' => $curr_periods[$n],
											'desc' => ( isset( $curr_descs[$n] ) && $curr_descs[$n] !== '' ? $curr_descs[$n] : '' ),
											'thumb' => ( isset( $curr_thumbs[$n] ) && $curr_thumbs[$n] !== '' ? $curr_thumbs[$n] : '' )
										);
									}
								}
							}
						}
						else {
							$curr = array(
								'type' => 'no_warranty'
							);
						}

						update_post_meta( $ids[$i], '_wcwar_warranty', $curr );

					}

				}

			}
			else {
				if ( isset( $_POST['wcwar_type_single'] ) ) {
					if ( $_POST['wcwar_type_single'] == 'preset_warranty' ) {
						$curr['type'] = 'preset_warranty';
						$curr['preset'] = ( isset( $_POST['wcwar_preset_single'] ) && $_POST['wcwar_preset_single'] !== '' ? $_POST['wcwar_preset_single'] : '' );
					}
					else if ( $_POST['wcwar_type_single'] == 'quick_warranty' ) {
						$curr['type'] = 'quick_warranty';
						if ( isset( $_POST['wcwar_q_type_single'] ) ){
							if ( $_POST['wcwar_q_type_single'] == 'included_warranty' ) {
								$curr['quick'] = 'included_warranty';
								$curr['included_warranty'] = array(
									'type' => ( isset( $_POST['wcwar_qi_type_single'] ) && $_POST['wcwar_qi_type_single'] !== '' ? $_POST['wcwar_qi_type_single'] : '' ),
									'period' => ( isset( $_POST['wcwar_qi_period_single'] ) && $_POST['wcwar_qi_period_single'] !== '' ? $_POST['wcwar_qi_period_single'] : '' ),
									'desc' => ( isset( $_POST['wcwar_qi_desc_single'] ) && $_POST['wcwar_qi_desc_single'] !== '' ? $_POST['wcwar_qi_desc_single'] : '' ),
									'thumb' => ( isset( $_POST['wcwar_qi_thumb_single'] ) && $_POST['wcwar_qi_thumb_single'] !== '' ? $_POST['wcwar_qi_thumb_single'] : '' )
								);
							}
							else if ( $_POST['wcwar_q_type_single'] == 'paid_warranty' ) {
								$curr['quick'] = 'paid_warranty';

								$curr_free = ( isset( $_POST['wcwar_qp_without_single'] ) && $_POST['wcwar_qp_without_single'] == 'yes' ? 'yes' : 'no' );
								$curr_prices = ( isset( $_POST['wcwar_qp_price_single'] ) && !empty( $_POST['wcwar_qp_price_single'] ) ? $_POST['wcwar_qp_price_single'] : array() );
								$curr_types = ( isset( $_POST['wcwar_qp_type_single'] ) && !empty( $_POST['wcwar_qp_type_single'] ) ? $_POST['wcwar_qp_type_single'] : array() );
								$curr_periods = ( isset( $_POST['wcwar_qp_period_single'] ) && !empty( $_POST['wcwar_qp_period_single'] ) ? $_POST['wcwar_qp_period_single'] : array() );
								$curr_descs = ( isset( $_POST['wcwar_qp_desc_single'] ) && !empty( $_POST['wcwar_qp_desc_single'] ) ? $_POST['wcwar_qp_desc_single'] : array() );
								$curr_thumbs = ( isset( $_POST['wcwar_qp_thumb_single'] ) && !empty( $_POST['wcwar_qp_thumb_single'] ) ? $_POST['wcwar_qp_thumb_single'] : array() );

								$curr['paid_no_warranty'] = $curr_free;
								for ( $i = 0; $i < count( $curr_types); $i++ ) {
									if (!isset( $curr_types[$i] ) || !isset( $curr_periods[$i] ) ) continue;

									$curr['paid_warranty'][] = array(
										'price' => $curr_prices[$i],
										'type' => $curr_types[$i],
										'period' => $curr_periods[$i],
										'desc' => ( isset( $curr_descs[$i] ) && $curr_descs[$i] !== '' ? $curr_descs[$i] : '' ),
										'thumb' => ( isset( $curr_thumbs[$i] ) && $curr_thumbs[$i] !== '' ? $curr_thumbs[$i] : '' )
									);
								}
							}
						}
					}
					else {
						$curr['type'] = 'no_warranty';
					}

					update_post_meta( $curr_id, '_wcwar_warranty', $curr );

				}
			}
		}

		function war_add_presets() {
			$curr_warranty = array();
			$curr_disable = '';

			if ( empty( $curr_warranty) ) {
				$curr_warranty = array( 'type' => 'no_warranty' );
			}
	?>
		<script type="text/javascript">

			jQuery(document).ready( function( $) {
				$('#tag-description').parent().remove();

				$(".wcwar_metaboxes.paid_warranty_items").sortable({handle:"a.move_paid_warranty"});

				var wcwar_metabox = "<div class=\"wcwar_metabox\">\
							<a href=\"#\" class=\"move_paid_warranty\"><i class=\"wcwar-move\"></i></a>\
							<a href=\"#\" class=\"remove_paid_warranty\"><i class=\"wcwar-close\"></i></a>\
							<div class=\"options_group grouping\" data-group=\"included_warranty\">\
								<p class=\"form-field wcwar_qp_price\">\
									<label for=\"wcwar_qp_price\"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>\
									<input type=\"number\" class=\"option short wc_input_price\" name=\"wcwar_qp_price[]\" value=\"\" placeholder=\"\" step=\"any\">\
									<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_type\">\
									<label for=\"wcwar_qp_type\"><?php  esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>\
									<select name=\"wcwar_qp_type[]\" class=\"option select short\">\
										<option value=\"\"><?php  esc_html_e( 'Not selected', 'wcwar' ); ?></option>\
										<option value=\"days\"><?php  esc_html_e( 'Days', 'wcwar' ); ?></option>\
										<option value=\"weeks\"><?php  esc_html_e( 'Weeks', 'wcwar' ); ?></option>\
										<option value=\"months\"><?php  esc_html_e( 'Months', 'wcwar' ); ?></option>\
										<option value=\"years\"><?php  esc_html_e( 'Years', 'wcwar' ); ?></option>\
										<option value=\"lifetime\"><?php  esc_html_e( 'Lifetime', 'wcwar' ); ?></option>\
									</select>\
									<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_period\">\
									<label for=\"wcwar_qp_period\"><?php  esc_html_e( 'Warranty period', 'wcwar' ); ?></label>\
									<input type=\"number\" class=\"option short\" name=\"wcwar_qp_period[]\" value=\"\" placeholder=\"\" step=\"any\">\
									<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_desc\">\
									<label for=\"wcwar_qp_desc\"><?php  esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>\
									<textarea class=\"option short\" name=\"wcwar_qp_desc[]\" placeholder=\"\" step=\"any\"></textarea>\
									<em><?php esc_html_e( 'Enter warranty descriptio.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_thumb\">\
									<span class=\"thumb_preview\"></span>\
									<label for=\"wcwar_qp_thumb\"><?php  esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>\
									<input type=\"hidden\" class=\"option short\" name=\"wcwar_qp_thumb[]\" value=\"\" placeholder=\"\" step=\"any\">\
									<button type=\"button\" class=\"option button add_wcwar_qp_thumb\"><?php  esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>\
								</p>\
							</div>\
						</div>";

				$(document).on("change", "#wcwar_q_type", function() {

					var curr = $("#wcwar_tab");
					curr.find(".options_group:not(.type) .option").attr("disabled", "disabled").closest(".options_group").hide();

					if ( $(this).val() != "") {
						curr.find(".options_group[data-group="+$(this).val()+"] .option").removeAttr("disabled").closest(".options_group").show();
						if ( $(this).val() == "included_warranty" ) {
							curr.find(".paid_warranty_items .wcwar_metabox").remove();
						}
					}
					else {
						curr.find(".paid_warranty_items .wcwar_metabox").remove();
					}

				});
				
				$(document).on("click", ".add_paid_warranty", function () {
					$(this).parent().prev().append(wcwar_metabox);
					return false;
				});

				$(document).on("click", ".remove_paid_warranty", function () {
					$(this).parent().remove();
					return false;
				});

				$(document).on("click", ".add_wcwar_qi_thumb, .add_wcwar_qp_thumb", function () {

					var frame;
					var el = $(this);
					var curr = el.parent();

					if ( frame ) {
						frame.open();
						return;
					}

					frame = wp.media({
						title: el.data("choose"),
						button: {
							text: el.data("update"),
							close: false
						}
					});

					frame.on( "select", function() {

						var attachment = frame.state().get("selection").first();
						frame.close();
						curr.find("input:hidden").val(attachment.attributes.url);
						if ( attachment.attributes.type == "image" ) {
							curr.find(".thumb_preview").empty().hide().append("<img width=\"64\" height=\"auto\" src=\""+attachment.attributes.url+"\">").slideDown("fast");
						}

					});

					frame.open();

					return false;
				});

			});

		</script>
		<div id="wcwar_tab" class="form-field">

			<div class="options_group grouping type" data-group="quick_warranty">
				<p class="form-field wcwar_q_type">
					<label for="wcwar_q_type"><?php esc_html_e( 'Select warranty type', 'wcwar' ); ?></label>
					<select id="wcwar_q_type" name="wcwar_q_type" class="option select short">
						<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
						<option value="included_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty') echo 'selected'; ?>><?php esc_html_e( 'Included warranty', 'wcwar' ); ?></option>
						<option value="paid_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty') echo 'selected'; ?>><?php esc_html_e( 'Paid warranty', 'wcwar' ); ?></option>
					</select>
					<em><?php esc_html_e( 'Warranties can be included or paid as an add-on. Choose your warranty type.', 'wcwar' ); ?></em>
				</p>
			</div>

			<?php
				$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty' ? '' : ' disabled="disabled"' );
			?>
			<div class="options_group grouping" data-group="included_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<p class="form-field wcwar_qi_type">
					<label for="wcwar_qi_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
					<select id="wcwar_qi_type" name="wcwar_qi_type" class="option select short"<?php echo $curr_disable; ?>>
						<?php
							$curr_options = ( isset( $curr_warranty['included_warranty'] ) ? $curr_warranty['included_warranty'] : array() );
						?>
						<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
						<option value="days" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
						<option value="weeks" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
						<option value="months" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
						<option value="years" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
						<option value="lifetime" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
					</select>
					<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
				</p>
				<p class="form-field wcwar_qi_period">
					<label for="wcwar_qi_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
					<input type="number" class="option short" name="wcwar_qi_period" id="wcwar_qi_period" value="<?php if (isset( $curr_options['period'] ) && $curr_options['period'] !== '') echo $curr_options['period']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
					<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
				</p>
				<p class="form-field wcwar_qi_desc">
					<label for="wcwar_qi_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
					<textarea class="option short" name="wcwar_qi_desc" id="wcwar_qi_desc" placeholder="" step="any"<?php echo $curr_disable; ?>><?php if (isset( $curr_options['desc'] ) && $curr_options['desc'] !== '') echo $curr_options['desc']; ?></textarea>
					<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
				</p>
				<p class="form-field wcwar_qi_thumb">
					<span class="thumb_preview"><?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo '<img width="64" height="auto" src="' . $curr_options['thumb'] . '" alt="' .esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
					<label for="wcwar_qi_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
					<input type="hidden" class="option short" name="wcwar_qi_thumb" id="wcwar_qi_thumb" value="<?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo $curr_options['thumb']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
					<button type="button" class="option button add_wcwar_qi_thumb"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
				</p>
			</div>

			<?php
				$curr_disable = ( $curr_warranty['type'] == 'quick_warranty' && isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty' ? '' : ' disabled="disabled"' );
			?>
			<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<p class="form-field wcwar_qp_without">
					<label for="wcwar_qp_without"><?php esc_html_e( 'Add <em>No Warranty</em> option', 'wcwar' ); ?></label>
					<input type="checkbox" name="wcwar_qp_without" id="wcwar_qp_without" value="yes" <?php if (isset( $curr_warranty['paid_warranty_exclude'] ) && $curr_warranty['paid_warranty_exclude'] == 'yes') echo 'checked'; ?> class="option checkbox"<?php echo $curr_disable; ?> />
					<em><?php esc_html_e( 'To enable product purchases without a paid warranty check this option.', 'wcwar' ); ?></em>
				</p>
			</div>

			<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
				<div class="paid_warranty_items wcwar_metaboxes">
				<?php
					if ( isset( $curr_warranty['paid_warranty'] ) ) {

						foreach ( $curr_warranty['paid_warranty'] as $warranty ){

					?>
						<div class="wcwar_metabox">
							<a href="#" class="move_paid_warranty"><i class="wcwar-move"></i></a>
							<a href="#" class="remove_paid_warranty"><i class="wcwar-close"></i></a>
							<div class="options_group grouping" data-group="included_warranty">
								<p class="form-field wcwar_qp_price">
									<label for="wcwar_qp_price"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>
									<input type="number" class="option short wc_input_price" name="wcwar_qp_price[]" value="<?php if (isset( $warranty['price'] ) ) echo $warranty['price']; ?>" placeholder="" step="any">
									<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_type">
									<label for="wcwar_qp_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
									<select name="wcwar_qp_type[]" class="option select short">
										<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
										<option value="days" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
										<option value="weeks" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
										<option value="months" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
										<option value="years" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
										<option value="lifetime" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
									</select>
									<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_period">
									<label for="wcwar_qp_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
									<input type="number" class="option short" name="wcwar_qp_period[]" value="<?php if (isset( $warranty['period'] ) && $warranty['period'] !== '') echo $warranty['period']; ?>" placeholder="" step="any">
									<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_desc">
									<label for="wcwar_qp_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
									<textarea class="option short" name="wcwar_qp_desc[]" placeholder="" step="any"><?php if (isset( $warranty['desc'] ) && $warranty['desc'] !== '') echo $warranty['desc']; ?></textarea>
									<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
								</p>
								<p class="form-field wcwar_qp_thumb">
									<span class="thumb_preview"><?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo '<img width="64" height="auto" src="' . $warranty['thumb'] . '" alt="' . esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
									<label for="wcwar_qp_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
									<input type="hidden" class="option short" name="wcwar_qp_thumb[]" value="<?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo $warranty['thumb']; ?>" placeholder="" step="any">
									<button type="button" class="option button add_wcwar_qp_thumb"><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
								</p>
							</div>
						</div>
					<?php
						}
					}
				?>
				</div>
				<p class="toolbar">
					<button type="button" class="option button button-primary add_paid_warranty"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add paid warranty', 'wcwar' ); ?></button>
				</p>
			</div>

		</div>
	<?php

		}


		function war_edit_presets( $term, $taxonomy) {
			$curr_warranty = get_woocommerce_term_meta( $term->term_id, '_wcwar_warranty', true );

			$curr_disable = '';

	?>
		<script type="text/javascript">

			jQuery(document).ready( function( $) {
				$('#description').closest('.form-field').remove();

				$(".wcwar_metaboxes.paid_warranty_items").sortable({handle:"a.move_paid_warranty"});

				var wcwar_metabox = "<div class=\"wcwar_metabox\">\
							<a href=\"#\" class=\"move_paid_warranty\"><i class=\"wcwar-move\"></i></a>\
							<a href=\"#\" class=\"remove_paid_warranty\"><i class=\"wcwar-close\"></i></a>\
							<div class=\"options_group grouping\" data-group=\"included_warranty\">\
								<p class=\"form-field wcwar_qp_price\">\
									<label for=\"wcwar_qp_price\"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>\
									<input type=\"number\" class=\"option short wc_input_price\" name=\"wcwar_qp_price[]\" value=\"\" placeholder=\"\" step=\"any\">\
									<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_type\">\
									<label for=\"wcwar_qp_type\"><?php  esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>\
									<select name=\"wcwar_qp_type[]\" class=\"option select short\">\
										<option value=\"\"><?php  esc_html_e( 'Not selected', 'wcwar' ); ?></option>\
										<option value=\"days\"><?php  esc_html_e( 'Days', 'wcwar' ); ?></option>\
										<option value=\"weeks\"><?php  esc_html_e( 'Weeks', 'wcwar' ); ?></option>\
										<option value=\"months\"><?php  esc_html_e( 'Months', 'wcwar' ); ?></option>\
										<option value=\"years\"><?php  esc_html_e( 'Years', 'wcwar' ); ?></option>\
										<option value=\"lifetime\"><?php  esc_html_e( 'Lifetime', 'wcwar' ); ?></option>\
									</select>\
									<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_period\">\
									<label for=\"wcwar_qp_period\"><?php  esc_html_e( 'Warranty period', 'wcwar' ); ?></label>\
									<input type=\"number\" class=\"option short\" name=\"wcwar_qp_period[]\" value=\"\" placeholder=\"\" step=\"any\">\
									<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_desc\">\
									<label for=\"wcwar_qp_desc\"><?php  esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>\
									<textarea class=\"option short\" name=\"wcwar_qp_desc[]\" placeholder=\"\" step=\"any\"></textarea>\
									<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>\
								</p>\
								<p class=\"form-field wcwar_qp_thumb\">\
									<span class=\"thumb_preview\"></span>\
									<label for=\"wcwar_qp_thumb\"><?php  esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>\
									<input type=\"hidden\" class=\"option short\" name=\"wcwar_qp_thumb[]\" value=\"\" placeholder=\"\" step=\"any\">\
									<button type=\"button\" class=\"option button add_wcwar_qp_thumb\"><?php  esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>\
								</p>\
							</div>\
						</div>";

				$(document).on("change", "#wcwar_q_type", function() {

					var curr = $("#wcwar_tab");
					curr.find(".options_group:not(.type) .option").attr("disabled", "disabled").closest('.options_group').hide();

					if ( $(this).val() != "") {
						curr.find(".options_group[data-group="+$(this).val()+"] .option").removeAttr("disabled").closest('.options_group').show();
						if ( $(this).val() == "included_warranty" ) {
							curr.find(".paid_warranty_items .wcwar_metabox").remove();
						}
					}
					else {
						curr.find(".paid_warranty_items .wcwar_metabox").remove();
					}

				});
				
				$(document).on("click", ".add_paid_warranty", function () {
					$(this).parent().prev().append(wcwar_metabox);
					return false;
				});

				$(document).on("click", ".remove_paid_warranty", function () {
					$(this).parent().remove();
					return false;
				});

				$(document).on("click", ".add_wcwar_qi_thumb, .add_wcwar_qp_thumb", function () {

					var frame;
					var el = $(this);
					var curr = el.parent();

					if ( frame ) {
						frame.open();
						return;
					}

					frame = wp.media({
						title: el.data("choose"),
						button: {
							text: el.data("update"),
							close: false
						}
					});

					frame.on( "select", function() {

						var attachment = frame.state().get("selection").first();
						frame.close();
						curr.find("input:hidden").val(attachment.attributes.url);
						if ( attachment.attributes.type == "image" ) {
							curr.find(".thumb_preview").empty().hide().append("<img width=\"64\" height=\"auto\" src=\""+attachment.attributes.url+"\">").slideDown("fast");
						}

					});

					frame.open();

					return false;
				});

			});

		</script>
		<tr class="form-field wcwar-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Warranty', 'wcwar' ); ?></label></th>
			<td>
				<div id="wcwar_tab" class="form-field">

					<div class="options_group grouping type" data-group="quick_warranty">
						<p class="form-field wcwar_q_type">
							<label for="wcwar_q_type"><?php esc_html_e( 'Select warranty type', 'wcwar' ); ?></label>
							<select id="wcwar_q_type" name="wcwar_q_type" class="option select short">
								<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
								<option value="included_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty') echo 'selected'; ?>><?php esc_html_e( 'Included warranty', 'wcwar' ); ?></option>
								<option value="paid_warranty" <?php if (isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty') echo 'selected'; ?>><?php esc_html_e( 'Paid warranty', 'wcwar' ); ?></option>
							</select>
							<em><?php esc_html_e( 'Warranties can be included or paid as an add-on. Choose your warranty type.', 'wcwar' ); ?></em>
						</p>
					</div>

					<?php
						$curr_disable = ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'included_warranty' ? '' : ' disabled="disabled"' );
					?>
					<div class="options_group grouping" data-group="included_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
						<p class="form-field wcwar_qi_type">
							<label for="wcwar_qi_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
							<select id="wcwar_qi_type" name="wcwar_qi_type" class="option select short"<?php echo $curr_disable; ?>>
								<?php
									$curr_options = ( isset( $curr_warranty['included_warranty'] ) ? $curr_warranty['included_warranty'] : array() );
								?>
								<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
								<option value="days" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
								<option value="weeks" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
								<option value="months" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
								<option value="years" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
								<option value="lifetime" <?php if (isset( $curr_options['type'] ) && $curr_options['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
							</select>
							<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
						</p>
						<p class="form-field wcwar_qi_period">
							<label for="wcwar_qi_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
							<input type="number" class="option short" name="wcwar_qi_period" id="wcwar_qi_period" value="<?php if (isset( $curr_options['period'] ) && $curr_options['period'] !== '') echo $curr_options['period']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
							<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
						</p>
						<p class="form-field wcwar_qi_desc">
							<label for="wcwar_qi_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
							<textarea class="option short" name="wcwar_qi_desc" id="wcwar_qi_desc" placeholder="" step="any"<?php echo $curr_disable; ?>><?php if (isset( $curr_options['desc'] ) && $curr_options['desc'] !== '') echo $curr_options['desc']; ?></textarea>
							<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
						</p>
						<p class="form-field wcwar_qi_thumb">
							<span class="thumb_preview"><?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo '<img width="64" height="auto" src="' . $curr_options['thumb'] . '" alt="' .esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
							<label for="wcwar_qi_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
							<input type="hidden" class="option short" name="wcwar_qi_thumb" id="wcwar_qi_thumb" value="<?php if (isset( $curr_options['thumb'] ) && $curr_options['thumb'] !== '') echo $curr_options['thumb']; ?>" placeholder="" step="any"<?php echo $curr_disable; ?>>
							<button type="button" class="option button add_wcwar_qi_thumb"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
						</p>
					</div>

					<?php
						$curr_disable = ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] == 'paid_warranty' ? '' : ' disabled="disabled"' );
					?>
					<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
						<p class="form-field wcwar_qp_without">
							<label for="wcwar_qp_without"><?php esc_html_e( 'Add <em>No Warranty</em> option', 'wcwar' ); ?></label>
							<input type="checkbox" name="wcwar_qp_without" id="wcwar_qp_without" value="yes" <?php if (isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes') echo 'checked'; ?> class="option checkbox"<?php echo $curr_disable; ?> />
							<em><?php esc_html_e( 'To enable product purchases without a paid warranty check this option.', 'wcwar' ); ?></em>
						</p>
					</div>

					<div class="options_group grouping" data-group="paid_warranty"<?php echo !empty( $curr_disable ) ? ' style="display:none;"' : ''; ?>>
						<div class="paid_warranty_items wcwar_metaboxes">
						<?php
							if ( isset( $curr_warranty['paid_warranty'] ) ) {

								foreach ( $curr_warranty['paid_warranty'] as $warranty ){

							?>
								<div class="wcwar_metabox">
									<a href="#" class="move_paid_warranty"><i class="wcwar-move"></i></a>
									<a href="#" class="remove_paid_warranty"><i class="wcwar-close"></i></a>
									<div class="options_group grouping" data-group="included_warranty">
										<p class="form-field wcwar_qp_price">
											<label for="wcwar_qp_price"><?php esc_html_e( 'Additional warranty price', 'wcwar' ); ?></label>
											<input type="number" class="option short wc_input_price" name="wcwar_qp_price[]" value="<?php if (isset( $warranty['price'] ) ) echo $warranty['price']; ?>" placeholder="" step="any">
											<em><?php esc_html_e( 'Enter additional price for this warranty.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_type">
											<label for="wcwar_qp_type"><?php esc_html_e( 'Warranty period type', 'wcwar' ); ?></label>
											<select name="wcwar_qp_type[]" class="option select short">
												<option value=""><?php esc_html_e( 'Not selected', 'wcwar' ); ?></option>
												<option value="days" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'days') echo 'selected'; ?>><?php esc_html_e( 'Days', 'wcwar' ); ?></option>
												<option value="weeks" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'weeks') echo 'selected'; ?>><?php esc_html_e( 'Weeks', 'wcwar' ); ?></option>
												<option value="months" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'months') echo 'selected'; ?>><?php esc_html_e( 'Months', 'wcwar' ); ?></option>
												<option value="years" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'years') echo 'selected'; ?>><?php esc_html_e( 'Years', 'wcwar' ); ?></option>
												<option value="lifetime" <?php if (isset( $warranty['type'] ) && $warranty['type'] == 'lifetime') echo 'selected'; ?>><?php esc_html_e( 'Lifetime', 'wcwar' ); ?></option>
											</select>
											<em><?php esc_html_e( 'Select warranty period type.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_period">
											<label for="wcwar_qp_period"><?php esc_html_e( 'Warranty period', 'wcwar' ); ?></label>
											<input type="number" class="option short" name="wcwar_qp_period[]" value="<?php if (isset( $warranty['period'] ) && $warranty['period'] !== '') echo $warranty['period']; ?>" placeholder="" step="any">
											<em><?php esc_html_e( 'Enter warranty period.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_desc">
											<label for="wcwar_qp_desc"><?php esc_html_e( 'Warranty description (optional)', 'wcwar' ); ?></label>
											<textarea class="option short" name="wcwar_qp_desc[]" placeholder="" step="any"><?php if (isset( $warranty['desc'] ) && $warranty['desc'] !== '') echo $warranty['desc']; ?></textarea>
											<em><?php esc_html_e( 'Enter warranty description.', 'wcwar' ); ?></em>
										</p>
										<p class="form-field wcwar_qp_thumb">
											<span class="thumb_preview"><?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo '<img width="64" height="auto" src="' . $warranty['thumb'] . '" alt="' . esc_html__( 'Thumbnail preview', 'wcwar') . '" />'; ?></span>
											<label for="wcwar_qp_thumb"><?php esc_html_e( 'Warranty thumbnail (optional)', 'wcwar' ); ?></label>
											<input type="hidden" class="option short" name="wcwar_qp_thumb[]" value="<?php if (isset( $warranty['thumb'] ) && $warranty['thumb'] !== '') echo $warranty['thumb']; ?>" placeholder="" step="any">
											<button type="button" class="option button add_wcwar_qp_thumb"><?php esc_html_e( 'Add warranty thumbnail', 'wcwar' ); ?></button>
										</p>
									</div>
								</div>
							<?php
								}
							}
						?>
						</div>
						<p class="toolbar">
							<button type="button" class="option button button-primary add_paid_warranty"<?php echo $curr_disable; ?>><?php esc_html_e( 'Add paid warranty', 'wcwar' ); ?></button>
						</p>
					</div>

				</div>
			</td>
		</tr>
	<?php
			
		}

/*		function war_preset_columns( $columns ) {

			if ( !isset( $_GET['taxonomy'] ) || $_GET['taxonomy'] != 'wcwar_warranty_pre' )

			return $columns;

			if ( $posts = $columns['description'] ){ unset( $columns['description'] ); }
			return $columns;

		}*/

		function war_save_preset( $term_id, $tt_id, $taxonomy ) {

			$curr = array();

			if ( isset( $_POST['wcwar_q_type'] ) ){
				if ( $_POST['wcwar_q_type'] == 'included_warranty' ) {
					$curr['quick'] = 'included_warranty';
					$curr['included_warranty'] = array(
						'type' => ( isset( $_POST['wcwar_qi_type'] ) && $_POST['wcwar_qi_type'] !== '' ? $_POST['wcwar_qi_type'] : '' ),
						'period' => ( isset( $_POST['wcwar_qi_period'] ) && $_POST['wcwar_qi_period'] !== '' ? $_POST['wcwar_qi_period'] : '' ),
						'desc' => ( isset( $_POST['wcwar_qi_desc'] ) && $_POST['wcwar_qi_desc'] !== '' ? $_POST['wcwar_qi_desc'] : '' ),
						'thumb' => ( isset( $_POST['wcwar_qi_thumb'] ) && $_POST['wcwar_qi_thumb'] !== '' ? $_POST['wcwar_qi_thumb'] : '' )
					);
				}
				else if ( $_POST['wcwar_q_type'] == 'paid_warranty' ) {
					$curr['quick'] = 'paid_warranty';

					$curr_free = ( isset( $_POST['wcwar_qp_without'] ) && $_POST['wcwar_qp_without'] == 'yes' ? 'yes' : 'no' );
					$curr_prices = ( isset( $_POST['wcwar_qp_price'] ) && !empty( $_POST['wcwar_qp_price'] ) ? $_POST['wcwar_qp_price'] : array() );
					$curr_types = ( isset( $_POST['wcwar_qp_type'] ) && !empty( $_POST['wcwar_qp_type'] ) ? $_POST['wcwar_qp_type'] : array() );
					$curr_periods = ( isset( $_POST['wcwar_qp_period'] ) && !empty( $_POST['wcwar_qp_period'] ) ? $_POST['wcwar_qp_period'] : array() );
					$curr_descs = ( isset( $_POST['wcwar_qp_desc'] ) && !empty( $_POST['wcwar_qp_desc'] ) ? $_POST['wcwar_qp_desc'] : array() );
					$curr_thumbs = ( isset( $_POST['wcwar_qp_thumb'] ) && !empty( $_POST['wcwar_qp_thumb'] ) ? $_POST['wcwar_qp_thumb'] : array() );

					$curr['paid_no_warranty'] = $curr_free;
					for ( $i = 0; $i < count( $curr_types); $i++ ) {
						if (!isset( $curr_types[$i] ) || !isset( $curr_periods[$i] ) ) continue;

						$curr['paid_warranty'][] = array(
							'price' => $curr_prices[$i],
							'type' => $curr_types[$i],
							'period' => $curr_periods[$i],
							'desc' => ( isset( $curr_descs[$i] ) && $curr_descs[$i] !== '' ? $curr_descs[$i] : '' ),
							'thumb' => ( isset( $curr_thumbs[$i] ) && $curr_thumbs[$i] !== '' ? $curr_thumbs[$i] : '' )
						);
					}
				}
				update_woocommerce_term_meta( $term_id, '_wcwar_warranty', $curr );
			}

		}

		function war_admin_scripts( $hook ) {

			$screen = get_current_screen();

			wp_enqueue_style( 'wcwar-font', self::$url_path . 'assets/fonts/styles.css' );
			wp_enqueue_style( 'wcwar-css', self::$url_path . 'assets/css/admin.css' );

			if ( $hook == 'post.php' || $hook == 'edit.php' || $hook == 'post-new.php' ) {
				wp_register_script( 'wcwar-js', self::$url_path . 'assets/js/admin.js', array( 'jquery' ), self::$version );
				wp_enqueue_script( 'wcwar-js' );

				$localize = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'localization' => array(
						'delete' => esc_html__( 'Delete?', 'wcwar' ),
						'notselected' => esc_html__( 'Not selected.', 'wcwar' ),
						'deleted' => esc_html__( 'Deleted!', 'wcwar' ),
						'error' => esc_html__( 'Error!', 'wcwar' ),
						'load' => esc_html__( 'Load?', 'wcwar' ),
						'loaded' => esc_html__( 'Loaded!', 'wcwar' ),
						'templatename' => esc_html__( 'Template name?', 'wcwar' ),
						'missing' => esc_html__( 'Missing email or name.', 'wcwar' ),
						'sendemail' => esc_html__( 'Send Email?', 'wcwar' ),
						'saved' => esc_html__( 'Saved!', 'wcwar' ),
						'emailsent' => esc_html__( 'Email sent!', 'wcwar' ),
					)
				);

				wp_localize_script( 'wcwar-js', 'wcwar', $localize );
			}
			if ( 'edit-tags.php' == $hook || 'term.php' == $hook ) {
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-sortable' );

				if ( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}
			}

		}

		function war_scripts() {

			if ( get_option( 'wcwar_force_scripts', 'yes' ) || is_product() ) {
				wp_enqueue_style( 'wcwar-product-css', self::$url_path . 'assets/css/product.css' );
				wp_register_script( 'wcwar-product-js', self::$url_path . 'assets/js/product.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'wcwar-product-js' );
			}

			if ( is_account_page() || is_checkout() ) {
				wp_enqueue_style( 'wcwar-font', self::$url_path . 'assets/fonts/styles.css' );
				wp_enqueue_style( 'wcwar-myaccount-css', self::$url_path . 'assets/css/myaccount.css' );
			}

			$request_page = self::wpml_get_id( get_option( 'war_settings_page' ) );
			if ( is_page( $request_page ) ) {
				wp_enqueue_style( 'wcwar-font', self::$url_path . 'assets/fonts/styles.css' );
				wp_enqueue_style( 'wcwar-request-css', self::$url_path . 'assets/css/request.css' );
				wp_register_script( 'wcwar-request-js', self::$url_path . 'assets/js/request.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'wcwar-request-js' );
			}

			if ( is_singular( 'wcwar_warranty_req' ) ) {
				wp_enqueue_style( 'wcwar-font', self::$url_path . 'assets/fonts/styles.css' );
				wp_enqueue_style( 'wcwar-warranty-css', self::$url_path . 'assets/css/warranty.css' );
			}

		}

		function war_product_warranty_output() {
			global $product;
			$type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;

			$ids = method_exists( $product, 'get_id' ) ? $product->get_children() : $product->children;
			if ( $type == 'variable' && $ids ) {
				$ids = isset( $ids['visible'] ) ? $ids['visible'] : $ids;
				$curr_class = ' war_variable';
			}
			else {
				$ids = array( method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id );
				$curr_class = ' war_simple';
			}

			foreach ( $ids as $id ) {

				$curr_warranty = get_post_meta( $id, '_wcwar_warranty', true );

				if ( empty( $curr_warranty ) ) {
					if ( ( $default_warranty = get_option( 'wcwar_default_warranty', '' ) ) == '' ) {
						$curr_warranty = array( 'type' => 'no_warranty' );
					}
					else {
						$curr_warranty = array(
							'type' => 'preset_warranty',
							'preset' => $default_warranty
						);
					}
				}

				if ( $curr_warranty['type'] == 'preset_warranty' ) {
					$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
					$curr_warranty = array_merge( $curr_warranty, $curr_preset);
				}
				else if ( $curr_warranty['type'] == 'quick_warranty' ) {

				}

				$title_tag = get_option( 'wcwar_single_titles', 'h4' );

				if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
					if ( $curr_warranty['quick'] == 'included_warranty' && isset( $curr_warranty['included_warranty'] ) ) {
				?>
					<div class="war_warranty<?php echo $curr_class; ?>" data-id="<?php echo $id; ?>">
						<<?php echo $title_tag; ?>><?php esc_html_e( 'Included Warranty', 'wcwar' ); ?></<?php echo $title_tag; ?>>
						<p>
							<?php if ( isset( $curr_warranty['included_warranty']['thumb'] ) ) { ?>
								<img width="64" height="auto" src="<?php echo $curr_warranty['included_warranty']['thumb']; ?>" />
							<?php } ?>
							<strong>
								
							<?php
								if ( $curr_warranty['included_warranty']['type'] !== 'lifetime' ) {
							?>
								<span>
									<?php echo $curr_warranty['included_warranty']['period'] . ' '; ?>
								</span>
								<?php
									echo self::hlp_get_warranty_string( $curr_warranty['included_warranty']['period'], $curr_warranty['included_warranty']['type'] );
								}
								else {
									esc_html_e( 'Lifetime Warranty', 'wcwar' );
								}
							?>
								
							</strong><br/>
							<?php if ( isset( $curr_warranty['included_warranty']['desc'] ) ) { ?>
								<small><?php echo $curr_warranty['included_warranty']['desc']; ?></small>
							<?php } ?>
						</p>
					</div>
				<?php
					}
					else if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {
				?>
					<div class="war_warranty war_paid<?php echo $curr_class; ?>" data-id="<?php echo $id; ?>">
						<<?php echo $title_tag; ?>><?php esc_html_e( 'Select Warranty Options', 'wcwar' ); ?></<?php echo $title_tag; ?>>
						<p>
							<select name="wcwar_pa_warranty">
								<?php if ( isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes' ) { ?>
									<option value="no_warranty"><?php esc_html_e( 'No warranty' ); ?></option>
								<?php } ?>
								<?php for ( $i = 0; $i < count( $curr_warranty['paid_warranty'] ); $i++ ) { ?>
									<option value="<?php echo $i; ?>"><?php echo $curr_warranty['paid_warranty'][$i]['period'] . ' ' . self::hlp_get_warranty_string( $curr_warranty['paid_warranty'][$i]['period'], $curr_warranty['paid_warranty'][$i]['type'] ); ?></option>
								<?php } ?>
							</select>
						</p>
						<?php if ( isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes' ) { ?>
						<p class="war_option" data-selected="no_warranty">
							<strong><?php esc_html_e( 'Not selected', 'wcwar' ); ?></strong><br/>
						<small><?php esc_html_e( 'Purchase this product without any warranty options.', 'wcwar' ); ?></small>
						</p>
						<?php } ?>
						<?php for ( $i = 0; $i < count( $curr_warranty['paid_warranty'] ); $i++ ) { ?>
						<p class="war_option" data-selected="<?php echo $i; ?>">
							<?php if ( isset( $curr_warranty['paid_warranty'][$i]['thumb'] ) && $curr_warranty['paid_warranty'][$i]['thumb'] !== '' ) { ?>
								<img width="64" height="auto" src="<?php echo $curr_warranty['paid_warranty'][$i]['thumb']; ?>" />
							<?php } ?>
							<strong>
							<?php
								if ( $curr_warranty['paid_warranty'][$i]['type'] !== 'lifetime' ) {
							?>
								<span>
									<?php echo $curr_warranty['paid_warranty'][$i]['period'] . ' '; ?>
								</span>
							<?php
									echo self::hlp_get_warranty_string( $curr_warranty['paid_warranty'][$i]['period'], $curr_warranty['paid_warranty'][$i]['type'] );
								}
								else {
									esc_html_e( 'Lifetime Warranty', 'wcwar' );
								}
							?>
							</strong><br/>
							<?php if ( isset( $curr_warranty['paid_warranty'][$i]['desc'] ) ) { ?>
								<small><?php echo $curr_warranty['paid_warranty'][$i]['desc']; ?></small><br/>
							<?php } ?>
							<strong>+ <?php echo wc_price( $curr_warranty['paid_warranty'][$i]['price'] ); ?></strong> 
						</p>
						<?php } ?>
					</div>
				<?php
					}
				}

				
			}

		}

		function wc_add_pa_warranty( $product_data, $product_id ) {
			global $woocommerce;

			if ( isset( $_POST['wcwar_pa_warranty'] ) && $_POST['wcwar_pa_warranty'] !== '' ) {
				$product_data['wcwar_pa_warranty'] = $_POST['wcwar_pa_warranty'];
			}
			else {
				$product_data['wcwar_pa_warranty'] = 'not_selected';
			}

			return $product_data;
		}

		function war_add_product_warranty( $product_data, $cart_item_key ) {
			global $woocommerce;

			$curr_product= $product_data['data'];
			$pa_warranty = false;

			if ( isset( $product_data['wcwar_pa_warranty'] ) && $product_data['wcwar_pa_warranty'] !== 'no_warranty' ) {
				$pa_warranty = $product_data['wcwar_pa_warranty'];
			}

			//$product_id = method_exists( $curr_product, 'get_id' ) ? absint( $curr_product->get_id() ) : ( absint( $product->variation_id ) !== 0 ? $product->variation_id : $product->id );
			$product_id = ( isset( $product_data['variation_id'] ) && $product_data['variation_id'] !== 0 ? $product_data['variation_id'] : $product_data['product_id'] );

			$curr_warranty = get_post_meta( $product_id, '_wcwar_warranty', true );

			if ( empty( $curr_warranty ) ) {
				if ( ( $default_warranty = get_option( 'wcwar_default_warranty', '' ) ) == '' ) {
					$curr_warranty = array( 'type' => 'no_warranty' );
				}
				else {
					$curr_warranty = array(
						'type' => 'preset_warranty',
						'preset' => $default_warranty
					);
				}
			}

			if ( $curr_warranty ) {

				if ( $curr_warranty['type'] == 'preset_warranty' ) {
					$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
					$curr_warranty = array_merge( $curr_warranty, $curr_preset);
				}
				else if ( $curr_warranty['type'] == 'quick_warranty' ) {

				}

				if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
					if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {
						if ( $pa_warranty === false ) {
							
						}
						else if ( $pa_warranty == 'not_selected' ) {
							if ( isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes' ) {

							}
							else {
								$product_data['wcwar_pa_price'] = $curr_warranty['paid_warranty'][0]['price'];
								if ( method_exists( $product_data['data'], 'set_price' ) ) {
									$product_data['data']->set_price( $product_data['data']->get_price() + $curr_warranty['paid_warranty'][0]['price'] );
								}
								else {
									$product_data['data']->adjust_price( $curr_warranty['paid_warranty'][0]['price'] );
								}
							}
						}
						else {
							if ( method_exists( $product_data['data'], 'set_price' ) ) {
								$product_data['data']->set_price( $product_data['data']->get_price() + $curr_warranty['paid_warranty'][$pa_warranty]['price'] );
							}
							else {
								$product_data['data']->adjust_price( $curr_warranty['paid_warranty'][$pa_warranty]['price'] );
							}
							$product_data['wcwar_pa_price'] = $curr_warranty['paid_warranty'][$product_data['wcwar_pa_warranty']]['price'];
						}
					}
				}

			}

			return $product_data;

		}

		function wc_get_cart_item_from_session( $session_data, $values, $key ) {

			if ( isset( $values['wcwar_pa_warranty'] ) ) {

				$session_data['wcwar_pa_warranty'] = $values['wcwar_pa_warranty'];

				$product_id = ( isset( $session_data['variation_id'] ) && $session_data['variation_id'] !== '' ? $session_data['variation_id'] : $session_data['product_id'] );

				$curr_warranty = get_post_meta( $product_id, '_wcwar_warranty', true );

				if ( $curr_warranty ) {

					if ( $curr_warranty['type'] == 'preset_warranty' ) {
						$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
						$curr_warranty = array_merge( $curr_warranty, $curr_preset);
					}
					else if ( $curr_warranty['type'] == 'quick_warranty' ) {

					}

					if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
						if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {
							if ( $session_data['wcwar_pa_warranty'] == '' || $session_data['wcwar_pa_warranty'] == 'no_warranty' ) {
								
							}
							else if ( $session_data['wcwar_pa_warranty'] == '' || $session_data['wcwar_pa_warranty'] == 'not_selected' ) {
								if ( isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes' ) {

								}
								else {
									$session_data['wcwar_pa_price'] = $curr_warranty['paid_warranty'][0]['price'];
								}
							}
							else {
								$session_data['wcwar_pa_price'] = $curr_warranty['paid_warranty'][$session_data['wcwar_pa_warranty']]['price'];
							}

						}
					}

				}



				$session_data = self::war_add_product_warranty( $session_data, $key );
			}

			return $session_data;

		}

		function war_order( $order ) {

			if ( sizeof( $order->get_items() ) > 0 ) {

				$curr_multi_req = get_option( 'wcwar_enable_multi_requests', 'no' );
				$curr_returns = get_option( 'wcwar_returns_no_warranty', 'no' );
				$war_pageid = self::wpml_get_id( get_option( 'war_settings_page' ) );

				$id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

				?>
				<div class="wcwar_warranty war_order">
				<h2><?php esc_html_e( 'Available Warranties for this Order', 'wcwar' ); ?></h2>
				<?php
					if ( !in_array( $order->get_status(), apply_filters( 'wcwar_warranty_order_status', array( 'completed' ), $order ) ) ) {
				?>
				<p class="wcwar_status blue">
					<span class="wcwar_info_icon"><i class="wcwar-info"></i></span> <?php esc_html_e( 'Your warranties will be available once your order is complete.', 'wcwar' ); ?>
				</p>
				<?php
					$curr_complete = get_post_meta( $id, '_ordered_date', true );
					$warranty_status_hold[] = '<p class="wcwar_status blue"><span class="wcwar_info_icon"><i class="wcwar-info"></i></span>' . esc_html__( 'Pending order', 'wcwar') . '</p>';

					$curr_notallowed = true;
				}
				else {
					$curr_complete = get_post_meta( $id, '_completed_date', true );

					$curr_notallowed = false;
				}
				?>
				<table class="shop_table wcwarranty">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Product', 'wcwar' ); ?></th>
							<th><?php esc_html_e( 'Warranty', 'wcwar' ); ?></th>
							<th><?php esc_html_e( 'Status', 'wcwar' ); ?></th>
							<th><?php esc_html_e( 'Action', 'wcwar' ); ?></th>
						</tr>
					</thead>
					<tbody>
				<?php

					$i=0;
					foreach( $order->get_items() as $key => $item ) {

						$i++;
						$request_button = true;
						$curr_link = null;
						$addon_msg = null;
						$warranty_status = array();

						if ( isset( $warranty_status_hold ) ) {
							$warranty_status = $warranty_status + $warranty_status_hold;
						}
					?>
						<tr>
							<td class="wcwar_myaccount_product"><span class="wcwar_item"><?php echo $item['name']; ?></span></td>
							<td class="wcwar_myaccount_warranty">
								<p class="wcwar_description">
					<?php
						$curr_args = array(
							'post_type' => 'wcwar_warranty_req',
							'orderby'   => 'date',
							'order'     => 'ASC',
							'post_status' => 'any',
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $id,
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => $key,
								)
							)
						 );
						$curr_req = get_posts( $curr_args );

						$curr_args = array(
							'order_id' => $id,
							'item_id' => $key,
							'multiple' => $item['qty'] > 1 ? true : false
						);
						$curr_request = esc_url( add_query_arg( $curr_args, get_permalink( $war_pageid ) ) );

						$curr_status = self::hlp_valid_warranty( $curr_complete, $item );

						$requested = false;

						if ( !empty( $curr_req ) ) {
		
							$i=0;

							$curr_addon = '';
							$req_count = count( $curr_req );

							foreach ( $curr_req as $req ) {

								$i++;

								$curr_numid = $curr_multi_req == 'yes' && $req_count > 1 ? ' #' . $i . ' ' : '';
								$curr_reqId = $req->ID;

								$curr_terms = get_the_terms( $curr_reqId, 'wcwar_warranty' );
								$curr_terms = reset( $curr_terms );

								$return = get_post_meta( $curr_reqId, '_wcwar_warranty_return_request', true );
								if ( $return !== '' && $return == 'return' ) {
									if ( !empty ( $curr_terms) && $curr_terms->slug == 'completed' ) {
										$curr_addon .= '<span class="wcwar_status grey"><span class="wcwar_info_icon"><i class="wcwar-cross"></i></span><span>' . esc_html__( 'This item has been returned to the store.', 'wcwar' ) . '</span></span>';
										$curr_link[] = '';
									}
									else if ( $req->post_status == 'pending' ) {
										$curr_addon .= '<span class="wcwar_status blue"><span class="wcwar_info_icon"><i class="wcwar-info"></i></span><span>' . esc_html__( 'Pending review', 'wcwar' ) . '</span></span>';
										$curr_link[] = '';
									}
									else if ( !empty ( $curr_terms) && $curr_terms->slug == 'rejected' ) {
										$curr_addon .= '<span class="wcwar_status grey"><span class="wcwar_info_icon"><i class="wcwar-cross"></i></span><span>' . esc_html__( 'Return rejected', 'wcwar' ) . '</span></span>';
										$curr_link[] = '<a href="' . get_permalink( $curr_reqId ) . '" class="wcwar_request_button button">' . esc_html__( 'View Request', 'wcwar' ) . '</a>';
									}
									else {
										$curr_addon .= '<span class="wcwar_status yellow"><span class="wcwar_info_icon"><i class="wcwar-info"></i></span><span>' . esc_html__( 'Return requested', 'wcwar' ) . '</span></span>';
										$curr_link[] = '<a href="' . get_permalink( $curr_reqId ) . '" class="wcwar_request_button button">' . esc_html__( 'View Request', 'wcwar' ) . '</a>';
									}
									$requested = true;
									$hlp_status = 'return-nowar';
								}
								else {
									if ( !empty ( $curr_terms) && $curr_terms->slug == 'completed' ) {
										$curr_addon .= '<span class="wcwar_status blue"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Completed', 'wcwar' ) . $curr_numid . '</span></span>';
										$curr_link[] = '<a href="' . get_permalink( $curr_reqId ) . '" class="wcwar_request_button button">' . esc_html__( 'View Request', 'wcwar' ) . $curr_numid . '</a>';
									}
									else if( !empty ( $curr_terms) && $curr_terms->slug == 'rejected' ) {
										$curr_addon .= '<span class="wcwar_status grey"><span class="wcwar_info_icon"><i class="wcwar-cross"></i></span><span>' . esc_html__( 'Warranty rejected', 'wcwar' ) . '</span></span>';
										$curr_link[] = '<a href="' . get_permalink( $curr_reqId ) . '" class="wcwar_request_button button">' . esc_html__( 'View Request', 'wcwar' ) . $curr_numid . '</a>';
									}
									else if ( !empty ( $curr_terms) && $req->post_status !== 'pending' ) {
										$curr_addon .= '<span class="wcwar_status yellow"><span class="wcwar_info_icon"><i class="wcwar-info"></i></span><span>' . esc_html__( 'Requested', 'wcwar' ) . '</span></span>';
										$curr_link[] = '<a href="' . get_permalink( $curr_reqId ) . '" class="wcwar_request_button button">' . esc_html__( 'View Request', 'wcwar' ) . $curr_numid . '</a>';
										$requested = true;
									}
									else {
										$curr_addon .= '<span class="wcwar_status blue"><span class="wcwar_info_icon"><i class="wcwar-info"></i></span><span>' . esc_html__( 'Pending review', 'wcwar') . '</span></span>';
										$curr_link[] = '';
										$requested = true;
									}
								}

								$curr_addon .= '<br/>';

							}

							$warranty_status[] = $curr_addon;

/*							if ( $requested === false && $curr_multi_req == 'yes' ) {
								$warranty_status[] = '<span class="wcwar_status green"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Valid', 'wcwar' ) . '</span></span>';
								$curr_link[] = '<a href="' . $curr_request . '" class="wcwar_request_button button">' . esc_html__( 'Request Warranty', 'wcwar' ) . '</a>';
							}*/
						}

						if ( $curr_notallowed === false && $curr_status === 'nowar' && $curr_returns == 'yes' && empty( $curr_req ) ) {

							$addon_msg = esc_html__( 'This item was sold without warranty', 'wcwar' );

							if ( self::hlp_valid_return( $curr_complete, $item ) === true ) {
								$addon_msg .= esc_html__( ', however you can still return it to the store', 'wcwar' );
								$warranty_status[] = '<p class="wcwar_status green"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span>' . esc_html__( 'Return available', 'wcwar') . '</p>';
								$curr_link[] = '<a href="' . $curr_request . '" class="wcwar_request_button button">' . esc_html__( 'Request Return', 'wcwar') . '</a>';
							}
							else {
								$warranty_status[] = '<p class="wcwar_status grey"><span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>' . esc_html__( 'No warranty', 'wcwar') . '</p>';
							}

							$request_button = false;

						}
						else if ( $curr_notallowed === false && $curr_status === 'nowar' && empty( $curr_req ) ) {
							$addon_msg = esc_html__( 'This item was sold without warranty', 'wcwar' );
							$warranty_status[] = '<p class="wcwar_status grey"><span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>' . esc_html__( 'No warranty', 'wcwar') . '</p>';
							$request_button = false;
						}
						else if ( $curr_notallowed === false && $curr_status && empty( $curr_req ) ) {
							$addon_msg = esc_html__( 'Warranty for this item has expired', 'wcwar' );
							$warranty_status[] = '<p class="wcwar_status grey"><span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>' . esc_html__( 'Expired', 'wcwar') . '</p>';
							$request_button = false;
						}
						if ( $curr_notallowed === false && $curr_status && $request_button === true ) {
							$request_button = false;
						}

						if ( isset( $item['wcwar_warranty'] ) ) {

							$curr_warranty = json_decode( $item['wcwar_warranty'], true );

							if ( $curr_warranty['type'] == 'preset_warranty' ) {
								$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
								$curr_warranty = array_merge( $curr_preset, $curr_warranty);
							}
							else if ( $curr_warranty['type'] == 'quick_warranty' ) {

							}

							if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {

								if ( $curr_warranty['quick'] == 'included_warranty' && isset( $curr_warranty['included_warranty'] ) ) {

							?>
										<span class="wcwar_block_wrap">
											<?php if ( isset( $curr_warranty['included_warranty']['thumb'] ) ) { ?>
												<img width="64" height="auto" src="<?php echo $curr_warranty['included_warranty']['thumb']; ?>" />
											<?php } ?>
											<span class="wcwar_warranty_period">
												<span>
													<?php echo esc_html__( 'Included Warranty', 'wcwar' ) . ' - ' . $curr_warranty['included_warranty']['period'] . ' ' . self::hlp_get_warranty_string( $curr_warranty['included_warranty']['period'], $curr_warranty['included_warranty']['type'] ); ?>
												</span>
												<?php
													if ( isset( $curr_warranty['included_warranty']['desc'] ) ) {
														echo '<small>' . $curr_warranty['included_warranty']['desc'] . '</small>';
													}
												?>
											</span>
										</span>
										<?php
											if ( $curr_notallowed === false && !isset( $warranty_status ) && $request_button === true ) {

												$warranty_status[] = '<span class="wcwar_status green"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Valid', 'wcwar' ) . '</span></span>';

											}
											else if ( $curr_notallowed === false && $requested === false && $curr_multi_req == 'yes' && $request_button === true ) {
												$warranty_status[] = '<span class="wcwar_status green"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Valid', 'wcwar' ) . '</span></span>';
												$curr_link[] = '<a href="' . $curr_request . '" class="wcwar_request_button button">' . esc_html__( 'Request Warranty', 'wcwar' ) . '</a>';
											}
/*											else if ( isset( $warranty_status ) ) {
												$warranty_status = '<span class="wcwar_status blue"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Completed', 'wcwar' ) . '</span></span>';
											}
											else {
												$warranty_status = '<span class="wcwar_status blue"><span class="wcwar_info_icon"><i class="wcwar-info"></i></span><span>' . esc_html__( 'Pending Order', 'wcwar' ) . '</span></span>';
											}*/
								}
								else if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {

									$curr = isset( $curr_warranty['paid_warranty']['selected'] ) ? $curr_warranty['paid_warranty']['selected'] : 'no_warranty';

									if ( $curr !== 'no_warranty' ) {
							?>
										<span class="wcwar_block_wrap">
											<?php if ( isset( $curr_warranty['paid_warranty'][$curr]['thumb'] ) && $curr_warranty['paid_warranty'][$curr]['thumb'] !== '' ) { ?>
												<img width="64" height="auto" src="<?php echo $curr_warranty['paid_warranty'][$curr]['thumb']; ?>" />
											<?php } ?>
											<span class="wcwar_warranty_period">
												<span>
													<?php echo esc_html__( 'Paid Warranty', 'wcwar') . ' (+' . wc_price( $curr_warranty['paid_warranty'][$curr]['price'] ) . ')' . ' - ' . $curr_warranty['paid_warranty'][$curr]['period'] . ' ' . self::hlp_get_warranty_string( $curr_warranty['paid_warranty'][$curr]['period'], $curr_warranty['paid_warranty'][$curr]['type'] ); ?>
												</span>
												<?php
													if ( isset( $curr_warranty['paid_warranty'][$curr]['desc'] ) ) {
														echo '<small>' . $curr_warranty['paid_warranty'][$curr]['desc'] . '</small>';
													}
												?>
											</span>
										</span>
										<?php
											if ( $curr_notallowed === false && !isset( $warranty_status ) && $request_button === true ) {
												$warranty_status[] = '<span class="wcwar_status green"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Valid', 'wcwar' ) . '</span></span>';
											}
											else if ( $curr_notallowed === false && $requested === false && $curr_multi_req == 'yes' && $request_button === true ) {
												$warranty_status[] = '<span class="wcwar_status green"><span class="wcwar_info_icon"><i class="wcwar-check"></i></span><span>' . esc_html__( 'Valid', 'wcwar' ) . '</span></span>';
												$curr_link[] = '<a href="' . $curr_request . '" class="wcwar_request_button button">' . esc_html__( 'Request Warranty', 'wcwar' ) . '</a>';
											}
									}
									else if ( isset( $hlp_status ) || !isset( $curr_warranty['paid_warranty']['selected'] ) ) {
										esc_html_e( 'This item was sold without warranty', 'wcwar' );
									}
								}
							}
							else {
								esc_html_e( 'This item was sold without warranty', 'wcwar' );
							}
						}
						else {
							esc_html_e( 'This item was sold without warranty', 'wcwar' );
						}

								if ( isset( $addon_msg ) ) {
								?>
									<small class="wcwar_addonmsg">* <?php echo $addon_msg; ?></small>
								<?php
								}

						?>
								</p>
							</td>
							<td class="wcwar_myaccount_status">
							<?php
								if ( is_array( $warranty_status ) ) {
									foreach ( $warranty_status as $wcstatus ) {
										echo $wcstatus;
									}
								}
								else {
									echo $warranty_status;
								}
							?>
							</td>
							<td class="wcwar_myaccount_request">
							<?php
								if ( $curr_notallowed === false && $request_button === true && !isset( $curr_link ) ) {
									echo '<a href="' . $curr_request . '" class="wcwar_request_button button">' . esc_html__( 'Request Warranty', 'wcwar') . '</a>';
								}
								else {
									if ( is_array( $curr_link ) ) {
										foreach ( $curr_link as $clink ) {
											echo $clink;
										}
									}
									else {
										echo $curr_link;
									}
								}
							?>
							</td>
						</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<?php
			}
		}

		function war_add_warranty_meta( $item_id, $item, $order_id ) {

			if ( is_object( $item ) ) {
				if ( ! property_exists( $item, 'legacy_values' ) ) {
					return;
				}
				$values = $item->legacy_values;
			}
			else {
				$values = $item;
			}


			$curr_product= $values['data'];

			$product_id = method_exists( $curr_product, 'get_id' ) ? absint( $curr_product->get_id() ) : ( absint( $product->variation_id ) !== 0 ? $product->variation_id : $product->id );

			$curr_warranty = get_post_meta( $product_id, '_wcwar_warranty', true );

			if ( empty( $curr_warranty ) ) {
				if ( ( $default_warranty = get_option( 'wcwar_default_warranty', '' ) ) == '' ) {
					$curr_warranty = array( 'type' => 'no_warranty' );
				}
				else {
					$curr_warranty = array(
						'type' => 'preset_warranty',
						'preset' => $default_warranty
					);
				}
			}

			if ( $curr_warranty['type'] == 'preset_warranty' ) {
				$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
				$curr_warranty = array_merge( $curr_warranty, $curr_preset);
			}
			else if ( $curr_warranty['type'] == 'quick_warranty' ) {

			}

			if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
				if ( $curr_warranty['quick'] == 'included_warranty' && isset( $curr_warranty['included_warranty'] ) ) {

				}
				else if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {
					if ( isset( $values['wcwar_pa_warranty'] ) && $values['wcwar_pa_warranty'] == 'not_selected') {
						if ( isset( $curr_warranty['paid_no_warranty'] ) && $curr_warranty['paid_no_warranty'] == 'yes' ) {

						}
						else {
							$curr_warranty['paid_warranty']['selected'] = 0;
						}
					}
					else if ( isset( $values['wcwar_pa_warranty'] ) ) {
						$curr_warranty['paid_warranty']['selected'] = $values['wcwar_pa_warranty'];
					}
				}
			}

			wc_add_order_item_meta( $item_id, '_wcwar_warranty', json_encode( $curr_warranty ) );

		}

		function war_hide_core_fileds( $array ) {
			$array[] = '_wcwar_warranty';
			return $array;
		}
		
		function war_items_warranty_column_header() {
			echo '<th class="wcwar_warranty_item">' . esc_html__( 'Warranty Type', 'wcwar') . '</th>';
			echo '<th class="wcwar_warranty_status">' . esc_html__( 'Warranty Status', 'wcwar') . '</th>';
		}

		function war_items_warranty_column( $curr_product, $curr_item, $curr_item_id ) {
			if ( $curr_product == NULL ) {
				return;
			}

			$curr_order = ( isset( get_current_screen()->parent_file ) && get_current_screen()->parent_file !== 'edit.php?post_type=wcwar_warranty_req' ? get_the_ID() : get_post_meta( get_the_ID(), '_wcwar_warranty_order_id', true ) );
			if ( $curr_order !== false ) {

				$curr_new_set = true;

				$curr_complete = get_post_meta( $curr_order, '_completed_date', true );

				$curr_fields = array( 'warranty', 'status' );

				$status = self::hlp_valid_warranty( $curr_complete, $curr_item);

				if ( $status && $status === 'nowar' ) {
					$curr_fields['warranty'] = esc_html__( 'No Warranty', 'wcwar' );
					$curr_fields['status'] = '<span class="wcwar_badge warranty_nowar">' . esc_html__( 'No Warranty', 'wcwar') . '</span>';
				}
				else if ( $status ) {
					$curr_fields['status'] = '<span class="wcwar_badge warranty_rejected">' . esc_html__( 'Warranty Expired', 'wcwar') . '</span>';
				}

				$curr_args = array(
					'post_type' => 'wcwar_warranty_req',
					'post_status' => 'any',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => '_wcwar_warranty_order_id',
							'value' => $curr_order,
						),
						array(
							'key' => '_wcwar_warranty_product_id',
							'value' => $curr_item_id,
						)
					)
				);
				$curr_req = get_posts( $curr_args );

				if ( !empty( $curr_req) ) {
					$get_terms = get_the_terms( $curr_req[0]->ID, 'wcwar_warranty' );
					if ( !is_array( $get_terms) ) {
						$curr_terms = (object) array( 'slug' => 'new' );
					}
					else {
						$curr_terms = reset( $get_terms );
					}

					if ( !empty ( $curr_terms) ) {

						$return = get_post_meta( $curr_req[0]->ID, '_wcwar_warranty_return_request', true );
						if ( $return !== '' && $return == 'return' ) {
							$curr_fields['status'] = '<span class="wcwar_badge warranty_only">' . esc_html__( 'Return', 'wcwar') . '</span>';
						}
						else {
							$curr_fields['status'] = '<span class="wcwar_badge return_only">' . esc_html__( 'Warranty', 'wcwar') . '</span>';
						}

						$switch_slug = $curr_terms->slug;
						if ( $switch_slug == 'new' ) {
							$curr_fields['status'] .= '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_new" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'New', 'wcwar') . '</a>';
							$curr_fields['status'] .= '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button view_warranty" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
						else if ( $switch_slug == 'processing' ) {
							$curr_fields['status'] .= '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_processing" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'Processing', 'wcwar') . '</a>';
							$curr_fields['status'] .= '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button view_warranty" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
						else if ( $switch_slug == 'completed' ) {
							$curr_fields['status'] .= '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_completed" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'Completed', 'wcwar') . '</a>';
							$curr_fields['status'] .= '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button view_warranty" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
						else if ( $switch_slug == 'rejected' ) {
							$curr_fields['status'] .= '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_rejected" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'Rejected', 'wcwar') . '</a>';
							$curr_fields['status'] .= '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button view_warranty" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
					}
					$curr_fields['status'] .= '<a href="#" class="wcwar_badge warranty_button warranty_change_status" data-id="' . $curr_req[0]->ID . '" title="' . esc_html__( 'Change Request Status', 'wcwar') . '"><i class="wcwar-change"></i></a>';
				}

				if ( isset( $curr_item['wcwar_warranty'] ) ) {

					$curr_warranty = json_decode( $curr_item['wcwar_warranty'], true );

					if ( $curr_warranty['type'] == 'preset_warranty' ) {
						$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
						$curr_warranty = array_merge( $curr_preset, $curr_warranty);
					}
					else if ( $curr_warranty['type'] == 'quick_warranty' ) {

					}
					
				}

				$curr_parent_args = array(
					'post_type' => 'wcwar_warranty_req',
					'post_status' => 'any',
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => '_wcwar_warranty_order_id',
							'value' => $curr_order,
						),
						array(
							'key' => '_wcwar_warranty_product_id',
							'value' => '-1',
						)
					)
				);
				$curr_parent_req = get_posts( $curr_parent_args );

				if ( !empty( $curr_parent_req) ) {
					$curr_parent = '&parent_id=' . $curr_parent_req[0]->ID;
				}
				else {
					$curr_parent = '';
				}

				$admin_url = admin_url( 'post-new.php?post_type=wcwar_warranty_req&order_id=' . $curr_order . $curr_parent . '&item_id=' . $curr_item_id ) . '&post_title=' . esc_html__( 'Request for Order', 'wcwar' ) . ' %23' . $curr_order . ' - ' . esc_html__( 'Item', 'wcwar') . ' %23' . $curr_item_id;

				if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
					$curr_new_set = false;
					if ( $curr_warranty['quick'] == 'included_warranty' && isset( $curr_warranty['included_warranty'] ) ) {
						$curr_fields['warranty'] = $curr_warranty['included_warranty']['period'] . ' ' . self::hlp_get_warranty_string( $curr_warranty['included_warranty']['period'], $curr_warranty['included_warranty']['type'] ) . ' ' . esc_html__( 'included warranty', 'wcwar' );
						if ( !isset( $curr_fields['status'] ) ) {
							$curr_fields['status'] = '<a href="' . $admin_url . '" class="wcwar_badge in_warranty" title="' . esc_html__( 'Create Warranty Request for this Item', 'wcwar') . '">' . esc_html__( 'In Warranty', 'wcwar') . '</a>';
							$curr_fields['status'] .= '<a href="' . $admin_url . '" class="wcwar_badge warranty_button wcwar_create" data-ids="' . $curr_order . '|' . $curr_item_id . '" title="' . esc_html__( 'Create Warranty Request for this Item', 'wcwar') . '"><i class="wcwar-new"></i></a>';
						}
					}
					else if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) && isset( $curr_warranty['paid_warranty']['selected'] ) ) {
						$curr = $curr_warranty['paid_warranty']['selected'];
						
						if ( $curr !== 'no_warranty' ) {

							$curr_fields['warranty'] = $curr_warranty['paid_warranty'][$curr]['period'] . ' ' . self::hlp_get_warranty_string( $curr_warranty['paid_warranty'][$curr]['period'], $curr_warranty['paid_warranty'][$curr]['type'] ) . ' ' . esc_html__( 'paid warranty', 'wcwar' ) . ' <small>(+ ' . wc_price( $curr_warranty['paid_warranty'][$curr]['price'] ) . ')</small>';
							

							if ( !isset( $curr_fields['status'] ) ) {
								$curr_fields['status'] = '<a href="' . $admin_url . '" class="wcwar_badge in_warranty" title="' . esc_html__( 'Create Warranty Request for this Item', 'wcwar') . '">' . esc_html__( 'In Warranty', 'wcwar') . '</a>';
								$curr_fields['status'] .= '<a href="' . $admin_url . '" class="wcwar_badge warranty_button wcwar_create" data-ids="' . $curr_order . '|' . $curr_item_id . '" title="' . esc_html__( 'Create Warranty Request for this Item', 'wcwar') . '"><i class="wcwar-new"></i></a>';
							}
						}
					}
				}

				$curr_admin_mod = get_option('wcwar_enable_multi_requests', 'yes' );

				if ( $curr_admin_mod == 'yes' && $curr_new_set ) {
					$curr_fields['status'] .= '<a href="' . $admin_url . '" class="wcwar_badge warranty_button wcwar_create" data-ids="' . $curr_order . '|' . $curr_item_id . '" title="' . esc_html__( 'Create Warranty Request for this Item', 'wcwar') . '"><i class="wcwar-new"></i></a>';
				}
				
			}
			else {
				$curr_fields['warranty'] = esc_html__( 'Please refresh', 'wcwar' );
				$curr_fields['status'] = esc_html__( 'Please refresh', 'wcwar' );
			}
	?>
			<td class="wcwar_warranty wcwar_warranty_item">
	<?php
				echo '<span class="wcwar_badge_war">' . $curr_fields['warranty'] . '</span>';
	?>
			</td>
			<td class="wcwar_warranty wcwar_warranty_status">
	<?php
				echo $curr_fields['status'];
	?>
			</td>
	<?php
		}

		function war_request_warranty_column_header( $columns ) {

			$columns['status'] = esc_html__( 'Request Status', 'wcwar' );
			return $columns;

		}

		function war_request_warranty_column( $column, $post_id ) {

			if ( $column == 'status' ) {
				$get_terms = get_the_terms( $post_id, 'wcwar_warranty' );
				if ( !is_array( $get_terms ) ) {
					$curr_terms = (object) array( 'slug' => 'new' );
				}
				else {
					$curr_terms = reset( $get_terms );
				}
				if ( !empty( $curr_terms ) ) {
					echo '<span class="wcwar_warranty_status">';
					$return = get_post_meta( $post_id, '_wcwar_warranty_return_request', true );
					if ( $return !== '' && $return == 'return' ) {
						echo '<span class="wcwar_badge warranty_only">' . esc_html__( 'Return', 'wcwar') . '</span>';
					}
					else {
						echo '<span class="wcwar_badge return_only">' . esc_html__( 'Warranty', 'wcwar') . '</span>';
					}
					$switch_slug = $curr_terms->slug;
					if ( $switch_slug == 'new' ) {
						echo '<span class="wcwar_badge wcwar_change warranty_new">' . esc_html__( 'New', 'wcwar') . '</span>';
					}
					else if ( $switch_slug == 'processing' ) {
						echo '<span class="wcwar_badge wcwar_change warranty_processing">' . esc_html__( 'Processing', 'wcwar') . '</span>';
					}
					else if ( $switch_slug == 'completed' ) {
						echo '<span class="wcwar_badge wcwar_change warranty_completed">' . esc_html__( 'Completed', 'wcwar') . '</span>';
					}
					else if ( $switch_slug == 'rejected' ) {
						echo '<span class="wcwar_badge wcwar_change warranty_rejected">' . esc_html__( 'Rejected', 'wcwar') . '</span>';
					}

					echo '<a href="' . get_permalink( $post_id ) . '" class="wcwar_badge warranty_button view_warranty" title="' . esc_html__( 'View Request Status', 'wcwar' ) . '"><i class="wcwar-view"></i></a>';
					echo '<a href="#" class="wcwar_badge warranty_button warranty_change_status" data-id="' . $post_id . '" title="' . esc_html__( 'Change Request Status', 'wcwar' ) . '"><i class="wcwar-change"></i></a>';
					echo '</span>';
				}

			}

		}

		function hlp_request_order( $query ){
			if( !is_admin() ) {
				return;
			}

			global $pagenow;

			if( 'edit.php' == $pagenow && isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'wcwar_warranty_req' && !isset( $_GET['orderby'] ) ) {
				$query->set( 'orderby', 'date' );
				$query->set( 'order', 'DESC' );
			}
		}

		function scr_view_request( $single_template ) {
			global $post;
			if ( $post->post_type == 'wcwar_warranty_req' ) {
				if ( get_option( 'wcwar_single_mode', 'new' ) == 'old' ) {
					$single_template = plugin_dir_path( __FILE__ ) . 'templates/content-request-old.php';
				}
				else{
					$single_template = plugin_dir_path( __FILE__ ) . 'templates/content-request.php';
				}
				return $single_template;
			}
			return $single_template;
		}

		function scr_comments( $comments_template ) {
			global $post;
			if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
				return;
			}
			if( $post->post_type == 'wcwar_warranty_req' ) {
				$comments_template = plugin_dir_path( __FILE__ ) . 'templates/comments-request.php';
				return $comments_template;
			}
			return $comments_template;
		}

		function hlp_get_warranty_string( $n, $t ) {
			switch ( $t ) {
				case 'days' :
					$out = _n( 'Day', 'Days', $n, 'wcwar' );
				break;
				case 'weeks' :
					$out = _n( 'Week', 'Weeks', $n, 'wcwar' );
				break;
				case 'months' :
					$out = _n( 'Month', 'Months', $n, 'wcwar' );
				break;
				case 'years' :
					$out = _n( 'Year', 'Years', $n, 'wcwar' );
				break;
				case 'lifetime' :
					$out = esc_html__( 'Lifetime', 'wcwar' );
				break;
				default :
					$out = '';
				break;
			}
			return $out;
		}

		function hlp_valid_warranty( $curr_complete, $item ) {

			if ( !isset( $item['wcwar_warranty'] ) ) {
				return true;
			}

			$curr_warranty = json_decode( $item['wcwar_warranty'], true );

			if ( $curr_warranty['type'] == 'preset_warranty' ) {
				$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true) ;
				$curr_warranty = array_merge( $curr_preset, $curr_warranty );
			}
			else if ( $curr_warranty['type'] == 'quick_warranty' ) {

			}

			if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
				if ( $curr_warranty['quick'] == 'included_warranty' && isset( $curr_warranty['included_warranty'] ) ) {

					if ( $curr_warranty['included_warranty']['type'] !== 'lifetime' ) {
						$curr_period = $curr_warranty['included_warranty']['period'];
						$curr_period .= ' ' . self::hlp_get_warranty_string( $curr_warranty['included_warranty']['period'], $curr_warranty['included_warranty']['type'] );
					}
					else {
						$curr_period = 'lifetime';
					}

				}
				else if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {

					$curr = isset( $curr_warranty['paid_warranty']['selected'] ) ? $curr_warranty['paid_warranty']['selected'] : 'no_warranty';

					if ( $curr !== 'no_warranty' ) {
						if ( isset( $curr_warranty['paid_warranty'][$curr]['type'] ) && $curr_warranty['paid_warranty'][$curr]['type'] !== 'lifetime' ) {
							$curr_period = $curr_warranty['paid_warranty'][$curr]['period'];
							$curr_period .= ' ' . self::hlp_get_warranty_string( $curr_warranty['paid_warranty'][$curr]['period'], $curr_warranty['paid_warranty'][$curr]['type'] );
						}
						else {
							$curr_period = 'lifetime';
						}
					}

				}
			}

			if ( !isset( $curr_period ) ) {
				return 'nowar';
			}

			if ( $curr_period == 'lifetime' ) {
				return false;
			}

			$curr_valid = strtotime( $curr_complete .' + ' . $curr_period );

			$curr_now = current_time( 'timestamp' );

			if ( $curr_valid && $curr_now > $curr_valid ) {
				return true;
			}

			return false;
		}

		function hlp_valid_return( $curr_complete, $item ) {

			$curr_returns = get_option( 'wcwar_enable_returns', 'no' );
			if ( $curr_returns == 'yes' ) {
				$curr_returns = get_option( 'wcwar_returns_period', '0' );
				if ( $curr_returns == '0' ) {
					return true;
				}

				$curr_period = $curr_returns . ' ' . self::hlp_get_warranty_string( $curr_returns, 'days' );

				$curr_valid = strtotime( $curr_complete .' + ' . $curr_period );

				$curr_now = current_time( 'timestamp' );

				if ( $curr_valid && $curr_now < $curr_valid ) {
					return true;
				}
			}

			return false;
		}

		function hlp_recursive_array_search( $needle, $haystack ) {
			foreach( $haystack as $key => $value ) {
				$current_key = $key;
				if( $needle === $value OR ( is_array( $value ) && self::hlp_recursive_array_search( $needle, $value ) !== false )) {
					return $current_key;
				}
			}
			return false;
		}

		function wc_add_screen_ids( $ids ) {
			$ids[] = 'edit-wcwar_warranty_req';
			$ids[] = 'wcwar_warranty_req';
			return $ids;
		}

		function war_register_request_metabox() {
			add_meta_box(
				'war-request',
				esc_html__( 'Warranties and Returns - Request Details', 'wcwar' ),
				array(&$this, 'war_request_metabox'),
				'wcwar_warranty_req',
				'normal',
				'high'
			);
		}

		function war_request_metabox( $object, $box ) {

			$curr_order = ( isset( $_GET['order_id'] ) ? $_GET['order_id'] : esc_attr( get_post_meta( $object->ID, '_wcwar_warranty_order_id', true ) ) );
			$curr_item = ( isset( $_GET['item_id'] ) ? $_GET['item_id'] : esc_attr( get_post_meta( $object->ID, '_wcwar_warranty_product_id', true ) ) );

			if ( $curr_order !== '' && $curr_item !== '' ) :

			if ( $object->post_parent !== '0' || $curr_item == '-1' ) {
				$parent = $object->post_parent;
				$curr_parent_order = ( $object->post_parent == '0' ? $object->ID : $object->post_parent );
				$str_type = esc_html__( 'Multiple Item Order', 'wcwar' );
			}
			else {
				$curr_parent_order = $object->ID;
				$str_type = esc_html__( 'Single Item Order', 'wcwar' );
			}

			$order = wc_get_order( $curr_order );

			$id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

			$metadata = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $curr_order, 'woocommerce_hidden_order_itemmeta' ) : $order->has_meta( $curr_item );

			$curr_complete = get_post_meta( $id, '_completed_date', true );

			$get_terms = get_the_terms( $object->ID, 'wcwar_warranty' );
			if ( !is_array( $get_terms) ) {
				$curr_selected_term = (object) array( 'slug' => 'new' );
			}
			else {
				$curr_selected_term = reset( $get_terms );
			}

			$curr_selected = ( empty( $curr_selected_term ) ? 'new' : $curr_selected_term->slug );
	?>
		<div id="woocommerce-order-items">
			<div class="war_warranty woocommerce_order_items_wrapper">
				<input type="hidden" name="wcwar_warranty_nonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__) . $object->ID); ?>" />
				<p class="wcwar_warranty_details">
					<span class="wcwar_meta_order"><i class="wcwar-order"></i> <?php esc_html_e( 'Order ', 'wcwar' ); ?> #<?php echo $curr_order; ?> - <?php echo $str_type; ?><a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $curr_order ) . '&action=edit' ) ); ?>" class="wcwar_badge warranty_button warranty_view_order" title="<?php esc_html_e( 'Manage Order', 'wcwar' ); ?>"><i class="wcwar-view"></i> <?php esc_html_e( 'Manage Order ', 'wcwar' ); ?></a></span>
				</p>
				<p class="wcwar_warranty_status">
					<span class="wcwar_badge_parent"><i class="wcwar-icon"></i> <?php echo ( isset( $parent ) ? esc_html__( 'Parent', 'wcwar' ) . ' ' : '' ) . esc_html__( 'Request Status', 'wcwar' ); ?>
					<?php
						$get_terms = get_the_terms( $curr_parent_order, 'wcwar_warranty' );
						if ( !is_array( $get_terms) ) {
							$curr_terms = (object) array( 'slug' => 'new' );
						}
						else {
							$curr_terms = reset( $get_terms );
						}
						if ( !empty ( $curr_terms) ) {
							$switch_slug = $curr_terms->slug;
							if ( $switch_slug == 'new' ) {
								echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $curr_parent_order ) . '&action=edit' ) ) . '" class="wcwar_badge wcwar_change warranty_new">' . esc_html__( 'New', 'wcwar') . '</a>';
							}
							else if ( $switch_slug == 'processing' ) {
								echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $curr_parent_order ) . '&action=edit' ) ) . '" class="wcwar_badge wcwar_change warranty_processing">' . esc_html__( 'Processing', 'wcwar') . '</a>';
							}
							else if ( $switch_slug == 'completed' ) {
								echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $curr_parent_order ) . '&action=edit' ) ) . '" class="wcwar_badge wcwar_change warranty_completed">' . esc_html__( 'Completed', 'wcwar') . '</a>';
							}
							else if ( $switch_slug == 'rejected' ) {
								echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $curr_parent_order ) . '&action=edit' ) ) . '" class="wcwar_badge wcwar_change warranty_rejected">' . esc_html__( 'Rejected', 'wcwar') . '</a>';
							}
							if ( $curr_item !== '-1' ) {
								echo '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $curr_parent_order ) . '&action=edit' ) ) . '" class="wcwar_badge warranty_button" title="' . esc_html__( 'View Parent Request Status', 'wcwar') . '"><i class="wcwar-view"></i></a>';
							}
							echo '<a href="#" class="wcwar_badge warranty_button warranty_change_status" data-id="' . $curr_parent_order . '" title="' . esc_html__( 'Change Request Status', 'wcwar') . '"><i class="wcwar-change"></i></a>';
						}
					?>
					</span>
				</p>
				<p class="wcwar_email">
					<?php esc_html_e( 'Send a quick E-Mail the Customer', 'wcwar' ); ?> : 
					<textarea class="war_message"></textarea>
					<small><?php echo esc_html__( 'Use these variables in Emails') . ' : <em>%order_id%, %order_date%, %completed_date%, %customer_name%, %warranty_link%</em>'; ?></small>
					<span class="wcwar_badge wcwar_label"><?php esc_html_e( 'Email Template', 'wcwar' ); ?></span>
					<select class="war_email_selected">
						<option value=""><?php esc_html_e( 'None', 'wcwar' ); ?></option>
						<?php
							$curr_presets = get_option('wcwar_email_templates' );
							if ( $curr_presets === false ) {
								$curr_presets = array();
							}
							if ( !empty( $curr_presets) ) {
								foreach ( $curr_presets as $k => $v ) {
							?>
									<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
							<?php
								}
							}
						?>
					</select>
					<a href="#" class="wcwar_badge war_load"><?php esc_html_e( 'Load', 'wcwar' ); ?></a>
					<a href="#" class="wcwar_badge war_save"><?php esc_html_e( 'Save', 'wcwar' ); ?></a>
					<a href="#" class="wcwar_badge war_delete"><?php esc_html_e( 'Delete Selected', 'wcwar' ); ?></a>
					<a href="#" class="wcwar_badge war_send" data-ids="<?php echo $curr_order . '|' . $object->ID;?>"><?php esc_html_e( 'Send Email', 'wcwar' ); ?></a>
				</p>
				<p style="display:none">
					<label for="_wcwar_warranty_status">
					<?php esc_html_e( 'Change Request Status', 'wcwar' ); ?> : 
					<?php

						$curr_terms = get_terms( 'wcwar_warranty', array('hide_empty' => false) );

						if ( !empty( $curr_terms) ) {
					?>
						<select name="_wcwar_warranty_status">
						<?php
							foreach ( $curr_terms as $k => $v ) {
						?>
							<option value="<?php echo $v->slug; ?>"<?php echo ( $curr_selected == $v->slug ? ' selected="selected"' : '' ); ?>><?php echo $v->name; ?></option>
						<?php
							}
						?>
						</select>
					<?php
						}
					?>
					</label>
				</p>
				<p style="display:none">
					<label for="_wcwar_warranty_order_id">
						<?php esc_html_e( 'Order ID', 'wcwar' ); ?> : 
						<input name="_wcwar_warranty_order_id" value="<?php echo $curr_order; ?>" />
					</label>
				</p>
				<p style="display:none">
					<label for="_wcwar_warranty_product_id">
						<?php esc_html_e( 'Item ID', 'wcwar' ); ?> : 
						<input name="_wcwar_warranty_product_id" value="<?php echo $curr_item; ?>" />
					</label>
				</p>
				<?php
					if ( isset( $_GET['parent_id'] ) ) {
				?>
				<p style="display:none">
					<label for="parent_id">
						<?php esc_html_e( 'Parent ID', 'wcwar' ); ?> : 
						<input name="parent_id" value="<?php echo $_GET['parent_id']; ?>" />
					</label>
				</p>
				<?php
					}
				?>

				<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
					<thead>
						<tr>
							<th class="item" colspan="2"><?php esc_html_e( 'Item', 'wcwar' ); ?></th>

							<?php do_action( 'woocommerce_admin_order_item_headers' ); ?>

							<th class="quantity"><?php esc_html_e( 'Qty', 'wcwar' ); ?></th>

							<th class="line_cost"><?php esc_html_e( 'Total', 'wcwar' ); ?></th>

							<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
						</tr>
					</thead>
					<tbody id="order_line_items">
				<?php

				$curr_mod = (int) get_post_meta( get_the_ID(), '_wcwar_warranty_product_id', true );

				foreach( $order->get_items() as $item_id => $item ) {

					if ( $curr_mod !== -1 ) {
						if ( !( $curr_mod == $item_id) ) {
							$class = ' not_active';
						}
						else {
							$class = ' active';
						}
					}
					else {

						$curr_args = array(
							'post_type' => 'wcwar_warranty_req',
							'post_status' => 'any',
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $curr_order,
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => $item_id,
								)
							)
						);
						$curr_req = get_posts( $curr_args );

					}

					$_product = wc_get_product( $item['product_id'] );
				?>
					<tr class="item <?php echo apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item ); ?>" data-order_item_id="<?php echo $item_id; ?>">
						<td class="thumb">
							<?php
								if ( $_product ) :
									$id = method_exists( $_product, 'get_id' ) ? $_product->get_id() : $_product->id;
							?>
								<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $id ) . '&action=edit' ) ); ?>" class="tips wc-order-item-thumbnail" data-tip="<?php

									echo '<strong>' . esc_html__( 'Product ID:', 'woocommerce' ) . '</strong> ' . absint( $item['product_id'] );

									if ( $item['variation_id'] && 'product_variation' === get_post_type( $item['variation_id'] ) ) {
										echo '<br/><strong>' . esc_html__( 'Variation ID:', 'woocommerce' ) . '</strong> ' . absint( $item['variation_id'] );
									} elseif ( $item['variation_id'] ) {
										echo '<br/><strong>' . esc_html__( 'Variation ID:', 'woocommerce' ) . '</strong> ' . absint( $item['variation_id'] ) . ' (' . esc_html__( 'No longer exists', 'woocommerce' ) . ')';
									}

									if ( $_product && $_product->get_sku() ) {
										echo '<br/><strong>' . esc_html__( 'Product SKU:', 'woocommerce' ).'</strong> ' . esc_html( $_product->get_sku() );
									}

									if ( $_product && isset( $_product->variation_data ) ) {
										echo '<br/>' . wc_get_formatted_variation( $_product->variation_data, true );
									}

								?>"><?php echo $_product->get_image( 'shop_thumbnail', array( 'title' => '' ) ); ?></a>
							<?php else : ?>
								<?php echo wc_placeholder_img( 'shop_thumbnail' ); ?>
							<?php endif; ?>
						</td>
						<td class="name">

							<?php echo ( $_product && $_product->get_sku() ) ? esc_html( $_product->get_sku() ) . ' &ndash; ' : ''; ?>

							<?php if ( $_product ) : ?>
								<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $id ) . '&action=edit' ) ); ?>">
									<?php echo esc_html( $item['name'] ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( $item['name'] ); ?>
							<?php endif; ?>

							<input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
							<input type="hidden" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" value="<?php echo isset( $item['tax_class'] ) ? esc_attr( $item['tax_class'] ) : ''; ?>" />

							<div class="view">
								<?php
									global $wpdb;
									if ( $metadata ) {
										echo '<table cellspacing="0" class="display_meta">';
										foreach ( $metadata as $meta ) {

											if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
												'_qty',
												'_tax_class',
												'_product_id',
												'_variation_id',
												'_line_subtotal',
												'_line_subtotal_tax',
												'_line_total',
												'_line_tax',
											) ) ) ) {
												continue;
											}

											if ( is_serialized( $meta['meta_value'] ) ) {
												continue;
											}

											if ( taxonomy_exists( $meta['meta_key'] ) ) {
												$term           = get_term_by( 'slug', $meta['meta_value'], $meta['meta_key'] );
												$attribute_name = str_replace( 'pa_', '', wc_clean( $meta['meta_key'] ) );
												$attribute      = $wpdb->get_var(
													$wpdb->prepare( "
															SELECT attribute_label
															FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
															WHERE attribute_name = %s;
														",
														$attribute_name
													)
												);

												$meta['meta_key']   = ( ! is_wp_error( $attribute ) && $attribute ) ? $attribute : $attribute_name;
												$meta['meta_value'] = ( isset( $term->name ) ) ? $term->name : $meta['meta_value'];
											}

											echo '<tr><th>' . wp_kses_post( urldecode( $meta['meta_key'] ) ) . ':</th><td>' . wp_kses_post( wpautop( urldecode( $meta['meta_value'] ) ) ) . '</td></tr>';
										}
										echo '</table>';
									}
								?>
							</div>
							<div class="edit" style="display: none;">
								<table class="meta" cellspacing="0">
									<tbody class="meta_items">
									<?php
										if ( $metadata ) {
											foreach ( $metadata as $meta ) {
												if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
													'_qty',
													'_tax_class',
													'_product_id',
													'_variation_id',
													'_line_subtotal',
													'_line_subtotal_tax',
													'_line_total',
													'_line_tax',
												) ) ) ) {
													continue;
												}

												if ( is_serialized( $meta['meta_value'] ) ) {
													continue;
												}

												$meta['meta_key']   = urldecode( $meta['meta_key'] );
												$meta['meta_value'] = esc_textarea( urldecode( $meta['meta_value'] ) ); // using a <textarea />
												$meta['meta_id']    = absint( $meta['meta_id'] );

												echo '<tr data-meta_id="' . esc_attr( $meta['meta_id'] ) . '">
													<td>
														<input type="text" name="meta_key[' . $meta['meta_id'] . ']" value="' . esc_attr( $meta['meta_key'] ) . '" />
														<textarea name="meta_value[' . $meta['meta_id'] . ']">' . $meta['meta_value'] . '</textarea>
													</td>
													<td width="1%"><button class="remove_order_item_meta button">&times;</button></td>
												</tr>';
											}
										}
									?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="4"><button class="add_order_item_meta button"><?php esc_html_e( 'Add&nbsp;meta', 'woocommerce' ); ?></button></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</td>

						<?php do_action( 'woocommerce_admin_order_item_values', $_product, $item, absint( $item_id ) ); ?>

						<td class="quantity" width="1%">
							<div class="view">
								<?php
									echo ( isset( $item['qty'] ) ) ? esc_html( $item['qty'] ) : '';

									if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
										echo '<small class="refunded">-' . $refunded_qty . '</small>';
									}
								?>
							</div>
							<div class="edit" style="display: none;">
								<?php $item_qty = esc_attr( $item['qty'] ); ?>
								<input type="number" step="<?php echo apply_filters( 'woocommerce_quantity_input_step', '1', $_product ); ?>" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo $item_qty; ?>" data-qty="<?php echo $item_qty; ?>" size="4" class="quantity" />
							</div>
							<div class="refund" style="display: none;">
								<input type="number" step="<?php echo apply_filters( 'woocommerce_quantity_input_step', '1', $_product ); ?>" min="0" max="<?php echo $item['qty']; ?>" autocomplete="off" name="refund_order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" size="4" class="refund_order_item_qty" />
							</div>
						</td>

						<td class="line_cost" width="1%">
							<div class="view">
								<?php
									if ( isset( $item['line_total'] ) ) {
										if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
											echo '<del>' . wc_price( $item['line_subtotal'] ) . '</del> ';
										}

										echo wc_price( $item['line_total'] );
									}

									if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
										echo '<small class="refunded">-' . wc_price( $refunded ) . '</small>';
									}
								?>
							</div>
							<div class="edit" style="display: none;">
								<div class="split-input">
									<?php $item_total = ( isset( $item['line_total'] ) ) ? esc_attr( wc_format_localized_price( $item['line_total'] ) ) : ''; ?>
									<input type="text" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo $item_total; ?>" class="line_total wc_input_price tips" data-tip="<?php esc_html_e( 'After pre-tax discounts.', 'woocommerce' ); ?>" data-total="<?php echo $item_total; ?>" />

									<?php $item_subtotal = ( isset( $item['line_subtotal'] ) ) ? esc_attr( wc_format_localized_price( $item['line_subtotal'] ) ) : ''; ?>
									<input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" value="<?php echo $item_subtotal; ?>" class="line_subtotal wc_input_price tips" data-tip="<?php esc_html_e( 'Before pre-tax discounts.', 'woocommerce' ); ?>" data-subtotal="<?php echo $item_subtotal; ?>" />
								</div>
							</div>
							<div class="refund" style="display: none;">
								<input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" class="refund_line_total wc_input_price" />
							</div>
						</td>
						<td class="wc-order-edit-line-item">
							<?php if ( $order->is_editable() ) : ?>
								<div class="wc-order-edit-line-item-actions">
									<a class="edit-order-item" href="#"></a><a class="delete-order-item" href="#"></a>
								</div>
							<?php endif; ?>
						</td>
					</tr>
				<?php
					}
				?>
					</tbody>
				</table>
			</div>
		</div>
	<?php
		else :
	?>
		<div id="woocommerce-order-items">
			<div class="war_warranty woocommerce_order_items_wrapper">
				<input type="hidden" name="wcwar_warranty_nonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__) . $object->ID); ?>" />
				<p>
					<label for="_wcwar_warranty_order_id">
						<?php esc_html_e( 'Please enter Order ID', 'wcwar' ); ?> : 
						<input name="_wcwar_warranty_order_id" value="<?php echo $curr_order; ?>" />
					</label>
				</p>
			</div>
		</div>
	<?php
		
		endif;
		}

		function war_save_request_metabox( $post_id, $post ) {

			if ( isset( $_POST['wcwar_warranty_nonce'] ) && !wp_verify_nonce( $_POST['wcwar_warranty_nonce'], plugin_basename(__FILE__) . $post_id) ) {
				return $post_id;
			}

			$post_type = get_post_type_object( $post->post_type );

			if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
				return $post_id;

			if ( isset( $_POST['_wcwar_warranty_order_id'] ) && isset( $_POST['_wcwar_warranty_product_id'] ) ) {

				$get_terms = get_the_terms( $post_id, 'wcwar_warranty' );
				if ( !is_array( $get_terms) ) {
					$curr_selected_term = (object) array( 'slug' => 'new' );
				}
				else {
					$curr_selected_term = reset( $get_terms );
				}

				$curr_selected = $curr_selected_term->slug;

				wp_set_post_terms( $post_id, $curr_selected, 'wcwar_warranty' );

				$new_meta_values = array();

				$new_meta_values[] = ( isset( $_POST['_wcwar_warranty_order_id'] ) ? $_POST['_wcwar_warranty_order_id'] : '' );
				$new_meta_values[] = ( isset( $_POST['_wcwar_warranty_product_id'] ) ? $_POST['_wcwar_warranty_product_id'] : '' );

				$meta_keys = array();

				$meta_keys[] = '_wcwar_warranty_order_id';
				$meta_keys[] = '_wcwar_warranty_product_id';


				$meta_values = array();

				$i = 0;

				foreach ( $meta_keys as $meta_key ) {

					$meta_value = get_post_meta( $post_id, $meta_key, true );
					
					if ( $new_meta_values[$i] && '' == $meta_value )
						add_post_meta( $post_id, $meta_key, $new_meta_values[$i], true );

					elseif ( $new_meta_values[$i] && $new_meta_values[$i] != $meta_value )
						update_post_meta( $post_id, $meta_key, $new_meta_values[$i] );

					elseif ( '' == $new_meta_values[$i] && $meta_value )
						delete_post_meta( $post_id, $meta_key, $meta_value );

					$i++;

				}

			}
			else if ( isset( $_POST['_wcwar_warranty_order_id'] ) && !isset( $_POST['_wcwar_warranty_product_id'] ) ) {

				$order = wc_get_order( $_POST['_wcwar_warranty_order_id'] );


				if ( !empty( $order) ) {

					$curr_items = $order->get_items();

					if ( count( $curr_items) > 1 ) {
						if ( !update_post_meta ( $post_id, '_wcwar_warranty_order_id', $_POST['_wcwar_warranty_order_id'] ) ) {
							add_post_meta( $post_id, '_wcwar_warranty_order_id', $_POST['_wcwar_warranty_order_id'], true );
						};
						if ( !update_post_meta ( $post_id, '_wcwar_warranty_product_id', '-1' ) ) {
							add_post_meta( $post_id, '_wcwar_warranty_product_id', '-1', true );
						};
					}
					else {
						if ( !update_post_meta ( $post_id, '_wcwar_warranty_order_id', $_POST['_wcwar_warranty_order_id'] ) ) {
							add_post_meta( $post_id, '_wcwar_warranty_order_id', $_POST['_wcwar_warranty_order_id'], true );
						};
						if ( !update_post_meta ( $post_id, '_wcwar_warranty_product_id', current(array_keys( $curr_items)) ) ) {
							add_post_meta( $post_id, '_wcwar_warranty_product_id', current(array_keys( $curr_items)), true );
						};
					}

					$get_terms = get_the_terms( $post_id, 'wcwar_warranty' );
					if ( !is_array( $get_terms) ) {
						$curr_selected_term = (object) array( 'slug' => 'new' );
					}
					else {
						$curr_selected_term = reset( $get_terms );
					}

					$curr_selected = $curr_selected_term->slug;

					wp_set_post_terms( $post_id, $curr_selected, 'wcwar_warranty' );

				}
			}

		}

		function war_ajax_et_save() {
			$curr_name = $_POST['curr_name'];

			$curr_data = array();
			$curr_data[$curr_name] = $_POST['curr_email'];

			$curr_presets = get_option('wcwar_email_templates' );

			if ( $curr_presets === false ) {
				$curr_presets = array();
			}

			if ( isset( $curr_presets) && is_array( $curr_presets) ) {
				if ( array_key_exists( $curr_name, $curr_presets) ) {
					unset( $curr_presets[$curr_name] );
				}
				$curr_presets = $curr_presets + $curr_data;
				update_option('wcwar_email_templates', $curr_presets);
				die( '1' );
				exit;
			}

			die();
			exit;
		}

		function war_ajax_et_load() {

			$curr_name = $_POST['curr_name'];

			$curr_presets = get_option('wcwar_email_templates' );
			if ( isset( $curr_presets) && !empty( $curr_presets) && is_array( $curr_presets) ) {
				if ( array_key_exists( $curr_name, $curr_presets) ) {
					die(json_encode( $curr_presets[$curr_name] ));
					exit;
				}
				die( '1' );
				exit;
			}

			die();
			exit;

		}

		function war_ajax_et_delete() {
			$curr_name = $_POST['curr_name'];

			$curr_presets = get_option('wcwar_email_templates' );
			if ( isset( $curr_presets) && !empty( $curr_presets) && is_array( $curr_presets) ) {
				if ( array_key_exists( $curr_name, $curr_presets) ) {
					unset( $curr_presets[$curr_name] );
					update_option('wcwar_email_templates', $curr_presets);
				}
				die( '1' );
				exit;
			}

			die();
			exit;
		}

		function war_ajax_email_send() {
			if ( !isset( $_POST['order_id'] ) && !isset( $_POST['request_id'] ) && !isset( $_POST['email'] ) ) {
				die();
				exit;
			}

			$order = wc_get_order( $_POST['order_id'] );

			if ( !empty( $order) ) {

				$curr_email = $_POST['email'];

				$curr_vars = array( '%order_id%', '%order_date%', '%completed_date%', '%customer_name%', '%warranty_link%' );
				$curr_vals = array(
					$_POST['order_id'],
					get_post_meta( $_POST['order_id'], '_ordered_date', true ),
					get_post_meta( $_POST['order_id'], '_completed_date', true ),
					$order->billing_first_name . ' ' . $order->billing_last_name,
					'<a href="' . get_permalink( $_POST['request_id'] ) . '">' . get_permalink( $_POST['request_id'] ) . '</a>'
				);

				$ready_email = str_replace( $curr_vars, $curr_vals, $curr_email);

				$curr_reply_from = get_option('wcwar_email_name', get_bloginfo('name'));
				$curr_reply_to = get_option('wcwar_email_address', get_bloginfo('admin_email'));

				$curr_headers = array();
				$curr_headers[] = 'From: ' . $curr_reply_from . ' <' . $curr_reply_to . '>';

				$curr_bcc = get_option('wcwar_email_bcc', '' );

				if ( $curr_bcc !== '' ) {
					$curr_headers[] = 'Bcc: ' . $curr_bcc;
				}

				$curr_subject = esc_html__( 'Warranty Request for order #', 'wcwar') . $_POST['order_id'] . ' - ' . get_bloginfo('name' );

				wc_mail( $order->billing_email, $curr_subject, $ready_email, $curr_headers, '' );
				
				die( '1' );
				exit;

			}
			die();
			exit;
		}

		function war_ajax_create() {
			if ( !isset( $_POST['order_id'] ) && !isset( $_POST['item_id'] ) ) {
				die();
				exit;
			}

			$order = wc_get_order( $_POST['order_id'] );

			if ( !empty( $order) ) {

				$curr_ordered = $order->get_items();

				if ( count( $curr_ordered) > 1 ) {

					$curr_parent_args = array(
						'post_type' => 'wcwar_warranty_req',
						'post_status' => 'any',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key' => '_wcwar_warranty_order_id',
								'value' => $_POST['order_id'],
							),
							array(
								'key' => '_wcwar_warranty_product_id',
								'value' => -1,
							)
						)
					);
					$curr_parent_req = get_posts( $curr_parent_args );

					if ( empty( $curr_parent_req) ) {

						$curr_parent_create = array(
							'post_title'    => esc_html__( 'Request for Order #', 'wcwar' ) . $_POST['order_id'],
							'post_content'  => esc_html__( 'This is a parent warranty request. Check child requests for details.', 'wcwar' ),
							'post_name'     => 'r_' . $_POST['order_id'],
							'post_status'   => get_option( 'wcwar_default_post', 'pending' ),
							'post_author'   => 1,
							'post_type'     => 'wcwar_warranty_req',
							'comment_status'=> 'open',
							'ping_status'   => 'closed'
						);
						$curr_parent_request = wp_insert_post( $curr_parent_create );

						wp_set_post_terms( $curr_parent_request, 'new', 'wcwar_warranty' );

						$curr_parent_meta = array(
							'order_id'      => $_POST['order_id'],
							'product_id'    => -1,
							'warranty_id'   => $_POST['order_id'] . '-' . $curr_parent_request
						);

						foreach ( $curr_parent_meta as $k => $v ) {
							add_post_meta( $curr_parent_request, '_wcwar_warranty_' . $k, $v, true );
						}
					}

					
					$curr_create = array(
						'post_title'    => esc_html__( 'Request for Order #', 'wcwar' ) . $_POST['order_id'] . ' - ' . esc_html__( 'Item #', 'wcwar') . $_POST['item_id'],
						'post_content'  => '',
						'post_name'     => 'i_' . $_POST['order_id'] . '_' . $_POST['item_id'],
						'post_status'   => get_option( 'wcwar_default_post', 'pending' ),
						'post_author'   => 1,
						'post_type'     => 'wcwar_warranty_req',
						'comment_status'=> 'open',
						'ping_status'   => 'closed',
						'post_parent'   => ( isset( $curr_parent_request) ? $curr_parent_request : $curr_parent_req[0]->ID )
					);
					$curr_request = wp_insert_post( $curr_create );

					wp_set_post_terms( $curr_request, 'new', 'wcwar_warranty' );

					$curr_meta = array(
						'order_id'      => $_POST['order_id'],
						'product_id'    => $_POST['item_id'],
						'warranty_id'   => $_POST['order_id'] . '-' . $_POST['item_id'] . '-' . $curr_request
					);

						if ( isset( $_POST['wcwar_return'] ) ) {
							$curr_meta= $curr_meta + array( 'return_request' => 'return' );
						}

					foreach ( $curr_meta as $k => $v ) {
						add_post_meta( $curr_request, '_wcwar_warranty_' . $k, $v, true );
					}

				}
				else {

					$curr_create = array(
						'post_title'    => esc_html__( 'Request for Order #', 'wcwar' ) . $_POST['order_id'] . ' - ' . esc_html__( 'Item #', 'wcwar') . $_POST['item_id'],
						'post_content'  => '',
						'post_name'     => 'r_' . $_POST['order_id'] . '_' . $_POST['item_id'],
						'post_status'   => get_option( 'wcwar_default_post', 'pending' ),
						'post_author'   => 1,
						'post_type'     => 'wcwar_warranty_req',
						'comment_status'=> 'open',
						'ping_status'   => 'closed'
					);
					$curr_request = wp_insert_post( $curr_create );

					wp_set_post_terms( $curr_request, 'new', 'wcwar_warranty' );

					$curr_meta = array(
						'order_id'      => $_POST['order_id'],
						'product_id'    => $_POST['item_id'],
						'warranty_id'   => $_POST['order_id'] . '-' . $_POST['item_id'] . '-' . $curr_request
					);

						if ( isset( $_POST['wcwar_return'] ) ) {
							$curr_meta= $curr_meta + array( 'return_request' => 'return' );
						}

					foreach ( $curr_meta as $k => $v ) {
						add_post_meta( $curr_request, '_wcwar_warranty_' . $k, $v, true );
					}

				}


				die( '1' );
				exit;
			}
			die();
			exit;
		}

		function war_ajax_status() {
			$request_id = intval( $_POST['request_id'] );

			$curr_terms = get_terms( 'wcwar_warranty', array('hide_empty' => false) );

			$curr_selected_term = get_the_terms( $request_id, 'wcwar_warranty' );
			$curr_selected_term = reset( $curr_selected_term );
			$curr_selected = ( empty( $curr_selected_term) ? 'new' : $curr_selected_term->slug );

			if ( !empty( $curr_terms) ) {
		?>
			<div id="wcwar_change_status">
				<ul>
					<li class="wcwar_close"><i class="wcwar-close"></i></li>
				<?php
					foreach ( $curr_terms as $k => $v ) {
				?>
					<li data-key="<?php echo $v->slug; ?>"<?php echo ( $curr_selected == $v->slug ? ' class="wcwar_selected"' : '' ); ?>><?php echo $v->name; ?></li>
				<?php
					}
				?>
				</ul>
			</div>
		<?php
			}
			die(1);
			exit;
		}


		function war_ajax_status_change() {
			$request_id = intval( $_POST['request_id'] );
			$new_status = $_POST['request_status'];

			wp_set_post_terms( $request_id, $new_status, 'wcwar_warranty' );

			die( '1' );
			exit;
		}

		function wc_add_order_request_status( $curr_order ) {
		?>
			<p>
		<?php

			$curr_items = $curr_order->get_items();
			$id = method_exists( $curr_order, 'get_id' ) ? $curr_order->get_id() : $curr_order->ID;

				if ( count( $curr_items) > 1 ) {
					$curr_args = array(
						'post_type' => 'wcwar_warranty_req',
						'post_status' => 'any',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key' => '_wcwar_warranty_order_id',
								'value' => $id,
							),
							array(
								'key' => '_wcwar_warranty_product_id',
								'value' => '-1',
							)
						)
					);
					echo '<p class="form-field form-field-wide wcwar_warranty_status multi-order">';
					esc_html_e( 'Warranty Status (Multi Item Order):', 'wcwar' );
				}
				else {
					$curr_args = array(
						'post_type' => 'wcwar_warranty_req',
						'post_status' => 'any',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key' => '_wcwar_warranty_order_id',
								'value' => $id,
							)
						)
					);
					echo '<p class="form-field form-field-wide wcwar_warranty_status single-order">';
					esc_html_e( 'Warranty Status (Single Item Order):', 'wcwar' );
				}
				echo '<br/>';

				$curr_complete = get_post_meta( $id, '_completed_date', true );

				$curr_fields = array( 'warranty', 'status' );

				$curr_req = get_posts( $curr_args );

				if ( !empty( $curr_req) ) {
					$curr_terms = get_the_terms( $curr_req[0]->ID, 'wcwar_warranty' );
					$curr_terms = reset( $curr_terms );
					if ( !empty ( $curr_terms) ) {
						$switch_slug = $curr_terms->slug;
						if ( $switch_slug == 'new' ) {
							echo '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_new" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'New', 'wcwar') . '</a>';
							echo'<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
						else if ( $switch_slug == 'processing' ) {
							echo '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_processing" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'Processing', 'wcwar') . '</a>';
							echo '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
						else if ( $switch_slug == 'completed' ) {
							echo '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_completed" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'Completed', 'wcwar') . '</a>';
							echo '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
						else if ( $switch_slug == 'rejected' ) {
							$curr_fields['status'] = '<a href="'.$curr_req[0]->guid.'" class="wcwar_badge wcwar_change warranty_rejected" title="' . esc_html__( 'View Request', 'wcwar') . '">' . esc_html__( 'Rejected', 'wcwar') . '</a>';
							echo '<a href="' . get_edit_post_link( $curr_req[0]->ID ) . '" class="wcwar_badge warranty_button" title="' . esc_html__( 'View Request', 'wcwar') . '"><i class="wcwar-view"></i></a>';
						}
					}
					echo '<a href="#" class="wcwar_badge warranty_button warranty_change_status" data-id="' . $curr_req[0]->ID . '" title="' . esc_html__( 'Change Request Status', 'wcwar') . '"><i class="wcwar-change"></i></a>';
				}
				else {
					echo '<span class="wcwar_badge warranty_notrequested" >' . esc_html__( 'This order has no warranty requests', 'wcwar') . '</span>';
				}

	?>
			</p>
	<?php
		}


		function wcwar_sc_request() {

			ob_start();

			$has_error = false;

			if ( isset( $_POST['war_submit'] ) && $_POST['war_submit'] == 'true' ) {

				if ( !isset( $_POST['wcwar_return'] ) ) {

					$curr_multi_req = get_option( 'wcwar_enable_multi_requests', 'no' );

					if ( $curr_multi_req == 'no' ) {

						$curr_args = array(
							'post_type' => 'wcwar_warranty_req',
							'post_status' => 'any',
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $_POST['order_id'],
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => $_POST['item_id'],
								)
							)
						);
						$curr_req = get_posts( $curr_args );

						if ( !empty( $curr_req ) ) {

							$m=0;
							foreach( $curr_req as $curr_reqq ) {
								$check_return = get_post_meta( $curr_reqq->ID, '_wcwar_warranty_return_request', true );
								if ( $check_return == 'return' ) {
									unset( $curr_req[$m] );
								}
								$m++;
							}
						}

						if ( !empty( $curr_req ) ) {
							$curr_req = array_values($curr_req);
						?>
							<p class="wcwar_form_error">
								<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
							<?php
								echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar') . ' <a href="'.$curr_req[0]->guid.'">' . esc_html__( 'link', 'wcwar') . '</a>.';
							?>
							</p>
						<?php
							$output = ob_get_clean();
							return $output;
						}
					}

					$curr_form_fields = get_option( 'wcwar_form', '' );

					if ( $curr_form_fields !== '' ) {
						$curr_fields = json_decode( $curr_form_fields, true );
					}
					else {
						$curr_fields = json_decode(stripslashes('{"fields":[{"label":"Reason for requesting warranty","field_type":"radio","required":true,"field_options":{"options":[{"label":"Item was damaged","checked":false},{"label":"Item was broken","checked":false},{"label":"Nothing wrong, just returning","checked":false}],"include_other_option":true},"cid":"c10"}]}'), true );
					}

					$request_content = '';


					if ( isset( $_POST['wcwar_qty'] ) ) {
						$request_content .= '<strong>' . esc_html__( 'Item was ordered multiple times, the requested number of item warranties is: ', 'wcwar' ) . intval( $_POST['wcwar_qty'] ) . '</strong>
';
					}

					foreach ( $curr_fields['fields'] as $cfld ) {

						switch ( $cfld['field_type'] ) :
							case 'text' :
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid']] ) && $_POST['wcwar_' . $cfld['cid']] !== '' ) {
									$request_content .= $cfld['label'] . ' : ' . wp_strip_all_tags( $_POST['wcwar_' . $cfld['cid']] ) . '
';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid']] ) ) {
									$request_content .= $cfld['label'] . ' : ' . wp_strip_all_tags( $_POST['wcwar_' . $cfld['cid']] ) . '
';
								}
								else {
									$has_error = true;
								}
							break ;
							case 'paragraph' :
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid']] ) && $_POST['wcwar_' . $cfld['cid']] !== '' ) {
									$request_content .= $cfld['label'] . ' : ' . wp_strip_all_tags( $_POST['wcwar_' . $cfld['cid']] ) . '
';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid']] ) ) {
									$request_content .= $cfld['label'] . ' : ' . wp_strip_all_tags( $_POST['wcwar_' . $cfld['cid']] ) . '
';
								}
								else {
									$has_error = true;
								}
							break ;
							case 'radio' :
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid']] ) && $_POST['wcwar_' . $cfld['cid']] !== '' ) {
									$request_content .= $cfld['label'] . ' : ' . $cfld['field_options']['options'][$_POST['wcwar_' . $cfld['cid']]]['label'] . '
';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid']] ) ) {
									$request_content .= $cfld['label'] . ' : ' . wp_strip_all_tags( $_POST['wcwar_' . $cfld['cid']] ) . '
';
								}
								else {
									$has_error = true;
								}
							break ;
							case 'checkboxes' :
								$i=0;
								foreach ( $cfld['field_options']['options'] as $cf ) {
									if ( isset( $_POST['wcwar_' . $cfld['cid'] . '_' . $i] ) ) {
										$request_content .= $cf['label'] . ' : ' . esc_html__( 'Checked', 'wcwar') . '
										';
									}
									$i++;
								}
								$request_content .= '';
							break ;
							case 'dropdown' :
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid']] ) && $_POST['wcwar_' . $cfld['cid']] !== '' ) {
									$request_content .= $cfld['label'] . ' : ' . $cfld['field_options']['options'][$_POST['wcwar_' . $cfld['cid']]]['label'] . '
';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid']] ) ) {
									$request_content .= $cfld['label'] . ' : ' . wp_strip_all_tags( $_POST['wcwar_' . $cfld['cid']] ) . '
';
								}
								else {
									$has_error = true;
								}
							break ;
							case 'address' :
								$request_content .= $cfld['label'] . ' :
';
								$adr_content = '';
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid'] . '_address'] ) && $_POST['wcwar_' . $cfld['cid'] . '_address'] !== '' ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_address'] . ', ';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid'] . '_address'] ) ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_address'] . ', ';
								}
								else {
									$has_error = true;
								}
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid'] . '_city'] ) && $_POST['wcwar_' . $cfld['cid'] . '_city'] !== '' ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_city'] . ', ';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid'] . '_city'] ) ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_city'] . ', ';
								}
								else {
									$has_error = true;
								}
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid'] . '_state'] ) && $_POST['wcwar_' . $cfld['cid'] . '_state'] !== '' ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_state'] . ', ';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid'] . '_state'] ) ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_state'] . ', ';
								}
								else {
									$has_error = true;
								}
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid'] . '_zip'] ) && $_POST['wcwar_' . $cfld['cid'] . '_zip'] !== '' ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_zip'] . ', ';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid'] . '_zip'] ) ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_zip'] . ', ';
								}
								else {
									$has_error = true;
								}
								if ( $cfld['required'] === true && isset( $_POST['wcwar_' . $cfld['cid'] . '_country'] ) && $_POST['wcwar_' . $cfld['cid'] . '_country'] !== '' ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_country'] . '';
								}
								else if ( $cfld['required'] === false && isset( $_POST['wcwar_' . $cfld['cid'] . '_country'] ) ) {
									$adr_content .= $_POST['wcwar_' . $cfld['cid'] . '_country'] . ', ';
								}
								else {
									$has_error = true;
								}
								$request_content .= $adr_content . '
';
							break ;
							default :
							break ;

						endswitch;

					}
				}
				else {
					$request_content = '';
					if ( isset( $_POST['wcwar_qty'] ) ) {
						$request_content .= '<strong>' . esc_html__( 'Item was ordered multiple times, the requested number of item returns is: ', 'wcwar' ) . intval( $_POST['wcwar_qty'] ) . '</strong>' . '
';
					}
					$request_content .= esc_html__( 'Nothing is wrong with the item. Customer just does not like it. Return was requested for this item.', 'wcwar') . '
';
					if ( isset( $_POST['wcwar_return_note'] ) ) {
						$request_content .= esc_html__( 'Return message from the buyer:', 'wcwar' ) . '
';
						$request_content .= $_POST['wcwar_return_note'] . '
';
					}

				}

				if ( $has_error === false ) {

					$order = wc_get_order( $_POST['order_id'] );

					$id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

					$curr_ordered = $order->get_items();

					if ( count( $curr_ordered ) > 1 ) {

						$item = $curr_ordered[$_POST['item_id']];

						if ( !in_array( $order->get_status(), apply_filters( 'wcwar_warranty_order_status', array( 'completed' ), $order ) ) ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_info">
									<span class="wcwar_info_icon"><i class="wcwar-info"></i></span> <?php esc_html_e( 'Your warranties will be available once your order is complete.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$curr_complete = get_post_meta( $id, '_ordered_date', true );

							$curr_notallowed = true;
						}
						else {
							$curr_complete = get_post_meta( $id, '_completed_date', true );

							$curr_notallowed = false;

						}

						$curr_args = array(
							'post_type' => 'wcwar_warranty_req',
							'orderby'   => 'date',
							'order'     => 'ASC',
							'post_status' => array( 'publish', 'pending' ),
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $id,
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => $_POST['item_id'],
								)
							)
						);
						$curr_req = get_posts( $curr_args );

						if ( !empty( $curr_req ) ) {

							$m=0;
							foreach( $curr_req as $curr_reqq ) {
								$check_return = get_post_meta( $curr_reqq->ID, '_wcwar_warranty_return_request', true );
								if ( $check_return == 'return' ) {
									$return = 'return';
									$return_id = $m;

									$curr_terms = get_the_terms( $curr_req[$m]->ID, 'wcwar_warranty' );
									$curr_terms = reset( $curr_terms );
								}
								$m++;
							}
						}

						$curr_status = self::hlp_valid_warranty( $curr_complete, $item );
						$curr_status_return = self::hlp_valid_return( $curr_complete, $item );
						$curr_returns = get_option( 'wcwar_returns_no_warranty', 'no' );
						$curr_multi_req = get_option( 'wcwar_enable_multi_requests', 'no' );

						if ( $curr_notallowed === false && $curr_status && $curr_status === 'nowar' && $curr_returns == 'yes' && empty( $curr_req ) && !$curr_status_return ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
									<?php esc_html_e( 'There is no warranty or return policy for this item.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( $curr_notallowed === false && $curr_status && $curr_status === 'nowar' && empty( $curr_req ) && $curr_returns == 'yes' && $curr_status_return && !isset( $_POST['wcwar_return'] ) ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
									<?php esc_html_e( 'There is no warranty for this item.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( $curr_notallowed === false && $curr_status && empty( $curr_req ) && $curr_returns == 'yes' && !$curr_status_return ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
									<?php esc_html_e( 'Warranty for this item has expired.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !empty( $curr_req ) && isset( $return ) && $return == 'return' ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
								<?php
									if ( !empty( $curr_terms ) && $curr_terms->slug == 'completed' ) {
										echo esc_html__( 'This item has already been returned to the store.', 'wcwar' );
									}
									else if ( $curr_req[$return_id]->post_status == 'pending' ) {
										echo esc_html__( 'Return already requested but is still pending review. Please be patient.', 'wcwar' ) . '<br/>';
									}
									else {
										echo esc_html__( 'Return already requested. View request status on this', 'wcwar' ) . ' <a href="' . get_permalink( $curr_req[$return_id]->ID ) . '">' . esc_html__( 'link', 'wcwar') . '</a>.<br/>';
									}
								?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !empty( $curr_req ) && $curr_multi_req == 'no' ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span><?php echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar' ) . ' <a href="' . $curr_req[0]->guid . '">' . esc_html__( 'link', 'wcwar' ) . '</a>.'; ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !empty( $curr_req ) && $curr_multi_req == 'yes' ) {
							if ( isset( $return_id ) ) {
								unset( $curr_req[$return_id] );
							}
							$curr_req_reverse = array_values( array_reverse ( $curr_req ) );

							$curr_terms = get_the_terms( $curr_req_reverse[0]->ID, 'wcwar_warranty' );
							$curr_terms = reset( $curr_terms );

							if ( $curr_terms->slug !== 'completed' ) {

							?>
								<div class="wcwar_warranty">
									<p class="wcwar_form_error">
										<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span><?php echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar' ) . ' <a href="' . $curr_req_reverse[0]->guid . '">' . esc_html__( 'link', 'wcwar' ) . '</a>.'; ?>
									</p>
								</div>
							<?php
								$output = ob_get_clean();
								return $output;

							}
						}

/*						$item = $curr_ordered[$_POST['item_id']];

						if ( self::hlp_valid_warranty( $curr_complete, $item) ) {
							echo $item['name'] . ' ' . esc_html__( 'warranty has expired.', 'wcwar' );
							return;
						}

						if ( isset( $_POST['wcwar_return'] ) ) {
							$curr_returns = get_option( 'wcwar_enable_returns', 'no' );
							if ( $curr_returns == 'yes' && self::hlp_valid_return( $curr_complete, $item ) === false ) {
								echo $item['name'] . ' ' . esc_html__( 'return period has expired.', 'wcwar' );
								return;
							}
						}*/

						$curr_parent_args = array(
							'post_type' => 'wcwar_warranty_req',
							'post_status' =>  array( 'publish', 'pending' ),
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $_POST['order_id'],
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => -1,
								)
							)
						);
						$curr_parent_req = get_posts( $curr_parent_args );

						if ( empty( $curr_parent_req ) ) {

							$curr_parent_create = array(
								'post_title'    => esc_html__( 'Request for Order #', 'wcwar' ) . $_POST['order_id'],
								'post_content'  => esc_html__( 'This is a parent warranty request. Check child requests for details.', 'wcwar' ),
								'post_name'     => 'r_' . $_POST['order_id'],
								'post_status'   => get_option( 'wcwar_default_post', 'pending' ),
								'post_author'   => 1,
								'post_type'     => 'wcwar_warranty_req',
								'comment_status'=> 'open',
								'ping_status'   => 'closed'
							);
							$curr_parent_request = wp_insert_post( $curr_parent_create );

							wp_set_post_terms( $curr_parent_request, 'new', 'wcwar_warranty' );

							$curr_parent_meta = array(
								'order_id'      => $_POST['order_id'],
								'product_id'    => -1,
								'warranty_id'   => $_POST['order_id'] . '-' . $curr_parent_request
							);

							foreach ( $curr_parent_meta as $k => $v ) {
								add_post_meta( $curr_parent_request, '_wcwar_warranty_' . $k, $v, true );
							}

						}

						$curr_complete = get_post_meta( $id, '_completed_date', true );

						$curr_ordered = $order->get_items();

						$item = $curr_ordered[$_POST['item_id']];

						if ( self::hlp_valid_warranty( $curr_complete, $item ) && $curr_returns == 'yes' && !$curr_status_return ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<?php echo $item['name'] . ' ' . esc_html__( 'warranty has expired.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}

						$curr_create = array(
							'post_title'    => esc_html__( 'Request for Order #', 'wcwar' ) . esc_sql( $_POST['order_id'] ) . ' - ' . esc_html__( 'Item #', 'wcwar' ) . esc_sql( $_POST['item_id'] ),
							'post_content'  => $request_content,
							'post_name'     => 'i_' . esc_sql( $_POST['order_id'] ) . '_' . esc_sql( $_POST['item_id'] ),
							'post_status'   => esc_sql( get_option( 'wcwar_default_post', 'pending' ) ),
							'post_author'   => 1,
							'post_type'     => 'wcwar_warranty_req',
							'comment_status'=> 'open',
							'ping_status'   => 'closed',
							'post_parent'   => ( isset( $curr_parent_request) ? esc_sql( $curr_parent_request ) : esc_sql( $curr_parent_req[0]->ID ) )
						);
						$curr_request = wp_insert_post( $curr_create );

						wp_set_post_terms( $curr_request, 'new', 'wcwar_warranty' );

						$curr_meta = array(
							'order_id'      => $_POST['order_id'],
							'product_id'    => $_POST['item_id'],
							'warranty_id'   => $_POST['order_id'] . '-' . $_POST['item_id'] . '-' . $curr_request
						);

							if ( isset( $_POST['wcwar_return'] ) ) {
								$curr_meta= $curr_meta + array( 'return_request' => 'return' );
							}

						foreach ( $curr_meta as $k => $v ) {
							add_post_meta( $curr_request, '_wcwar_warranty_' . $k, $v, true );
						}

					}

					else {

						$item = $curr_ordered[$_POST['item_id']];

						if ( !in_array( $order->get_status(), apply_filters( 'wcwar_warranty_order_status', array( 'completed' ), $order ) ) ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_info">
									<span class="wcwar_info_icon"><i class="wcwar-info"></i></span> <?php esc_html_e( 'Your warranties will be available once your order is complete.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$curr_complete = get_post_meta( $id, '_ordered_date', true );

							$curr_notallowed = true;
						}
						else {
							$curr_complete = get_post_meta( $id, '_completed_date', true );

							$curr_notallowed = false;

						}

						$curr_args = array(
							'post_type' => 'wcwar_warranty_req',
							'orderby'   => 'date',
							'order'     => 'ASC',
							'post_status' => array( 'publish', 'pending' ),
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $id,
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => $_POST['item_id'],
								)
							)
						);
						$curr_req = get_posts( $curr_args );

						if ( !empty( $curr_req ) ) {

							$m=0;
							foreach( $curr_req as $curr_reqq ) {
								$check_return = get_post_meta( $curr_reqq->ID, '_wcwar_warranty_return_request', true );
								if ( $check_return == 'return' ) {
									$return = 'return';
									$return_id = $m;

									$curr_terms = get_the_terms( $curr_req[$m]->ID, 'wcwar_warranty' );
									$curr_terms = reset( $curr_terms );
								}
								$m++;
							}
							
						}

						$curr_status = self::hlp_valid_warranty( $curr_complete, $item);
						$curr_status_return = self::hlp_valid_return( $curr_complete, $item );
						$curr_returns = get_option( 'wcwar_returns_no_warranty', 'no' );
						$curr_multi_req = get_option( 'wcwar_enable_multi_requests', 'no' );

						if ( $curr_notallowed === false && $curr_status && $curr_status === 'nowar' && $curr_returns == 'yes' && empty( $curr_req) && !$curr_status_return ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
									<?php esc_html_e( 'There is no warranty or return policy for this item.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( $curr_notallowed === false && $curr_status && $curr_status === 'nowar' && empty( $curr_req ) && $curr_returns == 'yes' && !$curr_status_return && !isset( $_POST['wcwar_return'] ) ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
									<?php esc_html_e( 'There is no warranty for this item.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( $curr_notallowed === false && $curr_status && empty( $curr_req ) && $curr_returns == 'yes' && !$curr_status_return ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
									<?php esc_html_e( 'Warranty for this item has expired.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !empty( $curr_req ) && isset( $return ) && $return == 'return' ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
								<?php
									if ( !empty( $curr_terms ) && $curr_terms->slug == 'completed' ) {
										echo esc_html__( 'This item has already been returned to the store.', 'wcwar' );
									}
									else if ( $curr_req[$return_id]->post_status == 'pending' ) {
										echo esc_html__( 'Return already requested but is still pending review. Please be patient.', 'wcwar' ) . '<br/>';
									}
									else {
										echo esc_html__( 'Return already requested. View request status on this', 'wcwar' ) . ' <a href="' . get_permalink( $curr_req[$return_id]->ID ) . '">' . esc_html__( 'link', 'wcwar' ) . '</a>.<br/>';
									}
								?>
								</p>
							</div>
							<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !empty( $curr_req) && $curr_multi_req == 'no' ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
								<?php
									echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar') . ' <a href="'.$curr_req[0]->guid.'">' . esc_html__( 'link', 'wcwar') . '</a>.';
								?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !empty( $curr_req ) && $curr_multi_req == 'yes' ) {
							if ( isset( $return_id ) ) {
								unset( $curr_req[$return_id] );
							}
							$curr_req_reverse = array_values( array_reverse ( $curr_req ) );

							$curr_terms = get_the_terms( $curr_req_reverse[0]->ID, 'wcwar_warranty' );
							$curr_terms = reset( $curr_terms );

							if ( $curr_terms->slug !== 'completed' ) {

							?>
								<div class="wcwar_warranty">
									<p class="wcwar_form_error">
										<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span><?php echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar' ) . ' <a href="' . $curr_req_reverse[0]->guid . '">' . esc_html__( 'link', 'wcwar' ) . '</a>.'; ?>
									</p>
								</div>
							<?php
								$output = ob_get_clean();
								return $output;

							}
						}

			 
						if ( self::hlp_valid_warranty( $curr_complete, $item) && $curr_returns == 'yes' && !$curr_status_return ) {
						?>
							<div class="wcwar_warranty">
								<p class="wcwar_form_error">
								<span class="wcwar_info_icon"><i class="wcwar-info"></i></span>
								<?php echo $item['name'] . ' ' . esc_html__( 'warranty has expired.', 'wcwar' ); ?>
								</p>
							</div>
						<?php
							$output = ob_get_clean();
							return $output;
						}

						if ( isset( $_POST['wcwar_return'] ) ) {
							$curr_returns = get_option( 'wcwar_enable_returns', 'no' );
							if ( $curr_returns == 'yes' && self::hlp_valid_return( $curr_complete, $item ) === false ) {
							?>
								<div class="wcwar_warranty">
									<p class="wcwar_form_error">
									<span class="wcwar_info_icon"><i class="wcwar-info"></i></span>
										<?php echo $item['name'] . ' ' . esc_html__( 'return period has expired.', 'wcwar' ); ?>
									</p>
								</div>
							<?php
							$output = ob_get_clean();
							return $output;
							}
						}

						$curr_create = array(
							'post_title'    => esc_html__( 'Request for Order #', 'wcwar' ) . esc_sql( $_POST['order_id'] ) . ' - ' . esc_html__( 'Item #', 'wcwar') . esc_sql( $_POST['item_id'] ),
							'post_content'  => $request_content,
							'post_name'     => 'r_' . esc_sql( $_POST['order_id'] ) . '_' . esc_sql( $_POST['item_id'] ),
							'post_status'   => esc_sql( get_option( 'wcwar_default_post', 'pending' ) ),
							'post_author'   => 1,
							'post_type'     => 'wcwar_warranty_req',
							'comment_status'=> 'open',
							'ping_status'   => 'closed'
						);
						$curr_request = wp_insert_post( $curr_create );

						wp_set_post_terms( $curr_request, 'new', 'wcwar_warranty' );

						$curr_meta = array(
							'order_id'      => $_POST['order_id'],
							'product_id'    => $_POST['item_id'],
							'warranty_id'   => $_POST['order_id'] . '-' . $_POST['item_id'] . '-' . $curr_request
						);

							if ( isset( $_POST['wcwar_return'] ) ) {
								$curr_meta= $curr_meta + array( 'return_request' => 'return' );
							}

						foreach ( $curr_meta as $k => $v ) {
							add_post_meta( $curr_request, '_wcwar_warranty_' . $k, $v, true );
						}

					}

					if ( isset( $_POST['wcwar_return'] ) ) {
					?>
						<div class="wcwar_warranty">
							<p class="wcwar_form_success">
								<span class="wcwar_info_icon"><i class="wcwar-check"></i></span><?php esc_html_e( 'Return request accepted. Go back to your order', 'wcwar' ); ?> <a href="<?php echo $order->get_view_order_url(); ?>"><?php esc_html_e( 'here', 'wcwar' ); ?></a>!
							</p>
						</div>
					<?php
					}
					else {
					?>
						<div class="wcwar_warranty">
							<p class="wcwar_form_success">
								<span class="wcwar_info_icon"><i class="wcwar-check"></i></span><?php esc_html_e( 'Warranty request accepted. Go back to your order', 'wcwar' ); ?> <a href="<?php echo $order->get_view_order_url(); ?>"><?php esc_html_e( 'here', 'wcwar' ); ?></a>!
							</p>
						</div>
					<?php
					}


				}

			}

			if ( !isset( $_POST['war_submit'] ) || $has_error === true ) {

				if ( isset( $_POST['war_submit'] ) ) {
					$_GET['order_id'] = $_POST['order_id'];
					$_GET['item_id'] = $_POST['item_id'];
					$_GET['multiple'] = isset( $_POST['multiple'] ) ? $_POST['multiple'] : null;
				}

				$curr_guests = get_option( 'wcwar_enable_guest_requests', 'no' );

				if ( $curr_guests == 'yes' ) {
					if ( isset( $_POST['war_guest'] ) ) {
						if ( isset( $_POST['email'] ) && isset( $_POST['order_id'] ) ) {
							$chck_order = wc_get_order( $_POST['order_id'] );
							if ( isset( $chck_order ) && !empty( $chck_order ) && $chck_order->billing_email == $_POST['email'] ) {
								$guest_request = true;
								$_GET['order_id'] = $_POST['order_id'];
								$_GET['item_id'] ='-1';
							}
						}
					}
				}


				if ( is_user_logged_in() || isset( $guest_request ) ) {

					if ( ( !isset( $_GET['order_id'] ) || !isset( $_GET['item_id'] ) ) ) {
					?>
						<p class="wcwar_form_info">
							<span class="wcwar_info_icon"><i class="wcwar-info"></i></span>
					<?php
							echo esc_html__( 'Product is not selected. Please visit your account page at this', 'wcwar' ) . ' <a href="' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '">' . esc_html__( 'link','wcwar') . '</a>.';
					?>
						</p>
					<?php
						$output = ob_get_clean();
						return $output;
					}

					$current_user = wp_get_current_user();

					$curr_user = ( $curr_user = get_post_meta( $_GET['order_id'], '_customer_user', true ) ) ? absint( $curr_user ) : '';

					if ( current_user_can( 'manage_options' ) || $curr_user == $current_user->ID || isset( $guest_request ) ) {

						$order = wc_get_order( $_GET['order_id'] );
						$id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

						if ( !in_array( $order->get_status(), apply_filters( 'wcwar_warranty_order_status', array( 'completed' ), $order ) ) ) {
					?>
					<p class="wcwar_form_info">
						<span class="wcwar_info_icon"><i class="wcwar-info"></i></span> <?php esc_html_e( 'Your warranties will be available once your order is complete.', 'wcwar' ); ?>
					</p>
					<?php
						$curr_notallowed = true;

						$output = ob_get_clean();
						return $output;
					}
					else {
						$curr_notallowed = false;
					}

/*						$curr_args = array(
						'post_type' => 'wcwar_warranty_req',
						'orderby'   => 'date',
						'order'     => 'ASC',
						'post_status' => 'any',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key' => '_wcwar_warranty_order_id',
								'value' => $id,
							),
							array(
								'key' => '_wcwar_warranty_product_id',
								'value' => $_GET['item_id'],
							)
						)
					);
					$curr_req = get_posts( $curr_args );

					if ( !empty( $curr_req ) ) {
						$curr_terms = reset( get_the_terms( $curr_req[0]->ID, 'wcwar_warranty' ) );
						$return = get_post_meta( $curr_req[0]->ID, '_wcwar_warranty_return_request', true );
					}

					$curr_status = self::hlp_valid_warranty( $curr_complete, $item );
					$curr_status_return = self::hlp_valid_return( $curr_complete, $item );
					$curr_returns = get_option( 'wcwar_returns_no_warranty', 'no' );
					$curr_multi_req = get_option( 'wcwar_enable_multi_requests', 'no' );

					$curr_multi_req = get_option( 'wcwar_enable_multi_requests', 'no' );
					if ( $curr_multi_req == 'no' ) {
						$curr_args = array(
							'post_type' => 'wcwar_warranty_req',
							'post_status' => 'any',
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => '_wcwar_warranty_order_id',
									'value' => $_GET['order_id']
								),
								array(
									'key' => '_wcwar_warranty_product_id',
									'value' => $_GET['item_id']
								)
							)
						);
						$curr_req = get_posts( $curr_args );

						if ( !empty( $curr_req) ) {
							echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar') . ' <a href="'.$curr_req[0]->guid.'">' . esc_html__( 'link', 'wcwar') . '</a>.';
							return;
						}
					}*/

					$curr_complete = get_post_meta( $id, '_completed_date', true );
					$curr_ordered = $order->get_items();

					if ( $_GET['item_id'] == '-1' ) {

						$form_out = '<p class="wcwar_item wcwar_return"><strong>' . esc_html__( 'Select item', 'wcwar' ) . '</strong>';

						$first = true;
						foreach ( $curr_ordered as $key => $item ) {
							$form_out .= '<span class="wcwar_item_wrap"><label for="wcwar_item_' . $key .'"><input id="wcwar_item_' . $key .'" name="item_id" type="radio" value="' . $key . '" ' . ( $first === true ? ' checked="checked"' : '' ) . ' /> ' . $item['name'] . '';

							if ( $item['qty'] > 1 ) {
								$form_out .= '<input name="wcwar_qty" type="number" min="1" max="' . $item['qty'] . '" value="1" ' . ( $first === true ? '' : ' disabled="disabled" style="display:none;"' ) . '/>';
								$form_out .= '<input name="multiple" type="hidden" value="1" />';
							}
							$form_out .= '</label></span>';

							$curr_stts = self::hlp_valid_warranty( $curr_complete, $item );
							$curr_stts_return = self::hlp_valid_return( $curr_complete, $item );

							if ( $curr_stts_return ) {
								$curr_returnfound = true;
							}
							if ( $curr_stts === false ) {
								$curr_warfound = true;
							}
							$first = false;
						}
						$form_out .= '</p>';

						if ( !isset( $curr_warfound ) && !isset( $curr_returnfound ) ) {
						?>
							<p class="wcwar_form_error">
								<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
								<?php esc_html_e( 'There is no valid warranty for any of the ordered items.', 'wcwar' ); ?>
							</p>
						<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( !isset( $curr_warfound ) && isset( $curr_returnfound ) ) {
						?>
							<p class="wcwar_form_info">
								<span class="wcwar_info_icon"><i class="wcwar-info"></i></span>
								<?php echo esc_html__( 'There is no warranty for this item.', 'wcwar' ) . ' ' . esc_html__( 'However you can still return it to the store as its return warranty is still valid.', 'wcwar' ); ?>
							</p>
						<?php
							$only_return = true;
						}
						else if ( !isset( $curr_warfound ) ) {
						?>
							<p class="wcwar_form_error">
								<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
								<?php esc_html_e( 'There is no valid warranty for any of the ordered items.', 'wcwar' ); ?>
							</p>
						<?php
							$output = ob_get_clean();
							return $output;
						}

					}
					else {
						$item = $curr_ordered[$_GET['item_id']];

						$curr_stts = self::hlp_valid_warranty( $curr_complete, $item );
						$curr_stts_return = self::hlp_valid_return( $curr_complete, $item );
						$curr_returns = get_option( 'wcwar_returns_no_warranty', 'no' );

						if ( $curr_notallowed === false && $curr_stts && $curr_stts === 'nowar' && $curr_returns == 'yes' && $curr_stts_return ) {
					?>
						<div class="wcwar_warranty">
							<p class="wcwar_form_info">
							<span class="wcwar_info_icon"><i class="wcwar-info"></i></span>
							<?php esc_html_e( 'There is no warranty for this item.', 'wcwar' ); ?>
							<?php
								if ( self::hlp_valid_return( $curr_complete, $item ) === true ) {
									$curr_args = array(
										'order_id' => $id,
										'item_id' => $item
									);
									$curr_request = esc_url( add_query_arg( $curr_args, get_permalink( self::wpml_get_id( get_option( 'war_settings_page' ) ) ) ) );
									echo esc_html__( 'However you can still return it to the store as its return warranty is still valid.', 'wcwar' );
									$only_return = true;
								}
							?>
						</p>
						</div>
						<?php
						}
						else if ( $curr_notallowed === false && $curr_stts && $curr_stts === 'nowar' ) {
					?>
						<p class="wcwar_form_error">
							<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
							<?php esc_html_e( 'There is no warranty for this item.', 'wcwar' ); ?>
						</p>
					<?php
							$output = ob_get_clean();
							return $output;
						}
						else if ( $curr_notallowed === false && $curr_stts ) {
					?>
						<p class="wcwar_form_info">
							<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
							<?php esc_html_e( 'Warranty for this item has expired.', 'wcwar' ); ?>
						</p>
					<?php
							$output = ob_get_clean();
							return $output;
						}
					}

					$curr_form_fields = get_option( 'wcwar_form', '' );

					if ( $curr_form_fields !== '' ) {
						$curr_fields = json_decode( $curr_form_fields, true );
					}
					else {
						$curr_fields = json_decode(stripslashes('{"fields":[{"label":"Reason for requesting warranty","field_type":"radio","required":true,"field_options":{"options":[{"label":"Item was damaged","checked":false},{"label":"Item was broken","checked":false},{"label":"Nothing wrong, just returning","checked":false}],"include_other_option":true},"cid":"c10"}]}'), true );
					}

					if ( isset( $_GET['multiple'] ) && $_GET['multiple'] == '1' ) {
						if ( !isset( $form_out ) ) {
							$form_out = '';
						}
						$form_out .= '<p class="wcwar_item wcwar_multiple"><strong>' . esc_html__( 'Select Quantity', 'wcwar' ) . '</strong><label for="wcwar_qty">';
						$form_out .= '<input id="wcwar_qty" name="wcwar_qty" type="number" min="1" max="' . $item['qty'] . '" value="1" />';
						$form_out .= '</label>';
						$form_out .= '<small>' . sprintf( esc_html__( 'Total number of items ordered is %1$s. Please select how many items will be included in your warranty request.', 'wcwar'), $item['qty'] ) . '</small>';
						$form_out .= '</p>';
					}

				?>
					<div class="wcwar_warranty woocommerce">
						<h2><?php printf( esc_html__( 'Order #<strong>%s</strong>', 'wcwar' ), $order->get_order_number() ); ?> - <?php echo $item['name']; ?></h2>
						<form action="<?php the_permalink(); ?>" method="POST">
							<p class="order-info">
							<?php
								printf(
									esc_html__( 'Order #%s was placed on %s and is currently %s. To request a warranty for <em>%s</em> please fill in the form bellow.', 'wcwar' ),
									'<mark class="order-number">' . $order->get_order_number() . '</mark>',
									'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>',
									'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>',
									$item['name']
								);
							?>
							</p>
						<?php

						if ( $has_error === true ) {
						?>
							<p class="wcwar_form_error">
								<span class="wcwar_info_icon"><i class="wcwar-info"></i></span><?php esc_html_e( 'Please fill in the required fields.', 'wcwar' ); ?>
							</p>
						<?php
						}

						$curr_returns = get_option( 'wcwar_enable_returns', 'no' );

						if ( isset( $form_out ) ) {
							echo $form_out;
						}

						if ( $curr_returns == 'yes' && self::hlp_valid_return( $curr_complete, $item ) === true ) {
					?>
						<p class="wcwar_return">
							<strong><?php esc_html_e( 'Return is still available', 'wcwar' ); ?></strong>
							<label for="wcwar_return">
								<input type="checkbox" id="wcwar_return" name="wcwar_return" /> <?php esc_html_e( 'Return item', 'wcwar' ); ?>
							</label><br/>
							<small><?php esc_html_e( 'Item returns are available in this shop. If you just want to return an item to the store please check this option.', 'wcwar' ); ?></small>
						</p>
						<p class="wcwar_return_note">
							<strong><?php esc_html_e( 'Why are you returning this item?', 'wcwar' ); ?></strong>
							<label for="wcwar_return_note">
								<textarea name="wcwar_return_note"></textarea>
							</label>
							<small><?php esc_html_e( 'Enter additional information for this return request.', 'wcwar' ); ?></small>
						</p>
					<?php
						}
						if ( !isset( $only_return ) ) :
						foreach ( $curr_fields['fields'] as $cfld ) {
							if ( $cfld['required'] === true ) {
								$req = '<span class="wcwar_required">' . esc_html__( 'Required', 'wcwar') . '</span>';
							}
							else $req = '';
					?>
						<p class="wcwar_form_field">
						<?php
							switch ( $cfld['field_type'] ) :
							case 'text' :
						?>
							<label for="wcwar_<?php echo $cfld['cid']; ?>"><strong><?php echo $cfld['label'] . $req; ?></strong>
								<input type="text" id="wcwar_<?php echo $cfld['cid']; ?>" name="wcwar_<?php echo $cfld['cid']; ?>" />
							</label>
						<?php
							if ( isset( $cfld['field_options']['description'] ) && $cfld['field_options']['description'] !== '' ) {
						?>
							<small><?php echo $cfld['field_options']['description']; ?></small>
						<?php
							}
							break;
							case 'paragraph' :
						?>
							<label for="wcwar_<?php echo $cfld['cid']; ?>"><strong><?php echo $cfld['label'] . $req; ?></strong>
								<textarea id="wcwar_<?php echo $cfld['cid']; ?>" name="wcwar_<?php echo $cfld['cid']; ?>"></textarea>
							</label>
						<?php
							if ( isset( $cfld['field_options']['description'] ) && $cfld['field_options']['description'] !== '' ) {
						?>
							<small><?php echo $cfld['field_options']['description']; ?></small>
						<?php
							}
							break;
							case 'radio' :
						?>
							<strong><?php echo $cfld['label'] . $req; ?></strong>
						<?php
							$i=0;
							foreach ( $cfld['field_options']['options'] as $cf ) {
							?>
								<label for="wcwar_<?php echo $cfld['cid'] . $i; ?>">
									<input type="radio" id="wcwar_<?php echo $cfld['cid'] . $i; ?>" name="wcwar_<?php echo $cfld['cid']; ?>" value="<?php echo $i; ?>" /> <?php echo $cf['label']; ?>
								</label>
								<br/>
							<?php
							$i++;
							}
							if ( isset( $cfld['field_options']['description'] ) && $cfld['field_options']['description'] !== '' ) {
						?>
							<small><?php echo $cfld['field_options']['description']; ?></small>
						<?php
							}
							break;
							case 'checkboxes' :
						?>
							<strong><?php echo $cfld['label'] . $req; ?></strong>
						<?php
							$i=0;
							foreach ( $cfld['field_options']['options'] as $cf ) {
							?>
								<label for="wcwar_<?php echo $cfld['cid'] . '_' . $i; ?>">
									<input type="checkbox" id="wcwar_<?php echo $cfld['cid'] . '_' . $i; ?>" name="wcwar_<?php echo $cfld['cid'] . '_' . $i; ?>" /> <?php echo $cf['label']; ?>
								</label>
								<br/>
							<?php
								$i++;
							}
							if ( isset( $cfld['field_options']['description'] ) && $cfld['field_options']['description'] !== '' ) {
						?>
							<small><?php echo $cfld['field_options']['description']; ?></small>
						<?php
							}
							break;
							case 'dropdown' :
						?>
							<label for="wcwar_<?php echo $cfld['cid']; ?>">
								<strong><?php echo $cfld['label'] . $req; ?></strong>
								<select id="wcwar_<?php echo $cfld['cid']; ?>" name="wcwar_<?php echo $cfld['cid']; ?>">
								<?php
									$i=0;
									foreach ( $cfld['field_options']['options'] as $cf ) {
								?>
								<option value="<?php echo $i; ?>"><?php echo $cf['label']; ?></option>
							<?php
								$i++;
								}
							?>
								</select>
							</label>
						<?php
							if ( isset( $cfld['field_options']['description'] ) && $cfld['field_options']['description'] !== '' ) {
						?>
							<small><?php echo $cfld['field_options']['description']; ?></small>
						<?php
							}
							break;
							case 'address' :
							echo '<strong>' . $cfld['label'] . $req . '</strong>';
						?>
							<label for="wcwar_<?php echo $cfld['cid']; ?>_address" class="double"><?php esc_html_e( 'Address', 'wcwar' ); ?>
								<input type="text" id="wcwar_<?php echo $cfld['cid']; ?>_address" name="wcwar_<?php echo $cfld['cid']; ?>_address" />
							</label>
							<label for="wcwar_<?php echo $cfld['cid']; ?>_city" class="single"><?php esc_html_e( 'City', 'wcwar' ); ?>
								<input type="text" id="wcwar_<?php echo $cfld['cid']; ?>_city" name="wcwar_<?php echo $cfld['cid']; ?>_city" />
							</label>
							<label for="wcwar_<?php echo $cfld['cid']; ?>_zip" class="single"><?php esc_html_e( 'Zipcode', 'wcwar' ); ?>
								<input type="text" id="wcwar_<?php echo $cfld['cid']; ?>_zip" name="wcwar_<?php echo $cfld['cid']; ?>_zip" />
							</label>
							<label for="wcwar_<?php echo $cfld['cid']; ?>_state" class="single"><?php esc_html_e( 'State / Province / Region', 'wcwar' ); ?>
								<input type="text" id="wcwar_<?php echo $cfld['cid']; ?>_state" name="wcwar_<?php echo $cfld['cid']; ?>_state" />
							</label>
							<label for="wcwar_<?php echo $cfld['cid']; ?>_country" class="single"><?php esc_html_e( 'Country', 'wcwar' ); ?>
								<select id="wcwar_<?php echo $cfld['cid']; ?>_country" name="wcwar_<?php echo $cfld['cid']; ?>_country">
							<?php
								$countries = WC()->countries->get_allowed_countries();
								foreach ( $countries as $k => $v ) {
									printf( '<option value="%1$s">%2$s</option>', $k, $v );
								}
								
							?>
								</select>
							</label>
						<?php
							if ( isset( $cfld['field_options']['description'] ) && $cfld['field_options']['description'] !== '' ) {
						?>
							<small><?php echo $cfld['field_options']['description']; ?></small>
						<?php
							}
							break;
							default :
							break;
							endswitch;
						?>
						</p>
					<?php

					}
					endif;
					?>
						<p class="wcwar_submit_fields">
							<input name="order_id" type="hidden" value="<?php echo $_GET['order_id']; ?>" />
						<?php
							if ( $_GET['item_id'] !== '-1' ) {
						?>
							<input name="item_id" type="hidden" value="<?php echo $_GET['item_id']; ?>" />
						<?php
							}
							if ( isset( $_GET['multiple'] ) && $_GET['multiple'] == '1' ) {
						?>
							<input name="multiple" type="hidden" value="1" />
						<?php
							}
						?>
							<input name="war_submit" type="hidden" value="true" />
							<input value="<?php esc_html_e( 'Submit Request', 'wcwar' ); ?>" type="submit" class="button" />
						</p>
						</form>
					</div>
				<?php
					}
					else {
					?>
						<div class="wcwar_warranty">
							<p class="wcwar_form_error">
								<span class="wcwar_info_icon"><i class="wcwar-cross"></i></span>
								<?php esc_html_e( 'You cannot make this request.', 'wcwar' ); ?>
							</p>
						</div>
					<?php
					}

				}
				else if ( $curr_guests == 'yes' ) {
			?>
				<div class="wcwar_warranty woocommerce">
					<form action="<?php the_permalink(); ?>" method="POST">
					<p class="wcwar_form_info">
						<span class="wcwar_info_icon"><i class="wcwar-info"></i></span> <?php esc_html_e( 'Warranty requests for users that are not logged in are allowed. Please fill in the form bellow to request a warranty for you order.', 'wcwar' ); ?>
					</p>
					<p>
						<label for="wcwar_guest_email"><strong><?php esc_html_e( 'Enter your E-Mail address', 'wcwar' ); ?><span class="wcwar_required"><?php esc_html_e( 'Required', 'wcwar' ); ?></span></strong>
						<input id="wcwar_guest_email" name="email" type="text"/></label>
					</p>
					<p>
						<label for="wcwar_guest_order_id"><strong><?php esc_html_e( 'Enter your order ID', 'wcwar' ); ?><span class="wcwar_required"><?php esc_html_e( 'Required', 'wcwar' ); ?></span></strong>
						<input id="wcwar_guest_order_id" name="order_id" type="text"/></label>
						<small><em><?php esc_html_e( '* Please fill in all required fields to continue.', 'wcwar' ); ?></em></small>
					</p>
					<p>
						<input name="war_guest" type="hidden" value="true" />
						<input value="<?php esc_html_e( 'Continue', 'wcwar' ); ?>" type="submit" class="button" />
					</p>
					</form>
				</div>
			<?php
				}
				else {
				?>
					<div class="wcwar_warranty">
						<p class="wcwar_form_error">
							<strong><?php esc_html_e( 'Guest warranty requests are not allowed!', 'wcwar' ); ?></strong>
							<?php esc_html_e( 'Warranty requests for users that are not logged in are not allowed. Please login to continue.', 'wcwar' ); ?>
						</p>
					</div>
				<?php
				}

			}
			$output = ob_get_clean();

			return $output;


		}

		function war_pending_requests() {
			global $menu;

			$found = get_term_by( 'slug', 'new', 'wcwar_warranty' );

			if ( $found->count ) {

				$suffix = "?post_type=wcwar_warranty_req";

				$key = self::hlp_recursive_array_search( "edit.php$suffix", $menu );

				if( !$key ) {
					return;
				}

				$menu[$key][0] .= sprintf(
					'<span class="update-plugins count-%1$s"><span class="plugin-count">%1$s</span></span>',
					$found->count
				);
			}

		}

		function war_add_menu_icon_styles() {
	?>
		<style type="text/css">
		#menu-posts-wcwar_warranty_req .dashicons-before:before {font-family:'wcwar'!important;content: '\f008'!important;}
		</style>
	<?php
		}
		
		function war_email( $order, $sent_to_admin, $plain_text ) {

			if ( sizeof( $order->get_items() ) > 0 ) {

			$id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

			?>
			<div class="war_warranty war_order">
			<h3><?php esc_html_e( 'Available Warranties for this Order', 'wcwar' ); ?></h3>
			<?php
				if ( !in_array( $order->get_status(), apply_filters( 'wcwar_warranty_order_status', array( 'completed' ), $order ) ) ) {
			?>
			<p>
				<?php esc_html_e( 'Your warranties will be available once your order is complete.', 'wcwar' ); ?>
			</p>
			<?php
				$curr_complete = get_post_meta( $id, '_ordered_date', true );

				$curr_notallowed = true;
			}
			else {
				$curr_complete = get_post_meta( $id, '_completed_date', true );

				$curr_notallowed = false;
			}
				$i=0;
				foreach( $order->get_items() as $key => $item ) {
					$i++;

					$curr_status = self::hlp_valid_warranty( $curr_complete, $item);

					if ( $curr_notallowed == false && $curr_status && $curr_status === 'nowar' ) {
				?>
					<p>
						<?php esc_html_e( 'There is no warranty for this item.', 'wcwar' ); ?>
					</p>
				<?php
						return;
					}
					else if ( $curr_notallowed == false && $curr_status ) {
				?>
					<p>
						<?php esc_html_e( 'Warranty for this item has expired.', 'wcwar' ); ?>
					</p>
				<?php
						return;
					}

					$curr_args = array(
						'post_type' => 'wcwar_warranty_req',
						'post_status' => 'any',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key' => '_wcwar_warranty_order_id',
								'value' => $id,
							),
							array(
								'key' => '_wcwar_warranty_product_id',
								'value' => $key,
							)
						)
					 );
					$curr_req = get_posts( $curr_args );

					if ( !empty( $curr_req) ) {
						echo esc_html__( 'Warranty already requested. View request status on this', 'wcwar') . ' <a href="'.$curr_req[0]->guid.'">' . esc_html__( 'link', 'wcwar') . '</a>.';
						continue;
					}

					$curr_warranty = json_decode( $item['wcwar_warranty'], true );

					if ( $curr_warranty['type'] == 'preset_warranty' ) {
						$curr_preset = get_woocommerce_term_meta( $curr_warranty['preset'], '_wcwar_warranty', true );
						$curr_warranty = array_merge( $curr_preset, $curr_warranty);
					}
					else if ( $curr_warranty['type'] == 'quick_warranty' ) {

					}

					if ( isset( $curr_warranty['quick'] ) && $curr_warranty['quick'] !== '' ) {
						if ( $curr_warranty['quick'] == 'included_warranty' && isset( $curr_warranty['included_warranty'] ) ) {
					?>
							<h3><?php echo '#' . $i . ' ' .$item['name'] . ' ' . esc_html__( 'Warranty', 'wcwar') . ' - ' . esc_html__( 'Included Warranty', 'wcwar' ); ?></h3>
							<p>
								<?php if ( isset( $curr_warranty['included_warranty']['thumb'] ) ) { ?>
									<img width="64" height="auto" src="<?php echo $curr_warranty['included_warranty']['thumb']; ?>" />
								<?php } ?>
								<span>
									<span>
										<?php echo $curr_warranty['included_warranty']['period'] . ' '; ?>
									</span>
									<?php echo self::hlp_get_warranty_string( $curr_warranty['included_warranty']['period'], $curr_warranty['included_warranty']['type'] ); ?>
								</span><br/>
								<?php if ( isset( $curr_warranty['included_warranty']['desc'] ) ) { ?>
									<small><?php echo $curr_warranty['included_warranty']['desc']; ?></small><br/>
								<?php } ?>
								<?php
									if ( $curr_notallowed === false ) {
										$curr_args = array(
											'order_id' => $id,
											'item_id' => $key
										);
										$curr_request = esc_url( add_query_arg( $curr_args, get_permalink( self::wpml_get_id( get_option( 'war_settings_page' ) ) ) ) );
										echo $item['name'] . ' ' . esc_html__( 'warranty is still valid. Request your warranty at this', 'wcwar') . ' ' . '<a href="' . $curr_request . '" class="wcwar_request">' . esc_html__( 'link', 'wcwar') . '</a>.';
									}
								?>
							</p>
					<?php
						}
						else if ( $curr_warranty['quick'] == 'paid_warranty' && isset( $curr_warranty['paid_warranty'] ) ) {
							$curr = $curr_warranty['paid_warranty']['selected'];
							if ( $curr !== 'no_warranty' ) {
					?>
							<h3><?php echo '#' . $i . ' ' .$item['name'] . ' ' . esc_html__( 'Warranty', 'wcwar') . ' - ' . esc_html__( 'Included Warranty', 'wcwar' ); ?></h3>
							<p>
								<?php if ( isset( $curr_warranty['paid_warranty'][$curr]['thumb'] ) ) { ?>
									<img width="64" height="auto" src="<?php echo $curr_warranty['paid_warranty'][$curr]['thumb']; ?>" />
								<?php } ?>
								<span>
									<span>
										<?php echo $curr_warranty['paid_warranty'][$curr]['period'] . ' '; ?>
									</span>
									<?php echo self::hlp_get_warranty_string( $curr_warranty['paid_warranty'][$curr]['period'], $curr_warranty['paid_warranty'][$curr]['type'] ); ?>
								</span><br/>
								<?php if ( isset( $curr_warranty['paid_warranty'][$curr]['desc'] ) ) { ?>
									<small><?php echo $curr_warranty['paid_warranty'][$curr]['desc']; ?></small><br/>
								<?php } ?>
								<?php
									if ( $curr_notallowed === false ) {
										$curr_args = array(
											'order_id' => $id,
											'item_id' => $key
										);
										$curr_request = esc_url( add_query_arg( $curr_args, get_permalink( self::wpml_get_id( get_option( 'war_settings_page' ) ) ) ) );
										echo $item['name'] . ' ' . esc_html__( 'warranty is still valid. Request your warranty at this', 'wcwar') . ' ' . '<a href="' . $curr_request . '" class="wcwar_request">' . esc_html__( 'link', 'wcwar') . '</a>.';
									}
								?>
							</p>
					<?php
							}
						}
					}
				}
			}
		?>
		</div>
		<?php
		}

		function war_cart_price( $val, $cart_item, $cart_item_key ) {

			if ( isset( $cart_item['wcwar_pa_price'] ) ) {
				self::$settings['add_msg'] = true;
				return $val . '<small> *(+ ' . wc_price( $cart_item['wcwar_pa_price'] ) . ')</small>';
			}

			return( $val );

		}

		function war_cart_help() {
			if ( isset( self::$settings['add_msg'] ) ) {
				printf( '
					<tr>
						<td colspan="6">
							<p><small>* %1$s</small></p>
						</td>
					</tr>', esc_html__( 'Your purchase has included paid warranties!', 'wcwar' ) );
			}
		}

		function wpml_get_id( $id ) {
			if( function_exists( 'icl_object_id' ) ) {
				return icl_object_id( $id, 'page', true );
			}
			else {
				return $id;
			}
		}

		public static function version_check( $version = '3.0.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if( version_compare( $woocommerce->version, $version, ">=" ) ) {
					return true;
				}
			}
			return false;
		}

	}

	function wcwar_init_plugin() {

		include( plugin_dir_path( __FILE__ ) . '/includes/war-public.php' );
		$GLOBALS['wc_warranties_and_returns'] = new WC_Warranties_And_Returns();

		if ( is_admin() ) {
			include( plugin_dir_path( __FILE__ ) . '/includes/war-settings.php' );
		}

	}

	add_action( 'woocommerce_init', 'wcwar_init_plugin', 10, 1 );

	if ( is_admin() ) {

		$purchase_code = get_option( 'wcwar_registration_key', '' );

		if ( $purchase_code ) {

			require 'includes/update/plugin-update-checker.php';

			$pf_check = PucFactory::buildUpdateChecker(
				'http://mihajlovicnenad.com/envato/get_json.php?p=9375424&k=' . $purchase_code,
				__FILE__
			);

		}

	}

	if ( !function_exists( 'mnthemes_add_meta_information' ) ) {
		function mnthemes_add_meta_information_action() {
			$val = apply_filters( 'mnthemes_add_meta_information_used', array() );
			if ( !empty( $val ) ) {
				echo '<meta name="generator" content="' . implode( ', ', $val ) . '"/>';
			}
		}
		function mnthemes_add_meta_information() {
			add_action( 'wp_head', 'mnthemes_add_meta_information_action', 99 );
		}
		mnthemes_add_meta_information();
	}

?>