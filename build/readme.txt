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

== Plugin Updates ==

Although the plugin is not _yet_ listed on https://wordpress.org/plugins/, you can use WordPress' update functionality to keep it in sync with the files from [GitHub](https://github.com/artcomventure/wordpress-plugin-columns).

**Since 1.4.0 please use for this our [WordPress Repository Updater](https://github.com/artcomventure/wordpress-plugin-repoUpdater)** with the settings:

* Repository URL: https://github.com/artcomventure/wordpress-plugin-columns/
* Subfolder (optionally, if you don't want/need the development files in your environment): build

_We test our plugin through its paces, but we advise you to take all safety precautions before the update. Just in case of the unexpected._

== Questions, concerns, needs, suggestions? ==

Don't hesitate! [Issues](https://github.com/artcomventure/wordpress-plugin-columns/issues) welcome.

== Changelog ==

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
