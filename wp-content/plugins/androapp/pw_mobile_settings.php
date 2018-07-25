<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
require_once("androapp_utils.php");


define('ANDROAPP_IMAGE_DIMENSION' , 'image_dimension');
define('ANDROAPP_EXCERPT_PREFERENCE' , 'excerpt_preference');
define('ANDROAPP_POST_CONTENT' , 'post_content');
define('ANDROAPP_FAILOVER_POST_CONTENT' , 'failover_post_content');
define('PWAPP_MENU' , 'app_menu');
define('SLIDER_MENU' , 'slider_menu');
define('PWAPP_SHARE_FN_NAME', 'share_function_name');
define('ANDROAPP_GCM_API_KEY', 'gcm_api_key');
define('ANDROAPP_ANALYTICS_TRACKING_ID_KEY', 'analytics_tracking_id');
define('ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY', 'ios_analytics_tracking_id');
define('ANDROAPP_APP_NAME', 'app_name');
define('ANDROAPP_DEEP_LINKING', 'app_deep_linking');
define('ANDROAPP_GOOGLE_APP_ID', 'google_app_id');
define('ANDROAPP_IOS_APP_ID', 'ios_app_id');
define('ANDROAPP_GOOGLE_SENDER_ID', 'google_sender_id');
define('ANDROAPP_HOST_NAME', 'app_host_name');
define('ANDROAPP_AUTHENTICATION_KEY', 'authentication_key');
define('ANDROAPP_EMAIL', 'email');
define('ANDROAPP_CLIENT_ID', 'client_id');
define('ANDROAPP_PACKAGE_NAME', 'package_name');
define('ANDROAPP_LAUNCHER_ICON', 'launcher_icon');
define('ANDROAPP_NOTIFICATION_ICON', 'notification_icon');
define('ANDROAPP_SPLASH_IMAGE', 'splashImage');
define('ANDROAPP_NOTIFICATION_TYPE_KEY', 'notification_type_key');
define('ANDROAPP_ENABLE_WP_SUPER_CACHE', 'cache_json_apis');
define('ANDROAPP_THEME_NAME', 'androapp_theme');
define('ANDROAPP_FONT_NAME', 'androapp_font_name');
define('ANDROAPP_RENEWAL_DATE', 'androapp_renewal_date');
define('ANDROAPP_PLUGIN_URL', 'androapp_plugin_url');

if (!defined('PW_ANDROAPP_VERSION_KEY'))
{
    define('PW_ANDROAPP_VERSION_KEY', 'pw_androapp_version');
}


class pw_mobile_app_settings {
	protected $androappVersion = 1200;
	// Name of the array
	protected $option_name = 'pw-mobile-app';
	protected $language_option_name = 'pw-mobile-app-language';
	protected $post_content_tab_key = 'androapp_post_content_tab';
	protected $get_started_option_name = 'androapp_getstarted';
	protected $build_option_name ='pw-mobile-build-options';
	protected $plugin_options_key = 'pw_mobile_app_options';
	protected $account_tab_key = 'androapp_account_settings';
        protected $ios_tab_key = 'ios_build_settings';
        
	protected $tools_tab_key = 'androapp_tools_tab';
	protected $publish_tab_key = 'androapp_publish';
	protected $scripts_option = 'androapp_scripts_detected';
	private $plugin_settings_tabs = array();
	private $androAppHost = "https://androapp.mobi";
	
        public static $topAdShowOnListPages = "top-ad-show-list";
        public static $topAdShowOnSinglePages = "top-ad-show-single";
        public static $middleAdShowOnListPages = "middle-ad-show-list";
        public static $middleAdShowOnSinglePages = "middle-ad-show-single";
        public static $bottomAdShowOnListPages = "bottom-ad-show-list";
        public static $bottomAdShowOnSinglePages = "bottom-ad-show-single";
        
        
	public static $defaultPostPushNotification = "default-post-push-notification";
	public static $disableBulkSend = "disable-bulk-send";
        public static $disableNotificationCache = "disable-notification-cache";
	public static $stripAdsenseUnits = "strip-adsense-units";
        public static $topAppNextAdUnitKey = 'top_appnext_ad_unit';
	public static $topAdUnitKey = 'top_ad_unit';
        public static $bottomAppNextAdUnitKey = 'bottom_appnext_ad_unit';
	public static $bottomAdUnitKey = 'bottom_ad_unit';
        public static $listViewAppNextAdUnitKey = 'list_appnext_ad_unit';
	public static $listViewAdUnitKey = 'list_ad_unit';
	public static $mopubTopAdUnitKey = 'mopub_top_ad_unit';
	public static $mopubBottomAdUnitKey = 'mopub_bottom_ad_unit';
	public static $mopubMiddleAdUnitKey = 'mopub_middle_ad_unit';
	public static $interstitialAdUnitKey = 'interstitial_ad_unit';
	public static $mopubInterstitialAdUnitKey = 'mopub_interstitial_ad_unit';
	public static $appNextInterstitialAdUnitKey = 'appnext_interstitial_ad_unit';
	public static $appNextInterstitialAdType = 'appnext_interstitial_ad_type';
	public static $bottomAdType = 'bottom_ad_ype';
        public static $bottomAppNextAdType = 'bottom_appnext_ad_ype';
	public static $topAdType = 'top_ad_ype';
        public static $topAppNextAdType = 'top__appnext_ad_ype';
	public static $listViewAdUnitFreqKey = 'list_ad_unit_freq';
        public static $pushStackThershold = 'push_stack_thershold';
	public static $listViewAdUnitTypeKey = 'list_ad_unit_type';
        public static $listViewAppNextAdUnitTypeKey = 'list_appnext_ad_unit_type';
	public static $interstitialAdUnitFreqKey = 'interstitial_ad_unit_freq';
	public static $androAppCss = "androapp_css";
	public static $shareImagePreference = "share_image_preference";
        public static $featuredImageShowHide = "featured_image_showhide";
	public static $shareTextWithImage = "share_textwithimage_preference";
	public static $sharePreference = "share_preference";
	public static $shareSuffixText = "share_suffix_test";
	public static $shareSuffixLink = "share_suffix_link";
        public static $loadUrlPostIds = "loadurl_postids";
        public static $preProcessedPostIds = "preprocessed_postids";
        public static $postProcessedPostIds = "postprocessed_postids";
        public static $loadimagesPostIds = "loadimages_postids";
	public static $shareImageWithCustomFunction = "share_image_with_custom_function";
	
	public static $tagTextColorKey = "tagTextColor";
	public static $tagBgColorKey = "tagBgColor";
	public static $feedBgColorKey = "feedBgColor";
	public static $feedTitleColorKey = "feedTitleColor";
	public static $feedContentTextColorKey = "feedContentTextColor";
	public static $screenBgColorKey = "screenBgColor";
	public static $actionBarTitleColorKey = "actionBarTitleColor";
	public static $actionBarBgColorKey = "actionBarBgColor";
	public static $statusBarBgColorKey = "statusBarBgColor";
	public static $authorTextColorKey = "authorTextColor";
	public static $timeTextColorKey = "timeTextColor";
	public static $selectedScripts = "androapp_selected_Scripts";
	public static $postProcessedCss = "androapp_postprocessed_css";
	
	public static $useOnlyFeaturedImage = "use_only_featured_image";
	public static $commentsProvider = "comments_provider";
	public static $searchBox = "search_box_status";
	public static $homePageWidget = "homepage_widget";
	public static $homePagePostId = "homepage_post_id";
	public static $homePagePostType = "homepage_post_type";
	public static $showCartIcon = "show_cart_icon";
	
	public static $showCommentsCount = "show_comments_count";
        public static $enableOfflineSave = 'enable_offline_save';
        public static $disableImageZoom = 'disable_image_zoom';
	
	public static $headerScript = "androapp_header_script";
	public static $beforePostContent = "androapp_before_post_content";
	public static $afterPostContent = "androapp_after_post_content";
	public static $regexForOpeningInBrowser = "regex_open_browser";
	public static $regexForOpeningInWebview = "regex_open_webview";
        
        protected $custom_taxonomies;
        protected $custom_post_types;
        
	private $scriptOptions;
	
	public static $fontArray = array(
									 "ABeeZee" => array("ABeeZee","ABeeZee"),
									 "Arvo-Italic" => array("Arvo:400italic","Arvo"),
									 "Alegreya-Regular" => array("Alegreya","Alegreya"),
									 "AlegreyaSans-Regular" => array("Alegreya+Sans","Alegreya Sans"),
									 "AnonymousPro-Regular" => array("Anonymous+Pro","Anonymous Pro"),
									 "ArchivoBlack-Regular" => array("Archivo+Black","Archivo Black"),
									 "ArchivoNarrow-Italic" => array("Archivo+Narrow:400italic","Archivo Narrow"),
									 "ArchivoNarrow-Regular" => array("Archivo+Narrow","Archivo Narrow"),
									 "Arvo-Regular" => array("Arvo","Arvo"),
									 "Bitter-Regular" => array("Bitter:400","Bitter"),
									 "Chivo-Regular" => array("Chivo","Chivo"),
									 "ContrailOne-Regular" => array("Contrail+One","Contrail One"),
									 "CrimsonText-Italic" => array("Crimson+Text:400italic","Crimson Text"),
									 "Domine-Regular" => array("Domine","Domine"),
									 "Bitter-Bold" => array("Bitter:700","Bitter"),
									 "FiraSans-Regular" => array("Fira+Sans","Fira Sans"),
									 "Inconsolata-Regular" => array("Inconsolata","Inconsolata"),
									 "JosefinSlab-Regular" => array("Josefin+Slab","Josefin Slab"),
									 "Karla-Regular" => array("Karla","Karla"),
									 "Lato-Regular" => array("Lato","Lato"),
									 "Bitter-Italic" => array("Bitter:400italic","Bitter"),
									 "LibreBaskerville-Regular" => array("Libre+Baskerville","Libre Baskerville"),
                                                                         "Cairo-Bold" => array("Cairo:700","Cairo")
									 );
	
	public static $languageArray = null;
	// Default values
	protected $op = array(
		ANDROAPP_IMAGE_DIMENSION => 'preview',
		ANDROAPP_EXCERPT_PREFERENCE => 'excerpt',
		ANDROAPP_POST_CONTENT => 'postprocessed',
		ANDROAPP_ENABLE_WP_SUPER_CACHE => '0'
		
	);
	
	protected $buildop = array(
		ANDROAPP_GOOGLE_APP_ID => '',
		ANDROAPP_GOOGLE_SENDER_ID => '',
                ANDROAPP_IOS_APP_ID => ''
	);
	
	protected $postContentop = array(
		'androapp_header_script' => '',
		'androapp_before_post_content' => '',
		'androapp_after_post_content' => '',
	);
	
	protected $accountop = array(
            ANDROAPP_NOTIFICATION_TYPE_KEY => 'single',
            'push_stack_thershold' => '5'
	);
	
	public function getInstallLink($slug){
		$action = 'install-plugin';
		return  wp_nonce_url(
			add_query_arg(
				array(
					'action' => $action,
					'plugin' => $slug
				),
				admin_url( 'update.php' )
			),
			$action.'_'.$slug
		);
	}
        
        function initCustomPostsTaxonomies(){
            $args=array('public'   => true, '_builtin' => false);
            $output = 'names'; // or objects
            $operator = 'and';
            $this->custom_taxonomies = get_taxonomies($args, $output, $operator);
            
            $args = array('public' => true, '_builtin' => false);
            $this->custom_post_types = get_post_types( $args, 'names', 'and' );
        }


        public function __construct(){
		
		pw_mobile_app_settings::$languageArray = array("HOME"=> __("Home", 'androapp'), "SELECT" => __("Select...", 'androapp'), "SELECT_CATEGORY"=> __("Select Category", 'androapp'), "CANT_CONNECT"=> __("Can't Connect", 'androapp'), "RETRY"=> __("Tap to Retry", 'androapp'), "CONNECTION_TIMEOUT"=> __("Connection Timeout", 'androapp'), "UNKNOWN_ERROR"=> __("Unknown Error", 'androapp'), "LOADING"=> __("Loading...", 'androapp'),
		 "SHARE_TITLE" => __("Hey, I found this interesting", 'androapp'), "NEW_POST"=> __("New Post", 'androapp'), "NEW_POSTS"=> __("new posts", 'androapp'), "YEAR"=> __("year", 'androapp'), "MONTH"=> __("month", 'androapp'), "DAY"=> __("day", 'androapp'), "HOUR"=> __("hour", 'androapp'), "MINUTE"=> __("minute", 'androapp'), "SECOND"=> __("second", 'androapp'), "YEARS"=> __("years", 'androapp'), "MONTHS"=> __("months", 'androapp'), "DAYS"=> __("days", 'androapp'), "HOURS"=> __("hours", 'androapp'), "MINUTES"=> __("minutes", 'androapp'), "SECONDS"=> __("seconds", 'androapp'), "AGO"=> __("ago", 'androapp'), "BY"=> __("by", 'androapp'), "IN"=> __("in", 'androapp'), "NO_COMMENTS"=> __("No comments yet, Be the first one to comment", 'androapp'), "COMMENTS_TITLE"=> __("COMMENTS", 'androapp'), "COMMENT_EMPTY"=> __("Comment field is Empty", 'androapp'), "PROVIDE_EMAIL"=> __("Please provide your name and email address", 'androapp'), "SENDING_COMMENT"=> __("Sending Comment...", 'androapp'), "AWAITING_MODERATION"=> __("Awaiting Moderation", 'androapp'), "TYPE_MESSAGE"=> __("Type Message", 'androapp'), "TYPE_REPLY_MESSAGE"=> __("Type Reply Message", 'androapp'), "COMMENT_SETTINGS"=> __("Comments Settings", 'androapp'), "NAME"=> __("Name", 'androapp'), "EMAIL"=> __("Email", 'androapp'), "EMAIL_EMPTY"=> __("Email can't be Empty", 'androapp'), "VALID_EMAIL"=> __("Please enter a valid email address", 'androapp'), "ATLEAST_THREE_CHARS"=> __("Please enter atleast 3 characters in name", 'androapp'), "VALID_NAME"=> __("Name can't be Empty", 'androapp'), "SUBMIT"=> __("Submit" ,'androapp'),"SEARCH_HINT" => __("Search Posts", 'androapp'), "EMPTY_SEARCH_RESULT" => __("Sorry, no content matched your criteria",'androapp')
                 ,"OFFLINE_POSTS" => __("Saved Posts")
                 ,"SAVE_FOR_OFFLINE" => __("Save Offline"),
                 "REMOVE_FROM_OFFLINE" => __("Remove from Offline Save"),
                 "PERMISSION_NEEDED" => __("Permission Needed"),   
	
	"wooseparator" => __("wooseparator",'androapp'),
	"PRODUCT_DESCRIPTION"=> __("Description", 'androapp'), "CART"=> __("Cart", 'androapp'), "CHECKOUT"=> __("Checkout", 'androapp'), "ADD_TO_CART_ERROR"=> __("Could not update cart !!", 'androapp'), "LOGIN"=> __("Login", 'androapp'), "USERNAME"=> __("Username", 'androapp'), "PASSWORD"=> __("Password", 'androapp'), "REPASSWORD"=> __("Re Type Password", 'androapp'), "CANT_BE_EMPTY"=> __("Can't be EMpty", 'androapp'), "SHIPPING_ADDRESS"=> __("Shipping Address", 'androapp'), "BILLING_ADDRESS"=> __("Billing Address", 'androapp'), "FIRST_NAME"=> __("First Name", 'androapp'), "LAST_NAME"=> __("Last Name", 'androapp'), "CITY"=> __("City", 'androapp'), "STATE"=> __("State", 'androapp'), "COUNTRY"=> __("Country", 'androapp'), "PINCODE"=> __("Pincode", 'androapp'), "ADDRESS1"=> __("Address 1", 'androapp'), "ADDRESS2"=> __("Address 2", 'androapp'), "PHONE"=> __("Phone", 'androapp'), "PASSWORDS_DONT_MATCH"=> __("Password do not match", 'androapp'), "ENTER_COUPON_CODE"=> __("Enter Coupon Code", 'androapp'), "APPLY_COUPON"=> __("Apply Coupon", 'androapp'), "SUBTOTAL"=> __("Subtotal", 'androapp'), "TAXES"=> __("Taxes", 'androapp'), "SHIPPING_AND_HANDLING"=> __("Shipping And Handling", 'androapp'), "TOTAL"=> __("Total", 'androapp'), "Discount"=> __("Discount", 'androapp'), "NEXT"=> __("Next", 'androapp'), "BACK"=> __("Back", 'androapp'), "ORDER_NOTE"=> __("Order Note", 'androapp'), "SHIPPING_METHOD"=> __("Shipping Method", 'androapp'), "ORDER_REVIEW"=> __("Order Review", 'androapp'), "ORDER_COMPLETE"=> __("Order Status", 'androapp'), "PAYMENT_METHOD"=> __("Payment Method", 'androapp'), "ORDER"=> __("Order", 'androapp'), "ORDER_STATUS"=> __("Order Status", 'androapp'), "LOGGED_OUT"=> __("You are now logged out", 'androapp'), "CART_EMPTY"=> __("Cart Empty", 'androapp'), "BROWSE_PRODUCTS"=> __("Browse Products", 'androapp'), "ADDED_TO_CART"=> __("Added to cart", 'androapp'), "COUPON"=> __("Coupon", 'androapp'), "APPLIED"=> __("Applied", 'androapp'), "MRP"=> __("MRP", 'androapp'), "BUY_NOW"=> __("BUY NOW", 'androapp'), "OUT_OF_STOCK"=> __("Out Of Stock", 'androapp'), "REGISTER" => __("Register", 'androapp'), "PRODUCT_ADD_TO_CART_ERROR" => __("Product can not be added to cart", 'androapp'), "COUPON_APPLY_ERROR" => __("Coupon can not be applied", 'androapp'), 
	"VENDOR" => __("Vendor",'androapp'), "NOT_ENOUGH_STOCK" => __("Sorry, Not enough stock",'androapp'),
	
	"SAME_AS_BILLING_ADDRESS"=> __("Same as Billing Address",'androapp'),
    "UPDATE_QUANTITY"=> __("Update Quantity",'androapp'),
    "CART_UPDATED"=> __("Cart Updated",'androapp'),
    "QUANTITY_TEXT_VALIDATION"=> __("Please enter a number",'androapp')
	);
		
		
		 add_action('admin_init', array($this,'admin_init'));
		
		add_action('admin_menu', array($this, 'add_page'));
		
		add_action('admin_notices', array($this, 'my_plugin_admin_notices'));
		
		$this->scriptOptions = get_option($this->scripts_option);

		if((empty($this->scriptOptions['scripts']) || ($this->scriptOptions['count'] < 5)))
		{
			add_action( 'print_scripts_array', array($this, 'add_scripts'), 999);
			add_action('wp_print_scripts', array($this, 'add_late_scripts'), 999);
			$this->scriptOptions['count'] = $this->scriptOptions['count'] +1;	
		}
		
		if(!get_option(PW_ANDROAPP_VERSION_KEY)) {
			//dont save any properties here as they are going to be set by activation hook
			update_option(PW_ANDROAPP_VERSION_KEY, $this->androappVersion);
		}else{
			$oldVersion = get_option(PW_ANDROAPP_VERSION_KEY);
			if(!empty($oldVersion) && $oldVersion != $this->androappVersion ){
				if( $oldVersion < 319){
					$this->updateFor319();
				}
				if( $oldVersion < 320){
					$this->updateFor320();
				}
				if( $oldVersion < 403){
					$this->updateFor403();
				}
				if( $oldVersion < 406){
					$this->updateFor406();
				}
				if( $oldVersion < 501){
					$this->updateFor501();
				}
				if( $oldVersion < 600){
					$this->updateFor600();
				}
				if( $oldVersion < 603){
					$this->updateFor603();
				}
                                if( $oldVersion < 605){
					$this->updateFor605();
				}
                                if( $oldVersion < 608){
					$this->updateFor608();
				}
                                if( $oldVersion < 700){
                                    $this->updateFor700();
				}
                                if( $oldVersion < 701){
                                    $this->updateFor701();
				}
                                if( $oldVersion < 1000){
                                    $this->updateFor605();
				}
				if( $this->androappVersion == 1200){
					$this->updateFor1200();
				}
				update_option(PW_ANDROAPP_VERSION_KEY, $this->androappVersion);
			}
		}
	}

