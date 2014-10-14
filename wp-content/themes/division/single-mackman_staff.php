<?php
/*
 * Staff Template Page
 */
?>

<?php get_header(); ?>

<?php $main_container_classes = bk_get_main_container_classes( get_the_ID() );?>

<div id="bk-fullscreen-background-wrap">

  <?php if ( have_posts() ) : // If we have posts ?>

			<?php while ( have_posts() ) : the_post(); // Start The Loop ?>
			
			  <?php // Set the details on the staff member
			    // Get the persons job title and picks
          $job_title = get_post_meta( $post->ID, 'mg_staff_job' );
          
          // Get the person wordpress id
          $wp_id = get_post_meta( $post->ID, 'mg_wp_user' );
          
          // Calculate the first name
          $names = preg_split( '/ /', get_the_title() );
          $first_name = $names[0];
          $last_name = $names[1];
          
        ?>
    	
		<div id="bk-content-inner-wrap" class="bk-fixed-page bk-float-left ">
			 <div id="bk-main-wrap" class="row-fluid">
			 	<div id="bk-content-wrap" class="span12">
			 	<style>#bk-content-inner-wrap{max-width:900px;background: white;}
        @media only screen and (max-width: 960px) {#bk-content-inner-wrap{margin:0px !important; max-width: none;}}
        @media only screen and (max-width: 1240px) {.bk-fixed-header-left #bk-content-inner-wrap{margin:0px !important; max-width: none;}}
        </style>
	 			 <article class="post-2459 page type-page status-publish hentry clearfix">
	 			   <h1 class="page-entry-title">
	 			     <?php the_title(); ?>
             <?php if ( !empty( $job_title ) ): ?>
              <span> - <?php echo $job_title[0]; ?></span>
             <?php endif; ?>
           </h1>
	 			   
	 			   <div class="bk-post-page-content-outer-wrap no-sidebar span12">
	 			     <div class="bk-page-content-wrap clearfix">
	 			       <div class="two_third">

	 			         <?php the_content(); ?>
	 			         
                </div><!-- two_third -->
              
                <div class="one_third last">
                  <?php if ( has_post_thumbnail() ): ?>
                    <?php the_post_thumbnail(); ?>
                  <?php endif; ?>
                </div>
                
                <div class="clear_both"></div>
                
                <?php if ($wp_id[0] != '-1'): ?>
                
                  <div class="one_half">
                    <div style="margin-bottom:30px" class="clearfix content-separator">
                      <span class="title">Recent posts by <?php echo $first_name; ?></span>
                    </div><!-- .content-separator -->
  
                    <?php
                    
                    // Build the query arguments
                    $args = array(
                                  'post_type'      => 'post',
                                  'author'         => $wp_id[0],
                                  'posts_per_page' => 3,
                                  'post_status'    => 'publish'
                                  );
                                  
                    // Run the query
                    $the_query = new WP_Query($args);
                    
                    // The Loop
                    if ( $the_query->have_posts() ): ?>
                      <ul>
                      <?php
                    	  while ( $the_query->have_posts() ) {
                    		  $the_query->the_post(); ?>
                      		<li class="">
                  		      <h3 class="post-entry-title">
                  		        <a rel="bookmark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  		      </h3>
                  		      <span class="bk-post-entry-meta">
                  		        Posted on <?php echo bk_get_post_publish_date($post_id); ?>
                  		      </span>
                          </li>
                        <?php } ?>
                      </ul>
                    <?php else: ?>
                    	<p>There are no posts.</p>
                    <?php endif; 
                    /* Restore original Post Data */
                    wp_reset_postdata();
                    ?>
                  </div><!-- .one_half -->
                  
                  <div class="one_half last">
                    <div style="margin-bottom:30px" class="clearfix content-separator">
                      <span class="title">Projects <?php echo $first_name; ?> has worked on</span>
                    </div>
                  
                    <?php
                    
                    // Build the aruments
                    $args = array(
                                  'post_type'      => 'portfolio', 
                                  'meta_key'       => 'mg_wp_user', 
                                  'meta_value'     => $wp_id[0], 
                                  'posts_per_page' => 3, 
                                  'post_status'    => 'publish'
                                  );
                    
                    // Run the query
                    $the_query = new WP_Query($args);
                    
                    // The Loop
                    if ( $the_query->have_posts() ): ?>
				  <style>
				    .staff_profile_cs {
				      list-style: none;
				    }
				    #staff_profile_ul {
				      margin-left: 0px!important;
				    }
				  </style>
                      <ul id="staff_profile_ul">
                      <?php
                    	  while ( $the_query->have_posts() ) {
                    		  $the_query->the_post(); ?>

                      		<li class="staff_profile_cs">
                  		      <h3 class="post-entry-title">
                  		        <a rel="bookmark" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  		      </h3>
				      <p><?php the_post_thumbnail(); ?></p>
				      <!--
                  		      <span class="bk-post-entry-meta">
                  		        Posted on <?php echo bk_get_post_publish_date($post_id); ?>
                  		      </span>#
				      -->
                          </li>
                        <?php } ?>
                      </ul>
                    <?php else: ?>
                    	<p>There are no posts.</p>
                    <?php endif; 
                    /* Restore original Post Data */
                    wp_reset_postdata();
                    ?>
                  </div><!-- .one_half -->
                  
                <?php endif; ?>
                
              </div><!-- bk-page-content-wrap -->
              
            </div><!-- bk-post-page-content-outer-wrap -->
            
          </article>
          
          <?php endwhile; // End The Loop ?>

        <?php else : // Else if we do not have any posts ?>

          <p>There are no posts</p>

        <?php endif; // End the check for posts ?>
        </div><!-- .bk-content-wrap -->
			 </div><!-- END #bk-main-wrap -->
			 
			 <div class="mg-footer">
			  <div class="clearfix content-separator"></div>
			  <p>© 2013 Mackman Ltd. Registered in England. Number: 04987877. Registered office: Meadow House, 1 Meadow Lane, Sudbury, Suffolk. CO10 2TD</p>
      </div><!-- .mg_footer -->
		</div><!-- END #bk-content-inner-wrap-->
		
  </div><!-- END #bk-fullscreen-background-wrap-->
	
<?php get_footer(); ?>