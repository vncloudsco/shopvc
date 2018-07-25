(function($){
"use strict";

	$(document).ajaxComplete( function() {
		wcwar_register();
	});

	function wcwar_register() {

		$('.war_warranty:not(.war_registered)').each( function() {

			$(this).addClass('war_registered');

			if ( $(this).find('select[name="wcwar_pa_warranty"]').length > 0 ) {

				if ( $(this).find('select[name="wcwar_pa_warranty"] option:first-child').val() == '' ) {
					$(this).find('select[name="wcwar_pa_warranty"] option:first-child').prop('selected', true);
				}

				if ( $(this).is('.war_warranty.war_paid') ) {
					$(this).find('.war_option[data-selected="'+$(this).find('select[name="wcwar_pa_warranty"]').val()+'"]').fadeIn(200);
				}

			}

		});

	}

	wcwar_register();

	$(document).on('change', 'select[name="wcwar_pa_warranty"]', function() {

		var war = $(this).closest('.war_warranty.war_paid');

		if ( war.length>0 ) {
			war.find('.war_option').hide();
			war.find('.war_option[data-selected="'+$('select[name="wcwar_pa_warranty"]').val()+'"]').fadeIn(200);
		}

	});

	$(document).on('change', '.variations_form input[name=variation_id]', function() {

		if ( $('.variations_form input[name=variation_id]').val() == '' ) {
			return;
		}

		$('.variations_form .war_warranty').hide();
		$('.variations_form .war_warranty[data-id='+$('.variations_form input[name=variation_id]').val()+']').fadeIn(200);

	});

})(jQuery);