<?php
/*----------------------------------------------------------------------------------
This file builds front page slider according to the theme settings
----------------------------------------------------------------------------------*/

$slides_category = get_option("homepage_slides_category");
$slider_type = get_option('homepage_slider_type');

global $post;
$page_slider_enabled = get_post_meta($post->ID, '_bk_featured_slider_enabled', true);
if( $page_slider_enabled == '' ) {  // handle when theres no such key
   	$page_slider_enabled = false;
}

if( $page_slider_enabled ) {
	  $page_slider_type = get_post_meta($post->ID, '_bk_featured_slider_type', true);
	  $page_slides_category = get_post_meta($post->ID, '_bk_featured_slides_category', true);
	  
	  if( $page_slider_type != '' )
	  		$slider_type = $page_slider_type;
	  		
	  if( $page_slides_category != '' )
	  		$slides_category = $page_slides_category;		
}

if( 'nivo' == $slider_type ) : 
?>

<?php bk_get_responsive_nivo( $slides_category, $slider_type );	?>
	
<?php 
elseif( 'piecemaker' == $slider_type ) : 
?>
	
	<?php if( !is_ipad() && !is_mobile() ) : ?>
	
		<div id="piecemaker-wrap">
			<div id="piecemaker"></div>
		</div>
		
		<div class="responsive-nivo-fallback">
			<?php bk_get_responsive_nivo( $slides_category, $slider_type );	?>
		</div>
	
	<?php else: ?>
	
		<?php bk_get_responsive_nivo( $slides_category, $slider_type );	?>
	
	<?php endif; ?>
		
