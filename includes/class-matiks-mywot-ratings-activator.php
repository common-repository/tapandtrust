<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * Fired during plugin activation
 *
 * @link       https://matiks.net
 * @since      1.0.0
 *
 * @package    Matiks_Mywot_Ratings
 * @subpackage Matiks_Mywot_Ratings/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Matiks_Mywot_Ratings
 * @subpackage Matiks_Mywot_Ratings/includes
 * @author     Matiks <matiks.net@gmail.com>
 */
class Matiks_Mywot_Ratings_Activator {


	private static $_db_version = '1.0.1';

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		Matiks_Mywot_Ratings_Activator::create_wot_ratings();
		//Version 1.0.1
		Matiks_Mywot_Ratings_Activator::create_scans();

	}

	/**
	 * Create table WOT ratings
	 */
	private  static function create_wot_ratings() {

		global $wpdb;
		global $matiks_mywot_db_version;

		$matiks_mywot_db_version = Matiks_Mywot_Ratings_Activator::$_db_version;

		$table_name = $wpdb->prefix . 'matiks_wot_ratings';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			domain VARCHAR(255) DEFAULT '' NOT NULL,
			trust INTEGER DEFAULT -1 NOT NULL,
			trust_c INTEGER DEFAULT -1 NOT NULL,
			child INTEGER DEFAULT -1 NOT NULL,
			child_c INTEGER DEFAULT -1 NOT NULL,
			items text,
			blacklist text,
			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'matiks_mywot_db_version', $matiks_mywot_db_version );
	}


	/***
	 * Create table WOT scans
	 */
	private static function create_scans()
	{
		global $wpdb;
		global $matiks_mywot_db_version;

		$installed_ver = get_option( "matiks_mywot_db_version" );

		$matiks_mywot_db_version = Matiks_Mywot_Ratings_Activator::$_db_version;



		if ( $installed_ver <= $matiks_mywot_db_version  ) {

			$table_name = $wpdb->prefix . 'matiks_wot_scans';
			$table_name_2 = $wpdb->prefix . 'matiks_wot_scan_results';

			$charset_collate = $wpdb->get_charset_collate();

			$sqld1 = "IF OBJECT_ID('".$table_name."', 'U') IS NOT NULL
				DROP TABLE ".$table_name;

			$sqld2 = "IF OBJECT_ID('".$table_name_2."', 'U') IS NOT NULL
				DROP TABLE ".$table_name_2;

			$sql = "CREATE TABLE $table_name (
			  id INT NOT NULL AUTO_INCREMENT,
			  date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  user_id INT NOT NULL,
			  with_posts TINYINT(1) NOT NULL,
			  with_comments TINYINT(1) NOT NULL,
			  with_pages TINYINT(1) NOT NULL,
			  countPosts  INT NULL,
			  countComments INT NULL,
			  countPages INT NULL,
			  PRIMARY KEY (id)
			) $charset_collate;";

			$sql2 = "CREATE TABLE IF NOT EXISTS $table_name_2 (
				 id INT NOT NULL AUTO_INCREMENT,
				  domain VARCHAR(50) NULL,
				  url VARCHAR(255) NULL,
				  title VARCHAR(255) NULL,
				  link VARCHAR(255) NULL,
				  author  VARCHAR(255) NULL,
				  author_id INT NULL,
				  post_id INT NULL,
				  comment_id INT NULL,
				  ".$table_name."_id INT NOT NULL,
				  trust INT NULL,
				  trust_c INT NULL,
				  child INT NULL,
				  child_c INT NULL,
				  PRIMARY KEY (id),
				  INDEX fk_".$table_name."_idx (".$table_name."_id ASC),
				  CONSTRAINT fk_".$table_name."
					FOREIGN KEY (".$table_name."_id)
					REFERENCES ".$table_name." (id)
					ON DELETE CASCADE
					ON UPDATE CASCADE
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sqld1 );
			dbDelta( $sqld2 );
			dbDelta( $sql );
			dbDelta( $sql2 );
			update_option( "matiks_mywot_db_version", $matiks_mywot_db_version );
		}
	}
}
