/**
 * CF7 Redirect Phone Field Debug Tool
 * Focused on cf7rl-phone-group spacing issue
 */
(function($) {
    'use strict';

    function debugPhoneGroup() {
        var $group = $('.cf7rl-phone-group').first();
        if (!$group.length) {
            console.log('[CF7RL-DEBUG] No .cf7rl-phone-group found');
            return;
        }

        var el = $group[0];
        var style = window.getComputedStyle(el);
        var rect = el.getBoundingClientRect();

        console.log('[CF7RL-DEBUG] === cf7rl-phone-group ===');
        console.log('Dimensions:', {
            width: rect.width,
            height: rect.height
        });
        console.log('Margin:', {
            top: style.marginTop,
            right: style.marginRight,
            bottom: style.marginBottom,
            left: style.marginLeft
        });
        console.log('Padding:', {
            top: style.paddingTop,
            right: style.paddingRight,
            bottom: style.paddingBottom,
            left: style.paddingLeft
        });
        console.log('Layout:', {
            display: style.display,
            gap: style.gap,
            flexDirection: style.flexDirection,
            alignItems: style.alignItems
        });

        // Check the validation tip sibling
        var $tip = $group.next('.wpcf7-not-valid-tip');
        if ($tip.length) {
            var tipEl = $tip[0];
            var tipStyle = window.getComputedStyle(tipEl);
            var tipRect = tipEl.getBoundingClientRect();

            console.log('[CF7RL-DEBUG] === wpcf7-not-valid-tip (sibling) ===');
            console.log('Dimensions:', {
                width: tipRect.width,
                height: tipRect.height
            });
            console.log('Margin:', {
                top: tipStyle.marginTop,
                right: tipStyle.marginRight,
                bottom: tipStyle.marginBottom,
                left: tipStyle.marginLeft
            });
            console.log('Padding:', {
                top: tipStyle.paddingTop,
                right: tipStyle.paddingRight,
                bottom: tipStyle.paddingBottom,
                left: tipStyle.paddingLeft
            });
            console.log('Position:', {
                display: tipStyle.display,
                position: tipStyle.position
            });
        }

        // Check parent wrapper
        var $wrap = $group.parent('.cf7rl-phone-wrap');
        if ($wrap.length) {
            var wrapStyle = window.getComputedStyle($wrap[0]);
            console.log('[CF7RL-DEBUG] === cf7rl-phone-wrap (parent) ===');
            console.log('Display:', wrapStyle.display);
            console.log('Gap:', wrapStyle.gap);
            console.log('Flex Direction:', wrapStyle.flexDirection);
        }
    }

    $(document).on('wpcf7invalid', function() {
        setTimeout(debugPhoneGroup, 100);
    });

    $(document).ready(function() {
        console.log('[CF7RL-DEBUG] Ready - submit form to see cf7rl-phone-group debug');
        window.cf7rlDebug = debugPhoneGroup;
    });

})(jQuery);
