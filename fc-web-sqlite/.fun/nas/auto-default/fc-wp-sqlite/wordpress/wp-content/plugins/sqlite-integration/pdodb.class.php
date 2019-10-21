<?php
/**
 * This file defines PDODB class, which inherits wpdb class and replaces it
 * global $wpdb variable.
 *
 * @package SQLite Integration
 * @author Kojima Toshiyasu
 *
 */
if (!defined('ABSPATH')) {
	echo 'Thank you, but you are not allowed to accesss this file.';
	die();
}
require_once PDODIR . 'pdoengine.class.php';
//require_once PDODIR . 'install.php';

if (!defined('SAVEQUERIES')){
	define ('SAVEQUERIES', false);
}
if(!defined('PDO_DEBUG')){
	define('PDO_DEBUG', false);
}

/**
 * This class extends wpdb and replaces it.
 *
 * It also rewrites some methods that use mysql specific functions.
 *
 */
class PDODB extends wpdb {
	/**
	 *
	 * @var reference to the object of PDOEngine class.
	 * @access protected
	 */
	protected $dbh = null;

	/**
	 * Constructor
	 *
	 * This overrides wpdb::__construct() which has database server, username and
	 * password as arguments. This class doesn't use them.
	 *
	 * @see wpdb::__construct()
	 */
	public function __construct() {
		register_shutdown_function(array($this, '__destruct'));

		if (WP_DEBUG)
			$this->show_errors();

		$this->init_charset();

		$this->db_connect();
	}
	/**
	 * Desctructor
	 *
	 * This overrides wpdb::__destruct(), but does nothing but return true.
	 *
	 * @see wpdb::__destruct()
	 */
	public function __destruct() {
		return true;
	}

