(function($){
"use strict";

	$(document).on('click', '#wcwar_change_status .wcwar_close', function() {
		$('#wcwar_change_status').remove();
		return false;
	});

	$(document).on('click', '.war_delete', function() {

		if ( confirm(wcwar.localization.delete) === false ) {
			return false;
		}

		var curr_data = {
			action: 'war_ajax_et_delete',
			curr_name: $('select.war_email_selected option:selected').val()
		};

		if ( curr_data.curr_name == '' ) {
			alert(wcwar.localization.notselected);
			return false;
		}

		$.post(wcwar.ajax, curr_data, function(response) {
			if (response) {
				$('select.war_email_selected option[value="'+curr_data.curr_name+'"]').remove();
				alert(wcwar.localization.deleted);
			} else {
				alert(wcwar.localization.error);
			}
		});

		return false;
	});

	$(document).on('click', '.war_load', function() {
		if ( confirm(wcwar.localization.load) === false ) {
			return false;
		}

		var curr_data = {
			action: 'war_ajax_et_load',
			curr_name: $('select.war_email_selected option:selected').val()
		};

		if ( curr_data.curr_name == '' ) {
			alert(wcwar.localization.notselected);
			return false;
		}

		$.post(wcwar.ajax, curr_data, function(response) {
			if (response) {
				var curr_preset = $.parseJSON(response);
				$('textarea.war_message').val(curr_preset);
				alert(wcwar.localization.loaded);
			} else {
				alert(wcwar.localization.error);
			}
		});

		return false;
	});

	$(document).on('click', '.war_save', function() {
		
		var curr_email = $('.war_message').val();
		var curr_name = prompt(wcwar.localization.templatename);

		if ( curr_name == '' || curr_email == '' ) {
			alert(wcwar.localization.missing);
			return false;
		}
		if ( curr_name === null ) {
			return false;
		}

		var curr_data = {
			action: 'war_ajax_et_save',
			curr_name: curr_name,
			curr_email: curr_email
		};

		$.post(wcwar.ajax, curr_data, function(response) {
			if (response) {
				$('select.war_email_selected').append('<option value="'+curr_data.curr_name+'">'+curr_data.curr_name+'</option>');
				alert(wcwar.localization.saved);
			} else { 
				alert(wcwar.localization.error);
			}
		});
	});

	$(document).on('click', '.war_send', function() {
		
		if ( confirm(wcwar.localization.sendemail) === false ) {
			return false;
		}

		var curr_id = $(this).attr('data-ids').split('|');
		var curr_email = $('.war_message').val();

		var curr_data = {
			action: 'war_ajax_email_send',
			order_id: curr_id[0],
			request_id: curr_id[1],
			email: curr_email
		};

		$.post(wcwar.ajax, curr_data, function(response) {
			if (response) {
				alert(wcwar.localization.emailsent);
			}
			else {
				alert(wcwar.localization.error);
			}
		});
		return false;

	});


	$(document).on('click', '.wcwar_create', function() {

		var curr_id = $(this).attr('data-ids').split('|');

		var curr_data = {
			action: 'war_ajax_create',
			order_id: curr_id[0],
			item_id: curr_id[1]
		};

		$.post(wcwar.ajax, curr_data, function(response) {
			if (response) {
				location.reload();
			}
			else {
				alert(wcwar.localization.error);
			}
		});
		return false;

	});

	$(document).on('click', '.warranty_change_status', function() {

		if ( $('#wcwar_change_status').length > 0 ) {
			$('#wcwar_change_status').remove();
		}

		var curr = $(this);
		var curr_id = $(this).attr('data-id');

		var curr_data = {
			action: 'war_ajax_status',
			request_id: curr_id
		};

		if ( curr.closest('.wcwar_warranty_status').hasClass('single-order') || $('#order_line_items > tr.item').length == 1 ) {
			$.post(wcwar.ajax, curr_data, function(response) {
				if (response) {
					var curr_after = curr.after(response);
					var curr_ps = curr.next().find('li.wcwar_selected').attr('data-key');
					var curr_pst = curr.next().find('li.wcwar_selected').text();

					curr.next().find('li:not(.wcwar_close)').on('click', function() {
						var curr_selected = $(this);

						var curr_s = $(this).attr('data-key');
						var curr_st = $(this).text();
						
						var curr_clicked_data = {
							action: 'war_ajax_status_change',
							request_id: curr_id,
							request_status: curr_s
						};
						$.post(wcwar.ajax, curr_clicked_data, function(status) {
							if (status) {
								var curr_clsst = $('.wcwar_warranty_status').find('.wcwar_change');

								curr_clsst.each( function() {
									var r = $(this).attr('class').replace(curr_ps, curr_s);
									$(this).attr('class', r);

									var t = $(this).text().replace(curr_pst, curr_st);
									$(this).text(t);
								});
								curr.next().remove();

							}
							else {
								alert(wcwar.localization.error);
							}
						});
					});
				}
				else {
					alert(wcwar.localization.error);
				}
			});
		}
		else {
			$.post(wcwar.ajax, curr_data, function(response) {
				if (response) {
					var curr_after = curr.after(response);
					var curr_ps = curr.next().find('li.wcwar_selected').attr('data-key');
					var curr_pst = curr.next().find('li.wcwar_selected').text();

					curr.next().find('li:not(.wcwar_close)').on('click', function() {
						var curr_selected = $(this);

						var curr_s = $(this).attr('data-key');
						var curr_st = $(this).text();
						
						var curr_clicked_data = {
							action: 'war_ajax_status_change',
							request_id: curr_id,
							request_status: curr_s
						};
						$.post(wcwar.ajax, curr_clicked_data, function(status) {
							if (status) {
								var curr_clsst = curr.closest('.wcwar_warranty_status').find('.wcwar_change');

								var r = curr_clsst.attr('class').replace(curr_ps.replace('warranty_', ''), curr_s.replace('warranty_', ''));
								curr_clsst.attr('class', r);

								var t = curr_clsst.text().replace(curr_pst, curr_st);
								curr_clsst.text(t);

								curr.next().remove();

							}
							else {
								alert(wcwar.localization.error);
							}
						});
					});
				}
				else {
					alert(wcwar.localization.error);
				}
			});
		}
		

		return false;
	});


})(jQuery);