jQuery(document).ready(function($) {
    'use strict';
    
    // Toggle time inputs based on checkbox state
    $('.cf7rl-availability-row input[type="checkbox"]').on('change', function() {
        var $row = $(this).closest('tr');
        var $timeInputs = $row.find('input[type="time"]');
        
        if ($(this).is(':checked')) {
            $timeInputs.prop('disabled', false);
        } else {
            $timeInputs.prop('disabled', true);
        }
    }).trigger('change');
});
