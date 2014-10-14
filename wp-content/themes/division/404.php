<?php
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
?>

<?php get_header(); 
  $bk_404_page_id = get_option("bk_404_page_id");
?>
 <?php $main_container_classes = bk_get_main_container_classes( $bk_404_page_id );?>
  <div id="bk-fullscreen-background-wrap">
	<div id="bk-content-inner-wrap" class="<?php echo $main_container_classes["bk-content-inner-wrap"];?>">
		 <div id="bk-main-wrap" class="row-fluid">
		 	<div id="bk-content-wrap" class="<?php echo $main_container_classes["bk-content-wrap"];?>">
 			 <?php 
 			 echo  '<article class="' . implode(" ", get_post_class("", $bk_404_page_id)) . '">';
 			  
 			 
 			 if( $bk_404_page_id != -1 && !get_post_meta( $bk_404_page_id, '_bk_page_title_disabled', true ) ) {
	 			 echo  '<h1 class="page-entry-title">';
	 			 echo  get_the_title($bk_404_page_id);
	 			 echo  '</h1>';
 			 } else {
 			 	 echo  '<h1 class="page-entry-title">';
 			 	 echo  __("404 - Not Found.", "corpora_theme");
 			 	 echo  '</h1>';
 			 }
 			 
 			 $content_wrap_classes = bk_get_post_page_content_wrap_classes();
 			 
 			 echo  '<div class="bk-post-page-content-outer-wrap ' . $content_wrap_classes . '">';
 			 echo  '<div class="bk-page-content-wrap clearfix">';
 			   
			 if( $bk_404_page_id != -1 ) { 
				 echo bk_get_the_content( $bk_404_page_id );
			 } else {
 				echo '<h2>' . __("We're very sorry but the page You're trying to find isn't here.", "corpora_theme") . '</h2>';
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