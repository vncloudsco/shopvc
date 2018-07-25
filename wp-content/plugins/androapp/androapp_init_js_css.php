<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
function androapp_back_enqueue_script() {
	if(isset($_GET['page']))
	{
		if($_GET['page'] == 'pw_mobile_app_options')
		{
			wp_enqueue_script("jquery-ui-core");	
			wp_enqueue_script("jquery-ui-accordion");
                        
			wp_register_script('AndroAppJs', plugins_url('js/androapp2.js',  __FILE__));
			wp_enqueue_script("AndroAppJs");
			
			/* Register our stylesheet. */
			wp_register_style( 'androAppStyleSheet', plugins_url('css/androapp.css?ver=434', __FILE__) );
			wp_enqueue_style("androAppStyleSheet");
		}
	}
}


add_action( 'admin_enqueue_scripts', 'androapp_back_enqueue_script' );
?>