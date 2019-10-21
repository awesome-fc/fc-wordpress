<?php
/**
 * This file contains the only one function wp_install().
 *
 * @package SQLite Integration
 * @author Kojima Toshiyasu, Justin Adie
 *
 */

if (!defined('ABSPATH')) {
	echo 'Thank you, but you are not allowed to access this file.';
	die();
}

/**
 * This function overrides wp_install() in wp-admin/includes/upgrade.php
 */
function wp_install($blog_title, $user_name, $user_email, $public, $deprecated = '', $user_password = '') {
	if (!empty($deprecated))
		_deprecated_argument(__FUNCTION__, '2.6');

	wp_check_mysql_version();
	wp_cache_flush();
	/* changes */
	require_once PDODIR . 'schema.php';
	make_db_sqlite();
	/* changes */
	populate_options();
	populate_roles();

	update_option('blogname', $blog_title);
	update_option('admin_email', $user_email);
	update_option('blog_public', $public);

	$guessurl = wp_guess_url();

	update_option('siteurl', $guessurl);

	if (!$public)
		update_option('default_pingback_flag', 0);

	$user_id        = username_exists($user_name);
	$user_password  = trim($user_password);
	$email_password = false;
	if (!$user_id && empty($user_password)) {
		$user_password = wp_generate_password(12, false);
		$message = __('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.');
		$user_id = wp_create_user($user_name, $user_password, $user_email);
		update_user_option($user_id, 'default_password_nag', true, true);
		$email_password = true;
	} else if (!$user_id) {
		$message = '<em>'.__('Your chosen password.').'</em>';
		$user_id = wp_create_user($user_name, $user_password, $user_email);
	}

	$user = new WP_User($user_id);
	$user->set_role('administrator');

	wp_install_defaults($user_id);

	flush_rewrite_rules();

	wp_new_blog_notification($blog_title, $guessurl, $user_id, ($email_password ? $user_password : __('The password you chose during the install.')));

	wp_cache_flush();

	if (isset($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false || isset($_SERVER['SERVER_SIGNATURE']) && stripos($_SERVER['SERVER_SIGNATURE'], 'apache') !== false) {
		;// Your server is Apache. Nothing to do more.
	} else {
		$server_message = sprintf('Your webserver doesn\'t seem to be Apache. So the database directory access restriction by the .htaccess file may not function. We strongly recommend that you should restrict the access to the directory %s in some other way.', FQDBDIR);
		echo '<div style="position: absolute; margin-top: 350px; width: 700px; border: .5px dashed rgb(0, 0, 0);"><p style="margin: 10px;">';
		echo $server_message;
		echo '</p></div>';
	}

	return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message);
}
?>