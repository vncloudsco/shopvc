<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class Pw_Wp_Woocommerce {
	
	protected $wooserver;
	
	protected $options;

	protected $languageOptions;

	public function __construct( ) {
            if(!class_exists('WP_JSON_Posts')){
                add_action( 'rest_api_init', function () {
                    register_rest_route( 'androapp/v2', '/androappaddtocart', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'add_to_cart'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androapp/addtocart/bulk', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'add_to_cart_bulk'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androappaddaddress', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'add_address'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappapplycoupon', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'apply_coupon'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappremovecoupon', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'remove_coupon'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappaddshippingmethod', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'add_shipping_method'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappupdatecart', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'update_cart_quantity'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappremovefromcart', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'remove_from_cart'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappgetcart', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'get_cart_v2'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappgetstates', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'get_states_v2'),
                    ) );
                    register_rest_route( 'androapp/v2', '/androappcart', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'cart_details'),
                    ) );
                } );
            }
                
            $this->options = get_option("pw-mobile-app");
            $this->languageOptions = get_option("pw-mobile-app-language");
	}

	/**
	 * Register the user-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes( $routes ) {
		$user_routes = array(
			// /users/me is an alias, and simply redirects to /users/<id>
			
			'/androappaddtocart' => array(
				array( array( $this, 'add_to_cart' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			
			'/androapp/addtocart/bulk' => array(
				array( array( $this, 'add_to_cart_bulk' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappaddaddress' => array(
				array( array( $this, 'add_address' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappapplycoupon' => array(
				array( array( $this, 'apply_coupon' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappremovecoupon' => array(
				array( array( $this, 'remove_coupon' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappaddshippingmethod' => array(
				array( array( $this, 'add_shipping_method' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappupdatecart' => array(
				array( array( $this, 'update_cart_quantity' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappremovefromcart' => array(
				array( array( $this, 'remove_from_cart' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
			'/androappgetcart' => array(
				array( array( $this, 'get_cart' ), WP_JSON_Server::READABLE ),
			),
			'/androappgetstates' => array(
				array( array( $this, 'get_states' ), WP_JSON_Server::READABLE ),
			),
			'/androappcart' => array(
				array( array( $this, 'cart_details' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
		);
		return array_merge( $routes, $user_routes );
	}
	
	public function add_address($data){
		global $woocommerce;

		$billing_address = $data['billing_address'];
		$woocommerce->customer->set_location( $billing_address['country'], $billing_address['state'], $billing_address['postcode'], $billing_address['city'] );

		$shipping_address = $data['shipping_address'];
		$woocommerce->customer->set_shipping_location( $shipping_address['country'], $shipping_address['state'], $shipping_address['postcode'], $shipping_address['city'] );
		
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
	}
	
	public function add_to_cart_bulk($data){
		global $woocommerce;
		if(isset($data)){
			foreach($data as $lineitem ){
				$this->add_to_cart($lineitem);
			}
		}
		
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
	}
	
	public function add_to_cart( $data ) {
		global $woocommerce;
		
		if(isset( $data['variation_id'])){
			if(!$woocommerce->cart->add_to_cart($data['product_id'], $data['quantity'], $data['variation_id'], $data['variations'] )){
				return new WP_Error( 'error_add_to_cart', $this->languageOptions['PRODUCT_ADD_TO_CART_ERROR'], array( 'status' => 403 ) );
			}
		}else{
			if(!$woocommerce->cart->add_to_cart($data['product_id'], $data['quantity'])){
				return new WP_Error( 'error_add_to_cart', $this->languageOptions['PRODUCT_ADD_TO_CART_ERROR'], array( 'status' => 403 ) );
			}
		}
		
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
		//return $woocommerce->cart;
	}
	
	public function remove_from_cart($data){
		global $woocommerce;
		$cart_item_key =  $data['cart_item_key'];
		unset( $woocommerce->cart->cart_contents[$cart_item_key] );
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
	}
	
	public function add_shipping_method( $data ) {
		global $woocommerce;
		$woocommerce->cart->calculate_totals();
		
		$shipping_method = array();
		$shipping_method[] = $data['method_id'];
		$woocommerce->session->set('chosen_shipping_methods', $shipping_method);
		$woocommerce->cart->calculate_shipping();
		
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
		//return $woocommerce->cart;
	}
	
	public function remove_coupon( $data ) {
		global $woocommerce;
		!$woocommerce->cart->remove_coupon( sanitize_text_field( $data['code']));
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
	}
	
	public function apply_coupon( $data ) {
		global $woocommerce;
		if(!$woocommerce->cart->add_discount( sanitize_text_field( $data['code']))){
			return new WP_Error( 'error_apply_coupon', $this->languageOptions['COUPON_APPLY_ERROR'], array( 'status' => 403 ) );
		}
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
	}
	
	private function getOutputFromCart($cart){
		global $woocommerce;
		$res = array();
		$res['cart_items'] = $this->getLineItems($cart);
		$res['coupon_lines'] = $this->getCouponLines($cart);
		$res['cart_contents_total'] = $cart->cart_contents_total;
		$res['cart_contents_weight'] =  $cart->cart_contents_weight;
		$res['cart_contents_count'] =  $cart->cart_contents_count;
		$res['total'] =  $cart->total;
		$res['subtotal'] =  $cart->subtotal;
		$res['subtotal_ex_tax'] =  $cart->subtotal_ex_tax;
		$res['tax_total'] =  $cart->tax_total;
		$cart->shipping_tax_total = array_sum( $cart->shipping_taxes );
		$res['shipping_total'] =  $cart->shipping_total + $cart->shipping_tax_total;
		$res['shipping_tax_total'] =  $cart->shipping_tax_total;
		$res['discount_cart'] =  $cart->discount_cart;
		
		$shippable_countries = $woocommerce->countries->get_shipping_countries();
		if(isset($shippable_countries) && count($shippable_countries) > 0){
			$res['shippable_countries'] = $shippable_countries;
		}
		
		return $res;
	}

	private function getShippingOutput($shipping){
		$shippinglines = array();
		if(is_array($shipping->get_packages()) && sizeof($shipping->get_packages()) > 0){
			$packages = $shipping->get_packages();
			if(is_array($packages[0])){
				foreach($packages[0]['rates'] as $shippingitem ){
					$shippingline = array();
					$shippingline['method_id'] = $shippingitem->method_id;
					$shippingline['method_title'] = $shippingitem->label;
					$shippingline['tax_total'] = array_sum( $shippingitem->taxes);
					$shippingline['total'] = $shippingitem->cost + $shippingline['tax_total'];
					
					$shippinglines[] = $shippingline;
				}
			}
		}
		$this->sksort($shippinglines, 'total', true);
		return $shippinglines;
	}
	
	function sksort(&$array, $subkey="id", $sort_ascending=false) {
		
		if (count($array))
		{
			$temp_array[key($array)] = array_shift($array);

			foreach($array as $key => $val){
				$offset = 0;
				$found = false;
				foreach($temp_array as $tmp_key => $tmp_val)
				{
					if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
					{
						$temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
													array($key => $val),
													array_slice($temp_array,$offset)
												  );
						$found = true;
					}
					$offset++;
				}
				if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
			}

			if ($sort_ascending) $array = array_reverse($temp_array);

			else $array = $temp_array;
		}
	}
	
	private function getCouponLines($cart){
		$couponLines = array();
		foreach($cart->applied_coupons as $coupon_code){
			$couponLine = array();
			
			$couponLine['code'] = $coupon_code;
			$couponLine['amount'] = $cart->coupon_discount_amounts[$coupon_code];
			$coupon_details = $cart->coupons[$coupon_code];
			$couponLine['id'] = $coupon_details->id;
			$couponLines[] = $couponLine;
		}
		return $couponLines;
	}
	
	private function getLineItems($cart){
		$lineitems = array();
		foreach($cart->cart_contents as $cart_item_key => $product_in_cart ){
			$lineitem = array();
			$lineitem['cart_item_key'] = $cart_item_key;
			$lineitem['productId'] = $product_in_cart['product_id'];
			$lineitem['quantity'] =  $product_in_cart['quantity'];
			$lineitem['variation_id'] =  $product_in_cart['variation_id'];
			if(isset($lineitem['variation_id']) && $lineitem['variation_id'] != 0){
				$lineitem['variation'] =  $product_in_cart['variation'];
			}
			
			$lineitem['sellingPrice'] =  $product_in_cart['line_total'];
			$lineitem['mrp'] =  $product_in_cart['line_total'];
			$lineitem['itemType'] =  'CART';
			$lineitem['title'] =  $product_in_cart['data']->post->post_title;
			
			if(has_post_thumbnail( $product_in_cart['product_id'] )){
				$attch = wp_get_attachment_image_src( get_post_thumbnail_id( $product_in_cart['product_id'] ), 'thumbnail');
				if(is_array($attch) && count($attch) > 0){
					$firstImage = $attch[0];
					$lineitem['image'] = $attch[0];
				}
			}
			$lineitems[] = $lineitem;
		}
		return $lineitems;
	}
	
	/* reference: http://stackoverflow.com/questions/32241270/error-updating-cart-item-quantity-using-ajax-woocommerce */
	public function update_cart_quantity($data){
		global $woocommerce;
		$cart_item_key =  $data['cart_item_key'];
		$quantity = $data['quantity'];
		$cartArray = $woocommerce->cart->get_cart();
		$values = $cartArray[ $cart_item_key ];

		$_product = $values['data'];

		
		// Sanitize
		$quantity = apply_filters( 'woocommerce_stock_amount_cart_item', 
		apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '',
		 filter_var($data['quantity'], FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

		if (!( '' === $quantity || $quantity == $values['quantity'] )){
			if(!$_product->has_enough_stock($quantity)){
				return new WP_Error( 'error_add_to_cart', $this->languageOptions['NOT_ENOUGH_STOCK'], array( 'status' => 403 ) );;
			}
			
			// Update cart validation
			$passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );
	
			// is_sold_individually
			if ( $_product->is_sold_individually() && $quantity > 1 ) {
				wc_add_notice( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_title() ), 'error' );
				$passed_validation = false;
			}
	
			if($passed_validation ){
				$woocommerce->cart->set_quantity($cart_item_key, $quantity, false );
			}
		}

		$woocommerce->cart->calculate_totals();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
	}
	
	/* reference http://stackoverflow.com/questions/19155682/woocommerce-get-and-set-shipping-billing-addresss-postcode */
	public function updateShipping(){
	
	}
	
        public function get_states_v2($data){
            return $this->get_states($data['cc'], 'view');
        }

        public function get_states($cc, $context = 'view'){
		global $woocommerce;
		return $woocommerce->countries->get_states($cc);
	}
	
        public function get_cart_v2($data){
            return $this->get_cart('view');
        }


        public function get_cart( $context = 'view' ) {
		global $woocommerce;	
		$woocommerce->cart->calculate_totals();
		$woocommerce->cart->calculate_shipping();
		$res = $this->getOutputFromCart($woocommerce->cart);
		$res['shipping_lines'] = $this->getShippingOutput($woocommerce->shipping);
		$res['payment_gateways'] =  $this->getAvailablePaymentGateways($woocommerce->payment_gateways->get_available_payment_gateways());
		return $res;
		//return $woocommerce->countries->get_shipping_country_states();		
		//return $woocommerce->cart;
	}
			
	private function getAvailablePaymentGateways($gateways){
		$payment_gateways = array();
		if(is_array($gateways) && sizeof($gateways) > 0){
			foreach($gateways as $key => $gateway ){
				$payment_gateway = array();
				$payment_gateway['method_id'] = $gateway->id;
				$payment_gateway['method_title'] = $gateway->title;
				$payment_gateway['description'] = $gateway->description;
				if(isset($gateway->instructions)){
					$payment_gateway['instructions'] = $gateway->instructions;
				}
				$payment_gateway['browser'] = 'webview';
				if($this->options['payweb'.$gateway->id] == '1'){
					$payment_gateway['browser'] = 'native';
				}
				if($this->options['pay'.$gateway->id] != '0'){
					$payment_gateways[] = $payment_gateway;
				}
				
			}
		}
		return $payment_gateways;
	}
	
	private function getShippingMethods(){
		global $woocommerce;
		$woocommerce->shipping->load_shipping_methods();
		return $woocommerce->shipping->get_shipping_methods() ;
	}
	
	/*public function create_order($data){
		global $woocommerce;
		global $wp;
		$woocommerce->api->includes();
		$this->wooserver = new WC_API_Server( $wp->query_vars['wc-api-route'] );
	}*/

     public function cart_details($data){
	global $woocommerce;
	$line_items = $data['order']['line_items'];
	foreach($line_items as $line_item){
		$woocommerce->cart->add_to_cart($line_item['product_id']);
	}
	$woocommerce->cart->calculate_shipping();
	return $woocommerce->cart;
  }
 }
?>