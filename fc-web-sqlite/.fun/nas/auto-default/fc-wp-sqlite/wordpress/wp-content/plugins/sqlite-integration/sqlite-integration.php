<?php
/*
Plugin Name: SQLite Integration
Plugin URI: http://dogwood.skr.jp/wordpress/sqlite-integration/
Description: SQLite Integration is the plugin that enables WordPress to use SQLite. If you don't have MySQL and want to build a WordPress website, it's for you.
Author: Kojima Toshiyasu
Version: 1.8.1
Author URI: http://dogwood.skr.jp
Text Domain: sqlite-integration
Domain Path: /languages
License: GPL2 or later
*/

/* Copyright 2013-2014 Kojima Toshiyasu (email: kjm@dogwood.skr.jp)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * This file defines global constants and defines SQLiteIntegration class.
 *
 * @package SQLite Integration
 * @author Kojima Toshiyasu
 */
if (!defined('ABSPATH')) {
	echo 'Thank you, but you are not allowed to access this file.';
	die();
}
/*
 * This will be activated after the installation is finished.
 * So you can use all the functionality of WordPress.
 */
$siteurl = get_option('siteurl');
/*
 * Defines basic constants.
 */
define('SQLITE_INTEGRATION_VERSION', '1.8.1');
define('SQLiteDir', dirname(plugin_basename(__FILE__)));
define('SQLiteFilePath', dirname(__FILE__));
define('SQLiteDirName', basename(SQLiteFilePath));
if (defined('WP_PLUGIN_URL')) {
	define('SQLiteUrl', WP_PLUGIN_URL . '/' . SQLiteDir);
} else {
	define('SQLiteUrl', $siteurl . '/wp-content/plugins/' . SQLiteDir);
}
/*
 * Defines patch file upload directory.
 */
if (defined('UPLOADS')) {
	define('SQLitePatchDir', UPLOADS . '/patches');
} else {
	if (defined('WP_CONTENT_DIR')) {
		define('SQLitePatchDir', WP_CONTENT_DIR . '/uploads/patches');
	} else {
		define('SQLitePatchDir', ABSPATH . 'wp-content/uploads/patches');
	}
}
/*
 * Plugin compatibility file in json format.
 */
define('SQLiteListFile', SQLiteFilePath . '/utilities/plugin_lists.json');
/*
 * Instantiates utility classes.
 */
if (!class_exists('SQLiteIntegrationUtils')) {
	require_once SQLiteFilePath . '/utilities/utility.php';
	$utils = new SQLiteIntegrationUtils();
}
if (!class_exists('SQLiteIntegrationDocument')) {
	require_once SQLiteFilePath . '/utilities/documentation.php';
	$doc = new SQLiteIntegrationDocument();
}
if (!class_exists('PatchUtils')) {
	require_once SQLiteFilePath . '/utilities/patch.php';
	$patch_utils = new PatchUtils();
}
if (!class_exists('DatabaseMaintenance')) {
	require_once SQLiteFilePath . '/utilities/database_maintenance.php';
	$maintenance = new DatabaseMaintenance();
}

/**
 * This class is for WordPress Administration Panel.
 *
 * This class and other utility classes don't affect the base functionality
 * of the plugin.
 *
 */
class SQLiteIntegration {
	/**
	 * Constructor.
	 *
	 * This constructor does everything needed for the administration panel.
	 *
	 * @param no parameter is provided.
	 */
	function __construct() {
		if (function_exists('register_activation_hook')) {
			register_activation_hook(__FILE__, array($this, 'install'));
		}
		if (function_exists('register_deactivation_hook')) {
			;
		}
		if (function_exists('register_uninstall_hook')) {
			register_uninstall_hook(__FILE__, array('SQLiteIntegration', 'uninstall'));
		}
		if (function_exists('is_multisite') && is_multisite()) {
			add_action('network_admin_menu', array($this, 'add_network_pages'));
			add_action('network_admin_notices', array('SQLiteIntegrationUtils', 'show_admin_notice'));
		} else {
			add_action('admin_menu', array($this, 'add_pages'));
			add_action('admin_notices', array('SQLiteIntegrationUtils', 'show_admin_notice'));
		}
		// See the docstring for download_backup_db() in utilities/utility.php
		// We need this registration process.
		add_action('admin_init', array('SQLiteIntegrationUtils', 'download_backup_db'));
		add_action('plugins_loaded', array($this, 'textdomain_init'));
	}

