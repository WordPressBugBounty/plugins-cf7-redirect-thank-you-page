function cf7rl_closetabs(ids) {
	var x = ids;
	y = x.split(",");

	for(var i = 0; i < y.length; i++) {
		//console.log(y[i]);
		var tabElement = document.getElementById(y[i]);
		var tabNavElement = document.getElementById("id"+y[i]);
		
		if (tabElement) {
			tabElement.style.display = 'none';
		}
		if (tabNavElement) {
			tabNavElement.classList.remove('nav-tab-active');
		}
	}
}

function cf7rl_newtab(id) {
	var x = id;
	//console.log(x);
	var tabElement = document.getElementById(x);
	var tabNavElement = document.getElementById("id"+x);
	var hiddenField = document.getElementById('hidden_tab_value');
	
	if (tabElement) {
		tabElement.style.display = 'block';
	}
	if (tabNavElement) {
		tabNavElement.classList.add('nav-tab-active');
	}
	if (hiddenField) {
		hiddenField.value = x;
	}
	
	// Save the active tab to localStorage for persistence
	localStorage.setItem('cf7rl_active_tab', x);
}




// tabs page - redirect type dropdown - on change
jQuery(document).ready(function() {
	jQuery('#cf7rl_redirect_type').on('change', function() {
		
		if (this.value == 'url') {
			jQuery('.cf7rl_redirect_option').hide();
			jQuery('.cf7rl_url').show();
		}
		
		if (this.value == 'thank') {
			jQuery('.cf7rl_redirect_option').hide();
			jQuery('.cf7rl_thank').show();
		}
		
		if (this.value == 'logic') {
			jQuery('.cf7rl_redirect_option').hide();
			jQuery('.cf7rl_logic').show();
		}
		
	});
	
	// tabs page - redirect type dropdown - onload
	var cf7rl_redirect_type = jQuery('#cf7rl_redirect_type').val();
	if (cf7rl_redirect_type == 'url') {
		jQuery('.cf7rl_redirect_option').hide();
		jQuery('.cf7rl_url').show();
	}
	if (cf7rl_redirect_type == 'thank') {
		jQuery('.cf7rl_redirect_option').hide();
		jQuery('.cf7rl_thank').show();
	}
	if (cf7rl_redirect_type == 'logic') {
		jQuery('.cf7rl_redirect_option').hide();
		jQuery('.cf7rl_logic').show();
	}
	
});


