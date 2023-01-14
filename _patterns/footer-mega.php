<?php
/**
 * Title: Footer with text, button, links.
 * Slug: mackman/footer-mega
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport Width: 1280
 */
?>
<!-- wp:group {"align":"full","style":{"typography":{"fontSize":"18px"},"spacing":{"margin":{"top":"0px"},"padding":{"top":"var:preset|spacing|x-large","bottom":"var:preset|spacing|x-large"}}},"layout":{"type":"constrained"}} -->
  <div class="wp-block-group alignfull" style="margin-top:0px;padding-top:var(--wp--preset--spacing--x-large);padding-bottom:var(--wp--preset--spacing--x-large);font-size:18px">
    
    <!-- wp:columns {"align":"wide","style":{"elements":{"link":{"color":[]}}}} -->
      <div class="wp-block-columns alignwide has-link-color">
        
        <!-- wp:column {"width":"55%"} -->
          <div class="wp-block-column" style="flex-basis:55%">
            
            <!-- wp:heading {"level":4} -->
              <h4 id="our-company"><?php echo esc_html__( 'Our Company', 'mackman' ); ?></h4>
            <!-- /wp:heading -->
  
            <!-- wp:paragraph -->
              <p>Quisque aliquam nisl quis metus taylor feugiat. Lorem ipsum dolor sit amet, consectetur adipiscing vestibulum vitae gravida non diam.</p>
            <!-- /wp:paragraph -->
              
            <!-- wp:buttons -->
              <div class="wp-block-buttons">
                
                <!-- wp:button {"style":{"border":{"radius":0}},"className":"is-style-fill"} -->
                  <div class="wp-block-button is-style-fill">
                    <a class="wp-block-button__link no-border-radius wp-element-button" href="#"><?php echo esc_html__( 'Learn More', 'mackman' ); ?></a>
                  </div>
                <!-- /wp:button -->
                  
              </div>
            <!-- /wp:buttons -->
              
          </div>
        <!-- /wp:column -->
          
        <!-- wp:column {"width":"15%"} -->
          <div class="wp-block-column" style="flex-basis:15%">
            
            <!-- wp:heading {"level":4} -->
              <h4 id="about-us"><?php echo esc_html__( 'About Us', 'mackman' ); ?></h4>
            <!-- /wp:heading -->
    
            <!-- wp:list {"fontSize":"small"} -->
              <ul class="has-small-font-size">
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Start Here', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Our Mission', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Brand Guide', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Newsletter', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Accessibility', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                  
              </ul>  
            <!-- /wp:list -->
                
          </div>
        <!-- /wp:column -->
        
        <!-- wp:column {"width":"15%"} -->
          <div class="wp-block-column" style="flex-basis:15%">
            
            <!-- wp:heading {"level":4} -->
              <h4 id="services"><?php echo esc_html__( 'Services', 'mackman' ); ?></h4>
            <!-- /wp:heading -->
            
            <!-- wp:list {"fontSize":"small"} -->
              <ul class="has-small-font-size">
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Web Design', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Development', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Copywriting', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Marketing', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#"><?php echo esc_html__( 'Social Media', 'mackman' ); ?></a></li>
                <!-- /wp:list-item -->
                  
              </ul>
            <!-- /wp:list -->
                
          </div>
        <!-- /wp:column -->
        
        <!-- wp:column {"width":"15%"} -->
          <div class="wp-block-column" style="flex-basis:15%">
            <!-- wp:heading {"level":4} -->
              <h4 id="connect"><?php echo esc_html__( 'Connect', 'mackman' ); ?></h4>
            <!-- /wp:heading -->
            
            <!-- wp:list {"fontSize":"small"} -->
              <ul class="has-small-font-size">
                
                <!-- wp:list-item -->
                  <li><a href="#">Facebook</a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#">Instagram</a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#">Twitter</a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#">LinkedIn</a></li>
                <!-- /wp:list-item -->
                
                <!-- wp:list-item -->
                  <li><a href="#">Dribbble</a></li>
                <!-- /wp:list-item -->
                  
              </ul>
            <!-- /wp:list -->
              
          </div>
        <!-- /wp:column -->
          
      </div>
    <!-- /wp:columns -->
    
  </div>
<!-- /wp:group -->