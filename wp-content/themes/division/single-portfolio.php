<?php bk_set_view_portfolio_counter( get_the_ID() ); ?>
<?php get_header(); ?>
<?php $main_container_classes = bk_get_main_container_classes( get_the_ID() );?>
<div id="bk-fullscreen-background-wrap">
  <?php 
	if( post_password_required( get_the_ID() ) ) {
		get_template_part( 'password-protected' );
	} else if( get_post_meta( get_the_ID(), '_bk_page_layout', true ) != 'no_content') : ?>
	<div id="bk-content-inner-wrap" class="<?php echo $main_container_classes["bk-content-inner-wrap"];?>">
		<div id="bk-main-wrap" class="row-fluid">
			<div id="bk-content-wrap" class="<?php echo $main_container_classes["bk-content-wrap"];?>">
				<?php bk_get_portfolio_entry( get_the_ID() ); ?>
			</div>
		</div>
		<!-- END #bk-main-wrap -->
	</div>
	<!-- END #bk-content-inner-wrap-->
  <?php endif; ?>
</div>
<!-- END #bk-fullscreen-background-wrap-->
<?php get_footer(); ?>