<?php
elseif( 'diamond' == $slider_type ) : 
?>
   <div class='bk-diamondslider'>
			<ul>
			
			<?php
			$count = -1; // all images
			 
			  $args= array(
				'posts_per_page' => $count,
				'post_type' => 'slide',
				'orderby' => 'menu_order', 
				'order' => 'ASC',
				'taxonomy' => 'slide_filter'
				);

				if( $slides_category != -1 ) {
					$args['term'] = $slides_category;
				}
				
				global $post;
				$captions = array();
			  
			  query_posts($args);
				if ( have_posts() ) : while ( have_posts() ) : the_post();
				
					$post_id = $post->ID;
				
				  if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
				  	
				  	$slide_image_size = "featured-slider";
				  	
				  	global $bk_featured_slider_sizing_mode;
				  
				  	if( $bk_featured_slider_sizing_mode == 'fit-featured-area' )
				  			$slide_image_size = 'featured-slider-boxed';
				  	
				  	$thumb_image_size = "featured-slider-thumb";
				  	
				  	if( get_option("bk_diamond_ken_burns_for_slides") == true ) {
				  		  $slide_image_size = "featured-slider-ken-burns";
				  	}
				  	
				  	if( get_option("bk_diamond_ken_burns_for_thumbs") == true ) {
				  		$thumb_image_size = "featured-slider-thumb-ken-burns";
				  	}
				  	
				  	$caption = '';
				  	$content = apply_filters('the_content',get_the_content());
						if( $content ) {
							  $caption_id = "caption_" . $post_id; 
							  $caption = 'data-title="#' . $caption_id . '"';
							  
							  $caption_markup = '';
							  $caption_markup .= $content;	
							  
							  $captions[$caption_id] = $caption_markup;
							  
							  $caption_width = get_post_meta($post_id, '_bk_slide_caption_width', true);
								if( -1 == $caption_width ) {
									$caption_width = "auto";
								} else {
									$caption_width .= "%";
								}	
								
								$caption_height = get_post_meta($post_id, '_bk_slide_caption_height', true);
								if( -1 == $caption_height ) {
									$caption_height = "auto";
								} else {
									$caption_height .= "%";
								}
								
								$caption_position = explode(" ", get_post_meta($post_id, '_bk_slide_caption_position', true));
								$caption_hor_offset = get_post_meta($post_id, '_bk_slide_caption_hor_offset', true) . 'px';
								$caption_ver_offset = get_post_meta($post_id, '_bk_slide_caption_ver_offset', true) . 'px';
								
								$caption_x = $caption_position[0] . $caption_hor_offset;
								$caption_y = $caption_position[1] . $caption_ver_offset;
								
								$caption_show_effect = get_post_meta($post_id, '_bk_slide_caption_show_effect', true);
								$caption_show_easing = get_post_meta($post_id, '_bk_slide_caption_show_easing', true);
								$caption_show_duration = get_post_meta($post_id, '_bk_slide_caption_show_duration', true);
								$caption_show_direction = get_post_meta($post_id, '_bk_slide_caption_show_direction', true);
								
								$caption .= 'data-caption_style="width:' . $caption_width . ';min-height:' . $caption_height . ';' . $caption_x . ';' . $caption_y . ';"';
								$caption .= ' data-caption_show_effect="' . $caption_show_effect . '" data-caption_show_easing="' . $caption_show_easing . '" data-caption_show_duration="' . $caption_show_duration . '" data-caption_show_direction="' . $caption_show_direction . '"' ;
						}
				  	
				  	$image =  wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $slide_image_size, false, '' );
						$image_link = $image[0];
						
						$thumb =  wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $thumb_image_size, false, '' );
						$thumb_link = $thumb[0];
						
						$def_transition = '';
						$slide_transition_easing = '';		
						
						$transition_type = get_post_meta($post_id, '_bk_slide_diamond_transition', true);
						if( isset( $transition_type ) && 'random' != $transition_type )
								$def_transition = 'data-def_transition="' . $transition_type . '"';
								
						$transition_easing = get_post_meta($post_id, '_bk_slide_diamond_transition_easing', true);
						if( isset( $transition_easing ) && 'def' != $transition_easing )
								$slide_transition_easing = 'data-easing="' . $transition_easing . '"';
								
						echo '<li data-thumb="' . $thumb_link . '" ' . $def_transition . ' ' . $slide_transition_easing  . ' ' . $caption .'>';
						
						$slide_link = trim(get_post_meta($post_id, '_bk_slide_link', true));
						if( isset($slide_link) && !empty($slide_link) ) {
							 if( preg_match ("/\b(?:vimeo|youtube)\.com\b/i", $slide_link) || preg_match("/\b\.(swf|jpg|jpeg|png|bmp|gif)\b/i", $slide_link) ) {
							 	echo '<a rel="prettyPhoto[' . $post_id . ']" href="' . $slide_link . '" title="' . $title . '">';
							 } elseif( parse_url($slide_link) ) {
							 	$target = get_post_meta($post_id, '_bk_slide_link_target', true);
							 	echo '<a target="' . $target . '" rel="bookmark" href="' . $slide_link . '" title="' . $title . '">';
							 }		
						}							
						
						echo '<img class="slide_img bk_ken_burns_img" src="' . $image_link . '" />';
						
						if( isset($slide_link) && !empty($slide_link) ) {
				  		echo '</a>';
				  	}
				  	
				  	echo '</li>';	
			    }
			    
			  endwhile;
				endif;
				wp_reset_query();  
			?>  	
			</ul>
	 </div>
	 
	 <?php
	  foreach ($captions as $id => $content) {
    	echo '<div id="' . $id . '" class="bk-diamond-html-caption">' . $content . '</div>';
		}
	 ?>
	 
	 <div class="responsive-nivo-fallback">
		<?php bk_get_responsive_nivo( $slides_category, $slider_type );	?>
	 </div>

