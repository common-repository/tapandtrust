<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * The admin-specific functionality of the plugin.
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Matiks_Mywot_Ratings
 * @subpackage Matiks_Mywot_Ratings/admin
 * @author     Matiks <matiks.net@gmail.com>
 */
class Matiks_Mywot_Ratings_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * The installation repository.
	 *
	 */
	protected $repinstall;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version, $repinstall ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->repinstall = $repinstall;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Matiks_Mywot_Ratings_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Matiks_Mywot_Ratings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name.'mCustomScrollbar', plugin_dir_url( __FILE__ ) . 'css/jquery.mCustomScrollbar.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'roundslider', plugin_dir_url( __FILE__ ) . 'css/roundslider.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/matiks-mywot-ratings-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $step, $withPosts, $withComments;
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Matiks_Mywot_Ratings_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Matiks_Mywot_Ratings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'mCustomScrollbar', plugin_dir_url( __FILE__ ) . 'js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ) );
		wp_enqueue_script( $this->plugin_name.'roundslider', plugin_dir_url( __FILE__ ) . 'js/roundslider.min.js', array( 'jquery' ) );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/matiks-mywot-ratings-admin.js', array( 'jquery' ), $this->version, false );
		$params =
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'step' => $step,
				'withPosts' => $withPosts,
				'withComments' => $withComments,
				'wot_analysis_nonce' => wp_create_nonce('wot_analysis_nonce')
			);
		wp_localize_script( $this->plugin_name, 'params', $params );


	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */

	public function add_plugin_admin_menu() {

		/*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */

		add_menu_page( 'Tap&Trust', 'Tap&Trust', 'manage_options', $this->plugin_name, '',plugin_dir_url( __FILE__ ).'icon.png');
		add_submenu_page($this->plugin_name, esc_attr('Settings', $this->plugin_name), __('Settings', $this->plugin_name), 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
		add_submenu_page($this->plugin_name, esc_attr('Website validation', $this->plugin_name), __('Website validation', $this->plugin_name), 'manage_options', $this->plugin_name."validate", array($this, 'display_plugin_validate'));
		add_submenu_page($this->plugin_name, esc_attr('Analysis', $this->plugin_name), __('Analysis', $this->plugin_name), 'manage_options', $this->plugin_name."analysis", array($this, 'display_plugin_analysis'));
		add_submenu_page($this->plugin_name, esc_attr('About', $this->plugin_name), __('About', $this->plugin_name), 'manage_options', $this->plugin_name."about", array($this, 'display_plugin_about'));
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 */

	public function add_action_links( $links ) {
		/*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
		);
		return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 */

	public function display_plugin_setup_page() {
		$_GET['tab'] = 'comments';
		include_once( 'partials/matiks-mywot-ratings-admin-display.php' );
	}

	public function display_plugin_validate() {
		$_GET['tab'] = 'validate';
		include_once( 'partials/matiks-mywot-ratings-admin-display.php' );
	}

	public function display_plugin_analysis() {
		$_GET['tab'] = 'analysis';
		include_once( 'partials/matiks-mywot-ratings-admin-display.php' );
	}

	public function display_plugin_about() {
		$_GET['tab'] = 'about';
		include_once( 'partials/matiks-mywot-ratings-admin-display.php' );
	}

	public function validate($input) {
		// All checkboxes inputs
		$valid = array();

		//Cleanup
		$valid['min_trust'] = (isset($input['min_trust']) && $input['min_trust'] >= 0 && $input['min_trust'] <= 100) ? $input['min_trust'] : 0;
		$valid['min_child'] = (isset($input['min_child']) && $input['min_child'] >= 0 && $input['min_child'] <= 100) ? $input['min_child'] : 0;
		$valid['unknown'] = (isset($input['unknown']) && !empty($input['unknown']) ) ? $input['unknown'] : 0;
		$valid['block'] = (isset($input['block']) && !empty($input['block']) ) ? $input['block'] : 0;
		$valid['api_key'] = (isset($input['api_key']) && preg_match('/^[a-f0-9]{40}$/', $input['api_key']))?$input['api_key']:"";

		return $valid;
	}

	public function validate_site($input) {
		// All checkboxes inputs
		$valid = array();

		//Cleanup
		$valid['validation_code'] = (isset($input['validation_code']) && preg_match('/^[a-f0-9]{20}$/', $input['validation_code']))?$input['validation_code']:"";
		return $valid;
	}

	/**
	 *
	 * admin/class-wp-cbf-admin.php
	 *
	 **/
	public function options_update() {
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}

	public function options_update_site() {
		register_setting($this->plugin_name.'validate', $this->plugin_name.'validate', array($this, 'validate_site'));
	}


	/**
	 * Function called recursively with a new value for $step passed in POST
	 */
	public function ajax_matiks_net_wot_analysis()
	{

		global $step, $withComments,$withPosts;

		//$_POST variables

		$action = isset($_POST['action'])?$_POST['action']:"NOTHING";
		$step = isset($_POST['step'])?$_POST['step']:0;
		$withPosts = isset($_POST['inc_posts'])?$_POST['inc_posts']:false;
		$withPages = isset($_POST['inc_pages'])?$_POST['inc_pages']:false;
		$withComments = isset($_POST['inc_comments'])?$_POST['inc_comments']:false;
		$inc = isset($_POST['inc'])?$_POST['inc']:0;
		$comment_id =  isset($_POST['comment_id'])?$_POST['comment_id']:null;
		//Nonce security
		if ( ! wp_verify_nonce( $_POST['wot_analysis_nonce'], 'wot_analysis_nonce' ) )
			die ( 'Not allowed!');

		//Nothing selected for the analysis => return with error message
		if( !$withPosts && !$withComments && $action == "get_option"  )
		{
			$response = array();
			$response['error'] = "Nothing to analyze!";
			die(json_encode($response));
		}

		/**
		 * Retrieves data for analysis
		 */
		@define('ROOTPATH', __DIR__);
		require_once(ROOTPATH.'/../Class/Analysis.php');

		$Analysis = new Analysis($this->plugin_name);
		if( $action == "matiks_net_get_option" ) //First step
		{
			die(json_encode($Analysis->getInfo($withPages, $withPosts,$withComments)));
		}
		elseif( $action == "matiks_net_get_urls" ) //Second step => URLs extraction with domains
		{
			die(json_encode($Analysis->getURLs()));
		}
		elseif( $action == "matiks_net_analyze_domains" ) //Second step => URLs extraction wit domains
		{
			$options = get_option($this->plugin_name);
			$result = $Analysis->analyzeDomains($inc,$step,$options['api_key']);
			die(json_encode($result));
		}
		elseif( $action == "matiks_net_display_results" ) //Display results of the last analysis
		{
			$result = $Analysis->displayResults();
			die(json_encode($result));
		}
		elseif( $action == "matiks_net_disapproved_comment" ) //Display results of the last analysis
		{
			$result = $Analysis->disapprovedComment($comment_id);
			die(json_encode($result));
		}
		else
		{
			die(json_encode(array("error" => "nothing to do!")));
		}

	}
}
