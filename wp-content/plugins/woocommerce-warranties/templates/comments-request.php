<?php
if ( post_password_required() )
	return;
?>

<div id="comments" class="woocommerce">

	<?php if ( have_comments() ) : ?>
		<h3 class="war_title">
			<?php
				printf( _n( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'wcwar' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h3>

		<ol class="war_commentlist">
			<?php wp_list_comments( array( 'callback' => 'wcwar_public_comment', 'style' => 'ol' ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav id="war_nav_bellow" class="navigation" role="navigation">
			<h3 class="war_title"><?php esc_html_e( 'Comment navigation', 'wcwar' ); ?></h3>
			<div class="previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'wcwar' ) ); ?></div>
			<div class="next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'wcwar' ) ); ?></div>
		</nav>
		<?php endif;?>

		<?php
			if ( ! comments_open() && get_comments_number() ) :
		?>
		<p class="war_nocomments"><?php esc_html_e( 'Comments for this request are closed.' , 'wcwar' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php comment_form(); ?>

</div>
<script>
	var btn = document.getElementById('submit');
	btn.className += ' button';
</script>