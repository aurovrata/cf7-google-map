=== Google Map for Contact Form 7 ===
Contributors: aurovrata
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=64EDYAVB7EGTJ
Tags: google map, maps, contact form 7, contact form 7 extension, contact form 7 module, location, geocode, reverse geocode, airplane mode
Requires at least: 4.4
Tested up to: 5.0.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables the insertion of google maps into contact form 7 as an input field.

== Description ==

This plugin enables the insertion of google maps into contact form 7 as an input field, functionality available with this plugin include
* the zoom and default location to be configured in the form edit page itself, thus different forms can have different default map zoom levels and pin location
* the front end form displays the configured map and registers the location change of the pin which can be included in the email notification.
* play nice with the [Post My CF7 Form](https://wordpress.org/plugins/post-my-contact-form-7/) plugin
* a search field is available to lookup addresses
* an optional set of address fields can be enabled from the cf7 tag to display reverse-geocode text address
* if a user changes manually the first line of the (optional) address field, the reverse-geocode is frozen.  This allows for address corrections.
* google map is disabled for [Airplane Mode plugin](https://github.com/norcross/airplane-mode/releases) activation to allow you to develop without an Internet connection.
* the plugin makes use of [JQuery Google Maps (gmap3) plugin](https://gmap3.net/).

= Checkout our other CF7 plugin extensions =

* [CF7 Polylang Module](https://wordpress.org/plugins/cf7-polylang/) - this plugin allows you to create forms in different languages for a multi-language website.  The plugin requires the [Polylang](https://wordpress.org/plugins/polylang/) plugin to be installed in order to manage translations.

* [CF7 Multi-slide Module](https://wordpress.org/plugins/cf7-multislide/) - this plugin allows you to build a multi-step form using a slider.  Each slide has cf7 form which are linked together and submitted as a single form.

* [Post My CF7 Form](https://wordpress.org/plugins/post-my-contact-form-7/) - this plugin allows you to save you cf7 form submissions to a custom post, map your fields to meta fields or taxonomy.  It also allows you to pre-fill fields before your form  is displayed.

* [CF7 Google Map](https://wordpress.org/plugins/cf7-google-map/) - allows google maps to be inserted into a Contact Form 7.  Unlike other plugins, this one allows map settings to be done at the form level, enabling diverse maps to be configured for each forms.

== Installation ==

1. Unpack `cf7-google-map.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Get a [Google Map API key](https://developers.google.com/maps/documentation/javascript/get-api-key#key) and insert it in the plugin Settings->CF7 Google Map page. Make sure you enable the required APIs (see faq #).
4. Create a new form in the CF7 editor.  Select the [Google Map] tag, and configure your map.
5. The plugin creates 2 email tags for submitted location, the `lat-<field-name' and `lng-<field-name>`.  This allows you to include multiple maps in a single form if needed.


== Frequently Asked Questions ==

= 1. My map is darkened , or 'negative' and is watermarked with the text "for development purposes only". =
This is an issue with your Google API key not having the APIs enabled.  You need to ensure several things.  If you have enabled both Geocode API option and Google Places in the plugin settings, then you need to make sure those APIs are enabled on your key.  To enalbe the APIs, log into your Google [dashboard](https://console.cloud.google.com/projectselector/home/dashboard), select your project (or create a new one) and navigate to the **APIs & Services** section.  You can then enable/add APIs and search for the Geocoding API and the Google Places API and enable the ones you need.  If you are still facing this issue, check Google's other steps in this [FAQ](https://developers.google.com/maps/faq#api-key-billing-errors) on this issue.

= 2. I am based in Brazil/Canada/India and my map is not working. =

If you are facing the issue described in faq#1 above, and you have enabled all the required APIs but your map is still not functioning, then likely the issue you are facing is related to billing.  Request from Brazil/Canada/India need to have API Keys for projects that are linked to a billing-enabled account. See this [issue](https://developers.google.com/maps/faq#api-key-billing-errors) on Google's faq.

== Screenshots ==
1. Save your Google API key in the settings, else your map will not function
2. Insert a Google Map tag into your cf7 form
3. You can set the default parameters for your map, this will be used to display the default zoom level as well as pin location in the form
4. The map is by default set to take up 100% width in the form, and a height of 120px.  Override this in your child css stylesheet to size up your map.
5. Optional address fields get auto-filled by the reverse-geocode lookup.  The map as contains a search field to locate an address (you will need to enable the appropriate Google APIs).



== Changelog ==
=1.3.1=
* url scheme bug fix.
=1.3.0=
* settings for Geocoding API and Google Places API.
* faq updated with more info.
* searchbox places marker are now draggable.
* searchbox places marker delete default marker location.
=1.2.6=
* fix optional address field bug.
* fix map not being displayed for std cf7 forms.
=1.2.5=
* fix WP_GURUS_DEBUG constant warning.
=1.2.4=
* airplane-mode plugin compatible.
=1.2.3=
* bug fix: validation error message
=1.2.2=
* bug fix: map centre on drag.
=1.2.1=
* bug fix for loading existing draft form maps.
= 1.2 =
* enable loading map coordinates in saved draft forms.
* map inputs not cleared when draft form saved using Post My CF7 Form plugin.
* bug fix saving map details using Post My CF7 Form plugin.
= 1.1 =
* added search field
* added optional address fields with reverse-geocoding
= 1.0 =
* first version, only in english locale

== Final slide-form data ==