	/**
	 * Method to set character set for the database.
	 *
	 * This overrides wpdb::set_charset(), only to dummy out the MySQL function.
	 *
	 * @see wpdb::set_charset()
	 */
	public function set_charset($dbh, $charset = null, $collate = null) {
		if ( ! isset( $charset ) )
			$charset = $this->charset;
		if ( ! isset( $collate ) )
			$collate = $this->collate;
	}
	/**
	 * Method to dummy out wpdb::set_sql_mode()
	 *
	 * @see wpdb::set_sql_mode()
	 */
	public function set_sql_mode($modes = array()) {
		unset($modes);
		return;
	}
	/**
	 * Method to select the database connection.
	 *
	 * This overrides wpdb::select(), only to dummy out the MySQL function.
	 *
	 * @see wpdb::select()
	 */
	public function select($db, $dbh = null) {
		if (is_null($dbh))
			$dbh = $this->dbh;
		$this->ready = true;
		return;
	}
	/**
	 * Method to dummy out wpdb::_weak_escape()
	 *
	 */
	function _weak_escape($string) {
		return addslashes($string);
	}
	/**
	 * Method to escape characters.
	 *
	 * This overrides wpdb::_real_escape() to avoid using mysql_real_escape_string().
	 *
	 * @see wpdb::_real_escape()
	 */
	function _real_escape($string) {
		return addslashes($string);
	}
	/**
	 * Method to dummy out wpdb::esc_like() function.
	 *
	 * WordPress 4.0.0 introduced esc_like() function that adds backslashes to %,
	 * underscore and backslash, which is not interpreted as escape character
	 * by SQLite. So we override it and dummy out this function.
	 *
	 * @see wpdb::esc_like()
	 */
	public function esc_like($text) {
		return $text;
	}
	/**
	 * Method to put out the error message.
	 *
	 * This overrides wpdb::print_error(), for we can't use the parent class method.
	 *
	 * @see wpdb::print_error()
	 */
	public function print_error($str = '') {
		global $EZSQL_ERROR;

		if (!$str) {
			$err = $this->dbh->get_error_message() ? $this->dbh->get_error_message() : '';
			if (!empty($err)) $str = $err[2]; else $str = '';
		}
		$EZSQL_ERROR[] = array('query' => $this->last_query, 'error_str' => $str);

		if ($this->suppress_errors)
			return false;

		wp_load_translations_early();

		if ($caller = $this->get_caller())
			$error_str = sprintf(__('WordPress database error %1$s for query %2$s made by %3$s'), $str, $this->last_query, $caller);
		else
			$error_str = sprintf(__('WordPress database error %1$s for query %2$s'), $str, $this->last_query);

		error_log($error_str);

		if (!$this->show_errors)
			return false;

		if (is_multisite()) {
			$msg = "WordPress database error: [$str]\n{$this->last_query}\n";
			if (defined('ERRORLOGFILE'))
				error_log($msg, 3, ERRORLOGFILE);
			if (defined('DIEONDBERROR'))
				wp_die($msg);
		} else {
			$str   = htmlspecialchars($str, ENT_QUOTES);
			$query = htmlspecialchars($this->last_query, ENT_QUOTES);

			print "<div id='error'>
			<p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
			<code>$query</code></p>
			</div>";
		}
	}
	/**
	 * Method to flush cached data.
	 *
	 * This overrides wpdb::flush(). This is not necessarily overridden, because
	 * $result will never be resource.
	 *
	 * @see wpdb::flush
	 */
	public function flush() {
		$this->last_result = array();
		$this->col_info    = null;
		$this->last_query  = null;
		$this->rows_affected = $this->num_rows = 0;
		$this->last_error  = '';
		$this->result      = null;
	}
	/**
	 * Method to do the database connection.
	 *
	 * This overrides wpdb::db_connect() to avoid using MySQL function.
	 *
	 * @see wpdb::db_connect()
	 */
	public function db_connect($allow_bail=true) {
		if (WP_DEBUG) {
			$this->dbh = new PDOEngine();
		} else {
			// WP_DEBUG or not, we don't use @ which causes the slow execution
			// PDOEngine class will take the Exception handling.
			$this->dbh = new PDOEngine();
		}
		if (!$this->dbh) {
			wp_load_translations_early();//probably there's no translations
			$this->bail(sprintf(__("<h1>Error establlishing a database connection</h1><p>We have been unable to connect to the specified database. <br />The error message received was %s"), $this->dbh->errorInfo()));
			return;
		}
		$this->has_connected = true;
		$this->ready = true;
	}
	/**
	 * Method to dummy out wpdb::check_connection()
	 *
	 */
	public function check_connection($allow_bail=true) {
	  return true;
	}
	/**
	 * Method to execute the query.
	 *
	 * This overrides wpdb::query(). In fact, this method does all the database
	 * access jobs.
	 *
	 * @see wpdb::query()
	 */
	public function query($query) {
		if (!$this->ready)
			return false;

		$query = apply_filters('query', $query);

		$return_val = 0;
		$this->flush();

		$this->func_call = "\$db->query(\"$query\")";

		$this->last_query = $query;

		if (defined('SAVEQUERIES') && SAVEQUERIES)
			$this->timer_start();

		$this->result = $this->dbh->query($query);
		$this->num_queries++;

		if (defined('SAVEQUERIES') && SAVEQUERIES)
			$this->queries[] = array($query, $this->timer_stop(), $this->get_caller());

		if ($this->last_error = $this->dbh->get_error_message()) {
			if (defined('WP_INSTALLING') && WP_INSTALLING) {
				//$this->suppress_errors();
			} else {
				$this->print_error($this->last_error);
				return false;
			}
		}

		if (preg_match('/^\\s*(create|alter|truncate|drop|optimize)\\s*/i', $query)) {
			//$return_val = $this->result;
			$return_val = $this->dbh->get_return_value();
		} elseif (preg_match('/^\\s*(insert|delete|update|replace)\s/i', $query)) {
			$this->rows_affected = $this->dbh->get_affected_rows();
			if (preg_match('/^\s*(insert|replace)\s/i', $query)) {
				$this->insert_id = $this->dbh->get_insert_id();
			}
			$return_val = $this->rows_affected;
		} else {
			$this->last_result = $this->dbh->get_query_results();
			$this->num_rows    = $this->dbh->get_num_rows();
			$return_val        = $this->num_rows;
		}
		return $return_val;
	}
	/**
	 * Method for future use?
	 *
	 * WordPress 3.9 separated the method to execute real query from query() function.
	 * This is for the restoration from the case that nothing returns from database.
	 * But this is necessary because we aleady did error manipulations in
	 * pdoengine.class.php. So we don't use this function.
	 *
	 * @access private
	 */
	private function _do_query($query) {
		if (defined('SAVEQUERIES') && SAVEQUERIES) {
			$this->timer_start();
		}
		$this->result = $this->dbh->query($query);
		$this->num_queries++;
		if (defined('SAVEQUERIES') && SAVEQUERIES) {
			$this->queries[] = array($query, $this->timer_stop(), $this->get_caller());
		}
	}
	/**
	 * Method to set the class variable $col_info.
	 *
	 * This overrides wpdb::load_col_info(), which uses a mysql function.
	 *
	 * @see wpdb::load_col_info()
	 * @access protected
	 */
	protected function load_col_info() {
		if ($this->col_info)
			return;
		$this->col_info = $this->dbh->get_columns();
	}

	/**
	 * Method to return what the database can do.
	 *
	 * This overrides wpdb::has_cap() to avoid using MySQL functions.
	 * SQLite supports subqueries, but not support collation, group_concat and set_charset.
	 *
	 * @see wpdb::has_cap()
	 */
	public function has_cap($db_cap) {
		switch(strtolower($db_cap)) {
			case 'collation':
			case 'group_concat':
			case 'set_charset':
				return false;
			case 'subqueries':
				return true;
			default:
				return false;
		}
	}
	/**
	 * Method to return database version number.
	 *
	 * This overrides wpdb::db_version() to avoid using MySQL function.
	 * It returns mysql version number, but it means nothing for SQLite.
	 * So it return the newest mysql version.
	 *
	 * @see wpdb::db_version()
	 */
	public function db_version() {
		//global $required_mysql_version;
		//return $required_mysql_version;
		return '5.5';
	}
}

/*
 * Initialize $wpdb with PDODB class
 */
if (!isset($wpdb)) {
	global $wpdb;
	$wpdb = new PDODB();
}
?>