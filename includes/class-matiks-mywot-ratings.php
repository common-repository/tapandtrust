<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://matiks.net
 * @since      1.0.0
 *
 * @package    Matiks_Mywot_Ratings
 * @subpackage Matiks_Mywot_Ratings/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Matiks_Mywot_Ratings
 * @subpackage Matiks_Mywot_Ratings/includes
 * @author     Matiks <matiks.net@gmail.com>
 */
class Matiks_Mywot_Ratings {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 */
	protected $version;

	/**
	 * The installation repository
	 */
	protected $repinstall;

	/**
	 * 	Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {

		$this->plugin_name = 'matiks-mywot-ratings';
		//TODO: Change on update
		$this->version = '1.0.5';
		$this->repinstall = 'tapandtrust';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Matiks_Mywot_Ratings_Loader. Orchestrates the hooks of the plugin.
	 * - Matiks_Mywot_Ratings_i18n. Defines internationalization functionality.
	 * - Matiks_Mywot_Ratings_Admin. Defines all hooks for the admin area.
	 * - Matiks_Mywot_Ratings_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-matiks-mywot-ratings-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-matiks-mywot-ratings-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-matiks-mywot-ratings-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-matiks-mywot-ratings-public.php';

		$this->loader = new Matiks_Mywot_Ratings_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Matiks_Mywot_Ratings_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {

		$plugin_i18n = new Matiks_Mywot_Ratings_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Matiks_Mywot_Ratings_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_repinstall() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add menu item
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add Settings link to the plugin
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

		$this->loader->add_action('admin_init', $plugin_admin, 'options_update');
		$this->loader->add_action('admin_init', $plugin_admin, 'options_update_site');
		// Ajax functions are defined here:
		$this->loader->add_action( 'wp_ajax_matiks_net_get_option',$plugin_admin, 'ajax_matiks_net_wot_analysis' );
		$this->loader->add_action( 'wp_ajax_matiks_net_get_urls',$plugin_admin, 'ajax_matiks_net_wot_analysis' );
		$this->loader->add_action( 'wp_ajax_matiks_net_analyze_domains',$plugin_admin, 'ajax_matiks_net_wot_analysis' );
		$this->loader->add_action( 'wp_ajax_matiks_net_display_results',$plugin_admin, 'ajax_matiks_net_wot_analysis' );
		$this->loader->add_action( 'wp_ajax_matiks_net_disapproved_comment',$plugin_admin, 'ajax_matiks_net_wot_analysis' );

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Matiks_Mywot_Ratings_Public( $this->get_plugin_name(), $this->get_version() );


		$this->loader->add_action( 'wp_head', $plugin_public, 'matiks_net_wot_validation');
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_matiks_net_wot_ratings',$plugin_public, 'prefix_ajax_matiks_net_wot_ratings' );
		$this->loader->add_action( 'wp_ajax_nopriv_matiks_net_wot_ratings',$plugin_public, 'prefix_ajax_matiks_net_wot_ratings' );

		//$this->loader->add_filter( 'comment_form_default_fields', $plugin_public,'remove_comment_url_fields' );
		$this->loader->add_filter( 'get_comment', $plugin_public,'matiks_net_modify_author_url');
		$this->loader->add_action( 'comment_text', $plugin_public,'matiks_net_modify_comments' );
		$this->loader->add_action( 'the_content ', $plugin_public,'matiks_net_get_all_domain' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 * @return Matiks_Mywot_Ratings_Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the version number of the plugin.
	 * @return string
	 */
	public function get_repinstall() {
		return $this->repinstall;
	}

}
