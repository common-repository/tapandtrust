<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://matiks.net
 * @since      1.0.0
 *
 * @package    Matiks_Mywot_Ratings
 * @subpackage Matiks_Mywot_Ratings/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Matiks_Mywot_Ratings_Public {

	/**
	 * The ID of this plugin.
	 *
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/matiks-mywot-ratings-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/matiks-mywot-ratings-public.js', array( 'jquery' ), $this->version, false );
		$params =
			array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'post_id' => get_the_id(),
				'wot_ratings_nonce' => wp_create_nonce('wot_ratings_nonce')
			);
		wp_localize_script( $this->plugin_name, 'params', $params );
	}


	/**
	 * Adds the meta tag for validation if not empty in admin settings
	 */
	function matiks_net_wot_validation()
	{
		$options = get_option($this->plugin_name.'validate');
		// Cleanup
		$validation_code = isset($options['validation_code'])?$options['validation_code']:"";
		if( preg_match('/^[a-f0-9]{20}$/', $validation_code) ) {
			echo '<meta name="wot-verification" content="'.$validation_code.'"/>';
		}
	}


	/**
	 * Disables links in comments (if enabled in settings) and adds attributes in links
	 */
	function matiks_net_modify_comments()
	{

		$comment_id = get_comment_ID();
		$comment = get_comment( $comment_id );
		$options = get_option($this->plugin_name);
		$block = isset($options['block'])?$options['block']:false;

		@define('ROOTPATH', __DIR__);
		require_once(ROOTPATH.'/../Class/Tools.php');
		$liveLinks_tmp = new Tools();


		$comment->comment_content = $liveLinks_tmp->disableLinks($comment->comment_content, $block);
		echo $comment->comment_content;
	}

	/**
	 * Disables author url (if enabled in settings) and adds attributes in link
	 */
	function matiks_net_modify_author_url($comment)
	{
		@define('ROOTPATH', __DIR__);
		require_once(ROOTPATH.'/../Class/Tools.php');
		$Tools = new Tools();

		if( function_exists('filter_var') ) {
			if (
				!filter_var($comment->comment_author_url, FILTER_VALIDATE_URL) === false &&
				$Tools->get_domain_fast($comment->comment_author_url) != get_site_url() &&
				$Tools->get_domain_fast($comment->comment_author_url) != "http://"
			) {
				parse_str(parse_url($comment->comment_author_url, PHP_URL_QUERY), $array);

				if (!array_key_exists("mw_ratings", $array)) {
					$domain = $Tools->get_domain_fast($comment->comment_author_url);
					$comment->comment_author_url = get_site_url() . "?mw_ratings=" . $domain . "&mw_redirect=" . $comment->comment_author_url;
				}
			} else {
				$comment->comment_author_url = "";
			}
		}else{
			if (
				$Tools->get_domain_fast($comment->comment_author_url) != get_site_url() &&
				$Tools->get_domain_fast($comment->comment_author_url) != "http://"
			) {
				parse_str(parse_url($comment->comment_author_url, PHP_URL_QUERY), $array);

				if (!array_key_exists("mw_ratings", $array)) {
					$domain = $Tools->get_domain_fast($comment->comment_author_url);
					$comment->comment_author_url = get_site_url() . "?mw_ratings=" . $domain . "&mw_redirect=" . $comment->comment_author_url;
				}
			} else {
				$comment->comment_author_url = "";
			}
		}
		return $comment;
	}


	/**
	 * Ajax function => called when page is loaded
	 */
	function prefix_ajax_matiks_net_wot_ratings() {


		$post_id = $_POST['post_id'];
		if ( ! wp_verify_nonce( $_POST['wot_ratings_nonce'], 'wot_ratings_nonce' ) )
			die ( 'Not allowed!');
		$options = get_option($this->plugin_name);
		$domains_to_check = $domains_valid = array();


		@define('ROOTPATH', __DIR__);
		require_once(ROOTPATH.'/../Class/Tools.php');


		$Tools = new Tools();
		$ALL_DOMAIN = $Tools->get_all_domain($post_id);

		$domains_array = array();


		foreach($ALL_DOMAIN as $domain)
		{
			$rating = $Tools->ratingInCache($domain);
			//Domain is not valid and is added to be checked with the WOT API
			if( !$rating['valid'])
			{
				$domains_to_check[] = $domain;
			}
			else
			{
				$domains_valid[] = $domain;
				$domains_array[$domain]['trust'] = $rating['trust'];
				$domains_array[$domain]['child'] = $rating['child'];
			}
		}


		$api_key = isset($options['api_key'])?$options['api_key']:"";

		//Number of domains to analyze
		$offset = 0; //Initial offset
		$length = count($domains_to_check); //Number of domains to check
		$step = 100; //Max domains per queries

		/**
		 * Loop until all domains are retrieved
		 */
		$number_of_tries = 0;
		while($offset*$step < $length )
		{
			$domains_array_tmp = $Tools->wot_get_ratings($domains_to_check,$offset,$step,$api_key);
			//An error occurred?
			if( isset($domains_array_tmp['error']) )
			{
				if( $number_of_tries <= 3 ) //Number of tries for this query < 3 ?
				{
					$number_of_tries++; //increases tries
					continue;//restart the query
				}
				else
				{
					//Too many tries => leaves the query for WOT ratings
					$AJAX_ANSWER = array();
					$AJAX_ANSWER['error'] = 'An error occurred when retrieving ratings!';
					die(json_encode($AJAX_ANSWER));
				}
			}
			else
			{
				$number_of_tries = 0; //No problem occurred, var $number_of_tries is reinitialized to zero
			}
			if( empty($domains_array) ) //First result for the domains
				$domains_array = $domains_array_tmp;
			else //There are already results, data array are merged.
				array_merge($domains_array,$domains_array_tmp);
			$offset += $step; //Increases the offset
		}

		$AJAX_ANSWER = array();
		$AJAX_ANSWER['site_url'] = get_site_url();
		$AJAX_ANSWER['min_trust'] = $options['min_trust'];
		$AJAX_ANSWER['min_child'] = $options['min_child'];
		$AJAX_ANSWER['unknown'] = $options['unknown'];
		$AJAX_ANSWER['block'] = $options['block'];
		$AJAX_ANSWER['domains'] = $domains_array;
		die(json_encode($AJAX_ANSWER));
	}
}
