<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class Pw_Wp_Posts {
	/**
	 * Server object
	 *
	 * @var WP_JSON_ResponseHandler
	 */
	protected $server;

	/**
	 * Constructor
	 *
	 * @param WP_JSON_ResponseHandler $server Server object
	 */
	public function __construct($server ) {
            $this->server = $server;
            if(!class_exists('WP_JSON_Posts')){
                add_action( 'rest_api_init', function () {
                register_rest_route( 'androapp/v2', '/posts/slug', array(
                        'methods' => 'GET',
                        'callback' => array($this, 'get_post_v2'),
                ) );
                
                register_rest_route( 'androapp/v2', '/posts', array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_posts_by_category_slug_v2'),
                    'show_in_rest' => true
                ));
             });
            }
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
			'/posts/slug' => array(
				array( array( $this, 'get_post' ), WP_JSON_Server::READABLE ),
			),
			'/androappposts' => array(
				array( array( $this, 'get_posts_by_category_slug_v1' ), WP_JSON_Server::READABLE ),
			),
		);
		return array_merge( $routes, $user_routes );
	}

	
	function get_ID_by_slug($page_slug) {
		return  url_to_postid($page_slug);
	}

        public function get_posts_by_category_slug_v2($request ){
            if(empty($request['page'])){
                $request['page'] = 1;
            }
            $data = $this->get_posts_by_category_slug_v1($request['filter'], $request['page'], 'view');
            return rest_ensure_response( $data );
        } 
        public function get_posts_by_category_slug_v1(  $filter, $page = 1, $context = 'view') {

            if(is_array($filter)){
                if(array_key_exists('category_name', $filter)){
                    $cat = get_category_by_slug( $filter['category_name']);
                    if($cat){
                        $posts = get_posts( 
                        array( 
                            'post_type' => 'post',
                            'posts_per_page' => 10, 
                            'category' => $cat->cat_ID,
                            'paged' => $page
                        )
                     );
                    }
                }else if (array_key_exists('tag', $filter)){

                    $posts = get_posts( 
                        array( 
                            'post_type' => 'post',
                            'posts_per_page' => 10, 
                            'tag' => $filter['tag'],
                            'paged' => $page
                        )
                     );
                }else if(array_key_exists('author_name', $filter)){
                    $user = get_user_by('slug', $filter['author_name']);

                     $posts = get_posts( 
                        array( 
                            'post_type' => 'post',
                            'posts_per_page' => 10, 
                            'author' => $user->ID,
                            'paged' => $page
                        )
                     );
                }
                else if(array_key_exists('taxonomy_slug', $filter)){
                    $taxonomy_name = "";
                    if(array_key_exists('taxonomy_name', $filter)){
                        $taxonomy_name =  $filter['taxonomy_name'];
                    }

                    if(!empty($taxonomy_name)){
                        $term = get_term_by('slug', $filter['taxonomy_slug'], $taxonomy_name);
                        #return rest_ensure_response( $term );
                    }
                    
                    if(empty($term))
                    {
                        $taxonomies = get_taxonomies();
                        foreach ( $taxonomies as $tax_type_key => $taxonomy ) {
                            // If term object is returned, break out of loop. (Returns false if there's no object)
                            if ( $term = get_term_by( 'slug', $term_slug , $taxonomy ) ) {
                                break;
                            }
                        }
                    }
                     $posts = get_posts( 
                        array( 
                            'post_type' => '',
                            'posts_per_page' => 10, 
                             'tax_query' => array(
                                array(
                                'taxonomy' => $term->taxonomy,
                                'field' => 'term_id',
                                'terms' => $term->term_id
                                 )
                              ),
                            'paged' => $page
                        )
                     );
                }
            }
            else{
                return array();
            }
            
            
             if ( empty( $posts ) ) {
                 return array();
             }

            if(class_exists('WP_REST_Posts_Controller'))
            {
                $controller = new WP_REST_Posts_Controller('post');

                foreach ( $posts as $post ) {
                    $response = $controller->prepare_item_for_response( $post, $request );
                    $data[] = $controller->prepare_response_for_collection( $response );
                }
                return $data;
            }
            
            return $posts;
        }

        
    public function get_post_v2($data){
        return $this->get_post($data['slug']);
    }
	/*
	reference: https://github.com/mcnasby/wp-json-api-menu-controller/blob/master/api-controllers/menus.php
	*/
  public function get_post( $slug, $context = 'view' ) {
    //return "Test".$slug. " ".$this->get_ID_by_slug($slug) ." ". get_category_by_path($slug,false)->cat_ID;
	$postId = $this->get_ID_by_slug($slug);
	if(!empty($postId)){
            if(class_exists('WP_JSON_Posts') && $this->server != null){
                $wp_server_posts = new WP_JSON_Posts( $this->server );
                return $wp_server_posts->get_post($postId, $context);
            }else if(class_exists('WP_REST_Posts_Controller')){
                $postdata = get_post( $postId); 
                if($postdata){
                    $wp_server_posts = new WP_REST_Posts_Controller($postdata->post_type);
                    $request = array();
                    $request['id'] = $postId;
                    return $wp_server_posts->get_item($request);    
                }
                
            }	
	}
	$category = get_category_by_path($slug,false);
	if($category){
		$menu = array(
							"id"=>"-1",
							"parent_id"=>"-1",
							"menu_order"=>"-1",
							"name"=>$category->name,
							"object_type"=>"category",
							"object_id"=>$category->cat_ID,
							"link"=> json_url("/taxonomies/category/terms/".$category->cat_ID)
						);
		return $menu;	
	}
	//list($slug, $remaining) = split('/', $slug, 2); 
	$pieces = explode('/', $slug, 2);
        $slug = $pieces[0];
        $remaining = $pieces[1];
	$tag = get_term_by('slug', $remaining, 'post_tag');
	if($tag){
		$menu = array(
							"id"=>"-1",
							"parent_id"=>"-1",
							"menu_order"=>"-1",
							"name"=>$tag->name,
							"object_type"=>"tag",
							"object_id"=>$tag->term_id,
							"link"=> json_url("/taxonomies/post_tag/terms/".$tag->term_id)
						);
		return $menu;	
	}

	return "no page found";
  }
}
?>