	/**
	 * Method to install on multisite or single site.
	 *
	 * There really is nothing to install for now. It is for future use...
	 *
	 * @param no parameter is provided.
	 * @return returns null.
	 */
	function install() {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			$old_blog = $wpdb->blogid;
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);
				$this->_install();
			}
			switch_to_blog($old_blog);
			return;
		} else {
			$this->_install();
			return;
		}
	}

	/**
	 * Method to install something.
	 *
	 * We have nothing to do for now.
	 * We show menu and documents only to the network administrator.
	 *
	 * @param no parameter is provided.
	 * @return no return values.
	 */
	function _install() {
	}

	/**
	 * Method to uninstall plugin.
	 *
	 * This will remove wp-content/db.php and wp-content/patches direcotry.
	 * If you migrate the site to the sever with MySQL, you have only to
	 * migrate the data in the database.
	 *
	 * @param no parameter is provided.
	 * @return no return values.
	 */
	function uninstall() {
		// remove patch files and patch directory
		if (file_exists(SQLitePatchDir) && is_dir(SQLitePatchDir)) {
			$dir_handle        = opendir(SQLitePatchDir);
			while (($file_name = readdir($dir_handle)) !== false) {
				if ($file_name != '.' && $file_name != '..') {
					unlink(SQLitePatchDir.'/'.$file_name);
				}
			}
			rmdir(SQLitePatchDir);
		}
		// remove wp-content/db.php
		if (defined('WP_CONTENT_DIR')) {
			$target = WP_CONTENT_DIR . 'db.php';
		} else {
			$target = ABSPATH . 'wp-content/db.php';
		}
		if (file_exists($target)) {
			unlink($target);
		}
	}

	/**
	 * Method to manipulate the admin panel, stylesheet and JavaScript.
	 *
	 * We use class method to show pages and want to load style files and script
	 * files only in our plugin documents, so we need add_submenu_page with parent
	 * slug set to null. This means that menu items are added but hidden from the
	 * users.
	 *
	 * @param no parameter is provided.
	 * @return no return values.
	 */
	function add_pages() {
		global $utils, $doc, $patch_utils, $maintenance;
		if (function_exists('add_options_page')) {
			$welcome_page     = add_options_page(__('SQLite Integration'), __('SQLite Integration'), 'manage_options', 'sqlite-integration', array($utils, 'welcome'));
			$util_page        = add_submenu_page(null, 'System Info', 'System Info', 'manage_options', 'sys-info', array($utils, 'show_utils'));
			$edit_db          = add_submenu_page(null, 'Setting File', 'Setting File', 'manage_options', 'setting-file', array($utils, 'edit_db_file'));
			$doc_page         = add_submenu_page(null, 'Documentation', 'Documentation', 'manage_options', 'doc', array($doc, 'show_doc'));
			$patch_page       = add_submenu_page(null, 'Patch Utility', 'Patch Utility', 'manage_options', 'patch', array($patch_utils, 'show_patch_page'));
			$maintenance_page = add_submenu_page(null, 'DB Maintenance', 'DB Maintenance', 'manage_options', 'maintenance', array($maintenance, 'show_maintenance_page'));
			add_action('admin_print_styles-'.$welcome_page, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$util_page, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$edit_db, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$doc_page, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$patch_page, array($this, 'add_style_sheet'));
			add_action('admin_print_scripts-'.$util_page, array($this, 'add_sqlite_script'));
			add_action('admin_print_scripts-'.$doc_page, array($this, 'add_sqlite_script'));
			add_action('admin_print_scripts-'.$patch_page, array($this, 'add_sqlite_script'));
			add_action('admin_print_scripts-'.$edit_db, array($this, 'add_sqlite_script'));
			add_action('admin_print_styles-'.$maintenance_page, array($this, 'add_style_sheet'));
		}
	}

	/**
	 * Method to manipulate network admin panel.
	 *
	 * Capability is set to manage_network_options.
	 *
	 * @param no parameter is provided.
	 * @return no return values.
	 */
	function add_network_pages() {
		global $utils, $doc, $patch_utils, $maintenance;
		if (function_exists('add_options_page')) {
			$welcome_page     = add_submenu_page('settings.php', __('SQLite Integration'), __('SQLite Integration'), 'manage_network_options', 'sqlite-integration', array($utils, 'welcome'));
			$util_page        = add_submenu_page(null, 'System Info', 'System Info', 'manage_network_options', 'sys-info', array($utils, 'show_utils'));
			$edit_db          = add_submenu_page(null, 'Setting File', 'Setting File', 'manage_network_options', 'setting-file', array($utils, 'edit_db_file'));
			$doc_page         = add_submenu_page(null, 'Documentation', 'Documentation', 'manage_network_options', 'doc', array($doc, 'show_doc'));
			$patch_page       = add_submenu_page(null, 'Patch Utility', 'Patch Utility', 'manage_network_options', 'patch', array($patch_utils, 'show_patch_page'));
			$maintenance_page = add_submenu_page(null, 'DB Maintenance', 'DB Maintenance', 'manage_network_options', 'maintenance', array($maintenance, 'show_maintenance_page'));
			add_action('admin_print_styles-'.$welcome_page, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$util_page, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$edit_db, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$doc_page, array($this, 'add_style_sheet'));
			add_action('admin_print_styles-'.$patch_page, array($this, 'add_style_sheet'));
			add_action('admin_print_scripts-'.$util_page, array($this, 'add_sqlite_script'));
			add_action('admin_print_scripts-'.$doc_page, array($this, 'add_sqlite_script'));
			add_action('admin_print_scripts-'.$patch_page, array($this, 'add_sqlite_script'));
			add_action('admin_print_scripts-'.$edit_db, array($this, 'add_sqlite_script'));
			add_action('admin_print_styles-'.$maintenance_page, array($this, 'add_style_sheet'));
		}
	}

	/**
	 * Method to initialize textdomain.
	 *
	 * Japanese catalog is only available.
	 *
	 * @param no parameter is provided.
	 * @return no return values.
	 */
	function textdomain_init() {
		global $utils;
		//$current_locale = get_locale();
		//if (!empty($current_locale)) {
		//  $moFile = dirname(__FILE__) . "/languages/sqlite-wordpress-" . $current_locale . ".mo";
		//  if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('sqlite-wordpress', $moFile);
		//}
		load_plugin_textdomain($utils->text_domain, false, SQLiteDir.'/languages/');
	}

	/**
	 * Method to initialize stylesheet on the admin panel.
	 *
	 * This determines which stylesheet to use depending on the users' choice
	 * of admin_color. Each stylesheet imports style.css and change the color
	 * of the admin dashboard.
	 *
	 * @param no parameter is provided.
	 * @return no return values.
	 */
	function add_style_sheet() {
		global $current_user;
		get_currentuserinfo();
		$admin_color = get_user_meta($current_user->ID, 'admin_color', true);
		if ($admin_color == 'fresh') {
			$stylesheet_file = 'style.min.css';
		} else {
		$stylesheet_file = $admin_color . '.min.css';
		}
		$style_url  = SQLiteUrl . '/styles/' . $stylesheet_file;
		$style_file = SQLiteFilePath . '/styles/' . $stylesheet_file;
		if (file_exists($style_file)) {
			wp_enqueue_style('sqlite_integration_stylesheet', $style_url);
		}
	}
	/**
	 * Method to register the JavaScript file.
	 *
	 * To register the JavaScript file. It's only for the admin dashboard.
	 * It won't included in web pages.
	 *
	 * @param no parameter is provided.
	 * @return no return value.
	 */
	function add_sqlite_script() {
		$script_url  = SQLiteUrl . '/js/sqlite.min.js';
		$script_file = SQLiteFilePath . '/js/sqlite.min.js';
		if (file_exists($script_file)) {
			wp_enqueue_script('sqlite-integration', $script_url, 'jquery');
		}
	}
}

/* this is enough for initialization */
new SQLiteIntegration;
?>