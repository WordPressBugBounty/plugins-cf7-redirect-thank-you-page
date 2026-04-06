(function($) {
	'use strict';
	
	// Initialize Material Theme when document is ready
	$(document).ready(function() {
		initMaterialTheme();
	});
	
	// Re-initialize after AJAX form submission
	document.addEventListener('wpcf7mailsent', function() {
		setTimeout(function() {
			initMaterialTheme();
		}, 100);
	});
	
	function initMaterialTheme() {
		$('.cf7rl-material-theme').each(function() {
			var $form = $(this);
			var formId = $form.data('form-id');
			
			// Get settings for this form
			var settings = {};
			if (typeof cf7rlMaterialTheme !== 'undefined' && cf7rlMaterialTheme.settings[formId]) {
				settings = cf7rlMaterialTheme.settings[formId];
			}
			
			// Initialize floating labels
			if (settings.enable_floating_labels !== '0') {
				initFloatingLabels($form);
			}
			
			// Initialize ripple effect
			if (settings.enable_ripple !== '0') {
				initRippleEffect($form);
			}
			
			// Add Material classes to form controls
			addMaterialClasses($form);
		});
	}
	
	function addMaterialClasses($form) {
		// Add classes to inputs
		$form.find('input[type="text"], input[type="email"], input[type="url"], input[type="tel"], input[type="number"], input[type="date"]').addClass('cf7rl-material-input');
		
		// Add classes to textareas
		$form.find('textarea').addClass('cf7rl-material-textarea');
		
		// Add classes to buttons
		$form.find('input[type="submit"], button').addClass('cf7rl-material-button');
		
		// Add classes to selects
		$form.find('select').addClass('cf7rl-material-select');
	}
	
	function initFloatingLabels($form) {
		// Find all form control wrappers
		$form.find('.wpcf7-form-control-wrap').each(function() {
			var $wrapper = $(this);
			var $input = $wrapper.find('input, textarea, select').first();
			
			if ($input.length === 0) {
				return;
			}
			
			// Skip if input already has a floating label
			if ($wrapper.find('.cf7rl-material-label').length > 0) {
				return;
			}
			
			// Get placeholder or create label text
			var labelText = $input.attr('placeholder') || $input.attr('aria-label') || '';
			
			if (labelText) {
				// Remove placeholder
				$input.removeAttr('placeholder');
				
				// Create floating label
				var $label = $('<label class="cf7rl-material-label">' + labelText + '</label>');
				$wrapper.prepend($label);
				
				// Check if input has value on load
				if ($input.val() !== '') {
					$label.addClass('active');
				}
				
				// Handle focus
				$input.on('focus', function() {
					$label.addClass('active');
				});
				
				// Handle blur
				$input.on('blur', function() {
					if ($input.val() === '') {
						$label.removeClass('active');
					}
				});
				
				// Handle input change
				$input.on('input change', function() {
					if ($input.val() !== '') {
						$label.addClass('active');
					} else {
						$label.removeClass('active');
					}
				});
			}
		});
	}
	
	function initRippleEffect($form) {
		$form.find('.cf7rl-material-button').off('click.ripple').on('click.ripple', function(e) {
			var $button = $(this);
			
			// Remove any existing ripples
			$button.find('.cf7rl-material-ripple').remove();
			
			// Create ripple element
			var $ripple = $('<span class="cf7rl-material-ripple"></span>');
			
			// Get button dimensions
			var btnOffset = $button.offset();
			var btnWidth = $button.outerWidth();
			var btnHeight = $button.outerHeight();
			
			// Calculate ripple position
			var x = e.pageX - btnOffset.left;
			var y = e.pageY - btnOffset.top;
			
			// Set ripple size (diameter of the circle)
			var rippleSize = Math.max(btnWidth, btnHeight);
			
			// Position and size the ripple
			$ripple.css({
				width: rippleSize,
				height: rippleSize,
				left: x - (rippleSize / 2),
				top: y - (rippleSize / 2)
			});
			
			// Add ripple to button
			$button.append($ripple);
			
			// Remove ripple after animation
			setTimeout(function() {
				$ripple.remove();
			}, 600);
		});
	}
	
	// Handle form validation errors with Material styling
	document.addEventListener('wpcf7invalid', function(event) {
		var $form = $(event.target);
		
		if ($form.hasClass('cf7rl-material-theme')) {
			// Add error styling to invalid fields
			$form.find('.wpcf7-not-valid').each(function() {
				$(this).closest('.wpcf7-form-control-wrap').addClass('cf7rl-material-error');
			});
		}
	});
	
	// Remove error styling when user starts typing
	$(document).on('input', '.cf7rl-material-theme .wpcf7-not-valid', function() {
		$(this).removeClass('wpcf7-not-valid');
		$(this).closest('.wpcf7-form-control-wrap').removeClass('cf7rl-material-error');
		$(this).siblings('.wpcf7-not-valid-tip').remove();
	});
	
})(jQuery);
