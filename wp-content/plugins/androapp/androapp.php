<?php
	/* 
            Plugin Name: Androapp - Native Android mobile app for wordpress site
            Plugin URI: http://androapp.mobi/
            Description: Native mobile app for android platform, create a beautiful mobile app for your wordpress blog with Push Notifications / Deep Linking / Monetization Option / Infinite Scroll / Fast Image Rendering / Inbuilt Whatsapp/facebook and other sharing channels / Customize Design  /Live preview / Test on your phone features. Get Started with your app in minutes at no cost for the first month and $60/year (reduced from $150/year) from next year onwards. We do not touch your existing mobile/desktop themes. 
            Author: Genius Fools
            Version: 16.00
            Author URI: http://www.geniusfools.com/
            Text Domain: androapp
            Domain Path: /languages/
	*/  

        if ( ! defined( 'ABSPATH' ) ) exit; 
	
        //added file check to fix support issue https://wordpress.org/support/topic/php-problem-plugin-crashed-my-website/
        $filephp = dirname(__FILE__) . '/../../../wp-admin/includes/file.php';
        if ( !function_exists( 'get_home_path' ) && file_exists($filephp))
        {
            require_once($filephp);
        }
        
	define('PW_MOBILE_FILE', __FILE__);
	define('PW_MOBILE_PATH',  plugin_dir_path(__FILE__));
	 
	require PW_MOBILE_PATH.'pw_mobile_settings.php';
	require PW_MOBILE_PATH.'androapp_functions.php';
	require PW_MOBILE_PATH.'androapp_basic_auth.php';
        
	$pwappSettings = new pw_mobile_app_settings();
 
	new androapp_functions();
        
	$plugin = plugin_basename(__FILE__); 
	add_filter("plugin_action_links_$plugin", 'pw_mobile_app_add_settings_link' );

	register_activation_hook( __FILE__, 'pwapp_install_db' );
	
	// Listen for the activate event
	register_activation_hook( __FILE__, array($pwappSettings, 'installpwmobileapp'));
		
	// Listen for the activate event
	register_uninstall_hook( __FILE__, array($pwappSettings, 'uninstall'));
	
	register_deactivation_hook( __FILE__, array($pwappSettings, 'deactivate'));
	
	$generalOptions = get_option("pw-mobile-app");
	
	if($generalOptions[ANDROAPP_ENABLE_WP_SUPER_CACHE] == '1'){
		add_filter("wp_cache_eof_tags",'pw_mobile_app_get_eof_tags');
	}
	
	
	function pw_mobile_app_get_eof_tags(){
		return "/(<\/html>|<\/rss>|<\/feed>|<\/urlset|<\?xml|androappsupercache)/i";
	}
	function pw_mobile_app_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=pw_mobile_app_options">Settings</a>';
		array_unshift($links, $settings_link); 
		return $links;
	}
	
	function pwMobileAppInit() {

	}
	add_action('init', 'pwMobileAppInit');
	include("androapp_init_js_css.php");
	function isAndroAppRequest(){
		return ((isset($_GET["pwapp"]) && $_GET["pwapp"] == "true") || (isset($_GET["androapp"]) && $_GET["androapp"] == "true"));
	}
	
	global $pwapp_db_version;
	$pwapp_db_version = '1.0.4';

	add_action( 'plugins_loaded', 'pwapp_install_db' );

	function pwapp_install_db() {
	
		load_plugin_textdomain( 'androapp', false, dirname( plugin_basename( __FILE__ ) ) );
		global $wpdb;
		global $pwapp_db_version;
		$installed_ver = get_option( "pwapp_db_version" );

		if ( empty($installed_ver)) {
			$table_name = $wpdb->prefix . 'pw_gcmusers';
			
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `gcm_regid` text,
				  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `status` tinyint default 1,
                                  `device` VARCHAR(128) default 'android',
                                  `topics` varchar(128),
				  PRIMARY KEY (`id`)
				) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			add_option( 'pwapp_db_version', $pwapp_db_version );
		}
		if($installed_ver != $pwapp_db_version){
			$table_name = $wpdb->prefix . 'androapp_stats';
			
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `title` text,
				  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `eligible` int default 0,
                                  `ios_eligible` int default 0,
				  `success` int default 0,
				  `notRegistered` int default 0,
				  `mismatchsenderid` int default 0,
				  `other` int default 0,
				  `status` VARCHAR(60) default 'START',
				  `ios_bulk_sent` int default 0,
                                  `ios_sent` int default 0,
                                  `ios_notRegistered` int default 0,
                                  `bulk_sent` int default 0,
                                   PRIMARY KEY (`id`)
				) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			update_option( 'pwapp_db_version', $pwapp_db_version );
		}
	}
	


?>