// Admin JavaScript
jQuery(document).ready(function($) {
    
    // Function to enforce mutual exclusivity between payment and redirect modules
    function cf7rl_enforce_module_exclusivity() {
        var $redirectNoticeContainer = $('#cf7rl-redirect-notice-container');
        var $paymentNoticeContainer = $('#cf7rl-payment-notice-container');
        
        var $redirectCheckbox = $('#cf7rl_enable_redirect');
        var $paypalCheckbox = $('input[name="cf7rl_enable"]');
        var $stripeCheckbox = $('input[name="cf7rl_enable_stripe"]');
        
        // Check if redirect is enabled
        var redirectEnabled = $redirectCheckbox.is(':checked');
        
        // Check if PayPal or Stripe is enabled
        var paypalEnabled = $paypalCheckbox.is(':checked');
        var stripeEnabled = $stripeCheckbox.is(':checked');
        var paymentEnabled = paypalEnabled || stripeEnabled;
        
        // Handle redirect checkbox and notice based on payment status
        if (paymentEnabled) {
            // Payment is enabled - disable redirect and show notice
            $redirectCheckbox.prop('checked', false).prop('disabled', true);
            
            if ($redirectNoticeContainer.length) {
                var paymentType = '';
                if (paypalEnabled && stripeEnabled) {
                    paymentType = 'PayPal and Stripe are';
                } else if (paypalEnabled) {
                    paymentType = 'PayPal is';
                } else {
                    paymentType = 'Stripe is';
                }
                
                var noticeHtml = '<div class="notice notice-warning inline cf7rl-dynamic-notice" style="margin: 10px 0; padding: 10px;">' +
                    '<p><strong>Note:</strong> ' + paymentType + ' enabled on this form. ' +
                    'The redirect functionality has been disabled because it cannot be used with payment processing.</p>' +
                    '</div>';
                
                // Remove any existing dynamic notices
                $redirectNoticeContainer.find('.cf7rl-dynamic-notice').remove();
                
                // Only add if there's no server-side notice
                if ($redirectNoticeContainer.find('.cf7rl-server-notice').length === 0) {
                    $redirectNoticeContainer.append(noticeHtml);
                }
            }
        } else {
            // No payment enabled - re-enable redirect and clear ALL notices
            $redirectCheckbox.prop('disabled', false);
            
            if ($redirectNoticeContainer.length) {
                // Remove both JavaScript-added and server-side notices since the condition no longer applies
                $redirectNoticeContainer.find('.cf7rl-dynamic-notice, .cf7rl-server-notice').remove();
            }
        }
        
        // Handle payment checkboxes and notice based on redirect status
        if (redirectEnabled) {
            // Redirect is enabled - disable payments and show notice
            $paypalCheckbox.prop('checked', false).prop('disabled', true);
            $stripeCheckbox.prop('checked', false).prop('disabled', true);
            
            if ($paymentNoticeContainer.length) {
                var noticeHtml = '<div class="notice notice-warning inline cf7rl-dynamic-notice" style="margin: 10px 0; padding: 10px;">' +
                    '<p><strong>Note:</strong> The Redirect & Thank You Page module is enabled on this form. ' +
                    'Payment processing has been disabled because it cannot be used with the redirect module.</p>' +
                    '</div>';
                
                // Remove any existing dynamic notices
                $paymentNoticeContainer.find('.cf7rl-dynamic-notice').remove();
                
                // Only add if there's no server-side notice
                if ($paymentNoticeContainer.find('.cf7rl-server-notice').length === 0) {
                    $paymentNoticeContainer.append(noticeHtml);
                }
            }
        } else {
            // Redirect not enabled - re-enable payments and clear ALL notices
            $paypalCheckbox.prop('disabled', false);
            $stripeCheckbox.prop('disabled', false);
            
            if ($paymentNoticeContainer.length) {
                // Remove both JavaScript-added and server-side notices since the condition no longer applies
                $paymentNoticeContainer.find('.cf7rl-dynamic-notice, .cf7rl-server-notice').remove();
            }
        }
    }
    
    // Check on page load
    cf7rl_enforce_module_exclusivity();
    
    // Check when checkboxes change
    $(document).on('change', '#cf7rl_enable_redirect, input[name="cf7rl_enable"], input[name="cf7rl_enable_stripe"]', function() {
        cf7rl_enforce_module_exclusivity();
    });
    
    // Determine which tab to show
    // Priority: 1) URL tab param, 2) Already visible tab (from POST), 3) Fresh menu click = tab 1
    var urlParams = new URLSearchParams(window.location.search);
    var urlTab = urlParams.get('tab');
    
    var savedTab;
    if (urlTab) {
        // URL parameter takes priority (e.g., clicking link from Getting Started)
        savedTab = urlTab;
    } else {
        // Check if a tab is already visible (set by PHP from POST hidden_tab_value)
        var $visibleTab = $('div[id]:visible').filter(function() {
            return /^[0-9]+$/.test($(this).attr('id'));
        });
        
        if ($visibleTab.length > 0) {
            // A tab is already visible from PHP - keep it and save to localStorage
            savedTab = $visibleTab.attr('id');
            localStorage.setItem('cf7rl_active_tab', savedTab);
        } else {
            // Fresh menu click (no POST, no URL param) - show Getting Started
            savedTab = '1';
            localStorage.setItem('cf7rl_active_tab', '1');
        }
    }
    
    if (savedTab && document.getElementById(savedTab)) {
        // Get all tab IDs from the page
        var allTabs = $('.nav-tab-wrapper .nav-tab');
        var tabIds = [];
        allTabs.each(function() {
            var tabId = $(this).attr('id');
            if (tabId) {
                tabIds.push(tabId.replace('id', ''));
            }
        });
        
        // Close all tabs and open the correct one
        if (tabIds.length > 0) {
            cf7rl_closetabs(tabIds.join(','));
            cf7rl_newtab(savedTab);
        }
    }
    
    // Remove tab parameter from URL after it's been used
    if (urlTab) {
        urlParams.delete('tab');
        var newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }
    
    // Store the current tab in the hidden field when the save button is clicked
    $('input[type="submit"]').on('click', function() {
        // Get the active tab
        var $activeTab = $('.nav-tab-active');
        var activeTab = $activeTab.length && $activeTab.attr('id') ? $activeTab.attr('id').replace('id', '') : '';
        // Update the hidden field
        $('#hidden_tab_value').val(activeTab);
    });
    
    // Auto-save when PayPal or Stripe mode is changed
    $('input[name="mode"], input[name="mode_stripe"]').on('change', function() {
        // Get the active tab
        var $activeTab = $('.nav-tab-active');
        var activeTab = $activeTab.length && $activeTab.attr('id') ? $activeTab.attr('id').replace('id', '') : '';
        // Update the hidden field
        $('#hidden_tab_value').val(activeTab);
        // Submit the form
        $(this).closest('form').submit();
    });
    
    // Update PPCP onboarding button URL when PayPal mode changes
    $('input[name="mode"]').on('change', function() {
        var sandbox = parseInt($('input[name="mode"]:checked').val()) === 1;
        var $onboardingStartBtn = $('#cf7rl-ppcp-onboarding-start-btn');
        
        // Check if the onboarding button exists before accessing its attributes
        if ($onboardingStartBtn.length && $onboardingStartBtn.attr('href')) {
            var onboardingUrl = $onboardingStartBtn.attr('href').split('?');
            var onboardingParams = new URLSearchParams(onboardingUrl[1] || '');
            
            if (sandbox) {
                onboardingParams.set('sandbox', '1');
            } else {
                onboardingParams.delete('sandbox');
            }
            
            onboardingUrl[1] = onboardingParams.toString();
            $onboardingStartBtn.attr('href', onboardingUrl.join('?'));
        }
    });
});


