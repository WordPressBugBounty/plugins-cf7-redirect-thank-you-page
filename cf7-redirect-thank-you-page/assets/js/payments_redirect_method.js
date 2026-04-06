jQuery(document).ready(function($) {

	var cf7rl_formid;
	var cf7rl_id_long;
	var cf7rl_amount_total;
	var cf7rl_email;
	var cf7rl_stripe_return;
	var cf7rl_payment_id;

	// for redirect method 1
	document.addEventListener('wpcf7mailsent', function( event ) {
		
		if (ajax_object_cf7rl.method == 1) {
			
			var cf7rl_id_long =			event.detail.id;
			var cf7rl_id = 				event.detail.contactFormId;

			cf7rl_redirect(cf7rl_id, cf7rl_id_long);
		}
		
	}, false );
	
	
	
	
	// for redirect method 2
	if (ajax_object_cf7rl.method == 2) {
		// Check on page load for forms already in sent state (non-AJAX submission)
		if (jQuery('.wpcf7-form[data-status="sent"]')[0]) {
			jQuery('.wpcf7-form[data-status="sent"]').each(function() {
				var cf7rl_id_long = jQuery(this).closest('.wpcf7').attr("id");
				var cf7rl_id = cf7rl_id_long.split('f').pop().split('-').shift();
				
				cf7rl_redirect(cf7rl_id, cf7rl_id_long);
			});
		}
		
		// Also listen for wpcf7mailsent event in case AJAX is still active
		document.addEventListener('wpcf7mailsent', function(event) {
			var cf7rl_id_long = event.detail.id;
			var cf7rl_id = event.detail.contactFormId;
			
			cf7rl_redirect(cf7rl_id, cf7rl_id_long);
		}, false);
	}

	
	// used for redirect method 1 and 2
	function cf7rl_redirect(cf7rl_id, cf7rl_id_long) {
		
		var cf7rl_forms = ajax_object_cf7rl.forms;
		
		var cf7rl_result_paypal = cf7rl_forms.indexOf(cf7rl_id+'|paypal');
		var cf7rl_result_stripe = cf7rl_forms.indexOf(cf7rl_id+'|stripe');
		
		
		var cf7rl_gateway;
		
		var cf7rl_data = {
			'action':	'cf7rl_get_form_post',
			'nonce':	ajax_object_cf7rl.nonce,
		};
		
		var requestUrl = (ajax_object_cf7rl.request_method == '2') 
			? ajax_object_cf7rl.rest_url + 'cf7rl_get_form_post'
			: ajax_object_cf7rl.ajax_url;
		
		jQuery.ajax({
			type: "GET",
			data: cf7rl_data,
			dataType: "json",
			async: false,
			url: requestUrl,
			xhrFields: {
				withCredentials: true
			},
			success: function (response) {
				cf7rl_gateway = 			response.gateway;
				cf7rl_amount_total = 		response.amount_total;
				cf7rl_email = 				response.email;
				cf7rl_stripe_return = 		response.stripe_return;
				cf7rl_payment_id = 			response.payment_id;
			}
		});
		
		var cf7rl_path_paypal = ajax_object_cf7rl.path_paypal+cf7rl_id+'&cf7rl_p='+cf7rl_payment_id;
		var cf7rl_path_stripe = ajax_object_cf7rl.path_stripe+cf7rl_id+'&cf7rl_fid='+cf7rl_id_long+'&cf7rl_p='+cf7rl_payment_id+'&cf7rl_return='+window.location.href;
		
		
		// gateway chooser
		if (cf7rl_gateway != null) {
			if (cf7rl_result_paypal > -1 && cf7rl_gateway == 'paypal') {
				window.location.href = cf7rl_path_paypal;
			}
			
			if (cf7rl_result_stripe > -1 && cf7rl_gateway == 'stripe') {
				window.location.href = cf7rl_path_stripe;
			}
		} else {
			if (cf7rl_result_paypal > -1) {
				window.location.href = cf7rl_path_paypal;
			}
			
			if (cf7rl_result_stripe > -1) {
				window.location.href = cf7rl_path_stripe;
			}
		}
	}


	// show stripe success message
	let searchParams = new URLSearchParams(window.location.search)
	
	if (searchParams.has('cf7rl_stripe_success')) {
		
		let fid = searchParams.get('cf7rl_fid');
		
		var cf7rl_data = {
			'action':	'cf7rl_get_form_stripe_success',
			'nonce':	ajax_object_cf7rl.nonce,
		};
		
		var requestUrl = (ajax_object_cf7rl.request_method == '2') 
			? ajax_object_cf7rl.rest_url + 'cf7rl_get_form_stripe_success'
			: ajax_object_cf7rl.ajax_url;
		
		jQuery.ajax({
			type: (ajax_object_cf7rl.request_method == '2') ? "GET" : "POST",
			data: cf7rl_data,
			dataType: "json",
			async: false,
			url: requestUrl,
			xhrFields: {
				withCredentials: true
			},
			success: function (response) {
				var html_response = response.html;
				
				jQuery('[id^="wpcf7-f' + fid + '-"]').html(html_response);
			}
		});
		
	}



});
