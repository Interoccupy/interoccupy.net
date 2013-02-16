=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: widget,wysiwyg,wysiwyg widget,rich text,rich text widget,widget editor,text widget,visual widget,image widget,tinymce,fckeditor
Requires at least: 3.1
Tested up to: 3.5
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create advanced, good looking widgets with rich text editing and media upload functionality.

== Description ==

= WYSIWYG Widgets or rich text widgets =

Don't you just miss the visual editor in WordPress' default text widgets? This plugin helps by letting you create rich text widgets just like you would create a post. You can use the visual editor to create beautiful HTML and even use the WordPress media upload functionality.

**BACKWARDS COMPATIBILITY DROPPED IN VERSION 2, PLEASE BACK-UP YOUR WYSIWYG WIDGETS BEFORE UPGRADING**

**Features:**

* Create stunning widget content without having to know any HTML
* Insert media like images or video into your widgets the way you are used to
* Create easy lists in your widgets
* Use WP Links dialog to easily link to any of your pages or posts from a widget
* Use shortcodes inside your widget to benefit from other WP Plugins.

**More info:**

* [WYSIWYG Widgets](http://dannyvankooten.com/wordpress-plugins/wysiwyg-widgets/)
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress-plugins/) by the same author
* Follow Danny on Twitter for lightning fast support and updates: [@DannyvanKooten](http://twitter.com/dannyvankooten)
* [Thank Danny for this plugin by donating $10, $20 or $50.](http://dannyvankooten.com/donate/)

== Installation ==

1. Upload the contents of wysiwyg-widgets.zip to your plugins directory.
1. Activate the plugin
1. Create a WYSIWYG Widget "post" through the new menu item "WYSIWYG Widgets".
1. Go to your Widgets page, drag an instance of the WYSIWYG Widgets widget to one of your widget areas and select which WYSIWYG Widget to display.
1. Go to the front end of your website and enjoy your beautiful widget.

== Frequently Asked Questions ==

= What does this plugin do? =
This plugin creates a custom post type "Widgets" where you can create widgets just like you would create posts. You can then show these "widget posts" by dragging a "WYSIWYG Widget" widget to one of your widget areas.

= What does WYSIWYG stand for? =
What You See Is What You Get

= Can I switch between 'Visual' and 'HTML' mode with this plugin? =
Yes, all the default options that you are used to from the post editor are available for the widget editor.

= Will this plugin help me create widgets with images and links =
Yes, you won't need to write a single line of HTML.

= Is this plugin free? =
Totally free, and it will always stay free. Donations are much appreciated though, I put a lot of time and effort in my plugins. Consider [donating $10, $20 or $50](http://dannyvankooten.com/donate/) as a token of your appreciation.

== Screenshots ==

1. Overview of created WYSIWYG Widgets
2. Edit the content of a WYSIWYG Widget just like you are used to edit posts.
3. Drag the WYSIWYG Widget widget to one of your widget areas and select the WYSIWYG Widget to show.

== Changelog ==

= 2.0.1 =
* Added: meta box in WYSIWYG Widget editor screen.
* Added: debug messages for logged in administrators on frontend when no WYSIWYG Widget OR an invalid WYSIWYG Widget is selected.
* Added: title is now optional for even more control. If empty, it won't be shown. You are now longer required to use the heading tag which is set in the widget options since you can use a heading in your post.

= 2.0 =
* Total rewrite WITHOUT backwards compatibility. Please back-up your existing WYSIWYG Widgets' content before updating, you'll need to recreate them. Don't drag them to "deactivated widgets", just copy & paste the HTML content somewhere.

= 1.2 =
* Updated the plugin for WP 3.3. Broke backwards compatibility (on purpose), so when running WP 3.2.x and below: stick with [version 1.1.1](http://downloads.wordpress.org/plugin/wysiwyg-widgets.zip).

= 1.1.2 =
* Temporary fix for WP 3.3+

= 1.1.1 =
* Fixed problem with link dialog reloading page upon submit

= 1.1 =
* Changed the way WYSIWYG Widget works, no more overlay, just a WYSIWYG editor in your widget form.
* Fixed full-screen mode
* Fixed link dialog for WP versions below 3.2
* Fixed strange browser compatibility bug
* Fixed inconstistent working
* Added the ability to use shortcodes in WYSIWYG Widget's text

= 1.0.7 =
* Fixed small bug that broke the WP link dialog for WP versions older then 3.2
* Fixed issue with lists and weird non-breaking spaces
* Added compatibility with Dean's FCKEditor for Wordpress plugin
* Improved JS

**NOTE**: In this version some things were changed regarding the auto-paragraphing. This is now being handled by TinyMCE instead of WordPress, so when updating please run trough your widgets to correct this. :) 

= 1.0.6 =
* Added backwards compatibility for WP installs below version 3.2 Sorry for the quick push!

= 1.0.5 =
* Fixed issue for WP3.2 installs, wp_tiny_mce_preload_dialogs is no valid callback. Function got renamed.

= 1.0.4 =
* Cleaned up code
* Improved loading of TinyMCE
* Fixed issue with RTL installs

= 1.0.3 =
* Bugfix: Hided the #wp-link block, was appearing in footer on widgets.php page.
* Improvement: Removed buttons added by external plugins, most likely causing issues. (eg Jetpack)
* Improvement: Increase textarea size after opening WYSIWYG overlay.
* Improvement: Use 'escape' key to close WYSIWYG editor overlay without saving changes.

= 1.0.2 =
* Bugfix: Fixed undefined index in dvk-plugin-admin.php
* Bugfix: Removed `esc_textarea` which caused TinyMCE to break
* Improvement: Minor CSS and JS improvements, 'Send to widget' button is now always visible
* Improvement: Added a widget description
* Improvement: Now using the correct way to set widget form width and height

= 1.0.1 =
* Bugfix: Fixed the default title, it's now an empty string. ('')

= 1.0 = 
* Initial release

== Upgrade Notice ==

= 2.0  =
No backwards compatibility, please back-up your existing widgets before upgrading!