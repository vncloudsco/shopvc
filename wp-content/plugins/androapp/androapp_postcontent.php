<?php
    if ( ! defined( 'ABSPATH' ) ) exit; 

	$postContentOptions = get_option($this->post_content_tab_key);
	$headerScript = $postContentOptions[pw_mobile_app_settings::$headerScript];
	$beforePostContent = $postContentOptions[pw_mobile_app_settings::$beforePostContent];
	$afterPostContent = $postContentOptions[pw_mobile_app_settings::$afterPostContent];
?>	
<h3>Here you can add any html code/message to be shown in post page of your app, it will only be visible in the app</h3>
<b>Add your html code in the textboxes below, here you can add any xss, javascript or message to your app users.</b>

<div style="color:red;font-size:small;">Note: this functionality is available only to the paid users.</div>
</br>
	Header(before title):</br>
	<textarea form="pwappsettingsform" rows="13" cols="60" 
	name="<?php echo $this->post_content_tab_key."[".pw_mobile_app_settings::$headerScript."]"?>" ><?php echo $headerScript;?></textarea>	
	<div style="color:red;font-size:small;">header will be added from App version 6.0.2 onwards</div>
</br>
	Before Post Content(after title):</br>
	<textarea form="pwappsettingsform" rows="13" cols="60" 
	name="<?php echo $this->post_content_tab_key."[".pw_mobile_app_settings::$beforePostContent."]"?>" ><?php echo $beforePostContent;?></textarea>	
	
</br>
	After Post Content:</br>
	<textarea form="pwappsettingsform" rows="13" cols="60" 
	name="<?php echo $this->post_content_tab_key."[".pw_mobile_app_settings::$afterPostContent."]"?>" ><?php echo $afterPostContent;?></textarea>	

		<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>