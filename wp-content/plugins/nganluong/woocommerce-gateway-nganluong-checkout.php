<?php

/**
 * Plugin Name: Ngan Luong payment gateway for WooCommerce
 * Plugin URI: https://www.nganluong.vn/
 * Description: Plugin tích hợp NgânLượng.vn được build trên WooCommerce 2.6.4
 * Version: 2.0
 * Author: DUCLM - 0948389111
 * Author URI: http://www.webckk.com/
 */
ini_set('display_errors', true);
add_action('plugins_loaded', 'woocommerce_payment_nganluong_init', 0);
add_action('parse_request', array(WC_Gateway_NganLuong, 'nganluong_return_handler'));

function woocommerce_payment_nganluong_init() {
    if (!class_exists('WC_Payment_Gateway'))
        return;

    class WC_Gateway_NganLuong extends WC_Payment_Gateway {

        // URL checkout của nganluong.vn - Checkout URL for Ngan Luong
        private $nganluong_url;
        // Mã merchant site code
        private $merchant_site_code;
        // Mật khẩu bảo mật - Secure password
        private $secure_pass;
        // Debug parameters
        private $debug_params;
        private $debug_md5;

        function __construct() {
            $this->icon = $this->settings['icon']; // Icon URL
            $this->id = 'nganluong';
            $this->method_title = 'Ngân Lượng';
            $this->has_fields = false;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->nganluong_url = $this->settings['nganluong_url'];
            $this->merchant_site_code = $this->settings['merchant_site_code'];
            $this->merchant_id = $this->settings['merchant_id'];
            $this->secure_pass = $this->settings['secure_pass'];
            $this->redirect_page_id = $this->settings['redirect_page_id'];

            $this->debug = $this->settings['debug'];
            $this->order_button_text = __('Proceed to Ngân Lượng', 'woocommerce');

            $this->msg['message'] = "";
            $this->msg['class'] = "";
            // Add the page after checkout to redirect to Ngan Luong
            add_action('woocommerce_receipt_NganLuong', array($this, 'receipt_page'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            // add_action('woocommerce_thankyou_NganLuongVN', array($this, 'thankyou_page'));
        }

        /**
         * Logging method.
         * @param string $message
         */
        public static function log($message) {
            $log = new WC_Logger();
            $log->add('nganluong', $message);
        }

        public function init_form_fields() {
            // Admin fields
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Activate', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Activate the payment gateway for Ngan Luong', 'woocommerce'),
                    'default' => 'yes'),
                'title' => array(
                    'title' => __('Name', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Tên phương thức thanh toán ( khi khách hàng chọn phương thức thanh toán )', 'woocommerce'),
                    'default' => __('NganLuongVN', 'woocommerce')),
                'icon' => array(
                    'title' => __('Icon', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Icon phương thức thanh toán', 'woocommerce'),
                    'default' => __('https://www.nganluong.vn/css/checkout/version20/images/logoNL.png', 'woocommerce')),
                'description' => array(
                    'title' => __('Mô tả', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Mô tả phương thức thanh toán.', 'woocommerce'),
                    'default' => __('Click place order and you will be directed to the Ngan Luong website in order to make payment', 'woocommerce')),
                'merchant_id' => array(
                    'title' => __('NganLuong.vn email address', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Đây là tài khoản NganLuong.vn (Email) để nhận tiền')),
                'redirect_page_id' => array(
                    'title' => __('Return URL'),
                    'type' => 'select',
                    'options' => $this->get_pages('Hãy chọn...'),
                    'description' => __('Hãy chọn trang/url để chuyển đến sau khi khách hàng đã thanh toán tại NganLuong.vn thành công', 'woocommerce')
                ),
                'status_order' => array(
                    'title' => __('Trạng thái Order'),
                    'type' => 'select',
                    'options' => wc_get_order_statuses(),
                    'description' => __('Chọn trạng thái orders cập nhật', 'woocommerce')
                ),
                'nlcurrency' => array(
                    'title' => __('Currency', 'woocommerce'),
                    'type' => 'text',
                    'default' => 'vnd',
                    'description' => __('"vnd" or "usd"', 'woocommerce')
                ),
                'nganluong_url' => array(
                    'title' => __('Ngan Luong URL', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('"https://www.nganluong.vn/checkout.php"', 'woocommerce')
                ),
                'merchant_site_code' => array(
                    'title' => __('Merchant Site Code', 'woocommerce'),
                    'type' => 'text'
                ),
                'secure_pass' => array(
                    'title' => __('Secure Password', 'woocommerce'),
                    'type' => 'password'
                ),
            );
        }

        /**
         *  There are no payment fields for NganLuongVN, but we want to show the description if set.
         * */
        function payment_fields() {
            if ($this->description)
                echo wpautop(wptexturize(__($this->description, 'woocommerce')));
        }

        /**
         * Process the payment and return the result.
         * @param  int $order_id
         * @return array
         */
        public function process_payment($order_id) {

            $order = wc_get_order($order_id);
            $checkouturl = $this->generate_NganLuongVN_url($order_id);
            $this->log($checkouturl);
            //  echo $checkouturl;
            // die();
            return array(
                'result' => 'success',
                'redirect' => $checkouturl
            );
        }

        function generate_NganLuongVN_url($order_id) {
            // This is from the class provided by Ngan Luong. Not advisable to mess.
            global $woocommerce;
            $order = new WC_Order($order_id);

            $order_items = $order->get_items();

            $return_url = get_site_url() . '/nganluong_return?order_id=' . $order_id;
            //$return_url = 'nganluong.vn';
            $receiver = $this->merchant_id;
            $currency = $this->settings['nlcurrency'];
            $transaction_info = ''; // urlencode("Order#".$order_id." | ".$_SERVER['SERVER_NAME']);

            $order_description = $order_id;

            $order_quantity = $order->get_item_count();
            //$discount = $order->get_cart_discount();
            $discount = 0;
            //$tax = $order->get_cart_tax();
            $tax = 0;
            $fee_shipping = $order->get_total_shipping();
            $product_names = '';
            foreach ($order_items as $order_item) {
                $product_names[] = $order_item['name'];
            }
            $order_description = implode(', ', $product_names); // this goes into transaction info, which shows up on Ngan Luong as the description of goods

            $price = $order->get_total() - ($tax + $fee_shipping);

            $checkouturl = $this->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_id, $price, $currency, $quantity = 1, $tax, $discount, $fee_cal = 0, $fee_shipping, $order_description);


            return $checkouturl;
        }

        function showMessage($content) {
            return '<div class="box ' . $this->msg['class'] . '-box">' . $this->msg['message'] . '</div>' . $content;
        }

        // get all pages
        function get_pages($title = false, $indent = true) {
            $wp_pages = get_pages('sort_column=menu_order');
            $page_list = array();
            if ($title)
                $page_list[] = $title;
            foreach ($wp_pages as $page) {
                $prefix = '';
                // show indented child pages?
                if ($indent) {
                    $has_parent = $page->post_parent;
                    while ($has_parent) {
                        $prefix .= ' - ';
                        $next_page = get_page($has_parent);
                        $has_parent = $next_page->post_parent;
                    }
                }
                // add to page list array array
                $page_list[$page->ID] = $prefix . $page->post_title;
            }
            return $page_list;
        }

        public function buildCheckoutUrl($return_url, $receiver, $transaction_info, $order_code, $price) {
            // This is from the class provided by Ngan Luong. Not advisable to mess.
            // This one is for simple checkout
            // Mảng các tham số chuyển tới nganluong.vn
            $arr_param = array(
                'merchant_site_code' => strval($this->merchant_site_code),
                'return_url' => strtolower(urlencode($return_url)),
                'receiver' => strval($receiver),
                'transaction_info' => strval($transaction_info),
                'order_code' => strval($order_code),
                'price' => strval($price)
            );
            $secure_code = '';
            $secure_code = implode(' ', $arr_param) . ' ' . $this->secure_pass;
            $this->debug_params = $secure_code;
            $arr_param['secure_code'] = md5($secure_code);
            $this->debug_md5 = $arr_param['secure_code'];

            /* Bước 2. Kiểm tra  biến $redirect_url xem có '?' không, nếu không có thì bổ sung vào */
            $redirect_url = $this->nganluong_url;
            if (strpos($redirect_url, '?') === false) {
                $redirect_url .= '?';
            } else if (substr($redirect_url, strlen($redirect_url) - 1, 1) != '?' && strpos($redirect_url, '&') === false) {
                // Nếu biến $redirect_url có '?' nhưng không kết thúc bằng '?' và có chứa dấu '&' thì bổ sung vào cuối
                $redirect_url .= '&';
            }

            /* Bước 3. tạo url */
            $url = '';
            foreach ($arr_param as $key => $value) {
                if ($url == '')
                    $url .= $key . '=' . $value;
                else
                    $url .= '&' . $key . '=' . $value;
            }

            return $redirect_url . $url;
        }

        public function buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency = 'vnd', $quantity = 1, $tax = 0, $discount = 0, $fee_cal = 0, $fee_shipping = 0, $order_description = '', $buyer_info = '', $affiliate_code = '') {
            // This is from the class provided by Ngan Luong. Not advisable to mess.
            //  This one is for advanced checkout, including taxes and discounts
            if ($affiliate_code == "")
                $affiliate_code = $this->affiliate_code;
            $arr_param = array(
                'merchant_site_code' => strval($this->merchant_site_code),
                'return_url' => strval(strtolower($return_url)),
                'receiver' => strval($receiver),
                'transaction_info' => strval($transaction_info),
                'order_code' => strval($order_code),
                'price' => strval($price),
                'currency' => strval($currency),
                'quantity' => strval($quantity),
                'tax' => strval($tax),
                'discount' => strval($discount),
                'fee_cal' => strval($fee_cal),
                'fee_shipping' => strval($fee_shipping),
                'order_description' => strval($order_description),
                'buyer_info' => strval($buyer_info),
                'affiliate_code' => strval($affiliate_code)
            );
            $secure_code = '';
            $secure_code = implode(' ', $arr_param) . ' ' . $this->secure_pass;
            $arr_param['secure_code'] = md5($secure_code);
            /* */
            $redirect_url = $this->nganluong_url;
            if (strpos($redirect_url, '?') === false) {
                $redirect_url .= '?';
            } else if (substr($redirect_url, strlen($redirect_url) - 1, 1) != '?' && strpos($redirect_url, '&') === false) {
                $redirect_url .= '&';
            }

            /* */
            $url = '';
            foreach ($arr_param as $key => $value) {
                $value = urlencode($value);
                if ($url == '') {
                    $url .= $key . '=' . $value;
                } else {
                    $url .= '&' . $key . '=' . $value;
                }
            }

            return $redirect_url . $url;
        }

        /* Hàm thực hiện xác minh tính đúng đắn của các tham số trả về từ nganluong.vn */

        public function nganluong_return_handler($order_id) {

            global $woocommerce;

            // This probably could be written better
            if (isset($_REQUEST['payment_id']) && !empty($_REQUEST['payment_id'])) {
                self::log($_SERVER['REMOTE_ADDR'] . json_encode(@$_REQUEST));
                $settings = get_option('woocommerce_nganluong_settings', null);

                $order_id = $_REQUEST['order_id'];
                $order = new WC_Order($order_id);
                $transaction_info = ''; // urlencode("Order#".$order_id." | ".$_SERVER['SERVER_NAME']);
                $order_code = $_REQUEST['order_code'];
                $price = $_REQUEST['price'];
                $secure_code = $_REQUEST['secure_code'];
                $payment_type = $_REQUEST['payment_type'];
                // This is from the class provided by Ngan Luong. Not advisable to mess.
                // Checks the returned URL from Ngan Luong to see if it matches
                // Tạo mã xác thực từ chủ web
                $str = '';
                $str .= ' ' . strval($transaction_info);
                $str .= ' ' . strval($order_code);
                $str .= ' ' . strval($price);
                $str .= ' ' . strval($_REQUEST['payment_id']);
                $str .= ' ' . strval($payment_type);
                $str .= ' ' . strval($_REQUEST['error_text']);
                $str .= ' ' . strval($settings['merchant_site_code']);
                $str .= ' ' . strval($settings['secure_pass']);

                // Mã hóa các tham số
                $verify_secure_code = '';
                $verify_secure_code = md5($str);

                // Xác thực mã của chủ web với mã trả về từ nganluong.vn
                if ($verify_secure_code === $secure_code) {
                    $new_order_status = $settings['status_order'];
                    $old_status = 'wc-' . $order->get_status();

                    if ($new_order_status !== $old_status) {
                        $note = 'Thanh toán trực tuyến qua Ngân Lượng.';
                        if ($payment_type == 2) {
                            $note .= ' Với hình thức thanh toán tạm giữ';
                        } else if ($payment_type == 1) {
                            $note .= ' Với hình thức thanh toán ngay';
                        }
                        $note .= ' .Mã thanh toán: ' . $_REQUEST['payment_id'];
                        $order->update_status($new_order_status);
                        $order->add_order_note(sprintf(__('Cập nhật trạng thái từ %1$s thành %2$s.' . $note, 'woocommerce'), wc_get_order_status_name($old_status), wc_get_order_status_name($new_order_status)), 0, false);
                        self::log('Cập nhật đơn hàng ID: ' . $order_id . ' trạng thái ' . $new_status);
                    }

                    // Remove cart
                    $woocommerce->cart->empty_cart();
                    // Empty awaiting payment session
                    unset($_SESSION['order_awaiting_payment']);
                    wp_redirect(get_permalink($settings['redirect_page_id']));
                    exit;
                } else {
                    self::log('Thông tin giao dịch không chính xác');
                  
                    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><h3>Thông tin giao dịch không chính xác</h3>';
                    die();
                }
            }
        }

    }

    function woocommerce_add_NganLuong_gateway($methods) {
        $methods[] = 'WC_Gateway_NganLuong';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_NganLuong_gateway');
}
