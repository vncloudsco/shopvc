<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
require_once("androapp_utils.php");
require_once dirname( __FILE__ ) . '/class-pw-wp-menu.php';
require_once dirname( __FILE__ ) . '/class-pw-wp-gcm-register.php';
require_once dirname( __FILE__ ) . '/class-pw-wp-woocommerce.php';
require_once dirname( __FILE__ ) . '/class-pw-wp-woo.php';
require_once dirname( __FILE__ ) . '/class-pw-wp-posts.php';
class androapp_functions {
	
	protected $options;
	protected $accountOptions;
	protected $scriptsOptions;
	protected $androAds;
	protected $buildOptions;
	protected $postContentOptions;
	protected $pw_wp_menu;
        protected $pwapp_gcm;
        protected $pwapp_woo;
        protected $pwapp_woo_post;
        protected $pwapp_posts;
        protected $custom_taxonomies;
        protected $custom_post_types;
        public function __construct(){
                add_action('init', array($this, 'add_mapped_shortcodes_on_init'));
		add_filter( 'woocommerce_api_product_response', array($this,'androapp_woocommerce_api_product_response'), 10, 4);
		add_filter( 'json_prepare_post', array($this,'pw_mobile_prepare_post'), 10, 3 );
                add_filter( 'rest_prepare_post', array($this,'pw_mobile_prepare_post_v2'), 10, 3 );
                add_filter( 'rest_prepare_page', array($this,'pw_mobile_prepare_post_v2'), 10, 3 );
                add_filter( 'json_prepare_comment', array($this,'pw_mobile_prepare_comment'), 10, 3 );
                add_filter( 'rest_prepare_comment', array($this,'pw_mobile_prepare_comment_v2'), 10, 3 );
                
                add_filter( 'comment_text', array($this,'pw_comment_filter'), 97);
		add_filter( 'wp_footer',  array($this,'add_css_bottom') );
		add_action( 'wp_json_server_before_serve', array($this,'myplugin_api_init'));
		add_action( 'send_push_notification_after_publish', array($this,'send_push_notification_after_publish'), 10, 10 );
		add_filter( 'the_content', array($this,'androapp_after_post_filter')  ); 
		add_filter( 'the_content', array($this,'androapp_before_post_filter')  ); 
		add_action('wp_head', array($this, 'androapp_header_action') );
		
		//add_action( 'publish_post', array($this,'schedule_push_notification_save') );
		//add_action( 'new_to_publish', array($this,'schedule_push_notification_save') );
		//add_action( 'pending_to_publish', array($this,'schedule_push_notification_save') );
		//add_action( 'draft_to_publish', array($this,'schedule_push_notification_save') );
		add_action( 'save_post', array($this, 'on_save_post'), 1, 3 );
                add_action ('transition_post_status',  array(&$this,'pwapp_post_transition'),10,3);
                
                #add_action('post_submitbox_misc_actions', array(&$this, 'androapp_notification_settings'));
                add_action('admin_init', array( $this, 'add_androapp_notification_settings' ));
                
		$this->options = get_option("pw-mobile-app");
		$this->accountOptions = get_option("androapp_account_settings");
		$this->scriptsOptions = get_option("androapp_scripts_detected");
		$this->buildOptions = get_option("pw-mobile-build-options");
		$this->androAds = get_option("androapp_ads");
		$this->postContentOptions = get_option("androapp_post_content_tab");
                
                                
		$this->pw_wp_menu = new Pw_Wp_Menu();
                $this->pwapp_gcm = new Pw_Wp_Gcm_Register();
		$this->pwapp_woo = new Pw_Wp_Woocommerce();
                $this->pwapp_woo_post = new Pw_Wp_Woo(null );
                $this->pwapp_posts = new Pw_Wp_Posts(null);
                
                add_action( 'init', array( $this, 'registerCustomPostTypes' ), 30 );
                add_action( 'init', array( $this, 'registerCustomTaxonomies'), 25 );
                //$this->registerCustomPostTypes();
                #add_action('init', array( $this, 'wpse70000_add_excerpt'), 100);
                //Uncomment below to add author and excerpt support for custom post types, or add it via CPT
                #add_filter( 'register_post_type_args', array( $this, 'add_custom_type_support'), 10, 2 );
	}

        
        function add_mapped_shortcodes_on_init()
        {
            if(class_exists('WPBMap')){
                WPBMap::addAllMappedShortcodes();
            }
        }

