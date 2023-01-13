<?php
/**
 * Title: Offset Hero - Image Left Text Right
 * Slug: mackman/hero-offset-hero-left
 * Categories: hero
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0px"},"padding":{"top":"var:preset|spacing|x-large","right":"30px","bottom":"var:preset|spacing|x-large","left":"30px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0px;padding-top:var(--wp--preset--spacing--x-large);padding-right:30px;padding-bottom:var(--wp--preset--spacing--x-large);padding-left:30px">
    
    <!-- wp:media-text {"mediaId":277,"mediaLink":"http://mackman-group-block-test-site.test/wp-content/themes/mackman/assets/images/holding.jpg","mediaType":"image"} -->
        <div class="wp-block-media-text alignwide is-stacked-on-mobile">
          <figure class="wp-block-media-text__media">
            <img src="<?php echo esc_url( get_theme_file_uri() ) . '/assets/images/holding.jpg'; ?>" alt="" class="wp-image-277 size-full"/>
          </figure>
          <div class="wp-block-media-text__content">
            
            <!-- wp:group {"textColor":"neutral","className":"pull-left","layout":{"type":"constrained"}} -->
              <div class="wp-block-group pull-left has-neutral-color has-text-color">
                
                <!-- wp:heading {"level":2,"textColor":"neutral"} -->
                  <h2 class="has-neutral-color has-text-color">Heading Here</h2>
                <!-- /wp:heading -->
            
                <!-- wp:paragraph {"placeholder":"Content…"} -->
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut etiam sit amet nisl purus in mollis. Tristique senectus et netus et malesuada fames.</p>
                <!-- /wp:paragraph -->
                  
              </div>
            <!-- /wp:group -->
              
          </div>
        </div>
      <!-- /wp:media-text -->
      
  </div>
<!-- /wp:group -->
  

