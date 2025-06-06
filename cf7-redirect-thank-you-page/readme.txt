=== Contact Form 7 Redirect & Thank You Page ===
Contributors: scottpaterson,wp-plugin
Donate link: https://wpplugin.org/donate/
Tags: contact form 7, cf7 redirect, thank you page, cf7, redirect to url
Author URI: https://wpplugin.org
Requires at least: 3.0
Tested up to: 6.8
Requires PHP: 5.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Contact Form 7 Redirect & Thank You Page

== Description ==
= Overview =

This plugin adds Contact Form 7 Redirect & Thank You Page features

Watch this short video of how the plugin works:

[youtube https://www.youtube.com/watch?v=QjDdjXf0Gxc]

Each Contact Form 7 contact form has its own Redirect and Thank You Page settings.

#### Thank you Page Feature 
When a contact form is set to use the Thank You Page and the user submits the form,  it will send the Contact Form 7 email as usual, then redirect the user to the Thank You Page. This Thank You Page is not an actual "page" on your site, this means that form specific data can be shown on this page and it won't be shown to any other users or indexed by search engines. The Pro version of this plugin allows you to show user specific data on the Thank You Page.

#### URL Redirect Feature 
When a contact form is set to redirect to a URL and the user submits the form it will send the Contact Form 7 email as usual, then redirect the user to the URL specified.

#### List of Features
* Each Contact Form can redirect to it's own URL
* Each Contact Form can redirect to a Thank You Page
* Link form items like dropdown menus or radio buttons to specific URLs ([Pro Version only](https://wpplugin.org/downloads/contact-form-7-redirect-thank-you-page-pro/?utm_source=repo&utm_medium=cf7rl&utm_campaign=readme))
* Use form items like input fields or dropdown menus in your Thank You Page body. ([Pro Version Only](https://wpplugin.org/downloads/contact-form-7-redirect-thank-you-page-pro/?utm_source=repo&utm_medium=cf7rl&utm_campaign=readme))

#### Support

If you have any problems, questions, or issues about this plugin then please create a support request and we will get back to you quickly.

WPPlugin LLC is based in Boulder, Colorado. You can visit WP Plugins website at wpplugin.org. Various trademarks held by their respective owners.




== Installation ==

= Automatic Installation =
> 1. Sign in to your WordPress site as an administrator.
> 2. In the main menu go to Plugins -> Add New.
> 3. Search for Contact Form 7 - Redirect & Thank You Page and click install.
> 4. That's it. You are now ready to start accepting PayPal payment on your website through your contact form.

== Frequently Asked Questions ==

= Does the plugin disables Contact Form 7 Ajax? =
Not by default, however there is a setting which allows you to turn off Contact Form 7 Ajax if you want. This can be useful for certain situations where the form does not redirect correctly.

= Does this plugin uses "on_sent_ok" additional setting? =
No, on_sent_ok is not depreceated by Contact Form 7 and has been replaced by DOM events.

== Screenshots ==
1. URL Redirect Settings
2. Thank You Page Redirect Settings
3. Settings Page


== Changelog ==

= 1.1 =
* 6/4/25
* Fix - Fixed issue with double slashes being added to redirect URL
* Fix - Fixed issue with enqueue css and js files having an ../ file path
* Fix - Fixed issue with redirect method 2 not working. The Contact Form 7 plugin was using wpcf7-mail-sent-ok which was discontinued for the data-status attribute.
* New - Added 1 week of using the plugin 'please review' admin notice.

= 1.0.9 =
* 6/2/25
* Change - Updated main PHP file to include PHP, WP minimum required version, and that the plugin requires Contact Form 7.
* New - Added banner images to plugin assests folder.

= 1.0.8 =
* 1/11/25
* Fix - Settings page small security issue.

= 1.0.7 =
* 11/8/24
* Fix - Settings page small security issue.

= 1.0.6 =
* 8/1/24
* Fix - Fixed issue with thank you page not showing after form is submitted.

= 1.0.5 =
* 7/31/24
* Fix - Changed redirection default settings. This will allow redirection to work for more websites without any configuration changes.

= 1.0.4 =
* 3/20/23
* Fix - Settings page security issue
* Tested - Tested up to 6.2.x

= 1.0.3 =
* 2/4/20
* Fix - CSS style issue on settings page, extensions tab.
* Tested - Tested up to 5.3.x

= 1.0.2 =
* 2/28/19
* Fix - Redirect issue with mutiple forms on the same page.

= 1.0.1 =
* 2/24/19
* Fix - Plugin page settings link was not showing

= 1.0 =
* 2/24/19
* Initial release


== Upgrade Notice ==

= 1.0 =
Initial release