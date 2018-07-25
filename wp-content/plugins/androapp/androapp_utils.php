<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

// PHP_VERSION_ID is available as of PHP 5.2.7, if our 
// version is lower than that, then emulate it
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

	// Check if post has atleast one image
	function androapp_get_first_image($post_content) {
	  $first_img = '';
	  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
	  $first_img = $matches [1][0];
	  return $first_img;
	}
	
	function convertToFullUrl($src){
            if(strpos($src, "http", 0) != 0 || strpos($src, "/", 0) == 0)
            {
                    return get_bloginfo('wpurl').$src;
            }
            return $src;
	}
        
        function androapp_get_all_images($post_content){
            //echo "Content ".$post_content;
            if(PHP_VERSION_ID > 50207){
                $images = androapp_get_all_images_using_dom_parser($post_content);
                if($images === FALSE){
                    return androapp_get_all_images_using_regex($post_content);
                }else{
                    return $images;
                }
            }else{
                return androapp_get_all_images_using_regex($post_content);
            }
        }
        
        function androapp_get_all_images_using_regex($post_content){
            preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
            $arr =  $matches[1];
            $images = array();
            foreach ($arr as $im){
                $image = array();
                $image['src'] = $im;
                $image['width'] = 100;
                $images[] = $image;
            }
            return $images; 
        }
        
        function androapp_get_all_images_using_dom_parser($post_content){
            require_once 'simple_html_dom.php';
            $html = str_get_html($post_content);
            if($html != FALSE)
            {
                $images = array();
                $imagearr = $html->find('img');
                if($imagearr && is_array($imagearr)){
                    foreach($imagearr as $element) 
                    {
                        $image = array();
                        $sibling = $element->next_sibling();
                        if($sibling  && $sibling->class == 'wp-caption-text')
                        {
                            $image['description'] = $sibling->innertext;
                        }else {
                                $imgparent = $element->parent();
                                if($imgparent && $imgparent->tag == 'a'){
                                        $sibling = $imgparent->next_sibling();
                                        if($sibling  && $sibling->class == 'wp-caption-text')
                                        {                                                   
                                                $image['description'] = $sibling->plaintext;
                                        }
                                }       
                        }
                        $image['src'] =  $element->src ;
                        $image['height'] = 200;
                        $images[] = $image;
                    }
                }
                return $images;
            }
            return FALSE;
        }
        

?>