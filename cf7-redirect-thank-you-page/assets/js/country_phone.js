jQuery(document).ready(function($) {
	// Initialize searchable functionality for country and phone fields
	$('.wpcf7-countryselect, .wpcf7-tel-dialcode').each(function() {
		var $select = $(this);
		
		// Wrap select in a container for positioning
		if (!$select.parent().hasClass('cf7rl-select-container')) {
			$select.wrap('<div class="cf7rl-select-container" style="position:relative;"></div>');
		}
		var $container = $select.parent('.cf7rl-select-container');
		
		// Create search panel
		var $searchPanel = $('<div class="cf7rl-search-panel" style="display:none;"></div>');
		var $searchInput = $('<input type="text" class="cf7rl-search-input" placeholder="Type to search..." />');
		var $resultsList = $('<div class="cf7rl-search-results"></div>');
		
		// Populate results with all options
		function populateResults(filterText) {
			$resultsList.empty();
			var hasResults = false;
			
			$select.find('option').each(function() {
				var $option = $(this);
				var value = $option.val();
				
				if (!value) return; // Skip empty option
				
				// Get the HTML content (includes img tags for emojis)
				var html = $option.html();
				var text = $option.text();
				
				// Filter by search text
				if (filterText && text.toLowerCase().indexOf(filterText.toLowerCase()) === -1) {
					return;
				}
				
				hasResults = true;
				var isSelected = $option.is(':selected');
				var $resultItem = $('<div class="cf7rl-search-result' + (isSelected ? ' selected' : '') + '" data-value="' + value + '"></div>');
				$resultItem.html(html); // Use html() instead of text() to preserve img tags
				$resultsList.append($resultItem);
			});
			
			if (!hasResults) {
				$resultsList.html('<div class="cf7rl-no-results">No results found</div>');
			}
		}
		
		$searchPanel.append($searchInput);
		$searchPanel.append($resultsList);
		
		// Insert panel in container
		$container.append($searchPanel);
		
		// Show panel on select click/focus
		$select.on('click focus', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			// Close other panels
			$('.cf7rl-search-panel').not($searchPanel).hide();
			
			// Show this panel
			$searchPanel.show();
			
			// Populate and focus search
			populateResults('');
			setTimeout(function() {
				$searchInput.focus();
			}, 50);
		});
		
		// Search functionality
		$searchInput.on('keyup', function() {
			var searchText = $(this).val();
			populateResults(searchText);
		});
		
		// Select result
		$resultsList.on('click', '.cf7rl-search-result', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			var $result = $(this);
			var value = $result.data('value');
			
			// Update select
			$select.val(value).trigger('change');
			
			// Close panel
			$searchPanel.hide();
		});
		
		// Prevent panel from closing when clicking inside
		$searchPanel.on('click', function(e) {
			e.stopPropagation();
		});
		
		// Prevent default select behavior
		$select.on('mousedown', function(e) {
			e.preventDefault();
			$(this).trigger('click');
		});
	});
	
	// Close panels when clicking outside
	$(document).on('click', function() {
		$('.cf7rl-search-panel').hide();
	});
});
