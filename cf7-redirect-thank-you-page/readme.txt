=== Business Essentials for Contact Form 7 ===
Contributors: scottpaterson,wp-plugin
Donate link: https://wpplugin.org/donate/
Tags: contact form 7, payments, database, appointments, reCAPTCHA
Author URI: https://wpplugin.org
Requires at least: 3.0
Tested up to: 6.9
Requires PHP: 5.6
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Business Essentials for Contact Form 7

== Description ==

= Overview =

Transform your Contact Form 7 into a complete business solution. Business Essentials adds the professional features you need to accept payments, fight spam, capture leads, schedule appointments, and create stunning forms - all without the complexity of multiple plugins.

The all-in-one toolkit designed for growing businesses. Choose from seven powerful modules and activate only what you need. No bloat, no confusion - just the features that matter to your business, working seamlessly with Contact Form 7.

Whether you're taking online payments, protecting against spam bots, storing customer submissions, or booking appointments, Business Essentials gives you enterprise-level functionality with the simplicity Contact Form 7 users love.

= Available Modules =

* **Redirect & Thank You Page** - Redirect users to a URL or display a custom thank you message after form submission
* **PayPal & Stripe Payments** - Accept payments through your forms using PayPal or Stripe payment gateways
* **Google reCAPTCHA v2** - Protect your forms from spam with Google reCAPTCHA verification
* **Country & Phone Fields** - Add country dropdowns and international phone number fields with dial codes
* **Database Submissions** - Store all form submissions in your database and export to CSV
* **Bookings & Appointments** - Add date and time picker fields for appointment scheduling
* **Material Design** - Apply beautiful Material Design styling to your Contact Form 7 forms


Each Contact Form 7 contact form has its own settings for each enabled module.

= Thank You Page Feature =
When a contact form is set to use the Thank You Page and the user submits the form, it will send the Contact Form 7 email as usual, then redirect the user to the Thank You Page. This Thank You Page is not an actual "page" on your site, meaning form-specific data can be shown on this page without being visible to other users or indexed by search engines.

= URL Redirect Feature =
When a contact form is set to redirect to a URL and the user submits the form, it will send the Contact Form 7 email as usual, then redirect the user to the specified URL.

= PayPal & Stripe Payments =
Accept one-time payments through PayPal or Stripe directly from your contact forms. When a user submits a form with payments enabled, the email is sent as usual, then the user is redirected to complete payment. All payments are tracked in a dedicated admin area where you can view transaction status, amounts, and payment details.

= Google reCAPTCHA v2 =
Protect your forms from spam with Google reCAPTCHA v2 verification. When enabled for a form, users must complete the reCAPTCHA challenge before submitting. Configure your site key and secret key in the settings, then enable reCAPTCHA on individual forms. Customize the theme (light or dark) and position (above or below submit button).

= Country & Phone Fields =
Add country dropdown and international phone number fields to your forms. The country field displays a searchable dropdown with country flags. The phone field includes a dial code selector that automatically formats phone numbers with the correct international prefix. Configure default countries, include/exclude specific countries, and set preferred countries to appear at the top of the list.

= Database Submissions =
Store all form submissions in your WordPress database for easy access and management. View submissions organized by form, search through entries, and export data to CSV. Each submission captures all form fields along with the submission date. Delete individual entries or export entire datasets for reporting.

= Bookings & Appointments =
Add date and time picker fields to your forms for appointment scheduling. Configure available days and hours, set slot durations, and define minimum advance booking time. The system prevents double-bookings by tracking confirmed appointments. Set unavailable dates for holidays or closures.

= Material Design Theme =
Apply beautiful Material Design styling to your Contact Form 7 forms. Enable per-form and customize the primary color, background color, and vertical spacing. Optional floating labels provide a modern input experience. The styling is applied via CSS classes so it won't conflict with your theme.

= List of Features =
* Modular system - enable only the features you need
* Each Contact Form can redirect to its own URL
* Each Contact Form can redirect to a Thank You Page
* Accept PayPal and Stripe payments
* Google reCAPTCHA v2 spam protection
* Country dropdown and international phone fields
* Database storage with CSV export
* Booking date and time pickers
* Material Design form styling

= Support =
If you have any problems, questions, or issues about this plugin then please create a support request and we will get back to you quickly.

WPPlugin LLC is based in Boulder, Colorado. You can visit WP Plugin's website at wpplugin.org. Various trademarks held by their respective owners.




== Installation ==

= Automatic Installation =
> 1. Sign in to your WordPress site as an administrator.
> 2. In the main menu go to Plugins -> Add New.
> 3. Search for Business Essentials for Contact Form 7 and click install.
> 4. That's it. You are now ready to you the plugin with your contact forms.

== Frequently Asked Questions ==

= Does the plugin disables Contact Form 7 Ajax? =
Not by default, however there is a setting which allows you to turn off Contact Form 7 Ajax if you want. This can be useful for certain situations where the form does not redirect correctly.

= Does this plugin uses "on_sent_ok" additional setting? =
No, on_sent_ok is not depreceated by Contact Form 7 and has been replaced by DOM events.

== Screenshots ==
1. Modules Settings Page
2. Country Dropdown Example
3. Booking Calendar Example
4. Booking Time Example
5. Form Booking Settings


== Changelog ==

= 1.2.1 =
* 1/12/26
* Fix - Fixed issue where redirect settings were not preserved when updating from version 1.1 to 1.2
* Fix - Security issues.

= 1.2 =
* 1/9/26
* New - Added modular system for managing plugin features
* New - Added Modules tab to settings page with toggle switches
* New - Redirect functionality can now be enabled/disabled via Modules tab
* New - Added 6 other modules, including payments, Google reCAPTCHA, Country and Phone Fields, Database Storage for Submissions, Booking and Appointments, and Material Design Theme. 
* Change - Renamed plugin to "Business Essentials for Contact Form 7"

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

= 1.2 =
Initial release