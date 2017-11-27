=== Widget Visibility Time Scheduler Pro ===
Contributors: Hinjiriyo
Tags: arabic, brazilian, control, date, day, deutsch, display, español, farsi, forever, german, future, hide, hour, hungarian, jetpack, magyar, minute, month, period, persian, plan, português do brasil, portuguese, schedule, scheduler, show, time, unlimited, visibility, weekdays, widget, widgets, year
Requires at least: 3.5
Tested up to: 4.8
Stable tag: 5.2
License: You may change and use it as often you want, but you must not sell it or must not give it away for free.

Control the visibility of each widget based on date, time and weekday easily.

== Description ==

The plugin is available in English, Spanish (Español), German (Deutsch), Brazilian Portuguese (Português do Brasil), Persian (Farsi), Hungarian (Magyar) Arabic (العربية) and Polish (Polski).

= Show and hide widgets within a desired period, weekdays and daytimes =

The Widget Visibility Time Scheduler Pro enables you to set the period, weekdays and daytimes of the visibility of each widget easily. It is available in english, german, spanish, brazilian portuguese, persian, hungarian and arabic language.

= What users said =

**"The plugin is perfect for seasonal widgets, temporary sales/promotions, events, live chat buttons, and any other time/date-dependent content."** in [Control the Visibility of WordPress Widgets Based on Time and Date](http://wptavern.com/control-the-visibility-of-wordpress-widgets-based-on-time-and-date) by Sarah Gooding on January 5, 2015.

= Compatibility with Jetpack =

This plugin works perfectly with Jetpack's "Widget Visibility" module. Both plugins enhance each other to give you great control about when and where to display which widget on your website.

= Languages =

The user interface is available in

* English
* German (Deutsch)
* Spanish (Español), kindly drawn up by [Eduardo Larequi](https://profiles.wordpress.org/elarequi)
* Brazilian Portuguese (Português do Brasil), kindly drawn up by [Christiano Albano P.](https://profiles.wordpress.org/cristianoalbanop)
* Persian (Farsi), kindly drawn up by [Sajjad Panahi](https://profiles.wordpress.org/asreelm)
* Hungarian (Magyar), kindly drawn up by [V.A.Lucky](https://wordpress.org/support/profile/valucky)
* Arabic (العربية), kindly drawn up by [Shadi AlZard](https://wordpress.org/support/profile/salzard)
* Polish (Polski), kindly drawn up by [Marcin Mikolajczyk](https://wordpress.org/support/profile/marcinmik)
* Greek (Ελληνικά)

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `widget-visibility-time-scheduler-pro.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard
6. Go to 'Widgets', set the visibility period in each widget

= Using FTP =

1. Download `widget-visibility-time-scheduler-pro.zip`
2. Extract the `widget-visibility-time-scheduler-pro` directory to your computer
3. Upload the `widget-visibility-time-scheduler-pro` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard
5. Go to 'Widgets', set the visibility period in each widget

== Frequently Asked Questions ==

= How to use? =

1. Go to the Widget page in the WordPress backend. Every widget is enhanced by easy-to-use fields for time data.
2. Set comfortably the start point of time, the end point of time and the weekdays when to display the widget. With the "unlimited" option the widget is displayed "forever" in the future.
3. After you have define the time data just save the widget settings. Done!

= Is there an option page? =

No. That is not neccessary. You set the visibility in each widget on the Widgets page in the backend.

= Do the scheduler settings effect cached pages? =

No. This plugin has no site effects to cache plugins. So it can happen that a cached page shows a widget although the scheduler settings says to hide it, and vice versa.

It is up to your cache settings how the visibility of a widget is considered. Maybe it is helpful to empty the cache automatically once a day.

= Does removing the plugin delete the settings in the database? =

Up to now: no. But you can remove the settings in the database easily with two possibilities:

* Either deactivate (uncheck) the visibility time scheduler in each widget and save the widget settings.
* Or remove the widget out of the widget area.

= Does the plugin work with Jetpack's Widget Visibility module? =

Yes. Both plugins work together perfectly and enhance each other to give you great control about when and where to display which widget.

= Why is the highest year number 2037? =

Most servers are 32-bit systems, either the hardware or the software WordPress uses: Apache, PHP, MySQL. The technical maximum time a 32-bit system can handle is 03:14:07 on Tuesday, 19 January 2038. If a user would type in a date after 19 January 2038 a strange behaviour would occur.

So to have safe values I have set deliberately the maximum valid year value to 2037. That allowed the latest point of time at 23:59:00 on December 31, 2037. And that avoids a more complicated, unsafe check for a date like “January 19, 2039 03:14:07″.

You can find a detailed and understandable explanation at [Wikipedia: Year 2038 problem](http://en.wikipedia.org/wiki/Year_2038_problem). That text also explains why the lowest year number is 1970.

= Where is the *.pot file for translating the plugin in any language? =

The plugin is ready for right-to-left languages like Arabic or Hebrew.

Further translations are welcome. If you want to give in your translation please leave a notice in the [plugin's support forum](https://wordpress.org/support/plugin/widget-visibility-time-scheduler). A translation of the plugin in your language would be great! 

All texts of the plugin are in the *.pot file. You would find the *.pot file in the 'languages' directory of this plugin. If you would send the *.po file to me I would include it in the next release of the plugin.


== Changelog ==

= 5.2 =
* Fixed typo in language-depented date format
* Tested successfully with WordPress 4.8

= 5.1 =
* Added closing and opening of the schedulers in the Customizer

= 5.0 =
* Added button for opening and closing the scheduler in the widgets
* Improved: Loads plugin's CSS and script only if the Widget page is loaded
* Revised translations

= 4.0.1 =
* Fixed wrong path to Updater class

= 4.0 =
* Improved in license activation: More stable and verbose license check
* Improved in license activation: Updated EDD Plugin Updater class
* Changed in license activation: link to shop to secure protocol
* Tested successfully with WordPress 4.7.2

= 3.1.1 =
* Tested successfully with WordPress 4.7

= 3.1 =
* Added greek translation
* Tested successfully with WordPress 4.6.1

= 3.0.1 =
* Tested successfully with WordPress 4.6

= 3.0.0 =
* Added license activation page
* Added polish translation. Thank you very much, [Marcin Mikolajczyk](https://wordpress.org/support/profile/marcinmik)
* Revised plugin activation message function
* Tested successfully with WordPress 4.5

= 2.2.2 =
* Tested successfully with WordPress 4.4

= 2.2.1 =
* Revised brazilian portuguese translation

= 2.2 =
* Added arabic translation. Thank you very much, [Shadi AlZard](https://wordpress.org/support/profile/salzard)

= 2.1 =
* Added hungarian translation. Thank you very much, [V.A.Lucky](https://wordpress.org/support/profile/valucky)
* Tested successfully with WordPress 4.3.1

= 2.0.2 =
Tiny design improvements of the daytime fields

= 2.0.1 =
* Tested successfully with WordPress 4.3
* Updated persian translation

= 2.0 =
* Simplified interface
* Changed: all weekdays are set by default
* Added styles for customizer
* Updated *.pot file and translations

= 1.1 =
* Added persian translation

= 1.0.1 =
* Added design for right-to-left languages (RTL ready)
* Fixed a typo
* Completed spanish translation
* Updated *.pot file and translations

= 1.0.0 =
* Initial release, based on the free version of the plugin
* Added local time notice
* Added functions to consider daytimes
* Updated *.pot file and translations

== Upgrade Notice ==

= 5.2 =
Fixed typo in language-depented date format, tested with WP 4.8

= 5.1 =
Added closing and opening of the schedulers in the Customizer

= 5.0 =
Added closing and opening of the schedulers

= 4.0.1 =
Fixed wrong path to Updater class

= 4.0 =
Improvements in license activation, tested with WP 4.7.2

= 3.1.1 =
Tested successfully with WordPress 4.7

= 3.1 =
Added greek translation

= 3.0.1 =
Tested successfully with WordPress 4.6

= 3.0.0 =
Revised plugin activation message function, polish translation, tested with WP 4.5

= 2.2.2 =
Tested successfully with WordPress 4.4

= 2.2.1 =
Revised brazilian portuguese translation

= 2.2 =
Added arabic translation

= 2.1 =
Added hungarian translation, tested successfully with WordPress 4.3.1

= 2.0.2 =
Tiny design improvements of the daytime fields

= 2.0.1 =
Updated persian translation, tested successfully with WordPress 4.3

= 2.0 =
Visual improvements

= 1.1 =
Added persian translation

= 1.0.1 =
Fixed a typo, completed spanish translation

= 1.0.0 =
Initial release.