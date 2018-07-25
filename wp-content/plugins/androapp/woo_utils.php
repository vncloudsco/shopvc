<?php	
if ( ! defined( 'ABSPATH' ) ) exit; 
	global $woocommerce;
    //$woocommerce->api->includes();
	
	if(isset($woocommerce) && WC()->payment_gateways()){

		$gateways = WC()->payment_gateways->payment_gateways();
		if(is_array($gateways) && sizeof($gateways) > 0){
		?>
		
			<table class="form-table">				
				<tr valign="top"><th scope="row"><?php _e('Payment Methods Settings','androapp');?>:</th>
					<td>
						<b>
						<?php _e('Configure payment methods for your mobile app, you can enable/disable selected payment methods for your app, also choose if you want to redirect to mobile native browser for payment processing.</br></b>(few payment options might now work well in webview(in your app), it is recommended to test and set the correct option)',
						'androapp');?>
						<br/>
						<?php _e('Note:- payment methods enabled here, also needs to be enabled in general','androapp');?>
						
						<table class="form-table">
						<th scope="row"><?php _e('Payment Method','androapp');?></th>
						<th scope="row"><?php _e('Enable','androapp');?></th>
						<th scope="row"><?php _e('Open in Native Browser','androapp');?></th>
						<?php
							foreach($gateways as $key => $gateway ){
								?>
								<tr>
									<td>
										<?php echo $gateway->title;?>
									</td>
									<td>
										<?php
										$enablekey = 'pay'.$gateway->id;
										?>
										<input type="radio"  name="<?php echo $this->option_name."[".$enablekey."]"?>" value="1" <?php if($options[$enablekey] == '1') echo "checked"; ?> />
										<?php _e('Enable','androapp');?>
						<input type="radio" name="<?php echo $this->option_name."[".$enablekey."]"?>" value="0" <?php if($options[$enablekey] == '0') echo "checked"; ?> />
						<?php _e('Disable','androapp');?>
									</td>
									<td>
										<?php
										$webviewkey = 'payweb'.$gateway->id;
										?>
										<input type="radio"  name="<?php echo $this->option_name."[".$webviewkey."]"?>" value="1" <?php if($options[$webviewkey] == '1') echo "checked"; ?> />
										<?php _e('Yes','androapp');?>
						<input type="radio" name="<?php echo $this->option_name."[".$webviewkey."]"?>" value="0" <?php if($options[$webviewkey] == '0') echo "checked"; ?> />
						<?php _e('No','androapp');?>
									</td>
								</tr>
								<?php
							}
							
						?>
						</table>
					
				</tr>
			</table>		
		<?php
		}
	}
?>
		<table class="form-table">				
			<tr valign="top"><th scope="row"><?php _e('Cart Icon','androapp');?>:</th>
				<td>
				<b><?php _e('Select only if you want to show cart icon in your app (applicable only for woocommerce stores, others please keep it unchecked',
				'androapp');?></b></br>
				<input type="checkbox" name="<?php echo $this->option_name."[".pw_mobile_app_settings::$showCartIcon."]"?>" value="1" <?php if($options[pw_mobile_app_settings::$showCartIcon] == '1') echo "checked"; ?> /><b>
				<?php _e('Show Cart Icon','androapp');?></b>
				</td>
			</tr>
		</table>