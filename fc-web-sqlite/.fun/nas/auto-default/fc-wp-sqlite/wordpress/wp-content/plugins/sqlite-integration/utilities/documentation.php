<?php
/**
 * This file defines SQLiteIntegrationDocument class.
 *
 * @package SQLite Integration
 * @author Kojima Toshiyasu
 */
/**
 * This class defines the methods to display the documentation page on the admin panel.
 *
 * TODO: separate database access methods and display methods for maintenance
 */
class SQLiteIntegrationDocument {
	/**
	 * Method to display the document page.
	 *
	 */
	function show_doc() {
		// textdomain is defined in the utils class
		global $utils;
		$domain = $utils->text_domain;
		if (is_multisite() && !current_user_can('manage_network_options')) {
			die(__('You are not allowed to access this page!', $domain));
		} elseif (!current_user_can('manage_options')) {
			die(__('You are not allowed to access this page!', $domain));
		}
		if (isset($_GET['page']) && $_GET['page'] == 'doc') :?>
		<div class="navigation">
			<ul class="navi-menu">
				<li class="menu-selected"><?php _e('Documentation', $domain);?></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=sys-info"><?php _e('System Info', $domain); ?></a></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=setting-file"><?php _e('Miscellaneous', $domain);?></a></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=patch"><?php _e('Patch Utility', $domain);?></a></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=maintenance"><?php _e('Maintenance', $domain);?></a></li>
			</ul>
		</div>
		<div class="wrap" id="sqlite-admin-wrap">
		<h2><?php _e('Documentation', $domain); ?></h2>
		<p>
		<?php _e('This is a brief documentation about this plugin. For more details, see also the <a href="http://dogwood.skr.jp/wordpress/sqlite-integration/">SQLite Integration page</a>.', $domain);?>
		</p>
		<p>
		<?php _e('Please don\'t forget: WordPress DOES NOT OFFICIALLY SUPPORT any database other than MySQL. So if you ask about this plugin in the Forum, it\'s not unlikely that you won\'t get no answers at all.', $domain);?>
		</p>

		<h3><?php _e('Features', $domain);?></h3>
		<p>
		<?php _e('This plugin is a successor to <a href="http://wordpress.org/extend/plugins/pdo-for-wordpress/">PDO for WordPress</a>, which enabled WordPress to use SQLite for its database. But PDO for WordPress doesn\'t seem to be maintained any more only to be outdated. SQLite Integration makes use of the basic ideas and framework of PDO for WordPress, adds some new features and updates it to be able to work with the newest version of WordPress(3.8.1).', $domain); ?>
		</p>
		<p>
		<?php _e('<a href="http://www.sqlite.org/">SQLite Web Page</a> says &mdash; SQLite is a &quot;software library that implements selfcontained, serverless, zero-configuration, transactional SQL database engine&quot;. It is &quot;a good choice for small to medium size websites&quot;. It\'s small and portable, and you don\'t need any database server system.', $domain); ?>
		</p>
		<p>
		<?php _e('Unfortunately enough, WordPress only supports MySQL. Consequently it doesn\'t provide any APIs for SQLite. So if you want to create a website using WordPress without a database server, you\'ve got to write a kind of wrapper program yourself to use SQLite. This is the way SQLite Integration goes.', $domain);?>
		</p>
		<p>
		<?php _e('SQLite Integration does the work as follows:', $domain); ?>
		</p>
		<ol>
			<li><?php _e('Intercepts SQL statement for MySQL from WordPress', $domain); ?></li>
			<li><?php _e('Rewrites it as SQLite can execute', $domain); ?></li>
			<li><?php _e('Gives it to SQLite', $domain); ?></li>
			<li><?php _e('Gets the results from SQLite', $domain); ?></li>
			<li><?php _e('Format the results as MySQL returns, if necessary', $domain);?></li>
			<li><?php _e('Gives back the results to WordPress', $domain); ?></li>
		</ol>
		<p>
		<?php _e('WordPress doesn\'t know what has happened in the background and will be happy with it.', $domain);?>
		</p>

		<h3><?php _e('Limitations', $domain);?></h3>
		<p>
		<?php _e('SQLite Integration uses SQLite, so the limitations of SQLite is, as it is, those of SQLite Integration. MySQL is far from a simple SQL engine and has many extended features and functionalities. WordPress uses some of them. Among those are some SQLite doesn\'t implement. For those features that WordPress uses, I made them work with SQLite Integration. But for others that some plugins are using, SQLite Integration can\'t manipulate. So...', $domain); ?>
		</p>
		<ol>
			<li><strong><?php _e('There are some plugins that you can\'t use. No way around.<br />', $domain);?></strong>
			<?php _e('Some plugins can\'t be activated or work properly. See the &quot;Plugin Compatibility/Incompatibility&quot; section.', $domain);?></li>
			<li><strong><?php _e('There are some plugins that you can\'t use without rewriting some codes in them.<br />', $domain);?></strong>
			<?php echo sprintf(__('Some plugins do work fine if you rewrite MySQL functions. I made some patch files and <a href="%s?page=patch">Patch Utility</a>. See also the <a href="http://dogwood.skr.jp/wordpress/sqlite-integration/#plugin-compat">SQLite Integration Page</a> for more details.', $domain), $utils->show_parent());?></li>
		</ol>
		<p>
		<?php _e('And there may be other problems I overlooked. If you find malfunctionality, please let me know at the <a href="http://wordpress.org/support/plugin/sqlite-integration">Support Forum</a>.', $domain);?>
		</p>
		<h3><?php _e('User Defined Functions', $domain); ?></h3>
			<p>
			<?php _e('SQLite Integration replaces some functions of MySQL with the user defined functions built in PHP PDO library. But some of the functions are meaningless in SQLite database: e.g. get_lock() or release_lock(). When SQLite Integration meets those functions, it does nothing but prevent the error.', $domain); ?>
			</p>
			<p>
			<?php _e('If you want SQLite Integration to execute more functions, you can add the definition in the file sqlite-integration/functions.php (functions-5-2.php is for PHP 5.2 or lesser).', $domain);?>
			</p>

			<h3><?php _e('Database Administration and Maintenance', $domain);?></h3>
			<p>
			<?php _e('SQLite Integration doesn\'t contain database maintenace functionality, because there are some other free or proprietary softwares that give you such functionalities. For example, these are among free softwares:', $domain);?>
			</p>
			<ul class="in-body-list">
			  <li><a href="https://addons.mozilla.org/en-US/firefox/addon/sqlite-manager/">SQLite Manager Mozilla Addon</a>(<?php _e('my recommendation', $domain);?>)</li>
			  <li><a href="http://www.sqlitemanager.org/">SQLiteManager</a>(<?php _e('unfortunately seems not to maintained...', $domain); ?>)</li>
			</ul>
			<p>
			<?php _e('I\'m not sure if future release may have some of the database maintenance functionality.', $domain);?>
			</p>
		</div>


		<div class="wrap" id="sqlite-admin-side-wrap">
		<h2><?php _e('Plugin Compatibility/Incompatibility', $domain);?></h2>
			<p>
			<?php _e('WordPress without its plugins is a king without people. Of course, you need plugins, I know.', $domain);?>
			</p>
			<p>
			<?php echo sprintf(__('This is the list of the problematic plugins (far from complete). You can see informations about your installed plugins in the <a href="%s?page=sys-info">System Info</a> page. To see more details, please visit the <a href="http://dogwood.skr.jp/wordpress/sqlite-integration">Plugin Page</a>.', $domain), $utils->show_parent());?>
			</p>

			<table class="widefat page fixed" id="plugins-table">
				<thead>
				<tr>
					<th data-sort='{"key":"name"}' class="item"><?php _e('Plugins Name', $domain); ?></th>
					<th data-sort='{"key":"compat"}' class="compat"><?php _e('Compatibility', $domain); ?></th>
					<th data-sort='{"key":"reason"}' class="reason"><?php _e('Reasons', $domain);?></th>
				</tr>
				</thead>
				<tbody>
				<?php
					if (file_exists(SQLiteListFile)) :?>
					<?php
					$contents = file_get_contents(SQLiteListFile);
					$plugin_info_list = json_decode($contents);
					foreach ($plugin_info_list as $plugin_info) :?>
					<?php if (in_array($plugin_info->compat, array('No', 'Probably No', 'Needs Patch'))) :?>
					<tr data-table='{"name":"<?php echo $plugin_info->name;?>", "compat":"<?php echo $plugin_info->compat;?>", "reason":"<?php echo $plugin_info->reason;?>"}'>
						<td><?php echo $plugin_info->name;?></a></td>
						<?php if (stripos($plugin_info->compat, 'patch') !== false) :?>
						<td><a href="<?php echo $plugin_info->patch_url;?>"><?php _e('Needs Patch', $domain);?></a></td>
						<?php elseif (stripos($plugin_info->compat, 'probably no')) :?>
						<td><?php _e('Probably No', $domain);?></td>
						<?php else :?>
						<td><?php _e('No', $domain);?></td>
						<?php endif;?>
						<td><?php echo $plugin_info->reason;?></td>
					</tr>
					<?php endif;?>
				<?php endforeach;?>
				<?php endif;?>
				</tbody>
			</table>
		</div>
		<?php endif;
	}
}
?>