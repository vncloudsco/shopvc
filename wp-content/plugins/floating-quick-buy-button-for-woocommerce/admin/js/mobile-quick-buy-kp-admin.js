(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	$( document ).ready( function () {
		 $('#background_color_1').iris({
			 width: 200,
			 hide: true,
			 palettes: true,
			 change: function(event, ui) {
			 // event = standard jQuery event, produced by whichever control was changed.
			 // ui = standard jQuery UI object, with a color member containing a Color.js object
			// change the headline color
			 $("#background_color_1").css( 'background', ui.color.toString());
			 $(".mobile-quick-buy-kp-sticky").css( 'background', ui.color.toString() );
			 }

			 });

			 $(this).click(function() {
			 $(this).iris('toggle'); //click came from somewhere else
		 });

		 $('#text_color_2').iris({
			 width: 200,
			 hide: true,
			 palettes: true,
			 change: function(event, ui) {
			 // event = standard jQuery event, produced by whichever control was changed.
			 // ui = standard jQuery UI object, with a color member containing a Color.js object
			// change the headline color
			 $("#text_color_2").css( 'background', ui.color.toString());
			 $(".mobile-quick-buy-kp-sticky a").css( 'color', ui.color.toString() );
			 }

			 });

			 $(this).click(function() {
			 $(this).iris('toggle'); //click came from somewhere else
		 });


			//Preview Button function START
			// jQuery('#preview_button').click(function(){
			// if (  ){
	
			var background_color_1 = jQuery('#background_color_1').val();
			var text_color_2 = jQuery('#text_color_2').val();
			var button_text_3 = jQuery('#button_text_3').val();
			var select_button_intention_0 = jQuery('#select_button_intention_0').val();
			var kp_icon;
			var kp_intent_class = select_button_intention_0;

			if ( select_button_intention_0 === 'quick-buy' ) {
				kp_icon = '&#xe800;';
				kp_intent_class = 'quick-buy';

			}else {
				kp_icon = '&#xf217;';
				kp_intent_class = 'add-to-cart';
			}

			jQuery('#preview_box').append('<div class="mobile-quick-buy-kp-sticky '+kp_intent_class+'" style="background: '+background_color_1+';"><a href="javascript:void();" class="mobile-quick-buy" style="color: '+text_color_2+';"><i class="kp-icon">'+kp_icon+'</i>'+button_text_3+'</a></div>');

			jQuery('#button_text_3').on('change keyup', function() {
				jQuery('.mobile-quick-buy-kp-sticky a').html( '<i class="kp-icon">&#xe800</i>'+this.value );
			});

			// }
			// });
			//Preview Button function END


			// Hide and show scroll offset field based on Button Visibility option START
			if( jQuery('#button_visibility_4').val() === 'all-time' ){
				jQuery('.mobile_quick_buy_tooltip').parent().parent().hide();
			}else{
				jQuery('.mobile_quick_buy_tooltip').parent().parent().show();
			}

			jQuery('#button_visibility_4').change(function(){
				console.log(jQuery(this).val());
				if( jQuery(this).val() === 'all-time' ){
					jQuery('.mobile_quick_buy_tooltip').parent().parent().hide();
				}else{
					jQuery('.mobile_quick_buy_tooltip').parent().parent().show();
				}
			});

			// Hide and show scroll offset field based on Button Visibility option END

	});


})( jQuery );
