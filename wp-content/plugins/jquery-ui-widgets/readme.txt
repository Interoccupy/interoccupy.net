=== jQuery UI Widgets ===
Contributors: dgwyer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MTVAN3NBV3HCA
Tags: jquery, tabs, accordion, dialog, ui, admin, enqueue, themeroller, styles, themes
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.22
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple, flexible, and powerful way to add jQuery UI widgets to your site posts, pages, or widgets. Works right out of the box!

== Description ==

Important! This Plugin requires WordPress 3.5 as it uses the jQuery UI 1.9 library shipped with this version of WordPress.

From version 0.2 this Plugin has been virtually rewritten to make it simpler and more intuitive to use! So, just how easy is it?

1. Install and activate the Plugin.
2. Add your jQuery code to the 'Custom jQuery Code' text box in Plugin settings.
3. On your post, page, or text widget, add the corresponding HTML markup.
4. That's it!!

You can easily tweak further settings such as the jQuery UI theme used to render the jQuery widgets, choose which scripts are added to your site, and overriding default CSS.

All standard pre-defined jQuery themes are supported, or you can upload your own custom theme built with the <a href="http://jqueryui.com/themeroller/" target="_blank">jQuery ThemeRoller</a>. See the FAQ page for detailed instructions on uploading your own custom theme.

No need to mess about with cryptic shortcodes! Just enter clean, valid, HTML markup and the Plugin does the rest, adding all the necessary jQuery scripts and styles for you!

Note: This Plugin uses the Google CDN to load the CSS for the official jQuery UI themes.

Please rate/review this Plugin if you find it useful. And see our <a href="http://www.presscoders.com" target="_blank">WordPress development site</a> for more Plugins and themes.

== Frequently Asked Questions ==

**I am not really sure what HTML code I need to add for each jQuery UI widget. Can you give me some examples?**

There are plenty of examples for each jQuery widget on the official <a href="http://jqueryui.com/demos/" target="_blank">demo and documentation pages</a> which include example code you can analyse and use on your own pages.

**How do you upload a custom theme using the official jQuery ThemeRoller?**

The Plugin supports themes created with the <a href="http://jqueryui.com/themeroller/" target="_blank">jQuery interactive ThemeRoller</a> which means you can very easily create a custom jQuery theme to match your WordPress theme perfectly.

1. Create your custom theme using the ThemeRoller online tool. When you have finished your masterpiece, click the 'Download theme' button.
2. On the 'Build Your Download' page make sure ALL check boxes are seletected (this is important).
3. Click the 'Advanced Theme Settings' button on the right hand side of the page and a couple of text boxes will come into view. Add a name for your custom theme in the 'Theme Folder Name' text box. Make sure that any words are separated by a dashes NOT spaces.
4. Finally, before downloading your custom theme, make sure the version radio button selected is 1.9.xx.
5. Now, you can click 'Download' to download your custom theme to your computer. It will be saved as a zip file.
6. Locate your downloaded custom theme and extract the zip file.
7. Open up the extracted custom theme folder, and you'll see three folders: 'css', 'development-bundle', and 'js'. The one we are interested in is 'css'.
8. Open the 'css' folder and inside will be a single folder containing your theme, which has the name you specified above. Inside this folder will be an images folder and a single stylesheet. Make a note of the stylesheet name, you will need it later on.
9. You need to now upload this custom theme folder to your site, so the Plugin can read the custom styles.
10. If you take a look at the Plugin settings page you will see the folder name that you should upload to. Usually this will be something like http://www.mysite.com/wp-content/uploads/. If you are running a WordPress multisite this will be something different. Using FTP upload your custom jQuery theme to this folder.
11. Finally, you just need to paste in the name of your custom theme stylsheet in Plugin settings, in the textbox provided.
12. So, if your custom theme folder is named 'mytheme' and your custom stylesheet is called 'jquery-ui-1.9.2.custom.css' then you would need to paste in something like 'mytheme/jquery-ui-1.9.2.custom.css' into the textbox.
13. If you wanted to try out multiple custom themes I would recommend adding a 'jquery-ui-themes' folder (or whatever name you wanted) and uploading all of your custom themes to this folder.
14. The path to your stylesheet would then become 'jquery-ui-themes/mytheme/jquery-ui-1.9.2.custom.css'.
15. If for whatever the reason the Plugin cannot find your stylesheet file it will display a warning message on the Plugin settings page.

**I have an issue with how the jQuery UI widgets are rendering with my WordPress theme. Can you help fix it?**

This isn't a Plugin issue. Any problems with how the jQuery CSS interacts with your current WordPress theme will need to be fixed by using tools such as Firebug, or by contacting the theme author to help tweak the CSS. I'm afraid I can't help with CSS queries for specific themes.

**The custom jQuery UI init code I added doesn't seem to be working. What could the problem be?**

Again, this isn't a Plugin issue. If you are using custom init code for some jQuery widgets and it isn't working then please use Firebug to detect the issue. I would also recommend posting on the jQuery forums if you are stuck.

== Installation ==

Instructions for installing:

1. In your WordPress admin go to Plugins -> Add New.
2. Enter jQuery UI Widgets in the text box and click Search Plugins.
3. In the list of Plugins click Install Now next to the jQuery UI Widgets Plugin.
4. Once installed click to activate.
5. Visit the Plugin options page via Settings -> jQuery UI Widgets.

Usage:

1. Go to Plugin settings and add your jQuery UI code. e.g. $( ".tabs" ).tabs().
2. Select the check boxes for the jQuery UI scripts you want to be available on your site pages (they are all selected by default when the Plugin is first activated).
3. Select the theme that you want to use to render the jQuery UI elements.
4. Quite often, depending on the WordPress theme you are using (and the specific jQuery selectors), you may run in minor issues with the CSS rendering. To help with this, there is a text box that you can enter custom style rules to tweak the jQuery theme styles, so they match your theme perfectly.to match your WordPress theme.
5. To upload a custom jQuery theme created with the ThemeRoller, see the FAQ for specific details.

== Screenshots ==

1. Plugin settings.
2. jQuery UI theme example.
3. jQuery UI theme example.
4. jQuery UI theme example.
5. jQuery UI theme example.

== Changelog ==

*0.22*

* Small bug fix.

*0.21*

* Updated Plugin readme.txt.

*0.2*

* Plugin UI in general changed around a bit and updated to make it easier and more intuitive.
* No default jQuery UI code is added to your site anymore unless added specifically by you. This makes the Plugin simpler to use and more intuitive.
* The Plugin admin text boxes will dynamically vary in height depending on the content, to make it easier to view your added code, but is capped at a maximum height. The text boxes will also shrink again when you remove content.
* Support added for entire jQuery UI stack included with WordPress 3.5 (including jQuery UI Effects).

*0.1*

* Initial release.