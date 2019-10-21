<?php
/**
 * This file defines PatchUtils class.
 *
 * @package SQLite Integration
 * @author Kojima Toshiyasu
 */
/**
 * This class provides the methods for patching utilities.
 *
 */
class PatchUtils {
	/**
	 * Method to read the patch directory and returns the list of the files in it.
	 *
	 * It reads wp-content/uploads/patches directory and returns file names in it.
	 * If directory contains none, returns empty array.
	 *
	 * @return array
	 * @access private
	 */
	private function get_patch_files() {
		$patch_files = array();
		if (!is_dir(SQLitePatchDir)) {
			return $patch_files;
		} else {
			if ($dir_handle = opendir(SQLitePatchDir)) {
				while (($file_name = readdir($dir_handle)) !== false) {
					if ($file_name == '.' || $file_name == '..' || $file_name == '.htaccess')
						continue;
					$patch_files[] = $file_name;
				}
			}
			return $patch_files;
		}
	}
	/**
	 * Method to apply patch to the plugins.
	 *
	 * It executes patch command and apply it to the target plugins.
	 * If patch file(s) is not selected, returns false.
	 * Or else returns array contains messages.
	 *
	 * @return boolean|array
	 * @access private
	 */
	private function apply_patches() {
		global $utils;
		$domain = $utils->text_domain;
		$installed_plugins = array();
		$file_names = array();
		$output = array();
		$retval = 0;
		$patch_results = array();
		$message = '';
		if (isset($_POST['plugin_checked'])) {
			$file_names = $_POST['plugin_checked'];
		} else {
			return false;
		}
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			exec('where patch 2>&1', $out, $val);
		} else {
			exec('which patch 2>&1', $out, $val);
		}
		if ($val != 0) {
			$patch_results['error'] = __('Patch command is not found', $domain);
			return $patch_results;
		} elseif (!is_executable(trim($out[0]))) {
			$patch_results['error'] = __('Patch command is not executable', $domain);
			return $patch_results;
		} else {
			$patch_command = trim($out[0]) . ' -s -N -p1';
		}
		$installed_plugins = get_plugins();
		foreach ($file_names as $file) {
			if (preg_match('/_(.*)\.patch/i', $file, $match)) {
				$plugin_version = trim($match[1]);
				$plugin_basename = preg_replace('/_.*\.patch$/i', '', $file);
				foreach (array_keys($installed_plugins) as $key) {
					if (stripos($key, $plugin_basename) !== false) {
						$installed_plugin_version = $installed_plugins[$key]['Version'];
						break;
					}
				}
			} else {
				$patch_results['error'] = __('Patch file name is invalid', $domain);
				break;
			}
			if (version_compare($installed_plugin_version, $plugin_version, '!=')) {
				$patch_results['error'] = __('Patch file version does not match with that of your plugin.', $domain);
				break;
			}
			$plugin_dir = WP_PLUGIN_DIR.'/'.$plugin_basename;
			$patch_file = SQLitePatchDir.'/'.$file;
			$command = $patch_command.' <'.$patch_file.' 2>&1';
			if (chdir($plugin_dir)) {
				exec("$command", $output, $retval);
			} else {
				$patch_results[$file] = __('Error! Plugin directory is not accessible.', $domain);
			}
			if ($retval == 0) {
				$patch_results[$file] = __('is patched successfully.', $domain);
			} else {
				foreach ($output as $val) {
					$message .= $val.'<br />';
				}
				$patch_results[$file] = sprintf(__('Error! Messages: %s', $domain), $message);
			}
		}
		return $patch_results;
	}
	/**
	 * Method to remove patch file(s) from the server.
	 *
	 * It deletes uploaded patch file(s).
	 * If patch file(s) is not selected, returns false.
	 * Or else returns array contains messages.
	 *
	 * @return boolean|array
	 * @access private
	 */
	private function delete_patch_files() {
		global $utils;
		$domain = $utils->text_domain;
		$file_names = array();
		$rm_results = array();
		if (isset($_POST['plugin_checked'])) {
			$file_names = $_POST['plugin_checked'];
		} else {
			return false;
		}
		if (chdir(SQLitePatchDir)) {
			foreach ($file_names as $file) {
				if (unlink($file)) {
					$rm_results[$file] = sprintf(__('File %s is deleted.', $domain), $file);
				} else {
					$rm_results[$file] = sprintf(__('Error! File %s is not deleted.', $domain), $file);
				}
			}
		} else {
			$rm_results[$file] = __('Error!: patches directory is not accessible.', $domain);
		}
		return $rm_results;
	}
	/**
	 * Method to upload patch file(s) to the server.
	 *
	 * It uploads a patch file to the server. You must have the permission to write to the
	 * temporary directory. If there isn't SQLitePatchDir, this method will create it and
	 * set the permission to 0707.
	 *
	 * No return values.
	 *
	 * @return boolean
	 * @access private
	 */
	private function upload_file() {
		global $utils;
		$domain = $utils->text_domain;
		if (!file_exists(SQLitePatchDir) || !is_dir(SQLitePatchDir)) {
			if (!mkdir(SQLitePatchDir, 0707, true)) {
				$message = __('Unable to create a patch directory.', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
				return false;
			}
		}
		if (!is_file(SQLitePatchDir . '/.htaccess')) {
			$fp = fopen(SQLitePatchDir . '/.htaccess', 'w');
			if (!$fp) {
				$message = __('Unable to create a .htaccess file.', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
				return false;
			}
			fwrite($fp, 'DENY FROM ALL');
			fclose($fp);
		}
		if (!isset($_FILES['upfile']['error']) || !is_int($_FILES['upfile']['error'])) {
			$message = __('Invalid operation.', $domain);
			echo '<div id="message" class="updated fade">'.$message.'</div>';
			return false;
		} elseif ($_FILES['upfile']['error'] != UPLOAD_ERR_OK) {
			switch ($_FILES['upfile']['error']) {
				case UPLOAD_ERR_FORM_SIZE:
					$message = __('File is too large to upload.', $domain);
					echo '<div id="message" class="updated fade">'.$message.'</div>';
					break;
				case UPLOAD_ERR_PARTIAL:
					$message = __('File upload is not complete.', $domain);
					echo '<div id="message" class="updated fade">'.$message.'</div>';
					break;
				case UPLOAD_ERR_NO_FILE:
					$message = __('File is not uploaded.', $domain);
					echo '<div id="message" class="updated fade">'.$message.'</div>';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$message = __('Temporary directory is not writable.', $domain);
					echo '<div id="message" class="updated fade">'.$message.'</div>';
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$message = __('File cannot be written on the disk.', $domain);
					echo '<div id="message" class="updated fade">'.$message.'</div>';
					break;
				default:
					$message = __('Unknown error.', $domain);
					break;
			}
			return false;
		}
		if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {
			$file_full_path = SQLitePatchDir . '/' . $_FILES['upfile']['name'];
			if (move_uploaded_file($_FILES['upfile']['tmp_name'], $file_full_path)) {
				$message = __('File is successfully uploaded.', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
				chmod(SQLitePatchDir.'/'.$_FILES['upfile']['name'], 0606);
			} else {
				$message = __('File upload failed. Possible file upload attack.', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
				return false;
			}
		} else {
			$message = __('File is not selected', $domain);
			echo '<div id="message" class="updated fade">'.$message.'</div>';
			return false;
		}
		return true;
	}
	/**
	 * Method to display the patch utility page on the admin panel.
	 *
	 */
	function show_patch_page() {
		global $utils;
		$domain = $utils->text_domain;
		if (is_multisite() && !current_user_can('manage_network_options')) {
			die(__('You are not allowed to access this page!', $domain));
		} elseif (!current_user_can('manage_options')) {
			die(__('You are not allowed to access this page!', $domain));
		}
		if (isset($_POST['apply_patch'])) {
			check_admin_referer('sqlitewordpress-plugin-manip-stats');
			if (is_multisite() && !current_user_can('manage_network_options')) {
				die(__('You are not allowed to do this operation!', $domain));
			} elseif (!current_user_can('manage_options')) {
				die(__('You are not allowed to do this operation!', $domain));
			}
			$result = $this->apply_patches();
			if ($result === false) {
				$message = __('Please select patch file(s)', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
			} elseif (is_array($result) && count($result) > 0) {
				echo '<div id="message" class="updated fade">';
				foreach ($result as $key => $val) {
					echo $key.' => '.$val.'<br />';
				}
				echo '</div>';
			} else {
				$message = __('None of the patches is applied!');
				echo '<div id="message" class="updated fade">'.$message.'</div>';
			}
		}
		if (isset($_POST['patch_file_delete'])) {
			check_admin_referer('sqlitewordpress-plugin-manip-stats');
			if (is_multisite() && !current_user_can('manage_network_options')) {
				die(__('You are not allowed to do this operation!', $domain));
			} elseif (!current_user_can('manage_options')) {
				die(__('You are not allowed to do this operation!', $domain));
			}
			$result = $this->delete_patch_files();
			if ($result === false) {
				$message = __('Please select patch file(s)', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
			} elseif (is_array($result) && count($result) > 0) {
				echo '<div id="message" class="updated fade">';
				foreach ($result as $key => $val) {
					echo $key.' => '.$val.'<br />';
				}
				echo '</div>';
			} else {
				$message = __('Error! Please remove files manually', $domain);
				echo '<div id="message" class="updated fade">'.$message.'</div>';
			}
		}
		if (isset($_POST['upload'])) {
			check_admin_referer('sqlitewordpress-plugin-patch-file-stats');
			if (is_multisite() && !current_user_can('manage_network_options')) {
				die(__('You are not allowed to do this operation!', $domain));
			} elseif (!current_user_can('manage_options')) {
				die(__('You are not allowed to do this operation!', $domain));
			}
			$result = $this->upload_file();
		}
		if (isset($_GET['page']) && $_GET['page'] == 'patch') : ?>
		<div class="navigation">
			<ul class="navi-menu">
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=doc"><?php _e('Documentation', $domain);?></a></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=sys-info"><?php _e('System Info', $domain);?></a></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=setting-file"><?php _e('Miscellaneous', $domain);?></a></li>
				<li class="menu-selected"><?php _e('Patch Utility', $domain);?></li>
				<li class="menu-item"><a href="<?php echo $utils->show_parent();?>?page=maintenance"><?php _e('Maintenance', $domain);?></a></li>
			</ul>
		</div>
		<div class="wrap" id="sqlite-admin-wrap">
		<h2><?php _e('Patch Files Upload and Apply', $domain);?></h2>
		<h3><?php _e('What you can do in this page', $domain);?></h3>
		<p>
		<?php _e('I made patch files for some plugins that are incompatible with SQLite Integration and need rewriting. And I wrote in the <a href="http://dogwood.skr.jp/wordpress/sqlite-integration">Plugin Page</a> about how to apply a patch file to the plugin. But the command line interface sometimes embarrasses some people, especially newbies.', $domain);?>
		</p>
		<p>
		<?php _e('In this page, you can upload patch files and apply them automatically. But there are some requirements.', $domain)?>
		</p>
		<ol>
			<li><?php _e('Think before you leap. Is the plugin to which you are going to apply patch really necessary for your site? Did you search in the <a href="http://wordpress.org/extend/plugins/">Plugin Directory</a> for the substitutes?', $domain)?></li>
			<li><?php _e('Your PHP script has the permission to create a directory and write a file in it.', $domain);?></li>
			<li><?php _e('Your PHP scripts can execute exec() function on the server.', $domain);?></li>
			<li><?php _e('Your PHP script can execute &quot;patch&quot; shell command.(Script will check if it is executable or not.)', $domain);?></li>
		</ol>
		<p>
		<?php _e('If uploading fails, it\' very likely that application will fail. When you try uploading with FTP client, the patch files must be put into the directory wp-content/uploads/patches/. When constant UPLOADS is defined, script follows it.', $domain)?>
		</p>
		<p>
		<?php _e('You can create your patch file yourself. When you create one, please test it on your local server first and check if it works fine without PHP error or notice ( set error_reporting(E_ALL) ). If you use this utility, name your patch file as follows:', $domain);?>
		</p>
		<ol>
			<li><?php _e('Use the file name beginning with the plugin directory name.', $domain);?></li>
			<li><?php _e('Use the plugin version number after the directory name with underscore.', $domain);?></li>
			<li><?php _e('Use the suffix .patch.', $domain);?></li>
			<li><?php _e('Use diff command options &quot;-Naur&quot;.', $domain);?></li>
		</ol>
		<p>
		<?php _e('For example, the patch file for the plugin &quot;Debug Bar&quot; is &quot;debug-bar_0.8.patch&quot;. Script interprets &quot;debug-bar&quot; as the target directory and &quot;0.8&quot; as the target version. If the version number doesn\'t match with the target, script shows the error message and skip applying the patch file. And script will reject any other file name.', $domain);?>
		</p>

		<h3><?php _e('How to install, patch and activate plugins', $domain);?></h3>
		<ol>
			<li><?php _e('Install the plugin (not yet activate it)', $domain);?></li>
			<li><?php _e('Upload the patch file (if any) to the server and ppply it in this page', $domain);?></li>
			<li><?php _e('Back to the installed plugin page and activate it', $domain);?></li>
		</ol>
		<h3><?php _e('How to upgrade plugins', $domain);?></h3>
		<p>
		<?php _e('When upgrading the plugin, it will be safer to follow next steps.', $domain);?>
		</p>
		<ol>
			<li><?php _e('Deactivate the plugin', $domain);?></li>
			<li><?php _e('Upgrade the plugin', $domain);?></li>
			<li><?php _e('Upload the patch file (if any) and apply it', $domain);?></li>
			<li><?php _e('Reactivate the plugin', $domain);?></li>
		</ol>
		<p><?php _e('If there isn\'t a patch file to match with the newest version of the plugin, it won\'t work properly. Please wait for somebody to make one or rewrite the codes checking the patch file for the previous version (it\'s not so difficult a matter, I guess, for almost all the cases, you\'ll have only to replace the MySQL functions with the WordPress built-in functions).', $domain);?></p>
		<h3><?php _e('Upload and Apply', $domain)?></h3>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e('File Select', $domain);?></th>
				<td>
					<form action="" id="upload-form" class="wp-upload-form" method="post" enctype="multipart/form-data">
					<?php if (function_exists('wp_nonce_field')) {
					  wp_nonce_field('sqlitewordpress-plugin-patch-file-stats');
					}
					?>
					<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
					<label for="upload"><?php _e('Select file from your computer. If the file name is the same as existent file, this operation will override it. You can\'t upload the file whose size is over 500kB.', $domain);?></label><br />
					<input type="file" id="upload" name="upfile" size="60"/>
					<input type="submit" name="upload" id="submit-upload" class="button" value="<?php _e('Upload', $domain)?>" />
					</form>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Patch files uploaded', $domain)?></th>
				<td>
					<form action="" method="post">
					<?php if (function_exists('wp_nonce_field')) {
						wp_nonce_field('sqlitewordpress-plugin-manip-stats');
					}
					?>
					<label for="plugin_check"><?php _e('Select the file(s) you want to apply to the plugin(s) or you want to delete. You can select multiple files.', $domain);?></label>
					<table class="widefat page fixed" id="patch-files">
						<thead>
						<tr>
							<th class="item"><?php _e('Apply/Hold', $domain)?></th>
							<th data-sort='{"key":"name"}'><?php _e('Patch files to apply', $domain)?></th>
						</tr>
						</thead>
						<tbody>
						<?php $files = $this->get_patch_files();
						if (!empty($files)) : ?>
						<?php foreach ($files as $file) : ?>
						<tr data-table='{"name":"<?php echo $file ?>"}'>
							<td><input type="checkbox" id="plugin_check" name="plugin_checked[]" value="<?php echo $file ?>"/></td>
							<td><?php echo $file ?></td>
						</tr>
						<?php endforeach;?>
						<?php endif;?>
						</tbody>
					</table>
					<p>
					<input type="submit" name="apply_patch" class="button-primary" value="<?php _e('Apply patch', $domain);?>" onclick="return confirm('<?php _e('Are you sure to apply patch files?\n\nClick [Cancel] to stop,[OK] to continue.', $domain);?>')" />
					<input type="submit" name="patch_file_delete" class="button-primary" value="<?php _e('Delete file', $domain);?>" onclick="return confirm('<?php _e('Are you sure to delete patch files?\n\nClick [Cancel] to stop,[OK] to continue.', $domain);?>')" />
					</p>
					</form>
				</td>
			</tr>
		</tbody>
		</table>
		</div>

		<div class="wrap" id="sqlite-admin-side-wrap">
		<div class="alert">
		<?php _e('Caution about your patch file(s)', $domain);?>
		</div>
		<blockquote class="caution">
		<p>
		<?php _e('If you don\'t know where it comes from or who created it, I strongly recommend that you should see and check the contents of the file. If a person who created it secretly inserted a malicious codes, it will be executed by the plugin and may damage your site or your server, for which damage I don\'t incur any liability. If you don\'t understand well, you\'d better use the substitute plugins. Take your own risk, please.', $domain);?>
		</p>
		</blockquote>
		</div>
		<?php endif;
	}
}
?>