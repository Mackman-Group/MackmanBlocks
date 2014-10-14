<?php get_header(); ?>
 <?php $main_container_classes = bk_get_main_container_classes( get_the_ID() );?>
  <div id="bk-fullscreen-background-wrap">
	<div id="bk-content-inner-wrap" class="<?php echo $main_container_classes["bk-content-inner-wrap"];?>">
		 <div id="bk-main-wrap" class="row-fluid">
		 	<div id="bk-content-wrap" class="<?php echo $main_container_classes["bk-content-wrap"];?>">
 			 <?php 
 			 echo  '<article class="' . implode(" ", get_post_class()) . '">'; 
 			 
 			 if( get_option( 'bk_blog_archives_show_page_title' ) == true ) {
	 			 echo  '<h1 class="page-entry-title">';
	 			 echo  bk_print_regular_featured_caption();
	 			 echo  '</h1>';
 			 }
 			 
 			 $content_wrap_classes = bk_get_post_page_content_wrap_classes();
 			 
 			 echo  '<div class="bk-post-page-content-outer-wrap ' . $content_wrap_classes . '">';
 			 echo  '<div class="bk-page-content-wrap clearfix">';
 			   
 			 global $bk_global_unique_blog_id;
 			 $bk_global_unique_blog_id++;
 			 
 			 $blog_navigation = 'pagination'; // hardcoded
 			 $base_size = get_option('bk_blog_archives_base_size');
 			 $gutter = get_option('bk_blog_archives_gutter');
 			 
 			 $blog_id = 'bk-blog-outer-wrap-' . $bk_global_unique_blog_id;
 			 
 			 if( $gutter > 0 ) {
 			 	echo '<style type="text/css">';
 			 	echo '#' . $blog_id . ' li.bk-blog-entry-wrap { margin-bottom: ' . $gutter . 'px; }';
 			 	echo '#' . $blog_id . ' ul.bk-blog-wrap { margin-top: ' . $gutter . 'px; }';
 			 	echo '#' . $blog_id . ' div.bk-load-more-wrap { margin-bottom: ' . $gutter . 'px; }';
 			 	echo '#' . $blog_id . ' div.bk-pagination-wrap { margin-bottom: ' . $gutter . 'px; }';
 			 	echo '#' . $blog_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
 			 	echo '</style>';
 			 }
 			 
 			 echo '<div class="bk-blog-outer-wrap ' . $blog_navigation . ' ' . $base_size . '" id="' . $blog_id . '"  data-blog_base_size="' . $base_size . '" data-blog_gutter="' . $gutter . '" >';
 			 echo '<ul class="bk-blog-wrap">';
 			 
 			 $loaded_items_count = 0;
 			 $total_items_matched = 0;
 			 
 			 $args = array();
 			 $show_cats = -1;
 			 
 			 echo bk_get_blog_items( $args, $show_cats, $loaded_items_count, $total_items_matched );
 			 
 			 echo '</ul>';
 			 echo '<div class="clearfix"></div>';
 			 echo bk_pagination('', 2, false, 'portfolio');
 			 
 			 echo  '</div>';	
 			 echo  '</div>';
 			 echo  '</div>';
 			   
 			 get_sidebar();
 			   
 			 echo  '</article>';
 			 ?>
 			</div>
		 </div><!-- END #bk-main-wrap --> 
	</div><!-- END #bk-content-inner-wrap-->
  </div><!-- END #bk-fullscreen-background-wrap-->	
<?php get_footer(); ?>
