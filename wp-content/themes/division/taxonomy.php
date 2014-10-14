<?php get_header(); ?>
 <?php $main_container_classes = bk_get_main_container_classes( get_the_ID() );?>
  <div id="bk-fullscreen-background-wrap">
	<div id="bk-content-inner-wrap" class="<?php echo $main_container_classes["bk-content-inner-wrap"];?>">
		 <div id="bk-main-wrap" class="row-fluid">
		 	<div id="bk-content-wrap" class="<?php echo $main_container_classes["bk-content-wrap"];?>">
 			 <?php 
 			 echo  '<article class="' . implode(" ", get_post_class()) . '">'; 
 			 
 			 if( ( is_tax('post_format') && get_option( 'bk_blog_archives_show_page_title' ) == true ) ||
 			 	 ( ( is_tax('filter') || is_tax('portfolio_skills') )  && get_option( 'bk_portfolio_archives_show_page_title' ) == true )
 			 	) {
	 			 echo  '<h1 class="page-entry-title">';
	 			 echo  bk_print_regular_featured_caption();
	 			 echo  '</h1>';
 			 }
 			 
 			 $content_wrap_classes = bk_get_post_page_content_wrap_classes();
 			 
 			 echo  '<div class="bk-post-page-content-outer-wrap ' . $content_wrap_classes . '">';
 			 echo  '<div class="bk-page-content-wrap clearfix">';
 			   
 			 if( is_tax('filter') || is_tax('portfolio_skills') ) {

				global $bk_global_unique_portfolio_id;
				$bk_global_unique_portfolio_id++;
				
				$port_navigation = 'pagination'; // hardcoded 
				$filter_mode = 'fade_and_shuffle'; // this will be not used
 			 	
				$layout = get_option('bk_portfolio_archives_layout');
 			 	$base_size = get_option('bk_portfolio_archives_base_size'); 
 			 	$description = get_option('bk_portfolio_archives_description');
 			 	$image_effect = get_option('bk_portfolio_archives_image_effect');
 			 	$image_overlay = get_option('bk_portfolio_archives_image_overlay');
 			   	$gutter = get_option('bk_portfolio_archives_gutter');
				
				$portfolio_id = 'bk-portfolio-wrap-' . $bk_global_unique_portfolio_id;
				
				if( $gutter > 0 ) {
					echo '<style type="text/css">';
					echo '#' . $portfolio_id . ' li.portfolio-item { margin-bottom: ' . $gutter . 'px; }';
					echo '#' . $portfolio_id . ' ul.filterable-grid { margin-top: ' . $gutter . 'px; }';
					echo '#' . $portfolio_id . ' div.bk-load-more-wrap { margin-bottom: ' . $gutter . 'px; }';
					echo '#' . $portfolio_id . ' div.bk-pagination-wrap { margin-bottom: ' . $gutter . 'px; }';
					echo '#' . $portfolio_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
					echo '</style>';
				}
				
				echo '<div class="bk-portfolio-wrap ' . $port_navigation . '" data-portfolio_filter_mode="' . $filter_mode . '" data-portfolio_image_effect="' . $image_effect . '" data-portfolio_image_overlay="' . $image_overlay . '" data-portfolio_layout="' . $layout . '" data-portfolio_description="' . $description . '" id="' . $portfolio_id . '" data-portfolio_base_size="' . $base_size . '" data-portfolio_gutter="' . $gutter . '">';
				
				echo '<ul class="filterable-grid clearfix ' . $layout . '">';
				
				global $paged;
				if( !isset($paged) ) {
					$paged = get_query_var('paged');
				}
				
				$loaded_items_count = 0;
				$total_items_matched = 0;
				
				$show_cats = -1;
				
				echo bk_get_portfolio_items( array(), $layout, $base_size, $show_cats, $description, $image_effect, $image_overlay, $loaded_items_count, $total_items_matched );
				
				echo  '</ul>';
				
				echo bk_pagination('', 2, false, 'portfolio');
				
				echo '</div>';
			 } else if( is_tax('post_format') ) {
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
					
				echo '<div class="bk-blog-outer-wrap ' . $blog_navigation . '" id="' . $blog_id . '"  data-blog_base_size="' . $base_size . '" data-blog_gutter="' . $gutter . '" >';
				echo '<ul class="bk-blog-wrap">';
					
				$loaded_items_count = 0;
				$total_items_matched = 0;
				
				$show_cats = -1;
					
				echo bk_get_blog_items( array(), $show_cats, $loaded_items_count, $total_items_matched );
					
				echo '</ul>';
				echo '<div class="clearfix"></div>';
				echo bk_pagination('', 2, false, 'portfolio');
					
				echo  '</div>';
			 }	
 			   
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
