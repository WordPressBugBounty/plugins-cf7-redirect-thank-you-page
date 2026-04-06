jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize date picker for booking date fields
    $('.wpcf7-bookingdate').each(function() {
        var $dateField = $(this);
        var $form = $dateField.closest('form.wpcf7-form');
        var formId = $dateField.data('form-id') || $form.find('input[name="_wpcf7"]').val();
        var $timeField = $form.find('.wpcf7-bookingtime');
        
        var maxAdvanceDays = parseInt($dateField.data('max-advance')) || 30;
        
        var maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + maxAdvanceDays);
        
        var dateFormat = cf7rlBooking.dateFormat || 'Y-m-d';
        
        // Fetch disabled dates then init calendar
        $.post(cf7rlBooking.ajaxUrl, {
            action: 'cf7rl_get_disabled_dates',
            nonce: cf7rlBooking.nonce,
            form_id: formId
        }, function(response) {
            var disabledDays = response.success ? response.data.disabledDays : [];
            var unavailableDates = response.success ? response.data.unavailableDates : [];
            var disableToday = response.success ? response.data.disableToday : false;
            var minAdvanceMinutes = response.success ? response.data.minAdvanceMinutes : 1440;
            
            // Calculate minDate based on advance notice in minutes
            var minDate = new Date();
            minDate.setMinutes(minDate.getMinutes() + minAdvanceMinutes);
            
            // Get today's date string for comparison
            var today = new Date();
            var todayStr = today.getFullYear() + '-' + 
                String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                String(today.getDate()).padStart(2, '0');
            
            flatpickr($dateField[0], {
                minDate: 'today',
                maxDate: maxDate,
                dateFormat: dateFormat,
                altInput: true,
                altFormat: dateFormat,
                disableMobile: true,
                allowInput: false,
                clickOpens: true,
                disable: [
                    function(date) {
                        // Disable specific days of week
                        if (disabledDays.indexOf(date.getDay()) !== -1) return true;
                        
                        // Build date string for comparison
                        var dateStr = date.getFullYear() + '-' + 
                            String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(date.getDate()).padStart(2, '0');
                        
                        // Disable specific unavailable dates
                        if (unavailableDates.indexOf(dateStr) !== -1) return true;
                        
                        // Disable today if server says no slots available
                        if (disableToday && dateStr === todayStr) return true;
                        
                        return false;
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        var serverDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                        loadTimeSlots(serverDate, formId, $timeField);
                    } else {
                        $timeField.html('<option value="">Select a time</option>');
                    }
                }
            });
        });
        
        $dateField.attr('readonly', 'readonly').css('cursor', 'pointer');
    });
    
    // Function to load available time slots via AJAX
    function loadTimeSlots(date, formId, $timeField) {
        $timeField.prop('disabled', true);
        $timeField.html('<option value="">' + cf7rlBooking.strings.loading + '</option>');
        
        $.ajax({
            url: cf7rlBooking.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cf7rl_get_time_slots',
                nonce: cf7rlBooking.nonce,
                date: date,
                form_id: formId
            },
            success: function(response) {
                if (response.success && response.data.slots) {
                    var slots = response.data.slots;
                    
                    if (slots.length === 0) {
                        $timeField.html('<option value="">' + cf7rlBooking.strings.noSlots + '</option>');
                    } else {
                        var options = '<option value="">Select a time</option>';
                        
                        $.each(slots, function(index, time) {
                            // The value is the formatted time (12h or 24h based on setting)
                            // The display text is also the formatted time
                            var formattedTime = formatTime(time);
                            options += '<option value="' + formattedTime + '">' + formattedTime + '</option>';
                        });
                        
                        $timeField.html(options);
                    }
                } else {
                    $timeField.html('<option value="">' + cf7rlBooking.strings.noSlots + '</option>');
                }
                
                $timeField.prop('disabled', false);
            },
            error: function() {
                $timeField.html('<option value="">' + cf7rlBooking.strings.noSlots + '</option>');
                $timeField.prop('disabled', false);
            }
        });
    }
    
    // Format time for display using WordPress time format
    function formatTime(time) {
        var phpFormat = cf7rlBooking.timeFormat || 'H:i';
        var parts = time.split(':');
        var hours = parseInt(parts[0]);
        var minutes = parts[1];
        
        var result = phpFormat;
        
        // 12-hour format without leading zero
        var hours12 = hours % 12;
        hours12 = hours12 ? hours12 : 12;
        
        // AM/PM
        var ampm = hours >= 12 ? 'PM' : 'AM';
        var ampmLower = hours >= 12 ? 'pm' : 'am';
        
        // Replace PHP format tokens with values
        result = result.replace('H', hours.toString().padStart(2, '0'));  // 24-hour with leading zero
        result = result.replace('G', hours.toString());                    // 24-hour without leading zero
        result = result.replace('h', hours12.toString().padStart(2, '0')); // 12-hour with leading zero
        result = result.replace('g', hours12.toString());                  // 12-hour without leading zero
        result = result.replace('i', minutes);                             // Minutes with leading zero
        result = result.replace('A', ampm);                                // AM/PM uppercase
        result = result.replace('a', ampmLower);                           // am/pm lowercase
        
        return result;
    }
});
