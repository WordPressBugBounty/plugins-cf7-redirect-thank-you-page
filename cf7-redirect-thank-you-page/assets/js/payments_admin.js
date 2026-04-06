// Payments admin JS
(function( $ ) {
    'use strict';
    $( function() {
        $('.cf7rl-stripe-connect-notice, .cf7rl-ppcp-connect-notice').on('click', '.notice-dismiss', function(event, el){
            var $notice = $(this).parent('.notice.is-dismissible');
            var dismiss_url = $notice.attr('data-dismiss-url');
            if (dismiss_url) {
                $.get(dismiss_url);
            }
        });

        $('[name="mode"]').on('change', function(){
            const sandbox = parseInt($('[name="mode"]:checked').val()) === 1,
                $onboardingStartBtn = $('#cf7rl-ppcp-onboarding-start-btn');
            
            // Check if the onboarding button exists before accessing its attributes
            if ($onboardingStartBtn.length && $onboardingStartBtn.attr('href')) {
                const onboardingUrl = $onboardingStartBtn.attr('href').split('?'),
                    onboardingParams = new URLSearchParams(onboardingUrl[1] || '');

                if (sandbox) {
                    onboardingParams.set('sandbox', '1');
                } else {
                    onboardingParams.delete('sandbox');
                }

                onboardingUrl[1] = onboardingParams.toString();
                $onboardingStartBtn.attr('href', onboardingUrl.join('?'));
            }
        });

        $(document).on('click', '#cf7rl-ppcp-disconnect', function(e){
            e.preventDefault();

            if (!confirm('Are you sure?')) return false;

            const $this = $(this),
                $ppcpStatusTable = $('#cf7rl-ppcp-status-table');

            if ($this.hasClass('processing')) return false;
            $this.addClass('processing');

            $ppcpStatusTable.css({'opacity': 0.5});

            $.post(cf7rl.ajaxUrl, {
                action: 'cf7rl-ppcp-disconnect',
                nonce: cf7rl.nonce,
                form_id: $(this).attr('data-form-id')
            }, function(response){
                $this.removeClass('processing');
                $ppcpStatusTable.css({'opacity': 1});

                if (response.success) {
                    $ppcpStatusTable.html(response.data.statusHtml);
                } else {
                    const message = response.data && response.data.message ?
                        response.data.message :
                        'An unexpected error occurred. Please reload the page and try again.';
                    alert(message);
                }
            });

            return false;
        });
    });
})(jQuery);
