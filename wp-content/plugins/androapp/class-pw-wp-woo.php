<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class Pw_Wp_Woo {
	/**
	 * Server object
	 *
	 * @var WP_JSON_ResponseHandler
	 */
	protected $server;
	
	/**
	 * Register the user-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes( $routes ) {
		$user_routes = array(
			'/androapp/woo/customers' => array(
				array( array( $this, 'create_customer' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androapp/woo/me' => array(
				array( array( $this, 'get_customer_info' ), WP_JSON_Server::READABLE ),
			),
			'/androappgetproducts' => array(
				array( array( $this, 'get_products' ), WP_JSON_Server::READABLE ),
			),
			'/androapp/woo/order' => array(
				array( array( $this, 'get_customer_order' ), WP_JSON_Server::READABLE ),
			),
			'/androappcreateorder' => array(
				array( array( $this, 'create_order' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androapppayment' => array(
				array( array( $this, 'process_payment' ), WP_JSON_Server::READABLE ),
			),
		);
		return array_merge( $routes, $user_routes );
	}
	
	/**
	 * Constructor
	 *
	 * @param WP_JSON_ResponseHandler $server Server object
	 */
	public function __construct($server ) {
            $this->server = $server;
            if(!class_exists('WP_JSON_Posts')){
                add_action( 'rest_api_init', function () {
                    register_rest_route( 'androapp/v2', '/androapp/woo/customers', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'create_customer'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androapp/woo/me', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'get_customer_info_v2'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androappgetproducts', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'get_products_v2'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androapp/woo/order', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'get_customer_order_v2'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androappcreateorder', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'create_order'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androapppayment', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'process_payment_v2'),
                    ) );

                });
            }
            
	}
	
			/**
	 * Get standard product data that applies to every product type
	 *
	 * @since 2.1
	 * @param WC_Product $product
	 * @return WC_Product
	 */
	private function get_product_data( $product ) {
		$prices_precision = wc_get_price_decimals();

		$androapp_meta = null;
		if(function_exists( 'get_wcmp_product_vendors' )){
			$androapp_meta = array();
			$vendor = get_wcmp_product_vendors($product->id);
			if(isset($vendor) /*&& is_array($vendor)*/){
				if(isset($vendor->user_data) && isset($vendor->user_data->data)){
					$androapp_meta['Vendor'] =  $vendor->user_data->data->display_name;
				}
			}
			
			if(empty($androapp_meta )){
				$androapp_meta = null;
			}
		}
		
		
		return array(
			'title'              => $product->get_title(),
			'id'                 => (int) $product->is_type( 'variation' ) ? $product->get_variation_id() : $product->id,
			'created_at'         => $this->format_datetime( $product->get_post_data()->post_date_gmt ),
			'updated_at'         => $this->format_datetime( $product->get_post_data()->post_modified_gmt ),
			'type'               => $product->product_type,
			'status'             => $product->get_post_data()->post_status,
			'downloadable'       => $product->is_downloadable(),
			'virtual'            => $product->is_virtual(),
			'permalink'          => $product->get_permalink(),
			'sku'                => $product->get_sku(),
			'price'              => wc_format_decimal( $product->get_price(), $prices_precision ),
			'regular_price'      => wc_format_decimal( $product->get_regular_price(), $prices_precision ),
			'sale_price'         => $product->get_sale_price() ? wc_format_decimal( $product->get_sale_price(), $prices_precision ) : null,
			'price_html'         => $product->get_price_html(),
			'taxable'            => $product->is_taxable(),
			'tax_status'         => $product->get_tax_status(),
			'tax_class'          => $product->get_tax_class(),
			'managing_stock'     => $product->managing_stock(),
			'stock_quantity'     => $product->get_stock_quantity(),
			'in_stock'           => $product->is_in_stock(),
			'backorders_allowed' => $product->backorders_allowed(),
			'backordered'        => $product->is_on_backorder(),
			'sold_individually'  => $product->is_sold_individually(),
			'purchaseable'       => $product->is_purchasable(),
			'featured'           => $product->is_featured(),
			'visible'            => $product->is_visible(),
			'catalog_visibility' => $product->visibility,
			'on_sale'            => $product->is_on_sale(),
			'product_url'        => $product->is_type( 'external' ) ? $product->get_product_url() : '',
			'button_text'        => $product->is_type( 'external' ) ? $product->get_button_text() : '',
			'weight'             => $product->get_weight() ? wc_format_decimal( $product->get_weight(), 2 ) : null,
			'dimensions'         => array(
				'length' => $product->length,
				'width'  => $product->width,
				'height' => $product->height,
				'unit'   => get_option( 'woocommerce_dimension_unit' ),
			),
			'shipping_required'  => $product->needs_shipping(),
			'shipping_taxable'   => $product->is_shipping_taxable(),
			'shipping_class'     => $product->get_shipping_class(),
			'shipping_class_id'  => ( 0 !== $product->get_shipping_class_id() ) ? $product->get_shipping_class_id() : null,
			'description'        => wpautop( do_shortcode( $product->get_post_data()->post_content ) ),
			'short_description'  => apply_filters( 'woocommerce_short_description', $product->get_post_data()->post_excerpt ),
			'reviews_allowed'    => ( 'open' === $product->get_post_data()->comment_status ),
			'average_rating'     => wc_format_decimal( $product->get_average_rating(), 2 ),
			'rating_count'       => (int) $product->get_rating_count(),
			'related_ids'        => array_map( 'absint', array_values( $product->get_related() ) ),
			'upsell_ids'         => array_map( 'absint', $product->get_upsells() ),
			'cross_sell_ids'     => array_map( 'absint', $product->get_cross_sells() ),
			'parent_id'          => $product->post->post_parent,
			'categories'         => wp_get_post_terms( $product->id, 'product_cat', array( 'fields' => 'names' ) ),
			'tags'               => wp_get_post_terms( $product->id, 'product_tag', array( 'fields' => 'names' ) ),
			//'images'             => $this->get_images( $product ),
			'featured_src'       => wp_get_attachment_url( get_post_thumbnail_id( $product->is_type( 'variation' ) ? $product->variation_id : $product->id ) ),
			'attributes'         => $this->get_attributes( $product ),
			//'downloads'          => $this->get_downloads( $product ),
			//'download_limit'     => (int) $product->download_limit,
			//'download_expiry'    => (int) $product->download_expiry,
			//'download_type'      => $product->download_type,
			'purchase_note'      => wpautop( do_shortcode( wp_kses_post( $product->purchase_note ) ) ),
			'total_sales'        => metadata_exists( 'post', $product->id, 'total_sales' ) ? (int) get_post_meta( $product->id, 'total_sales', true ) : 0,
			'variations'         => array(),
			'parent'             => array(),
			'androapp_meta'		 => $androapp_meta,
		);
	}
	
		/**
	 * Get the attributes for a product or product variation
	 *
	 * @since 2.1
	 * @param WC_Product|WC_Product_Variation $product
	 * @return array
	 */
	private function get_attributes( $product ) {

		$attributes = array();

		if ( $product->is_type( 'variation' ) ) {

			// variation attributes
			foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {

				// taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
				$attributes[] = array(
					'name'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) ),
					'slug'   => str_replace( 'attribute_', '', str_replace( 'pa_', '', $attribute_name ) ),
					'option' => $attribute,
				);
			}

		} else {

			foreach ( $product->get_attributes() as $attribute ) {

				// taxonomy-based attributes are comma-separated, others are pipe (|) separated
				if ( $attribute['is_taxonomy'] ) {
					$options = explode( ',', $product->get_attribute( $attribute['name'] ) );
				} else {
					$options = explode( '|', $product->get_attribute( $attribute['name'] ) );
				}

				$attributes[] = array(
					'name'      => wc_attribute_label( $attribute['name'] ),
					'slug'      => str_replace( 'pa_', '', $attribute['name'] ),
					'position'  => (int) $attribute['position'],
					'visible'   => (bool) $attribute['is_visible'],
					'variation' => (bool) $attribute['is_variation'],
					'options'   => array_map( 'trim', $options ),
				);
			}
		}

		return $attributes;
	}
	
		private function get_variation_data( $product ) {
		$prices_precision = wc_get_price_decimals();
		$variations       = array();

		foreach ( $product->get_children() as $child_id ) {

			$variation = $product->get_child( $child_id );

			if ( ! $variation->exists() ) {
				continue;
			}

			$variations[] = array(
					'id'                => $variation->get_variation_id(),
					'created_at'        => $this->format_datetime( $variation->get_post_data()->post_date_gmt ),
					'updated_at'        => $this->format_datetime( $variation->get_post_data()->post_modified_gmt ),
					'downloadable'      => $variation->is_downloadable(),
					'virtual'           => $variation->is_virtual(),
					'permalink'         => $variation->get_permalink(),
					'sku'               => $variation->get_sku(),
					'price'             => wc_format_decimal( $variation->get_price(), $prices_precision ),
					'regular_price'     => wc_format_decimal( $variation->get_regular_price(), $prices_precision ),
					'sale_price'        => $variation->get_sale_price() ? wc_format_decimal( $variation->get_sale_price(), $prices_precision ) : null,
					'taxable'           => $variation->is_taxable(),
					'tax_status'        => $variation->get_tax_status(),
					'tax_class'         => $variation->get_tax_class(),
					'managing_stock'    => $variation->managing_stock(),
					'stock_quantity'    => $variation->get_stock_quantity(),
					'in_stock'          => $variation->is_in_stock(),
					'backordered'       => $variation->is_on_backorder(),
					'purchaseable'      => $variation->is_purchasable(),
					'visible'           => $variation->variation_is_visible(),
					'on_sale'           => $variation->is_on_sale(),
					'weight'            => $variation->get_weight() ? wc_format_decimal( $variation->get_weight(), 2 ) : null,
					'dimensions'        => array(
						'length' => $variation->length,
						'width'  => $variation->width,
						'height' => $variation->height,
						'unit'   => get_option( 'woocommerce_dimension_unit' ),
					),
					'shipping_class'    => $variation->get_shipping_class(),
					'shipping_class_id' => ( 0 !== $variation->get_shipping_class_id() ) ? $variation->get_shipping_class_id() : null,
					//'image'             => $this->get_images( $variation ),
					'attributes'        => $this->get_attributes( $variation ),
					//'downloads'         => $this->get_downloads( $variation ),
					//'download_limit'    => (int) $product->download_limit,
					'download_expiry'   => (int) $product->download_expiry,
			);
		}

		return $variations;
	}
	
	private function get_product( $id, $fields = null ) {

		$product = wc_get_product( $id );

		// add data that applies to every product type
		$product_data = $this->get_product_data( $product );

		// add variations to variable products
		if ( $product->is_type( 'variable' ) && $product->has_child() ) {

			$product_data['variations'] = $this->get_variation_data( $product );
		}

		// add the parent product data to an individual variation
		if ( $product->is_type( 'variation' ) && $product->parent ) {

			$product_data['parent'] = $this->get_product_data( $product->parent );
		}

		return array( 'product' => apply_filters( 'woocommerce_api_product_response', $product_data, $product, $fields, null ) );
	}
	
		/**
	 * Helper method to get product post objects
	 *
	 * @since 2.1
	 * @param array $args request arguments for filtering query
	 * @return WP_Query
	 */
	private function query_products( $args ) {

		// Set base query arguments
		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'product',
			'post_status' => 'publish',
			'meta_query'  => array(),
		);

		if ( ! empty( $args['type'] ) ) {

			$types = explode( ',', $args['type'] );

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $types,
				),
			);

			unset( $args['type'] );
		}

		// Filter products by category
		if ( ! empty( $args['category'] ) ) {
			$query_args['product_cat'] = $args['category'];
		}

		// Filter products by tag
		if ( ! empty( $args['tag'] ) ) {
			$query_args['product_tag'] = $args['tag'];
		}
		
		// Filter by specific sku
		if ( ! empty( $args['sku'] ) ) {
			if ( ! is_array( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = array();
			}

			$query_args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => $args['sku'],
				'compare' => '='
			);
		}

		$query_args = $this->merge_query_args( $query_args, $args );

		return new WP_Query( $query_args );
	}

		protected function merge_query_args( $base_args, $request_args ) {

		$args = array();

		// search
		if ( ! empty( $request_args['q'] ) ) {
			$args['s'] = $request_args['q'];
		}

		// resources per response
		if ( ! empty( $request_args['limit'] ) ) {
			$args['posts_per_page'] = $request_args['limit'];
		}

		// resource offset
		if ( ! empty( $request_args['offset'] ) ) {
			$args['offset'] = $request_args['offset'];
		}

		// order (ASC or DESC, ASC by default)
		if ( ! empty( $request_args['order'] ) ) {
			$args['order'] = $request_args['order'];
		}

		// orderby
		if ( ! empty( $request_args['orderby'] ) ) {
			$args['orderby'] = $request_args['orderby'];

			// allow sorting by meta value
			if ( ! empty( $request_args['orderby_meta_key'] ) ) {
				$args['meta_key'] = $request_args['orderby_meta_key'];
			}
		}

		// allow post status change
		if ( ! empty( $request_args['post_status'] ) ) {
			$args['post_status'] = $request_args['post_status'];
			unset( $request_args['post_status'] );
		}

		// filter by a list of post id
		if ( ! empty( $request_args['in'] ) ) {
			$args['post__in'] = explode( ',', $request_args['in'] );
			unset( $request_args['in'] );
		}

		// exclude by a list of post id
		if ( ! empty( $request_args['not_in'] ) ) {
			$args['post__not_in'] = explode( ',', $request_args['not_in'] );
			unset( $request_args['not_in'] );
		}

		// resource page
		$args['paged'] = ( isset( $request_args['page'] ) ) ? absint( $request_args['page'] ) : 1;

		$args = apply_filters( 'woocommerce_api_query_args', $args, $request_args );

		return array_merge( $base_args, $args );
	}
	
        public function get_products_v2($data){
            return $this->get_products($data['fields'], $data['type'], $data['filter'], $data['page'], 'view');
        }

        public function get_products( $fields = null, $type = null, $filter = array(), $page = 1, $context = 'view' ) {
		global $woocommerce;
		global $wp;
		$woocommerce->api->includes();
		$this->wooserver = new WC_API_Server( $wp->query_vars['wc-api-route'] );

		if ( ! empty( $type ) ) {
			$filter['type'] = $type;
		}

		$filter['page'] = $page;

		$query = $this->query_products( $filter );

		$products = array();

		foreach ( $query->posts as $product_id ) {
			$products[] = current( $this->get_product( $product_id, $fields ) );
		}

		return $products;
	}
        
        public function get_customer_info_v2($data){
            return $this->get_customer_info('view');
        }

        public function get_customer_info($context = 'view'){
		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return new WP_Error( 'json_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
		}
		
		return $this->get_customer($current_user_id);
	}
	
	public function create_customer( $data ) {
		global $woocommerce;
		global $wp;
		$woocommerce->api->includes();
		try {
			if ( ! isset( $data['customer'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_customer_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'customer' ), 400 );
			}

			$customer = $data['customer'];
			$customer['password'] = $data['password'];
			$data = $customer;
			$data = apply_filters( 'woocommerce_api_create_customer_data', $data, $this );

			// Checks with the email is missing.
			if ( ! isset( $data['email'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_customer_email', sprintf( __( 'Missing parameter %s', 'woocommerce' ), 'email' ), 400 );
			}

			// Sets the username.
			$data['username'] = ! empty( $data['username'] ) ? $data['username'] : '';

			// Sets the password.
			$data['password'] = ! empty( $data['password'] ) ? $data['password'] : '';

			// Attempts to create the new customer
			$id = wc_create_new_customer( $data['email'], $data['username'], $data['password'] );

			// Checks for an error in the customer creation.
			if ( is_wp_error( $id ) ) {
				throw new WC_API_Exception( $id->get_error_code(), $id->get_error_message(), 400 );
			}

			// Added customer data.
			$this->update_customer_data( $id, $data );

			do_action( 'woocommerce_api_create_customer', $id, $data );

			//$this->server->send_status( 201 );

			return $this->get_customer( $id );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
	
	/**
	 * Format a unix timestamp or MySQL datetime into an RFC3339 datetime
	 *
	 * @since 2.1
	 * @param int|string $timestamp unix timestamp or MySQL datetime
	 * @param bool $convert_to_utc
	 * @return string RFC3339 datetime
	 */
	public function format_datetime( $timestamp, $convert_to_utc = false ) {

		if ( $convert_to_utc ) {
			$timezone = new DateTimeZone( wc_timezone_string() );
		} else {
			$timezone = new DateTimeZone( 'UTC' );
		}

		try {

			if ( is_numeric( $timestamp ) ) {
				$date = new DateTime( "@{$timestamp}" );
			} else {
				$date = new DateTime( $timestamp, $timezone );
			}

			// convert to UTC by adjusting the time based on the offset of the site's timezone
			if ( $convert_to_utc ) {
				$date->modify( -1 * $date->getOffset() . ' seconds' );
			}

		} catch ( Exception $e ) {

			$date = new DateTime( '@0' );
		}

		return $date->format( 'Y-m-d\TH:i:s\Z' );
	}
	/**
	 * Get the customer for the given ID
	 *
	 * @since 2.1
	 * @param int $id the customer ID
	 * @param array $fields
	 * @return array
	 */
	public function get_customer( $id, $fields = null ) {
		global $wpdb;

		//$id = $this->validate_request( $id, 'customer', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$customer = new WP_User( $id );

		// Get info about user's last order
		$last_order = $wpdb->get_row( "SELECT id, post_date_gmt
						FROM $wpdb->posts AS posts
						LEFT JOIN {$wpdb->postmeta} AS meta on posts.ID = meta.post_id
						WHERE meta.meta_key = '_customer_user'
						AND   meta.meta_value = {$customer->ID}
						AND   posts.post_type = 'shop_order'
						AND   posts.post_status IN ( '" . implode( "','", array_keys( wc_get_order_statuses() ) ) . "' )
						ORDER BY posts.ID DESC
					" );

		$customer_data = array(
			'id'               => $customer->ID,
			'created_at'       => $this->format_datetime( $customer->user_registered ),
			'email'            => $customer->user_email,
			'first_name'       => $customer->first_name,
			'last_name'        => $customer->last_name,
			'username'         => $customer->user_login,
			//'role'             => $customer->roles[0],
			//'last_order_id'    => is_object( $last_order ) ? $last_order->id : null,
			//'last_order_date'  => is_object( $last_order ) ? $this->format_datetime( $last_order->post_date_gmt ) : null,
			//'orders_count'     => wc_get_customer_order_count( $customer->ID ),
			//'total_spent'      => wc_format_decimal( wc_get_customer_total_spent( $customer->ID ), 2 ),
			//'avatar_url'       => $this->get_avatar_url( $customer->customer_email ),
			'billing_address'  => array(
				'first_name' => $customer->billing_first_name,
				'last_name'  => $customer->billing_last_name,
				'company'    => $customer->billing_company,
				'address_1'  => $customer->billing_address_1,
				'address_2'  => $customer->billing_address_2,
				'city'       => $customer->billing_city,
				'state'      => $customer->billing_state,
				'postcode'   => $customer->billing_postcode,
				'country'    => $customer->billing_country,
				'email'      => $customer->billing_email,
				'phone'      => $customer->billing_phone,
			),
			'shipping_address' => array(
				'first_name' => $customer->shipping_first_name,
				'last_name'  => $customer->shipping_last_name,
				'company'    => $customer->shipping_company,
				'address_1'  => $customer->shipping_address_1,
				'address_2'  => $customer->shipping_address_2,
				'city'       => $customer->shipping_city,
				'state'      => $customer->shipping_state,
				'postcode'   => $customer->shipping_postcode,
				'country'    => $customer->shipping_country,
			),
		);

		return apply_filters( 'woocommerce_api_customer_response', $customer_data, $customer, $fields, $this->server ) ;
	}
	
	protected function update_customer_data( $id, $data ) {
		// Customer first name.
		if ( isset( $data['first_name'] ) ) {
			update_user_meta( $id, 'first_name', wc_clean( $data['first_name'] ) );
		}

		// Customer last name.
		if ( isset( $data['last_name'] ) ) {
			update_user_meta( $id, 'last_name', wc_clean( $data['last_name'] ) );
		}

		/*
		// Customer billing address.
		if ( isset( $data['billing_address'] ) ) {
			foreach ( $this->get_customer_billing_address() as $address ) {
				if ( isset( $data['billing_address'][ $address ] ) ) {
					update_user_meta( $id, 'billing_' . $address, wc_clean( $data['billing_address'][ $address ] ) );
				}
			}
		}

		// Customer shipping address.
		if ( isset( $data['shipping_address'] ) ) {
			foreach ( $this->get_customer_shipping_address() as $address ) {
				if ( isset( $data['shipping_address'][ $address ] ) ) {
					update_user_meta( $id, 'shipping_' . $address, wc_clean( $data['shipping_address'][ $address ] ) );
				}
			}
		}*/

		do_action( 'woocommerce_api_update_customer_data', $id, $data );
	}
	
        public function get_customer_order_v2($data){
            return $this->get_customer_order($data['orderId'], 'view');
        }

        public function get_customer_order( $orderId, $context = 'view'  ) {
		global $woocommerce;
		global $wp;
		
		
		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return new WP_Error( 'json_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
		}
		$woocommerce->api->includes();
		$order = $this->get_order($orderId);
		if(isset($order) && isset($order['customer_id'])){
			if($order['customer_id'] != $current_user_id){
				return new WP_Error( 'json_not_logged_in', __( 'This order does not belong to you.' ), array( 'status' => 401 ) );
			}
		}
		return $order;
	}
	
	private function getCustomCouponLines($coupon_lines){
	
		if(isset($coupon_lines) && !empty($coupon_lines)){
			$custom_lines =  array();
			
			foreach($coupon_lines as $coupon_line){
				$custom_line =  array();
				if(isset($coupon_line['code']) && isset($coupon_line['amount'])){
					$custom_line['title'] = $coupon_line['code'];
					$custom_line['total'] = 0 - $coupon_line['amount'];
					$custom_lines[] = $custom_line;
				}
				
			}
			return $custom_lines;
		}
	}
	/**
	 * Create an order
	 *
	 * @since 2.2
	 * @param array $data raw order data
	 * @return array
	 */
	public function create_order( $data ) {
		global $woocommerce;
		global $wp;
		
		
		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return new WP_Error( 'json_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
		}
		
		$woocommerce->api->includes();
		$this->wooserver = new WC_API_Server( $wp->query_vars['wc-api-route'] );
		global $wpdb;

		$wpdb->query( 'START TRANSACTION' );

		try {
			if ( ! isset( $data['order'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_order_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'order' ), 400 );
			}

			$data = $data['order'];

			if($data['customer_id'] != $current_user_id){
				throw new WC_API_Exception( 'woocommerce_api_missing_order_data', sprintf( __( 'Please relogin.' ), 'order' ), 400 );
			}
			$data = apply_filters( 'woocommerce_api_create_order_data', $data, $this );

			// default order args, note that status is checked for validity in wc_create_order()
			$default_order_args = array(
				'status'        => isset( $data['status'] ) ? $data['status'] : '',
				'customer_note' => isset( $data['note'] ) ? $data['note'] : null,
			);

			// if creating order for existing customer
			if ( ! empty( $data['customer_id'] ) ) {

				// make sure customer exists
				if ( false === get_user_by( 'id', $data['customer_id'] ) ) {
					throw new WC_API_Exception( 'woocommerce_api_invalid_customer_id', __( 'Customer ID is invalid', 'woocommerce' ), 400 );
				}

				$default_order_args['customer_id'] = $data['customer_id'];
			}

			// create the pending order
			$order = $this->create_base_order( $default_order_args, $data );

			if ( is_wp_error( $order ) ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_create_order', sprintf( __( 'Cannot create order: %s', 'woocommerce' ), implode( ', ', $order->get_error_messages() ) ), 400 );
			}

			// billing/shipping addresses
			$this->set_order_addresses( $order, $data );
			
			$data['fee_lines'] = $this->getCustomCouponLines($data['coupon_lines']);
			unset($data['coupon_lines']);
			$lines = array(
				'line_item' => 'line_items',
				'shipping'  => 'shipping_lines',
				'fee'       => 'fee_lines',
				'coupon'    => 'coupon_lines',
			);

			
			foreach ( $lines as $line_type => $line ) {

				if ( isset( $data[ $line ] ) && is_array( $data[ $line ] ) ) {

					$set_item = "set_{$line_type}";

					foreach ( $data[ $line ] as $item ) {

						$this->$set_item( $order, $item, 'create' );
					}
				}
			}

			// calculate totals and set them
			$order->calculate_totals();

			// payment method (and payment_complete() if `paid` == true)
			if ( isset( $data['payment_details'] ) && is_array( $data['payment_details'] ) ) {

				// method ID & title are required
				if ( empty( $data['payment_details']['method_id'] ) || empty( $data['payment_details']['method_title'] ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_payment_details', __( 'Payment method ID and title are required', 'woocommerce' ), 400 );
				}

				update_post_meta( $order->id, '_payment_method', $data['payment_details']['method_id'] );
				update_post_meta( $order->id, '_payment_method_title', $data['payment_details']['method_title'] );

				// mark as paid if set
				/*if ( isset( $data['payment_details']['paid'] ) && true === $data['payment_details']['paid'] ) {
					$order->payment_complete( isset( $data['payment_details']['transaction_id'] ) ? $data['payment_details']['transaction_id'] : '' );
				}*/
			}else{
				throw new WC_API_Exception( 'woocommerce_invalid_payment_details', __( 'Payment method ID and title are required', 'woocommerce' ), 400 );
			}

			// set order currency
			if ( isset( $data['currency'] ) ) {

				if ( ! array_key_exists( $data['currency'], get_woocommerce_currencies() ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_order_currency', __( 'Provided order currency is invalid', 'woocommerce'), 400 );
				}

				update_post_meta( $order->id, '_order_currency', $data['currency'] );
			}

			// set order number
			if ( isset( $data['order_number'] ) ) {

				update_post_meta( $order->id, '_order_number', $data['order_number'] );
			}

			// set order meta
			if ( isset( $data['order_meta'] ) && is_array( $data['order_meta'] ) ) {
				$this->set_order_meta( $order->id, $data['order_meta'] );
			}

			// HTTP 201 Created
			//$this->server->send_status( 201 );

			wc_delete_shop_order_transients( $order->id );

			do_action( 'woocommerce_api_create_order', $order->id, $data, $this );

			$wpdb->query( 'COMMIT' );

			return $this->get_order( $order->id );

		} catch ( WC_API_Exception $e ) {
			$wpdb->query( 'ROLLBACK' );

			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
	
	protected function set_fee( $order, $fee, $action ) {

		if ( 'create' === $action ) {

			// fee title is required
			if ( ! isset( $fee['title'] ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_fee_item', __( 'Fee title is required', 'woocommerce' ), 400 );
			}

			$order_fee            = new stdClass();
			$order_fee->id        = sanitize_title( $fee['title'] );
			$order_fee->name      = $fee['title'];
			$order_fee->amount    = isset( $fee['total'] ) ? floatval( $fee['total'] ) : 0;
			$order_fee->taxable   = false;
			$order_fee->tax       = 0;
			$order_fee->tax_data  = array();
			$order_fee->tax_class = '';

			// if taxable, tax class and total are required
			if ( isset( $fee['taxable'] ) && $fee['taxable'] ) {

				if ( ! isset( $fee['tax_class'] ) ) {
					throw new WC_API_Exception( 'woocommerce_invalid_fee_item', __( 'Fee tax class is required when fee is taxable', 'woocommerce' ), 400 );
				}

				$order_fee->taxable   = true;
				$order_fee->tax_class = $fee['tax_class'];

				if ( isset( $fee['total_tax'] ) ) {
					$order_fee->tax = isset( $fee['total_tax'] ) ? wc_format_refund_total( $fee['total_tax'] ) : 0;
				}

				if ( isset( $fee['tax_data'] ) ) {
					$order_fee->tax      = wc_format_refund_total( array_sum( $fee['tax_data'] ) );
					$order_fee->tax_data = array_map( 'wc_format_refund_total', $fee['tax_data'] );
				}
			}

			$fee_id = $order->add_fee( $order_fee );

			if ( ! $fee_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_create_fee', __( 'Cannot create fee, try again', 'woocommerce' ), 500 );
			}

		} else {

			$fee_args = array();

			if ( isset( $fee['title'] ) ) {
				$fee_args['name'] = $fee['title'];
			}

			if ( isset( $fee['tax_class'] ) ) {
				$fee_args['tax_class'] = $fee['tax_class'];
			}

			if ( isset( $fee['total'] ) ) {
				$fee_args['line_total'] = floatval( $fee['total'] );
			}

			if ( isset( $fee['total_tax'] ) ) {
				$fee_args['line_tax'] = floatval( $fee['total_tax'] );
			}

			$fee_id = $order->update_fee( $fee['id'], $fee_args );

			if ( ! $fee_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_fee', __( 'Cannot update fee, try again', 'woocommerce' ), 500 );
			}
		}
	}

	public function get_order( $id, $fields = null, $filter = array() ) {

		// ensure order ID is valid & user has permission to read
		//$id = $this->validate_request( $id, $this->post_type, 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		// Get the decimal precession
		$dp         = ( isset( $filter['dp'] ) ? intval( $filter['dp'] ) : 2 );
		$order      = wc_get_order( $id );
		$order_post = get_post( $id );
		if(!isset($order) || empty($order)){
			return new WP_Error( 'json_not_logged_in', __( 'Order does not exist.' ), array( 'status' => 404 ) );
		}
		$order_data = array(
			'id'                        => $order->id,
			'order_number'              => $order->get_order_number(),
			'created_at'                => $this->format_datetime( $order_post->post_date_gmt ),
			'updated_at'                => $this->format_datetime( $order_post->post_modified_gmt ),
			'completed_at'              => $this->format_datetime( $order->completed_date, true ),
			'status'                    => $order->get_status(),
			'currency'                  => $order->get_order_currency(),
			'total'                     => wc_format_decimal( $order->get_total(), $dp ),
			'subtotal'                  => wc_format_decimal( $order->get_subtotal(), $dp ),
			'total_line_items_quantity' => $order->get_item_count(),
			'total_tax'                 => wc_format_decimal( $order->get_total_tax(), $dp ),
			'total_shipping'            => wc_format_decimal( $order->get_total_shipping(), $dp ),
			'cart_tax'                  => wc_format_decimal( $order->get_cart_tax(), $dp ),
			'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax(), $dp ),
			'total_discount'            => wc_format_decimal( $order->get_total_discount(), $dp ),
			'shipping_methods'          => $order->get_shipping_method(),
			'payment_details' => array(
				'method_id'    => $order->payment_method,
				'method_title' => $order->payment_method_title,
				'paid'         => isset( $order->paid_date ),
			),
			'billing_address' => array(
				'first_name' => $order->billing_first_name,
				'last_name'  => $order->billing_last_name,
				'company'    => $order->billing_company,
				'address_1'  => $order->billing_address_1,
				'address_2'  => $order->billing_address_2,
				'city'       => $order->billing_city,
				'state'      => $order->billing_state,
				'postcode'   => $order->billing_postcode,
				'country'    => $order->billing_country,
				'email'      => $order->billing_email,
				'phone'      => $order->billing_phone,
			),
			'shipping_address' => array(
				'first_name' => $order->shipping_first_name,
				'last_name'  => $order->shipping_last_name,
				'company'    => $order->shipping_company,
				'address_1'  => $order->shipping_address_1,
				'address_2'  => $order->shipping_address_2,
				'city'       => $order->shipping_city,
				'state'      => $order->shipping_state,
				'postcode'   => $order->shipping_postcode,
				'country'    => $order->shipping_country,
			),
			'note'                      => $order->customer_note,
			'customer_ip'               => $order->customer_ip_address,
			'customer_user_agent'       => $order->customer_user_agent,
			'customer_id'               => $order->get_user_id(),
			'view_order_url'            => $order->get_view_order_url(),
			'line_items'                => array(),
			'shipping_lines'            => array(),
			'tax_lines'                 => array(),
			'fee_lines'                 => array(),
			'coupon_lines'              => array(),
		);

		// add line items
		foreach ( $order->get_items() as $item_id => $item ) {

			$product     = $order->get_product_from_item( $item );
			$product_id  = null;
			$product_sku = null;

			// Check if the product exists.
			if ( is_object( $product ) ) {
				$product_id  = ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id;
				$product_sku = $product->get_sku();
			}

			$meta = new WC_Order_Item_Meta( $item, $product );

			$item_meta = array();

			$hideprefix = ( isset( $filter['all_item_meta'] ) && $filter['all_item_meta'] === 'true' ) ? null : '_';

			foreach ( $meta->get_formatted( $hideprefix ) as $meta_key => $formatted_meta ) {
				$item_meta[] = array(
					'key'   => $formatted_meta['key'],
					'label' => $formatted_meta['label'],
					'value' => $formatted_meta['value'],
				);
			}

			$order_data['line_items'][] = array(
				'id'           => $item_id,
				'subtotal'     => wc_format_decimal( $order->get_line_subtotal( $item, false, false ), $dp ),
				'subtotal_tax' => wc_format_decimal( $item['line_subtotal_tax'], $dp ),
				'total'        => wc_format_decimal( $order->get_line_total( $item, false, false ), $dp ),
				'total_tax'    => wc_format_decimal( $item['line_tax'], $dp ),
				'price'        => wc_format_decimal( $order->get_item_total( $item, false, false ), $dp ),
				'quantity'     => wc_stock_amount( $item['qty'] ),
				'tax_class'    => ( ! empty( $item['tax_class'] ) ) ? $item['tax_class'] : null,
				'name'         => $item['name'],
				'product_id'   => $product_id,
				'sku'          => $product_sku,
				'meta'         => $item_meta,
			);
		}

		// add shipping
		foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {

			$order_data['shipping_lines'][] = array(
				'id'           => $shipping_item_id,
				'method_id'    => $shipping_item['method_id'],
				'method_title' => $shipping_item['name'],
				'total'        => wc_format_decimal( $shipping_item['cost'], $dp ),
			);
		}

		// add taxes
		foreach ( $order->get_tax_totals() as $tax_code => $tax ) {

			$order_data['tax_lines'][] = array(
				'id'       => $tax->id,
				'rate_id'  => $tax->rate_id,
				'code'     => $tax_code,
				'title'    => $tax->label,
				'total'    => wc_format_decimal( $tax->amount, $dp ),
				'compound' => (bool) $tax->is_compound,
			);
		}

		// add fees
		foreach ( $order->get_fees() as $fee_item_id => $fee_item ) {

			$order_data['fee_lines'][] = array(
				'id'        => $fee_item_id,
				'title'     => $fee_item['name'],
				'tax_class' => ( ! empty( $fee_item['tax_class'] ) ) ? $fee_item['tax_class'] : null,
				'total'     => wc_format_decimal( $order->get_line_total( $fee_item ), $dp ),
				'total_tax' => wc_format_decimal( $order->get_line_tax( $fee_item ), $dp ),
			);
		}

		// add coupons
		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon_item ) {

			$order_data['coupon_lines'][] = array(
				'id'     => $coupon_item_id,
				'code'   => $coupon_item['name'],
				'amount' => wc_format_decimal( $coupon_item['discount_amount'], $dp ),
			);
		}

		return apply_filters( 'woocommerce_api_order_response', $order_data, $order, $fields, $this->wooserver );
	}

	/**
	 * Given a product ID & API provided variations, find the correct variation ID to use for calculation
	 * We can't just trust input from the API to pass a variation_id manually, otherwise you could pass
	 * the cheapest variation ID but provide other information so we have to look up the variation ID.
	 *
	 * @param  WC_Product $product Product instance
	 * @return int                 Returns an ID if a valid variation was found for this product
	 */
	public function get_variation_id( $product, $variations = array() ) {
		$variation_id = null;
		$variations_normalized = array();

		if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			if ( isset( $variations ) && is_array( $variations ) ) {
				// start by normalizing the passed variations
				foreach ( $variations as $key => $value ) {
					$key = str_replace( 'attribute_', '', str_replace( 'pa_', '', $key ) ); // from get_attributes in class-wc-api-products.php
					$variations_normalized[ $key ] = strtolower( $value );
				}
				// now search through each product child and see if our passed variations match anything
				foreach ( $product->get_children() as $variation ) {
					$meta = array();
					foreach ( get_post_meta( $variation ) as $key => $value ) {
						$value = $value[0];
						$key = str_replace( 'attribute_', '', str_replace( 'pa_', '', $key ) );
						$meta[ $key ] = strtolower( $value );
					}
					// if the variation array is a part of the $meta array, we found our match
					if ( $this->array_contains( $variations_normalized, $meta ) ) {
						$variation_id = $variation;
						break;
					}
				}
			}
		}

		return $variation_id;
	}

	/**
	 * Utility function to see if the meta array contains data from variations
	 */
	protected function array_contains( $needles, $haystack ) {
		foreach ( $needles as $key => $value ) {
			if ( $haystack[ $key ] !== $value ) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Create or update an order coupon
	 *
	 * @since 2.2
	 * @param \WC_Order $order
	 * @param array $coupon item data
	 * @param string $action 'create' to add coupon or 'update' to update it
	 * @throws WC_API_Exception invalid data, server error
	 */
	protected function set_coupon( $order, $coupon, $action ) {

		// coupon amount must be positive float
		if ( isset( $coupon['amount'] ) && floatval( $coupon['amount'] ) < 0 ) {
			throw new WC_API_Exception( 'woocommerce_invalid_coupon_total', __( 'Coupon discount total must be a positive amount', 'woocommerce' ), 400 );
		}

		if ( 'create' === $action ) {

			// coupon code is required
			if ( empty( $coupon['code'] ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_coupon_coupon', __( 'Coupon code is required', 'woocommerce' ), 400 );
			}

			$coupon_id = $order->add_coupon( $coupon['code'], isset( $coupon['amount'] ) ? floatval( $coupon['amount'] ) : 0 );

			if ( ! $coupon_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_create_order_coupon', __( 'Cannot create coupon, try again', 'woocommerce' ), 500 );
			}

		} else {

			$coupon_args = array();

			if ( isset( $coupon['code'] ) ) {
				$coupon_args['code'] = $coupon['code'];
			}

			if ( isset( $coupon['amount'] ) ) {
				$coupon_args['discount_amount'] = floatval( $coupon['amount'] );
			}

			$coupon_id = $order->update_coupon( $coupon['id'], $coupon_args );

			if ( ! $coupon_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_order_coupon', __( 'Cannot update coupon, try again', 'woocommerce' ), 500 );
			}
		}
	}

	
	protected function set_order_addresses( $order, $data ) {

		$address_fields = array(
			'first_name',
			'last_name',
			'company',
			'email',
			'phone',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
		);

		$billing_address = $shipping_address = array();

		// billing address
		if ( isset( $data['billing_address'] ) && is_array( $data['billing_address'] ) ) {

			foreach ( $address_fields as $field ) {

				if ( isset( $data['billing_address'][ $field ] ) ) {
					$billing_address[ $field ] = wc_clean( $data['billing_address'][ $field ] );
				}
			}

			unset( $address_fields['email'] );
			unset( $address_fields['phone'] );
		}

		// shipping address
		if ( isset( $data['shipping_address'] ) && is_array( $data['shipping_address'] ) ) {

			foreach ( $address_fields as $field ) {

				if ( isset( $data['shipping_address'][ $field ] ) ) {
					$shipping_address[ $field ] = wc_clean( $data['shipping_address'][ $field ] );
				}
			}
		}

		$order->set_address( $billing_address, 'billing' );
		$order->set_address( $shipping_address, 'shipping' );

		// update user meta
		if ( $order->get_user_id() ) {
			foreach ( $billing_address as $key => $value ) {
				update_user_meta( $order->get_user_id(), 'billing_' . $key, $value );
			}
			foreach ( $shipping_address as $key => $value ) {
				update_user_meta( $order->get_user_id(), 'shipping_' . $key, $value );
			}
		}
	}
	
		protected function set_line_item( $order, $item, $action ) {

		$creating = ( 'create' === $action );
		$item_args = array();
		// product is always required
		if ( ! isset( $item['product_id'] ) && ! isset( $item['sku'] ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_id', __( 'Product ID or SKU is required', 'woocommerce' ), 400 );
		}

		// when updating, ensure product ID provided matches
		if ( 'update' === $action ) {

			$item_product_id   = wc_get_order_item_meta( $item['id'], '_product_id' );
			$item_variation_id = wc_get_order_item_meta( $item['id'], '_variation_id' );

			if ( $item['product_id'] != $item_product_id && $item['product_id'] != $item_variation_id ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_id', __( 'Product ID provided does not match this line item', 'woocommerce' ), 400 );
			}
		}

		if ( isset( $item['product_id'] ) ) {
			$product_id = $item['product_id'];
		} elseif ( isset( $item['sku'] ) ) {
			$product_id = wc_get_product_id_by_sku( $item['sku'] );
		}

		// variations must each have a key & value
		$variation_id = 0;
		if ( isset( $item['variations'] ) && is_array( $item['variations'] ) ) {
			foreach ( $item['variations'] as $key => $value ) {
				if ( ! $key || ! $value ) {
					throw new WC_API_Exception( 'woocommerce_api_invalid_product_variation', __( 'The product variation is invalid', 'woocommerce' ), 400 );
				}
			}
			$item_args['variation'] = $item['variations'];
			$variation_id = $this->get_variation_id( wc_get_product( $product_id ), $item_args['variation'] );
		}

		$product = wc_get_product( $variation_id ? $variation_id : $product_id );

		// must be a valid WC_Product
		if ( ! is_object( $product ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product', __( 'Product is invalid', 'woocommerce' ), 400 );
		}

		// quantity must be positive float
		if ( isset( $item['quantity'] ) && floatval( $item['quantity'] ) <= 0 ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_quantity', __( 'Product quantity must be a positive float', 'woocommerce' ), 400 );
		}

		// quantity is required when creating
		if ( $creating && ! isset( $item['quantity'] ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_quantity', __( 'Product quantity is required', 'woocommerce' ), 400 );
		}

		

		// quantity
		if ( isset( $item['quantity'] ) ) {
			$item_args['qty'] = $item['quantity'];
		}

		// total
		if ( isset( $item['total'] ) ) {
			$item_args['totals']['total'] = floatval( $item['total'] );
		}

		// total tax
		if ( isset( $item['total_tax'] ) ) {
			$item_args['totals']['tax'] = floatval( $item['total_tax'] );
		}

		// subtotal
		if ( isset( $item['subtotal'] ) ) {
			$item_args['totals']['subtotal'] = floatval( $item['subtotal'] );
		}

		// subtotal tax
		if ( isset( $item['subtotal_tax'] ) ) {
			$item_args['totals']['subtotal_tax'] = floatval( $item['subtotal_tax'] );
		}

		if ( $creating ) {

			$item_id = $order->add_product( $product, $item_args['qty'], $item_args );

			if ( ! $item_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_create_line_item', __( 'Cannot create line item, try again', 'woocommerce' ), 500 );
			}

		} else {

			$item_id = $order->update_product( $item['id'], $product, $item_args );

			if ( ! $item_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_line_item', __( 'Cannot update line item, try again', 'woocommerce' ), 500 );
			}
		}
	}

	
	protected function set_item( $order, $item_type, $item, $action ) {
		global $wpdb;

		$set_method = "set_{$item_type}";

		// verify provided line item ID is associated with order
		if ( 'update' === $action ) {

			$result = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d AND order_id = %d",
				absint( $item['id'] ),
				absint( $order->id )
			) );

			if ( is_null( $result ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_item_id', __( 'Order item ID provided is not associated with order', 'woocommerce' ), 400 );
			}
		}

		$this->$set_method( $order, $item, $action );
	}

		protected function set_shipping( $order, $shipping, $action ) {

		// total must be a positive float
		if ( isset( $shipping['total'] ) && floatval( $shipping['total'] ) < 0 ) {
			throw new WC_API_Exception( 'woocommerce_invalid_shipping_total', __( 'Shipping total must be a positive amount', 'woocommerce' ), 400 );
		}

		if ( 'create' === $action ) {

			// method ID is required
			if ( ! isset( $shipping['method_id'] ) ) {
				throw new WC_API_Exception( 'woocommerce_invalid_shipping_item', __( 'Shipping method ID is required', 'woocommerce' ), 400 );
			}

			$rate = new WC_Shipping_Rate( $shipping['method_id'], isset( $shipping['method_title'] ) ? $shipping['method_title'] : '', isset( $shipping['total'] ) ? floatval( $shipping['total'] ) : 0, array(), $shipping['method_id'] );

			$shipping_id = $order->add_shipping( $rate );

			if ( ! $shipping_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_create_shipping', __( 'Cannot create shipping method, try again', 'woocommerce' ), 500 );
			}

		} else {

			$shipping_args = array();

			if ( isset( $shipping['method_id'] ) ) {
				$shipping_args['method_id'] = $shipping['method_id'];
			}

			if ( isset( $shipping['method_title'] ) ) {
				$shipping_args['method_title'] = $shipping['method_title'];
			}

			if ( isset( $shipping['total'] ) ) {
				$shipping_args['cost'] = floatval( $shipping['total'] );
			}

			$shipping_id = $order->update_shipping( $shipping['id'], $shipping_args );

			if ( ! $shipping_id ) {
				throw new WC_API_Exception( 'woocommerce_cannot_update_shipping', __( 'Cannot update shipping method, try again', 'woocommerce' ), 500 );
			}
		}
	}
	
	protected function create_base_order( $args, $data ) {
		return wc_create_order( $args );
	}

        protected function process_payment_v2($data){
            return $this->process_payment($data['orderId'], $data['gateway'], $data['e'], 'view');
        }
        /*
	reference: http://stackoverflow.com/questions/31787244/woocommerce-create-an-order-programmatically-and-redirect-to-payment
	https://github.com/woothemes/woocommerce/issues/4169
	*/
	public function process_payment($orderId, $gateway, $e, $context = 'view' ) {
		global $woocommerce;
		$woocommerce->api->includes();
		
		try{
			if(!isset($orderId) || empty($orderId) || empty($e)){
				throw new WC_API_Exception( 'woocommerce_request_not_valid', __( 'Invalid Request', 'woocommerce' ), 403 );
			}
			$order = $this->get_order($orderId);

			if(isset($order) && isset($order['billing_address'])){
				if($order['billing_address']['email'] != $e){
					throw new WC_API_Exception( 'woocommerce_not_authorized', __( 'Not Authorized', 'woocommerce' ), 401 );
				}
			}else{
				throw new WC_API_Exception( 'woocommerce_not_authorized', __( 'Not Authorized', 'woocommerce' ), 401 );
			}
			// Store Order ID in session so it can be re-used after payment failure
			$woocommerce->session->order_awaiting_payment = $orderId;

			// Process Payment
			$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
			if(empty($available_gateways[$gateway])){
				throw new WC_API_Exception( 'woocommerce_gatewat_not_valid', __( "payment gaeway $gateway is not available", 'woocommerce' ), 401 );
			}
			$result = $available_gateways[ $gateway ]->process_payment( $orderId );

			// Redirect to success/confirmation/payment page
			if ( $result['result'] == 'success' ) {

				$result = apply_filters( 'woocommerce_payment_successful_result', $result, $orderId );

				wp_redirect( $result['redirect'] );
				exit;
			}
		}
		catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}
?>