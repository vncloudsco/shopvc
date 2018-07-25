(function($){
"use strict";

	$('#wcwar_form').closest('table').before('<div id="wcwar_formbuilder">asd</div>');

	if ( $('#wcwar_form').val() == '' ) {
		var currData = [
			{
			"label": "Reason for requesting warranty",
			"field_type": "radio",
			"required": true,
			"field_options": {
					"options": [{
							"label": "Item was damaged",
							"checked": false
						}, {
							"label": "Item was broken",
							"checked": false
						}, {
							"label": "Nothing wrong, just returning",
							"checked": false
					}],
				"include_other_option": true
				},
				"cid": "c10"
			}
		];
	}
	else {
		var currData = $.parseJSON($('#wcwar_form').val());
		currData = currData.fields;
	}

	var fb = new Formbuilder({
		selector: '#wcwar_formbuilder',
		bootstrapData: currData
	});

	fb.on('save', function(saved){
		$('#wcwar_form').val(saved);
	});

})(jQuery);