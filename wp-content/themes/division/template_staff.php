<?php
/*
 * Template Name: Staff Template
 *
 * The staff template
 * 
 * @package MackmanGroup
 * @author Adam Chamberlin
 * @since 1.0
 * 
 */

?>

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
	 			   
	 			 echo bk_get_the_content( get_the_ID() ); ?>
	 			 
	 			 <div style="margin-bottom:30px" class="clearfix content-separator"><span class="title">The people</span></div>
	 			 <style>
	 			 
	 			   li.staff-member {
  	 			   float: left;
  	 			   list-style: none;
  	 			   margin: 1em;
  	 			   padding: 1em 0;
  	 			   width: 30%;
  	 			   border-bottom: 1px solid #ebebeb;
	 			   }
	 			   
	 			   i.staff-member .no-image {
  	 			   height: 100px;
  	 			   width: 150px;
	 			   }
	 			   
	 			   li.staff-member h2 {
  	 			   margin: 0;
	 			   }
	 			   
	 			   li.staff-member h4 {
  	 			   min-height: 44px;
	 			   }
	 			 </style>
	 			 
	 			 <?php $loop = new WP_Query( array( 'post_type' => 'mackman_staff', 'posts_per_page' => 50, 'orderby' => 'title', 'order' => 'ASC' ) ); ?>
          <?php if ( $loop->have_posts() ): ?>
            <ul>
            <?php while ( $loop->have_posts() ): ?>
              <?php $loop->the_post(); ?>
              <li class="staff-member">
                <?php
                  $job_title = get_post_meta( $post->ID, 'mg_staff_job' );
                ?>
                <?php if ( has_post_thumbnail() ): ?>
                  <a href="<?php the_permalink(); ?>" title="Read more" class="">
                    <?php the_post_thumbnail(); ?>
                  </a>
                <?php else: ?>
                  <p class="no-image">No Image</p>
                <?php endif; ?>
                <h2><?php echo $post->post_title; ?></h2>
                <?php if ( !empty( $job_title ) ): ?>
                  <h4>
                    <?php echo "{$job_title[0]} "; ?>
                  </h4>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="">View Profile</a>
              </li><!-- .staff-member -->
            <?php endwhile; ?>
            </ul>
          <?php else: ?>
            <p>We do not have any members of staff on file just yet.</p>
          <?php endif; ?>
          <?php wp_reset_postdata(); ?>
	 			 
	 			 <?php
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