        function add_custom_type_support( $args, $post_type ) {
            #if ($post_type != 'POST_TYPE_NAME') // set post type
            #    return $args;
            if(!empty($args['supports'])){
                $supports = $args['supports'];
                if(!isset($supports))
                {
                    $supports = array();
                    $args['supports'] = array();
                }
                if(!in_array('author', $supports)){
                    array_push($args['supports'], 'author');
                }
                if(!in_array('excerpt', $supports)){
                    array_push($args['supports'], 'excerpt');
                }
            }
            return $args;
        }


        /**
        * Add REST API support to an already registered taxonomy.
        */
       
       function registerCustomTaxonomies() {
         global $wp_taxonomies;
         $args=array('public'   => true, '_builtin' => false);
          $output = 'names'; // or objects
          $operator = 'and';
          $this->custom_taxonomies =get_taxonomies($args, $output, $operator); 

          foreach($this->custom_taxonomies as $taxonomy_name){
                $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
                // Optionally customize the rest_base or controller class
                $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
                $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
          }
       }

        function registerCustomPostTypes(){
            global $wp_post_types;
            $args = array('public' => true, '_builtin' => false);
            $post_types = get_post_types( $args, 'names', 'and' );
            
            
            //if(class_exists('WP_REST_Posts_Controller'))
            {
                foreach ( $post_types  as $post_type ) {
                    $wp_post_types[$post_type]->show_in_rest = true;
                    $wp_post_types[$post_type]->rest_base = $post_type;
                    $wp_post_types[$post_type]->rest_controller_class = 'WP_REST_Posts_Controller';
                    #$wp_post_types[$post_type]->public = true;
                    #$wp_post_types[$post_type]->publicly_queryable= true;
                    #$wp_post_types[$post_type]->capability_type = 'post';
                    #$wp_post_types[$post_type]->query_var = true;
                    #$wp_post_types[$post_type]->supports = array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' );
                    
                    add_filter( 'rest_prepare_'.$post_type, array($this,'pw_mobile_prepare_post_v2'), 10, 3 );
                }
            }  
        }
        
        function pw_comment_filter($content) {
            if(!is_single())
            {
                remove_filter('comment_text','stallion_sc_link2', 99);
            }
            return $content;
        }

        function add_androapp_notification_settings(){
            //Add metabox for posts and pages
            add_meta_box('androapp_push_notifications', 'AndroApp Push Notification',
                array( $this, 'androapp_notification_settings' ), 'post', 'side','high');
               
            add_meta_box('androapp_push_notifications', 'AndroApp Push Notification',
                array( $this, 'androapp_notification_settings' ), 'page', 'side','high');
            
            // Adding metabox for custom post types
            $args = array('public' => true, '_builtin' => false);
            $post_types = get_post_types( $args, 'names', 'and' );
            foreach ( $post_types  as $post_type ) {
                add_meta_box('androapp_push_notifications', 'AndroApp Push Notification',
                       array( $this, 'androapp_notification_settings' ), $post_type, 'side','high');
            }
        }
        
	// Add checkbox
	function androapp_notification_settings() {
		global $post;
		$prevSelectedValue = get_post_meta($post->ID, "androapp_post_notify", "on");
		$dont_send_push_notification =  ($prevSelectedValue == "on" ? ' checked' : '');
		if(!$prevSelectedValue){
		    $dont_send_push_notification = ($this->accountOptions[pw_mobile_app_settings::$defaultPostPushNotification] == '1' ? ' checked': '');
		}

		?>
		<div class="misc-pub-section">
		<input id="androapp_post_notify" type="checkbox" name="androapp_post_notify"<?php echo $dont_send_push_notification; ?> />
		<label for="androapp_post_notify">Do not send Push Notification</label>
		</div>
		<?php
	}
        
       public function on_save_post($post_id, $post, $updated) {
            if(!$this->isValidPostType($post->post_type)){
                return;
            }
            
            if(empty($_POST)){
                return;
            }
           // Check the user's permissions.
            if ( 'page' == $_POST['post_type'] ) {
              if (!current_user_can( 'edit_page', $post_id)) {
                return $post_id;
              }
            } else {
              if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
              }
            }
    