	function show_success_message($message) {
    ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo $message; ?></p>
		</div>
		<?php
	}
	
	function my_plugin_admin_notices() {
	}
	
	function initProperties(){
		$this->buildop[ANDROAPP_HOST_NAME] =  get_bloginfo('url');;
		$this->buildop[ANDROAPP_APP_NAME] = get_bloginfo('name');
		$this->buildop[ANDROAPP_DEEP_LINKING] = "/";
		$this->buildop[ANDROAPP_EMAIL] = get_option('admin_email');
		$this->buildop[ANDROAPP_AUTHENTICATION_KEY] = $this->getToken(64);
		$this->buildop[ANDROAPP_THEME_NAME] = 'cardview';
		$this->op[PWAPP_MENU] = $this->getFirstMenu();
		$this->op[SLIDER_MENU] = $this->getFirstMenu();
                
		$this->op[pw_mobile_app_settings::$androAppCss] = '#content-sidebar{
	display:none;
}
#secondary {
	display:none !important;
}
.sidebar, .site-header, .footer-widgets {
	display:none;
}
#menu-header-menu{
	display:none;
}';

		$this->op[pw_mobile_app_settings::$postProcessedCss] = 'androapp img{
    max-width: 100%;
    height: auto;
}
iframe{
    max-width: 100%;    
}
figure {
    max-width: 100%;
    height: auto;
}
img{
    max-width: 100% !important;
    height: auto;
}
div {
    max-width: 100% !important;
}
span {
    max-width:100%;
    overflow: auto;
}
audio {
    display:block;
    visibility:visible !important;
}
video{
    width:100%;
    height:auto;
}
';
		$this->op[pw_mobile_app_settings::$shareImagePreference] = 'first';
                $this->op[pw_mobile_app_settings::$featuredImageShowHide] = 'show';
		$this->op[pw_mobile_app_settings::$sharePreference] = 'EXCERPT';
		$this->op[pw_mobile_app_settings::$shareTextWithImage] = 'TITLE';
		$this->op[pw_mobile_app_settings::$shareSuffixText]  = 'via';
		$this->op[pw_mobile_app_settings::$shareSuffixLink] = 'POST';
		$this->op[pw_mobile_app_settings::$shareImageWithCustomFunction] = '1';
		
		if(get_option("comment_registration") == 1){
			$this->op[pw_mobile_app_settings::$commentsProvider] = 'disabled';
		}else{
			$this->op[pw_mobile_app_settings::$commentsProvider] = 'wordpress';
		}
		
		$this->op[pw_mobile_app_settings::$showCommentsCount] = '1';
                $this->op[pw_mobile_app_settings::$enableOfflineSave] = '1';
		

		$this->op[pw_mobile_app_settings::$homePageWidget] = 'posts';
		
		$this->buildop[pw_mobile_app_settings::$tagTextColorKey] = "#ff808080";
		$this->buildop[pw_mobile_app_settings::$tagBgColorKey] = "#E6E6E6";
		$this->buildop[pw_mobile_app_settings::$feedBgColorKey] = "#FFFFFF";
		$this->buildop[pw_mobile_app_settings::$feedTitleColorKey] = "#424242";
		$this->buildop[pw_mobile_app_settings::$feedContentTextColorKey] = "#616161";
		$this->buildop[pw_mobile_app_settings::$screenBgColorKey] = "#E6E6E6";
		$this->buildop[pw_mobile_app_settings::$actionBarTitleColorKey] = "#FFFFFF";
		$this->buildop[pw_mobile_app_settings::$actionBarBgColorKey] = "#F4832C";
		$this->buildop[pw_mobile_app_settings::$authorTextColorKey] = "#477c29";
		$this->buildop[pw_mobile_app_settings::$timeTextColorKey] = "#757575";
		$this->buildop[pw_mobile_app_settings::$statusBarBgColorKey] = "#f45917";
	}
	
	private function resetBuildOptions(){
		$this->initProperties();
		//not resetting icon
		$buildOptions = get_option($this->build_option_name);
		$this->buildop[ANDROAPP_LAUNCHER_ICON] = $buildOptions[ANDROAPP_LAUNCHER_ICON];
                $this->buildop[ANDROAPP_NOTIFICATION_ICON] = $buildOptions[ANDROAPP_NOTIFICATION_ICON];
		$this->buildop[ANDROAPP_SPLASH_IMAGE] = $buildOptions[ANDROAPP_SPLASH_IMAGE];
		update_option($this->build_option_name, $this->buildop);
	}
	
	private function resetLanguageOptions(){
		update_option($this->language_option_name, pw_mobile_app_settings::$languageArray);
	}
	
	public function installpwmobileapp() {
		$this->initProperties();
		
		if(!get_option($this->option_name)) {
			add_option($this->option_name, $this->op);
		}
		
		if(!get_option($this->build_option_name)) {
			add_option($this->build_option_name, $this->buildop);
		}
		
		if(!get_option($this->account_tab_key)) {
			add_option($this->account_tab_key, $this->accountop);
		}
		
		if(!get_option($this->post_content_tab_key)) {
			add_option($this->post_content_tab_key, $this->postContentop);
		}
		
		if(!get_option($this->language_option_name)){
			add_option($this->language_option_name, pw_mobile_app_settings::$languageArray);
		}
		if(!get_option($this->scripts_option)) {
			$scriptOptions = array();
			$scriptOptions['count'] = 0;
			$scriptOptions['scripts'] = null;
			add_option($this->scripts_option, $scriptOptions);
		}
		
		$this->initiAndroAds();
		$this->doCurlRequest($this->buildop[ANDROAPP_HOST_NAME], "INSTALL", $this->buildop[ANDROAPP_EMAIL]);
	}
	
	
	private function updateFor403(){
		if(get_option($this->language_option_name)){
			$languageOption = get_option($this->language_option_name);
			$languageOption['VENDOR'] = "Vendor";
			update_option($this->language_option_name, $languageOption);
		}
	}

	private function updateFor406(){
		if(get_option($this->language_option_name)){
			$languageOption = get_option($this->language_option_name);
			$languageOption['SEARCH_HINT'] = "Search posts";
			$languageOption['EMPTY_SEARCH_RESULT'] = "Sorry, no content matched your criteria";
			update_option($this->language_option_name, $languageOption);
		}
		if(get_option($this->option_name)){
			$op = get_option($this->option_name);
			$op[pw_mobile_app_settings::$postProcessedCss] .= 'img{
     max-width: 100% !important;
     height: auto;
}
div {
 max-width: 100% !important;
}
';
			update_option($this->option_name, $op);
		}
	}
	
	private function initiAndroAds(){
		if(!get_option('androapp_ads')){
			add_option('androapp_ads', array());	
		}
		$androAds = get_option('androapp_ads');
		$androAds['androapp_header'] = '';
		$androAds['top_ad'] = "";
		$androAds['bottom_ad'] = "<!-- AndroApp Start -->
<div id=\"M182532ScriptRootC61716\">
    <div id=\"M182532PreloadC61716\">
        Loading...
    </div>
    <script>
                (function(){
            var D=new Date(),d=document,b='body',ce='createElement',ac='appendChild',st='style',ds='display',n='none',gi='getElementById';
            var i=d[ce]('iframe');i[st][ds]=n;d[gi](\"M182532ScriptRootC61716\")[ac](i);try{var iw=i.contentWindow.document;iw.open();iw.writeln(\"<ht\"+\"ml><bo\"+\"dy></bo\"+\"dy></ht\"+\"ml>\");iw.close();var c=iw[b];}
            catch(e){var iw=d;var c=d[gi](\"M182532ScriptRootC61716\");}var dv=iw[ce]('div');dv.id=\"MG_ID\";dv[st][ds]=n;dv.innerHTML=61716;c[ac](dv);
            var s=iw[ce]('script');s.async='async';s.defer='defer';s.charset='utf-8';s.src=\"//jsc.mgid.com/a/n/androapp.mobi.61716.js?t=\"+D.getYear()+D.getMonth()+D.getDate()+D.getHours();c[ac](s);})();
    </script>
</div>
<!-- AndroApp End -->";

		update_option('androapp_ads', $androAds);
	}
	
        private function updateFor700(){
            if(get_option($this->option_name)){
                $op = get_option($this->option_name);
                $op[pw_mobile_app_settings::$enableOfflineSave] = 1;
                update_option($this->option_name, $op);
            }
            
            if(get_option($this->language_option_name)){
                $languageOption = get_option($this->language_option_name);
                $languageOption['SAVE_FOR_OFFLINE'] = "Save Offline";
                $languageOption['REMOVE_FROM_OFFLINE'] = "Remove from Offline Save";
                $languageOption['OFFLINE_POSTS'] = "Saved Posts";
                update_option($this->language_option_name, $languageOption);
            }
        }
        
        private function updateFor608(){
            if(get_option($this->option_name)){
                $op = get_option($this->option_name);
                $op[pw_mobile_app_settings::$featuredImageShowHide] = 'show';
                update_option($this->option_name, $op);
            }
        }
        
        private function updateFor1200(){
            if(get_option($this->language_option_name)){
                $languageOption = get_option($this->language_option_name);
                $languageOption['PERMISSION_NEEDED'] = "Permission Needed";
                update_option($this->language_option_name, $languageOption);
            }
        }
        
        private function updateFor701(){
            {
                global $wpdb;
                $tablename = $wpdb->prefix."androapp_stats";
                $sql = "alter table $tablename add column ios_bulk_sent int default 0";
                $wpdb->query($sql);
                $sql = "alter table $tablename add column ios_sent int default 0";
                $wpdb->query($sql);
                $sql = "alter table $tablename add column ios_eligible int default 0";
                $wpdb->query($sql);
                $sql = "alter table $tablename add column ios_notRegistered int default 0";
                $wpdb->query($sql);
                
                $tablename = $wpdb->prefix."pw_gcmusers";
                $sql = "alter table $tablename add column device varchar(128) default 'android'";
                $wpdb->query($sql);
            }
        }
        
        private function updateFor605(){
            global $wpdb;

            {
                $tablename = $wpdb->prefix.pw_gcmusers;
                $sql = "alter table $tablename add column topics varchar(128)";
                $wpdb->query($sql);
            }

            {
                $tablename = $wpdb->prefix."androapp_stats";
                $sql = "alter table $tablename add column bulk_sent int default 0";
                $wpdb->query($sql);
            }
	}
	
	private function updateFor603(){
		
		$this->initiAndroAds();
		
		if(!get_option($this->post_content_tab_key)) {
			add_option($this->post_content_tab_key, $this->postContentop);
		}
		
		$options = get_option($this->build_option_name);
		$clientId = $options[ANDROAPP_CLIENT_ID];
		$res = file_get_contents($this->androAppHost.'/appCreator/info.php?clientId='.$clientId);
		$res = json_decode($res);
		if(isset($res->vt)){
			$renewaldate = $res->vt;
			
			$options[ANDROAPP_RENEWAL_DATE] = strtotime($renewaldate);
			update_option($this->build_option_name, $options);
		}
	}
	
	private function updateFor600(){
		$buildOption = get_option($this->build_option_name);
		$buildOption[ANDROAPP_DEEP_LINKING] = "/";
		update_option($this->build_option_name, $buildOption);
		
		if(get_option($this->language_option_name)){
			$languageOption = get_option($this->language_option_name);
			$languageOption['SAME_AS_BILLING_ADDRESS'] = "Same as Billing Address";
			$languageOption['UPDATE_QUANTITY'] = "Cart Updated";
			$languageOption['NOT_ENOUGH_STOCK'] = "Sorry, Not enough stock";
			$languageOption['QUANTITY_TEXT_VALIDATION'] = "Please enter a number";
			update_option($this->language_option_name, $languageOption);
		}
		
	}
	
	private function updateFor501(){
		if(get_option($this->option_name)){
			$op = get_option($this->option_name);
			$op[pw_mobile_app_settings::$postProcessedCss] .= 'audio {
      display:block;
      visibility:visible !important;
}
video{
     width:100%;
     height:auto;
}
';
			update_option($this->option_name, $op);
		}
	}
	
	private function updateFor320(){
		if(get_option($this->language_option_name)){
			$languageOption = get_option($this->language_option_name);
			$tmpArr = array();
			$tmpArr = array_merge($tmpArr, pw_mobile_app_settings::$languageArray);
			$result = array_merge($tmpArr, $languageOption);
			update_option($this->language_option_name, $result);
		}
	}
	
	private function updateFor319(){
		if(!get_option($this->language_option_name)){
			add_option($this->language_option_name, pw_mobile_app_settings::$languageArray);
		}
	}
	
	public function doCurlRequest($host, $action, $email){
		if(function_exists('curl_init')){
			$url = $this->androAppHost."/appCreator/log.php?action=".$action."&email=".urlencode($email)."&host=".urlencode($host);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);       
			curl_close($ch);
			//echo $output;
		}
	}
	
	public function clearAllOptions(){
		delete_option($this->option_name);
		delete_option($this->build_option_name);
		delete_option($this->account_tab_key);
		delete_option($this->language_option_name);
		delete_option($this->get_started_option_name);
		delete_option($this->scripts_option);
		delete_option($this->post_content_tab_key);		
		
		delete_option(PW_ANDROAPP_VERSION_KEY);
	}
	
	public function uninstall() {
		$options = get_option($this->build_option_name);
		$this->doCurlRequest($options[ANDROAPP_HOST_NAME], "UNINSTALL", $options[ANDROAPP_EMAIL]);
		$this->clearAllOptions();
	}
	public function deactivate() {
		$options = get_option($this->build_option_name);
		$this->doCurlRequest($options[ANDROAPP_HOST_NAME], "DEACTIVATE", $options[ANDROAPP_EMAIL]);
		//$this->clearAllOptions();
	}
	
	// White list our options using the Settings API
	public function admin_init() {
		$this->plugin_settings_tabs[$this->get_started_option_name] = __('Get Started','androapp');	
		register_setting($this->get_started_option_name, $this->get_started_option_name);
		add_settings_section( 'section_get_started', __('Welcome to AndroApp Native Android Mobile App','androapp'), array( &$this, 'options_do_getstarted' ), $this->get_started_option_name );
		
		$this->plugin_settings_tabs[$this->build_option_name] = __('Look & Feel','androapp');
		register_setting($this->build_option_name, $this->build_option_name);
		add_settings_section( 'section_build_settings', __('Look & Feel','androapp'), array( &$this, 'build_options_do_page_parent' ), $this->build_option_name );
		
		$this->plugin_settings_tabs[$this->option_name] = __('Configure','androapp');	
		register_setting($this->option_name, $this->option_name);
		add_settings_section( 'section_app_settings', __(' dynamic settings for your app','androapp'), array( &$this, 'options_do_page' ), $this->option_name );
		
		$this->plugin_settings_tabs[$this->post_content_tab_key] = __('Post Content','androapp');	
		register_setting($this->post_content_tab_key, $this->post_content_tab_key);
		add_settings_section( 'section_post_content_settings', __('Post Content','androapp'), array( &$this, 'post_content_do_page_parent' ), $this->post_content_tab_key );


		$this->plugin_settings_tabs[$this->language_option_name] = __('Internationalization','androapp');	
		register_setting($this->language_option_name, $this->language_option_name);
		add_settings_section( 'section_language_settings', __(' Change texts for your app','androapp'), array( &$this, 'language_options_do_page_parent' ), $this->language_option_name );
		
		$this->plugin_settings_tabs[$this->account_tab_key] = __('Account Settings','androapp');	
		register_setting($this->account_tab_key, $this->account_tab_key);
		add_settings_section( 'section_publish_settings', __('Account Settings','androapp'), array( &$this, 'accounts_do_page' ), $this->account_tab_key );
		
                
                $this->plugin_settings_tabs[$this->ios_tab_key] = __('IOS Build','androapp');	
		register_setting($this->ios_tab_key, $this->ios_tab_key);
		add_settings_section( 'section_ios_build', __('IOS Build','androapp'), array( &$this, 'ios_do_page' ), $this->ios_tab_key );
		
                
		$this->plugin_settings_tabs[$this->tools_tab_key] = __('Push Notifications','androapp');	
		register_setting($this->tools_tab_key, $this->tools_tab_key);
		add_settings_section( 'section_tools_settings', __('Push Notifications','androapp'), array( &$this, 'tools_do_page' ), $this->tools_tab_key );
	}
	
	function add_page() {
		add_menu_page('AndroApp', 'AndroApp', 'manage_options', $this->plugin_options_key, array($this, 'plugin_options_page'), "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iNDMyLjAwMDAwMHB0IiBoZWlnaHQ9IjQzMi4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDQzMi4wMDAwMDAgNDMyLjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgo8bWV0YWRhdGE+CkNyZWF0ZWQgYnkgcG90cmFjZSAxLjEwLCB3cml0dGVuIGJ5IFBldGVyIFNlbGluZ2VyIDIwMDEtMjAxMQo8L21ldGFkYXRhPgo8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLjAwMDAwMCw0MzIuMDAwMDAwKSBzY2FsZSgwLjEwMDAwMCwtMC4xMDAwMDApIgpmaWxsPSIjMDAwMDAwIiBzdHJva2U9Im5vbmUiPgo8cGF0aCBkPSJNMTQzMCA0MzAxIGMwIC0xMCAtNyAtMjQgLTE1IC0zMSAtMjUgLTIwIC0xNyAtNjIgMjEgLTEyMCA1NCAtODIKMTA0IC0xODAgOTggLTE5MyAtMiAtNyAtNDcgLTUyIC05OSAtMTAxIC01NiAtNTEgLTExNSAtMTE4IC0xNDIgLTE1OSAtNjkKLTEwNiAtMTQzIC0yNjAgLTE0MyAtMjk4IDAgLTExIC01IC0yOSAtMTEgLTQwIC02IC0xMSAtMTQgLTU3IC0xOSAtMTAyIC00Ci00NSAtMTMgLTgzIC0xOSAtODUgLTYgLTIgLTExIC0xNiAtMTEgLTMwIDAgLTE5IDMgLTIzIDkgLTEzIDEwIDE2IDMxIDYgMzEKLTE0IDAgLTEyIDE1NSAtMTQgMTAzOSAtMTQgbDEwMzkgMCAtMiA4MiBjLTQgODggLTE1IDE0NCAtNTEgMjQ3IC02NCAxODMKLTEzMCAyODYgLTI3NCA0MjggbC0xMDQgMTAyIDIzIDUwIGMxMyAyOCAzMiA1NyA0MiA2NyAxMCA5IDE4IDIyIDE4IDI5IDAgNwoxMSAzMCAyNSA1MCAyNiAzOCAzNCAxMDcgMTUgMTE5IC01IDMgLTEwIDE1IC0xMCAyNiAwIDI2IC0xNCAyNCAtMjkgLTMgLTIwCi0zOSAtMzYgLTY1IC00OCAtNzkgLTYgLTggLTI4IC00NSAtNDggLTg0IC0zOSAtNzMgLTcxIC0xMTUgLTg4IC0xMTUgLTYgMAotNDAgMTMgLTc2IDI5IC0xNjMgNzAgLTI2NiA5MiAtNDQxIDkyIC0xMjIgMCAtMjg5IC0yNCAtMzM1IC00OCAtMTEgLTYgLTMzCi0xNSAtNTAgLTIxIC0xNiAtNSAtNTQgLTIwIC04MyAtMzMgLTY3IC0yOSAtNzQgLTI1IC0xMzIgNzcgLTI1IDQzIC01NyA5OAotNzIgMTIxIC0xNiAyNCAtMjggNTIgLTI4IDYzIDAgMTEgLTcgMjAgLTE1IDIwIC04IDAgLTE1IC04IC0xNSAtMTl6IG00MTAKLTU0MiBjMzkgLTI5IDcwIC04NyA3MCAtMTMxIC0xIC03NCAtODEgLTE1NSAtMTU1IC0xNTcgLTE2IDAgLTQ4IDkgLTcwIDIyCi0xMjQgNzAgLTExMSAyMjMgMjUgMjg5IDI3IDE0IDk2IDEgMTMwIC0yM3ogbTc5MCA3IGM1MSAtMjcgOTAgLTg0IDkwIC0xMzIgMAotMzYgLTIyIC05NSAtNDIgLTExMSAtMzIgLTI2IC05MSAtNTMgLTExNiAtNTMgLTM4IDAgLTEwMiAzOSAtMTI5IDc5IC00MiA2MgotMjQgMTYxIDM1IDE5OSAxNSA5IDI5IDE5IDMyIDIzIDIwIDI1IDc4IDIzIDEzMCAtNXoiLz4KPHBhdGggZD0iTTU5MiAzMDUxIGMtNjQgLTIyIC0xMTQgLTY4IC0xNDcgLTEzNSBsLTI1IC01MCAwIC01ODcgMCAtNTg3IDMzCi02MiBjMjYgLTUwIDQ0IC02OSA5MSAtOTggNTEgLTMyIDY1IC0zNiAxMjUgLTM2IDQ4IDAgODAgNiAxMTMgMjEgNzUgMzYgMTMyCjEyMCAxMjIgMTc5IC0zIDIwIC0xIDI1IDUgMTUgMjggLTQzIDMxIDggMzEgNTcwIDAgNTU3IDAgNTcwIC0yMCA1ODYgLTExIDEwCi0yMCAyNyAtMjAgMzkgMCA0MiAtNTggMTA0IC0xMjYgMTM1IC03NCAzNCAtMTA4IDM1IC0xODIgMTB6Ii8+CjxwYXRoIGQ9Ik0zNTYyIDMwNTAgYy0zMCAtMTAgLTY5IC0zMyAtODkgLTUxIC03MiAtNzAgLTY4IC0yOCAtNzEgLTY5NyAtMgotMzg4IDEgLTYxNiA3IC02NDAgMTQgLTQ5IDcyIC0xMTcgMTI1IC0xNDQgNTggLTMxIDE2MCAtMjkgMjIyIDIgNjUgMzQgMTI0CjExMSAxMjUgMTY0IDEgMzUgMCAxMDcwIC0xIDExOTYgMCA0MCAtNiA1MiAtNDIgOTMgLTc2IDgzIC0xNzUgMTExIC0yNzYgNzd6Ii8+CjxwYXRoIGQ9Ik0xMDg5IDI5MjcgYy02IC0xMiAtMiAtMTk3MCA0IC0xOTc0IDQgLTIgMTAgMyAxNCAxMiA1IDE2IDcgMTYgMTUgMQo3IC0xMyAyNyAtMTYgMTA2IC0xNiA1MyAwIDEwNCAtNSAxMTIgLTEwIDEzIC04IDE2IC02NCAyMCAtMzY3IDQgLTI4OCA3IC0zNTQKMTcgLTMzOCAxMSAxNyAxMiAxMiAxMyAtMzggMCAtNTMgMyAtNjEgNDAgLTEwMSAyNCAtMjYgNTkgLTUwIDg4IC02MSAyNiAtMTAKNTEgLTIyIDU3IC0yNiAxNiAtMTUgNjQgLTEwIDEwNSAxMSAyMiAxMSA0NiAyMCA1NSAyMCAxOCAwIDczIDUyIDEwMSA5NSAxOAoyNyAxOSA1NyAyMiA0MDcgbDMgMzc4IDI5IDE1IGMyMyAxMiA3NiAxNSAyNzIgMTUgMTM1IDAgMjUwIC00IDI1OCAtMTAgMTMgLTgKMTUgLTYyIDE4IC0zNjIgMiAtMTk0IDcgLTM1NyAxMSAtMzYzIDUgLTUgMTEgLTIzIDE1IC00MCA5IC0zNSA5MSAtMTM1IDExMgotMTM1IDggMCAzNCAtOSA1NiAtMjAgNTQgLTI2IDc2IC0yNSAxNDIgNSAzMCAxNCA1OCAyNSA2MyAyNSAxNSAwIDYzIDU5IDgzCjEwMiAxOCA0MCAyMCA2NyAyMCA0MDAgMCAzMTAgMiAzNTkgMTYgMzcyIDIxIDIyIDQzIDI2IDE1NCAyOSBsOTUgMiA1IDk3MyBjMwo1MzUgMSA5ODAgLTMgOTg4IC03IDEyIC0xNTMgMTQgLTEwNjMgMTQgLTU4MCAwIC0xMDU0IC0yIC0xMDU1IC0zeiBtMTYwNQotNDE4IGM1MCAtNDMgMTI0IC0xNDQgMTUzIC0yMDcgMjAgLTQzIDI0IC02NCAyMCAtMTEwIC02IC02NiAtMjIgLTE0MyAtMzYKLTE2OSAtNSAtMTAgLTE5IC00NyAtMzEgLTgzIC0xMSAtMzYgLTI0IC03NCAtMjkgLTg1IC0xMyAtMzEgLTMyIC04NyAtNDMKLTEyNSAtNSAtMTkgLTE0IC0zOSAtMTkgLTQ1IC01IC01IC05IC0xOCAtOSAtMjggMCAtMTAgLTYgLTMzIC0xMyAtNTAgLTE4Ci00MSAtMzcgLTk0IC00NyAtMTMyIC0yMCAtNzAgLTQ1IC0xMzIgLTY3IC0xNjggLTQwIC02NCAtMTc1IC0xMTQgLTE4OSAtNjkKLTMgOSAtMTIgMjkgLTIwIDQ0IC04IDE0IC0xNCAzNyAtMTQgNTAgMCAxMiAtNyAzMiAtMTUgNDIgLTggMTEgLTE1IDI5IC0xNQo0MCAwIDEyIC03IDMwIC0xNSA0MCAtOCAxMSAtMTUgMjggLTE1IDM4IDAgMTAgLTQgMjYgLTEwIDM2IC0xMiAyMSAtNDAgOTgKLTQ3IDEzMiAtMyAxNCAtMTAgMzAgLTE0IDM2IC01IDYgLTE2IDM1IC0yNSA2MyAtMTcgNTkgLTMwIDY1IC0zNyAxOSAtMyAtMTgKLTIxIC03OCAtNDIgLTEzMyAtMjAgLTU1IC00MCAtMTEzIC00NSAtMTMwIC0xMSAtNDAgLTMwIC05MyAtNDYgLTEzMyAtOCAtMTgKLTE0IC00MCAtMTQgLTUwIDAgLTkgLTcgLTMwIC0xNSAtNDYgLTggLTE1IC0xNSAtMzcgLTE1IC00NyAwIC00NiAtNjMgLTQ0Ci0xNDcgNCAtMzEgMTggLTgzIDEwMSAtODMgMTMyIDAgNyAtNyAyNiAtMTUgNDEgLTggMTYgLTE1IDM3IC0xNSA0NiAwIDkgLTQKMTkgLTkgMjMgLTUgMyAtMTIgMTkgLTE2IDM2IC00IDE3IC0xMyA0MiAtMjEgNTcgLTggMTUgLTE0IDM1IC0xNCA0NCAwIDkgLTQKMTkgLTkgMjMgLTUgMyAtMTMgMjAgLTE3IDM4IC0zIDE3IC0xMiA0NSAtMTkgNjIgLTE0IDMyIC0zOSA5OCAtNjAgMTU1IC03IDE5Ci0yMCA1NCAtMjkgNzggLTkgMjMgLTIzIDYxIC0zMiA4NSAtOSAyMyAtMjIgNTYgLTI5IDcyIC0yOCA2MiAtMjggMTAxIDAgMTU4CjE1IDMwIDM3IDY2IDQ5IDgyIGwyMiAyOCAxMzUgLTIgYzExMSAtMSAxMzYgLTUgMTQ3IC0xOCAyOCAtMzggNyAtNjQgLTU3IC03MQotNTcgLTUgLTYzIC0xNyAtMzYgLTY4IDggLTE2IDE1IC0zOCAxNSAtNTAgMCAtMTEgNCAtMjQgOSAtMzAgNSAtNSAxNCAtMjUgMTkKLTQ0IDYgLTE5IDE2IC01MSAyMyAtNzAgNiAtMTkgMTcgLTUxIDI0IC03MCA3IC0xOSAxOCAtNTEgMjQgLTcwIDcgLTE5IDE2Ci00OCAyMSAtNjUgMTAgLTM3IDI5IC04OSA0NyAtMTMyIDcgLTE3IDEzIC0zOCAxMyAtNDYgMCAtOCA3IC0yOCAxNSAtNDMgOAotMTYgMTUgLTQxIDE1IC01NiAwIC0xNiA0IC0yOCA4IC0yOCAxMSAwIDMyIDQ1IDMyIDY4IDAgOSA3IDMwIDE1IDQ2IDggMTUgMTUKMzcgMTUgNDggMCAxMSA1IDI5IDEyIDQxIDYgMTIgMTUgMzUgMTkgNTIgNCAxNiAxOSA2MyAzNCAxMDQgMTUgNDEgMjUgODMgMjIKOTUgLTUgMjIgLTI0IDc2IC0zOCAxMTEgLTE2IDM4IC00MCAxMTAgLTQ4IDE0MSAtMTIgNDcgLTE5IDU0IC01MiA1NCAtNDkgMAotODkgMjYgLTg5IDU5IDAgMTkgNyAzMSAyMyAzNyAxMyA2IDExNyA4IDI1NyA0IGwyMzUgLTUgMCAtNDAgYzAgLTQyIC00IC00NQotNzcgLTYwIC0yOSAtNiAtMjkgLTQgMyAtODAgMTIgLTI3IDY4IC0xOTMgNzkgLTIzMCA0IC0xNiAxOCAtNTcgMzAgLTkwIDExCi0zMyAyNSAtNzMgMzAgLTkwIDEwIC0zOCAyOSAtOTEgNDcgLTEzMiA3IC0xNyAxMyAtMzYgMTMgLTQyIDAgLTcgNyAtMjggMTYKLTQ5IGwxNiAtMzcgMTMgNTAgYzEyIDQ4IDI0IDg5IDYwIDIwNSAzOCAxMjcgNDYgMTkxIDM0IDI2NSAtOCA0OCAtMjAgODEgLTM4CjEwNSAtMTEwIDE0NCAtMTIxIDI4MSAtMjkgMzUyIDM0IDI2IDYzIDIyIDEwMiAtMTN6IG00NDUgLTU2MSBjMCAtNzEgLTMgLTg3Ci0xMCAtNjggLTE0IDM0IC04IDE2MCA3IDE2MCAyIDAgNCAtNDIgMyAtOTJ6Ii8+CjwvZz4KPC9zdmc+Cg==");
		
		add_submenu_page( $this->plugin_options_key, 'Look & Feel', __('Look & Feel','androapp'), 'manage_options', $this->plugin_options_key.'&tab=pw-mobile-build-options', array($this, 'plugin_options_page'));
		
		add_submenu_page( $this->plugin_options_key, 'Configure', __('Configure','androapp'), 'manage_options', $this->plugin_options_key.'&tab=pw-mobile-app', array($this, 'plugin_options_page'));
		
		add_submenu_page( $this->plugin_options_key, 'Internationalization', __('Internationalization','androapp'), 'manage_options', $this->plugin_options_key.'&tab=pw-mobile-app-language', array($this, 'plugin_options_page'));
		
		add_submenu_page( $this->plugin_options_key, 'Account Settings', __('Account Settings','androapp'), 'manage_options', $this->plugin_options_key.'&tab=androapp_account_settings', array($this, 'plugin_options_page'));
		
		add_submenu_page( $this->plugin_options_key, 'Push Notifications', __('Push Notifications','androapp'), 'manage_options', $this->plugin_options_key.'&tab=androapp_tools_tab', array($this, 'plugin_options_page'));
                
                add_submenu_page( $this->plugin_options_key, 'IOS Build', __('IOS Build','androapp'), 'manage_options', $this->plugin_options_key.'&tab=ios_build_settings', array($this, 'plugin_options_page'));
	}

	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
                if(!current_user_can('manage_options')){
                    print "Sorry, you don't have sufficient priviledges";
                    exit;
                }
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $this->get_started_option_name;
                  
		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
	
	function add_scripts($todo) {
		//echo "<h2>Add Scripts</h2>";
		//print_r($todo);
		if(is_single()){
			$this->process_scripts($todo);
		}
		return $todo;
	}
	
	function add_late_scripts() {
		global $wp_scripts;
		if(is_single()){
			$this->process_scripts($wp_scripts->queue);
		}
	}
	
	function process_scripts($scriptsHandle){
		global $wp_scripts;
		$options = get_option($this->scripts_option);
		$scripts = $options ['scripts'];
		//print_r($scripts);
		if(empty($scripts)){
			$scripts = array();
		}
		$scripts = $scripts + $this->process_scripts_inner($scriptsHandle);
		
		$options['scripts'] = $scripts;
		$options['count'] = $this->scriptOptions['count'];
		update_option($this->scripts_option, $options);
	}

	function process_scripts_inner($scriptsHandle){
		global $wp_scripts;
		$scripts = array();
		foreach( $scriptsHandle as $handle ) :
			//echo $handle."</br>";
			if(!empty($wp_scripts->registered[$handle]->src))
			{
				$scripts[$handle] = convertToFullUrl($wp_scripts->registered[$handle]->src);
			}else{
				$deps = $wp_scripts->registered[$handle]->deps;
				$scripts  = $scripts +  $this->process_scripts_inner($deps);
			}
		endforeach;
		return $scripts;
	}
	
	function clearScripts(){
		$this->scriptOptions['scripts'] = null;
		$this->scriptOptions['count'] = 0;
		update_option($this->scripts_option, $this->scriptOptions);
		
		header("Location: ?page=pw_mobile_app_options");
	}
	
	function renderForm($tab, $functionname){
	?>
		<form name="pwappsettingsform" id="pwappsettingsform"  method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			
			<?php 
			if($tab == 'androapp_getstarted' ){
				settings_fields( "pw-mobile-build-options" ); 
			}else{
				settings_fields( $tab ); 
			}
			?>
			<?php 
			
			if(!empty($functionname)){
				$this->$functionname();
			}else{
				do_settings_sections( $tab ); 
			}
				
			?>
			<?php //submit_button(); ?>
		</form>
		<?php
	}
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
	
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : $this->get_started_option_name;
		
		if(isset($_GET['clear_scripts_list'])){
			$this->clearScripts();
		}
		
		?>
		<div class="wrap">
		
			<?php $this->plugin_options_tabs();

			if($tab == 'androapp_getstarted' || $tab == $this->build_option_name || 
			$tab == $this->language_option_name || $tab == 'androapp_tools_tab'
			|| $tab == 'androapp_post_content_tab'){
				do_settings_sections( $tab ); 
			}
			else {
				$this->renderForm($tab, "");
			}
			?>
		</div>
		<?php
	}

	function publish_do_page($options) {

		$clientId = $options[ANDROAPP_CLIENT_ID];
		if(isset($clientId) && !empty($clientId) && function_exists('file_get_contents')){
			$res = file_get_contents($this->androAppHost.'/appCreator/info.php?clientId='.$clientId);
			$res = json_decode($res);
			if(isset($res->vt)){
				$renewaldate = $res->vt;
				$options[ANDROAPP_RENEWAL_DATE] = strtotime($renewaldate);
								
				$renewaldate = date("d-m-Y", $options[ANDROAPP_RENEWAL_DATE]);
				update_option($this->build_option_name, $options);
			}
		}
	
		_e('You need to submit your app to Google App Store such that your readers can install your mobile app.','androapp');
		?>
		</br></br>
		<?php _e('You have two options','androapp');?>
		<ol>
		<li>
		<p style="font-size:17px;">
		<b><?php _e('Submit it yourself','androapp');?></b></br>
		<?php
		$googleConsoleLink = '<a href="https://play.google.com/apps/publish/">'. __('Google play developer console','androapp').'</a>';
		printf(
		/* translators: %s: Google Play developer console link */
		__('Create a new account on %s and submit your app yourself. 
		You need to pay one time fee of $25. Generally it takes around 48 hours (after the payment) for new accounts to get approved.',
		'androapp'),
		$googleConsoleLink);
		?>
		</p>
                While uploading your apk to google play store remember to not sign up for App Signing as we are taking care of that for you: <a target="_blank" href="https://androapp.mobi/blog/google-play-upload-failed-due-sign-issue/418">click here</a> for more info.
                
		</li>
		<li>
		<p style="font-size:17px;">
		<b><?php _e('Leave it onto us','androapp');?></b></br>
		<?php _e('We can submit your app from our account, ownership of the app can be transferred to you later at any point of time.',
		'androapp');?>
	</br>
		<?php _e('We will need following details from you','androapp');?>
		<ol>
		<li><?php _e('One Application Icon of 512×512 size, same which is used while creating the app','androapp');?></li>
		<li><?php _e('One feature graphic of 1024×500 size','androapp');?></li>
		<li><?php _e('App Description','androapp');?></li>
		<li><?php _e('2-3 app screenshots','androapp');?></li>
		<li><?php _e('apk link which you tested','androapp');?></li>
		<li>
		<?php
		$payumoneyLink = '<a href="https://www.payumoney.com/store/product/81b7c4c1fc01c86d9e3d9780cea49633" target="_blank" >'.
		__('click here','androapp').'</a>';
		printf(
		/* translators: %s: click here */
		__('payment of $10 for our efforts, %s to make the payment','androapp'),
		$payumoneyLink);
		?>
		</li>
		</ol>
		<?php 
		printf(
		/* translators: %s: our Email */
		__('For more details, drop an email @ %s','androapp'),
		'<b>contact@androapp.mobi</b>');
		?>
		</p>
		</li>
		</p>
		</ol>
		
		<?php
			global $options;
			if(!empty($options[ANDROAPP_PACKAGE_NAME])){
				$applinktext = "<a target='_blank' href='https://play.google.com/store/apps/details?id=".$options[ANDROAPP_PACKAGE_NAME]."'>https://play.google.com/store/apps/details?id=".$options[ANDROAPP_PACKAGE_NAME]."</a>";
				
				echo '<p style="font-size:17px;">'.
				printf(
				/* translators: %s: apk link on play store */
				__('After you submit your app to Google Play Store, this will be the link to your app %s',
				'androapp'),
				$applinktext).'</p>';
			}
		?>
		<h2><?php _e('Annual Renewal','androapp');?></h2>
		<p style="font-size:17px;">
		<?php 
		$purchaseLink = '<a href="https://www.payumoney.com/store/product/4a48ec6c814b2f0a8f0e87d426ece891" target="_blank">here</a>';
		printf(
		/* translators: %s: here (with payment link) */
		__('AndroApp is completely free for the first month, after that you can purchase annual subscription from %s. Mention your email id and site link in shipping address. We will activate your annual subscription.',
		'androapp'),
		 $purchaseLink);
		 ?>
		<br/><br/>
		<?php 
		if(isset($renewaldate)){
			echo "<b>".__('Your Renewal date: ','androapp')."</b>".$renewaldate;
			echo "<br/><br/>";
		}
		
		printf(
		/* translators: %s: our email */
		__('Please get in touch with us @ %s for any more clarifications.','androapp'),
		'<b>contact@androapp.mobi</b>');
		?>
		</p>
	<?php
	}

	function schedule_push_notification($id, $title, $excerpt, $postimage, $link, $cache, $postType, $key, $isBulkSent){
            wp_schedule_single_event( time()+ 10*30 , 'send_push_notification_after_publish', 
                    array ($id, $title, $excerpt, $postimage, $link, $cache, $postType, $key , "stack", $isBulkSent )); 
	}
	
	function send_push_notification_after_publish($post_id, $post_title, 
                $excerpt, $postimage, $postlink, $cache, $postType,
                $google_api_key, $notification_type, $isBulkSend)
	{
		require_once PW_MOBILE_PATH.'gcm/send_message.php';
		sendPushNotification(array("post_id" => $post_id, "title" => $post_title,
                     "excerpt" =>$excerpt, "postImage" => $postimage, "link" => $postlink, "cache" => $cache,
                    "postType" => $postType,
                    "notification_type" => $notification_type), $google_api_key, $isBulkSend);
	}
	
	function post_content_do_page_parent(){
		$this->renderForm($this->post_content_tab_key ,'post_content_do_page');
	}
	
	function post_content_do_page(){
		include("androapp_postcontent.php");
	}
	
	function tools_do_page() {
		include("androapp_tools.php");
	}
        
        function ios_do_page(){
            include("ios_build.php");
        }
        
	function accounts_do_page() {
		$options = get_option($this->account_tab_key);
		?>
		
		<h2><?php _e('Push Notification Settings','androapp');?></h2>
		<table class="form-table" border="1">				
			<tr valign="top"><th scope="row">Google API Key:</th>
				<td>
				<?php
				$androAppGcmLink = '<a href="https://androapp.mobi/blog/setup-firebase-cloud-messaging/182" target="_blank" >'.__('Click here','androapp').'</a>';
				printf(
				/* translators: %s: Click here */
				__('We use Firebase cloud messaging API\'s for push notifications, %s to see the instructions to create a Firebase API project',
				'androapp'),
				$androAppGcmLink);?>
				</br></br>
				<input type="text" name="<?php echo $this->account_tab_key."[".ANDROAPP_GCM_API_KEY."]"?>" value="<?php echo $options[ANDROAPP_GCM_API_KEY];?>"  />
				
				<?php
					if(!function_exists('curl_init')){
						echo "<p style='color:red' >".__('Please install curl for push notifications to work.','androapp')."</p>";
					}
				?>
				
				</td>
				
			</tr>
			<tr valign="top"><th scope="row"><?php _e('Google Project Number','androapp');?>:</th>
				<td>
				<?php _e('Enter your Google Project Number for the project created in above step.','androapp');?>
				<p style="color:red"><?php _e('Note: You need to publish new build everytime you change it','androapp');?></p>
				<input type="text" name="<?php echo $this->account_tab_key."[".ANDROAPP_GOOGLE_SENDER_ID."]"?>" value="<?php echo $options[ANDROAPP_GOOGLE_SENDER_ID];?>"  />
				</td>
			</tr>
			
			<tr valign="top"><th scope="row"><?php _e('Firebase APP ID','androapp');?>:</th>
				<td>
				<?php _e('Enter your Google App ID for the project created in above step.','androapp');?>
				</br>
                                
				<b>For Android App</b>: <input type="text" name="<?php echo $this->account_tab_key."[".ANDROAPP_GOOGLE_APP_ID."]"?>" value="<?php echo $options[ANDROAPP_GOOGLE_APP_ID];?>"  />
				<br/>
                                Create one more Firebase app for IOS in same project, <a href="https://androapp.mobi/blog/create-firebase-app-ios/247" target="_blank">click here</a> for instructions.<br/>
                                <b>For IOS App</b>: <input type="text" name="<?php echo $this->account_tab_key."[".ANDROAPP_IOS_APP_ID."]"?>" value="<?php echo $options[ANDROAPP_IOS_APP_ID];?>"  />
                                <p style="color:red"><?php _e('Note: You need to publish new build everytime you change it','androapp');?></p>
				
                                </td>
			</tr>
			
			
			<tr valign="top"><th scope="row"><?php _e('Other Notification Settings','androapp');?>:</th>
				<td>
				<?php 
				_e('We send auto push notification whenever you publish a new post, you can control this on Post Edit page by <b>Do not send Push Notification</b> checkbox.<br/>
				Set the default value for that checkbox here (if checked, push notification will not be sent by default)','androapp');?></br>
				<input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$defaultPostPushNotification."]"?>" value="1" <?php if($options[pw_mobile_app_settings::$defaultPostPushNotification] == '1') echo "checked"; ?> /><b>
				<?php _e('Don\'t send push notification','androapp');?></b>
				<br/><br/>
				<b>Bulk Send:</b>
				We introduced bulk notification send from 6.05, by default it is enabled. it is recommended, but does not give detailed info on successful sent count.
				<br/>
				You can disable it to get the correct count, but not recommended for performance reasons.<br/>
				<input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$disableBulkSend."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$disableBulkSend] == '1') echo "checked"; ?> /><b>Disable Bulk Send</b>
                                
                                <br/><br/>
				<b>Notification Cache:</b>
                                When a push notification is received by end user, by default we cache the post data on phone, so that even if user is offline, when he clicks on the notification, post can be opened(for post content type: preprocessed/postprocessed).<br/>
                                <b>But</b>, this might slow down your server, So if your server is not able to handle the load, you can disable the cache.
                                <br/>By disabling the cache, post data will be fetched only when user clicks on the notification.<br/>
                                <input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$disableNotificationCache."]"?>" 
				value="yes" <?php if($options[pw_mobile_app_settings::$disableNotificationCache] == 'yes') echo "checked"; ?> /><b>Disable Notification Cache</b>
                                
                                <br/><br/>
                                <b>Notification Stacking</b> <span style="color:red;font-size:small;">(* since version 14.01)</span>: 
                                Android has the capability of stacking push notifications.For multiple notifications, we can club all notifications in 1 instead of showing them individually.
                                <br/>It's a good user experience.
                                By default we stack them after 3 unclicked notifications, you can change that setting by increasing this number.
                                <br/>
                                Stack them up after 
                                <select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$pushStackThershold."]"?>">
				<?php
					for ($x=3; $x<=25; $x++) {
						echo '    <option value="' . $x . '"  '.  (($options[pw_mobile_app_settings::$pushStackThershold] == $x) ?'selected' : '' ) .'>' . $x . '</option>' . PHP_EOL;
					}
				?>
                                </select> unclicked notifications.
				</td>
			</tr>
		</table>
		
		</br>

		<h2><?php _e('Analytics Settings','androapp');?></h2>
		<table class="form-table" border="1">				
			<tr valign="top"><th scope="row"><?php _e('Tracking ID','androapp');?>:</th>
				<td>
				<?php
				$androappAnalyticsLink = '<a href="https://androapp.mobi/blog/androapp-create-google-analytics-property/41" target="_blank" >'.__('Click here','androapp').'</a>';
				printf(
				/* translators: %s: Click here */
				__('To know pageviews, exceptions, number of shares, comments from the app, %s to create a google analytics account.'
				,'androapp'),
				$androappAnalyticsLink);
				?>
				</br>
				<?php _e('Add your tracking id (will look like  UA-XXXXXXXX-X) in the text box below.','androapp');?></br>
				<p style="color:red"><?php _e('Note: You need to publish new build everytime you change it','androapp');?></p>
				For Android App:<input type="text" name="<?php echo $this->account_tab_key."[".ANDROAPP_ANALYTICS_TRACKING_ID_KEY."]"?>" value="<?php echo $options[ANDROAPP_ANALYTICS_TRACKING_ID_KEY];?>"  />
                                </br>
                                For IOS App:<input type="text" name="<?php echo $this->account_tab_key."[".ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY."]"?>" value="<?php echo $options[ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY];?>"  />
				
                                </td>
			</tr>
		</table>
			
		
		<h2><?php _e('Monetization Settings','androapp');?></h2>
		<?php
		$admobLink = '<a href="http://www.google.com/admob/?subid=apac-sem&gclid=CjwKEAiA_s2lBRCe1YPXxtSe-DcSJACCIh3LASegyQrCjEeN1sz7PK_aCBHUg7A7j7H0xf2yHqqKyhoC_uvw_wcB" target="_blank" >Admob</a>';
		$mopubLink = '<a href="http://www.mopub.com" target="_blank" >Mopub</a>';
		$appNextLink = '<a href="https://www.appnext.com/" target="_blank">Appnext</a>';
		printf(
		/* translators: %1$s: Admob, %2$s: Mopub and %3$s:Appnext */
		__('We are using top performing mobile ad network <b>AdMob by Google</b>, <b>Mopub</b> and <b>AppNext</b>. Please create publishers account on %1$s, %2$s and %3$s and enter ad id\'s here.',
		'androapp'),
		$admobLink,
		$mopubLink,
		$appNextLink);
		?>
		<br/>
		<?php
		$androappAdGuideLink = '<a href="https://androapp.mobi/blog/ad-guidelines-for-androapp/78" target="_blank">'.__('Click here','androapp').'</a>';
		printf(
		/* translators: %s: Click here */
		__('You can use various ad providers using mopub on the fly, %s to read more about Ad guidelines and help in setting up the ads on your AndroApp)',
		'androapp'),
		$androappAdGuideLink);
		?>
		<table class="form-table" border="1">
			<tr valign="top"><th scope="row"><?php _e('Top Ad Unit','androapp');?>:</th>
				<td>
				Mopub
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$mopubTopAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$mopubTopAdUnitKey];?>"  /> 
				</br><?php _e('OR','androapp');?> Admob
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$topAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$topAdUnitKey];?>"  /> 
				<?php _e('of size','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$topAdType."]"?>">
					<option value="BANNER" <?php if($options[pw_mobile_app_settings::$topAdType] == "BANNER") echo "selected";?> >
					<?php _e('Banner','androapp');?></option>
					<option value="MEDIUM_RECTANGLE" <?php if($options[pw_mobile_app_settings::$topAdType] == "MEDIUM_RECTANGLE") echo "selected";?>>
					<?php _e('Medium Rectangle','androapp');?></option>
					<option value="LARGE_BANNER" <?php if($options[pw_mobile_app_settings::$topAdType] == "LARGE_BANNER") echo "selected";?>>
					<?php _e('Large Banner','androapp');?></option>
					<option value="SMART_BANNER" <?php if($options[pw_mobile_app_settings::$topAdType] == "SMART_BANNER") echo "selected";?>>
					<?php _e('Smart Banner','androapp');?></option>
				</select>
                                
                                <br/><?php _e('OR','androapp');?> AppNext
                                <input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$topAppNextAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$topAppNextAdUnitKey];?>"  /> 
                                <?php _e('of size','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$topAppNextAdType."]"?>">
					<option value="BANNER" <?php if($options[pw_mobile_app_settings::$topAppNextAdType] == "BANNER") echo "selected";?> >
					<?php _e('Banner','androapp');?></option>
					<option value="MEDIUM_RECTANGLE" <?php if($options[pw_mobile_app_settings::$topAppNextAdType] == "MEDIUM_RECTANGLE") echo "selected";?>>
					<?php _e('Medium Rectangle','androapp');?></option>
				</select>                           
                                <br/>
                                Show Only On
                                <input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$topAdShowOnListPages."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$topAdShowOnListPages] == '1') echo "checked"; ?> /><b>List Screen</b>
                                
				<input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$topAdShowOnSinglePages."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$topAdShowOnSinglePages] == '1') echo "checked"; ?> /><b>Single Post/Page Screen</b>
                                
				</td>
			</tr>
                        
                        	<tr valign="top"><th scope="row"><?php _e('List View/Middle Ad Unit','androapp');?>:</th>
				<td>
				Mopub
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$mopubMiddleAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$mopubMiddleAdUnitKey];?>"  /> 
				<br/>
				<?php _e('OR','androapp');?> Admob
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$listViewAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$listViewAdUnitKey];?>"  /> 
				<?php _e('of size','androapp');?> 
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$listViewAdUnitTypeKey."]"?>">
					<option value="BANNER" <?php if($options[pw_mobile_app_settings::$listViewAdUnitTypeKey] == "BANNER") echo "selected";?> >
					<?php _e('Banner','androapp');?></option>
					<option value="MEDIUM_RECTANGLE" <?php if($options[pw_mobile_app_settings::$listViewAdUnitTypeKey] == "MEDIUM_RECTANGLE") echo "selected";?>>
					<?php _e('Medium Rectangle','androapp');?></option>
					<option value="LARGE_BANNER" <?php if($options[pw_mobile_app_settings::$listViewAdUnitTypeKey] == "LARGE_BANNER") echo "selected";?>>
					<?php _e('Large Banner','androapp');?></option>
					<option value="SMART_BANNER" <?php if($options[pw_mobile_app_settings::$listViewAdUnitTypeKey] == "SMART_BANNER") echo "selected";?>>
					<?php _e('Smart Banner','androapp');?></option>
					<option value="FULL_WIDTH" <?php if($options[pw_mobile_app_settings::$listViewAdUnitTypeKey] == "FULL_WIDTH") echo "selected";?>>
					<?php _e('Full Width','androapp');?></option>
				</select>
                                <br/><?php _e('OR','androapp');?> AppNext
                                <input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$listViewAppNextAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$listViewAppNextAdUnitKey];?>"  /> 
                                <?php _e('of size','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$listViewAppNextAdUnitTypeKey."]"?>">
					<option value="BANNER" <?php if($options[pw_mobile_app_settings::$listViewAppNextAdUnitTypeKey] == "BANNER") echo "selected";?> >
					<?php _e('Banner','androapp');?></option>
					<option value="MEDIUM_RECTANGLE" <?php if($options[pw_mobile_app_settings::$listViewAppNextAdUnitTypeKey] == "MEDIUM_RECTANGLE") echo "selected";?>>
					<?php _e('Medium Rectangle','androapp');?></option>
				</select>  
                                
				
				</br></br> <?php _e('show it after every','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$listViewAdUnitFreqKey."]"?>">
				<?php
					for ($x=5; $x<=25; $x++) {
						echo '    <option value="' . $x . '"  '.  (($options[pw_mobile_app_settings::$listViewAdUnitFreqKey] == $x) ?'selected' : '' ) .'>' . $x . '</option>' . PHP_EOL;
					}
				?>
                                </select> 
				<?php _e('posts','androapp');?>.
                                <span style="font-size:0.7em">(for list view)</span>
                                
                                <br/>
                                Show Only On
                                <input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$middleAdShowOnListPages."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$middleAdShowOnListPages] == '1') echo "checked"; ?> /><b>List Screen</b>
                                
				<input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$middleAdShowOnSinglePages."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$middleAdShowOnSinglePages] == '1') echo "checked"; ?> /><b>Single Post/Page Screen</b>
                                
				</td>
			</tr>
                        
			<tr valign="top"><th scope="row"><?php _e('Bottom Ad Unit','androapp');?>:</th>
				<td>
				Mopub
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$mopubBottomAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$mopubBottomAdUnitKey];?>"  /> 
				</br><?php _e('OR','androapp');?> Admob
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$bottomAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$bottomAdUnitKey];?>"  /> 
				<?php _e('of size','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$bottomAdType."]"?>">
					<option value="BANNER" <?php if($options[pw_mobile_app_settings::$bottomAdType] == "BANNER") echo "selected";?> >
					<?php _e('Banner','androapp');?></option>
					<option value="MEDIUM_RECTANGLE" <?php if($options[pw_mobile_app_settings::$bottomAdType] == "MEDIUM_RECTANGLE") echo "selected";?>>
					<?php _e('Medium Rectangle','androapp');?></option>
					<option value="LARGE_BANNER" <?php if($options[pw_mobile_app_settings::$bottomAdType] == "LARGE_BANNER") echo "selected";?>>
					<?php _e('Large Banner','androapp');?></option>
					<option value="SMART_BANNER" <?php if($options[pw_mobile_app_settings::$bottomAdType] == "SMART_BANNER") echo "selected";?>>
					<?php _e('Smart Banner','androapp');?></option>
				</select>
                                <br/><?php _e('OR','androapp');?> AppNext
                                <input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$bottomAppNextAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$bottomAppNextAdUnitKey];?>"  /> 
                                <?php _e('of size','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$bottomAppNextAdType."]"?>">
					<option value="BANNER" <?php if($options[pw_mobile_app_settings::$bottomAppNextAdType] == "BANNER") echo "selected";?> >
					<?php _e('Banner','androapp');?></option>
					<option value="MEDIUM_RECTANGLE" <?php if($options[pw_mobile_app_settings::$bottomAppNextAdType] == "MEDIUM_RECTANGLE") echo "selected";?>>
					<?php _e('Medium Rectangle','androapp');?></option>
				</select>   
                                <br/>
                                Show Only On
                                <input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$bottomAdShowOnListPages."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$bottomAdShowOnListPages] == '1') echo "checked"; ?> /><b>List Screen</b>
                                
				<input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$bottomAdShowOnSinglePages."]"?>" 
				value="1" <?php if($options[pw_mobile_app_settings::$bottomAdShowOnSinglePages] == '1') echo "checked"; ?> /><b>Single Post/Page Screen</b>
                                
				
				</td>
			</tr>
		
			<tr valign="top"><th scope="row"><?php _e('Interstitial Ad Unit','androapp');?>:</th>
				<td>
				<?php _e('Mopub FullScreen Ad id','androapp');?>:
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$mopubInterstitialAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$mopubInterstitialAdUnitKey];?>"  />
				</br>
				<?php _e('Appnext placement id','androapp');?>:
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$appNextInterstitialAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$appNextInterstitialAdUnitKey];?>"  />
				<?php _e('Appnext Ad Type','androapp');?>
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$appNextInterstitialAdType."]"?>">
					<option value="INTERSTITIAL_VIDEO" <?php if($options[pw_mobile_app_settings::$appNextInterstitialAdType] == "INTERSTITIAL_VIDEO") echo "selected";?> >
					<?php _e('INTERSTITIAL_VIDEO','androapp');?></option>
					<option value="FULL_SCREEN_VIDEO" <?php if($options[pw_mobile_app_settings::$appNextInterstitialAdType] == "FULL_SCREEN_VIDEO") echo "selected";?>>
					<?php _e('FULL_SCREEN_VIDEO','androapp');?></option>
				</select> (<?php _e('we recommend interstitial video','androapp');?>)
				
				<br/>
				<?php _e('Admob Ad id','androapp');?>:
				<input type="text" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$interstitialAdUnitKey."]"?>" value="<?php echo $options[pw_mobile_app_settings::$interstitialAdUnitKey];?>"  /> 
				(<?php _e('preference will be given in this order','androapp');?> mopub, appnext and than Admob)
				<br/>

				<?php _e('show it after every','androapp');?>
				
				<select name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$interstitialAdUnitFreqKey."]"?>">
				<?php
					for ($x=3; $x<=25; $x++) {
						echo '    <option value="' . $x . '"  '.  (($options[pw_mobile_app_settings::$interstitialAdUnitFreqKey] == $x) ?'selected' : '' ) .'>' . $x . '</option>' . PHP_EOL;
					}
				?>
				  </select>
				<?php _e('page screens','androapp');?>.
				<br/>
				(<?php _e('we recommend to fill all mopub, admob and appnext ids','androapp');?>)
				</td>
			</tr>
			
			<tr valign="top"><th scope="row"><?php _e('Strip Adsense Units','androapp');?>:</th>
				<td>
				<?php
				$googleAdsensePolicyLink = '<a href="https://support.google.com/admob/answer/2753860?rd=1" target="_blank">'.
				__('Google Adsense policy','androapp').'</a>';
				printf(
				/* translators: %s: Google Adsense Policy */
				__('%s does not allow to use adsense units in mobile app, select this checkbox if you want to strip adsense code from mobile app pages. (works only when post content type is pre processed or post processed)',
				'androapp'),
				$googleAdsensePolicyLink);
				?>
				<br/>
				<input type="checkbox" name="<?php echo $this->account_tab_key."[".pw_mobile_app_settings::$stripAdsenseUnits."]"?>" value="1" <?php if(isset($options[pw_mobile_app_settings::$stripAdsenseUnits]) && $options[pw_mobile_app_settings::$stripAdsenseUnits] == '1') echo "checked"; ?> />
				<b><?php _e('Remove Adsense Units on Mobile App Pages','androapp');?></b>
				</td>
			</tr>
		</table>
		
		<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
				
			<?php
	}
	
	function print_checkMark(){
		return "<span class=\"checkmark\">&#x2713;</span>";
	}
	
	function isTimeZoneError(){
		try{
			$this->androapp_json_get_timezone();
			return false;
		}catch(Exception $e){
			return true;
		}
		return false;
	}
	
	function isAndroAppHtAccessRulesPresent(){
		$home_path = trailingslashit( get_home_path() );
		$rules = implode( "\n", extract_from_markers( $home_path.'.htaccess', 'AndroApp' ) );
		return !empty($rules);
	}
	
	function androapp_update_htaccess() {
		$home_path = trailingslashit( get_home_path() );
		$wprules = implode( "\n", extract_from_markers( $home_path.'.htaccess', 'WordPress' ) );
		$rules = "<IfModule mod_rewrite.c>\n";
		$rules .= "RewriteEngine On\n";
		$rules .= "RewriteBase /\n";
		$rules .= "RewriteRule ^wp-json/(.*)/$ /?json_route=/$1 [NC,L,QSA]\n";
		$rules .= "RewriteRule ^wp-json/(.*)$ /?json_route=/$1 [NC,L,QSA]\n";
		$rules .= "</IfModule>";
		
		
		// remove original WP rules so SuperCache rules go on top
		if($this->wpsc_remove_marker( $home_path.'.htaccess', 'WordPress' )){
			$res = insert_with_markers( $home_path.'.htaccess', 'AndroApp', explode( "\n", $rules ));
			$res = $res & insert_with_markers( $home_path.'.htaccess', 'WordPress', explode( "\n", $wprules ));
			if($res)
			{
				return true;
			} else {
				return false;
			}
		}else{
			return false;
		}
	}

	//from wp super cache
	function wpsc_remove_marker( $filename, $marker ) {
		if (!file_exists( $filename ) || $this->is_writeable_ACLSafe( $filename ) ) {
			if (!file_exists( $filename ) ) {
				return '';
			} else {
				$markerdata = explode( "\n", implode( '', file( $filename ) ) );
			}

			$f = fopen( $filename, 'w' );
			$foundit = false;
			if ( $markerdata ) {
				$state = true;
				foreach ( $markerdata as $n => $markerline ) {
					if (strpos($markerline, '# BEGIN ' . $marker) !== false)
						$state = false;
					if ( $state ) {
						if ( $n + 1 < count( $markerdata ) )
							fwrite( $f, "{$markerline}\n" );
						else
							fwrite( $f, "{$markerline}" );
					}
					if (strpos($markerline, '# END ' . $marker) !== false) {
						$state = true;
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}

	// from legolas558 d0t users dot sf dot net at http://www.php.net/is_writable
	function is_writeable_ACLSafe($path) {

		// PHP's is_writable does not work with Win32 NTFS

		if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
			return $this->is_writeable_ACLSafe($path.uniqid(mt_rand()).'.tmp');
		else if (is_dir($path))
			return $this->is_writeable_ACLSafe($path.'/'.uniqid(mt_rand()).'.tmp');
		// check tmp file for read/write capabilities
		$rm = file_exists($path);
		$f = @fopen($path, 'a');
		if ($f===false)
			return false;
		fclose($f);
		if (!$rm)
			unlink($path);
		return true;
	}
		
	function isShowHtAccessRules($permalink_structure){
		return empty($permalink_structure) && !$this->isAndroAppHtAccessRulesPresent();
	}
	
	function options_do_getstarted(){
		$lookAndFeel = '<a href="?page=pw_mobile_app_options&tab=pw-mobile-build-options"><b>'.
				__('Look & Feel','androapp').'</b></a>';
		$configure = '<a href="?page=pw_mobile_app_options&tab=pw-mobile-app" ><b>'.
				__('Configure','androapp').'</b></a>';
		$internalization = '<a href="?page=pw_mobile_app_options&tab=pw-mobile-app-language" ><b>'.
				__('Internationalization','androapp').'</b></a>';
				
		$sectionTobeOpened = -1;
		$stepOneCompleted = false;
		$updateHtaccessFailed = false;
		if(isset($_POST['updatehtaccess']) && $_POST['updatehtaccess'] == "1"){
			$updateHtaccessFailed = !$this->androapp_update_htaccess();
		}
		$permalink_structure = get_option('permalink_structure');
		if(!$this->isTimeZoneError() && !$this->isShowHtAccessRules($permalink_structure))
                {
			$stepOneCompleted = true;
		}else{
			$sectionTobeOpened = 0;
		}
		global $options ;
		$options = get_option($this->build_option_name);
		$stepTwoCompleted = false;
		if(!empty($options[ANDROAPP_CLIENT_ID])){
			$stepTwoCompleted = true;
		}else{
			if($sectionTobeOpened == -1){
				$sectionTobeOpened = 1;
			}
		}
		
		$stepThreeCompleted = false;
		if(!empty($options[ANDROAPP_LAUNCHER_ICON])){
			$stepThreeCompleted = true;
		}
		else{
			if($sectionTobeOpened == -1){
				$sectionTobeOpened = 2;
			}
		}
		
		$accountOptions = get_option($this->account_tab_key);
		$stepFourCompleted = false;
		if(!empty($accountOptions[ANDROAPP_GOOGLE_SENDER_ID])){
			$stepFourCompleted = true;
		}else{
			if($sectionTobeOpened == -1){
				$sectionTobeOpened = 3;
			}
		}
		
		
		if($sectionTobeOpened == -1){
			$sectionTobeOpened = 4;
		}
		
	?>
		<h2><?php _e('Follow these 5 steps to see your mobile app in Google play store', 'androapp'); ?></h2>
		<div id="accordion">
		
  <h3 class="ui-androapp-step-header" role="tab"><span><b>1</b></span><?php if($stepOneCompleted) { echo $this->print_checkMark();}?>Preliminary Checks</h3>
  <div>
   <?php
		if($stepOneCompleted){
				?>
				<h2><?php _e('You don\'t have to do anything in this step.' , 'androapp'); ?></h2>
				<p style="font-size:17px;">
				
				</p>
				<h3><?php _e('Move over to the next Step.' ,'androapp');?></h3>
				<?php
		}else{
			if($this->isTimeZoneError())
			{
			?>
				<p style="font-size:17px;">
				<?php
				$optionsLink = '<a href="options-general.php#default_role" >' .__('click here', 'androapp').'</a>';
				printf(
				/* translators: %s: click here */
    				__( 'Wordpress REST APIs does not work correctly for your current timezone, %s to change it and come back.', 'androapp' ),
    				$optionsLink
					);
?>
				</br></br>
				<?php 
				$wpjsonlink = $options[ANDROAPP_HOST_NAME]."/wp-json";
				?>
				(<?php _e('Please change your timezone by +-30 minutes or more and see if this link works fine:','androapp');?> <a target="_blank" href="<?php echo $wpjsonlink;?>"><?php echo $wpjsonlink; ?></a>
			</p>
			<?php
                        }
			else if($this->isShowHtAccessRules($permalink_structure)){
			
				$home_path = trailingslashit( get_home_path() );
				?>
				<p style="font-size:17px;">
					<?php
					$linkOpen = '<a href="options-permalink.php" target="_blank">'.__('Permalink link structure from here','androapp').'</a> </br>';
					printf(
					/* translators: %s: Permanent link structure from here */
    __( 'Wordpress REST APIs does not work as expected in default link structure, if possible change your Permalink Structure(recommended) <b>OR</b> add this on top of your .htaccess file'
     ,'androapp'),
     $$linkOpen);
					
					?>
					<pre># BEGIN AndroApp
&lt;IfModule mod_rewrite.c&gt;
RewriteEngine On
RewriteBase /
RewriteRule ^wp-json/(.*)/$ /?rest_route=/$1 [NC,L,QSA]
RewriteRule ^wp-json/(.*)$ /?rest_route=/$1 [NC,L,QSA]
&lt;/IfModule&gt;
# END AndroApp</pre>
				
				<?php
				if($updateHtaccessFailed){
					echo "</br><p style='color:red'>
					
					". __('We could not modify your .htaccess file, please check the file permission.' , 'androapp') ."</p></br>";
				}
				?>
				</p>
				<?php
				echo '<form name="updatehtaccess" action="#modrewrite" method="post">';
				echo '<input type="hidden" name="updatehtaccess" value="1" />';
				echo '<div class="submit"><input class="button-primary" type="submit" id="updatehtaccess" value="'.
				__('Update Mod_Rewrite Rules', 'androapp') .'" /></div>';
				wp_nonce_field('updatehtaccess');
				echo "</form>";
			}
                }
   ?>
  </div>
  <h3 class="ui-androapp-step-header" ><span><b>2</b></span><?php if($stepTwoCompleted) { echo $this->print_checkMark();}?>
  
  <?php _e('Generate Android apk file','androapp');?> </h3>
  <div>
    <?php
		if(!$stepTwoCompleted)
		{
		?>
			<p style="font-size:17px;">
				<?php _e('Click below button to generate android apk file now' ,'androapp'); ?>
			</p>
		<?php
				$this->renderForm('androapp_getstarted' ,'render_invisibleForm');
		}else{
		?>
		
		<h2><?php _e('You have completed this step' , 'androapp'); ?></h2>
		
		<p style="font-size:17px;">
		<?php
		
		printf(
		/* translators: %s: androapp email */
		__('We sent your mobile app download link to your email id, please check your mail and install it on your android phone.</br>
		
		In case you did not receive any mail, Don\'t worry, just drop an email to us @ %s, we will be happy to help you.',
		'androapp'),
		'contact@androapp.mobi');
		?>
		</p>
		<h3><?php _e('And move over to the next Step.' ,'androapp');?></h3>
		
			<?php
		}
		
	?>
  </div>
  <h3 class="ui-androapp-step-header"><span><b>3</b></span><?php if($stepThreeCompleted) { echo $this->print_checkMark();}?>
  <?php _e('Configure Look and Feel and Other Settings','androapp');?> </h3>
  <div>
    <?php
    
		if($stepTwoCompleted && !$stepThreeCompleted){
			?>
			<p style="font-size:17px;color:red;">
				<?php
				printf(
				/* translators: %s: Look & Feel */
				__('If you haven\'t received your apk link yet, check your email in %s tab','androapp'),
				$lookAndFeel);
				?>
			</p>
			<?php
		}
		
		if($stepThreeCompleted){
			?>
			<h2><?php _e('You have completed this step, However', 'androapp');?></h2>
			<?php
		}
		
		
		
		?>
		<p style="font-size:17px;">
		<?php
			printf(
			/* translators: %s: Look & Feel */
			__('Go to %s tab to change App Name, Logo, Colors and email id, you can also generate build from there again. Make the changes here and restart your app to see the changes.',
			'androapp'),
			$lookAndFeel);
			echo '</br></br>';
			
			printf(
			/* translators: %s: Configure */
			__('Go to  %s tab to change dynamic settings, like Menu Items, Comments Settings, Image Preview options, Share Text options etc. These settings can be changed anytime, you don\'t really need to publish a new build everytime, just restart your app and browse, you will see the changes.',
			'androapp'),
			$configure);
			echo '</br></br>';
			printf(
			/* translators: %s: Internalization */
			__('Go to %s tab to change the texts used in app, you can use this section to change the texts to your own language, OR to change the default values to suit your website.',
			'androapp'),
			$internalization);
		?>
		</p>
			<h3><?php _e('And move over to the next Step.','androapp');?></h3>
		<?php
	
	?>
  </div>
  <h3 class="ui-androapp-step-header"><span><b>4</b></span><?php if($stepFourCompleted) { echo $this->print_checkMark();}?>
  <?php _e('Create /Google Messaging/Admob Accounts','androapp'); ?> </h3>
  <div>
		<?php 
		$accountSettings = '<a href="?page=pw_mobile_app_options&tab=androapp_account_settings">'.
				__('Account Settings','androapp').'</a>';
			if($stepFourCompleted){
				?>
				<h2><?php _e('You have completed this step, However','androapp');?></h2>
				<?php
			}
			?>
			<p style="font-size:17px;">
				<?php
				printf(
				/* translators: %s: Account Settings */
				__('Go to %s tab and' ,'androapp'),$accountSettings);
				?> 
				<ol>
				<li>
				<?php _e('update <b>Google Cloud Messaging for Android</b> api key and project number for Push Notifications to work.',
				'androapp');?> </li>
				<li><?php _e('Create Google AdMob account for monetizing your app and fill Ad id\'s.','androapp');?>
				</ol>
				</br>
			</p>
			<h3><?php _e('And move over to the next Step.','androapp');?></h3>
			<?php
		?>
  </div>
  <h3 class="ui-androapp-step-header"><span><b>5</b></span>
  <?php _e('Publish Your App on Google Play Store', 'androapp');?> </h3>
  <div>
  <p style="font-size:17px;">
	<?php
	
	printf(
	/* translators: %s: Look & Feel */
	__('You need to create a new apk after <b>Step 4</b>, Go to %s section to create the new apk for your mobile app.',
	'androapp'),
	$lookAndFeel);
	?>
	</br>
	<?php
		$this->publish_do_page($options);
	?>
  </p>
  </div>
</div>
  <script>
  
  jQuery(document).ready(function() {

    jQuery("#accordion").show().accordion({
		collapsible: true,
        active: <?php echo $sectionTobeOpened;?>,
        autoHeight: false
    });

    jQuery("#accordion div").css({ 'height': 'auto' });
});  
  
  </script>
  
		<?php
	}
	
	function render_invisibleForm(){
		global $options;
		$accountOptions = get_option($this->account_tab_key);
		
		$tagTextColor = $options[pw_mobile_app_settings::$tagTextColorKey];
		$tagBgColor = $options[pw_mobile_app_settings::$tagBgColorKey];
		$feedBgColor = $options[pw_mobile_app_settings::$feedBgColorKey];
		$feedTitleColor = $options[pw_mobile_app_settings::$feedTitleColorKey];
		$feedContentTextColor = $options[pw_mobile_app_settings::$feedContentTextColorKey];
		$screenBgColor = $options[pw_mobile_app_settings::$screenBgColorKey];
		$actionBarTitleColor = $options[pw_mobile_app_settings::$actionBarTitleColorKey];
		$actionBarBgColor = $options[pw_mobile_app_settings::$actionBarBgColorKey];
	
		?>
		
		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_APP_NAME."]"?>" value="<?php echo $options[ANDROAPP_APP_NAME];?>"/>
		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_HOST_NAME."]"?>" value="<?php echo $options[ANDROAPP_HOST_NAME];?>"  />
		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$actionBarBgColorKey."]"?>" type="color" value="<?php echo $actionBarBgColor;?>" />
		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$actionBarTitleColorKey."]"?>" type="color" value="<?php echo $actionBarTitleColor;?>" />

		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$screenBgColorKey."]"?>" type="color" value="<?php echo $screenBgColor;?>" />

		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$feedTitleColorKey."]"?>" type="color" value="<?php echo $feedTitleColor;?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$feedContentTextColorKey."]"?>" type="color" value="<?php echo $feedContentTextColor;?>" />

		<input type="hidden"  name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$feedBgColorKey."]"?>" type="color" value="<?php echo $feedBgColor;?>" />

		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$tagTextColorKey."]"?>" type="color" value="<?php echo $tagTextColor;?>" />

		<input type="hidden" name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$tagBgColorKey."]"?>" type="color" value="<?php echo $tagBgColor;?>" />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_GOOGLE_SENDER_ID."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_GOOGLE_SENDER_ID]) ? $accountOptions[ANDROAPP_GOOGLE_SENDER_ID] : '';?>"  />
		
		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_ANALYTICS_TRACKING_ID_KEY."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_ANALYTICS_TRACKING_ID_KEY]) ? $accountOptions[ANDROAPP_ANALYTICS_TRACKING_ID_KEY] : '';?>"  />
		
                <input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY]) ? $accountOptions[ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY] : '';?>"  />
                
		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_GOOGLE_APP_ID."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_GOOGLE_APP_ID]) ? $accountOptions[ANDROAPP_GOOGLE_APP_ID]: '';?>"  />

                <input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_IOS_APP_ID."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_IOS_APP_ID]) ? $accountOptions[ANDROAPP_IOS_APP_ID]: '';?>"  />
                
		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_AUTHENTICATION_KEY."]"?>" value="<?php echo $options[ANDROAPP_AUTHENTICATION_KEY];?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_EMAIL."]"?>" value="<?php echo $options[ANDROAPP_EMAIL];?>"  />

		<input type="submit" class="button-primary" value="<?php _e('Send me my Apk Link Now !')  ?>"  onClick='return loadXMLDoc();' />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_CLIENT_ID."]"?>" value="<?php echo $options[ANDROAPP_CLIENT_ID];?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_RENEWAL_DATE."]"?>" value="<?php echo $options[ANDROAPP_RENEWAL_DATE];?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_PLUGIN_URL."]"?>" value="<?php echo plugins_url();?>"  />
		
		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_PACKAGE_NAME."]"?>" value="<?php echo isset($options[ANDROAPP_PACKAGE_NAME]) ? $options[ANDROAPP_PACKAGE_NAME] : '';?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_LAUNCHER_ICON."]"?>" value="<?php echo isset($options[ANDROAPP_LAUNCHER_ICON]) ? $options[ANDROAPP_LAUNCHER_ICON] : '';?>"  />
                
                <input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_NOTIFICATION_ICON."]"?>" value="<?php echo isset($options[ANDROAPP_NOTIFICATION_ICON]) ? $options[ANDROAPP_NOTIFICATION_ICON] : '';?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_SPLASH_IMAGE."]"?>" value="<?php echo isset($options[ANDROAPP_SPLASH_IMAGE]) ? $options[ANDROAPP_SPLASH_IMAGE] : '';?>"  />
		
		<input type="hidden" name="<?php echo $this->build_option_name."[category_base]"?>" value="<?php echo get_option( 'category_base' );?>"  />

		<input type="hidden" name="<?php echo $this->build_option_name."[tag_base]"?>" value="<?php echo get_option( 'tag_base' );?>"  />
		
		<input type="hidden" name="<?php echo $this->build_option_name."[build_source]"?>" value="getstarted"  />
		
		<div id="responseDiv"></div>
		
		<script>		
		function loadXMLDoc()
		{
			var test = "0";
			jQuery.ajax({
			  url: "<?php echo $this->androAppHost;?>/appCreator/save.php",
			  type: "POST",
			  async: false,
			  data: jQuery( "#pwappsettingsform" ).serialize(),
			  success: function (data, status) {
				var jsonData = jQuery.parseJSON(data);
				if(jsonData && jsonData.ClientId != null && jsonData.ClientId != ''){
					document.pwappsettingsform.elements['pw-mobile-build-options[client_id]'].value = jsonData.ClientId;
					if(jsonData.PackageName != null){
						document.pwappsettingsform.elements['pw-mobile-build-options[package_name]'].value = jsonData.PackageName;
					}
					if(jsonData.ValidTill != null){
						document.pwappsettingsform.elements['pw-mobile-build-options[androapp_renewal_date]'].value = jsonData.ValidTill;
					}
					alert("Woohoo!\n You will shortly receive your mobile app download link @ <?php echo $options[ANDROAPP_EMAIL];?>");
					test = "1";
				}else{
					alert("Go to Look & Feel Section and generate the build from there");
					test = "0";
				}
				
			  },
			  error: function (xhr, desc, err) {
				alert(xhr.responseText);
				test = "0";
			  }
			});
			
			if(test=="1")
			{
				return true;
			}
			else if(test=="0")
			{
				return false;
			}
		}
		</script>
		
		<?php
	}
	
	function build_options_do_page_parent(){
		if(isset($_POST['restorebuildoptions']) && $_POST['restorebuildoptions'] == "1"){
                    if(! wp_verify_nonce( $_POST['_wpnonce'], 'restorebuildoptions' ))
		    {
                        print 'Sorry, your nonce did not verify. Please try again.';
                        exit;
                    }
                    $this->resetBuildOptions();
		}
		$this->renderForm($this->build_option_name ,'build_options_do_page');
		?>
		<p>
		Restore default settings
		<form name="restorebuildoptions" action="#restorebuildoptions" method="post">
		<input type="hidden" name="restorebuildoptions" value="1" />
		<input class="button-primary" type="submit" id="restorebuildoptions" value="Restore Defaults" onclick="return confirm(
  'Are you sure you want to restore to default settings.');" />
		<?php
		wp_nonce_field('restorebuildoptions');
		?>
		</form>
		</p>
		<?php
	}
	
		// Print the menu page itself
	function build_options_do_page() {
		$options = get_option($this->build_option_name);
		$accountOptions = get_option($this->account_tab_key);
		
		$tagTextColor = $options[pw_mobile_app_settings::$tagTextColorKey];
		$tagBgColor = $options[pw_mobile_app_settings::$tagBgColorKey];
		$feedBgColor = $options[pw_mobile_app_settings::$feedBgColorKey];
		$feedTitleColor = $options[pw_mobile_app_settings::$feedTitleColorKey];
		$feedContentTextColor = $options[pw_mobile_app_settings::$feedContentTextColorKey];
		$screenBgColor = $options[pw_mobile_app_settings::$screenBgColorKey];
		$actionBarTitleColor = $options[pw_mobile_app_settings::$actionBarTitleColorKey];
		$actionBarBgColor = $options[pw_mobile_app_settings::$actionBarBgColorKey];
		$statusBarBgColor = $options[pw_mobile_app_settings::$statusBarBgColorKey];
		if(empty($statusBarBgColor)){
			$statusBarBgColor = "#f45917";
		}
		$authorTextColor = $options[pw_mobile_app_settings::$authorTextColorKey];
		$timeTextColor = $options[pw_mobile_app_settings::$timeTextColorKey];
		
		
		 $args = array(
			'posts_per_page'   => 5,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => false ); 
			
		$postslist = get_posts( $args );
		
		?>
		
<script>		
		function loadXMLDoc()
		{
			var test = "0";
			jQuery.ajax({
			  url: "<?php echo $this->androAppHost;?>/appCreator/save.php",
			  type: 'post',
			  contentType: "application/x-www-form-urlencoded",
			  data: jQuery( "#pwappsettingsform" ).serialize(),
			  async: false,
			  success: function (data, status) {
				var jsonData = jQuery.parseJSON(data);
				if(jsonData && jsonData.ClientId != null && jsonData.ClientId != ''){
					document.pwappsettingsform.elements['pw-mobile-build-options[client_id]'].value = jsonData.ClientId;
					if(jsonData.PackageName != null){
						document.pwappsettingsform.elements['pw-mobile-build-options[package_name]'].value = jsonData.PackageName;
					}
					if(jsonData.ValidTill != null){
						document.pwappsettingsform.elements['pw-mobile-build-options[androapp_renewal_date]'].value = jsonData.ValidTill;
					}
					alert("Almost There !!\nWe have received your request to generate the build.\nYou will soon receive an email with the build link!");
					test = "1";
				}else{
					alert("Unknown Error!");
					test = "0";
				}
				
			  },
			  error: function (xhr, desc, err) {
				alert(xhr.responseText);
				test = "0";
			  }
			});
			
			if(test=="1")
			{
				return true;
			}
			else if(test=="0")
			{
				return false;
			}
		}
		
		function clearImage(imgId, formFieldId){
			document.pwappsettingsform.elements[formFieldId].value = "";
			document.getElementById(imgId).src = "";
			return false;
		}
		function upload(inputFieldId, resultDivId, imgId, formFieldId) 
	    {
			var file = document.getElementById(inputFieldId);
		 
		  /* Create a FormData instance */
		  var formData = new FormData();
		  /* Add the file */ 
		  formData.append(inputFieldId, file.files[0]);
                  formData.append("fileId" , inputFieldId);
		  var test = "1";
			jQuery.ajax({
			  url: "<?php echo $this->androAppHost;?>/appCreator/upload.php",
			  type: 'post',
			  data: formData,
			  cache: false,
			  processData: false, // Don't process the files
			  contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			  async: false,
			  success: function (data, status) {
				var jsonData = jQuery.parseJSON(data);
				if(jsonData && jsonData.ImageLink != null && jsonData.ImageLink != ''){
					document.pwappsettingsform.elements[formFieldId].value = jsonData.ImageLink;
					document.getElementById(imgId).src = jsonData.ImageLink;
					document.getElementById(resultDivId).innerHTML = "Uploaded " + jsonData.fileName;
					test = "1";
				}else{
					alert("Unknown Error!");
					test = "0";
				}
				
			  },
			  error: function (xhr, desc, err) {
				alert(xhr.responseText);
				test = "0";
			  }
			});
			
			if(test=="1")
			{
				return true;
			}
			else if(test=="0")
			{
				return false;
			}
	   }
</script>

				<table class="form-table">
					<tr valign="top">
					<td scope="row">
				
				<table class="form-table" border="1">
					<tr valign="top"><th scope="row">Theme</th>
						<td>
						<?php _e('Select the application theme','androapp');?>
						</br>
						<select name="<?php echo $this->build_option_name."[".ANDROAPP_THEME_NAME."]"?>" onChange="switchMode(this.value);" value="<?php echo $options[ANDROAPP_THEME_NAME];?>" onChange="";  >
							<option value="cardview" <?php if($options[ANDROAPP_THEME_NAME] == "cardview") { echo "selected";}?> ><?php _e('Card View','androapp');?></option>
							<option value="compact" <?php if($options[ANDROAPP_THEME_NAME] == "compact") { echo "selected";}?> ><?php _e('News','androapp');?></option>
						</select>
						</td>
					</tr>
					
					<tr valign="top"><th scope="row"><?php _e('Theme Colors','androapp');?>:</th>
						<td>
						
						<?php _e('Status Bar background color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$statusBarBgColorKey."]"?>" type="color" value="<?php echo $statusBarBgColor;?>" onChange="document.getElementById('statusBar').style.background = this.value;" />
						</br>
						<?php _e('Action Bar background color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$actionBarBgColorKey."]"?>" type="color" value="<?php echo $actionBarBgColor;?>" onChange="document.getElementById('actionBar').style.background = this.value;" />
						</br>
						<?php _e('Action Bar Text color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$actionBarTitleColorKey."]"?>" type="color" value="<?php echo $actionBarTitleColor;?>" onChange="document.getElementById('actionBarTitle').style.color = this.value; document.getElementById('statusBarTitle').style.color = this.value;" />
						</br>
						<?php _e('Screen Background color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$screenBgColorKey."]"?>" type="color" value="<?php echo $screenBgColor;?>" onChange="document.getElementById('androScreen').style.background = this.value;" />
						</br>
						<?php _e('Feed Title color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$feedTitleColorKey."]"?>" type="color" value="<?php echo $feedTitleColor;?>" onChange="changeTextColor('androPostTitle', this.value);" />
						</br>
						<?php _e('Feed preview text color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$feedContentTextColorKey."]"?>" type="color" value="<?php echo $feedContentTextColor;?>" onChange="changeTextColor('androPostContent', this.value);" />
						</br>
						<?php _e('Feed background color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$feedBgColorKey."]"?>" type="color" value="<?php echo $feedBgColor;?>" onChange="changeBgColor('androFeed', this.value);" />
						</br>
						<?php _e('Category title color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$tagTextColorKey."]"?>" type="color" value="<?php echo $tagTextColor;?>" onChange="changeTextColor('tagTitle', this.value);" />
						</br>
						<?php _e('Category Background color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$tagBgColorKey."]"?>" type="color" value="<?php echo $tagBgColor;?>" onChange="changeBgColor('tagTitle', this.value);" />
						</br>
						<?php _e('Author Text color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$authorTextColorKey."]"?>" type="color" value="<?php echo $authorTextColor;?>" onChange="changeTextColor('authorTitle', this.value);" />
						</br>
						<?php _e('Time Ago Text color','androapp');?>: <input name="<?php echo $this->build_option_name."[".pw_mobile_app_settings::$timeTextColorKey."]"?>" type="color" value="<?php echo $timeTextColor;?>" onChange="changeTextColor('timeTitle', this.value);" />
						</br>
						</td>
					</tr>
					
					<tr valign="top"><th scope="row"><font style="color:red">*</font> <?php _e('Application Name','androapp');?>:</th>
						<td>
						<?php _e('Define application name (This is the name which will appear in play store).','androapp');?>
						</br>
						<input type="text" name="<?php echo $this->build_option_name."[".ANDROAPP_APP_NAME."]"?>" value="<?php echo $options[ANDROAPP_APP_NAME];?>" onChange="document.getElementById('actionBarTitle').innerHTML=this.value;";  />
						</td>
					</tr>
					
					<tr valign="top"><th scope="row"><font style="color:red">*</font><?php _e('Site Url','androapp');?>:</th>
						<td>
						<?php _e('Enter Your site url (most probably you don\'t need to edit it).','androapp');?>
						</br>
						<input type="text" name="<?php echo $this->build_option_name."[".ANDROAPP_HOST_NAME."]"?>" value="<?php echo $options[ANDROAPP_HOST_NAME];?>"  />
						</td>
					</tr>
					
					<tr valign="top"><th scope="row"><font style="color:red">*</font><?php _e('Application Icon','androapp');?>:</th>
						<td>
						<?php _e('Upload a png image of 512x512 dimension.','androapp');?>
						</br>
						<img id="launcherIconImg" src="<?php echo $options[ANDROAPP_LAUNCHER_ICON];?>" width="50px" height="50px"/>
						<input type="file" id="fileToUpload" name="fileToUpload" onChange="upload('fileToUpload','imageUploadDiv','launcherIconImg', 'pw-mobile-build-options[launcher_icon]')" />
						<div id="imageUploadDiv"></div>
						</td>
					</tr>
					
                                        <tr valign="top">
                                            <th scope="row">
                                                <font style="color:red">*</font><?php _e('Notification Icon','androapp');?>:
                                                <br/>
                                                (needed only for android app)
                                            </th>
						<td>
                                                    By default, we use Application Icon for notification icon, you can override it here. It should be <b>white pixels on a transparent backdrop</b>.<br/><br/>
						<?php _e('Upload a png image of 96x96 dimension.','androapp');?>
						</br>
						<img id="notificationIconImg" src="<?php echo $options[ANDROAPP_NOTIFICATION_ICON];?>" width="50px" height="50px"/>
						<input type="file" id="notificationIconToUpload" name="notificationIconToUpload" onChange="upload('notificationIconToUpload','notificationIconUploadDiv','notificationIconImg', 'pw-mobile-build-options[notification_icon]')" />
						<div id="notificationIconUploadDiv"></div>
						</td>
					</tr>
                                        
					<tr valign="top"><th scope="row">Font:</th>
						<td>
						<?php _e('Select a font of your choice','androapp');?>
						<br/>
						<select name="<?php echo $this->build_option_name."[".ANDROAPP_FONT_NAME."]"?>" onChange="switchFont(this.value);" value="<?php echo $options[ANDROAPP_FONT_NAME];?>" onChange="";  >
						
							<option value="" id="Default" <?php if($options[ANDROAPP_FONT_NAME] == "Default") { echo "selected";}?> fontname="" ffamily="" >Default</option>
							
							<?php
								foreach(pw_mobile_app_settings::$fontArray as $fontname=>$font){
									?>
									<option value="<?php echo $fontname;?>" id="<?php echo $fontname;?>" <?php if($options[ANDROAPP_FONT_NAME] == $fontname) { echo "selected";}?> fontname="<?php echo $font[0];?>" ffamily="<?php echo $font[1];?>" ><?php echo $fontname;?></option>
									<?php
								}
								
							?>
						</select>
						<br/>
						<br/>
						<b><?php _e('Note','androapp');?>:-</b> <ol>
						<li><?php _e('please check the font in html preview on right, and than finally check on device.','androapp');?> </li>
						<li><?php _e('Default - default font of the device, may vary with device','androapp');?></li>
						<li><?php _e('you can change the font at run time, just restart your app twice to see the change.','androapp');?></li>
						<li><?php _e('Please check the individual font license before using it','androapp');?></li>
						</ol>
						</td>
					</tr>
					
					<tr valign="top"><th scope="row"><font style="color:red">*</font><?php _e('Splash Image','androapp');?></th>
						<td>
						<?php _e('If you wish you can set a custom splash image,(we will use application icon otherwise), to make the image fit to screen, some portion of image might be invisible.',
						'androapp');?><br/>
						<?php _e('Upload a png image, recommended dimension is 1280px1920px, image size should be less than 200kb.','androapp');?>
						</br>
						<div style="position: relative; left: 0; top: 0;">
						<img id="splashImg" src="<?php echo $options[ANDROAPP_SPLASH_IMAGE];?>" width="100px" height="150px" style="position: relative; top: 0; left: 0;"/>
						<a href="#" style="position: absolute; top: 2px; left: 2px;" onClick="return clearImage('splashImg', 'pw-mobile-build-options[splashImage]')">X</a>
						</div>
						<input type="file" id="splashImageInput" name="splashImageInput" onChange="upload('splashImageInput','splashImageUploadDiv','splashImg', 'pw-mobile-build-options[splashImage]')" />
						
						<div id="splashImageUploadDiv"></div>
						
						</td>
					</tr>
					
					<tr valign="top"><th scope="row"><font style="color:red">*</font> <?php _e('Deep Linking','androapp');?>:</th>
						<td>
						<b><?php _e('Enter the path prefixes for the urls you wish to be opened in the app.','androapp');?></b>
						</br>
						<?php _e('path prefix should start from /, enter one path prefix per line, If you want all the urls to be attempted to open in app, just enter /','androapp');?>
						</br>
						<?php _e('Keep it blank to disable deep linking','androapp');?>
						</br>
						<textarea form = "pwappsettingsform" name="<?php echo $this->build_option_name."[".ANDROAPP_DEEP_LINKING."]"?>"><?php echo $options[ANDROAPP_DEEP_LINKING];?></textarea>
						</td>
					</tr>
				</table>

				
				<table class="form-table">
					
				</table>
				
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_GOOGLE_SENDER_ID."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_GOOGLE_SENDER_ID]) ? $accountOptions[ANDROAPP_GOOGLE_SENDER_ID] : '';?>"  />
					
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_ANALYTICS_TRACKING_ID_KEY."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_ANALYTICS_TRACKING_ID_KEY]) ? $accountOptions[ANDROAPP_ANALYTICS_TRACKING_ID_KEY] : '';?>"  />				
				
                                <input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY]) ? $accountOptions[ANDROAPP_IOS_ANALYTICS_TRACKING_ID_KEY] : '';?>"  />				
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_GOOGLE_APP_ID."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_GOOGLE_APP_ID]) ?  $accountOptions[ANDROAPP_GOOGLE_APP_ID]: '';?>"  />
				
                                <input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_IOS_APP_ID."]"?>" value="<?php echo isset($accountOptions[ANDROAPP_IOS_APP_ID]) ?  $accountOptions[ANDROAPP_IOS_APP_ID]: '';?>"  />
                                
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_AUTHENTICATION_KEY."]"?>" value="<?php echo $options[ANDROAPP_AUTHENTICATION_KEY];?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_CLIENT_ID."]"?>" value="<?php echo isset($options[ANDROAPP_CLIENT_ID]) ? $options[ANDROAPP_CLIENT_ID] : '' ;?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_RENEWAL_DATE."]"?>" value="<?php echo isset($options[ANDROAPP_RENEWAL_DATE]) ? $options[ANDROAPP_RENEWAL_DATE] : '' ;?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_PLUGIN_URL."]"?>" value="<?php echo plugins_url(); ?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_PACKAGE_NAME."]"?>" value="<?php echo isset($options[ANDROAPP_PACKAGE_NAME]) ? $options[ANDROAPP_PACKAGE_NAME] : '';?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_LAUNCHER_ICON."]"?>" value="<?php echo isset($options[ANDROAPP_LAUNCHER_ICON]) ? $options[ANDROAPP_LAUNCHER_ICON] :'';?>"  />
				
                                <input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_NOTIFICATION_ICON."]"?>" value="<?php echo isset($options[ANDROAPP_NOTIFICATION_ICON]) ? $options[ANDROAPP_NOTIFICATION_ICON] : '';?>"  />
                                
				<input type="hidden" name="<?php echo $this->build_option_name."[".ANDROAPP_SPLASH_IMAGE."]"?>" value="<?php echo isset($options[ANDROAPP_SPLASH_IMAGE]) ? $options[ANDROAPP_SPLASH_IMAGE] : '';?>"  />
				
				
				<input type="hidden" name="<?php echo $this->build_option_name."[category_base]"?>" value="<?php echo get_option( 'category_base' );?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[tag_base]"?>" value="<?php echo get_option( 'tag_base' );?>"  />
				
				<input type="hidden" name="<?php echo $this->build_option_name."[build_source]"?>" value="getyourapp"  />
					</td>
						<td style="min-width:350px">
							<center>
							<div style="width:290px;height:auto;">
							<?php _e('This is just approximate <b>html preview</b>, It is recommended to test it on device directly.',
							'androapp');?>
							</div>
	<div style="width:290px;height:480px;">
	
	<div id="statusBar" style="display: table; width:290px;height:10px;background:<?php echo $statusBarBgColor;?>;">
		<div id="statusBarTitle" style="color:<?php echo $actionBarTitleColor;?>;font-size:large;display: table-cell; vertical-align: middle;">
		<?php _e('status bar','androapp');?>
		</div>
	</div>
	<div id="actionBar" style="display: table; width:290px;height:40px;background:<?php echo $actionBarBgColor;?>;">
		<div id="actionBarTitle" style="color:<?php echo $actionBarTitleColor;?>;font-size:large;display: table-cell; vertical-align: middle;">
		<?php echo $options[ANDROAPP_APP_NAME]; ?>
		</div>
	</div>
	
	<div id="androScreen" style="width:100%;height:430px;background:<?php echo $screenBgColor;?>;overflow:hidden;">
		<?php
		foreach ( $postslist as $post ) :
		?>
		<div name="androFeed" style="background:<?php echo $feedBgColor;?>;width:270px;min-height:85px;">
			<?php
			$firstImage = androapp_get_first_image($post->post_content);
			//if(!empty($firstImage))
			{
				?>
				<div name="androPostImage" >
				<?php
				if(!empty($firstImage)){
					echo "<img name='androPostImageSrc' src='$firstImage' width='100%' ></img>";
				}else{
					echo '<img name="androPostNoImage" src="' . plugins_url( 'androapp/images/no_image.png', dirname(__FILE__) ) . '"  width="100%" > ';
				}
				?>
				</div>
				<?php
			}
			?>
			<div name="androPostTitle" style="text-align:left;color:<?php echo $feedTitleColor;?>;">
			<?php echo "<b>".$post->post_title."</b>";?>
			</div>
			
			<?php
			if(empty($firstImage))
			{
			?>
			<div name="androPostContent"  style="color:<?php echo $feedContentTextColor;?>;">
			<?php echo substr(strip_tags(wpautop( strip_shortcodes( $post->post_content))), 0, 115); ?>
			</div>
			<?php } ?>
			<div style="display:flex;">
				<div name="tagTitle" style="background:<?php echo $tagBgColor;?>;float:left;margin:5px;color:<?php echo $tagTextColor;?>;">
				<?php
				$category = get_the_category($post->ID); 
				if($category[0]){
					echo $category[0]->cat_name;
				}
?>

				</div>
				
				<div name="author" style="float:left;margin:5px;color:<?php echo $authorTextColor;?>;">
				<?php
		
				$author = get_the_author_meta( 'first_name', $post->post_author );
				echo $author;
?>

				</div>
				
				<div name="timeAgo" style="float:left;margin:5px;color:<?php echo $timeTextColor;?>;">
				<?php
		
				if(!empty($author)){
					echo " - ";
				}
				echo __('"30 mins ago"','androapp');
?>

				</div>
				<!-- <div style="float:right;margin:5px">Share icons</div> -->
			</div>
		</div>
		<?php
		endforeach; 
		
		?>
	</div>
	</div>
	
	<script>

		window.onload=<?php echo "switchMode('".$options[ANDROAPP_THEME_NAME]."')";?>;
	</script>

							</center>
						</td>
					</tr>
				</table>
				
				<p style="color:red" >* <?php _e('Application Name, Site Url, Application Icon, Notification Icon, Splash Image and Deep Linlinking settings go into your mobile app and can\'t be changed once you publish your app in google app store. You have to publish your app everytime you update these settings, so carefully fill these before generating the build.',
				'androapp');?></p>
				
				
				<table class="form-table">
					<tr valign="top"><th scope="row">Email:</th>
						<td>
						<?php _e('We will send generated apk link to this mail id.','androapp');?>
						</br></br>
						<input type="text" name="<?php echo $this->build_option_name."[".ANDROAPP_EMAIL."]"?>" value="<?php echo $options[ANDROAPP_EMAIL];?>"  />
						</td>
					</tr>
				</table>
				
				<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
				<?php
				
				$termAndConditions = '<a href="https://androapp.mobi/blog/terms-conditions" target="_blank" >'.
				__('Terms & Conditions','androapp').'</a>';
				printf(
				/* translators: %s: Terms & Conditions */
				__('By clicking on the below button you agree to our %s.','androapp'),
				$termAndConditions);
				?>
				<div id="responseDiv"></div>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Generate Build','androapp') ?>" onClick='return loadXMLDoc();' />
				</p>
<?php
	}
	
	function render_scripts($scripts, $selectedScripts, $contentType, $postProcessedCss){
		global $wp_scripts;
		?>
		<script>
			function addToSelectedScripts(handle){
				if(document.getElementById("SelectedScripts").value  == "")
				{
					document.getElementById("SelectedScripts").value=handle;
				}
				else{
					document.getElementById("SelectedScripts").value = document.getElementById("SelectedScripts").value + "\n"+ handle;
				}
				return false;
			}
		</script>
		
		<div id="scripts_selector_div" style="margin-bottom:10px;display:<?php echo (($contentType == 'postprocessed')?"block":"none") ;?>">
		<div style="color:red;font-size:small;">Adding javascripts will slow down post detail screen , Ideally you don't need to add any javascripts, add them only if you know what you are acheiving by that</div> 
                <div style="display:inline-flex">
		<div style="float:left; width:70%;"><br/>
		<b>Select JS files you want to include</b>
		<table  class="androapp-table" cellpadding="0" cellspacing="0" border="0" >
			<thead>
			<tr>
				<th>Handle</th>
				<th>Script src</th>
				<th>Action</th>
			</tr>
			</thead>
			<tbody>
			<?php
				if(!empty($scripts))
				{
					foreach($scripts as $handle => $src)
					{
						?>
						<tr>
							<td style="width:20%" ><?php echo $handle;?></td>
							<td style="width:60%"><input readonly="readonly" type="text" value="<?php echo $src;?>"></td>
							<td style="width:15%"><a href="javascript:void(0);" onClick="addToSelectedScripts('<?php echo $handle;?>');">Add>></a></td>
						</tr>
					<?php
					}
				}
				else{
				?>
					<tr>
					<td colspan="3">
						No enqueued files detected.</br></br>

Please try visiting a few pages on your site and then refresh this page.
</br></br>
You should clear this list once in a while to get rid of files that are no longer being used as this is not done automatically.
</br></br>
</td>
					</tr>
					<?php
				}
			?>
			</tbody>
		</table>
		</div>
			<div style="float:right;width:25%; padding-left:10px;"></br>
			<b>Selected Scripts</b>
			<textarea id="SelectedScripts" form="pwappsettingsform" rows="13" cols="20" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$selectedScripts."]"?>" ><?php echo $selectedScripts;?></textarea>
			</div>	
		</div>
		<a href="?page=pw_mobile_app_options&clear_scripts_list=true" >Clear Script File List</a>
		
		</br></br>
		<b>Define custom css here for postprocessed content type</b></br>
		<textarea id="postProcessedCss" form="pwappsettingsform" rows="13" cols="45" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$postProcessedCss."]"?>" ><?php echo $postProcessedCss;?></textarea>	
		</div>
		<?php
	

	}
	
	function language_options_do_page_parent(){
		if(isset($_POST['restorelanguageoptions']) && $_POST['restorelanguageoptions'] == "1"){
                    if(! wp_verify_nonce( $_POST['_wpnonce'], 'restorelanguageoptions' ))
		    {
                        print 'Sorry, your nonce did not verify. Please try again.';
                        exit;
                    }
			$this->resetLanguageOptions();
		}
		$this->renderForm($this->language_option_name ,'language_options_do_page');
		?>
		<p>
		Restore default settings
		<form name="restorelanguageoptions" action="#restorelanguageoptions" method="post">
		<input type="hidden" name="restorelanguageoptions" value="1" />
		<input class="button-primary" type="submit" id="restorelanguageoptions" value="Restore Defaults" onclick="return confirm(
  'Are you sure you want to restore to default settings.');" />
		<?php
		wp_nonce_field('restorelanguageoptions');
		?>
		</form>
		</p>
		<?php
	}
	
	function language_options_do_page(){
		$options = get_option($this->language_option_name);
		?>
		<h2>Add your text in corresponding textbox</h2>
		<table class="form-table">
			<th scope="row">Text Title</th>
			<th scope="row">Your Value</th>
			<th scope="row">Default Value</th>
			<?php
			
			foreach(pw_mobile_app_settings::$languageArray as $key => $value)
			{
				if($key == "wooseparator"){
					?>
						</table>
						Below options are specific to woocommerce, most probably you dont need to change them if you are not using woocommerce plugin</tr>
						<table class="form-table">
						<th scope="row">Text Title</th>
						<th scope="row">Your Value</th>
						<th scope="row">Default Value</th>
					<?php
					continue;
				}
			?>
			<tr valign="top"><th scope="row"><?php echo $key; ?></th>
				<td>
					<input type="text" name="<?php echo $this->language_option_name."[".$key."]"?>" value="<?php echo $options[$key];?>"  />
				</td>
				<td>
				<?php echo $value;?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>	
		
		<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
		<?php
	}
	
	// Print the menu page itself
	function options_do_page() {	
		$options = get_option($this->option_name);
		
		$scriptOptions = $this->scriptOptions;
		?>
		
<script>

	   function onPostContentTypeChange(){
			if(document.pwappsettingsform.elements['pw-mobile-app[post_content]'].value == "loadurl"){
				document.getElementById("css_sample_div").style.display = "inherit";
			}else{
				document.getElementById("css_sample_div").style.display = "none";
			}
			
			if(document.pwappsettingsform.elements['pw-mobile-app[post_content]'].value == "postprocessed"){
				document.getElementById("scripts_selector_div").style.display = "inherit";
			}else{
				document.getElementById("scripts_selector_div").style.display = "none";
			}
	   }
</script>
</br>
<p style="font-size:17px;">
		<?php _e('Note:- You can change all below settings at runtime (without any need to create new apk). You need to restart your app and browse few pages to see the change.',
		'androapp');?>
</p>
				<?php

$menus = wp_get_nav_menus();

$action = 'install-plugin';
    $slug = 'menu-icons';
    $menu_icons_installurl =  wp_nonce_url(
    add_query_arg(
            array(
                    'action' => $action,
                    'plugin' => $slug
            ),
            admin_url( 'update.php' )
    ),
    $action.'_'.$slug
);
?>

				<table class="form-table">				
					<tr valign="top"><th scope="row">Select Menu:</th>
						<td>
						<?php _e('Select the menu to be used for mobile app, you may use the same menu as your website, or create a new menu specifically for mobile app.',
						'androapp');?></br>
</br>
<b>Drawer Menu (left menu): </b>
						<select name="<?php echo $this->option_name."[".PWAPP_MENU."]"?>">
<option value=""></option>

<br/>
<?php
foreach ($menus as $menu) {
	//print_r($menu);
?>
<option value="<?php echo $menu->term_id;?>"  <?php if($options[PWAPP_MENU] == $menu->term_id) echo "selected"; ?>><?php echo $menu->name;?></option>
<?php
}
?>
</select>
<br/>
*We have added menu icons support from 10.00 version<br/>
to change default icons - Install <a href="<?php echo $menu_icons_installurl; ?>" target="_blank"> Menu Icons</a> plugin and follow <a href="https://androapp.mobi/blog/manage-androapp-menu-icons/433" target="_blank">this post</a>.
<br/><br/>
<b>Slider Menu (top menu)</b>
<select name="<?php echo $this->option_name."[".SLIDER_MENU."]"?>">
<option value=""></option>
<?php
foreach ($menus as $menu) {
	//print_r($menu);
?>
<option value="<?php echo $menu->term_id;?>"  <?php if($options[SLIDER_MENU] == $menu->term_id) echo "selected"; ?>><?php echo $menu->name;?></option>
<?php
}
?>
</select><span style="color:red;font-size: small">(since app version 11.00)</span>
</td>
</tr>
</table>

</br></br>
				<table class="form-table">
					<tr valign="top"><th scope="row"><?php _e('Image Dimension','androapp')?>:</th>
						<td>
						<?php _e('We use the featured image to show on the feeds pages(list pages like, category and home) on mobile app.','androapp');?></br>
						<?php _e('If no featured image is present than we try and fetch the first image from the post content','androapp');?></br>
						<input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$useOnlyFeaturedImage."]"?>"  value="1" <?php if($options[pw_mobile_app_settings::$useOnlyFeaturedImage] == '1') echo "checked"; ?> /> (if checked, will not try to fetch the first image from the post content, will use only featured image)
						</br></br>
						<?php _e('Select the image dimension for the image.','androapp');?></br> </br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_IMAGE_DIMENSION."]"?>" value="full" <?php if($options[ANDROAPP_IMAGE_DIMENSION] == 'full') echo "checked"; ?> /><?php _e('Full Image (Fit to width)','androapp');?>
						</br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_IMAGE_DIMENSION."]"?>" value="preview" <?php if($options[ANDROAPP_IMAGE_DIMENSION] == 'preview') echo "checked"; ?> /><?php _e('Preview (fit to width with limited height)','androapp');?>
						</br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_IMAGE_DIMENSION."]"?>" value="noimage" <?php if($options[ANDROAPP_IMAGE_DIMENSION] == 'noimage') echo "checked"; ?> /><?php _e('No Image (Image from the preview text will be visible, if any)','androapp');?>
						</td>
					</tr>
				</table>	
					
				</br></br>
				
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Preview Text Setting','androapp');?>:</th>
						<td>
						<?php _e('Select which text to be used for preview on feeds(category and home page) in mobile app.','androapp');?>
						</br></br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_EXCERPT_PREFERENCE."]"?>" value="excerpt" <?php if($options[ANDROAPP_EXCERPT_PREFERENCE] == 'excerpt') echo "checked"; ?> />
						<?php _e('Excerpt','androapp');?>
						</br>
						
						<?php
							$action = 'install-plugin';
							$slug = 'wordpress-seo';
							$wp_seo_installurl =  wp_nonce_url(
							add_query_arg(
								array(
									'action' => $action,
									'plugin' => $slug
								),
								admin_url( 'update.php' )
							),
							$action.'_'.$slug
						);?>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_EXCERPT_PREFERENCE."]"?>" value="seo_meta_desc" <?php if($options[ANDROAPP_EXCERPT_PREFERENCE] == 'seo_meta_desc') echo "checked"; ?> />
						<?php 
						$wpseoLink = '<a href="'.$wp_seo_installurl.'" target="_blank" >Wordpress SEO plugin</a>';
						printf(
						/* translators: %s: Wordpress SEO Plugin */
						__('SEO Meta Description( install and Activate %s )','androapp'),
						$wpseoLink
						);
						?>
						</br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_EXCERPT_PREFERENCE."]"?>" value="none" <?php if($options[ANDROAPP_EXCERPT_PREFERENCE] == 'none') echo "checked"; ?> />
						<?php _e('No Preview Text (only title will be shown)','androapp');?>
						</td>
					</tr>
				</table>
				
				</br></br>
				
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Post Content Type','androapp');?>:</th>
						<td>
						<?php _e('Select the post content type to use for your app, we suggest to select different options and test in on mobile app live.',
						'androapp');?></br></br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_POST_CONTENT."]"?>" value="preprocessed" <?php if($options[ANDROAPP_POST_CONTENT] == 'preprocessed') echo "checked"; ?> onChange="onPostContentTypeChange();" />
						<?php _e('Pre Processed content (shortcodes will be stripped, except caption, galley, audio, video, playlist and wp_caption)','androapp');?>
						</br>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_POST_CONTENT."]"?>" value="postprocessed" <?php if($options[ANDROAPP_POST_CONTENT] == 'postprocessed') echo "checked"; ?> onChange="onPostContentTypeChange();" />
						<?php _e('Post Processed (all shortcodes will be processed, you might need to add js files specifically)','androapp');?>
						<br/>
						<?php
							$this->render_scripts($scriptOptions['scripts'] , $options[pw_mobile_app_settings::$selectedScripts], $options[ANDROAPP_POST_CONTENT], $options[pw_mobile_app_settings::$postProcessedCss]);
						?>
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_POST_CONTENT."]"?>" value="loadurl" <?php if($options[ANDROAPP_POST_CONTENT] == 'loadurl') echo "checked"; ?> onChange="onPostContentTypeChange();" />
						<?php _e('Load from Url (It will open post page directly, you can modify CSS to hide some content, OR use isAndroAppRequest() method to check if it is a AndroApp Request.).','androapp');?>
						<br/>
						<div id="css_sample_div" style="display:<?php echo (($options[ANDROAPP_POST_CONTENT] == 'loadurl')?"inherit":"none") ;?>">
						<b><?php _e('Modify below css for mobile app requests','androapp');?></b><br/>
						<div style="float:left;width:40%;">
						<textarea form="pwappsettingsform" rows="15" cols="45" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$androAppCss."]"?>" ><?php echo $options[pw_mobile_app_settings::$androAppCss];?>
						</textarea>
						</div>
						<div style="float:right;width:50%;">
						<b><?php _e('Example to check if it is a AndroAppRequest','androapp');?></b>
<pre>
if(function_exists('isAndroAppRequest') && isAndroAppRequest()){
//Code to be called for AndroApp Requests(mobile app)
}
</pre>
						<b><?php _e('Example to check if its not a AndroAppRequest','androapp');?></b>
<pre>
if(!function_exists('isAndroAppRequest') || !isAndroAppRequest()){
//Code to be called for regular requests
}
</pre>
						</div>
						</div>
                                                <span style="color:blue">
                                                <input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_POST_CONTENT."]"?>" value="loadimages" <?php if($options[ANDROAPP_POST_CONTENT] == 'loadimages') echo "checked"; ?> onChange="onPostContentTypeChange();" />
						Slide Show (it will open all the images in the post as slide show)
                                                <br/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select Failover Post Type -  when no images in the post <br/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_FAILOVER_POST_CONTENT."]"?>" value="preprocessed" <?php if($options[ANDROAPP_FAILOVER_POST_CONTENT] == 'preprocessed') echo "checked"; ?>  /> Pre processed 
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_FAILOVER_POST_CONTENT."]"?>" value="postprocessed" <?php if($options[ANDROAPP_FAILOVER_POST_CONTENT] == 'postprocessed') echo "checked"; ?>  /> Post processed
						<input type="radio" name="<?php echo $this->option_name."[".ANDROAPP_FAILOVER_POST_CONTENT."]"?>" value="loadurl" <?php if($options[ANDROAPP_FAILOVER_POST_CONTENT] == 'loadurl') echo "checked"; ?>  /> Load from Url<br/>
                                                </span>
                                                </td>
					</tr>
                                        
                                        
<tr valign="top"><th scope="row"><?php _e('Override Post Content Type','androapp');?>:</th>
    <td>Fill in the comma separated post/page ids to override default post content type selected above.</br></br>
        use <b>Load from url</b> for below post/page ids</br>
        <input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$loadUrlPostIds."]"?>" value="<?php echo $options[pw_mobile_app_settings::$loadUrlPostIds];?>"  /></br>
        use <b>Pre Processed</b> for below post/page ids</br>
        <input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$preProcessedPostIds."]"?>" value="<?php echo $options[pw_mobile_app_settings::$preProcessedPostIds];?>"   /></br>
        use <b>Post Processed</b> for below post/page ids</br>
        <input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$postProcessedPostIds."]"?>" value="<?php echo $options[pw_mobile_app_settings::$postProcessedPostIds];?>"  /></br>
        use <b>Slideshow</b> for below post/page ids</br>
        <input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$loadimagesPostIds."]"?>" value="<?php echo $options[pw_mobile_app_settings::$loadimagesPostIds];?>"  /></br>
    </td>
</tr>

				</table>

				</br></br>
				
				
				
				<table class="form-table">
                                    
                                        <tr valign="top"><th scope="row"><?php _e('Post Detail Page Elements','androapp');?>:</th>
                                            <td><b>Parallax Effect for Featured Image</b><br/>
						<input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$featuredImageShowHide."]"?>" value="show" <?php if($options[pw_mobile_app_settings::$featuredImageShowHide] == 'show') echo "checked"; ?> />
						<?php _e('Enable','androapp');?>
						<input type="radio" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$featuredImageShowHide."]"?>" value="hide" <?php if($options[pw_mobile_app_settings::$featuredImageShowHide] == 'hide') echo "checked"; ?> />
						<?php _e('Disable','androapp');?>
                                                <br/>
                                                <div style="color:red;font-size: small" >applicable from app version 6.0.9 onwards.</div>
                                                <br/>
                                                if you don't want featured image on post detail page, then disable parallax effect and hide featured image using css as described in below article.<br/><br/>
                                                Few of the elements on post detail page can be controlled by pure css, check out this <a href="https://androapp.mobi/blog/hide-elements-on-post-page/73" target="_blank" >post</a> for details.
					    </td>
					</tr>
                                    
					<tr valign="top"><th scope="row"><?php _e('Share Image','androapp');?>:</th>
						<td>
						<input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$shareImagePreference."]"?>" value="first" <?php if($options[pw_mobile_app_settings::$shareImagePreference] == 'first') echo "checked"; ?> />
						<?php _e('Use First Image','androapp');?>
						</br>
						<input type="radio" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$shareImagePreference."]"?>" value="noimage" <?php if($options[pw_mobile_app_settings::$shareImagePreference] == 'noimage') echo "checked"; ?> />
						<?php _e('Don\'t use image for sharing(share only text)','androapp');?>
						</td>
					</tr>
					<tr valign="top"><th scope="row">Share Text:</th>
						<td>
						<?php _e('Select the text to be used for share via WhatsApp and other share intents(excluding Facebook).','androapp');?>
						</br></br>
						<?php _e('Select the text to be used without image(when no image is available to share)','androapp');?></br>
						<select name="<?php echo $this->option_name."[".pw_mobile_app_settings::$sharePreference."]"?>">
						<option value="TITLE" <?php if($options[pw_mobile_app_settings::$sharePreference] == "TITLE") echo "selected";?>> <?php _e('Title','androapp');?></option>
						<option value="EXCERPT" <?php if($options[pw_mobile_app_settings::$sharePreference] == "EXCERPT") echo "selected";?> ><?php _e('Excerpt','androapp');?></option>
						<option value="SEO" <?php if($options[pw_mobile_app_settings::$sharePreference] == "SEO") echo "selected";?>><?php _e('SEO Meta Description (Wordpress SEO Plugin is required)','androapp');?></option>
						<option value="FULL" <?php if($options[pw_mobile_app_settings::$sharePreference] == "FULL") echo "selected";?>><?php _e('Full Post Text','androapp');?></option>
						</select>
						</br>
						<?php _e('Select the text to be used with image','androapp');?></br>
						<select name="<?php echo $this->option_name."[".pw_mobile_app_settings::$shareTextWithImage."]"?>">
						<option value="TITLE" <?php if($options[pw_mobile_app_settings::$shareTextWithImage] == "TITLE") echo "selected";?>> <?php _e('Title','androapp');?></option>
						<option value="EXCERPT" <?php if($options[pw_mobile_app_settings::$shareTextWithImage] == "EXCERPT") echo "selected";?> ><?php _e('Excerpt','androapp');?></option>
						<option value="SEO" <?php if($options[pw_mobile_app_settings::$shareTextWithImage] == "SEO") echo "selected";?>><?php _e('SEO Meta Description (Wordpress SEO Plugin is required)','androapp');?></option>
						<option value="FULL" <?php if($options[pw_mobile_app_settings::$shareTextWithImage] == "FULL") echo "selected";?>><?php _e('Full Post Text','androapp');?></option>
						<option value="NONE" <?php if($options[pw_mobile_app_settings::$shareTextWithImage] == "NONE") echo "selected";?>><?php _e('No Text, share only image','androapp');?></option>
						</select>
						</td>
					</tr>
					<tr valign="top"><th scope="row"><?php _e('Share Suffix','androapp');?>:</th>
						<td>
						<?php _e('Suffix Text','androapp');?>
						<input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$shareSuffixText."]"?>" value="<?php echo $options[pw_mobile_app_settings::$shareSuffixText];?>"  />
						
						<?php _e('Suffix Link','androapp');?>
						<select name="<?php echo $this->option_name."[".pw_mobile_app_settings::$shareSuffixLink."]"?>">
						<option value="HOME" <?php if($options[pw_mobile_app_settings::$shareSuffixLink] == "HOME") echo "selected";?>><?php _e('Homepage','androapp');?></option>
						<option value="POST" <?php if($options[pw_mobile_app_settings::$shareSuffixLink] == "POST") echo "selected";?> ><?php _e('Post','androapp');?></option>
						<option value="NONE" <?php if($options[pw_mobile_app_settings::$shareSuffixLink] == "NONE") echo "selected";?>><?php _e('No Link','androapp');?></option>
						</select>
						</td>
					</tr>
				</table>
				
				
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Share Function Name','androapp');?>:</th>
					<td>
						<b><?php _e('Custom share text, you can overwrite above share settings for few posts using this method',
						'androapp');?></b></br>
						<input type="text" name="<?php echo $this->option_name."[".PWAPP_SHARE_FN_NAME."]"?>" value="<?php echo $options[PWAPP_SHARE_FN_NAME];?>"  />
					<?php _e('define a method in your themes functions.php file, if this function returns empty string, we will use above share settings.',
					'androapp');?>
					<pre>
function getShareText($post, $link){
	return "<?php _e('Share Text','androapp');?> ". via ".$link;
}
					</pre>
					<input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$shareImageWithCustomFunction."]"?>" value="1" <?php if($options[pw_mobile_app_settings::$shareImageWithCustomFunction] == '1') echo "checked"; ?> />
					<?php _e('Do not share Image When This function returns something','androapp');?>
						</td>
					</tr>
				</table>
				
				</br></br>
								
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Enable WP Super Cache for mobile app','androapp');?>:</th>
						<td>
						<?php _e('If you are using WP Super Cache, you can enable it for mobile app requests as well. <p style="color:red">You have to disable <b>debugging option</b> and <b>display comments at the end option</b> from Debug tab of WP Super Cache Settings page.</br> also uncheck <b>Don’t cache pages with GET parameters. (?x=y at the end of a url)</b> option</p></br>',
						'androapp');
						?>
						<input type="checkbox" name="<?php echo $this->option_name."[".ANDROAPP_ENABLE_WP_SUPER_CACHE."]"?>" value="1" <?php if($options[ANDROAPP_ENABLE_WP_SUPER_CACHE] == '1') echo "checked"; ?> />
						<?php _e('Enable','androapp');?>
						</br>
						</td>
					</tr>
				</table>
				
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Comments','androapp');?>:</th>
						<td><b><?php _e('Provider','androapp');?></b>
						<input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$commentsProvider."]"?>" value="wordpress" <?php if($options[pw_mobile_app_settings::$commentsProvider] == 'wordpress') echo "checked"; ?> />
						<?php _e('Wordpress','androapp');?>
						<input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$commentsProvider."]"?>" value="facebook" <?php if($options[pw_mobile_app_settings::$commentsProvider] == 'facebook') echo "checked"; ?> />
                                                Facebook
                                                <input type="radio" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$commentsProvider."]"?>" value="disabled" <?php if($options[pw_mobile_app_settings::$commentsProvider] == 'disabled') echo "checked"; ?> />
						<?php _e('Disable','androapp');?></br>
						(<?php _e('We do not support disqus, google+ comments as of now, we are working on them','androapp');?>)
						</br>
						<input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$showCommentsCount."]"?>" value="1" <?php if($options[pw_mobile_app_settings::$showCommentsCount] == '1') echo "checked"; ?> /><b>
						<?php _e('Show Comments Count','androapp');?></b> <span style="font-size:0.7em;">(shows wordpress comment counts)</span>
						</td>
					</tr>
				</table>
				
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Search Box','androapp');?>:</th>
						<td><b><?php _e('check to disable','androapp');?></b><br/>
						(<?php _e('search might not work properly, if you have installed some search plugin like relevanssi, please check and disable it in that case',
						'androapp');?>)<br/>
						<input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$searchBox."]"?>" value="1" <?php if($options[pw_mobile_app_settings::$searchBox] == '1') echo "checked"; ?> /><b>
						<?php _e('Disable','androapp');?></b>
						</td>
					</tr>
				</table>
				
				<table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Homepage','androapp');?>:</th>
						<td>
						<input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePageWidget."]"?>" value="posts" <?php if($options[pw_mobile_app_settings::$homePageWidget] == 'posts') echo "checked"; ?> />
						<?php _e('Posts','androapp');?></br>
						<input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePageWidget."]"?>" value="pages" <?php if($options[pw_mobile_app_settings::$homePageWidget] == 'pages') echo "checked"; ?> />
						<?php _e('Pages','androapp');?></br>
						<?php if(is_plugin_active('woocommerce/woocommerce.php')){
						?>
							<input type="radio" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePageWidget."]"?>" value="products" <?php if($options[pw_mobile_app_settings::$homePageWidget] == 'products') echo "checked"; ?> />
							<?php _e('Products','androapp');?><br/>
						<?php 
                                                }
                                                $this->initCustomPostsTaxonomies();
                                                
                                                
                                                foreach($this->custom_post_types as $post_type){
                                                    ?>
                                                    <input type="radio"  name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePageWidget."]"?>" value="<?php echo $post_type;?>" <?php if($options[pw_mobile_app_settings::$homePageWidget] == $post_type) echo "checked"; ?> />
                                                    <?php
                                                    echo $post_type."<br/>";
                                                }
                                                    ?>
						<input type="radio" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePageWidget."]"?>" value="single" <?php if($options[pw_mobile_app_settings::$homePageWidget] == 'single') echo "checked"; ?> />
						<?php _e('Single','androapp');?>
						
						<select name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePagePostType."]"?>" onChange="changeHomePageIdTextboxText(this.value);">
						<option value="post" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "post") echo "selected";?>>
						<?php _e('Post','androapp');?></option>
						<option value="page" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "page") echo "selected";?> >
						<?php _e('Page','androapp');?></option>
						<option value="category" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "category") echo "selected";?> >
						<?php _e('Category','androapp');?></option>
						<option value="tag" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "tag") echo "selected";?> >
						<?php _e('Tag','androapp');?></option>
						<option value="author" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "author") echo "selected";?> >
						<?php _e('Author','adroapp');?></option>
						<?php
						if(is_plugin_active('woocommerce/woocommerce.php')){
						?>
						<option value="product-cat" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "product-cat") echo "selected";?> >
						<?php _e('Product Category','androapp');?></option>
						<option value="product-tag" <?php if($options[pw_mobile_app_settings::$homePagePostType] == "product-tag") echo "selected";?> >
						<?php _e('Product Tag','androapp');?></option>
						<?php }?>
						</select>
						<?php _e('with','androapp');?> 
						<input id="homepage_id_textbox" type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$homePagePostId."]"?>" value="<?php echo $options[pw_mobile_app_settings::$homePagePostId];?>"  /> (<?php _e('please enter correct id/slug','androapp');?>)
						</br>
						<div style="color:red;font-size:small;"><?php _e('tag,author','androapp');?>  
						<?php
						if(is_plugin_active('woocommerce/woocommerce.php')){
							_e('product_category,product_tag ','androapp');
						}
						?>
						<?php _e('is supported from app version 5.0.7 onwards','androapp'); ?></div>
						</td>
					</tr>
				</table>
				
				<?php
					include("woo_utils.php");
				?>
                                
                                
                                <table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Offline Save','androapp');?>:</th>
						<td>
						<input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$enableOfflineSave."]"?>" value="1" <?php if($options[pw_mobile_app_settings::$enableOfflineSave] == '1') echo "checked"; ?> /><b>
						<?php _e('Enable Offline Save','androapp');?></b><br/>
                                                <span style='color:red;font-size:small'>Offline Save option will be visible only if post content type is preprocessed OR postprocessed<br/>
                                                Also, images and javascripts will be saved as per the space available, it might get deleted after saving more posts or depedning on the space available on the device.
                                                </span>
						</td>
					</tr>
				</table>
                                
                                <table class="form-table">				
					<tr valign="top"><th scope="row"><?php _e('Image Zoom & Save','androapp');?>:</th>
						<td>
                                                When user clicks on the image(which is not inside 'a' tag), it will pop up a dialog with image, where user will have option to zoom, share and save the image.
						<br/><input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$disableImageZoom."]"?>" value="yes" <?php if($options[pw_mobile_app_settings::$disableImageZoom] == 'yes') echo "checked"; ?> /><b>
						<?php _e('Disable Image Zoom','androapp');?></b><br/>
                                                <span style='color:red;font-size:small'>This feature is available only if post content type is preprocessed OR postprocessed<br/>
                                                </span>
						</td>
					</tr>
				</table>
                                
                                <table class="form-table">				
                                    <tr valign="top"><th scope="row"><?php _e('Webview or Mobile Browser','androapp');?>: <span style="color:red;font-size:small">since 11.02 version</span></th>
						<td>
                                                By default all of your website's urls are opened in webview(i.e. in the app).
                                                <br/>You can add the regex to overide this behavior
						<br/>
                                                <b>Regex to open your website url in mobile browser: </b><input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$regexForOpeningInBrowser."]"?>" value="<?php echo $options[pw_mobile_app_settings::$regexForOpeningInBrowser];?>"  />
						<br/><br/>
                                                <b>Similarly</b>, all extrenal urls are opened in external browser by default
                                                <br/>You can add the regex to overide this behavior
						<br/>
                                                <b>Regex to open external url in app:</b><input type="text" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$regexForOpeningInWebview."]"?>" value="<?php echo $options[pw_mobile_app_settings::$regexForOpeningInWebview];?>"  />
                                                <br/>
                                                <a href="https://androapp.mobi/blog/?p=456&preview=true" target="_blank">Click here</a> to read more about this feature.
                                                
                                                </td>
                                                
					</tr>
				</table>
</br>
<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
<?php		
	}

	function getFirstMenu(){
		$menus = wp_get_nav_menus();
		foreach($menus as $menu){
			if(!empty($menu->name)){
				return $menu->term_id;
			}
		}
		return null;
	}
	
	
	function getToken($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}
	
	function androapp_json_get_timezone() {

		$tzstring = get_option( 'timezone_string' );

		if ( ! $tzstring ) {
			// Create a UTC+- zone if no timezone string exists
			$current_offset = get_option( 'gmt_offset' );
			if ( 0 == $current_offset ) {
				$tzstring = 'UTC';
			} elseif ( $current_offset < 0 ) {
				$tzstring = 'Etc/GMT' . $current_offset;
			} else {
				$tzstring = 'Etc/GMT+' . $current_offset;
			}
		}
		$zone = new DateTimeZone( $tzstring );
		
		return $zone;
	}
}