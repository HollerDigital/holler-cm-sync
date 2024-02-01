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
  
 

		add_action( 'init', function() {
	
			// Here its safe to include our action class file
			require_once 'inc/class.php';
			require_once 'inc/settings-page.php';
			require_once 'inc/campaignmonitor/csrest_general.php';
			require_once 'inc/campaignmonitor/csrest_subscribers.php';
			require_once 'inc/campaignmonitor/csrest_clients.php';

		});
	}
}

// Instantiate Plugin Class
Plugin::instance();