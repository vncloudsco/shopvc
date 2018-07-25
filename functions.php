<?php
// Add custom Theme Functions here

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

function add_content_after_addtocart() {
$current_product_id = get_the_ID();
$product = wc_get_product( $current_product_id );
$checkout_url = WC()->cart->get_checkout_url();
if( $product->is_type( 'simple' ) ){
echo '<a href="'.$checkout_url.'?add-to-cart='.$current_product_id.'" class="single_add_to_cart_button button alt">Mua Nhanh</a>';
}
}
add_action( 'woocommerce_after_add_to_cart_button', 'add_content_after_addtocart' );
add_action('woocommerce_checkout_process', 'devvn_validate_phone_field_process' );
function devvn_validate_phone_field_process() {
    $billing_phone = filter_input(INPUT_POST, 'billing_phone');
    if ( ! (preg_match('/^0([0-9]{9,10})+$/D', $billing_phone )) ){
        wc_add_notice( "Xin nhập đúng <strong>số điện thoại</strong> của bạn"  ,'error' );
    }
}

add_filter( 'woocommerce_order_button_text', 'woo_custom_order_button_text' ); function woo_custom_order_button_text() { return __( 'Thanh Toán', 'woocommerce' ); }
 
function woo_redirect_to_checkout() {
 global $woocommerce;
 $checkout_url = $woocommerce->cart->get_checkout_url();
 return $checkout_url;
}

// hide coupon field on cart page
function hide_coupon_field_on_cart( $enabled ) {
	if ( is_cart() ) {
		$enabled = false;
	}
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'hide_coupon_field_on_cart' );
// hide coupon field on checkout page
function hide_coupon_field_on_checkout( $enabled ) {
	if ( is_checkout() ) {
		$enabled = false;
	}
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'hide_coupon_field_on_checkout' );



// chu noi bat khi viet bai
// cach su dung [yellowbox]Your featured content here[/yellowbox]
function make_yellowbox($atts, $content = null) {
return '<p style="background: none repeat scroll 0 0 #ff9; clear: both; margin-bottom: 18px; overflow: hidden; border: 1px solid #e5e597; padding: 13px;">' . do_shortcode($content) . '</p>';
}
add_shortcode('yellowbox', 'make_yellowbox');
// ket thuc code chu noi bat


// remove text cuoi trang wp-admin
function remove_footer_admin () {

  echo "vouu shop";

} 

add_filter('admin_footer_text', 'remove_footer_admin');
// remove text cuoi trang wp-admin

// Remove help and screen options from dashboard
add_filter( 'contextual_help', 'wpse_25034_remove_dashboard_help_tab', 999, 3 );

add_filter( 'screen_options_show_screen', 'wpse_25034_remove_help_tab' );

function wpse_25034_remove_dashboard_help_tab( $old_help, $screen_id, $screen )

  {

     if( 'dashboard' != $screen->base )

     return $old_help;

     $screen->remove_help_tabs();

     return $old_help;

  }

function wpse_25034_remove_help_tab( $visible )

  {

     global $current_screen;

     if( 'dashboard' == $current_screen->base )

     return false;

     return $visible;

  }


// Remove help and screen options from dashboard

// nang cao quan ly cmt tren shop
function delete_comment_link($id) {

  if (current_user_can('edit_post')) {

    echo '| <a href="'.admin_url("comment.php?action=cdc&c=$id").'">del</a> ';

    echo '| <a href="'.admin_url("comment.php?action=cdc&dt=spam&c=$id").'">spam</a>';

  }

}

// nang cao quan ly cmt tren shop

// xóa admin bar
show_admin_bar(false);
// xóa admin bar

// check lai noi dung truoc khi xuat ban
add_action( 'admin_print_footer_scripts', 'sr_publish_molly_guard' );

function sr_publish_molly_guard() {

echo "

<script>

jQuery(document).ready(function($){

$('#publishing-action input[name=\"publish\"]').click(function() {

if(confirm('Are you sure you want to publish this?')) {

return true;

} else {

$('#publishing-action .spinner').hide();

$('#publishing-action img').hide();

$(this).removeClass('button-primary-disabled');

return false;

}

});

});

</script>

";

}

// check lai noi dung truoc khi xuat ban

// chuyen huong dieu khoan su dung
function __my_registration_redirect(){

    return home_url( '/chinh-sach-dieu-khoan' );

}

add_filter( 'registration_redirect', '__my_registration_redirect' );

// chuyen huong dieu khoan su dung

// log erors dis

function no_wordpress_errors(){

  return 'Đăng Nhập Không Thành Công';

}
add_filter( 'login_errors', 'no_wordpress_errors' );
// log erors dis
remove_action('wp_head','wp_generator');
function check_referrer() {

    if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == “”) {

        wp_die( __('Please enable referrers in your browser!') );

    }

}

add_action('check_comment_flood', 'check_referrer');

// Disable self pingbacks in WordPress

function disable_self_trackback( &$links ) {

  foreach ( $links as $l => $link )

        if ( 0 === strpos( $link, get_option( 'home' ) ) )

            unset($links[$l]);

}

add_action( 'pre_ping', 'disable_self_trackback' );

// This will occur when the comment is posted

function plc_comment_post( $incoming_comment ) {

 

// convert everything in a comment to display literally

$incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);

 

// the one exception is single quotes, which cannot be #039; because WordPress marks it as spam

$incoming_comment['comment_content'] = str_replace( "'", '&apos;', $incoming_comment['comment_content'] );

return( $incoming_comment );

}
// dang nhap bang email
//remove wordpress authentication
remove_filter('authenticate', 'wp_authenticate_username_password', 20);
add_filter('authenticate', function($user, $email, $password){
 
    //Check for empty fields
        if(empty($email) || empty ($password)){        
            //create new error object and add errors to it.
            $error = new WP_Error();
 
            if(empty($email)){ //No email
                $error->add('empty_username', __('<strong>ERROR</strong>: Email field is empty.'));
            }
            else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //Invalid Email
                $error->add('invalid_username', __('<strong>ERROR</strong>: Email is invalid.'));
            }
 
            if(empty($password)){ //No password
                $error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
            }
 
            return $error;
        }
 
        //Check if user exists in WordPress database
        $user = get_user_by('email', $email);
 
        //bad email
        if(!$user){
            $error = new WP_Error();
            $error->add('invalid', __('<strong>ERROR</strong>: Either the email or password you entered is invalid.'));
            return $error;
        }
        else{ //check password
            if(!wp_check_password($password, $user->user_pass, $user->ID)){ //bad password
                $error = new WP_Error();
                $error->add('invalid', __('<strong>ERROR</strong>: Either the email or password you entered is invalid.'));
                return $error;
            }else{
                return $user; //passed
            }
        }
}, 20, 3);

