=== Knews Multilingual Newsletters ===
Contributors: creverter
Donate link: http://www.knewsplugin.com/multi-language/
Tags: newsletters, newsletter, multilanguage, automated newsletter, newsletter multilingual, wysiwyg newsletter editor, batch sending
Requires at least: 3.1
Tested up to: 3.5.0
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Finally, newsletters are multilingual, quick and professional.

== Description ==

Knews is a powerful multilingual plug-in that allows you to **build professional looking newsletters**, segment subscribers in different mailing lists as well as segment them by language all in a matter of minutes.

Includes a custom, unique **modular WYSIWYG** (What You See Is What You Get) editor. Based on templates, with no need to know HTML.

= Features =

* **Automated** newslettering creation and submit [(tutorial here)](http://www.knewsplugin.com/automated-newsletter-creation/).
* New **premium** template "Glossy Black", in our [(new shop)](http://www.knewsplugin.com/ps/en/).
* **Widget** for subscriber and surfing language capture, with Name and Surname optional fields.
* **Newsletter customization**: Name and Surname token replacement in submit time.
* Possibility of creating **your own templates** [(tutorial here)](http://www.knewsplugin.com/tutorial/).
* **Multilingual**: it recognizes the languages of the blog or website automatically; compatible with WPML and qTranslate.
* **Segmentation** of subscribers by language and in different mailing lists
* Support for **SMTP** sending [(tutorial here)](http://www.knewsplugin.com/configure-smtp-submits/).
* **Total control** of deferred sending, pause, start, end, logs, error reports and re-sending.
* Support for **CRON** [(tutorial here)](http://www.knewsplugin.com/configure-webserver-cron/) and Cron emulation by JavaScript.
* **Personalisation of all interaction messages** with users, in any installed language.
* **Multilingual back office**: English, Russian, Arabic, Spanish, German, French, Italian, Finnish, Dutch and Catalan.
* **Automated** subscription, cancellation and confirmation of subscribers. 
* Flexible, simple and intuitive **import wizard**: any order of columns and encoding will be correctly interpreted in a .CSV file.
* **Statistics**: Sign ups, unsubscriptions, newsletter submits, user clicks, user can't read click, etc.
* **Free and without limitations**.

More info here: [http://www.knewsplugin.com](http://www.knewsplugin.com/).

A WYSIWYG Editor Demo:
[youtube http://www.youtube.com/watch?v=axDO5ZIW-9s]

**Admin languages:**

* **NEW**: Russian - ru_RU (Thanks to Ivan Komarov. http://http://ivkom.ru )
* **NEW**: Dutch - nl_NL (Thanks to: Carl Rozema. http://www.hetsites.nl )
* English - en_US (Knews Team)
* French - fr_FR (thanks to: Ypsilon http://www.ypsilonet.com )
* German - de_DE (thanks to: Ypsilon http://www.ypsilonet.com )
* Italian - it_IT (thanks to: Ypsilon http://www.ypsilonet.com )
* Spanish - es_ES (Knews Team)
* Catalan - ca (Knews Team)
* Arabic - ar (thanks to: Hasan Yousef)
* Finnish - fi (thanks to: Esa Ratanen http://eccola.fi )
* Bosnian - sr_RS - about 60% translated (thanks to: Hasan Yousef)
* Croatian - hr - about 60% translated (thanks to: Hasan Yousef)
* Serbian - sr_RS - about 60% translated (thanks to: Hasan Yousef)

= Future release =

* Support for xili-language, polylang and transposh plugins.
* Continued improvement of the WYSIWYG editor.
* More templates.


== Installation ==

1. **Add the plug-in using wordpress admin, find or upload it, via website or FTP.**

2. **Activate it.**

3. **Go to the Knews configuration page and configure:** Sender: Name and e-mail will appear as those of the submitted newsletters.

4. **You can also optionally configure:**

5. a) **Multilingual**: Knews works as monolingual by default, but it can recognise the languages defined in WPML or qTranslate if you choose. 

6. b) **CRON**: By default Knews works with wp_cron, but it can be changed (highly recommendable for websites with low traffic). [(tutorial here)](http://www.knewsplugin.com/configure-webserver-cron/).

7. c) **SMTP** sending: by default Knews sends by wp_mail (). You will have more features and fewer newsletters ending up as spam if you configure data sending using SMTP. [(tutorial here)](http://www.knewsplugin.com/configure-smtp-submits/)

8. **As an option, you can create different mailing lists: open to all registered wordpress users and/or segmented by language.**

9. **You can optionally modify all the texts and dialogues in all the installed languages.**

10. **To place the subscription form, you have the following options:**

11. a) Drag the Knews **widget** to the sidebar.

12. b) Put the following **shortcode** on any page or post: [knews_form]. **NEW:** Now you can specify a mailing list id, name, surname and/or stylize it: [knews_form id=1 name=ask surname=required stylize=1]

13. c) **Write** in your theme: `<?php echo knews_plugin_form(); ?>`. **NEW:** Or: `<?php echo knews_plugin_form( array('id'=>1, 'name'=>'ask', 'surname'=>'required', 'stylize'=>1) ); ?>`

14. **If you already have subscribers in some other system or e-mail programme, save them as CSV files: with the import wizard everything will be simple and intuitive.**

15. You can configure the automated newsletters feature: [(tutorial here)] (http://www.knewsplugin.com/automated-newsletter-creation/).

16. You can customize your email/newsletter with name and surname users values, write: {%name%[Dear user]} {%surname%[]} in any place of newsletter, the tokens %name% and %surname% will be replaced by the user fields in submit time. If there are empty, then will be replaced by the default values (between []).


== Frequently Asked Questions ==

** How can I use the tokens to customize the e-mails? **

In the email/newsletter, write: {%name%[Dear user]} {%surname%[]} in any place, the tokens %name% and %surname% will be replaced by the user fields in submit time. If there are empty, then will be replaced by the default values (between []).

**Can someone with no knowledge of HTML create a newsletter and send it to their clients?**

Yes, absolutely. The newsletter editor is WYSIWYG and modular. It is not necessary to have any knowledge of HTML and in a matter of minutes you can make a professional looking newsletter that will be seen correctly by all devices and e-mail programmes. 

**What is special about Knews editor?**

Knews editor is unique. It takes advantage of HTML5 properties to create a really unique WYSIWYG editor. You can drag modules to build the newsletter as large as you have things to say, load content of the posts or enter new ones, upload images or use those of the multimedia library, change colours, fonts, images and much more...

**The widget and shorcode of knews don't work, I can't see the subscription form.**

If there is not any mailing list opened, the widget and the form doesn't prints... maybe this the problem?
Go to Knews > Mailing lists in the WP admin menu, and check if at least one mailing list is defined and opened for everyone and for the registered users.
If you are running a multilanguage WP, check if at least one mailing list are opened for every lang in the same admin page.

**Knews is not translated into my language**

Knews allows you to easily modify all the interactive texts for the website visitor (subscriptions, cancellations, etc.). In your case, you will begin with texts in English. There are 20 sentences in all. If you want to collaborate and translate the whole of Knews into your own language, you will find the necessary files in directories/languages. Contact us and help us make Knews great! 

**How do I install additional languages in Knews?**

You don't have to. If you have configured Knews to work with WPML or qTranslate, when you configure a new language in these plug-ins, Knews will already be ready. 

**Why do you recommend configuring CRON, when wp_cron already works in wordpress without doing anything?**

Knews works initially with wp_cron that is based on running the tasks assigned from time to time. Now, if a blog or website doesn't receive many visits, it won't be reliable, because wp_cron has not been run if there are no website visits, so, start sends and the rate at which they are sent depends on the visits to the website. [(tutorial here)](http://www.knewsplugin.com/configure-webserver-cron/).

**Knews have statistics?**

Since version 1.1.0, Knews has statistics. This include: sign ups, unsubscriptions, newsletter submits, user clicks, user can't read click, etc.

**I am a designer and I need to give my clients a customised template**

[Here you have a tutorial](http://www.knewsplugin.com/tutorial/) to create a 100% personalised template. You will be able to define which areas are editable and which are not (images, colours, texts, links, etc.), and preview the different types of information to display, creating different modules for the newsletter editor. 

**Why do you recommend configuring an SMTP account?**

If sending is done by SMTP, the amount of e-mails reported as SPAM will drop. Knews sends the e-mails one by one by SMTP as you would, therefore guaranteeing a high rate of assured sending. [(tutorial here)](http://www.knewsplugin.com/configure-smtp-submits/).

**My newsletter must go out today, CRON doesn't work and I don't have enough entries in the website.**

No problem. Simply choose the option to use Cron emulation in JavaScript and send normally. You will have to keep a window open until the sending ends that's all.

**Does Knews only have 4 templates?**

At the moment Knews only has 3 free templates. Just we launched our first premium template "Glossy Black", in our [(new shop)](http://www.knewsplugin.com/ps/en/).
In any case, the degree of personalisation of our templates is immense, with thousands of different combinations available. You can also follow our tutorial to modify a template or create a new custom one for yourself.

== Screenshots ==

1. Personalisation of all interaction messages in all languages.
2. Segmentation of subscribers by language in different mailing lists.
3. The WYSIWYG Editor doing module insertion (doing drag).
4. The submit process.
5. Statistics: Sign ups, unsubscriptions, newsletter submits, user clicks, user can't read click, etc.
6. The Clean Blue Template and a sample customisation (Sports Car Magazine).
7. The Sweet Barcelona Template and a sample customization (Wine).
8. The Casablanca Template and a sample customisation (Christmas).
9. The Glossy Black Template and a sample customisation (White Background).

== Changelog ==

= 1.3.0 =

* Solved a bug while saving the newsletter in the WYSIWYG editor

= 1.2.9 =

* Russian language added (Thanks to Ivan Komarov. http://http://ivkom.ru ).
* Google + icon added in the newsletter templates.
* Added IsSendmail() in mailserver connection options (before only IsSMTP(), 1&1 webservers needs this method ).
* Added alerts if all the mailing lists are closed (subscribe widget doesn't shown).
* Fixed image resize issue under WP 3.5v (first time image resize / change).
* Solved broken links can't read and unsubscribe for users not subscribed (submits through manual submit).
* Some unstranslated strings solved (some strings added in latest releases).
* Solved issue while saving in the editor (broken ajax communication).
* Now the_excerpt gets the real post excerpt if there is one.
* Subscription form back (if something goes wrong) now doesn't scroll up when doing click.
* Right headers for stats images (some users can't see graphics stats).

= 1.2.8 =

* Error message "Knews cant update. Please, check user database permisions. (You must allow ALTER TABLE)." solved. We're Sorry!!!
* Subscription form back (if something goes wrong) without reload NOW WORKS.

= 1.2.7 =

* Dutch language added (Thanks to Carl Rozema. http://www.hetsites.nl )
* Title added in the newsletter page after user clicks can't read link
* User administration improvements: search for name and surname and order by any field
* Subscription form back (if something goes wrong) without reload
* Special Class added to the subscription form button for easy CSS customisation
* Now Knews uses the WordPress PHPMailer built-in library, less conflicts with another plugins
* Plugin upgrade checks for database alter table permision before upgrade
* FIXES:
* Admin prefs checkboxes (automated options and compatibility options) changes aren't saved into 1.2.6 version, fixed.
* Eternal welcome to the 1.2.6 version bug solved
* Font Picker (selector) blank window bug solved
* Select page for insertion 404 error solved
* Magic quotes gpc activated proof (some webservers has this activated)
* Fifth step importation bug solved (rare issue)
* JS-Cron step #2 404 error solved

= 1.2.6 =

* TESTED IN WP 3.5
* DEEP CHANGES IN SECURITY: XSS & CSRF attaks prevention
* AUTOMATION IMPROVEMENTS:
* Added button to force automate script start with debugging info
* Now the imported posts gets the "include posts for automation by default" preference
* Autosave posts deactivating automation user preference solved
* Automate posts by creation/update date preference added
* FIXES:
* Pagination added into select post popup (preventing memory overflow for much posts)
* Long lang code support added (chinese and portuguese)
* First character ? in newsletters fixed (rare, only a few webservers issue)
* Import users bug fixed
* PHP set_time_limit warning solved

= 1.2.5 =

* Added automated config option: older edited posts should be included on automation (on/off)
* Added subscribe config option: Antispam bot check (on/off) (Subscribe always fails "wrong e-mail adress" message issue in some Cache systems)
* Fatal error in admin newsletter page fixed
* Added 250 and 500 users per iteration submit (only for high performance SMTP)
* Fixed issue in the text email version (Thanks to Ernscht)
* Fixed issue in special chars email validation (Thanks to Ernscht)
* Added widget format (label/input position)
* Hidden Knews in admin menu fixed
* Fixed mailto in links on newsletters

= 1.2.4 =

* Solved double subscription bug into windows webservers (definitely, we swear)
* Solved bug in the scheduling of autocreation task
* Added JS-CRON force button in submits screen (look at the bottom list)
* Added support for NextGen Gallery images insertion in the WYSIWYG editor
* Added apply_filters['the_content'] deactivation to avoid post insertion issues with another plugins like NextGen Gallery and others

= 1.2.3 =

* Solved htaccess bug in newsletter images (bug in 1.2.2 version for some webservers)
* Double subscription bug fixed
* Fixed broken HTML newsletter submit when includes bad URLs (like <a href="">)
* Added mailing lists order field (for selector order in subscription form)
* Bad URL tutorials fixed
* Solved JavaScript post content insertion into newsletter bug (from plugins like add Link to Facebook)

= 1.2.2 =

* Solved SQL bug ('last_run' missing field) in knews 1.2.0 /1.2.1 clean installations (in Auto-creation admin page)
* Solved a token replacement bug about name/surname fields in submitted newsletters.
* Now you can specify a mailing list id, name, surname and/or stylize it in shortcode and theme  knews_plugin_form() call (see installation for details).
* Solved statistics bug: now shows the Submits OK/Error graph and calculates right the clicks percentages
* The custom templates can be uploaded to /wp-content/uploads/knewstemplates/ in order to preserve it when you updating the plugin.
* New premium template "Glossy Black", in our [(new shop)](http://www.knewsplugin.com/ps/en/).

= 1.2.1 =

* SOLVED IMPORTANT BUG!!! Cron submits never be submitted in 1.2.0 version.
* Solved two bugs in the can't read page: Now replaces the tokens (Name and Surname) and the unsubscribe link now works. Thanks to IanFox.
* The e-mail now is url encoded into user confirmation, unsubscribe and can't read email. Emails like abc+def@test.com works now. Thanks to Ernscht.

= 1.2.0 =

* Added Name and Surname fields, in the subscription form and token replacement in the newsletter submit.
* Automated creation and submit newslettering.
* Deep change in the Ajax way: from old-school method to standard WordPress method: This will solve some incompatibility issues with other plugins in the WYSIWYG editor and other Ajax Calls.

= 1.1.5 =

* NEW: Bosnian added - sr_RS - about 60% translated (thanks to: Hasan Yousef)
* WOW: CSS and images on/off preview in the WYSIWYG editor.
* Post selection for insertion in the newsletter editor enharcements:
* - in the right language by default.
* - with the_content filters from theme/plugins in %the_content% replacement
* - posts without title can be selected. Thanks to Hasan Yousef
* Add subscriptor function added: $Knews_plugin->add_user(email, id_list_news, lang, lang_locale) e.g. $Knews_plugin->add_user('mail@domain.com', 1, 'en', 'en_US'). Thanks to @drskullster.
* Cross-domain bug solved in ajax subscription when WPML differents domains per language option activated. Thanks to Matthieu Huguet
* Import bug solved when submit confirmation is activated. Thanks to @berardini.
* Cache bug in the WYSIWYG editor after saving for some users solved by adding random param. Thanks to Miran Peterman

* Stats bug solved (function name incompatibility with other themes/plugins). Thanks to @ashishsehgal.
* The extrange forbidden error while saving the newsletter solved. Thanks to Hasan Yousef

* A casablanca template background
 image bug solved.

= 1.1.4 =

* Fixed an Stupid bug in the widget that breaks the sidebar... we apologize this!!!

= 1.1.3 =

* NEW: Croatian added - hr - about 60% translated (thanks to: Hasan Yousef)
* NEW: Serbian added - sr_RS - about 60% translated (thanks to: Hasan Yousef)
* Custom templates folder. Upload your custom templates to /wp-content/uploads/knewstemplates and plugin uploads don't erase it.
* FIXED AUTOMATED SUBMIT BUG IN WP CRON configs (some users can't submit newsletters, the submit process stopped at 0% forever). Thanks to: Tudor
* Fixed issue with the JavaScript CRON Emulation (previous versions marks JS-Cron as a cronjob server input)
* Fixed exact size upload error in the WYSIWYG editor (when no resize is needed). Thanks to: Hans-Heinz Bieling
* Fixed the selection post issue in the WYSIWYG Knews Editor (some qTranslate configurations can't switch between languages). Thanks to: Acorderob
* Added mailing list ID as optional param in the knews shortcode. Thanks to: Luis Briso de Montiano Aldecoa
* Fixed javascript bug when 2 or more knews subscription form cohexists in one page (sidebar + shortcode or more than one shortcode). Thanks to: Luis Briso
* Fixed the drag and drop modules issue in older created newsletters (from knews versions 1.0.0 to 1.0.5). Thanks to: Xavier Goula
* Easy color change and delete of links in the newsletter editor. Thanks to Hans-Heinz Bieling
* Fixed the position of the false comments textarea in the subscription form (to avoid spam bots). Thanks to: Hasan Yousef
* DOING_AJAX constant added in ajax pages for WP_DEBUG activated configs. (See http://wordpress.stackexchange.com/questions/13509/how-to-override-wp-debug-for-ajax-responses)

= 1.1.2 =

* Fixed automated submit bug (some users can't submit newsletters, the submit process stopped at 0% forever). Thanks to webken.
* Added import option that allow add mailing lists to old subscriptors trough new CSV. Thanks to: Luis Briso de Montiano Aldecoa.
* Fixed minor translation bug in Arabic language. Thanks to: Hasan Yousef.
* Updated dashboard advices system.
* Fixed a path bug link to the JavaScript CRON Emulation file.
* Fixed the selection post issue in the WYSIWYG Knews Editor (some WPML configurations can't switch between languages). thanks to: Hasan Yousef.
* Fixed an undo image URL change issue in Editor.
* Fixed the too small image message bug in Editor: Before, show an incorrect permissions error message.
* Link to videotutorial in the editor added.

= 1.1.1 =

* VERY IMPORTANT: A SECURITY UPDATE *

Solved a Cross-Site Scripting Vulnerability (XSS) in the file: knews/wysiwyg/fontpicker/index.php
(Technical info here http://www.securelist.com/en/advisories/49825)

* Anti-spam bots hidden inputs was added to registration widget / form (thanks to Hans-Heinz Bieling)
* Anti-spam bots hidden inputs was added to registration widget / form (thanks to Hans-Heinz Bieling)

= 1.1.0 =

* WORDPRESS 3.4 COMPATIBLE
* NEW: MULTISITE SUPPORT
* NEW: STATISTICS
* TEMPLATES ARE MULTILANGUAGE
* ALL THE TEMPLATES HAS SOCIAL BUTTONS
* RIGHT TO LEFT LANGUAGES SUPPORT
* WYSIWYG IMPROVEMENTS:
* wizard dialog for social buttons, to easily insert the URLs and hide undesired icons.
* Image oversize limitation (non breaking layout)
* Image attributes edition (alternate text, links, border, vspace and hspace properties)
* The horizontal insertion spaces between modules are hidden in edition time, only appears during dragging module
* There are a zoom view in the editor
* The editor has now an insert image button at the cursor position
* SMTP / CRON TUTORIALS ADDED
* -- languages --
* NEW: Arabic added - ar (thanks to: Hasan Yousef)
* NEW: Finnish added - fi (thanks to: Eccola http://eccola.fi )
* -- fixes --
* Save newsletter error re-fixed (thanks to Javier)
* User and password SMTP fields autocomplete issue solved (thanks to Esa Rantanen and Thorsten Wollenhöfer)
* WP_DEBUG alerts breaking ajax actions and WYSIWYG editor (from current theme or another plugins error messages) fixed
* Wordpress core files inside directory broken URLs fixed (different WP address and Site address) (thanks to Manuel Burak and André Hilhorst)


= 1.0.5 =

* Support for image selection in Multisite WordPress sites (thanks to Esa Rantanen)
* WP-CRON interactions with other plugins fixed (thanks to Thorsten Wollenhöfer)
* Save newsletter error fixed (thanks to Javier)
* Image selection blank dialog fixed (thanks to Javier)

= 1.0.4 =

* URGENT: Preview and can't read newsletters bug fixed (thanks to Esa Rantanen)
* Image resize bugs fixed (thanks to Esa Rantanen)

= 1.0.3 =

* MAJOR BUGS Fixed in Windows webservers (thanks to Hans-Heinz Bieling)
* Resolved WYSIWYG editor issues in Macintosh Chrome (thanks to Max Schanfarber)
* Minor bug in modal window after subscription on twenty elevens theme (thanks to Esa Rantanen)
* Fixed customised messages bug (thanks to Hans-Heinz Bieling)

= 1.0.2 =

* WYSIWYG improvements:
* Solved change image bug when no link is provided (thanks to Alfredo Pradanos)
* Now you can resize template images in situ, with re-sharp and undo buttons (click on images)

= 1.0.1 =

* Template Casablanca improvements: background and layout issues with Gmail solved
* Duplication of newsletters option added (not necessary start from scratch every newsletter)

= 1.0.0 =

* Habemus Plugin!!!

== Upgrade Notice ==

= 1.3.0 =

* Solved a bug while saving the newsletter in the WYSIWYG editor

= 1.2.9 =

* Russian language added (Thanks to Ivan Komarov. http://http://ivkom.ru ).
* Google + icon added in the newsletter templates.
* Added IsSendmail() in mailserver connection options (before only IsSMTP(), 1&1 webservers needs this method ).
* Added alerts if all the mailing lists are closed (subscribe widget doesn't shown).
* Fixed image resize issue under WP 3.5v (first time image resize / change).
* Solved broken links can't read and unsubscribe for users not subscribed (submits through manual submit).
* Some unstranslated strings solved (some strings added in latest releases).
* Solved issue while saving in the editor (broken ajax communication).
* Now the_excerpt gets the real post excerpt if there is one.
* Subscription form back (if something goes wrong) now doesn't scroll up when doing click.
* Right headers for stats images (some users can't see graphics stats).

= 1.2.8 =

* Error message "Knews cant update. Please, check user database permisions. (You must allow ALTER TABLE)." solved. We're Sorry!!!
* Subscription form back (if something goes wrong) without reload NOW WORKS.

= 1.2.7 =

* Dutch language added (Thanks to Carl Rozema. http://www.hetsites.nl )
* Title added in the newsletter page after user clicks can't read link
* User administration improvements: search for name and surname and order by any field
* Subscription form back (if something goes wrong) without reload
* Special Class added to the subscription form button for easy CSS customisation
* Now Knews uses the WordPress PHPMailer built-in library, less conflicts with another plugins
* Plugin upgrade checks for database alter table permision before upgrade
* FIXES:
* Admin prefs checkboxes (automated options and compatibility options) changes aren't saved into 1.2.6 version, fixed.
* Eternal welcome to the 1.2.6 version bug solved
* Font Picker (selector) blank window bug solved
* Select page for insertion 404 error solved
* Magic quotes gpc activated proof (some webservers has this activated)
* Fifth step importation bug solved (rare issue)
* JS-Cron step #2 404 error solved

