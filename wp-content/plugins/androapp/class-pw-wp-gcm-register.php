<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class Pw_Wp_Gcm_Register {

	public function __construct( ) {
            if(!class_exists('WP_JSON_Posts')){
                add_action( 'rest_api_init', function () {
                    register_rest_route( 'androapp/v2', '/gcm/register/(?P<gcmid>.+)', array(
                            'methods' => 'GET',
                            'callback' => array($this, 'register_v2'),
                    ) );
                } );
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
			'/gcm/register/(?P<gcmid>.+)' => array(
				array( $this, 'register' ),         WP_JSON_Server::READABLE
			),
		);
		return array_merge( $routes, $user_routes );
	}

        public function register_v2($data){
            return $this->pwapp_adduser($data['gcmid'], $data['topics'], $data['devicetype']);
        }

        public function register( $gcmid, $topics = null, $devicetype = null, $context = 'view' ) {
            return $this->pwapp_adduser($gcmid, $topics, $devicetype);
        }
  
  function pwapp_adduser($gcmid, $topics, $devicetype) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'pw_gcmusers';
	
	$sql = "SELECT COUNT(*) FROM $table_name where gcm_regid=%s" ;
	
	$sql = $wpdb->prepare($sql, $gcmid);
	
	
	$user_count = $wpdb->get_var($sql);
	
	if($user_count > 0){
		return $wpdb->update( 
			$table_name, 
			array( 
				'created_at' => current_time( 'mysql' ), 
				'gcm_regid' => sanitize_text_field($gcmid), 
				'status' => "1", 
				'topics' => sanitize_text_field($topics),
                                'device' => sanitize_text_field($devicetype)
			),
			array(
				'gcm_regid' => sanitize_text_field($gcmid), 
			)
		);	
	}else{
		return $wpdb->insert( 
			$table_name, 
			array( 
				'created_at' => current_time( 'mysql' ), 
				'gcm_regid' => sanitize_text_field($gcmid), 
				'status' => "1", 
				'topics' => sanitize_text_field($topics),
                                'device' => sanitize_text_field($devicetype)
			) 
		);	
	}
	
  }
}
?>