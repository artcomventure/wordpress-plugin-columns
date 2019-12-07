=== Editor Columns ===

Contributors:
Donate link:
Tags: Tinymce, Editor, Columns
Requires at least:
Tested up to:
Stable tag:
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Extends HTML Editor with WYSIWYG columns.

== Description ==

Extends the WordPress HTML editor with columns ... **_the WYSIWYG way_**.

Insert multiple column sets with up to 9 columns directly into the HTML editor.

* no shortcodes
* no extra templates
* _customizable_ **responsive**

== Installation ==

1. Upload files to the `/wp-content/plugins/` directory of your WordPress installation.
  - Either [download the latest files](https://github.com/artcomventure/wordpress-plugin-columns/archive/master.zip) and extract zip (optionally rename folder)
  - ... or clone repository:
  ```
  $ cd /PATH/TO/WORDPRESS/wp-content/plugins/
  $ git clone https://github.com/artcomventure/wordpress-plugin-columns.git
  ```
  If you want a different folder name than `wordpress-plugin-columns` extend clone command by ` 'FOLDERNAME'` (replace the word `'FOLDERNAME'` by your chosen one):
  ```
  $ git clone https://github.com/artcomventure/wordpress-plugin-columns.git 'FOLDERNAME'
  ```
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. **Enjoy**

== Usage ==

Once activated you'll find the columns button at the very left position of the first editor buttons row.

1. Select the text or position the cursor at the point where the columns should be inserted.
2. Click the columns button.
3. Choose number of columns.
4. Edit content.

To change the number columns of an existing set or to remove the columns set (only columns, not the content) just click somewhere in the columns and repeat from == Description ==

=== Settings ===

You can find the 'Editor columns' options page listed in the submenu of 'Settings'.

1. Define the number of columns which are available in the editor. _Default: 9_
2. Specify the gap between two columns. _Default: 1.5em_
3. Include default responsive CSS. _Default: false_
4. ... and define the @media breakpoints. _Default: calculated from `global $content_width;`_

== Plugin Updates ==

Although the plugin is not _yet_ listed on https://wordpress.org/plugins/, you can use WordPress' update functionality to keep it in sync with the files from [GitHub](https://github.com/artcomventure/wordpress-plugin-columns).

**Since 1.4.0 please use for this our [WordPress Repository Updater](https://github.com/artcomventure/wordpress-plugin-repoUpdater)** with the settings:

* Repository URL: https://github.com/artcomventure/wordpress-plugin-columns/
* Subfolder (optionally, if you don't want/need the development files in your environment): build

_We test our plugin through its paces, but we advise you to take all safety precautions before the update. Just in case of the unexpected._

== Questions, concerns, needs, suggestions? ==

Don't hesitate! [Issues](https://github.com/artcomventure/wordpress-plugin-columns/issues) welcome.

== Changelog ==

= 1.7.8 - 2019-12-07 =
**Changed**

* Add-paragraph UI (not likely to interfere with CSS anymore).

= 1.7.7 - 2019-10-16 =
**Fixed**

* Gallery override CSS.

= 1.7.6 - 2018-01-11 =
**Fixed**

* Column's box-sizing (CSS).

= 1.7.5 - 2017-10-16 =
**Fixed**

* Let empty paragraph be removed from div.columns if not the only child.

= 1.7.4 - 2017-10-12 =
**Added**

* Protect div.column from being removed on delete keys press.

**Changed**

* No backend js/css file minification.

= 1.7.3 - 2017-09-29 =
**Added**

* intermediate styles
* css for left justify columns

= 1.7.2 - 2017-09-26 =
**Fixed**

* Don't strip units if value is 0 on css minification.

= 1.7.1 - 2017-06-19 =
**Fixed**

* Tinymce gallery CSS.

= 1.7.0 - 2017-06-01 =
**Added**

* Option to apply CSS for galleries.

= 1.6.7 - 2017-04-27 =
**Fixed**

* Add missing css unit.

= 1.6.6 - 2017-04-20 =
**Fixed**

* Editor css.

= 1.6.5 - 2017-04-20 =
**Fixed**

* Faulty css.

= 1.6.4 - 2017-04-04 =
**Fixed**

* Responsive css.

= 1.6.3 - 2017-03-23 =
**Fixed**

* Editor css bugs.

= 1.6.2 - 2017-03-22 =
**Fixed**

* Typo :/

= 1.6.1 - 2017-03-22 =
**Changed**

* No margin top/bottom for column's first/last child.
* Regenerate css on new plugin version.

= 1.6.0 - 2017-03-21 =
**Added**

* Gap settings.
* Auto create/update columns.css with values from settings.

= 1.5.9 - 2017-03-15 =
**Fixed**

* Wrong gap calculation.
* Columns editor UI (add paragraph).

= 1.5.8 - 2017-03-08 =
**Fixed**

* Responsive css.

= 1.5.7 - 2017-02-27 =
**Fixed**

* Css' border-box (with padding) vs flex box.

= 1.5.6 - 2017-02-21 =
**Fixed**

* editos.js problem with fb:embeds.

= 1.5.5 - 2016-05-04 =
**Fixed**

* Resolve 404 file error in backend due to missing/fake js file.

= 1.5.4 - 2016-04-14 =
**Added**

* Setting/Option: available number of columns.

= 1.5.3 - 2016-04-12 =
**Fixed**

* Default breakpoints in type integer.

= 1.5.2 - 2016-04-12 =
**Changed**

* Manually @media query breakpoint settings.

= 1.5.1 - 2016-04-10 =
**Added**

* Column set specific auto responsive columns.

= 1.5.0 - 2016-04-09 =
**Added**

* Auto ($global_width) responsive columns.

= 1.4.4 - 2016-04-04 =
**Fixed**

* Fix columns selection in editor.

= 1.4.3 - 2016-04-01 =
**Fixed**

* Imrove/fix 'refresh' behaviour

= 1.4.2 - 2016-03-10 =
**Added**

* Changed 'Plugins' screen detail link

= 1.4.1 - 2016-03-10 =
**Added**

* build/

= 1.4.0 - 2016-03-10 =
**Removed**

* Plugin update from GitHub. **Please use https://github.com/artcomventure/wordpress-plugin-repoUpdater for this!**

= 1.3.2 - 2016-03-09 =
**Changed**

* Get master version: switch from curl to wp_remote_get()
* Use WP's update message

= 1.3.1 - 2016-03-08 =
**Fixed**

* Non git update message

= 1.3.0 - 2016-03-08 =
**Added**

* Update plugin files directly from github.com via WordPress' plugin updater
* style and script version number
* code comments

**Changed**

* README.md

= 1.2.3 - 2016-03-07 =
**Added**

* README.md

= 1.2.2 - 2016-03-05 =
**Changed**

* update check: always (but cache expired) use cached master version number

= 1.2.1 - 2016-03-05 =
**Changed**

* update notification text

**Fixed**

* range marker bug on columns removal

== 1.2.0 - 2016-03-04 [YANKED] ==
**Added**

* update notification from git

= 1.1.5 - 2016-03-04 =
**Added**

* CHANGELOG.md

== 1.1.4 - 2016-03-04 [YANKED] ==
**Fixed**

* js columns creation bug

= 1.1.2 - 2016-03-04 =
**Changed**

* better UX

= 1.1.1 - 2016-03-04 =
**Added**

* de_DE translation

= 1.1.0 - 2016-03-04 =
**Added**

* Gulp
* docblocks and comments

= 1.0.0 - 2016-03-03 =
**Added**

* Inititial file commit