// Settings Tab Accordion Functionality
jQuery(document).ready(function($) {
	$('.cf7rl-accordion-header').on('click', function() {
		var $header = $(this);
		var $content = $header.next('.cf7rl-accordion-content');
		
		// Toggle active class on header
		$header.toggleClass('active');
		
		// Slide toggle for smooth animation (don't use CSS display toggle)
		$content.slideToggle(300);
	});
	
	// Restore open accordions from URL parameter or PHP variable on page load
	var openAccordions = null;
	var urlParams = new URLSearchParams(window.location.search);
	if (urlParams.has('accordions')) {
		openAccordions = urlParams.get('accordions');
	} else if (typeof cf7rl_open_accordions !== 'undefined' && cf7rl_open_accordions !== null && cf7rl_open_accordions !== '') {
		openAccordions = cf7rl_open_accordions;
	}
	
	if (openAccordions !== null && openAccordions !== '') {
		var indices = openAccordions.split(',');
		$('.cf7rl-accordion-header').each(function(index) {
			if (indices.indexOf((index + 1).toString()) !== -1) { // Compare with 1-based index
				var $header = $(this);
				var $content = $header.next('.cf7rl-accordion-content');
				$header.addClass('active');
				$content.show();
			}
		});
	}
	
	// Capture open accordions before form submit and add to form action URL
	$('form').on('submit', function() {
		var openIndices = [];
		$('.cf7rl-accordion-header.active').each(function() {
			var index = $('.cf7rl-accordion-header').index(this) + 1; // Start from 1 instead of 0
			openIndices.push(index);
		});
		
		if (openIndices.length > 0) {
			// Add hidden field for accordions state
			var $existingField = $('input[name="cf7rl_open_accordions"]');
			if ($existingField.length) {
				$existingField.val(openIndices.join(','));
			} else {
				$(this).append('<input type="hidden" name="cf7rl_open_accordions" value="' + openIndices.join(',') + '">');
			}
		}
	});
});
