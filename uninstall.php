<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * Fired when the plugin is uninstalled.
 *
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;



$table_name = $wpdb->prefix . 'matiks_wot_ratings';
$table_name_1 = 'matiks_wot_scan_results';
$table_name_2 = 'matiks_wot_scans';

$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

$wpdb->query( "DROP TABLE IF EXISTS $table_name_1" );

$wpdb->query( "DROP TABLE IF EXISTS $table_name_2" );
