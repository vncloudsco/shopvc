<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
require_once ABSPATH . 'wp-admin/includes/misc.php';
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class Pw_Wp_Menu {

	protected $options;
	
	protected $buildOptions;
	
	protected $accountOptions;
	
	protected $languageOptions;
	
        protected $version = "16.00";

        protected $menu_icons_active = false;
        
        protected $custom_taxonomies;
        
        protected $custom_post_types;
        
        /**
	 * Constructor
	 *
	 */
	public function __construct( ) {
            if(!class_exists('WP_JSON_Posts')){
                add_action( 'rest_api_init', function () {
                    register_rest_route( 'androapp/v2', 'androappconfig', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'get_config'),
                    ) );

                    register_rest_route( 'androapp/v2', 'androappauthcheck', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'check_auth_v2'),
                    ) );

                    register_rest_route( 'androapp/v2', '/androapp/add/(?P<id>\d+)/comments_new', array(
                            'methods' => 'POST',
                            'callback' => array($this, 'new_comment_v2'),
                    ) );         
                } ); 
            }
            
                
		$this->options = get_option("pw-mobile-app");
		$this->buildOptions = get_option("pw-mobile-build-options");
		$this->accountOptions = get_option("androapp_account_settings");
		$this->languageOptions = get_option("pw-mobile-app-language");
                $this->menu_icons_active = is_plugin_active( 'menu-icons/menu-icons.php' );
                $this->custom_taxonomies = $this->getCustomTaxonomies();
                $this->custom_post_types = $this->getCustomPostTypes();
	}
	/**
	 * Register the user-related routes
	 *
	 * @param array $routes Existing routes
	 * @return array Modified routes
	 */
	public function register_routes( $routes ) {
		$user_routes = array(
			// /users/me is an alias, and simply redirects to /users/<id>
			'/pwmenu' => array(
				array( array( $this, 'get_menu' ), WP_JSON_Server::READABLE ),
			),
			'/androappconfig' => array(
				array( array( $this, 'get_config' ), WP_JSON_Server::READABLE ),
			),
			'/androappauthcheck' => array(
				array( array( $this, 'check_auth' ), WP_JSON_Server::READABLE ),
			),
			// Comments
			'/androapp/add/(?P<id>\d+)/comments'                  => array(
				array( array( $this, 'new_comment' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			),
                        '/androapp/add/(?P<id>\d+)/comments_new'                  => array(
				array( array( $this, 'new_comment_new' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
			)
		);
		return array_merge( $routes, $user_routes );
	}

        public function check_auth_v2($data){
            return $this->check_auth($data['key'], 'view');
        }
	public function check_auth( $key, $context = 'view' ) {
		$buildOptions = get_option("pw-mobile-build-options");
		if($buildOptions['authentication_key'] == $key){
			return "valid";
		}
		return "Key not valid";
	}
	
        /**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,
 * or null if none.
 */
function my_awesome_func( $data ) {
    return "title2";
}
	public function get_config( $context = 'view' ) {
                if(empty($this->custom_taxonomies)){
                    $this->custom_taxonomies = $this->getCustomTaxonomies();
                    $this->custom_post_types = $this->getCustomPostTypes();
                }
		
                $config = array();
		$config['menulist'] = $this->get_menu();
                
                $config['slidermenu'] =  $this->get_menu_helper($this->options['slider_menu']);
		$config['monetise'] = $this->get_monetise();
		$config['layout'] = ($this->buildOptions['androapp_theme'] == null)?"cardview":$this->buildOptions['androapp_theme'];
		$config['colors'] = $this->get_colors();
		$config['comments_provider'] = ($this->options['comments_provider'] == null)?"disabled":$this->options['comments_provider'];
		$config['show_comments_count'] = ($this->options['show_comments_count'] == null)?"0":$this->options['show_comments_count'];
		$config['comments_depth'] = get_option("thread_comments_depth");
                $thread_comments = get_option("thread_comments");
                if(empty($thread_comments)){
                    $config['comments_depth']  = 1;
                }
 		$config['stringMap'] = $this->getLanguage();
		$config['homepage'] = $this->getHomepageSetting();
		$config['show_cart_icon'] = ($this->options['show_cart_icon'] == null)?"0":$this->options['show_cart_icon'];
                $config['disable_image_zoom'] = ($this->options['disable_image_zoom'] == null)?"no":$this->options['disable_image_zoom'];
                $config['show_save_option'] = ($this->options['enable_offline_save'] == null)?"0":$this->options['enable_offline_save'];
		$config['search_box_status'] = ($this->options['search_box_status'] == null)?"0":$this->options['search_box_status'];
		
                $config['regex_open_webview'] = $this->options['regex_open_webview'];
                $config['regex_open_browser'] = $this->options['regex_open_browser'];
                global $woocommerce;
		if(isset($woocommerce)){
			$config['iswoo'] = true;
		}else{
			$config['iswoo'] = false;
		}
		$config['urlHandle'] = $this->getUrlHandle();
		$prefix = $blog_prefix = '';
		
                //TODO: find alternate way
                if ( ! got_url_rewrite() )
			$prefix = '/index.php';
		if ( is_multisite() && !is_subdomain_install() && is_main_site() )
			$blog_prefix = '/blog';
		
		$config['blogurl'] = get_option('home') . $blog_prefix . $prefix;
		$config['fontName'] = $this->buildOptions['androapp_font_name'];
                $tagbase =  get_option( 'tag_base' );
                if(empty($tagbase)){
                    $tagbase = "tag";
                }
                $config['tag_base'] = $tagbase;
                
                $categorybase = get_option( 'category_base' );
                if(empty($categorybase)){
                    $categorybase = "category";
                }
                $config['category_base'] =  $categorybase;
                $config['version'] = $this->version;
                $config['custom_taxonomies'] = $this->custom_taxonomies;
                $config['custom_post_types'] = $this->custom_post_types;
                
                $config['push_stack_thershold'] = $this->accountOptions['push_stack_thershold'];
		return $config;
	}
	
        private function getCustomPostTypes(){
            $args = array('public' => true, '_builtin' => false);
            $post_types = get_post_types( $args, 'names', 'and' );
            
            $posttypes = array();
            foreach ($post_types as $posttype){
                $posttypes[] = $posttype;
            }
            return $posttypes;
        }


        private function getCustomTaxonomies(){
            $args=array('public'   => true, '_builtin' => false);
            $output = 'names'; // or objects
            $operator = 'and';
            $custom_taxonomies = get_taxonomies($args, $output, $operator); 
            
            $taxonomies = array();
            foreach ($custom_taxonomies as $taxonomy){
                $taxonomies[] = $taxonomy;
            }
            return $taxonomies;
        }


        private function getUrlHandle(){
		$handles = array();
		$woo_permalinks =  get_option( 'woocommerce_permalinks' );
		if(isset($woo_permalinks)){
			$handles['product_category_base'] = 'product-category';
			$handles['product_tag_base'] = 'product-tag';
			if(isset($woo_permalinks['category_base']) && !empty($woo_permalinks['category_base'])){
				$handles['product_category_base'] =  $woo_permalinks['category_base'];
			}
			if(isset($woo_permalinks['tag_base']) && !empty($woo_permalinks['tag_base'])){
				$handles['product_tag_base'] =  $woo_permalinks['tag_base'];
			}
		}
		return $handles;
	}
	
	private function getHomepageSetting(){
		$homepage = $this->options['homepage_widget'];
		if($homepage == 'single'){
			$postType = $this->options['homepage_post_type'];
			$postId = $this->options['homepage_post_id'];
			
			if(empty($postId) || empty($postType)){
				return "";
			}
			return $homepage."_".$postType."_".$postId;
		}
		return $homepage ;
	}
	
	private function get_colors(){
		return array(
			"tagTextColor" => $this->buildOptions['tagTextColor'],
			"tagBgColor" => $this->buildOptions['tagBgColor'],
			"feedBgColor" => $this->buildOptions['feedBgColor'],
			"feedTitleColor" => $this->buildOptions['feedTitleColor'],
			"feedContentTextColor" => $this->buildOptions['feedContentTextColor'],
			"screenBgColor" => $this->buildOptions['screenBgColor'],
			"actionBarTitleColor" => $this->buildOptions['actionBarTitleColor'],
			"actionBarBgColor" => $this->buildOptions['actionBarBgColor'],
			"authorTextColor" => $this->buildOptions['authorTextColor'],
			"timeTextColor" => $this->buildOptions['timeTextColor'],
			"statusBarBgColor" => $this->buildOptions['statusBarBgColor'],
		);
	}
	
	private function getLanguage(){
		if(isset($this->languageOptions) && !empty($this->languageOptions))
		{
			return $this->languageOptions;
		}
		$arr =  Array();
		$arr['k'] = 'v';
		return $arr;
	}
	
	private function get_adArray($adUnit, $adType, $provider, $freq, $showOnListPages = '1', $showOnSinglePages= '1'){
		$topAd =  array();
		$topAd['adId'] = trim($adUnit);
		$topAd['adProvider'] = $provider;
		$topAd['adType'] = $adType;
		$topAd['adFrequency'] = $freq;
                $adLocations = array();
                if($showOnListPages == '1'){
                    $adLocations[] = 'LIST';
                }
                if($showOnSinglePages == '1'){
                    $adLocations[] = 'SINGLE';
                }
                $topAd['adLocations'] = $adLocations;
		return $topAd;
	}
	private function get_monetise(){
		$monetise = array(
			"topAdUnitId" => $this->accountOptions[pw_mobile_app_settings::$topAdUnitKey],
			"bottomAdUnitId" => $this->accountOptions[pw_mobile_app_settings::$bottomAdUnitKey],
			"listViewAdUnitId" => $this->accountOptions[pw_mobile_app_settings::$listViewAdUnitKey],
			"listViewAdFreq" => $this->accountOptions[pw_mobile_app_settings::$listViewAdUnitFreqKey],
			"listViewAdType" => $this->accountOptions[pw_mobile_app_settings::$listViewAdUnitTypeKey],
			"interstitialAdUnitId" => $this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitKey],
			"interstitialAdFreq" => $this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitFreqKey],
			"appNextPlacementId" => $this->accountOptions[pw_mobile_app_settings::$appNextInterstitialAdUnitKey],	
			"appNextInterstitialAdType" => $this->accountOptions[pw_mobile_app_settings::$appNextInterstitialAdType],
			"bottomAdType" => $this->accountOptions[pw_mobile_app_settings::$bottomAdType],
			"topAdType" => $this->accountOptions[pw_mobile_app_settings::$topAdType]
		);
		$monetise['topAdUnitList'] = array();
		
		if(!empty($this->accountOptions[pw_mobile_app_settings::$mopubTopAdUnitKey])){
			$monetise['topAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$mopubTopAdUnitKey],
			'', 'MOPUB', 3, $this->accountOptions[pw_mobile_app_settings::$topAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$topAdShowOnSinglePages]);
		}
		
		if(!empty($this->accountOptions[pw_mobile_app_settings::$topAdUnitKey])){
			$monetise['topAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$topAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$topAdType], 'ADMOB', 3, $this->accountOptions[pw_mobile_app_settings::$topAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$topAdShowOnSinglePages]);
		}
		
                if(!empty($this->accountOptions[pw_mobile_app_settings::$topAppNextAdUnitKey])){
			$monetise['topAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$topAppNextAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$topAppNextAdType], 'APPNEXT', 3, $this->accountOptions[pw_mobile_app_settings::$topAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$topAdShowOnSinglePages]);
		}
                
		if(!empty($this->accountOptions[pw_mobile_app_settings::$mopubMiddleAdUnitKey])){
			$monetise['middleAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$mopubMiddleAdUnitKey],
			'', 'MOPUB', 
			$this->accountOptions[pw_mobile_app_settings::$listViewAdUnitFreqKey]
                        , $this->accountOptions[pw_mobile_app_settings::$middleAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$middleAdShowOnSinglePages] );
		}
		
		if(!empty($this->accountOptions[pw_mobile_app_settings::$listViewAdUnitKey])){
			$monetise['middleAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$listViewAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$listViewAdUnitTypeKey], 'ADMOB', 
			$this->accountOptions[pw_mobile_app_settings::$listViewAdUnitFreqKey]
                        , $this->accountOptions[pw_mobile_app_settings::$middleAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$middleAdShowOnSinglePages] );
		}
		
                if(!empty($this->accountOptions[pw_mobile_app_settings::$listViewAppNextAdUnitKey])){
			$monetise['middleAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$listViewAppNextAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$listViewAppNextAdUnitTypeKey], 'APPNEXT', 
			$this->accountOptions[pw_mobile_app_settings::$listViewAdUnitFreqKey]
                        , $this->accountOptions[pw_mobile_app_settings::$middleAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$middleAdShowOnSinglePages] );
		}
                
		if(!empty($this->accountOptions[pw_mobile_app_settings::$mopubBottomAdUnitKey])){
			$monetise['bottomAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$mopubBottomAdUnitKey],
			'', 'MOPUB', 3
                        , $this->accountOptions[pw_mobile_app_settings::$bottomAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$bottomAdShowOnSinglePages] );	
		}
		
		if(!empty($this->accountOptions[pw_mobile_app_settings::$bottomAdUnitKey])){
			$monetise['bottomAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$bottomAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$bottomAdType], 'ADMOB', 3
                        , $this->accountOptions[pw_mobile_app_settings::$bottomAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$bottomAdShowOnSinglePages] );	
		}
		
                if(!empty($this->accountOptions[pw_mobile_app_settings::$bottomAppNextAdUnitKey])){
			$monetise['bottomAdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$bottomAppNextAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$bottomAppNextAdType], 'APPNEXT', 3
                        , $this->accountOptions[pw_mobile_app_settings::$bottomAdShowOnListPages], $this->accountOptions[pw_mobile_app_settings::$bottomAdShowOnSinglePages] );	
		}
                
		if(!empty($this->accountOptions[pw_mobile_app_settings::$mopubInterstitialAdUnitKey])){
			$monetise['interstitialdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$mopubInterstitialAdUnitKey],
			'', 'MOPUB', 
			$this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitFreqKey]);
		}
		
		if(!empty($this->accountOptions[pw_mobile_app_settings::$appNextInterstitialAdUnitKey])){
			$monetise['interstitialdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$appNextInterstitialAdUnitKey],
			$this->accountOptions[pw_mobile_app_settings::$appNextInterstitialAdType], 'APPNEXT', 
			$this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitFreqKey]);
		}
		
		if(!empty($this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitKey])){
			$monetise['interstitialdUnitList'][] = $this->get_adArray($this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitKey],
			'', 'ADMOB', $this->accountOptions[pw_mobile_app_settings::$interstitialAdUnitFreqKey]);	
		}
		return $monetise;
	}

        function getMenuIcon($menu_id){
            global $menuiconpluginactive ;
            $menuicon = get_post_meta( $menu_id, 'menu-icons', true );
            if($menuiconpluginactive && $menuicon && is_array($menuicon)){
                if($menuicon['type'] == 'fa'){
                    return str_replace("fa-", "faw_", $menuicon['icon']);
                }
            }
            return null;
        }

        function getJsonLink($objectType, $objectId, $url){
                /*if($objectType =="category"){
                        return json_url("/taxonomies/category/terms/".$objectId);
                }else if($objectType =="page"){
                        return json_url("/pages/".$objectId);
                }else if($objectType =="pose"){
                        return json_url("/posts/".$objectId);
                } else */
                {
                        return $url;
                }
        }

        function get_nav_items($id) {
            global $json_api;

            $menu_output = wp_get_nav_menu_items( $id );
            //print_r($menu_output);
            if($menu_output)
            {
                    $output = array();
                    foreach($menu_output as $el){
                            $slug = "";
                            if($el->object == 'product_cat'){
                                    $category = get_term_by('id', $el->object_id, $el->object, 'ARRAY_A');

                                    if(isset($category)){
                                            $slug = $category['slug'];
                                    }
                            }
                            //$output = $el;
                            //continue;
                            //checking if menu item is a custom taxonomy
                            if ($el->object != "product_tag" && $el->object != "product_cat" 
                                    && in_array($el->object, $this->custom_taxonomies)){
                                $term = get_term_by('id', $el->object_id, $el->object, 'ARRAY_A');

                                if(isset($term)){
                                        $slug = $term['slug'];
                                }
                                $el->taxonomy_name = $el->object;
                                $el->object = 'custom_taxonomy';
                            }
                            //Checking if menu item is a custom post type
                            else if(in_array($el->object, $this->custom_post_types)){
                                $el->taxonomy_name = $el->object;
                                $el->object = 'custom_post_type';
                            }
                            
                            $filter = array(
                                    "id"=>$el->ID,
                                    "parent_id"=>$el->menu_item_parent,
                                    "menu_order"=>$el->menu_order,
                                    "name"=>$el->title,
                                    "object_type"=>$el->object,
                                    "object_id"=>($el->object == "custom" ? "0" : $el->object_id ),
                                    "taxonomy_name" => $el->taxonomy_name,
                                    "taxonomy" => $el->taxonomy_name,
                                    "link"=> $this->getJsonLink($el->object,$el->object_id, $el->url),
                                    "slug"=> $slug,
                                    "icon" => $this->getMenuIcon($el->ID)
                            );
                            $output[] = $filter;
                    }
                    $count = count($output);
            }
            else{
                    $count = 0;
            }

            if ($count == "0") {
                    return array(
                            "output" => "Empty Menu",
                            "count" => $count
                    );
            } else {
                    return array(
                            "output" => $output,
                            "count" => $count
                    );

            }
        }
        
        public function get_menu_helper($id){
            global $menuiconpluginactive ;
            $menuiconpluginactive= $this->menu_icons_active;

            // Make sure we have key/value query vars
            if ( $id ) {
                if ($id) {
                        $menuid = $id;
                        $menuloc = "";
                        $menu_items = $this->get_nav_items($menuid);
                }
                if($menu_items["count"] == 0){
                    return array();
                    //return new WP_Error( 'json_invalid_menu_id', __( 'Invalid or Empty Menu.' ), array( 'status' => 400 ) );
                }
                return $menu_items["output"];
            } else {
                return array();
                //return new WP_Error('json_invalid_menu_id', "Include the parameter 'menu_id' with an appropriate string value.", array( 'status' => 400 ));
            }
        }
        /*
	reference: https://github.com/mcnasby/wp-json-api-menu-controller/blob/master/api-controllers/menus.php
	*/
  public function get_menu( $context = 'view' ) {
     	$id = $this->options['app_menu'];
        return $this->get_menu_helper($id);
  }
  
  /**
	 * Create a new comment.
	 *
	 * Comment will be attached to $id. $data is the data for the new comment.
	 *
	 * @param int $id Post ID
	 * @param array $data
	 * @return int|mixed|void|WP_Error|WP_JSON_ResponseInterface
	 */
	public function new_comment( $id, $data ) {
		unset( $data['ID'] );
		$result = $this->insert_comment( $id, $data );
		if ( $result instanceof WP_Error ) {
			return $result;
		}
		/*$response = json_ensure_response($result);
		$response->set_status( 201 );
		$response->header( 'Location', json_url( '/comments/' . $result ) );*/
		//echo $result;
		return $result;
	}
	
        public function new_comment_v2($data){
            $postid =  $data['id'];
            unset( $data['id'] );
            return $this->new_comment_new($postid, $data);
        }

        public function new_comment_new( $id, $data ) {
		unset( $data['ID'] );
		$result = $this->insert_comment( $id, $data );
		if ( $result instanceof WP_Error ) {
                    return $result;
		}
		/*$response = json_ensure_response($result);
		$response->set_status( 201 );
		$response->header( 'Location', json_url( '/comments/' . $result ) );*/
		//echo $result;
		$comment =  get_comment($result);
                
                $resp  = array();
                
                $resp['ID'] = $comment->comment_ID;
                $resp['status'] = 'hold';
                if($comment->comment_approved == '1'){
                    $resp['status'] = 'approved';    
                }
                return $resp;
	}
		/**
	 * Retrieve comments
	 *
	 * @param int $id Post ID to retrieve comments for
	 * @return array List of Comment entities
	 */
	public function get_comments( $id ) {
		return get_comment($id);
	}
	
	/**
	 * Update/insert comment.
	 *
	 * If $data comments a key ID, then it will update the comment with that ID.
	 *
	 * @param int $post_id Post ID for comment
	 * @param array $data Comment data
	 * @return int|mixed|void|WP_Error
	 */
	protected function insert_comment( $post_id, $data ) {
		$comment = array(
			'comment_post_ID' => $post_id,
		);
		$update = false;
		
		//print_r($data);
	
		// Permissions check
		/*if ( ! current_user_can( 'edit_comment' ) )
			return new WP_Error( 'json_cannot_create', __( 'Sorry, you are not allowed to edit comments on this site.' ), array( 'status' => 400 ) );
			*/
		// Comment date
		if ( ! empty( $data['date'] ) ) {
			list( $comment['comment_date'], $comment['comment_date_gmt'] ) = $this->server->get_date_with_gmt( $data['date'] );
		} elseif ( ! empty( $data['date_gmt'] ) ) {
			list( $comment['comment_date'], $comment['comment_date_gmt'] ) = $this->server->get_date_with_gmt( $data['date_gmt'], true );
		}
		// Comment content
		if ( isset( $data['content'] ) ) {
			$comment['comment_content'] = $data['content'];
		}
		// User id - since the user must have edit_comment caps to do this, we might as well let them
		// assign whatever user to this comment that they want.
		if ( isset( $data['user_id'] ) ) {
			$comment['user_id'] = $data['user_id'];
		}
		// Comment karma
		if ( isset( $data['karma'] ) ) {
			$comment['comment_karma'] = (int) $data['karma'];
		}
		// Comment agent - this can't be updated
		if ( ! empty( $data['agent'] ) ) {
			$comment['comment_agent'] = $data['agent'];
		}
		// Comment approved
		if ( isset( $data['approved'] ) ) {
			$comment['comment_approved'] = (int) $data['approved'];
		}
		// Comment author stuff. The IP can't be updated once it's set.
		if ( isset( $data['author'] ) ) {
			$comment['comment_author'] = $data['author'];
		} if ( isset( $data['author_email'] ) ) {
			$comment['comment_author_email'] = $data['author_email'];
		} if ( isset( $data['author_IP'] ) ) {
			$comment['comment_author_IP'] = $data['author_IP'];
		} if ( isset( $data['author_url'] ) ) {
			$comment['comment_author_url'] = $data['author_url'];
		}
		// Parent
		if ( isset( $data['parent'] ) ) {
			$comment['comment_parent'] = (int) $data['parent'];
		} elseif ( ! $update ) {
			$comment['comment_parent'] = 0;
		}
		// Type
		if ( isset( $data['type'] ) ) {
			$comment['comment_type'] = $data['type'];
		}
		// Pre-insert hook
		$can_insert = apply_filters( 'json_pre_insert_comment', true, $comment, $data, $update );
		if ( is_wp_error( $can_insert ) ) {
			return $can_insert;
		}
		define('DOING_AJAX',1);
		try{
			$comment_ID = $update ? wp_update_comment( $comment, true ) : wp_new_comment( $comment);
		}catch(Exception  $e){
			echo "Exception ".$e->getMessage();
		}
		// Comment meta
		// TODO: implement this
		if ( is_wp_error( $comment_ID ) ) {
			return $comment_ID;
		}
		do_action( 'json_insert_comment', $comment, $data, $update );
		return $comment_ID;
	}
}
?>