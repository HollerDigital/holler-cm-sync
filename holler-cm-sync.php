<?php
/**
 * Plugin Name: Holler CM Sync
 * Description: This plugin adds the ability to Sync Wordpress Users to Campaign Monitor :)
 * Plugin URI: http://hollerdigital.com/
 * Version: 1.0
 * Author: Holler Digital
 * Author URI: http://hollerdigital.com/
 * Text Domain: holler
 * License: GPL2
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Globals
define('HOLLER_CMSYNC_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('HOLLER_CMSYNC_PATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );
define("HOLLER_CMSYNC_VERSION", "1.0");

// Plugin Updater
// https://github.com/YahnisElsts/plugin-update-checker
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/HollerDigital/holler-cm-sync',
	__FILE__,
	'holler-cm-sync'
);
 
//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

//Optional: If you're using a private repository, specify the access token like this:
//$myUpdateChecker->setAuthentication('your-token-here');

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.0
 */
class Plugin {

	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {
		
			add_action('wp', array($this, 'schedule_cron_job'));

        	// Hook your cron job function to your custom cron event
        	add_action('holler_sync_contacts', array($this, 'execute_cron_job'));

			add_action('user_register', array($this, 'holler_sync_register'));
			add_action('delete_user', array($this, 'holler_sync_delete'));

			add_action( 'init', function() {
			// Here its safe to include our action class file
			require_once 'inc/settings-page.php';
			require_once 'inc/campaignmonitor/csrest_general.php';
			require_once 'inc/campaignmonitor/csrest_subscribers.php';
			require_once 'inc/campaignmonitor/csrest_clients.php';

		});
	}

	public function holler_sync_delete($user_id){
		// Get user data before the user is deleted
		$user_info = get_userdata($user_id);
		if ($user_info) {
			// Access user's email
			$user_email = $user_info->user_email;

			// get the CM options from WP
			$cm_options = get_option('holler_signup_cm_settings');
			// run sync only if the API key is set.

			if(strlen(trim($cm_options['api_key'])) > 0 ) {
				require_once 'inc/campaignmonitor/csrest_general.php';
				require_once 'inc/campaignmonitor/csrest_subscribers.php';
				require_once 'inc/campaignmonitor/csrest_clients.php';
		
				$wrap = new CS_REST_Subscribers($cm_options['list'], $cm_options['api_key']);
				$result = $wrap->unsubscribe($user_email);
				if($result->was_successful()) {
					echo "Unsubscribed with code ".$result->http_status_code;
				} else {
					echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
					var_dump($result->response);
					echo '</pre>';
				}
			}
		}
	}

	public function holler_sync_register($user_id){
		$user_info = get_userdata($user_id);
		if ($user_info) {
			// Access user's email
			$user_email = $user_info->user_email;
			// $username = $user_info->user_login;
			$first_name = $user_info->first_name;
			$last_name = $user_info->last_name;

			// get the CM options from WP
			$cm_options = get_option('holler_signup_cm_settings');
			
			// run sync only if the API key is set.
			if(strlen(trim($cm_options['api_key'])) > 0 ) {
				require_once 'inc/campaignmonitor/csrest_general.php';
				require_once 'inc/campaignmonitor/csrest_subscribers.php';
			  	require_once 'inc/campaignmonitor/csrest_clients.php';
	  
			  	$wrap = new CS_REST_Subscribers($cm_options['list'], $cm_options['api_key']);
				  $result = false;
				  $subscribe = $wrap->add(array(
					  'EmailAddress' => $user_email,
					  'Name' => $first_name . " " . $last_name,
					  'ConsentToTrack' => 'yes',
					  'Resubscribe' => true
				  ));
  
				  if($subscribe->was_successful()) {
					  $res =  "Subscribed with code ".$result->http_status_code;
					  error_log($res,0);
					  $result = true;
				
					} else {
					  $res =  'Failed with code '.$result->http_status_code."\n";
					  error_log($res,0);
					  $result = false;
					}
			}
		}
	}

	public function schedule_cron_job() {
        if (!wp_next_scheduled('holler_sync_contacts')) {
            wp_schedule_event(time(), 'hourly', 'holler_sync_contacts');
        }
    }

	public function add_subscriber(){

	}

    public function execute_cron_job() {
		 

			$args = array(
				'role'    => '', // Leave empty to get users of all roles
				'orderby' => 'login',
				'order'   => 'ASC',
			);

			$users = get_users($args);

			// Loop through the users
			foreach ($users as $user) {
				$this->holler_sync_register($user->ID);
			}
		}
 

}

// Instantiate Plugin Class
Plugin::instance();