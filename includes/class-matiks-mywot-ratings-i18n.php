<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 */
class Matiks_Mywot_Ratings_i18n {

	/**
	 * The domain specified for this plugin.
	 */
	private $domain;

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Set the domain equal to that of the specified domain.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}
