=== NS Cloner - Site Copier ===
Contributors: neversettle
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=53JXD4ENC8MM2&rm=2
Tags: never settle, automate, duplicate, copy, copier, clone, cloner, multisite, nework, subdomain, template, developer
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The NS Cloner saves multisite admins LOTS of time by enabling them to clone existing sites in their network to a completely new site in a few seconds.

== Description ==

This is by far the easiest, fastest, and most user-friendly way you will ever create fully configured sites on your multisite networks. As with everything we do, Never Settle is fanatical about simplifying user tasks to an absolute bare and joyful minimum without sacrificing the complex functionality going on behind the scenes. You will not find another site cloner that comes anywhere close to how easy this is to use.

The NS Cloner will take any existing site on your WordPress multisite network and clone it into a new site that is completely identical in theme & theme settings, plugins & plugin configurations, content, pictures, videos, and site settings. **Everything** is preserved and intelligent replacements are made so that the new site settings reflect your choices for the name and title of the new site as well as other automated background housekeeping to make sure the new site works exactly the same way as if you had taken the time to set it all up manually.

= Standard Precautions and Notes =
* This plugin ONLY works on WordPress Multisite and is not for use on single site installations. 
* It now supports both subdomain and subdirectory mode! It will auto-detect and auto-render appropriate UI.
* We have used the NS Cloner on production systems for months and months without issue. That doesn't mean your scenario won't find some new condition that could cause you some headaches. Unlikey, but always possible. We recommend getting familiar with it on a test system before you deploy it to a critical network.
* And for the love - backup your data. This plugin operates at the database level to work its magic. We've run it hundreds of times on our own sites and client sites, and tested it thoroughly. It's safe. But don't take our word for it.

= Typical Workflow for using the NS Cloner =
1. Set up 1 or more "template" sites exactly the way you want your clones to start out
1. Go to your Network Dashboard > Sites > NS Cloner
1. Select the "template" site you that want to clone, type the name of the new site, and put in it's Title
1. Clone Away!

Yes, it really is that easy.

= Primary Use Cases =
* Developers who host and manage multiple client sites in their own multisite environment - this will allow you to rapidly roll out new baseline sites with all your favorite standard plugins and configurations in place - no more tedious manual repetitive entry.
* Organizations which provide "member" sites and want to be able to reduce the site spin up time to almost nothing.
* Affiliates that host numerous sites through Multisite and are looking for a way to increase reach and decrease deployment times. 
* Designers who want to be able to create several versions of sites to test and play with different theme designs in parallel without having to re-install all the same plugins and base themes over and over.

== Features ==

= Some of the NS Cloner highlight features: =
1. Incredibly simple 4 step process to copy entire sites in seconds
1. Works in subdomain or subdirectory modes and auto-detects which is in use
1. Copies ALL theme and plugin settings
1. Copies ALL content including custom post types and taxonomies
1. Copies ALL site settings and configurations 
1. Copies ALL media files uploaded to the original site
1. Intelligently replaces subdomain and subdirectory names to ensure that everything works in the context of the new site

== Installation ==

1. Log in to your WordPress network as a multisite super admin and go to /wp-admin/network
1. Use the built-in *Plugins* tools to install NS Cloner from the repository or Upload the `ns-cloner` directory to the `/wp-content/plugins/` directory
1. Network Activate the plugin through the 'Plugins' menu in WordPress
1. Access the NS Cloner tool in the Network Sites Menu

== Frequently Asked Questions ==

= Does the NS Cloner work on subdomain networks as well as subfolder networks? =

YES! We have just added this functionality.

= When I click the "Clone Away" submit button, the new site is created, but the response generates a 404 page not found? =

Check with your host. They probably have an agreesive mod_security configuration and might need to add or modify some rules for you. For example, rule 1234234 needs to be present which allows dots in querystring parameters.

= When will the Pro version be available? =

Wait no longer! It's here: http://neversettle.it/shop/ns-cloner-pro/

= Why can't I clone the root site (ID:1)? =

The tables for the root site are prefixed differently than all the other tables in sub sites and this structure doesn't lend itself to the same automation that is possible with ID > 1. We are looking into away to support this as well.

= Why aren't my images or files being copied to the new cloned site? =

The Cloner looks for your media files and uploads in the standard, default directories depending on WP version number. If you have set a custom upload location and/or if another plugin or theme has altered that upload location dynamically, it might not be able to automatically copy the files and/or update the URLs. Please check to make sure that your uploads are in the standard locations:
* ../wp-content/blogs.dir/ID for < 3.5
* ../wp-content/uploads/sites/ID for >= 3.5

== Screenshots ==

1. The NS Cloner in all its simple, user-friendly glory

== Changelog ==
= 2.1.4.1 =
* Fixed 2.1.4 to make file copies compatible with the new uploads structure in native WP 3.5 installs.
* ANNOUNCING NS Cloner Pro is now Available

= 2.1.4 =
* Fixed bug in 2.1.3 that caused file copies to fail in some cases where the target folders already existed

= 2.1.3 =
* Fixed bug in 2.1.2 that forced subdirectory mode

= 2.1.2 =
* Added Auto-detect of Multisite mode and Subdirectory site support!
* Added Automatic Copy of all media files in blogs.dir/##
* Fixed some image loading fails in certain scenarios

= 2.1.1 =
* First public release

== Upgrade Notice ==

= 2.1.1 =
First public release

= 2.1.3 =
Fixed bug in 2.1.2 that forced subdirectory mode - if you updated to 2.1.2 please update to 2.1.3 immediately.

= 2.1.4 =
* Fixed bug in 2.1.3 that caused file copies to fail in some cases where the target folders already existed. Update to correct the issue if affected.

= 2.1.4.1 =
* Fixed 2.1.4 to make file copies compatible with the new uploads structure in native WP 3.5 installs. This should correct issues with the media file copes! Please update ASAP.