            if (array_key_exists('androapp_post_notify', $_POST)) {
		update_post_meta($post_id, 'androapp_post_notify', "on");
            } else {
                update_post_meta($post_id, 'androapp_post_notify', "off");
            }
       }
       
	function pwapp_post_transition($new_status, $old_status, $post) {
            if($post->post_type != "page" && $post->post_type != "post"){
                return;
            }
            $was_posted = !empty($_POST);
            if($was_posted){
                if (array_key_exists('androapp_post_notify', $_POST)) {
                    update_post_meta($post->ID, 'androapp_post_notify', "on");
                } else {
                    update_post_meta($post->ID, 'androapp_post_notify', "off");
                }
            }
            if (($old_status != 'publish') &&  ($new_status == 'publish') && 
                    ($this->isValidPostType($post->post_type))){
            //if(true){
                $dont_send_push_notification = get_post_meta($post->ID, "androapp_post_notify", "on");
                //echo "<h1>dont send $dont_send_push_notification</h1>";
                if(empty($dont_send_push_notification) || $dont_send_push_notification == 'off')
                {
                    $this->schedule_push_notification($post);
                }
            }
	}

	function schedule_push_notification_save(){
		global $post;
		$this->schedule_push_notification($post);
	}
	
	function schedule_push_notification($post){
            $postType = $post->post_type;
            $postid = $post->ID;
            if(class_exists('WP_JSON_Posts')){
                $wp_server_posts = null;
                if($postType == 'page'){
                    $wp_server_posts = new WP_JSON_Pages(new WP_JSON_Server());
                }else{
                    $wp_server_posts = new WP_JSON_Posts(new WP_JSON_Server());
                }
                $response =  $wp_server_posts->get_post($postid, 'view');
                if(isset($response) && isset($response->data)){
                        $wppost = $response->data;
                }
                $title = $wppost['title'];
                $excerpt = $wppost['pwapp_excerpt'];
            }else if(class_exists('WP_REST_Posts_Controller')){
                $wp_server_posts = new WP_REST_Posts_Controller($postType);
                $request = array();
                $request['id'] = $postid;
                $response =  $wp_server_posts->get_item($request);   
                //print_r($response);
                if(isset($response) && isset($response->data)){
                    $wppost = $response->data;
                }
                $title = $wppost['title']['rendered'];
                $excerpt = $wppost['excerpt']['rendered'];
            }
         
            $postimage = $wppost['featuredimage'];
            if(empty($postimage)){
                $postimage = $wppost['pwapp_post_image'];
            }
            $postlink = $wppost['link'];

            $cache = "yes";
            if(isset($disableNotificationCache) && $disableNotificationCache == 'yes'){
                $cache = "no";
            }

            $disableBulkSend = $accountOptions[pw_mobile_app_settings::$disableBulkSend];	
            $disableNotificationCache = $accountOptions[pw_mobile_app_settings::$disableNotificationCache];

            wp_schedule_single_event( time()+ 10*30 , 'send_push_notification_after_publish',
             array ($post->ID, $post->post_title, 
                 $excerpt, $postimage, $postlink, $cache, $postType,
                 $this->accountOptions[ANDROAPP_GCM_API_KEY] , 
                "stack",
             !($this->accountOptions[pw_mobile_app_settings::$disableBulkSend] == 1) 
              )); 
	}
	
	function send_push_notification_after_publish($post_id, $post_title, 
                 $excerpt, $postimage, $link, $cache, $postType,
                $google_api_key, $notification_type, $isBulkSend)
	{
		require_once PW_MOBILE_PATH.'gcm/send_message.php';
		sendPushNotification(array("post_id" => $post_id, "title" => $post_title,
                     "excerpt" =>$excerpt, "postImage" => $postimage, "link" => $postlink, 
                    "cache" => $cache, "postType" => $postType,
                    "notification_type" => $notification_type), $google_api_key, $isBulkSend);
	}

	function removeShortcodes($shortcodes){
		global $shortcode_tags;
		$shortCodesFunctionArray = array();
		foreach($shortcodes as $shortcode){
			if(!empty($shortcode_tags[$shortcode])){
				$shortCodesFunctionArray[$shortcode] = $shortcode_tags[$shortcode];
				unset($shortcode_tags[$shortcode]);
			}
		}
		return $shortCodesFunctionArray;
	}
	
	function addShortcodesBack($shortCodesFunctionArray){
		global $shortcode_tags;
		foreach($shortCodesFunctionArray as $key => $shortcode){
			$shortcode_tags[$key] = $shortcode;
		}
	}

	function getScripts(){
		$out = "";
		if(!empty($this->scriptsOptions) && !empty($this->options[pw_mobile_app_settings::$selectedScripts])){
			$scripts = $this->scriptsOptions['scripts'];
			$selectedScripts = explode("\n", $this->options[pw_mobile_app_settings::$selectedScripts]);
			
			foreach($selectedScripts as $handle){
				$handle = trim($handle);
				if($scripts[$handle]){
					$out .= "<script type='text/javascript' src='".$scripts[$handle]."' ></script>";
				}
			}
		}
		
		if(!empty($this->options[pw_mobile_app_settings::$postProcessedCss])){
			$out .= "<style type=\"text/css\">".$this->options[pw_mobile_app_settings::$postProcessedCss]."</style>";
		}
		return $out;
	}

	function getShareText($post, $data, $key){
		if($key == "TITLE"){
			return $data['title'];
		}
		else if($key == "EXCERPT"){
                    return strip_tags($this->androapp_get_rendered_value($data['excerpt']));
		}
		else if($key == "SEO"){
			$wpseo_metadesc = get_post_meta($post['ID'], '_yoast_wpseo_metadesc', true);
			if(!empty($wpseo_metadesc)){
				return $wpseo_metadesc;
			}
			else{
				if (class_exists('WPSEO_Meta')) {
					return $this->get_seo_description($post['ID']);
				}
			}
			return $data['title'];
		}else if($key == "FULL"){
			return wpautop($post['post_content']);
		}
		return "";
	}
	
        function getPostContentTypeFromPostId($postId, $defaultPostContentType){
            $loadUrlPostIds = array_map('trim', explode(",", $this->options['loadurl_postids']));
            $preProcessedPostIds = array_map('trim', explode(",", $this->options['preprocessed_postids']));
            $postProcessedPostIds = array_map('trim', explode(",", $this->options['postprocessed_postids']));
            $loadImagesPostIds = array_map('trim', explode(",", $this->options['loadimages_postids']));
            
            if(in_array($postId, $loadUrlPostIds)){
                return "loadurl";
            }else if(in_array($postId, $preProcessedPostIds)){
                return "preprocessed";
            }else if(in_array($postId, $postProcessedPostIds)){
                return "postprocessed";
            }else if(in_array($postId, $loadImagesPostIds)){
                return "loadimages";
            }
            return $defaultPostContentType;
        }
        
	function androapp_woocommerce_api_product_response($data, $product, $fields, $server){
		
		global $wp_query;
		global $post;
		$orig_post = $post;
		$prev_ishome = $wp_query->is_home;
		$wp_query->is_home = false;
		
		if(is_array($product)){
			$postid = $product['ID'];
		}else{
			$postid = $product->id;
		}

		$post_obj = get_post($postid);
		$post = $post_obj;
		setup_postdata( $post_obj );
		$data['pwapp_feed_image_dimension_type'] = $this->options['image_dimension'];
		$share_text_fn = $this->options['share_function_name'];
		$firstImage = $data['featured_src'];
		if($this->options['image_dimension'] != 'noimage'){
			$data['pwapp_post_image'] = $firstImage;
		}
		if($this->options['share_image_preference'] != 'noimage'){
			$data['share_image'] = $firstImage;
		}
		
		$preview = "";
		
		if($this->options['excerpt_preference'] == 'seo_meta_desc'){
			$wpseo_metadesc = get_post_meta($postid, '_yoast_wpseo_metadesc', true);
			if(!empty($wpseo_metadesc)){
				$preview = html_entity_decode($wpseo_metadesc,null,'UTF-8');
			}
			else{
				if (class_exists('WPSEO_Meta')) {
					$preview = $this->get_seo_description($postid);
				}
			}
		}else if($this->options['excerpt_preference'] == 'none'){
			$preview = "";
		}else{
			$this->options['excerpt_preference'] = "excerpt";
			$preview = $data['description'];
		}
		
		$data['pwapp_excerpt']   = $preview;
		$data['excerpt'] = $preview;
		
		$postcontent = "";
                $postContentType = $this->getPostContentTypeFromPostId($postid, $this->options['post_content']);
		if($postContentType == 'preprocessed'){
			$shortcodesToRemove = array('caption', 'wp_caption', 'gallery', 'playlist', 'audio','video');
			$shortcodesArray = $this->removeShortcodes($shortcodesToRemove);
			$stripped_content = strip_shortcodes(wpautop($post['post_content']));
			$this->addShortcodesBack($shortcodesArray);
			$postcontent = do_shortcode($stripped_content);
		}else if($postContentType == 'postprocessed'){
			$postcontent = $this->getScripts()."<div class='androapp'>"
                                .$this->androapp_get_rendered_value($data['content'])
                                ."</div>";
		}
		
		$data['pwapp_post_content'] = $postcontent;
		$data['pwapp_post_content_type'] = $postContentType;	
		$data['pwapp_preview_type'] = $this->options['excerpt_preference'];
		
		$data['share_text'] = "";
		if(!empty($share_text_fn) && function_exists($share_text_fn) ){
			$data['share_text'] = $share_text_fn($product, $data['link']);
		}
		if(empty($data['share_text'])){
			if(empty($data['share_image'])){
				$data['share_text'] = $this->getShareText($product, $data, $this->options[pw_mobile_app_settings::$sharePreference]);
			}else{
				$data['share_text'] = $this->getShareText($product, $data, $this->options[pw_mobile_app_settings::$shareTextWithImage]);
			}
			
			if(!empty($this->options[pw_mobile_app_settings::$shareSuffixText])){
				$data['share_text'] .= "\n".$this->options[pw_mobile_app_settings::$shareSuffixText];
			}
			
			if($this->options[pw_mobile_app_settings::$shareSuffixLink] == "HOME"){
				$data['share_text']  .= " ".get_bloginfo('url');
			}
			else if($this->options[pw_mobile_app_settings::$shareSuffixLink] == "POST"){
				$data['share_text']  .= " ". $data['permalink'];
			}	
		}else{
			if($this->options[pw_mobile_app_settings::$shareImageWithCustomFunction] == "1"){
				$data['share_image'] = null;
			}
		}

		if($this->options[pw_mobile_app_settings::$showCommentsCount] == "1" && $this->options[pw_mobile_app_settings::$commentsProvider] == "wordpress"){
			$comments_count = wp_count_comments( $postid);
			$data['androapp_comments_count'] = $comments_count->approved;
		}
		//$data['supercache'] = "androappsupercache";
		$post = $orig_post;
                wp_reset_postdata();
		$wp_query->is_home = $prev_ishome;
		return $data;
	}
	
	function androapp_header_action( ) { 
	    if ( is_single() && isAndroAppRequest()) {
		echo $this->postContentOptions[pw_mobile_app_settings::$headerScript];
	    }
	}
	
	function androapp_before_post_filter( $content ) { 
	    if ( is_single() && isAndroAppRequest()) {
		$content = $this->postContentOptions[pw_mobile_app_settings::$beforePostContent].$content;
	    }
	    return $content;
	}
	
	function androapp_after_post_filter( $content ) { 
	    if ( is_single() && isAndroAppRequest()) {
		$content = $content.$this->postContentOptions[pw_mobile_app_settings::$afterPostContent];
	    }
	    return $content;
	}
	function add_pre_post_content($data){
            if (array_key_exists('pwapp_after_post_content', $data))
            {
                    $data['pwapp_after_post_content'] = $this->postContentOptions[pw_mobile_app_settings::$afterPostContent].$data['pwapp_after_post_content'];
            }else{
                    $data['pwapp_after_post_content'] = $this->postContentOptions[pw_mobile_app_settings::$afterPostContent];	
            }

            if (array_key_exists('pwapp_before_post_content', $data)){
                    $data['pwapp_before_post_content'] = $this->postContentOptions[pw_mobile_app_settings::$beforePostContent].$data['pwapp_before_post_content'];
            }else{
                    $data['pwapp_before_post_content'] = $this->postContentOptions[pw_mobile_app_settings::$beforePostContent];
            }

            if (array_key_exists('androapp_header', $data)){
                    $data['androapp_header'] = $this->postContentOptions[pw_mobile_app_settings::$headerScript].$data['androapp_header'];
            }else{
                    $data['androapp_header'] = $this->postContentOptions[pw_mobile_app_settings::$headerScript];
            }
            return $data;
	}
	
        function pw_mobile_prepare_comment_v2($data, $comment, $request){
            $data->data = $this->pw_mobile_prepare_comment($data->data, $comment , 'view');
            return $data;
        }
        
        function pw_mobile_prepare_comment($data, $comment, $context){
            $data['androapp_content'] = $this->androapp_get_rendered_value($data['content']);
            $data['androapp_author_name'] = $comment->comment_author;
            return $data;
        }
        
        function pw_mobile_prepare_post_v2($data, $post, $request){
            //print_r($data);
            $data->data = $this->pw_mobile_prepare_post($data->data, (array) $post, 'view');
            return $data;
        }
        
        function androapp_get_rendered_value($input){
            if(is_array($input)){
                return $input['rendered'];
            }
            return $input;
        }
        
        function androapp_getAuthor($id){
            global $post;
            $author = array();
            $author['ID'] = $id;
            $author['name'] = get_the_author_meta('display_name', $id );
            $author['slug'] = get_the_author_meta('user_nicename', $id );
            return $author;
        }
        
        function androapp_getTerms(){
            global $post;
            $terms = array();
            $categories =  get_the_category();
            if($categories){
                foreach($categories as $category){
                    $category->ID = $category->term_id;
                }
                $terms['category'] = $categories;
            }
            $tags = get_the_tags();
            if($tags){
                foreach($tags as $tag){
                    $tag->ID = $tag->term_id;
                }
                $terms['post_tag'] = $tags;
            }
            
            $customTaxonomies = array();
            
            foreach ($this->custom_taxonomies as $taxonomy){
                $customTerms = get_the_terms($post, $taxonomy);
                if($customTerms){
                    foreach($customTerms as $customTerm){
                        $customTaxonomies[$customTerm->term_id] = $customTerm;    
                    }
                    
                }
            } 
            if(!empty($customTaxonomies)){
                $terms['taxonomies'] = $customTaxonomies;
            }
            
            return $terms;
        }
        
        //returns true for post, page and custom post type
        function isValidPostType($postType){
            if(empty($postType)){
                return false;
            }
            if($postType == "attachment" || $postType == "revision" || $postType == "nav_menu_item"
                    || $postType == "custom_css" || $postType == "customize_changeset"){
                return false;
            }
            return true;
        }
	function pw_mobile_prepare_post($data, $post_array, $context) {
		if(!$this->isValidPostType($data['type'])){
                    return $data;
		}
		global $wp_query;
		global $post;
		$data  = $this->add_pre_post_content($data);
		$orig_post = $post;
		$prev_ishome = $wp_query->is_home;
		$wp_query->is_home = false;
		$post_obj = get_post($post_array['ID']);
		$post = $post_obj;
		//setup_postdata( $post_obj );
                
               // print_r($post);
                //return "";
                #$data['custom_taxonomies'] = $this->custom_taxonomies;
                $data['pwapp_author'] = $this->androapp_getAuthor($data['author']);
                $data['pwapp_terms'] = $this->androapp_getTerms();
		$data['pwapp_feed_image_dimension_type'] = $this->options['image_dimension'];
		$share_text_fn = $this->options['share_function_name'];
		$firstImage = null;
		if(has_post_thumbnail( $post_array['ID'] )){
			$imagesize = 'full';
			if($this->buildOptions['androapp_theme'] == 'compact'){
				$imagesize = 'thumbnail';
			}
			$attch = wp_get_attachment_image_src( get_post_thumbnail_id( $post_array['ID'] ), $imagesize);
			if(is_array($attch) && count($attch) > 0){
				$firstImage = $attch[0];
				$data['featuredimage'] = $attch[0];
			}
		}
		if(empty($firstImage)){
			if(empty($this->options[pw_mobile_app_settings::$useOnlyFeaturedImage]) || $this->options[pw_mobile_app_settings::$useOnlyFeaturedImage] != 1){
				$firstImage = androapp_get_first_image($post_array['post_content']);
			}
		}
                
                //echo "content ".$post_array['post_content'];
                
		if($this->options['image_dimension'] != 'noimage'){
			$data['pwapp_post_image'] = $firstImage;
		}
		if($this->options['share_image_preference'] != 'noimage'){
			$data['share_image'] = $firstImage;
		}
		$data['featured_image_showhide'] = $this->options['featured_image_showhide'];
                
                if(!empty($data['featured_media']) && $data['featured_media'] != 0){
                    $featuredimagejsobject = wp_prepare_attachment_for_js($data['featured_media']);
                    $featured_image = array();
                    $featured_image['ID'] = $data['featured_media'];
                    $featured_image['excerpt'] = $featuredimagejsobject['caption'];
                    $featured_image['title'] = $featuredimagejsobject['title'];
                    if(is_array($featuredimagejsobject['sizes']) && 
                            is_array($featuredimagejsobject['sizes']['large'])){
                        $featured_image['source'] = $featuredimagejsobject['sizes']['large']['url'];
                    }
                    
                    if(empty($featured_image['source'])){
                        $featured_image['source']  = $featuredimagejsobject['url'];
                    }
                    $data['featured_image'] = $featured_image;
                }
		$preview = "";
		
		if($this->options['excerpt_preference'] == 'seo_meta_desc'){
			$wpseo_metadesc = get_post_meta($post_array['ID'], '_yoast_wpseo_metadesc', true);
			if(!empty($wpseo_metadesc)){
				$preview = html_entity_decode($wpseo_metadesc,null,'UTF-8');
			}else{
				if (class_exists('WPSEO_Meta')) {
					$preview = $this->get_seo_description($post_array['ID']);
				}
			}
		}else if($this->options['excerpt_preference'] == 'none'){
			$preview = "";
		}else{
			$this->options['excerpt_preference'] = "excerpt";
			$preview = $this->androapp_get_rendered_value($data['excerpt']);
		}
		
		$data['pwapp_excerpt']   = $preview;
		
         
                $data['pwapp_title']  = $this->androapp_get_rendered_value($data['title']);
                
                
		$postcontent = "";
		if (array_key_exists('pwapp_before_post_content', $data)){
			$postcontent = $data['pwapp_before_post_content'];
		}
		$after_post_content = "";
		if (array_key_exists('pwapp_after_post_content', $data)){
			$after_post_content = $data['pwapp_after_post_content'];
		}
		
                $postContentType = $this->getPostContentTypeFromPostId($post_array['ID'], $this->options['post_content']);
		$failoverPostContentType = $this->options['failover_post_content'];
                if($postContentType == 'loadimages'){
                    if(empty($failoverPostContentType)){
                        $failoverPostContentType = 'postprocessed';
                    }
                }else{
                    $failoverPostContentType = "";
                }
                
                if($postContentType == 'preprocessed' || $failoverPostContentType =='preprocessed'){
			$shortcodesToRemove = array('caption', 'wp_caption', 'gallery', 'playlist', 'audio','video');
			$shortcodesArray = $this->removeShortcodes($shortcodesToRemove);
			$stripped_content = strip_shortcodes(wpautop($post_array['post_content']));
			$this->addShortcodesBack($shortcodesArray);
			$stripped_content .= $after_post_content;
			$postcontent .= do_shortcode($stripped_content);
                        $data['androapp_image_list'] = androapp_get_all_images($postcontent);
		}else if($postContentType == 'postprocessed' || $failoverPostContentType =='postprocessed'){
			//using $data['content'] for instagram video for autostrada.tv, changing back to post_array
			$content = do_shortcode($postcontent)
                                .$this->androapp_get_rendered_value($post_array['post_content'])
                                .do_shortcode($after_post_content);
			$content = apply_filters( 'the_content', $content );//apply filters for related posts
			$postcontent = $this->getScripts()."<div class='androapp'>".$content."</div>";
                        $data['androapp_image_list'] = androapp_get_all_images($postcontent);
		}else{
                    $data['androapp_image_list'] = androapp_get_all_images($this->androapp_get_rendered_value($data['content']));
                }
		
                if(count($data['androapp_image_list']) == 0 && !empty($failoverPostContentType) ){
                    $postContentType = $failoverPostContentType;
                }
                    
		if(isset($this->accountOptions[pw_mobile_app_settings::$stripAdsenseUnits]) && $this->accountOptions[pw_mobile_app_settings::$stripAdsenseUnits] == '1'){
			$data['pwapp_post_content'] = $this->stripAdsense($postcontent);
		}else{
			$data['pwapp_post_content'] = $postcontent;
		}
		
		$data['pwapp_post_content_type'] = $postContentType;	
		$data['pwapp_preview_type'] = $this->options['excerpt_preference'];
		$data['androapp_failover_post_content_type'] = $failoverPostContentType;
		$data['share_text'] = "";
		if(!empty($share_text_fn) && function_exists($share_text_fn) ){
			$data['share_text'] = $share_text_fn($post_array, $data['link']);
		}
		if(empty($data['share_text'])){
                    if(empty($data['share_image'])){
                            $data['share_text'] = $this->getShareText($post_array, $data, $this->options[pw_mobile_app_settings::$sharePreference]);
                    }else{
                            $data['share_text'] = $this->getShareText($post_array, $data, $this->options[pw_mobile_app_settings::$shareTextWithImage]);
                    }

                    if(!empty($this->options[pw_mobile_app_settings::$shareSuffixText])){
                            $data['share_text'] .= "\n".$this->options[pw_mobile_app_settings::$shareSuffixText];
                    }

                    if($this->options[pw_mobile_app_settings::$shareSuffixLink] == "HOME"){
                            $data['share_text']  .= " ".get_bloginfo('url');
                    }
                    else if($this->options[pw_mobile_app_settings::$shareSuffixLink] == "POST"){
                            $data['share_text']  .= " ". $data['link'];
                    }	
		}else{
                    if($this->options[pw_mobile_app_settings::$shareImageWithCustomFunction] == "1"){
                            $data['share_image'] = null;
                    }
		}

		if($this->options[pw_mobile_app_settings::$showCommentsCount] == "1" && $this->options[pw_mobile_app_settings::$commentsProvider] == "wordpress"){
			$comments_count = wp_count_comments( $post_array['ID']);
			$data['androapp_comments_count'] = $comments_count->approved;
		}
		$data['supercache'] = "androappsupercache";
		$post = $orig_post;
                wp_reset_postdata();
		$wp_query->is_home = $prev_ishome;
		return $data;
	}
	
	function myplugin_api_init( $server ) {
            
		add_filter( 'json_endpoints',       array( $this->pw_wp_menu, 'register_routes'), 0);
		add_filter( 'json_endpoints',       array( $this->pwapp_gcm, 'register_routes'), 0);
                add_filter( 'json_endpoints',       array( $this->pwapp_woo, 'register_routes'), 0);
                                
		$this->pwapp_posts = new Pw_Wp_Posts( $server );
		add_filter( 'json_endpoints',       array( $this->pwapp_posts, 'register_routes'), 0);
		
		$this->pwapp_woo_post = new Pw_Wp_Woo( $server );
		add_filter( 'json_endpoints',       array( $this->pwapp_woo_post, 'register_routes'), 0);
	}


	function get_seo_description($post_id){
		global $post;
		$ogdesc = "";
		if (class_exists('WPSEO_Meta')){
			$ogdesc  = WPSEO_Meta::get_value( 'opengraph-description', $post_id );

			// Replace Yoast SEO Variables.
			$ogdesc = wpseo_replace_vars( $ogdesc, $post );

			// Use metadesc if $ogdesc is empty.
			if ( $ogdesc === '' ) {
				$frontend = WPSEO_Frontend::get_instance();
				$ogdesc = $frontend->metadesc( false );
			}
		}
					

		// Tag og:description is still blank so grab it from get_the_excerpt().
		if ( ! is_string( $ogdesc ) || ( is_string( $ogdesc ) && $ogdesc === '' ) ) {
				$ogdesc = $this->my_excerpt($post);
		}
		return html_entity_decode($ogdesc,null,'UTF-8');
	}

 function my_excerpt($post) {
    if ($post->post_excerpt) {
        // excerpt set, return it
        return apply_filters('the_excerpt', $post->post_excerpt);

    } else {
       // setup_postdata( $post );
        $excerpt = get_the_excerpt();
        //wp_reset_postdata();
        return $excerpt;
    }
}

	function add_css_bottom() {
		if(isAndroAppRequest())
		{
			echo '<style type="text/css">';
			echo $this->options[pw_mobile_app_settings::$androAppCss];
			echo '</style>';
		}
	}

	function endswith($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ($testlen > $strlen) return false;
		$substr = substr($string, $strlen - $testlen-1, $testlen);
		return $substr == $test;
	}
	
	function removeIns($el){
		$count = 0;
		while($el != null){
			$count++;
			if($count > 10){
				return;	
			}
			if($el->nodeName == 'br' || $el->nodeName == 'ins'){
				$nextSibling = $el->nextSibling;
				$el->parentNode->removeChild($el);	
				$el = $nextSibling;
			}else if($el->nodeName == 'script'){
				return;	
			}else{
				$el = $el->nextSibling;	
			}
		}	
	}
	
	function stripAdsense($html){
		try{
			$html = "<html><body>".$html."</body></html>";
			$domDocument = new DOMDocument();
			// modify state
			$libxml_previous_state = libxml_use_internal_errors(true);
			// parse
			$domDocument->loadHTML('<?xml encoding="UTF-8">' . $html);
			// handle errors
			libxml_clear_errors();
			// restore
			libxml_use_internal_errors($libxml_previous_state);
			

			$domNodeList = $domDocument->getElementsByTagname('script'); 
			foreach ( $domNodeList as $domElement ) { 
			  $src = $domElement->getAttribute('src');
			  if (strpos($src, 'adsbygoogle.js') !== false) {
				$this->removeIns($domElement->nextSibling);
				$domElement->parentNode->removeChild($domElement);
			  }
			  
			  if (strpos($src, 'show_ads.js') !== false) {
			  	$this->removeIns($domElement->nextSibling);
				$domElement->parentNode->removeChild($domElement);
			  }
			} 
			$html =  $domDocument->saveHTML();

			$bodyPos = strpos($html, '<body>') ;
			if($bodyPos !== false){
				$html = substr($html, $bodyPos+6);
				$end = "</body></html>";
				if($this->endswith($html, $end)){
					$html = substr($html, 0, strlen($html) - strlen($end) -1);
				}
			}
		}
		catch(Exception $e) {
		  //echo 'Message: ' .$e->getMessage();
		}
		return $html;
	}
}