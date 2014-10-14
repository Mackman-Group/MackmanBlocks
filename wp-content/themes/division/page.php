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
	 			 <?php 
	 			 echo  '<article class="' . implode(" ", get_post_class("clearfix")) . '">';
	 			   
	 			 if( !get_post_meta( get_the_ID(), '_bk_page_title_disabled', true ) ) {
		 			 echo  '<h1 class="page-entry-title">';
		 			 echo  get_the_title(get_the_ID());
		 			 echo  '</h1>';
	 			 }
	 			 
	 			 $content_wrap_classes = bk_get_post_page_content_wrap_classes();
	 			 
	 			 echo  '<div class="bk-post-page-content-outer-wrap ' . $content_wrap_classes . '">';
	 			 echo  '<div class="bk-page-content-wrap clearfix">';
	 			   
	 			 echo bk_get_the_content( get_the_ID() );
	 			   
	 			 echo  '</div>';
	 			 
	 			 comments_template( '', true );
	 			 
	 			 echo  '</div>';
	 			   
	 			 get_sidebar();
	 			   
	 			 echo  '</article>';
	 			 ?>
	 			</div>
			 </div><!-- END #bk-main-wrap -->
			<div class="mg-footer">
			  <div class="clearfix content-separator"></div>
			  <?php echo do_shortcode('[smartblock id=8235]') ?>
      </div><!-- .mg_footer -->
		</div><!-- END #bk-content-inner-wrap-->
	<?php endif; ?>	
  </div><!-- END #bk-fullscreen-background-wrap-->	
<?php get_footer(); ?>