<?php
elseif( 'content' == $slider_type ) : 
?>

	 <div id="content-slider-wrap">
	  <div id="content-slider">
	  
	  <?php
	  $count = -1; // all images
			 
			  $args = array(
				'posts_per_page' => $count,
				'post_type' => 'slide',
				'orderby' => 'menu_order', 
				'order' => 'ASC',
				'taxonomy' => 'slide_filter'
				);
			
				if( $slides_category != -1 ) {
					$args['term'] = $slides_category;
				}
				
				global $post;
				$captions = array();
			  
			  $count = 0;
			  $init_style = '';
			  
			  query_posts($args);
				if ( have_posts() ) : while ( have_posts() ) : the_post();
				
					$post_id = $post->ID;
				  if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
				  	
				  }
				  
				$content = apply_filters('the_content',get_the_content());
				$margin = get_post_meta($post_id, '_bk_slide_content_bottom_margin', true);
				
				$flying_captions_enabled = 0;
				if( get_post_meta($post_id, '_bk_flying_captions_enabled', true) == true ) {
					  $flying_captions_enabled = 1;
				}	
				
				$caption_show_effect = get_post_meta($post_id, '_bk_content_slide_caption_show_effect', true);
				$caption_show_easing = get_post_meta($post_id, '_bk_content_slide_caption_show_easing', true);
				$caption_show_duration = get_post_meta($post_id, '_bk_content_slide_caption_show_duration', true);
				$caption_show_direction = get_post_meta($post_id, '_bk_content_slide_caption_show_direction', true);
				$caption_chain_delay = get_post_meta($post_id, '_bk_content_slide_caption_chain_delay', true);
				
				$child_selector = '';
				
				if( get_post_meta($post_id, '_bk_content_slide_animate_paragraphs', true) == true )
						$child_selector .= 'p,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_headers_1', true) == true )
						$child_selector .= 'h1,';		
				if( get_post_meta($post_id, '_bk_content_slide_animate_headers_2', true) == true )
						$child_selector .= 'h2,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_headers_3', true) == true )
						$child_selector .= 'h3,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_headers_4', true) == true )
						$child_selector .= 'h4,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_headers_5', true) == true )
						$child_selector .= 'h5,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_headers_6', true) == true )
						$child_selector .= 'h6,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_lists', true) == true )
						$child_selector .= 'ul,ol,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_list_items', true) == true )
						$child_selector .= 'ul > li,ol > li,';
				if( get_post_meta($post_id, '_bk_content_slide_animate_images', true) == true )
						$child_selector .= 'img';
						
				$layer_1_background_image = get_post_meta($post_id, '_bk_content_slide_layer_1_background_image', true);
				if( $layer_1_background_image != '' ) {
					  $background_position = get_post_meta($post_id, '_bk_content_slide_layer_1_background_image_position', true);
					  $style = 'background: url(' . $layer_1_background_image . ') no-repeat ' . $background_position . ';';
					  
					  $layer_show_effect = get_post_meta($post_id, '_bk_content_slide_layer_1_show_effect', true);
					  $layer_show_easing = get_post_meta($post_id, '_bk_content_slide_layer_1_show_easing', true);
					  $layer_show_direction = get_post_meta($post_id, '_bk_content_slide_layer_1_show_direction', true);
					  
					  $layer_effect = 'data-layer_show_effect="' . $layer_show_effect . '" data-layer_show_easing="' . $layer_show_easing . '" data-layer_show_direction="' . $layer_show_direction . '"';
					  
					  $content .= '<div ' . $layer_effect . ' class="bk-content-slide-layer bk-content-slide-layer-1" style="' . $style . '"></div>';
					  if( get_post_meta($post_id, '_bk_content_slide_animate_layer_1', true) == true ) {
					  		$child_selector .= ',div.bk-content-slide-layer-1';	
					  }	
				}
				
				$layer_2_background_image = get_post_meta($post_id, '_bk_content_slide_layer_2_background_image', true);
				if( $layer_2_background_image != '' ) {
					  $background_position = get_post_meta($post_id, '_bk_content_slide_layer_2_background_image_position', true);
					  $style = 'background: url(' . $layer_2_background_image . ') no-repeat ' . $background_position . ';';
					  
					  $layer_show_effect = get_post_meta($post_id, '_bk_content_slide_layer_2_show_effect', true);
					  $layer_show_easing = get_post_meta($post_id, '_bk_content_slide_layer_2_show_easing', true);
					  $layer_show_direction = get_post_meta($post_id, '_bk_content_slide_layer_2_show_direction', true);
					  
					  $layer_effect = 'data-layer_show_effect="' . $layer_show_effect . '" data-layer_show_easing="' . $layer_show_easing . '" data-layer_show_direction="' . $layer_show_direction . '"';
					  
					  $content .= '<div ' . $layer_effect . ' class="bk-content-slide-layer bk-content-slide-layer-2" style="' . $style . '"></div>';
					  if( get_post_meta($post_id, '_bk_content_slide_animate_layer_2', true) == true ) {
					  		$child_selector .= ',div.bk-content-slide-layer-2';	
					  }
				}
				
				$layer_3_background_image = get_post_meta($post_id, '_bk_content_slide_layer_3_background_image', true);
				if( $layer_3_background_image != '' ) {
					  $background_position = get_post_meta($post_id, '_bk_content_slide_layer_3_background_image_position', true);
					  $style = 'background: url(' . $layer_3_background_image . ') no-repeat ' . $background_position . ';';
					  
					  $layer_show_effect = get_post_meta($post_id, '_bk_content_slide_layer_3_show_effect', true);
					  $layer_show_easing = get_post_meta($post_id, '_bk_content_slide_layer_3_show_easing', true);
					  $layer_show_direction = get_post_meta($post_id, '_bk_content_slide_layer_3_show_direction', true);
					  
					  $layer_effect = 'data-layer_show_effect="' . $layer_show_effect . '" data-layer_show_easing="' . $layer_show_easing . '" data-layer_show_direction="' . $layer_show_direction . '"';
					  
					  $content .= '<div ' . $layer_effect . ' class="bk-content-slide-layer bk-content-slide-layer-3" style="' . $style . '"></div>';
					  if( get_post_meta($post_id, '_bk_content_slide_animate_layer_3', true) == true ) {
					  		$child_selector .= ',div.bk-content-slide-layer-3';	
					  }
				}
				
				$layer_4_background_image = get_post_meta($post_id, '_bk_content_slide_layer_4_background_image', true);
				if( $layer_4_background_image != '' ) {
					  $background_position = get_post_meta($post_id, '_bk_content_slide_layer_4_background_image_position', true);
					  $style = 'background: url(' . $layer_4_background_image . ') no-repeat ' . $background_position . ';';
					  
					  $layer_show_effect = get_post_meta($post_id, '_bk_content_slide_layer_4_show_effect', true);
					  $layer_show_easing = get_post_meta($post_id, '_bk_content_slide_layer_4_show_easing', true);
					  $layer_show_direction = get_post_meta($post_id, '_bk_content_slide_layer_4_show_direction', true);
					  
					  $layer_effect = 'data-layer_show_effect="' . $layer_show_effect . '" data-layer_show_easing="' . $layer_show_easing . '" data-layer_show_direction="' . $layer_show_direction . '"';
					  
					  $content .= '<div ' . $layer_effect . ' class="bk-content-slide-layer bk-content-slide-layer-4" style="' . $style . '"></div>';
					  if( get_post_meta($post_id, '_bk_content_slide_animate_layer_4', true) == true ) {
					  		$child_selector .= ',div.bk-content-slide-layer-4';	
					  }
				}
				
				$layer_5_background_image = get_post_meta($post_id, '_bk_content_slide_layer_5_background_image', true);
				if( $layer_5_background_image != '' ) {
					  $background_position = get_post_meta($post_id, '_bk_content_slide_layer_5_background_image_position', true);
					  $style = 'background: url(' . $layer_5_background_image . ') no-repeat ' . $background_position . ';';
					  
					  $layer_show_effect = get_post_meta($post_id, '_bk_content_slide_layer_5_show_effect', true);
					  $layer_show_easing = get_post_meta($post_id, '_bk_content_slide_layer_5_show_easing', true);
					  $layer_show_direction = get_post_meta($post_id, '_bk_content_slide_layer_5_show_direction', true);
					  
					  $layer_effect = 'data-layer_show_effect="' . $layer_show_effect . '" data-layer_show_easing="' . $layer_show_easing . '" data-layer_show_direction="' . $layer_show_direction . '"';
					  
					  $content .= '<div ' . $layer_effect . ' class="bk-content-slide-layer bk-content-slide-layer-5" style="' . $style . '"></div>';
					  if( get_post_meta($post_id, '_bk_content_slide_animate_layer_5', true) == true ) {
					  		$child_selector .= ',div.bk-content-slide-layer-5';	
					  }
				}
				
				$slide_height = '';
				$class = '';
				
				if( get_post_meta($post_id, '_bk_content_slide_force_height', true) == true ) {
					  	$height =	get_post_meta($post_id, '_bk_content_slide_forced_height', true);
					  	$slide_height = 'data-slide_height="'. $height .'"';
					  	$class = 'height_100_percent';	
				}	

				echo '<div ' . $style . ' ' . $slide_height  . ' data-margin_bottom="' . $margin . '" data-captions_enabled="' . $flying_captions_enabled .
																																	  '" data-caption_show_effect="' . $caption_show_effect .  
																																	  '" data-caption_show_easing="' . $caption_show_easing . 
																																	  '" data-caption_show_duration="' . $caption_show_duration . 
																																	  '" data-caption_show_direction="' . $caption_show_direction .
																																	  '" data-caption_chain_delay="' . $caption_chain_delay . 
																																	  '" data-child_selector="' . $child_selector .  
																																	  '" class="slide bk_slide_' . $count . ' ' . $class . ' container_16">' . $content . '</div>';
				
				if( $count == 0 && $child_selector != '' ) {
					$init_style = '<style rel="stylesheet" type="text/css">';
					$children = explode( ',', $child_selector );
					for( $i = 0; $i < count($children); $i++ ) {
							 $init_style .= '#content-slider div.bk_slide_0 ' . $children[$i] . ' {display: none;} ';
				  }
					$init_style .= '</style>';
					
					$init_style .= '<noscript>';
					$init_style .= '<style rel="stylesheet" type="text/css">';
					$children = explode( ',', $child_selector );
					for( $i = 0; $i < count($children); $i++ ) {
						   if( (false !== strpos($children[$i], 'div.bk-content-slide-layer-')) || (false !== strpos($children[$i], 'h')) || (false !== strpos($children[$i], 'img')) ||  
						   		 $children[$i] == 'ul' || $children[$i] == 'ol' ) { 
							 		$init_style .= '#content-slider div.bk_slide_0 ' . $children[$i] . ' {display: block;} ';
							 } else if( $children[$i] == 'p' ) {
							 	  $init_style .= '#content-slider div.bk_slide_0 ' . $children[$i] . ' {display: inline;} ';
							 } else if( ($children[$i] == 'ul > li') || ($children[$i] == 'ol > li') ) {
							 	  $init_style .= '#content-slider div.bk_slide_0 ' . $children[$i] . ' {display: list-item;} ';
							 }				
				  }
					$init_style .= '</style>';
					$init_style .= '</noscript>';
				}	
				
				$count++;  	
				
				endwhile;
				endif;
				wp_reset_query();  	
		?>				
	  
	  </div>
	  
	  <?php if( $count > 1 ) :?>
		  <a class="content-prev"></a>
		  <a class="content-next"></a>
	  <?php endif; ?>
	 </div>
	 
	 <?php
	  echo $init_style;
	 ?>	

<?php
elseif( 'static' == $slider_type ) : 
?>

	<?php
	
	$static_slide_post_id = get_option('bk_static_slider_post');
	if( $page_slider_enabled ) {
		  $static_slide_post_id = get_post_meta($post->ID, '_bk_featured_static_slider_post', true);
	}	
	 
	if( $static_slide_post_id != -1 ) :
			$margin = get_post_meta($static_slide_post_id, '_bk_slide_content_bottom_margin', true);
	?>
	
	<div id="content-slider-wrap">
		<div id="static-content" style="margin-bottom: <?php echo $margin; ?>px;">
			<?php
					$args = array(
					'post_type' => 'slide',
					'p' => $static_slide_post_id
					);
					
					query_posts($args);
					if ( have_posts() ) : the_post();

						echo apply_filters('the_content',get_the_content());
					
					endif;
					wp_reset_query();
			?>
		</div>
	</div>	
	
	<?php endif; ?>
	 				
<?php 
endif; 
?>