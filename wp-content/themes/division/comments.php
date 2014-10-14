<?php
/**
 * The actual display of comments is
 * handled by a callback to bk_comments() which is
 * located in the functions.php file.
 *
 */
?>
<?php if( (comments_open() || have_comments()) && post_type_supports( get_post_type(), 'comments' ) ) :  ?>
	
	<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php __( 'This post is password protected. Enter the password to view any comments.', 'corpora_theme' ); ?></p>
	</div><!-- #comments -->
	<?php
			/* Stop the rest of comments.php from being processed,
			 * but don't kill the script entirely -- we still have
			 * to fully load the template.
			 */
			return;
		endif;
	?>

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
	<div class="bk-comments-wrap">
		<h4 class="section-start comments-section">
			<?php printf( _n( 'One comment on "%2$s"', '%1$s comments on "%2$s"', get_comments_number(), 'corpora_theme' ), number_format_i18n( get_comments_number() ), bk_get_custom_text(get_the_title()) ); ?>
		</h4>
		<ol class="bk_comment_list">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use bk_comments() to format the comments.
				 */
				wp_list_comments( array( 'callback' => 'bk_comments' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below">
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'corpora_theme' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'corpora_theme' ) ); ?></div>
		</nav>
		<div class="clear_both"></div>
		<?php endif; // check for comment navigation ?>
	</div>
	<?php endif; ?> 
  <?php if( comments_open() ) : ?>
	  <div class="bk-respond-wrap">
			<?php comment_form( array('title_reply'=> sprintf('<h4 class="section-start comments-section">%1$s</h4>', __( 'Leave a Reply', 'corpora_theme' ) ), 
																'title_reply_to'=> sprintf('<h4 class="section-start comments-section">%1$s</h4>', __( 'Leave a Reply', 'corpora_theme' ) ),
																'cancel_reply_link'=> sprintf('<span class="bk-cancel-reply-link">%1$s</span>', __( 'Cancel', 'corpora_theme' )) ) ); ?>
	  </div>
  <?php endif; ?> 
</div><!-- #comments -->
<?php endif; ?> 