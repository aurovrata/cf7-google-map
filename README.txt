=== Plugin Name ===
Contributors: aurovrata
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=64EDYAVB7EGTJ
Tags: multislide, slide form, contact form 7, contact form 7 extension, contact form 7 module
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables creation of multi-slide forms using the contact form 7 plugin.

== Description ==

This plugin is an extension (module) for Contact Form 7 plugin.  It allows users to develop multislide
forms, one cf7 form on each slide.  However, a single mail with all the combined slide-forms entry
is sent upon successful submission of the last slide-form.

= Checkout our other CF7 plugin extensions =

* [CF7 Polylang Module](https://wordpress.org/plugins/cf7-polylang/) - this plugin allows you to create forms in different languages for a multi-language website.  The plugin requires the [Polylang](https://wordpress.org/plugins/polylang/) plugin to be installed in order to manage translations.

* [CF7 Multi-slide Module](https://wordpress.org/plugins/cf7-multislide/) - this plugin allows you to build a multi-step form using a slider.  Each slide has cf7 form which are linked together and submitted as a single form.

* [Post My CF7 Form](https://wordpress.org/plugins/post-my-contact-form-7/) - this plugin allows you to save you cf7 form submissions to a custom post, map your fields to meta fields or taxonomy.  It also allows you to pre-fill fields before your form  is displayed.

== Installation ==

1. Unpack `cf7-multislide.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a new form in the CF7 editor.  Select the [multislide] tag, and place it at the end of your form.  Define the number of total slides as well as the current slide.  So if you plan on having 3 slides, you need to create 3 cf7 forms.  The first form (show on the first slide) will have slide number = 1 and total slides = 3.  In the 2nd cf7 form you will need to add a new [multislide] tag, this time the slide number = 2 and total slides = 3 and so on.
4. In your last form, you can use the previous slide-forms fields in your mail settings. Note, CF7 plugin may complain that these don't exist.  Ignore these messages.


== Frequently Asked Questions ==

= What slider should I use? =

Any slider should be compatible, use whichever is your favourite slider plugin for build your own.

= How do I make the slider change to the next slide upon successful form submission? =

Contact Form 7 allows you to add javascript command to be executed upon successful form submission.
Use this functionality to execute the slide-change command automatically.  You need to add your code to the
Additional Settings tab of your newly created form.  Here is the [CF7 documentation](http://contactform7.com/additional-settings/) for this.
You need to use the,

 `on_sent_ok: "alert('form sent!');"`

 format. You can use jQuery's [`trigger`]() function to trigger the slider click event. most
 slider plugins will create navigation buttons to change to the next slide, these are triggered
 with a click of the mouse.  Inspect your button element, and copy the css selector to uniquely identify
 this button, then add it to your form Additional Settings section as,

 `on_sent_ok:  "jQuery('#my-home-page-slider .next .nav-container').trigger('click')";`

where `#my-home-page-slider .next .nav-container` would uniquely identify your slider element to trigger
a mouse click event.

== Screenshots ==

1. A new `[multislide]` tag is added to your CF7 form options. Ideally place it at
the end of the form.
2. Define the current slide and the total number of slides.

== Changelog ==
= 1.1 =
* updated to reflect changes in Contact Form 7 v4.6
* fixed jquery bug in tag generator

= 1.0 =
* first version, only in english locale

== Final slide-form data ==

This plugin merges all slides form data into a single list of submitted data. It
does this my storing intermediate slides data as transients.  When the final slide form
is submitted, it merges all submitted data sets into a single one, allowing the CF7
plugin to find all the field tags described in the various slide forms.

Before this final merged data-set is submitted to the CF7 plugin, it applies the following
filer `cf7_mulstislide_merged_posted_data`.  You can use this filter to further interact with
the final data-set before it is being sent by email using,

`add_filter('cf7_mulstislide_merged_posted_data','filter_the_final_data')
function filter_the_final_data($posted_data){
  //do somethinn here with the array of <tag_name>=><value> pairs
  return $posted_data;
}
`
hope you find this plugin useful.
