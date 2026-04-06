/**
 * Handle reCAPTCHA expiration and reset
 */
(function() {
    'use strict';
    
    // Wait for reCAPTCHA to be loaded
    var checkRecaptchaLoaded = setInterval(function() {
        if (typeof grecaptcha !== 'undefined' && grecaptcha.render) {
            clearInterval(checkRecaptchaLoaded);
            initRecaptchaHandlers();
        }
    }, 100);
    
    function initRecaptchaHandlers() {
        // Find all reCAPTCHA elements
        var recaptchaElements = document.querySelectorAll('.g-recaptcha');
        
        if (recaptchaElements.length === 0) {
            return;
        }
        
        recaptchaElements.forEach(function(element) {
            // Store the original data attributes
            var sitekey = element.getAttribute('data-sitekey');
            var theme = element.getAttribute('data-theme') || 'light';
            
            // Render the reCAPTCHA with callbacks
            try {
                var widgetId = grecaptcha.render(element, {
                    'sitekey': sitekey,
                    'theme': theme,
                    'callback': function(response) {
                        // Called when user completes the reCAPTCHA
                        if (window.console && window.console.log) {
                            console.log('reCAPTCHA completed');
                        }
                    },
                    'expired-callback': function() {
                        // Called when reCAPTCHA expires (after ~2 minutes)
                        if (window.console && window.console.log) {
                            console.log('reCAPTCHA expired - resetting');
                        }
                        
                        // Reset the reCAPTCHA so user needs to complete it again
                        grecaptcha.reset(widgetId);
                        
                        // Optional: Show a message to the user
                        showExpirationMessage(element);
                    },
                    'error-callback': function() {
                        // Called when reCAPTCHA encounters an error
                        if (window.console && window.console.log) {
                            console.log('reCAPTCHA error');
                        }
                    }
                });
                
                // Store widget ID for later use
                element.setAttribute('data-widget-id', widgetId);
                
            } catch (e) {
                // Element might already be rendered, skip
                if (window.console && window.console.log) {
                    console.log('reCAPTCHA already rendered or error:', e);
                }
            }
        });
    }
    
    /**
     * Show a temporary message when reCAPTCHA expires
     */
    function showExpirationMessage(recaptchaElement) {
        // Check if message already exists
        var existingMessage = recaptchaElement.parentElement.querySelector('.cf7rl-recaptcha-expired-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create expiration message
        var message = document.createElement('div');
        message.className = 'cf7rl-recaptcha-expired-message';
        message.style.cssText = 'color: #d63638; font-size: 14px; margin-top: 8px; padding: 8px; background: #fcf0f1; border-left: 3px solid #d63638;';
        message.textContent = 'Your reCAPTCHA verification has expired. Please verify again.';
        
        // Insert message after reCAPTCHA
        recaptchaElement.parentElement.insertBefore(message, recaptchaElement.nextSibling);
        
        // Remove message after 5 seconds
        setTimeout(function() {
            if (message.parentElement) {
                message.remove();
            }
        }, 5000);
    }
    
    /**
     * Reset reCAPTCHA when form submission fails
     * This ensures users can retry after a failed submission
     */
    document.addEventListener('wpcf7invalid', function(event) {
        resetAllRecaptchas();
    }, false);
    
    document.addEventListener('wpcf7spam', function(event) {
        resetAllRecaptchas();
    }, false);
    
    document.addEventListener('wpcf7mailfailed', function(event) {
        resetAllRecaptchas();
    }, false);
    
    /**
     * Reset all reCAPTCHA widgets on the page
     */
    function resetAllRecaptchas() {
        if (typeof grecaptcha === 'undefined') {
            return;
        }
        
        var recaptchaElements = document.querySelectorAll('.g-recaptcha');
        recaptchaElements.forEach(function(element) {
            var widgetId = element.getAttribute('data-widget-id');
            if (widgetId !== null) {
                try {
                    grecaptcha.reset(parseInt(widgetId));
                } catch (e) {
                    // Widget might not be initialized yet
                }
            }
        });
    }
    
})();
