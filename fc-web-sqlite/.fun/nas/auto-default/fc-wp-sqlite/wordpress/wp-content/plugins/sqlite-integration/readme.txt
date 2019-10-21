=== SQLite Integration ===
Contributors: kjmtsh
Plugin Name: SQLite Integration
Plugin URI: http://dogwood.skr.jp/wordpress/sqlite-integration/
Tags: database, SQLite, PDO
Author: Kojima Toshiyasu
Author URI: http://dogwood.skr.jp/
Requires at least: 3.3
Tested up to: 4.1.1
Stable tag: 1.8.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SQLite Integration is the plugin that enables WordPress to use SQLite. If you want to build a WordPress website with it, this plugin is for you.

== Description ==

This plugin enables you to create WordPress based web sites without MySQL database server. All you've got to prepare is the Apache web server or the like and PHP with PDO extension. WordPress archive and this plugin in hand, you can build a WordPress web site out of the box.

SQLite Integration is a successor to [PDO for WordPress](http://wordpress.org/extend/plugins/pdo-for-wordpress) plugin, which unfortunately enough, doesn't seem to be maintained any more. SQLite Integration uses the basic idea and structures of that plugin and adds some utilities with more features.

= Features =

SQLite Integration is a database access engine program, which means it's not like the other plugins. It must be used to install WordPress. Please read the install section. And see more detailed instruction in the [SQLite Integration Page](http://dogwood.skr.jp/wordpress/sqlite-integration/).

Once you succeed in installing WordPress, you can use it just like the other systems using MySQL. Optionally, this plugin provides the feature to temporarily change the database to MySQL and come back to SQLite, which may help developers test their sites on the local machines without MySQL.

After you finish installing, you can activate this plugin (this is optional but I recommend you to). And you can see some instructions and useful information on your server or your installed plugins.

= System Requirements =

* PHP 5.2 or newer with PDO extension (PHP 5.3 or newer is better).
* PDO SQLite driver must be loaded.

= Backward Compatibility =

If you are using 'PDO for WordPress', you can migrate your database to this plugin. Please check the install section.

= Support =

Please contact us with the methods below:

1. Post to [Support Forum](http://wordpress.org/support/plugin/sqlite-integration/).
2. Visit the [SQLite Integration Page](http://dogwood.skr.jp/wordpress/sqlite-integration/)(in English) or [SQLite Integration(ja) Page](http://dogwood.skr.jp/wordpress/sqlite-integration-ja/)(in Japanese) and leave a message there.

= Notes about Support =

WordPress.org doesn't officially support using any other database than MySQL. So you will have no supports from WordPress.org. Even if you post to the general Forum, you'll have few chances to get the answer. And if you use patched plugins, you will have no support from the plugin author(s), eithter. I will help you as much as I can, but take your own risk, please.

= Translation =

Documentation is written in English. If you translate it into your language, please let me know.

* Japanese (kjmtsh)
* Spanish (Pablo Laguna)

== Installation ==

For more detailed instruction, please visit [SQLite Integration](http://dogwood.skr.jp/wordpress/sqlite-integration/).

= Preparation =

1. Download the latest WordPress archive and this plugin. And expand them on your machine.
2. Move sqlite-integration folder to wordpress/wp-content/plugins folder.
3. Copy db.php file in sqlite-integratin folder to wordpress/wp-content folder.
4. Rename wordpress/wp-config-sample.php to wordpress/wp-config.php.

= Basic settings =

Open wp-config.php and edit the section below:

* Authentication Unique keys and Salts
* WordPress Database Table prefix
* WordPress Localized Language

See also [Editing wp-config.php](http://codex.wordpress.org/Editing_wp-config.php) in the Codex. Note that you don't have to write your database server, user name, user password or etc...

= Less than 5 minutes installation =

Upload everything (keeping the directory structure) to your server and access the wp-admin/install.php with your favorite browser, and WordPress installation process will begin. Enjoy your blogging!

= Optional settings =

You can change some default settings with the directives in wp-config.php.If you change the SQLite database file name (default is .ht.sqlite) to others, add the next line in your wp-config.php.

`define('DB_FILE', 'your_database_name');`

If you change the directory where the SQLite database is put, add the next line in your wp-config.php.

`define('DB_DIR', '/home/youraccount/database_directory/');`

You can change either of them or both of them.

= Use MySQL without uninstalling this plugins =

If you want to use MySQL, add the next line in your wp-config.php.

`define('USE_MYSQL', true);`

Of course, this is not enough. You must give your database server address, user name, passowrd or etc... in the same file. After you add that line and access your web site for the first time, WordPress installer will begin. Then you must finish setting MySQL database. As you know, data in the SQLite database is not automatically migrated to MySQL.

If you want to use SQLite again, change the line in wp-config.php as below or just remove this line.

`define('USE_MYSQL', false);`

= For PDO for WordPress users =

If you are using PDO for WordPress now, you can migrate your database to SQLite Integration. I recommend the way below. See more detailed instruction [SQLite Integration](http://dogwood.skr.jp/wordpress/sqlite-integration/).

1. Export your data from current database.
2. Install latest WordPress with SQLite Integration.
3. Import the old data.

If export or import fails for some reason, please visit our site and try another way described there.

== Frequently Asked Questions ==

= Install stops with 'Error establishing a database connection' =

It is required that you should prepare wp-config.php manually. If you try to make WordPress create wp-config.php, you'll get that message and can't continue install process.

= Database file is not created =

The reason of failure in creating directory or files is often that PHP is not allowed to craete them. Please check your server setting or ask the administrator.

= Such and such plugins can't be activated or doesn't seem to work properly =

Some of the plugins, especially cache plugins or database maintenace plugins, are not compatible with this plugin. Please activate SQLite Integration and see the known limitations section in this document or visit the [SQLite Integration](http://dogwood.skr.jp/wordpress/sqlite-integration/) for more detailed information.

= I don't want the admin menu and documentation =

Just deactivate the plugin, and you can remove them. Activation and deactivation affect only admin menu. If you want to remove all the plugin files, just delete it.

== Screenshots ==

1. System Information tells you your database status and installed plugins compatibility.

== Known Limitations ==

Many of the other plugins will work fine with this plugin. But there are some you can't use. Generally speaking, the plugins that manipulate database not with WordPress' APIs but with MySQL or MySQLi native drivers from PHP might cause the problem.

These are some examples:

You can't use these plugins because they create the same file that this plugin uses.

* W3 Total Cache
* DB Cache Reloaded Fix
* HyperDB

You may be able to use 'WP Super Cache' or 'Quick Cache' instead of them. I don't mean to recommend them and give no warranty at all.

You can't use these plugins, because they are using MySQL specific features that SQLite Integration can't emulate.

* Yet Another Related Posts
* Better Related Posts

You may be able to use 'WordPress Related Posts' or 'Related Posts' instead of them. Probably there are more, I'm afraid. If you find one, please let me know.

There are some among the incompatible plugins, which work fine by rewriting some codes. I give information about them and provide the patch files on [Plugins](http://dogwood.skr.jp/wordpress/plugins/).

This plugin doesn't support 'WP_PLUGIN_URL' constant.

== Upgrade Notice ==

WordPress 4.1.1 compatibility is checked and some bugs are fixed. Upgrade is recommended. When auto upgrading fails, please try manual upgrade via FTP.

== Changelog ==

See also ChangeLog file contained in the archive.

= 1.8 (2015-03-06) =
* Fixed the bug about install process algorithm.
* Fixed the index query regexp, which may cause a problem to some plugins.
* Solved PHP 5.2.x compatibility issue.

= 1.7 (2014-09-05) =
* Fixed the bug about changing the order of the attachment file in the editor screen.
* Fixed the bug about the manipulation of CREATE query.
* Added an 128x128 icon and 256x256 icon.
* Change for checking the user defined value of pcre.backtrack_limit and using it.
* WordPress 4.0 compatibilitiy checked.

= 1.6.3 (2014-05-10) =
* Fixed the bug about manipulating meta query with BETWEEN comparison.
* Added the Spanish langugae support.
* WordPress 3.9.1 compatibility checked.

= 1.6.2 (2014-05-05) =
* Fixed some bugs for the regular expression.
* Fixed the documents on the admin dashboard.

= 1.6.1 (2014-04-22) =
* Fixed some bugs for using with WP Slimstat plugin.
* Display admin notice when not replacing the old db.php with the new one (when necessary).
* Add the feature for replacing the old db.php file with the button click.
* Fixed the Japanese translation catalog file.

= 1.6 (2014-04-17) =
* Fixed the bug of error messaging control for the unknown query.
* Fixed the bug for 'SQL_CALC_FOUND_ROW' statement. This is for the main query, WP_Query class and WP_Meta_Query concerning paging information.
* Fixed the bug that the back quote in the comments was removed.
* Added the feature to download a backup file to a local machine.
* Revised all the doc strings in the sourcse code for PHP documentor.
* Changed the documentation.
* Fixed minor bugs and typos.
* Tested to install WordPress 3.8.2 and 3.9 beta.
* Augumented the plugin compatibility list.
* Some functions in wp-db.php, which was disabled by the plugin, is enabled and can be used.
* Some more user defined functions are added.

= 1.5 (2013-12-17) =
* Tested WordPress 3.8 installation and compatibility.
* Add the optional feature to change the database from SQLite to MySQL.
* Changed the install instruction in the readme.txt.
* Add the code to check if the SQLite library was compiled with the option 'ENABLE_UPDATE_DELETE_LIMIT'.
* Changed the admin panel style to fit for WordPress 3.8.
* Restricted the direct access to the files that works in the global namespace.

= 1.4.2 (2013-11-06) =
* Fixed some minor bugs about the information in the dashboard.
* Changed the screenshot.
* Tested WordPress 3.7.1 installation.

= 1.4.1 (2013-09-27) =
* Fixed the rewriting process of BETWEEN function. This is a critical bug. When your newly created post contains 'between A and B' phrase, it is not published and disappears.
* Fixed the admin dashboard display when using MP6.
* Fixed the Japanese catalog.
* Added the procedure for returning the dummy data when using SELECT version().
* Added the procedure for displaying column informatin of WordPress tables when WP_DEBUG enabled.

= 1.4 (2013-09-12) =
* Added the database maintenance utility for fixing the database malfunction of the upgraded WordPress installation.
* Changed the manipulation of SHOW INDEX query with WHERE clause.
* Fixed the bug of the manipulation of ALTER TABLE query.

= 1.3 (2013-09-04) =
* Added the backup utility that creates the zipped archive of the current snapshot of the database file.
* Changed the dashboard style to match MP6 plugin.
* Changed the way of putting out the error messages when language catalogs are not loaded.
* Modified the _rewrite_field_types() in query_create.class.php for the dbDelta() function to work properly.
* Added the support for BETWEEN statement.
* Changed the regular expression to remove all the index hints from the query string.
* Fixed the manipulation of ALTER TABLE CHANGE COLUMN query for NewStatPress plugin to work.
* Fixed minor bugs.

= 1.2.1 (2013-08-04) =
* Removed wpdb::real_escape property following the change of the wpdb.php file which makes the plugin compatible with Wordpress 3.6.

= 1.2 (2013-08-03) =
* Fixed the date string format and its quotation for calendar widget.
* Fixed the patch utility program for using on the Windows machine.
* Fixed the textdomain error in utilities/patch.php file when uploading the patch file.
* Changed the manipulation of the query with ON DUPLICATE KEY UPDATE.
* Fixed the typos in readme.txt and readme-ja.txt.

= 1.1 (2013-07-24) =
* Fixed the manipulation of DROP INDEX query.
* Removed destruct() from shutdown_hook.
* Enabled LOCATE() function in the query string.

= 1.0 (2013-07-07) =
* First release version of the plugin.
