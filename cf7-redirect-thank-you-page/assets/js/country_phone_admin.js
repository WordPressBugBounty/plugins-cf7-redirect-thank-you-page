jQuery(document).ready(function($) {
	// Initialize CF7 tag generators for country phone fields
	if (typeof wpcf7 !== 'undefined' && typeof wpcf7.taggen !== 'undefined') {
		// Listen for when tag generator dialog opens
		$(document).on('thickbox:iframe:loaded', function() {
			var $iframe = $('#TB_iframeContent');
			if ($iframe.length) {
				var iframeDoc = $iframe[0].contentDocument || $iframe[0].contentWindow.document;
				var $panel = $(iframeDoc).find('.tag-generator-panel-countryselect, .tag-generator-panel-teltext');
				
				if ($panel.length) {
					// Trigger initial tag generation
					setTimeout(function() {
						$(iframeDoc).find('[data-tag-part="name"]').trigger('input');
					}, 200);
				}
			}
		});
		
		// Also handle when inputs change
		$(document).on('input change', '[data-tag-part]', function() {
			// Let CF7's native handler process this
		});
	}
	
	// Helper function to get country flag emoji
	function getCountryFlag(countryCode) {
		if (!countryCode || countryCode.length !== 2) return '';
		const codePoints = countryCode
			.toUpperCase()
			.split('')
			.map(char => 127397 + char.charCodeAt());
		return String.fromCodePoint(...codePoints);
	}
	
	// Check if flags should be shown for country fields
	var showCountryFlags = $('input[name="country_show_flags"]').is(':checked');
	
	// Check if flags should be shown for phone fields
	var showPhoneFlags = $('input[name="phone_show_flags"]').is(':checked');
	
	// Find all country multi-select dropdowns
	$('.cf7rl-country-multiselect').each(function() {
		var $select = $(this);
		var isPhoneField = $select.attr('name') && $select.attr('name').indexOf('phone_') === 0;
		var shouldShowFlags = isPhoneField ? showPhoneFlags : showCountryFlags;
		
		// Add flags to options if enabled
		if (shouldShowFlags) {
			$select.find('option').each(function() {
				var $option = $(this);
				var value = $option.val();
				if (value) {
					var flag = getCountryFlag(value);
					var text = $option.text();
					// Only add flag if not already present
					if (text.indexOf(flag) !== 0) {
						$option.text(flag + ' ' + text);
					}
					$option.attr('data-flag', flag);
				}
			});
		}
		
		// Create wrapper
		var $wrapper = $('<div class="cf7rl-searchable-select"></div>');
		$select.wrap($wrapper);
		$wrapper = $select.parent();
		
		// Create search input
		var $searchBox = $('<input type="text" class="cf7rl-search-input" placeholder="Type to search countries..." />');
		$select.before($searchBox);
		
		// Create selected items display
		var $selectedDisplay = $('<div class="cf7rl-selected-items"></div>');
		$select.before($selectedDisplay);
		
		// Style the select to show multiple items
		$select.attr('size', '8');
		
		// Update selected display
		function updateSelectedDisplay() {
			$selectedDisplay.empty();
			var selectedCount = $select.find('option:selected').length;
			
			if (selectedCount > 0) {
				$select.find('option:selected').each(function() {
					var optionText = $(this).text();
					var optionValue = $(this).val();
					var $tag = $('<span class="cf7rl-tag">' + 
						'<span class="cf7rl-tag-text">' + optionText + '</span>' +
						'<span class="cf7rl-tag-remove" data-value="' + optionValue + '" title="Remove">×</span>' +
						'</span>');
					$selectedDisplay.append($tag);
				});
			}
		}
		
		// Remove tag on click
		$selectedDisplay.on('click', '.cf7rl-tag-remove', function(e) {
			e.preventDefault();
			var value = $(this).data('value');
			$select.find('option[value="' + value + '"]').prop('selected', false);
			updateSelectedDisplay();
		});
		
		// Toggle selection on click (no Ctrl needed)
		$select.on('mousedown', 'option', function(e) {
			e.preventDefault();
			$(this).prop('selected', !$(this).prop('selected'));
			updateSelectedDisplay();
			return false;
		});
		
		// Prevent default selection behavior
		$select.on('click', function(e) {
			e.preventDefault();
		});
		
		// Search functionality
		$searchBox.on('keyup', function() {
			var searchTerm = $(this).val().toLowerCase();
			var visibleCount = 0;
			
			$select.find('option').each(function() {
				var optionText = $(this).text().toLowerCase();
				if (optionText.indexOf(searchTerm) > -1) {
					$(this).show();
					visibleCount++;
				} else {
					$(this).hide();
				}
			});
		});
		
		// Initial display
		updateSelectedDisplay();
	});
	
	// Handle single-select dropdowns (Default Country)
	$('select[name="country_default"], select[name="phone_default"]').each(function() {
		var $select = $(this);
		var isPhoneField = $select.attr('name') === 'phone_default';
		var shouldShowFlags = isPhoneField ? showPhoneFlags : showCountryFlags;
		
		// Add flags to options if enabled
		if (shouldShowFlags) {
			$select.find('option').each(function() {
				var $option = $(this);
				var value = $option.val();
				if (value) {
					var flag = getCountryFlag(value);
					var text = $option.text();
					// Only add flag if not already present
					if (text.indexOf(flag) !== 0) {
						$option.text(flag + ' ' + text);
					}
					$option.attr('data-flag', flag);
				}
			});
		}
		
		// Create wrapper
		var $wrapper = $('<div class="cf7rl-searchable-select cf7rl-single-select"></div>');
		$select.wrap($wrapper);
		$wrapper = $select.parent();
		
		// Create search input
		var $searchBox = $('<input type="text" class="cf7rl-search-input" placeholder="Type to search countries..." />');
		$select.before($searchBox);
		
		// Create selected display for single-select
		var $selectedDisplay = $('<div class="cf7rl-selected-single"></div>');
		$select.before($selectedDisplay);
		
		// Style the select
		$select.attr('size', '6');
		
		// Update selected display
		function updateSelectedDisplay() {
			$selectedDisplay.empty();
			var $selectedOption = $select.find('option:selected');
			
			if ($selectedOption.length && $selectedOption.val() !== '') {
				var optionText = $selectedOption.text();
				var $tag = $('<span class="cf7rl-tag">' + 
					'<span class="cf7rl-tag-text">' + optionText + '</span>' +
					'<span class="cf7rl-tag-remove" title="Clear selection">×</span>' +
					'</span>');
				$selectedDisplay.append($tag);
			}
		}
		
		// Remove selection on click
		$selectedDisplay.on('click', '.cf7rl-tag-remove', function(e) {
			e.preventDefault();
			$select.val('');
			updateSelectedDisplay();
		});
		
		// Search functionality
		$searchBox.on('keyup', function() {
			var searchTerm = $(this).val().toLowerCase();
			var visibleCount = 0;
			
			$select.find('option').each(function() {
				var optionText = $(this).text().toLowerCase();
				if (optionText.indexOf(searchTerm) > -1) {
					$(this).show();
					visibleCount++;
				} else {
					$(this).hide();
				}
			});
		});
		
		// Update display when selection changes
		$select.on('change', function() {
			$searchBox.val('');
			$select.find('option').show();
			updateSelectedDisplay();
		});
		
		// Initial display
		updateSelectedDisplay();
	});
});
