(function($){
"use strict";

	$(document).on('change', 'input[name=item_id]', function() {

		$(this).closest('.wcwar_item ').find('input[name=wcwar_qty]').prop('disabled',true).attr('disabled','disabled').hide();
		$(this).closest('.wcwar_item_wrap').find('input[name=wcwar_qty]').removeAttr('disabled').show();

	});

	$(document).on('change', 'input[name=wcwar_return]', function() {
		if(this.checked) {
			$('.wcwar_warranty form p.wcwar_form_field input[type="text"], .wcwar_warranty form p.wcwar_form_field input[type="checkbox"], .wcwar_warranty form p.wcwar_form_field input[type="radio"], .wcwar_warranty form p.wcwar_form_field textarea, .wcwar_warranty form p.wcwar_form_field select').prop('disabled', true);
			$('.wcwar_warranty form p.wcwar_form_field').fadeTo(200,0.5);
			$('.wcwar_warranty form p.wcwar_return_note').slideDown();
		}
		else {
			$('.wcwar_warranty form p.wcwar_form_field input[type="text"], .wcwar_warranty form p.wcwar_form_field input[type="checkbox"], .wcwar_warranty form p.wcwar_form_field input[type="radio"], .wcwar_warranty form p.wcwar_form_field textarea, .wcwar_warranty form p.wcwar_form_field select').prop('disabled', false);
			$('.wcwar_warranty form p.wcwar_form_field').fadeTo(200,1).removeAttr('style');
			$('.wcwar_warranty form p.wcwar_return_note').slideUp();
		}
	});

})(jQuery);