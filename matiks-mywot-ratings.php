<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://matiks.net
 * @since             1.0.0
 * @package           Matiks_Mywot_Ratings
 *
 * @wordpress-plugin
 * Plugin Name:       TapAndTrust
 * Plugin URI:        https://matiks.net
 * Description:       Block unsafe links in comments. Improve your site's User Experience with the WOT API.
 * Version:           1.0.5
 * Author:            Matiks
 * Author URI:        https://matiks.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       TapAndTrust
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-matiks-mywot-ratings-activator.php
 */
function activate_matiks_mywot_ratings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-matiks-mywot-ratings-activator.php';
	Matiks_Mywot_Ratings_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-matiks-mywot-ratings-deactivator.php
 */
function deactivate_matiks_mywot_ratings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-matiks-mywot-ratings-deactivator.php';
	Matiks_Mywot_Ratings_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_matiks_mywot_ratings' );
register_deactivation_hook( __FILE__, 'deactivate_matiks_mywot_ratings' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-matiks-mywot-ratings.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_matiks_mywot_ratings() {

	$plugin = new Matiks_Mywot_Ratings();
	$plugin->run();

}
run_matiks_mywot_ratings();
