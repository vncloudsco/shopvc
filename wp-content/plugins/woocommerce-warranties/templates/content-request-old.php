<?php
	get_header();
	
	woocommerce_output_content_wrapper();

	$curr_guests = get_option( 'wcwar_enable_guest_requests', 'no' );

	if ( is_user_logged_in() ) {

	?>
		<div class="wcwar_warranty">
	<?php

		$current_user = wp_get_current_user();

		$curr_order_id = get_post_meta( get_the_ID(), '_wcwar_warranty_order_id', true);

		$curr_user = ( $curr_user = get_post_meta( $curr_order_id, '_customer_user', true ) ) ? absint( $curr_user ) : '';

		if ( current_user_can( 'manage_options' ) || $curr_user == $current_user->ID ) {
		?>
			<h1 class="wcwar_title"><?php the_title(); ?></h1>
		<?php
			while ( have_posts() ) {
				the_post();

				global $post;

				$curr_item = esc_attr( get_post_meta( $post->ID, '_wcwar_warranty_product_id', true ) );
				$curr_parent_order = $post->ID;

			?>

				<p class="wcwar_warranty_status">
					<span class="wcwar_badge_parent"><i class="wcwar-icon"></i> <?php esc_html_e('Request Type', 'wcwar'); ?> : 
					<?php
						$curr_terms = get_the_terms( $curr_parent_order, 'wcwar_warranty' );
						$curr_terms = reset( $curr_terms );
						if ( !empty ($curr_terms) ) {
							$return = get_post_meta( $curr_parent_order, '_wcwar_warranty_return_request', true );
							if ( $return !== '' && $return == 'return' ) {
								echo '<span class="wcwar_badge warranty_only">' . esc_html__('Return', 'wcwar') . '</span>';
							}
							else {
								echo '<span class="wcwar_badge return_only">' . esc_html__('Warranty', 'wcwar') . '</span>';
							}
							echo esc_html__( 'Status', 'wcwar' ) . ' : ';
							$switch_slug = $curr_terms->slug;
							if ( $switch_slug == 'new' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_new">' . esc_html__('New', 'wcwar') . '</span>';
							}
							else if ( $switch_slug == 'processing' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_processing">' . esc_html__('Processing', 'wcwar') . '</span>';
							}
							else if ( $switch_slug == 'completed' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_completed">' . esc_html__('Completed', 'wcwar') . '</span>';
							}
							else if ( $switch_slug == 'rejected' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_rejected">' . esc_html__('Rejected', 'wcwar') . '</span>';
							}
							echo esc_html__( 'Requested on', 'wcwar' ) . ' : ';
							echo get_the_date();
						}
					?>
					</span>
				</p>
				<div class="war_request">
			<?php
				the_content();
			?>
				</div>
			<?php
				comments_template();
			}
		}
		else {
			esc_html_e('You should not be here.', 'wcwar');
		}
	?>
		</div>
	<?php
	}
	else if ( $curr_guests == 'yes' && isset( $_POST['email'] ) && isset( $_POST['order_id'] ) && isset( $_POST['war_guest'] ) ) {
		$chck_order = wc_get_order($_POST['order_id']);
		if ( isset( $chck_order ) && !empty( $chck_order ) && $chck_order->billing_email == $_POST['email'] ) {
			$guest_request = true;
		}
	?>
		<div class="wcwar_warranty">
			<h1 class="wcwar_title"><?php the_title(); ?></h1>
		<?php
			while ( have_posts() ) {
				the_post();

				global $post;

				$curr_item = esc_attr( get_post_meta( $post->ID, '_wcwar_warranty_product_id', true ) );

				$curr_parent_order = $post->ID;

			?>

				<p class="wcwar_warranty_status">
					<span class="wcwar_badge_parent"><i class="wcwar-icon"></i> <?php esc_html_e('Request Type', 'wcwar'); ?> : 
					<?php
						$curr_terms = get_the_terms( $curr_parent_order, 'wcwar_warranty' );
						$curr_terms = reset( $curr_terms );
						if ( !empty ($curr_terms) ) {
							$return = get_post_meta( $curr_parent_order, '_wcwar_warranty_return_request', true );
							if ( $return !== '' && $return == 'return' ) {
								echo '<span class="wcwar_badge warranty_only">' . esc_html__('Return', 'wcwar') . '</span>';
							}
							else {
								echo '<span class="wcwar_badge return_only">' . esc_html__('Warranty', 'wcwar') . '</span>';
							}
							echo esc_html__( 'Status', 'wcwar' ) . ' : ';
							$switch_slug = $curr_terms->slug;
							if ( $switch_slug == 'new' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_new">' . esc_html__('New', 'wcwar') . '</span>';
							}
							else if ( $switch_slug == 'processing' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_processing">' . esc_html__('Processing', 'wcwar') . '</span>';
							}
							else if ( $switch_slug == 'completed' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_completed">' . esc_html__('Completed', 'wcwar') . '</span>';
							}
							else if ( $switch_slug == 'rejected' ) {
								echo '<span class="wcwar_badge wcwar_change warranty_rejected">' . esc_html__('Rejected', 'wcwar') . '</span>';
							}
							echo esc_html__( 'Requested on', 'wcwar' ) . ' : ';
							echo get_the_date();
						}
					?>
					</span>
				</p>
				<div class="war_request">
			<?php
				the_content();
			?>
				</div>
			<?php
				comments_template();
			}
?>
	</div>
<?php
	}
	else if ( $curr_guests == 'yes' ) {
?>
	<div class="wcwar_warranty woocommerce">
		<form action="<?php the_permalink(); ?>" method="POST">
		<p><?php esc_html_e( 'Warranty requests for users that are not logged in are allowed. Please fill in the form bellow to view your warranty request.', 'wcwar' ); ?></p>
		<p>
			<label for="wcwar_guest_email"><strong><?php esc_html_e( 'Enter your E-Mail address', 'wcwar' ); ?><span class="wcwar_required"><?php esc_html_e( 'Required', 'wcwar' ); ?></span></strong>
			<input id="wcwar_guest_email" name="email" type="text"/></label>
		</p>
		<p>
			<label for="wcwar_guest_order_id"><strong><?php esc_html_e( 'Enter your order ID', 'wcwar' ); ?><span class="wcwar_required"><?php esc_html_e( 'Required', 'wcwar' ); ?></span></strong>
			<input id="wcwar_guest_order_id" name="order_id" type="text"/></label>
			<small><em><?php esc_html_e( '* Please fill in all required fields to continue.', 'wcwar' ); ?></em></small>
		</p>
		<p>
			<input name="war_guest" type="hidden" value="true" />
			<input value="<?php esc_html_e( 'Continue', 'wcwar' ); ?>" type="submit" class="button" />
		</p>
		</form>
	</div>
<?php
	}
	else {
	?>
		<div class="wcwar_warranty">
			<p class="wcwar_form_error">
				<strong><?php esc_html_e( 'Guest warranties are not allowed!', 'wcwar' ); ?></strong>
				<?php esc_html_e( 'Warranty requests for users that are not logged in are not allowed. Please login to continue.', 'wcwar' ); ?>
			</p>
		</div>
	<?php
	}

	woocommerce_output_content_wrapper_end();

	get_footer();

?>