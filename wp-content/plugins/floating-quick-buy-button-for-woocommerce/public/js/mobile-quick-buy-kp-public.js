(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 jQuery(document).ready(function(){
	 	var data_btn_intention = jQuery('.mobile-quick-buy-kp-sticky').attr('data-btn-intention');
	 	var data_checkout_url = jQuery('.mobile-quick-buy-kp-sticky').attr('data-checkout-url');
		var data_show_both = jQuery('.mobile-quick-buy-kp-sticky').attr('data-show-both');

		if ( data_show_both == 'select_both_7' ) {
			jQuery("body").delegate(".mobile-quick-buy-kp-sticky a.btn-add-to-cart", "click", function(e){
				e.preventDefault();
				var add_to_cart_url = jQuery(this).attr("href");
				jQuery.get( add_to_cart_url, function( data ){
					window.location.href = window.location.href;
				});
			});

			jQuery("body").delegate(".mobile-quick-buy-kp-sticky a.btn-quick-buy", "click", function(e){
				e.preventDefault();
				var add_to_cart_url = jQuery(this).attr("href");
				jQuery.get( add_to_cart_url, function( data ){
					window.location.href = data_checkout_url;
				});
			});
		} else {
			if ( data_btn_intention == 'quick-buy' ) {
				jQuery("body").delegate(".mobile-quick-buy-kp-sticky a", "click", function(e){
					e.preventDefault();
					var add_to_cart_url = jQuery(this).attr("href");
					jQuery.get( add_to_cart_url, function( data ){
						window.location.href = data_checkout_url;
					});
				});
			}else {
				jQuery("body").delegate(".mobile-quick-buy-kp-sticky a", "click", function(e){
					e.preventDefault();
					var add_to_cart_url = jQuery(this).attr("href");
					jQuery.get( add_to_cart_url, function( data ){
						window.location.href = window.location.href;
					});
				});
			}
		}


		// For 'On Scroll' functionality START
		var data_button_visibility = jQuery('.mobile-quick-buy-kp-sticky').attr('data-button-visibility');
		var data_top_offset = jQuery('.mobile-quick-buy-kp-sticky').attr('data-top-offset');
		if ( data_button_visibility == 'on-scroll' ) {

			jQuery(window).scroll(function (event) {
			    var scroll = jQuery(window).scrollTop();
			    // console.log(scroll);
			    if( scroll >= data_top_offset ){
			    	jQuery(".mobile-quick-buy-kp-sticky").addClass("show-time");
			    }else{
			    	jQuery(".mobile-quick-buy-kp-sticky").removeClass("show-time");
			    }
			});
		}
		// For 'On Scroll' functionality END

	 });

})( jQuery );
