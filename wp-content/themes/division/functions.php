<?php
/*----------------------------------------------------------------------------------
 All functions are placed in this file
----------------------------------------------------------------------------------*/
if ( ! isset( $content_width ) ) $content_width = 940;
/*----------------------------------------------------------------------------------
 Load theme translation
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_theme_setup' ) ) {
	function bk_theme_setup(){
		load_theme_textdomain('corpora_theme', get_template_directory() . '/languages');

		$locale = get_locale();
		$locale_file = get_template_directory() . "/languages/$locale.php";
		if ( is_readable($locale_file) )
			require_once($locale_file);
	}
}
add_action('after_setup_theme', 'bk_theme_setup');

/*----------------------------------------------------------------------------------
 * Hook in on activation
----------------------------------------------------------------------------------*/
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
	add_action( 'init', 'bk_woocommerce_image_dimensions', 1 );
}

/*----------------------------------------------------------------------------------
 * Define image sizes
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_woocommerce_image_dimensions' ) ) {
	function bk_woocommerce_image_dimensions() {
		$catalog = array(
				'width' 	=> '480',	// px
				'height'	=> '594',	// px
				'crop'		=> 1 		// true
		);

		$single = array(
				'width' 	=> '960',	// px
				'height'	=> '1188',	// px
				'crop'		=> 1 		// true
		);

		$thumbnail = array(
				'width' 	=> '150',	// px
				'height'	=> '185',	// px
				'crop'		=> 1 		// false
		);

		// Image sizes
		update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
		update_option( 'shop_single_image_size', $single ); 		// Single product image
		update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
	}
}


/*
 * Add webm video mime type to allowed uploaders
*
*/
add_filter('upload_mimes', 'bk_custom_upload_mimes');

if ( !function_exists( 'bk_custom_upload_mimes' ) ) {
	function bk_custom_upload_mimes( $existing_mimes=array() ) {
		$existing_mimes['webm'] = 'video/webm';
		return $existing_mimes;
	}
}

/*
 * Enable shortcodes in widgets - especially text widget
 */
add_filter('widget_text', 'do_shortcode');

/*----------------------------------------------------------------------------------
 register menu
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_register_menu' ) ) {
	function bk_register_menu() {
		register_nav_menu( 'primary-menu', __( 'Primary Menu', 'corpora_theme' ) );
		register_nav_menu( 'top-menu', 'Top Menu' );
		add_post_type_support( 'page', 'excerpt' );
		remove_post_type_support( 'page', 'comments' );	// remove this if comments on page are needed
		add_post_type_support( 'portfolio', 'excerpt' );
	}
}
add_action( 'init', 'bk_register_menu' );
/*----------------------------------------------------------------------------------
 register sidebars
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_register_sidebars' ) ) {
	function bk_register_sidebars() {
		if( function_exists('register_sidebar') ) {
			$sidebars = get_option("bk_sidebars_list");
			if( is_array( $sidebars ) ) {
				foreach( $sidebars as $sidebar ) {
					register_sidebar(array(
					'id' => $sidebar["bk-sidebar-id"],
					'name' => $sidebar["bk-sidebar-name"],
					'description' => __('This is one of Your custom sidebars. Add any widgets here and then assigns this sidebar to any post/page/portfolio entry in post/page/portfolio settings', 'corpora_theme'),
					'before_widget' => '<li id="%1$s" class="widget clearfix %2$s">',
					'after_widget'  => '</li>',
					'before_title' => '<h3 class="widget-title"><span class="widget-title-wrap">',
					'after_title' => '</span></h3>'
							));
				}
			}
		}
	}
}
add_action( 'widgets_init', 'bk_register_sidebars' );


/*
 * Add conditional IE styles and scripts to head
*
*/
add_action( 'wp_head', 'bk_print_conditional_ie_styles_and_scripts' );
/**
 *  Add IE conditional html5 shim to header
*/
if ( !function_exists( 'bk_print_conditional_ie_styles_and_scripts' ) ) {
	function bk_print_conditional_ie_styles_and_scripts() {
		global $is_IE;
		if ( $is_IE ) {
			echo '<!--[if lt IE 9]>';
			echo '<link href="' . get_template_directory_uri() . '/jackbox/css/jackbox-ie8.css" rel="stylesheet" type="text/css" />';
			echo '<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>';
			echo '<![endif]-->';

			echo '<!--[if gt IE 8]>';
			echo '<link href="' . get_template_directory_uri() . '/jackbox/css/jackbox-ie9.css" rel="stylesheet" type="text/css" />';
			echo '<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>';
			echo '<![endif]-->';
		}
	}
}
/*
 * this function prints out supersized slideshow
*
*/
if ( !function_exists( 'bk_print_supersized_slideshow' ) ) {
	function bk_print_supersized_slideshow() {
		if( is_page() || is_single() ) {  // check if it is single page, post, or portfolio
			global $post;

			if( get_post_meta( $post->ID, '_bk_page_fullscreen_background_content', true ) != 'bk-fullscreen-slideshow-enabled' || 
				post_password_required( $post->ID ) ) {
				return;
			}

			$gallery_items = Bk_Fullscreen_Gallery_Manager::getInstance()->get_gallery_items($post->ID,  '_bk_fullscreen_background_gallery', 'popup-gallery-thumb');

			/*
			 * enqueue scripts
		 *
		 */
			wp_enqueue_script('supersized_js');
			wp_enqueue_script('supersized_shutter_js');

			if( !empty($gallery_items) ) {
					
				$images = '';
					
				foreach( $gallery_items as $image ) {
					$title = '';

					if( !empty($image['image_title']) ) {
						$title .= '<h2 class=\"slide_caption_title\">' . htmlentities($image['image_title'] , ENT_QUOTES) . '</h2>';
					}

					if( !empty($image['image_description']) ) {
						$title .= '<p class=\"slide_caption_description\">' . htmlentities($image['image_description'], ENT_QUOTES) . '</p>';
					}

					$images .= '{image: "' . $image['image_url'] . '", thumb: "' . $image['thumb_url'] . '", url: "' . $image['image_link'] . '", title: "' . $title . '"},';
				}
					
				$images = trim($images, ",");
					
				$autoplay = get_post_meta( $post->ID, '_bk_fullscreen_gallery_autoplay', true ) == "on" ? 1 : 0;
				$pause_on_hover = get_post_meta( $post->ID, '_bk_fullscreen_gallery_pause_on_hover', true ) == "on" ? 1 : 0;
				$new_window = get_post_meta( $post->ID, '_bk_fullscreen_gallery_new_window', true ) == "on" ? 1 : 0;
				$fit_allways = get_post_meta( $post->ID, '_bk_fullscreen_gallery_fit_allways', true ) == "on" ? 1 : 0;
					
					
				$transition = get_post_meta( $post->ID, '_bk_fullscreen_gallery_transition', true );
				$transition_speed = get_post_meta( $post->ID, '_bk_fullscreen_gallery_transition_speed', true );
				$transition_interval = get_post_meta( $post->ID, '_bk_fullscreen_gallery_transition_interval', true );
					
				$overlay = get_post_meta( $post->ID, '_bk_fullscreen_gallery_overlay', true );
					
				if( !empty($overlay) ) {
					echo '<style type="text/css">';
					echo '#supersized-overlay {background: url("' . $overlay . '");}';
					echo '</style>';
				}
					
				?>

<script type="text/javascript">
			
			jQuery(document).ready(function($) {
			  $('#bk-content-outer-wrap').append('<div id="thumb-tray" class="load-item"><div id="thumb-back"></div><div id="thumb-forward"></div></div><ul id="supersized"></ul>');
			  $('#bk-fullscreen-background-wrap').append('<div id="supersized-overlay"></div><div id="supersized-loader"></div><a id="prevslide" class="load-item"></a><a id="nextslide" class="load-item"></a><div id="slidecaption"></div>');
			});

			jQuery(function($){
				$.supersized({
				
					// Functionality
					slideshow               :   1,			// Slideshow on/off
					autoplay				:	<?php echo $autoplay; ?>,			// Slideshow starts playing automatically
					start_slide             :   1,			// Start slide (0 is random)
					stop_loop				:	0,			// Pauses slideshow on last slide
					random					: 	0,			// Randomize slide order (Ignores start slide)
					slide_interval          :   <?php echo $transition_interval; ?>,		// Length between transitions
					transition              :   <?php echo $transition; ?>, 			// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
					transition_speed		:	<?php echo $transition_speed; ?>,		// Speed of transition
					new_window				:	<?php echo $new_window; ?>,			// Image links open in new window/tab
					pause_hover             :   <?php echo $pause_on_hover; ?>,			// Pause slideshow on hover
					keyboard_nav            :   1,			// Keyboard navigation on/off
					performance				:	1,			// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
					image_protect			:	1,			// Disables image dragging and right click with Javascript
															   
					// Size & Position						   
					min_width		        :   0,			// Min width allowed (in pixels)
					min_height		        :   400,			// Min height allowed (in pixels)
					vertical_center         :   1,			// Vertically center background
					horizontal_center       :   1,			// Horizontally center background
					fit_always				:	<?php echo $fit_allways; ?>,			// Image will never exceed browser width or height (Ignores min. dimensions)
					fit_portrait         	:   1,			// Portrait images will not exceed browser height
					fit_landscape			:   0,			// Landscape images will not exceed browser width
															   
					// Components							
					slide_links				:	'blank',	// Individual links for each slide (Options: false, 'num', 'name', 'blank')
					thumb_links				:	1,			// Individual thumb links for each slide
					thumbnail_navigation    :   0,			// Thumbnail navigation
					slides 					:  	[ <?php echo $images; ?> ],
												
					// Theme Options			   
					progress_bar			:	0,			// Timer for each slide							
					mouse_scrub				:	1
					
				});
		    });
		    
		</script>
<?php
			}
		}
	}
}
add_action( 'wp_footer', 'bk_print_supersized_slideshow' );

/*
 * This functions prints out background goolge map with custom pin locations
*/
if ( !function_exists( 'bk_print_background_google_map' ) ) {
	function bk_print_background_google_map() {
		if( is_page() || is_single() ) {  // check if it is single page, post, or portfolio
			global $post;

			if( get_post_meta( $post->ID, '_bk_page_fullscreen_background_content', true ) != 'bk-fullscreen-google-maps-enabled' ) {
				return;
			}

			wp_enqueue_script('google_maps');

			$marker =  get_post_meta( $post->ID, '_bk_fullscreen_google_map_marker', true );
			$location = get_post_meta( $post->ID, '_bk_fullscreen_google_map_location', true );
			$title = get_post_meta( $post->ID, '_bk_fullscreen_google_map_marker_title', true );

			echo '<div id="bk-background-google-map-canvas" data-marker="' . $marker . '" data-location="' . $location . '" data-title="' . $title . '"></div>';

		}
	}
}
add_action( 'wp_footer', 'bk_print_background_google_map' );

/*
 Globals
*/
$bk_unique_twitter_widget_id = 0;

/*----------------------------------------------------------------------------------
 register scripts used in theme
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_enqueue_scripts' ) ) {
	function bk_enqueue_scripts() {
		//Register styles

		wp_register_style('stylesheet', get_stylesheet_uri()  );
		wp_register_style('jackbox_css', get_template_directory_uri() . '/jackbox/css/jackbox.min.css' );
		wp_register_style('supersized_css', get_template_directory_uri() . '/css/supersized.css' );
		wp_register_style('supersized_shutter_css', get_template_directory_uri() . '/css/supersized.shutter.css' );

		//Enqueue styles
		wp_enqueue_style( 'stylesheet' );
		wp_enqueue_style( 'jackbox_css' );
		wp_enqueue_style( 'supersized_css' );
		wp_enqueue_style( 'supersized_shutter_css' );
		
		if( bk_is_woocommerce_activated() ) {
			wp_register_style('bk_custom_woocommerce', get_template_directory_uri() . '/css/bk_custom_woocommerce.css', array('woocommerce_frontend_styles') );
			wp_enqueue_style( 'bk_custom_woocommerce' );
		}

		wp_register_script('hoverintent', get_template_directory_uri() . '/js/jquery.hoverIntent.min.js', array('jquery') );
		wp_register_script('superfish', get_template_directory_uri() . '/js/superfish.js', array('jquery') );
		wp_register_script('jackbox_js', get_template_directory_uri() . '/jackbox/js/jackbox-packed.min.js', array('jquery') );
		wp_register_script('isotope', get_template_directory_uri() . '/js/jquery.isotope.js', array('jquery') );
		wp_register_script('infinite_scroll', get_template_directory_uri() . '/js/jquery.infinitescroll.js', array('jquery') );
		wp_register_script('touchswipe', get_template_directory_uri() . '/js/jquery.touchSwipe.min.js', array('jquery') );
		wp_register_script('jplayer', get_template_directory_uri() . '/js/jquery.jplayer.min.js', array('jquery') );
		wp_register_script('flexslider', get_template_directory_uri() . '/js/jquery.flexslider.js', array('jquery') );
		wp_register_script('jquery_easing_js', get_template_directory_uri() . '/js/jquery.easing.min.js', array('jquery') );


		wp_register_script('supersized_js', get_template_directory_uri() . '/js/supersized.3.2.7.min.js', array('jquery'), false, true );
		wp_register_script('supersized_shutter_js', get_template_directory_uri() . '/js/supersized.shutter.min.js', array('jquery'), false, true );

		wp_register_script('twitter',"//platform.twitter.com/widgets.js", false, false, true);
		wp_register_script('google_plus_one',"//apis.google.com/js/plusone.js", false, false, true);
		wp_register_script('pinterest',"//assets.pinterest.com/js/pinit.js", false, false, true);
		wp_register_script('google_maps',"//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false", false, false, true);

		if( bk_is_woocommerce_activated() ) {
			wp_register_script('custom_select', get_template_directory_uri() . '/js/jquery.customSelect.min.js', array('jquery') );
		}
		
		wp_register_script('bk_functions', get_template_directory_uri() . '/js/functions.js', array('jquery', 'isotope') );

		//Enqueue scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('isotope');
		wp_enqueue_script('hoverintent');
		wp_enqueue_script('superfish');
		wp_enqueue_script('jackbox_js');
		wp_enqueue_script('infinite_scroll');
		wp_enqueue_script('touchswipe');
		wp_enqueue_script('jplayer');
		wp_enqueue_script('flexslider');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery_easing_js');
		
		if( bk_is_woocommerce_activated() ) {
			wp_enqueue_script('custom_select');
		}
		
		wp_enqueue_script('bk_functions');

		if( get_option('bk_portfolio_show_tweet_button') || get_option('bk_blog_show_tweet_button') ) {
			wp_enqueue_script('twitter');
		}

		if( get_option('bk_portfolio_show_google_plus_button') || get_option('bk_blog_show_google_plus_button') ) {
			wp_enqueue_script('google_plus_one');
		}

		if( get_option('bk_portfolio_show_pinterest_button') || get_option('bk_blog_show_pinterest_button') ) {
			wp_enqueue_script('pinterest');
		}

		if ( (is_single() || is_page()) && comments_open() && get_option('thread_comments') ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_localize_script( 'bk_functions', 'bk_settings', array( 'bkajax_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'jackbox_js', 'bk_settings', array( 'bk_theme_root' => get_template_directory_uri() ) );
	}
}
add_action('wp_enqueue_scripts', 'bk_enqueue_scripts');

/*----------------------------------------------------------------------------------
 Custom search form
----------------------------------------------------------------------------------*/
if ( !function_exists( 'inq_search_form' ) ) {
	function inq_search_form( $form ) {
		$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '"><div class="bk-search-form-wrap"><input placeholder="' . __("Type and hit enter", 'corpora_theme') . '" type="text" value="" name="s" id="s" /></div></form>';
		return $form;
	}
}
//This function replaces the default functionality of get_search_form to the set by inq_search_form
add_filter( 'get_search_form', 'inq_search_form' );

/*
 * get protected password form for pages portfolio or posts
*/
if ( !function_exists( 'bk_password_form' ) ) {
	function bk_password_form() {
		global $post;

		$text = '<p>' . __( "To view this page, enter the password below:", 'corpora_theme' ) . '</p>';

		if( $post->post_type == 'post' ) {
	  $text = apply_filters("the_content", get_option("bk_protected_post_text"));
		} else if ( $post->post_type == 'portfolio' ) {
	  $text = apply_filters("the_content", get_option("bk_protected_portfolio_text"));
		} else if ( $post->post_type == 'page' ) {
	  $text = apply_filters("the_content", get_option("bk_protected_page_text"));
		}

		$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
		$o = '<div class="bk-protected-form-wrap"><form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
			  		' . $text . '
			  		<input placeholder="' . __( "Password", 'corpora_theme' ) . '" name="post_password" id="' . $label . '" type="password" size="30" /><br /><input type="submit" name="Submit" value="' . __( "Submit", 'corpora_theme' ) . '" />
							</form>
							</div>';
		return $o;
	}
}
add_filter( 'the_password_form', 'bk_password_form' );


/*----------------------------------------------------------------------------------
 handle gallery upload ajax button
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_bk_reset_counter_ajax', 'bk_reset_counter_ajax' );
if ( !function_exists( 'bk_reset_counter_ajax' ) ) {
	function bk_reset_counter_ajax() {
		$token = $_POST['token'];

		$data['success'] = false;
		if( $token == 'bk_reset_blog_love_counter' ) {
			$args= array(
					'post_type' => 'post',
					'posts_per_page' => -1
			);
			$query= new WP_Query($args);
			while($query->have_posts()):
			$query->next_post();
			$post_id = $query->post->ID;
				
			update_post_meta( $post_id, '_bk_portfolio_item_love_count', 0 );
				
			endwhile;
			wp_reset_postdata();
			$data['success'] = true;
		} elseif (  $token == 'bk_reset_portfolio_love_counter' ) {
			$args= array(
					'post_type' => 'portfolio',
					'posts_per_page' => -1
			);
			$query= new WP_Query($args);
			while($query->have_posts()):
			$query->next_post();
			$post_id = $query->post->ID;
				
			update_post_meta( $post_id, '_bk_portfolio_item_love_count', 0 );
				
			endwhile;
			wp_reset_postdata();
			$data['success'] = true;
		}	elseif( $token == 'bk_reset_portfolio_view_counter' ) {
			$args= array(
					'post_type' => 'portfolio',
					'posts_per_page' => -1
			);
			$query= new WP_Query($args);
			while($query->have_posts()):
			$query->next_post();
			$post_id = $query->post->ID;
				
			update_post_meta( $post_id, '_bk_portfolio_item_view_count', 0 );
				
			endwhile;
			wp_reset_postdata();
			$data['success'] = true;
		}

		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);
		exit;
	}
}


/**
 * Change Posts Per Page for Portfolio Archive
 *
 *
 */
add_action( 'pre_get_posts', 'bk_pre_get_posts_hook' );
if ( !function_exists( 'bk_pre_get_posts_hook' ) ) {
	function bk_pre_get_posts_hook( $query ) {

		if( $query->is_main_query() && !is_admin() && ( is_tax('filter') || is_tax('portfolio_skills') ) ) {
			$query->set( 'posts_per_page', get_option('bk_portfolio_archives_portfolios_per_page') );
		}

	}
}

/*----------------------------------------------------------------------------------
 Send Email ajax handle
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_bk_contact_form_ajax', 'bk_contact_form_ajax' );
add_action( 'wp_ajax_bk_contact_form_ajax', 'bk_contact_form_ajax' );

if ( !function_exists( 'bk_contact_form_ajax' ) ) {
	function bk_contact_form_ajax() {

		$from = $_POST['email_address'];
		$first_name = $_POST['first_name'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];

		if( bk_send_email( $from, $first_name, $subject, $message ) )
			$response = apply_filters('the_content', get_option("bk_email_success_info"));
		else
			$response = apply_filters('the_content', get_option("bk_email_fail_info"));

		header( "Content-Type: text/plain; charset=UTF-8" );
		echo $response;

		exit;
	}
}

if ( !function_exists( 'bk_send_email' ) ) {
	function bk_send_email($from, $first_name, $subject, $message) {
		$to = get_option('bk_admin_email');
		if( empty($to) )
			$to = get_option('admin_email');

		if( !empty($to) ) {
			$prefix = get_option('bk_contactform_email');
				
			$title = $prefix . ' ' . $first_name . ' "' . $subject . '"';
			$headers = 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $from . "\r\n" ;

			$msg = 'Name: ' . $first_name . "\r\n" .
					'Subject: ' . $subject . "\r\n" .
					'e-mail: ' . $from . "\r\n\r\n" .
					$message;

			return wp_mail($to, $title, $msg, $headers);
		} else {
			return false;
		}
	}
}
/*----------------------------------------------------------------------------------
 Portfolio load more items ajax handle
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_bk_load_more_portfolio_items_ajax', 'bk_load_more_portfolio_items_ajax' );
add_action( 'wp_ajax_bk_load_more_portfolio_items_ajax', 'bk_load_more_portfolio_items_ajax' );

if ( !function_exists( 'bk_load_more_portfolio_items_ajax' ) ) {
	function bk_load_more_portfolio_items_ajax() {

		$count = $_POST['count'];
		$layout = $_POST['portfolio_layout'];
		$show_cats = $_POST['show_cats'];
		$offset = $_POST['offset'];
		$portfolio_order = $_POST['portfolio_order'];
		$portfolio_order_by = $_POST['portfolio_order_by'];
		$portfolio_description = $_POST['portfolio_description'];
		$portfolio_image_effect = $_POST['portfolio_image_effect'];
		$portfolio_image_overlay = $_POST['portfolio_image_overlay'];
		$portfolio_base_size = $_POST['portfolio_base_size'];
		

		$args= array(
				'offset' => $offset,
				'posts_per_page' => $count,
				'post_type' => 'portfolio',
				'post_status' => 'publish',
				'order' => $portfolio_order,
				'orderby' => $portfolio_order_by
		);

		if( $show_cats != -1 ) {
			$args['tax_query'] = array(
					array(
							'taxonomy' => 'filter',
							'field' => 'slug',
							'terms' => explode(",", $show_cats)
					)
			);
		}

		$loaded_items_count = 0;
		$total_items_matched = 0;

		$items = bk_get_portfolio_items( $args, $layout, $portfolio_base_size, $show_cats, $portfolio_description, $portfolio_image_effect, $portfolio_image_overlay, $loaded_items_count, $total_items_matched );

		wp_reset_query();

		$offset += $loaded_items_count;

		$data = array( "loaded_items" => $loaded_items_count,
				"new_offset" => $offset,
				"total_items_matched" => $total_items_matched,
				"items" => trim($items) );

		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);

		exit;
	}
}
/*----------------------------------------------------------------------------------
 Portfolio load more items ajax handle
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_bk_load_more_gallery_items_ajax', 'bk_load_more_gallery_items_ajax' );
add_action( 'wp_ajax_bk_load_more_gallery_items_ajax', 'bk_load_more_gallery_items_ajax' );

if ( !function_exists( 'bk_load_more_gallery_items_ajax' ) ) {
	function bk_load_more_gallery_items_ajax() {

		$count = $_POST['count'];
		$layout = $_POST['portfolio_layout'];
		$show_cats = $_POST['show_cats'];
		$offset = $_POST['offset'];
		$portfolio_order = $_POST['portfolio_order'];
		$portfolio_order_by = $_POST['portfolio_order_by'];
		$portfolio_image_effect = $_POST['portfolio_image_effect'];
		$portfolio_image_overlay = $_POST['portfolio_image_overlay'];
		$portfolio_base_size = $_POST['portfolio_base_size'];

		$args= array(
				'offset' => $offset,
				'posts_per_page' => $count,
				'post_type' => 'gallery',
				'post_status' => 'publish',
				'order' => $portfolio_order,
				'orderby' => $portfolio_order_by
		);

		if( $show_cats != -1 ) {
			$args['tax_query'] = array(
					array(
							'taxonomy' => 'gallery_category',
							'field' => 'slug',
							'terms' => explode(",", $show_cats)
					)
			);
		}

		$loaded_items_count = 0;
		$total_items_matched = 0;

		$items = bk_get_gallery_items( $args, $layout, $portfolio_base_size, $show_cats, $portfolio_image_effect, $portfolio_image_overlay, $loaded_items_count, $total_items_matched );

		wp_reset_query();

		$offset += $loaded_items_count;

		$data = array( "loaded_items" => $loaded_items_count,
				"new_offset" => $offset,
				"total_items_matched" => $total_items_matched,
				"items" => trim($items) );

		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);

		exit;
	}
}
/*----------------------------------------------------------------------------------
 Blog post load more items ajax handle
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_bk_load_more_blog_items_ajax', 'bk_load_more_blog_items_ajax' );
add_action( 'wp_ajax_bk_load_more_blog_items_ajax', 'bk_load_more_blog_items_ajax' );

if ( !function_exists( 'bk_load_more_blog_items_ajax' ) ) {
	function bk_load_more_blog_items_ajax() {

		$count = $_POST['count'];
		$show_cats = $_POST['show_cats'];
		$offset = $_POST['offset'];

		$args= array(
				'offset' => $offset,
				'posts_per_page' => $count,
				'post_type' => 'post',
				'post_status' => 'publish'
		);

		if( $show_cats != -1 ) {
			$args['category_name'] = $show_cats;
		}

		$loaded_items_count = 0;
		$total_query_items = 0;

		$items = bk_get_blog_items( $args, $show_cats, $loaded_items_count, $total_query_items );

		wp_reset_query();

		$offset += $loaded_items_count;

		$data = array( "loaded_items" => $loaded_items_count,
				"new_offset" => $offset,
				"total_items_matched" => $total_query_items,
				"items" => $items );

		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);

		exit;
	}
}
/*----------------------------------------------------------------------------------
 Portfolio load portfolio item hook
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_bk_load_portfolio_item_ajax', 'bk_load_portfolio_item_ajax' );
add_action( 'wp_ajax_bk_load_portfolio_item_ajax', 'bk_load_portfolio_item_ajax' );

if ( !function_exists( 'bk_load_portfolio_item_ajax' ) ) {
	function bk_load_portfolio_item_ajax() {

		$post_id = $_POST['id'];

		bk_set_view_portfolio_counter( $post_id );
		$content = bk_get_portfolio_entry( $post_id, true );

		$success = false;
		if( !empty($content) )
			$success = true;

		$data = array( "success" => $success,
				"item" => $content );

		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);

		exit;
	}
}
/*----------------------------------------------------------------------------------
 Portfolio love portfolio item hook
----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_bk_love_portfolio_item_ajax', 'bk_love_portfolio_item_ajax' );
add_action( 'wp_ajax_bk_love_portfolio_item_ajax', 'bk_love_portfolio_item_ajax' );

if ( !function_exists( 'bk_love_portfolio_item_ajax' ) ) {
	function bk_love_portfolio_item_ajax() {

		$post_id = $_POST['id'];

		$post_type = get_post($post_id)->post_type;

		$timeout = 24; // set cookie for 24 hours by defualt
		if( $post_type == 'portfolio' ) {
			$timoeut_hours = get_option('bk_portfolio_love_button_timeout', 24);  // it is stored in hours
		} elseif( $post_type == 'post' ) {
			$timoeut_hours = get_option('bk_post_love_button_timeout', 24);  // it is stored in hours
		}

		if($timoeut_hours == -1 || $timoeut_hours == 0) {
			$timeout = 24 * 365 * 10;  // set timout for 10 years
		} else {
			$timeout = $timoeut_hours;
		}

		$love_count = get_post_meta( $post_id, '_bk_portfolio_item_love_count', true );
		if( empty( $love_count ) ) {
			$love_count = 0;
		}

		if( !isset($_COOKIE['bk_love_post_'. $post_id]) ) {
			$love_count++;
			update_post_meta($post_id, '_bk_portfolio_item_love_count', $love_count );
		}

		setcookie('bk_love_post_'. $post_id, true, time() + ( 3600 * $timeout ), '/');  // set cookie for 24 hours mulitply by 3600 seconds

		$data = array( "love_count" => $love_count );

		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);

		exit;
	}
}
/*----------------------------------------------------------------------------------
 utils - number to human readable string 1500 -> 1.5K
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_number_to_human_readable_string' ) ) {
	function bk_get_number_to_human_readable_string( $number ) {
		if( ($number / 1000000) >= 1 ) {
			return round( $number / 1000000, 1 ) . ' M';
		} else if( ($number / 1000) >= 1 ) {
			return round( $number / 1000, 1 ) . ' K';
		}	else {
			return $number;
		}
	}
}
/*----------------------------------------------------------------------------------
 print header logo regular and retina version
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_print_header_logo' ) ) {
	function bk_print_header_logo() {
		$logo = get_option('bk_header_logo_url');
		if( isset($logo) && !empty($logo) ) {
			$retina_logo = get_option('bk_header_retina_logo_url');

			$standard_logo_classes = '';
			if( isset($retina_logo) && !empty($retina_logo) ) {
				$standard_logo_classes = 'with-retina-replacement';
			}

			echo '<div id="bk-logo-wrap">';
			echo '<a href="' .  home_url(). '">';
			echo '<img class="bk-theme-logo standard ' . $standard_logo_classes . '" src="' . $logo . '" alt="' . get_bloginfo( 'name' ) . '" title="' . get_bloginfo( 'name' ) . '" />';
			if( isset($retina_logo) && !empty($retina_logo) ) {
				$img_id =  bk_get_image_id($retina_logo);
				$retina_image = wp_get_attachment_image_src( $img_id, 'fullsize' );
				if( $retina_image ) {
					$width = floor($retina_image[1] / 2);
					echo '<img width="' . $width . '" class="bk-theme-logo retina" src="' . $retina_logo . '" alt="' . get_bloginfo( 'name' ) . '" title="' . get_bloginfo( 'name' ) . '" />';
				}
			}
			echo '</a>';
			echo '</div>';
		}

	}
}
/*
 * Prints out header navigation for site
*
*/
if ( !function_exists( 'bk_print_header_navigation' ) ) {
	function bk_print_header_navigation() {
		$header_additional_classes = array();
		$bk_content_outer_wrap_classes = array();
		$bk_collapsible_header = get_option("bk_collapsible_header");
		$header_collapsed_onload = get_option("bk_header_collapsed_onload");
		$header_layout = get_option("bk_header_layout");
		$header_menu = get_option("bk_header_menu");

		$header_additional_classes[] = 'clearfix';

		global $post;

		if( ( is_page() || is_single() ) && get_post_meta( $post->ID, '_bk_override_header_settings', true ) == true ) {
			$bk_collapsible_header = get_post_meta( $post->ID, '_bk_collapsible_header', true );
			$header_collapsed_onload = get_post_meta( $post->ID, '_bk_header_collapsed_onload', true );
		}

		if( $bk_collapsible_header ) {
			$header_additional_classes[] = 'bk-collapsible-header';
			if( $header_collapsed_onload ) {
				$header_additional_classes[] = 'bk-collapsed-header';
				$bk_content_outer_wrap_classes[] = 'bk-navigation-hidden';
			}
		}

		$header_additional_classes[] = $header_layout;

		if( $header_layout == 'bk-classic-header-top' || $header_layout == 'bk-fixed-header-top' ) {
			$bk_content_outer_wrap_classes[] = 'bk-top-header';
			$header_menu = 'bk-sfmenu';
		}

		echo '<header id="bk-header" class="' . implode(' ',$header_additional_classes) . '" role="banner">';
		echo '<div id="bk-header-inner-wrap" class="clearfix">';
		bk_get_header_socials_icons();

		bk_print_header_logo();

		echo '<nav id="nav-main" class="clearfix" role="navigation">';
		if (has_nav_menu('primary-menu')) {
			if( $header_layout == 'bk-fixed-header-left' ) {
				if( $header_menu == "bk-sfmenu" ) {
					wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'sf-menu', 'menu_id' => 'bk-main-menu', 'container' => 'div', 'container_id' => 'fullwidth_navigation', 'walker' => new bk_description_walker() ) );
					wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'bk-stacked-menu', 'menu_id' => 'bk-responsive-menu', 'container' => 'div', 'container_id' => 'responsive_navigation', 'walker' => new bk_description_walker() ) );
				} else if( $header_menu == "bk-stacked-menu" ) {
					wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'bk-stacked-menu', 'menu_id' => 'bk-responsive-menu', 'container' => 'div', 'container_id' => 'fullwidth_navigation', 'walker' => new bk_description_walker() ) );
					wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'bk-stacked-menu', 'menu_id' => 'bk-responsive-menu', 'container' => 'div', 'container_id' => 'responsive_navigation', 'walker' => new bk_description_walker() ) );
				}
			} else if( $header_layout == 'bk-classic-header-top' || $header_layout == 'bk-fixed-header-top' ) {
				$container_class = get_option("bk_header_menu_float");
				wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'sf-menu', 'menu_id' => 'bk-main-menu', 'container' => 'div', 'container_id' => 'fullwidth_navigation', 'container_class' => $container_class, 'walker' => new bk_description_walker() ) );
				wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_class' => 'bk-stacked-menu', 'menu_id' => 'bk-responsive-menu', 'container' => 'div', 'container_id' => 'responsive_navigation', 'walker' => new bk_description_walker() ) );
			}
		}

		echo '</nav>';

		if( $header_layout == 'bk-fixed-header-left' ) {
			bk_print_primary_sidebar();
			
			bk_print_header_tagline();
		}
		
		echo '</div>';

		if( $bk_collapsible_header ) {
			echo '<span id="bk-navigation-toggle"><span id="bk-toggle-button"></span></span>';
		}

		echo '<div id="bk-header-shadow"></div>';
		echo '</header>';
		
		//if( $header_layout == 'bk-classic-header-top' ) {
		//	echo '<div class="bk-fixed-header-spacer" style="display: block; float: none;"></div>';
		//}
		
		echo '<div id="bk-content-outer-wrap" class="' . implode(' ', $bk_content_outer_wrap_classes) . '" role="document">';
	}
}
/*
 * Print header tagline
 */
if (!function_exists( 'bk_print_header_tagline' )) {
	function bk_print_header_tagline() {
	  $header_tagline = get_option("bk_header_tagline");
	  if( !empty($header_tagline) ) {
	  	echo '<div id="bk-header-tagline" class="clearfix">';
	  	echo apply_filters("the_content", $header_tagline);
	  	echo '</div>'; 
	  }
    }
}

/*
 * Get primary sidebar which is displayed on the left side below menu
*
*/
if ( !function_exists( 'bk_print_primary_sidebar' ) ) {
	function bk_print_primary_sidebar() {
		$primary_sidebar_id = -1;

		global $post;

		if( bk_is_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
			$primary_sidebar_id = get_option("bk_shop_archives_sidebar_id");
		} else if( is_page() || is_single() ) {
			$primary_sidebar_id = get_post_meta( $post->ID, '_bk_page_sidebar_id', true );
		} else if(is_search() || is_category() || is_day() || is_month() || is_year() || is_tag() || is_author() || is_tax('post_format') ) {
			$primary_sidebar_id = get_option("bk_blog_archives_sidebar_id");
		} else if( is_tax('filter') || is_tax('portfolio_skills') ) {
			$primary_sidebar_id = get_option("bk_portfolio_archives_sidebar_id");
		} else if( is_404() ) {
			if( get_option("bk_404_page_id") != -1 )
				$primary_sidebar_id = get_post_meta( get_option("bk_404_page_id"), '_bk_page_sidebar_id', true );
		}

		if( $primary_sidebar_id != -1 && is_active_sidebar( $primary_sidebar_id ) ) {
			echo '<div class="bk-sidebar bk-primary-sidebar">';
			echo '<ul class="sidebar-content clearfix">';
			dynamic_sidebar( $primary_sidebar_id );
			echo '</ul>';
			echo '</div>';
		}
	}
}
/*----------------------------------------------------------------------------------
 Custom navigation walker that uses descriptions for top level menu pages
----------------------------------------------------------------------------------*/
$bk_sub_menu_offset_correction = null;

$bk_menu_portfolio_page_id = get_option("bk_portfolio_homepage_link");
$bk_menu_blog_page_id = get_option("bk_blog_homepage_link");

class bk_description_walker extends Walker_Nav_Menu
{
	
	
	function start_lvl(&$output, $depth = 0, $args = Array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu bk-sub-menu\">\n";
	}


	function start_el(&$output, $item, $depth = 0, $args = Array(), $current_object_id = 0)
	{
		global $bk_menu_portfolio_page_id;
		global $bk_menu_blog_page_id;
		$prepend = '';
		$append = '';
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			
		$classes[] = "bk-sub-menu-item";
		
		if( (get_post_type() == 'portfolio' && $bk_menu_portfolio_page_id == $item->object_id) || 
			(get_post_type() == 'post' && $bk_menu_blog_page_id == $item->object_id) ) {
			$classes[] = "current-menu-item";
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$description  = ! empty( $item->description ) ? '<span class="bk-description">'.esc_attr( $item->description ).'</span>' : '';
			
		/*  Comment this out if You want the descriptions to be showed only for top level menus
		 if($depth != 0)
		 {
		$description = $append = $prepend = "";
		}
		*/

		$item_output = $args->before;
		$item_output .= '<a class="bk-menu-item-anchor" '. $attributes .'>';
		$item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
		$item_output .= $description.$args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
/*----------------------------------------------------------------------------------
 print header inline styles - boxed, fullwidth layout classes, body content regular
and retina backgrounds, theme skin rules, theme fonts
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_print_header_inline_style' ) ) {
	function bk_print_header_inline_style() {
		$style = '<style type="text/css">';

		/*
		 Header sizes
		*/
		$style .= '/*-----------------Header------------------*/' . "\n";

		/*
		 * Logo when fixed left header is on
	 */
		$logo_top_margin = get_option('bk_header_logo_top_margin');
		$logo_bottom_margin = get_option('bk_header_logo_bottom_margin');

		$style .= '.bk-fixed-header-left #bk-logo-wrap {margin-top: ' . $logo_top_margin . 'px; margin-bottom: ' . $logo_bottom_margin . 'px;}' . "\n";

		/*
		 * Logo when fixed or classic horizontal header is on
	 */
		$logo_top_margin_horizontal_header = get_option('bk_header_logo_top_margin_horizontal_header');
		$style .= '.bk-fixed-header-top #bk-logo-wrap, .bk-classic-header-top #bk-logo-wrap {margin-top: ' . $logo_top_margin_horizontal_header . 'px;}' . "\n";

		/*
		 * Navigation top bottom margins when classic or fixed horizontal header is on
	 */

		$navigation_top_margin = get_option("bk_navigation_top_margin");
		$navigation_bottom_margin = get_option("bk_navigation_bottom_margin");

		$style .= '.bk-fixed-header-top .sf-menu > li.menu-item, .bk-classic-header-top .sf-menu > li.menu-item {padding-top: ' . $navigation_top_margin . 'px; padding-bottom: ' . $navigation_bottom_margin. 'px;}' . "\n";

		/*
		 * Social icons top margin when classic or fixed horizontal header is used
	 */

		$social_top_margin = get_option("bk_social_icons_top_margin");
		$style .= '.bk-fixed-header-top #bk-header-social-wrapper, .bk-classic-header-top #bk-header-social-wrapper {margin-top: ' . $social_top_margin . 'px;}' . "\n";

		/*
		 * Responsive navigation logo top ottom margin
	 *
	 */
		$responsive_logo_top_margin = get_option('bk_responsive_header_logo_top_margin');
		$responsive_logo_bottom_margin = get_option('bk_responsive_header_logo_bottom_margin');

		$style .= '@media only screen and (max-width: 767px) { .bk-fixed-header-left #bk-logo-wrap, .bk-fixed-header-top #bk-logo-wrap, .bk-classic-header-top #bk-logo-wrap {margin-top: ' . $responsive_logo_top_margin . 'px; margin-bottom: ' . $responsive_logo_bottom_margin . 'px;}}' . "\n";
		/*
		 Theme fonts
		*/
		$google_fonts = array();

		$style .= '/*-----------------Theme Fonts------------------*/' . "\n";

		/*
		 body main font
		*/
		$body_font_type = get_option("bk_body_font_type", -1);
		$style .= 'body{';
		if( $body_font_type != -1 ) {
			if( !in_array( $body_font_type, $google_fonts ) ) {
				$google_fonts[] = $body_font_type . ',700';  // add bold font to make hardcoded bol font look ok
			}
			$style .= google_font_family_to_style( $body_font_type, true ); // add fallback?? 'Helvetica,Arial,Garuda,sans-serif'
		}
		$style .= 'font-size:' . get_option("bk_body_font_size", 13) . 'px; ';
		$style .= 'line-height:' . get_option("bk_body_line_height", 19) . 'px; ';
		$style .= '}' . "\n";

		/*
		 Headers h1 ... h6
		*/
		for( $i = 1; $i <= 6; $i++ ) {
			$heading_font_type = get_option("bk_h" . $i . "_font_type", -1);
			$style .= 'h' . $i . '{';
			if( $heading_font_type != -1 ) {
				if( !in_array( $heading_font_type, $google_fonts ) ) {
					$google_fonts[] = $heading_font_type;
				}
				$style .=  google_font_family_to_style( $heading_font_type );
			}
			$style .= 'font-size:' . get_option("bk_h" . $i . "_font_size") . 'px; ';
			$style .= '}' . "\n";
		}

		/*
		 Navigation
		*/

		$navigation_primary_font_type = get_option("bk_navigation_primary_font_type", -1);
		$style .= '.bk-stacked-menu > li > a, .sf-menu > li > a {';
		if( $navigation_primary_font_type != -1 ) {
			if( !in_array( $navigation_primary_font_type, $google_fonts ) ) {
				$google_fonts[] = $navigation_primary_font_type;
			}
			$style .=  google_font_family_to_style( $navigation_primary_font_type );
		}
		$style .= 'font-size:' . get_option("bk_navigation_primary_font_size", 14) . 'px; ';
		$style .= '}' . "\n";

		$navigation_secondary_font_type = get_option("bk_navigation_secondary_font_type", -1);
		$style .= '.bk-stacked-menu li a span.bk-description, .sf-menu li a span.bk-description {';
		if( $navigation_secondary_font_type != -1 ) {
			if( !in_array( $navigation_secondary_font_type, $google_fonts ) ) {
				$google_fonts[] = $navigation_secondary_font_type;
			}
			$style .=  google_font_family_to_style( $navigation_secondary_font_type );
		}
		$style .= 'font-size:' . get_option("bk_navigation_secodnary_font_size", 12) . 'px; ';
		$style .= 'line-height:' . get_option("bk_navigation_secodnary_font_size", 12) . 'px; ';
		$style .= '}' . "\n";

		$navigation_tertiary_font_type = get_option("bk_navigation_tertiary_font_type", -1);
		$style .= '.bk-stacked-menu ul li a, ul.sf-menu ul.sub-menu li.menu-item > a {';
		if( $navigation_tertiary_font_type != -1 ) {
			if( !in_array( $navigation_tertiary_font_type, $google_fonts ) ) {
				$google_fonts[] = $navigation_tertiary_font_type;
			}
			$style .=  google_font_family_to_style( $navigation_tertiary_font_type );
		}
		$style .= 'font-size:' . get_option("bk_navigation_tertiary_font_size", 12) . 'px; ';
		$style .= 'line-height:' . get_option("bk_navigation_tertiary_font_size", 12) . 'px; ';
		$style .= '}' . "\n";

		$bk_pagination_font_type = get_option("bk_pagination_font_type", -1);
		$style .= 'a.bk-load-more-button, div.bk-pagination-wrap {';
		if( $bk_pagination_font_type != -1 ) {
			if( !in_array( $bk_pagination_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_pagination_font_type;
			}
			$style .=  google_font_family_to_style( $bk_pagination_font_type );
		}
		$style .= 'font-size:' . get_option("bk_pagination_font_size", 13) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * Divider, widget tiles, tabs toggles etc...
	 */

		$bk_divider_font_type = get_option("bk_divider_font_type", -1);
		$style .= 'h3.widget-title, .content-separator, div.bk-tabs ul.ui-tabs-nav li a {';
		if( $bk_divider_font_type != -1 ) {
			if( !in_array( $bk_divider_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_divider_font_type;
			}
			$style .=  google_font_family_to_style( $bk_divider_font_type );
		}
		$style .= 'font-size:' . get_option("bk_divider_font_size", 12) . 'px; ';
		$style .= 'line-height:' . get_option("bk_divider_font_size", 12) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * widget post tile
		*/

		$bk_widget_post_font_type = get_option("bk_widget_post_font_type", -1);
		$style .= 'ul.bk-recent-posts-widget span.widget-post-title {';
		if( $bk_widget_post_font_type != -1 ) {
			if( !in_array( $bk_widget_post_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_widget_post_font_type;
			}
			$style .=  google_font_family_to_style( $bk_widget_post_font_type );
		}
		$style .= 'font-size:' . get_option("bk_widget_post_font_size", 13) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * Regular page title font
	 */

		$bk_page_title_font_type = get_option("bk_page_title_font_type", -1);
		$style .= 'h1.page-entry-title {';
		if( $bk_page_title_font_type != -1 ) {
			if( !in_array( $bk_page_title_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_page_title_font_type;
			}
			$style .=  google_font_family_to_style( $bk_page_title_font_type );
		}
		$style .= 'font-size:' . get_option("bk_page_title_font_size", 24) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * Single portfolio title
	 */

		$bk_portfolio_title_font_type = get_option("bk_portfolio_title_font_type", -1);
		$style .= 'div.bk-portfolio-title-wrap h1.bk-portfolio-title {';
		if( $bk_portfolio_title_font_type != -1 ) {
			if( !in_array( $bk_portfolio_title_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_portfolio_title_font_type;
			}
			$style .=  google_font_family_to_style( $bk_portfolio_title_font_type );
		}
		$style .= 'font-size:' . get_option("bk_portfolio_title_font_size", 24) . 'px; ';
		$style .= '}' . "\n";


		/*
		 * Portfolio thumb overlay title
	 */
		$bk_portfolio_thumb_title_font_type = get_option("bk_portfolio_thumb_title_font_type", -1);
		$style .= 'div.portfolio-thumb-title-meta-wrap	h4.portfolio-thumb-title {';
		if( $bk_portfolio_thumb_title_font_type != -1 ) {
			if( !in_array( $bk_portfolio_thumb_title_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_portfolio_thumb_title_font_type;
			}
			$style .=  google_font_family_to_style( $bk_portfolio_thumb_title_font_type );
		}
		$style .= 'font-size:' . get_option("bk_portfolio_thumb_title_font_size", 24) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * Portfolio thumb overlay tags
		*/
		$bk_portfolio_thumb_tags_font_type = get_option("bk_portfolio_thumb_tags_font_type", -1);
		$style .= 'div.portfolio-thumb-title-meta-wrap span.porfolio-thumb-tags {';
		if( $bk_portfolio_thumb_tags_font_type != -1 ) {
			if( !in_array( $bk_portfolio_thumb_tags_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_portfolio_thumb_tags_font_type;
			}
			$style .=  google_font_family_to_style( $bk_portfolio_thumb_tags_font_type );
		}
		$style .= 'font-size:' . get_option("bk_portfolio_thumb_tags_font_size", 10) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * Portfolio thumb description title
	 */
		$bk_portfolio_thumb_desc_title_font_type = get_option("bk_portfolio_thumb_desc_title_font_type", -1);
		$style .= 'li.portfolio-item .portfolio-thumb-description-title {';
		if( $bk_portfolio_thumb_desc_title_font_type != -1 ) {
			if( !in_array( $bk_portfolio_thumb_desc_title_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_portfolio_thumb_desc_title_font_type;
			}
			$style .=  google_font_family_to_style( $bk_portfolio_thumb_desc_title_font_type );
		}
		$style .= 'font-size:' . get_option("bk_portfolio_thumb_desc_title_font_size", 14) . 'px; ';
		$style .= '}' . "\n";


		/*
		 *  Single post title
	 */

		$bk_blog_title_font_type = get_option("bk_blog_title_font_type", -1);
		$style .= 'h1.post-entry-title {';
		if( $bk_blog_title_font_type != -1 ) {
			if( !in_array( $bk_blog_title_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_blog_title_font_type;
			}
			$style .=  google_font_family_to_style( $bk_blog_title_font_type );
		}
		$style .= 'font-size:' . get_option("bk_blog_title_font_size", 24) . 'px; ';
		$style .= '}' . "\n";

		/*
		 * Blog thumb title
	 */
		$bk_blog_title_font_type = get_option("bk_blog_thumb_title_font_type", -1);
		$style .= 'h3.post-entry-title {';
		if( $bk_blog_title_font_type != -1 ) {
			if( !in_array( $bk_blog_title_font_type, $google_fonts ) ) {
				$google_fonts[] = $bk_blog_title_font_type;
			}
			$style .=  google_font_family_to_style( $bk_blog_title_font_type );
		}
		$style .= 'font-size:' . get_option("bk_blog_thumb_font_size", 16) . 'px; ';
		$style .= '}' . "\n";



		/*----------------------------------------------------------------------------------
		 Theme colors style
		----------------------------------------------------------------------------------*/

		global $post;

		/*
		 * Page
		*/

		$bk_page_background_color = get_option('bk_page_background_color');
		$bk_page_background_image = get_option('bk_page_background_image');
		$bk_page_background_image_position = get_option('bk_page_background_image_position');
		$bk_page_background_image_repeat = get_option('bk_page_background_image_repeat');
		$bk_page_background_image_attachment = get_option('bk_page_background_image_attachment');
		$bk_page_retina_background_image = get_option('bk_page_retina_background_image');

		if( (is_single() || is_page() ) && !is_search() && get_post_meta($post->ID, '_bk_override_page_default_background', true) == true ) {
			$bk_page_background_color = get_post_meta($post->ID, '_bk_page_background_color', true);
			$bk_page_background_image = get_post_meta($post->ID, '_bk_page_background_image', true);
			$bk_page_background_image_position = get_post_meta($post->ID, '_bk_page_background_image_position', true);
			$bk_page_background_image_repeat = get_post_meta($post->ID, '_bk_page_background_image_repeat', true);
			$bk_page_background_image_attachment = get_post_meta($post->ID, '_bk_page_background_image_attachment', true);
			$bk_page_retina_background_image = get_post_meta($post->ID, '_bk_page_retina_background_image', true);
		}

		$style .= 'body' . bk_get_style( $bk_page_background_image, $bk_page_background_image_position, $bk_page_background_image_repeat, $bk_page_background_color, $bk_page_background_image_attachment )  . "\n";

		if( $bk_page_retina_background_image ) {
			$style .= bk_get_retina_background_css_markup( 'body', $bk_page_retina_background_image )  . "\n";
		}

		/*
		 * end of page
		*/

		/*
		 Body
		*/
		$bk_body_font_color = get_option('bk_body_font_color');
		$style .= 'body, input[type="text"], inpup[type="password"] textarea{color:' . $bk_body_font_color . ';}' . "\n";

		$bk_body_link_color = get_option('bk_body_link_color');
		$style .= 'body a{color:' . $bk_body_link_color . ';}' . "\n";

		$bk_body_link_hover_color = get_option('bk_body_link_hover_color');
		$style .= 'body a:hover{color:' . $bk_body_link_hover_color . ';}' . "\n";

		$bk_body_background_color = get_option('bk_body_background_color');
		$bk_body_background_image = get_option('bk_body_background_image');
		$bk_body_background_image_position = get_option('bk_body_background_image_position');
		$bk_body_background_image_repeat = get_option('bk_body_background_image_repeat');
		$bk_body_retina_background_image = get_option('bk_body_retina_background_image');

		if( (is_single() || is_page() ) && !is_search() && get_post_meta($post->ID, '_bk_override_body_default_background', true) == true ) {
			$bk_body_background_color = get_post_meta($post->ID, '_bk_body_background_color', true);
			$bk_body_background_image = get_post_meta($post->ID, '_bk_body_background_image', true);
			$bk_body_background_image_position = get_post_meta($post->ID, '_bk_body_background_image_position', true);
			$bk_body_background_image_repeat = get_post_meta($post->ID, '_bk_body_background_image_repeat', true);
			$bk_body_retina_background_image = get_post_meta($post->ID, '_bk_body_retina_background_image', true);
		}

		$style .= '.single-page .bk-fixed-page, .page .bk-fixed-page, .archive .bk-fixed-page, .single-post #bk-content-wrap, .single-portfolio article.description_right_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_right_no_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_right div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_left div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_top div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_bottom div.bk-portfolio-content-wrap' . bk_get_style( $bk_body_background_image, $bk_body_background_image_position, $bk_body_background_image_repeat, $bk_body_background_color )  . "\n";

		if( $bk_body_retina_background_image ) {
			$style .= bk_get_retina_background_css_markup( '.single-page .bk-fixed-page, .page .bk-fixed-page, .archive .bk-fixed-page, .single-post #bk-content-wrap, .single-portfolio article.description_right_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_with_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_right_no_meta div.bk-portfolio-content-wrap, .single-portfolio article.description_left_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_right div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_left div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_no_meta div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_top div.bk-portfolio-content-wrap, .single-portfolio .bk-fixed-page article.fullwidth_meta_bottom div.bk-portfolio-content-wrap', $bk_body_retina_background_image )  . "\n";
		}

		/* Body separator color */
		$bk_separator_color = get_option('bk_separator_color');
		$style .= 'h3.widget-title span, .content-separator span.title {border-bottom-color:' . $bk_separator_color . ';}' . "\n";

		/* Body image overlay color */
		$bk_image_overlay_background = get_option('bk_image_overlay_background');

		$bk_img_opacity = get_option('bk_image_overlay_opacity');
		$thumb_overlay_opacity = floatval( $bk_img_opacity / 100 );

		$style .= 'div.bk-img-overlay {background-color:' . $bk_image_overlay_background . ';}' . "\n";
		$style .= 'div.bk-img-overlay {background-color:' . bk_hex2rgba($bk_image_overlay_background, $thumb_overlay_opacity) . ';}' . "\n";

		$bk_image_overlay_text_color = get_option('bk_image_overlay_text_color');
		$style .= 'div.bk-img-overlay {color:' . $bk_image_overlay_text_color . ';}' . "\n";
		$style .= 'div.title_and_tags div.portfolio-thumb-title-meta-wrap h4.portfolio-thumb-title, div.title_and_cats div.portfolio-thumb-title-meta-wrap	h4.portfolio-thumb-title {border-bottom-color:' . $bk_image_overlay_text_color . ';}' . "\n";


		/*
		 * portfolio, gallery thumb tags
	 */

		$bk_image_overlay_tag_color = get_option('bk_image_overlay_tag_color');
		$style .= 'div.portfolio-thumb-title-meta-wrap span.porfolio-thumb-tags a {color:' . $bk_image_overlay_tag_color . ';}' . "\n";

		$bk_image_overlay_tag_hover_color = get_option('bk_image_overlay_tag_hover_color');
		$style .= 'div.portfolio-thumb-title-meta-wrap span.porfolio-thumb-tags a:hover {color:' . $bk_image_overlay_tag_hover_color . ';}' . "\n";


		/* Body odd background color */
		$bk_odd_background = get_option('bk_odd_background');
		$style .= 'ol.bk_comment_list li.comment div.content-wrap, div.bk-testimonial-body, ol.bk_comment_list div#respond, ul.bk-recent-comments-widget div.bk-recent-comment {background-color:' . $bk_odd_background . ' ;}' . "\n";
		$style .= 'ol.bk_comment_list li.comment div.content-wrap span.comment-tip, ul.bk-recent-comments-widget div.bk-recent-comment:after { border-right-color:' . $bk_odd_background . ' ; }' . "\n";
		$style .= 'div.testimonial-tip {border-top-color:' . $bk_odd_background . ';}' . "\n";

		/* textarea input border */
		$bk_body_input_text_area_border = get_option('bk_body_input_text_area_border');
		$style .= 'input[type="text"], input[type="password"], textarea, input[type="email"] {border-color:' . $bk_body_input_text_area_border . ' ;}' . "\n";

		/* textarea input background */
		$bk_body_input_text_area_background = get_option('bk_body_input_text_area_background');
		$style .= 'input[type="text"], input[type="password"], textarea, input[type="email"] {background-color:' . $bk_body_input_text_area_background . ' ;}' . "\n";

		/* textarea input focus border */
		$bk_body_input_text_area_border_focus = get_option('bk_body_input_text_area_border_focus');
		$style .= 'input[type="text"]:focus, input[type="password"]:focus, textarea:focus, input[type="email"]:focus {border-color:' . $bk_body_input_text_area_border_focus . ' ;}' . "\n";

		/* textarea input focus background */
		$bk_body_input_text_area_background_focus = get_option('bk_body_input_text_area_background_focus');
		$style .= 'input[type="text"]:focus, input[type="password"]:focus, textarea:focus, input[type="email"]:focus {background-color:' . $bk_body_input_text_area_background_focus . ' ;}' . "\n";

		/*
		 End of Body
		*/

		/*
		 * Header
	 */

		$bk_header_background_color = get_option('bk_header_background_color');
		$bk_header_background_image = get_option('bk_header_background_image');
		$bk_header_background_image_position = get_option('bk_header_background_image_position');
		$bk_header_background_image_repeat = get_option('bk_header_background_image_repeat');

		$style .= '#bk-header' . bk_get_style( $bk_header_background_image, $bk_header_background_image_position, $bk_header_background_image_repeat, $bk_header_background_color )  . "\n";

		$style .= '#bk-header.bk-collapsible-header #bk-navigation-toggle:after{border-color: transparent; border-left-color:' . $bk_header_background_color . '}' . "\n";
		$style .= '@media only screen and (max-width: 767px) {#bk-header.bk-collapsible-header #bk-navigation-toggle:after{border-color: transparent; border-top-color:' . $bk_header_background_color . '}}' . "\n";
		$style .= '#bk-header.bk-classic-header-top.bk-collapsible-header #bk-navigation-toggle:after, #bk-header.bk-fixed-header-top.bk-collapsible-header #bk-navigation-toggle:after{border-color: transparent; border-top-color:' . $bk_header_background_color . '}' . "\n";

		$bk_header_retina_background_image = get_option('bk_header_retina_background_image');
		if( $bk_header_retina_background_image ) {
			$style .= bk_get_retina_background_css_markup( '#bk-header', $bk_header_retina_background_image )  . "\n";
		}

		$bk_header_font_color = get_option('bk_header_font_color');
		$style .= '#bk-header {color:' . $bk_header_font_color . ';}' . "\n";

		$bk_header_link_color = get_option('bk_header_link_color');
		$style .= '#bk-header a{color:' . $bk_header_link_color . ';}' . "\n";
		//$style .= '#bk-header li.widget .tagcloud a{color:' . $bk_header_link_color . '; border-color:' . $bk_header_link_color . ';}' . "\n";

		$bk_header_link_hover_color = get_option('bk_header_link_hover_color');
		$style .= '#bk-header a:hover{color:' . $bk_header_link_hover_color . ';}' . "\n";

		/*
		 Navigation
		*/
		$bk_navigation_text_color = get_option('bk_navigation_text_color');
		$style .= '#bk-header .bk-stacked-menu li a, #bk-header ul.sf-menu > li.menu-item > a, .sf-menu li.menu-item > a, #bk-header .sf-menu li.menu-item > a:visited, #bk-header .menu li.menu-item a {color:' . $bk_navigation_text_color . ';}' . "\n";

		$bk_navigation_text_hover_color = get_option('bk_navigation_text_hover_color');
		$style .= '#bk-header .bk-stacked-menu li > a:hover, #bk-header .bk-stacked-menu li.current-menu-item > a, #bk-header ul.sf-menu > li.menu-item > a:hover, #bk-header ul.sf-menu li.menu-item.current-menu-item > a, #bk-header .sf-menu li.menu-item:hover > a, #bk-header .sf-menu li.menu-item.sfHover > a, #bk-header .menu li.menu-item a:hover, .menu li.menu-item a:hover, #bk-header .menu li.current-menu-item a, .menu li.current-menu-item a {color:' . $bk_navigation_text_hover_color  . ';}' . "\n";

		$bk_navigation_submenu_color = get_option('bk_navigation_submenu_color');
		$style .= 'ul.sf-menu ul.sub-menu {background-color:' . $bk_navigation_submenu_color . ';}' . "\n" ;

		/* pagination background */
		$bk_pagination_background = get_option('bk_pagination_background');
		$style .= 'a.bk-load-more-button, div.bk-pagination-wrap {background-color:' . $bk_pagination_background . ' ;}' . "\n";

		/* pagination text color */
		$bk_pagination_text_color = get_option('bk_pagination_text_color');
		$style .= 'a.bk-load-more-button, ul.bk-pagination li a {color:' . $bk_pagination_text_color . ' ;}' . "\n";

		/* pagination text hover color */
		$bk_pagination_text_hover_color = get_option('bk_pagination_text_hover_color');
		$style .= 'a.bk-load-more-button:hover, ul.bk-pagination li.current a, ul.bk-pagination li a:hover {color:' . $bk_pagination_text_hover_color . ' ;}' . "\n";


		/*
		 * Navigation portfolio filter
	 */
		$bk_navigation_portfolio_filter_text_color = get_option('bk_navigation_portfolio_filter_text_color');
		$style .= '#bk-header ul.portfolio-filter li a {color:' . $bk_navigation_portfolio_filter_text_color . ';}' . "\n";

		$bk_navigation_portfolio_filter_text_hover_color = get_option('bk_navigation_portfolio_filter_text_hover_color');
		$style .= '#bk-header ul.portfolio-filter li a:hover, #bk-header ul.portfolio-filter li.current a  {color:' . $bk_navigation_portfolio_filter_text_hover_color  . ';}' . "\n";

		/*
		 * Portfolio filter
	 */

		$bk_portfolio_filter_text_color = get_option('bk_portfolio_filter_text_color');
		$style .= 'ul.portfolio-filter li a {color:' . $bk_portfolio_filter_text_color . ';}' . "\n";

		$bk_portfolio_filter_text_hover_color = get_option('bk_portfolio_filter_text_hover_color');
		$style .= 'ul.portfolio-filter li a:hover, ul.portfolio-filter li.current a  {color:' . $bk_portfolio_filter_text_hover_color  . ';}' . "\n";

		/*
		 * Portfolio thumb
	 */

		$bk_portfolio_thumb_description_background_color = get_option('bk_portfolio_thumb_description_background_color');
		$style .= 'li.portfolio-item div.portfolio-thumb-description-wrap {background-color:' . $bk_portfolio_thumb_description_background_color . ';}' . "\n";
		$style .= 'li.portfolio-item div.portfolio-thumb-description-wrap:after {border-bottom-color:' . $bk_portfolio_thumb_description_background_color . ';}' . "\n";


		/* Post Title */
		$bk_post_title_color = get_option('bk_post_title_color');
		$style .= 'h3.post-entry-title a, span.widget-post-title a {color:' . $bk_post_title_color . ' ;}' . "\n";

		/* Post Title Hover*/
		$bk_post_title_hover_color = get_option('bk_post_title_hover_color');
		$style .= 'h3.post-entry-title a:hover, span.widget-post-title a:hover {color:' . $bk_post_title_hover_color . ' ;}' . "\n";

		/* Quote Post Background */
		$bk_quote_post_format_background = get_option('bk_quote_post_format_background');
		$style .= 'div.bk-quote-post-format-featured-inner-wrap {background-color:' . $bk_quote_post_format_background . ' ;}' . "\n";

		/* Quote Post Text */
		$bk_quote_post_format_text = get_option('bk_quote_post_format_text');
		$style .= 'div.bk-quote-post-format-featured-inner-wrap {color:' . $bk_quote_post_format_text . ' ;}' . "\n";

		/* Chat Post Background */
		$bk_chat_post_format_background = get_option('bk_chat_post_format_background');
		$style .= 'div.bk-chat-post-format-featured-inner-wrap {background-color:' . $bk_chat_post_format_background . ' ;}' . "\n";

		/* Chat Post Text */
		$bk_chat_post_format_text = get_option('bk_chat_post_format_text');
		$style .= 'div.bk-chat-post-format-featured-inner-wrap {color:' . $bk_chat_post_format_text . ' ;}' . "\n";

		/* Link Post Format Background */
		$bk_link_post_format_background = get_option('bk_link_post_format_background');
		$style .= 'div.bk-link-post-format-featured-inner-wrap {background-color:' . $bk_link_post_format_background . ' ;}' . "\n";

		/* Link Post Format Text */
		$bk_link_post_format_text = get_option('bk_link_post_format_text');
		$style .= 'div.bk-link-post-format-featured-inner-wrap a{color:' . $bk_link_post_format_text . ' ;}' . "\n";


		/* Link Post Format Text Hover */
		$bk_link_post_format_text_hover = get_option('bk_link_post_format_text_hover');
		$style .= 'div.bk-link-post-format-featured-inner-wrap a:hover{color:' . $bk_link_post_format_text_hover . ' ;}' . "\n";

		/* Tweet Post Format Background */
		$bk_tweet_post_format_background = get_option('bk_tweet_post_format_background');
		$style .= 'div.bk-status-post-format-featured-inner-wrap {background-color:' . $bk_tweet_post_format_background . ' ;}' . "\n";

		/* Tweet Post Format Text */
		$bk_tweet_post_format_text = get_option('bk_tweet_post_format_text');
		$style .= 'div.bk-status-post-format-featured-inner-wrap {color:' . $bk_tweet_post_format_text . ' ;}' . "\n";

		/* Tweet Post Format Link */
		$bk_tweet_post_format_link = get_option('bk_tweet_post_format_link');
		$style .= 'div.bk-status-post-format-featured-inner-wrap a{color:' . $bk_tweet_post_format_link . ' ;}' . "\n";

		/* Tweet Post Format Link Hover */
		$bk_tweet_post_format_link_hover = get_option('bk_tweet_post_format_link_hover');
		$style .= 'div.bk-status-post-format-featured-inner-wrap a:hover{color:' . $bk_tweet_post_format_link_hover . ' ;}' . "\n";

		/* chat background */
		$bk_chat_post_format_background = get_option('bk_chat_post_format_background');
		$style .= 'div.bk-chat-post-format-featured-inner-wrap {background-color:' . $bk_chat_post_format_background . ' ;}' . "\n";

		/* chat text */
		$bk_chat_post_format_text = get_option('bk_chat_post_format_text');
		$style .= 'div.bk-chat-post-format-featured-inner-wrap {color:' . $bk_chat_post_format_text . ' ;}' . "\n";

		/*
		 * blog thumb
		*/

		$bk_blog_thumb_description_background_color = get_option('bk_blog_thumb_description_background_color');
		$style .= 'article.bk-post-summary {background-color:' . $bk_blog_thumb_description_background_color . ';}' . "\n";
		$style .= 'article.bk-post-summary.format-image div.bk-blog-content-wrap:after, article.bk-post-summary.format-standard div.bk-blog-content-wrap:after, article.bk-post-summary.format-quote div.bk-blog-content-wrap:after, article.bk-post-summary.format-link div.bk-blog-content-wrap:after, article.bk-post-summary.format-chat div.bk-blog-content-wrap:after, article.bk-post-summary.format-status div.bk-blog-content-wrap:after {border-bottom-color:' . $bk_blog_thumb_description_background_color . ';}' . "\n";


		/*
		 * Shortcodes
	 */

		/* Body button background color */
		$bk_button_background = get_option('bk_button_background');
		$style .= 'a.bk_button, div#respond p.form-submit input[type=submit], div.bk-protected-form-wrap input[type=submit], a.comment-reply-link, span.bk-cancel-reply-link, a.comment-edit-link, li.widget .tagcloud a, input[type=submit].wpcf7-submit {color:' . $bk_button_background . '; border-color: ' . $bk_button_background . ';}' . "\n";

		/* Body button background hover color */
		$bk_button_hover_background = get_option('bk_button_hover_background');
		$style .= 'a.bk_button:hover, div#respond p.form-submit input[type=submit]:hover, div.bk-protected-form-wrap input[type=submit]:hover, a.comment-reply-link:hover, span.bk-cancel-reply-link:hover, a.comment-edit-link:hover, li.widget .tagcloud a:hover, input[type=submit].wpcf7-submit:hover {color:' . $bk_button_hover_background . '; border-color: ' . $bk_button_hover_background . ';}' . "\n";

		/* highlight background color */
		$bk_highlight_background = get_option('bk_highlight_background');
		$style .= 'em.bk_highlight {background-color: ' . $bk_highlight_background . ';}' . "\n";

		/* highlight text color */
		$bk_highlight_text_color = get_option('bk_highlight_text_color');
		$style .= 'em.bk_highlight {color:' . $bk_highlight_text_color . ';}' . "\n";

		/* highlight background color */
		$bk_progressbar_background = get_option('bk_progressbar_background');
		$style .= 'div.bk-progress-bar-base {background-color: ' . $bk_progressbar_background . ';}' . "\n";

		/* highlight text color */
		$bk_progressbar_color = get_option('bk_progressbar_color');
		$style .= 'div.bk-progress-bar {background-color:' . $bk_progressbar_color . ';}' . "\n";

		/*
		 Price Table
		*/
		$bk_price_table_background = get_option('bk_price_table_background');
		$style .= 'div.bk-pricing-table-column-wrap {background-color:' . $bk_price_table_background . ' ;}' . "\n";

		$bk_price_table_border = get_option('bk_price_table_border');
		$style .= 'div.bk-pricing-table-column-wrap {border-color:' . $bk_price_table_border . ' ;}' . "\n";

		$bk_price_table_alternate_color = get_option('bk_price_table_alternate_color');
		$style .= 'div.bk-pricing-table-feature.bk_alternate {background-color:' . $bk_price_table_alternate_color . ' ;}' . "\n";

		/*
		 * Toggles header
		*/

		$bk_toggle_header_color = get_option('bk_toggle_header_color');
		$style .= 'div.bk-tabs ul.ui-tabs-nav li a, div.bk-toggle-header, div.bk-toggle.bk-toggle-closed div.bk-toggle-header {color:' . $bk_toggle_header_color . ' ;}' . "\n";

		$bk_toggle_header_hover_color = get_option('bk_toggle_header_hover_color');
		$style .= 'div.bk-tabs ul.ui-tabs-nav li.ui-tabs-selected a, div.bk-tabs ul.ui-tabs-nav li.ui-tabs-active a, div.bk-tabs ul.ui-tabs-nav li:hover a, div.bk-toggle-header:hover, div.bk-toggle-header.ui-state-active, div.bk-toggle div.bk-toggle-header {color:' . $bk_toggle_header_hover_color . ' ;}' . "\n";

		/*
		 * On Iphone & Ipad remove volume bar from jplayer 
		 */
		
		if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') ) {
			$style .= '.jp-volume-bar-wrapper, .jp-mute, .jp-unmute, .jp-divider.right-side { display: none !important; }' . "\n";
			$style .= 'div.jp-progress-wrapper { margin-right: 12px !important; }' . "\n";
		}
			
		$style .= '</style>';

		// print links to all google fonts before printing whole style
		foreach( $google_fonts as $google_font ) {
			echo '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ' ,'+' , $google_font) . '&subset=latin-ext" rel="stylesheet" type="text/css">' . "\n";  // add ,700 -> bold font face to body to make work some hardcoded bold strings that can be ovveride individually via next font settings
		}
		echo $style;
	}
}
add_action( 'wp_head' , 'bk_print_header_inline_style', 99 );  // make sure its printed before theme customizer style hooks


/*
 * Print custom javascript
 */
if ( !function_exists( 'bk_print_custom_javascript' ) ) {
  function bk_print_custom_javascript() {
   $custom_javascript = get_option("bk_custom_javascript");
   if( !empty($custom_javascript) ) {
		  echo '<!-- theme custom javascript -->';
		  echo '<script type="text/javascript">';
		  echo stripslashes($custom_javascript); 
		  echo '</script>';
	  }
  }
}

add_action( 'wp_head' , 'bk_print_custom_javascript', 99 );

/*
 * Print custom css
 */
if ( !function_exists( 'bk_print_custom_css' ) ) {
	function bk_print_custom_css() {
		$custom_css = get_option("bk_custom_stylesheet");
		if( !empty($custom_css) ) {
			echo '<!-- theme custom css -->';
			echo '<style type="text/css">';
			echo stripslashes($custom_css);
			echo '</style>';
		}
	}
}
add_action( 'wp_head' , 'bk_print_custom_css', 99 );  
	
/*
 * convert hex with opacity to rgba
*
*/
if ( !function_exists( 'bk_hex2rgba' ) ) {
	function bk_hex2rgba($hex, $opacity) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b, $opacity);
		return 'rgba(' . implode(",", $rgb) . ')'; // returns the rgba values separated by commas
	}
}
/*----------------------------------------------------------------------------------
 Get retina background image markup
to make this work background_image have to be fullsize version so bk_get_iamge_id can work
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_retina_background_css_markup' ) ) {
	function bk_get_retina_background_css_markup( $selector, $background_image ) {
		$image = wp_get_attachment_image_src( bk_get_image_id($background_image), 'fullsize' );
		$style = '';
		if( $image ) {
			// calculate width and height of retina background image
			$width = floor($image[1] / 2) . 'px';
			$height = floor($image[2] / 2) . 'px';

			$style = '@media';
			$style .= '(min--moz-device-pixel-ratio: 2),';
			$style .= '(-o-min-device-pixel-ratio: 2/1),';
			$style .= '(-webkit-min-device-pixel-ratio: 2),';
			$style .= '(min-device-pixel-ratio: 2) {';
			$style .= $selector . '{';
			$style .= 'background-image: url("' . $background_image . '");';
			$style .= 'background-size: ' . $width . ' ' . $height . ';';
			$style .= '}';
			$style .= '}';
		}
		return $style;
	}
}
/*----------------------------------------------------------------------------------
 Get style string based on settings from general css, theme admin and post / page specific style
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_style' ) ) {
	function bk_get_style( $background_image = null, $background_position = null, $background_repeat = null, $background_color = null, $background_attachement = 'scroll' ) {

		$style = null;
		if( !empty($background_image) && $background_image ) {
			$style .= 'background-image: url(' . $background_image . '); ';
		}
		if( $background_position ) {
			$style .= 'background-position:' . $background_position . ';';
		}
		if( $background_repeat ) {
			$style .= 'background-repeat:' . $background_repeat . ';';
		}
		if( $background_color ) {
			$style .= 'background-color:' . $background_color . ';';
		}
		if( $background_attachement ) {
			$style .= 'background-attachment:' . $background_attachement . ';';
		}
		return '{' . $style . '}';
	}
}

/*----------------------------------------------------------------------------------
 helper function - google font family to style as input it takes string parameter
e.g 'PT Sans:400italic' and returns string in following format
'font-family: "PT Sans"; font-style: italic; font-weight: 400;'
----------------------------------------------------------------------------------*/
if ( !function_exists( 'google_font_family_to_style' ) ) {
	function google_font_family_to_style( $font = '', $fallback = false ) {
		$font_family = substr($font, 0, strpos($font, ":"));
		$font_weight = substr($font, strpos($font, ":") + 1);

		$fallback_font = '';
		if( $fallback ) {
			$fallback_font = ',Helvetica,Arial,Garuda,sans-serif';
		}

		$css_markup = 'font-family:"' . $font_family . '"' . $fallback_font . '; ';
		if( strpos($font_weight, "italic") !== false ) {
			$css_markup .= 'font-style:italic; ';
			$font_weight = substr($font_weight, 0, strpos($font_weight, "italic"));
		}

		if( empty($font_weight) || $font_weight == 'regular' )
			$font_weight = '400';

		$css_markup .= 'font-weight:' . $font_weight . '; ';
		return $css_markup;
	}
}
/*----------------------------------------------------------------------------------
 use filter to add body class for boxed layout
----------------------------------------------------------------------------------*/
add_filter('body_class','bk_class_names');
if ( !function_exists( 'bk_class_names' ) ) {
	function bk_class_names($classes) {
		$bk_collapsible_header = get_option("bk_collapsible_header");
		$header_collapsed_onload = get_option("bk_header_collapsed_onload");
		$classes[] = get_option("bk_header_layout");
		global $post;

		if( ( is_page() || is_single() ) ) {
			if( get_post_meta( $post->ID, '_bk_override_header_settings', true ) == true ) {
				$bk_collapsible_header = get_post_meta( $post->ID, '_bk_collapsible_header', true );
				$header_collapsed_onload = get_post_meta( $post->ID, '_bk_header_collapsed_onload', true );
			}
			
			if( post_password_required( $post->ID ) ) {
				$classes[] = 'bk-post-password-required';
			}
		}
		
		

		$classes[] = get_post_meta( $post->ID, '_bk_page_fullscreen_background_content', true );

		if( $bk_collapsible_header ) {
			$classes[] = 'bk-collapsible-header';
		}

		if( $header_collapsed_onload ) {
			$classes[] = 'bk-collapsed-header-onload';
		}

		return $classes;
	}
}
/*----------------------------------------------------------------------------------
 Show social icons and search form in header
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_header_socials_icons' ) ) {
	function bk_get_header_socials_icons() {
		$hide_on_mobile_class = "";
		if( get_option('bk_hide_search_on_mobile') ) {
			$hide_on_mobile_class = "hide_on_mobile";
		}

		echo '<div id="bk-header-social-wrapper">';
		echo '<ul class="bk-social-icons">';
		$socials = get_option('bk_social_icons');

		if( is_array($socials) ) {
			foreach( $socials as $social_item ) {
				$icon_class = $social_item["bk-social-item-icon"];
				$icon_text = $social_item["bk-social-item-text"];
				$icon_link = $social_item["bk-social-item-link"];
				$icon_link_target = $social_item["bk-social-item-link-target"];
				echo '<li data-label="' . $icon_text . '"><a class="bk-social-icon ' . $icon_class . '" title="' . $icon_text . '" target="' . $icon_link_target . '" href="' . $icon_link . '"></a></li>';
			}
		}

		echo '</ul>	';
		echo '</div>';
	}
}
/*----------------------------------------------------------------------------------

----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_print_regular_featured_caption' ) ) {
	function bk_print_regular_featured_caption() {  // based on page, post name, archives search tags etc...
		//echo '<h1 class="featured-caption">'; // START of .featured-caption .cufon_font

		$content = '';

		$delimiter = ' / '; // delimiter between crumbs
		$showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$home = 'Home'; // text for the 'Home' link
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show

		$start_quote = '<span class="bk-themedefault-highlight">'; // tag before the current crumb
		$end_quote = '</span>'; // tag after the current crumb

		global $post;
		$homeLink = home_url();

		if( is_category() ) {
			global $wp_query;
			$category_obj = $wp_query->get_queried_object();
			$this_category = $category_obj->term_id;
			$this_category = get_category($this_category);
			$parent_category = get_category($this_category->parent);
			echo __('Archive for category', 'corpora_theme') . ' ' . $start_quote . single_cat_title('', false) . $end_quote;

			if ($this_category->parent != 0)
				echo(get_category_parents($parent_category, TRUE, ' ' . $delimiter . ' '));
		} elseif ( is_tax('filter') || is_tax('portfolio_skills') ) {
			if( get_query_var('portfolio_skills') != '' ) {
				$terms = get_terms('portfolio_skills', array( 'slug' => get_query_var('portfolio_skills') ) );
				if( count($terms) ) {
					echo __('Portfolios tagged', 'corpora_theme') . ' ' . $start_quote . $terms[0]->name . $end_quote;
				} else {
					echo __('Portfolio', 'corpora_theme');
				}
			} elseif( get_query_var('filter') != '' ) {
				$terms = get_terms('filter', array( 'slug' => get_query_var('filter') ) );
				if( count($terms) ) {
					echo __('Portfolios for category', 'corpora_theme') . ' ' . $start_quote . $terms[0]->name . $end_quote;
				} else {
					echo __('Portfolio', 'corpora_theme');
				}
			}
		} elseif ( is_day() ) {															// TO DO!!! make something with those dates
			echo __('Archive for', 'corpora_theme') . ' ' . $start_quote;
			echo get_the_time('Y') . $delimiter . ' ';
			echo get_the_time('F') . $delimiter . ' ';
			echo get_the_time('d') . $end_quote;
				
		} elseif ( is_month() ) {
			echo __('Archive for', 'corpora_theme') . ' ' . $start_quote;
			echo get_the_time('Y') . $delimiter . ' ';
			echo get_the_time('F') . $end_quote;
				
		} elseif ( is_year() ) {
			echo __('Archive for', 'corpora_theme') . ' '  . $start_quote;
			echo get_the_time('Y') . $end_quote;
		} elseif ( is_search() ) {
			echo __('Search results for', 'corpora_theme') . ' ' . $start_quote . get_search_query() . $end_quote;

		} elseif ( is_single() && !is_attachment() ) {
			if ( get_post_type() == 'post' ) {
				if ($showCurrent == 1) echo bk_get_custom_text( get_the_title(), 10 );
			} else {  // TO DO handle portfolio post type
				if ($showCurrent == 1) echo bk_get_custom_text( get_the_title(), 10 );
			}
				
		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			echo $post_type->labels->singular_name;
				
		} elseif ( is_attachment() ) {
			$parent = get_post($post->post_parent);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
			if ($showCurrent == 1) echo bk_get_custom_text( get_the_title(), 10 );
				
		} elseif ( is_page() ) { // add description handling form custom fields or the same as in menu
			if ($showCurrent == 1) echo bk_get_custom_text( get_the_title(), 10 );

		} elseif ( is_tag() ) {
			echo __('Posts tagged', 'corpora_theme') . ' ' . $start_quote . single_tag_title('', false) . $end_quote;
				
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata($author);
			echo __('Articles posted by', 'corpora_theme') . ' ' . $start_quote . $userdata->display_name . $end_quote;
				
		} elseif ( is_404() ) {
			echo __('Not Found / Error 404', 'corpora_theme');
		}  elseif ( get_post_format() ) {
			echo __('Posts with format', 'corpora_theme') . ' ' . $start_quote .  get_post_format() . $end_quote;
		}

		//echo '</h1>'; // END of .featured-caption .cufon_font
	}
}
/*----------------------------------------------------------------------------------
 Get main container class - used to specify width and position
of main container area based on page layout settings
and print out helper css to position and style main container.
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_main_container_classes' ) ) {
	function bk_get_main_container_classes( $post_id ) {
		$main_container_classes = array();

		$page_layout = "fullwidth";
		$fullwidth_page_padding = 0;
		$fixed_page_max_width = 1024;

		if( bk_is_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
			$page_layout = get_option("bk_shop_archives_page_layout", "fullwidth");
			$fullwidth_page_padding = 0;
			$fixed_page_max_width = get_option("bk_shop_archives_fixed_page_width", 1024);
		} else if( is_page() || is_single() ) {
			$page_layout = get_post_meta( $post_id, '_bk_page_layout', true );
			$fullwidth_page_padding = get_post_meta( $post_id, '_bk_fullwidht_page_padding', true );
			$fixed_page_max_width = get_post_meta( $post_id, '_bk_fixed_page_width', true );
		} else if(is_search() || is_category() || is_day() || is_month() || is_year() || is_tag() || is_author() || is_tax('post_format') ) {
			$page_layout = get_option("bk_blog_archives_page_layout", "fullwidth");
			$fullwidth_page_padding = get_option("bk_blog_archives_fullwidht_page_padding", 0);
			$fixed_page_max_width = get_option("bk_blog_archives_fixed_page_width", 1024);
		} else if( is_tax('filter') || is_tax('portfolio_skills') ) {
			$page_layout = get_option("bk_portfolio_archives_page_layout", "fullwidth");
			$fullwidth_page_padding = get_option("bk_portfolio_archives_fullwidht_page_padding", 0);
			$fixed_page_max_width = get_option("bk_portfolio_archives_fixed_page_width", 1024);
		} else if( is_404() ) {
			if( get_option("bk_404_page_id") != -1 ) {
			  $page_layout = get_post_meta( get_option("bk_404_page_id"), '_bk_page_layout', true );
			  $fullwidth_page_padding = get_post_meta( get_option("bk_404_page_id"), '_bk_fullwidht_page_padding', true );
			} else {
			  $page_layout = "fullwidth";
			  $fullwidth_page_padding = 30;
			}
		}

		if( $page_layout == "fullwidth" ) {
			echo '<style>div.bk-page-content-wrap, div#bk-content-wrap{padding:' . $fullwidth_page_padding . 'px; padding-bottom: 0px;}</style>';
		} else {
			echo '<style>';
			echo '#bk-content-inner-wrap{max-width:' . $fixed_page_max_width . 'px;}' . "\n";
			
			$viewport_max_width = $fixed_page_max_width + 60; // in case of classic or fixed top header max width plus margins
			$left_header_viewport_max_width = $fixed_page_max_width + 60 + 280; // in case of fixed left header add header width as well
			
			echo '@media only screen and (max-width: ' . $viewport_max_width . 'px) {#bk-content-inner-wrap{margin:0px !important; max-width: none;}}' . "\n";
			echo '@media only screen and (max-width: ' . $left_header_viewport_max_width . 'px) {.bk-fixed-header-left #bk-content-inner-wrap{margin:0px !important; max-width: none;}}' . "\n";
			
		    echo '</style>';
		}

		switch( $page_layout ) {

			default:
			case "fullwidth":
				$main_container_classes["bk-content-inner-wrap"] = "bk-fullwidth-page ";
				break;

			case "fixed_centered":
				$main_container_classes["bk-content-inner-wrap"] = "bk-fixed-page bk-centered ";
				break;

			case "fixed_float_right":
				$main_container_classes["bk-content-inner-wrap"] = "bk-fixed-page bk-float-right ";
				break;

			case "fixed_float_left":
				$main_container_classes["bk-content-inner-wrap"] = "bk-fixed-page bk-float-left ";
				break;
		}

		$main_container_classes["bk-content-wrap"] = "span12";

		return $main_container_classes;
	}
}
/*----------------------------------------------------------------------------------
 Get page, post, portfolio entry content
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_the_content' ) ) {
	function bk_get_the_content( $post_id ) {
		$content = '';

		if( get_post_meta($post_id, '_bk_page_builder_enabled', true) == true ) {
			$content .= Bk_Page_Builder::getInstance()->bk_get_page_builder_content($post_id);
		} else {
			$content = apply_filters('the_content', get_post_field('post_content', $post_id));
			$content = str_replace(']]>', ']]&gt;', $content);
		}

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Helper function gets given size image link form fullsize image link@
@params	$image_size (string) - desired image size e.g thumbnail, medium etc
$full_size_image_url - link to full size uploaded image!!!

@returns ( string ) - link to image desired image size -
or fullsize image - hopefully not - sky will fall!!!
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_image_link' ) ) {
	function bk_get_image_link( $image_size, $full_size_image_url ) {
		$img_id = bk_get_image_id($full_size_image_url);
		$image = wp_get_attachment_image_src( $img_id, $image_size );
		if( $image ) {		// if image was found
			return $image[0];	// return url
		}

		return $full_size_image_url;
	}
}
/*----------------------------------------------------------------------------------
 Get single portfolio entry featured content - image, gallery, video, audio etc...
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_portfolio_entry_featured_content' ) ) {
	function bk_get_portfolio_entry_featured_content( $post_id ) {
		$content = '';

		$format = get_post_format($post_id);
		$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'fullsize', false, '' );
		$large_image = $large_image[0];

		$link = $large_image;

		if( false == $format ) { // standard portfolio format - featured image displayed as featured content
			$image_size = 'fullsize';
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $image_size );

			if( get_post_meta( $post_id, '_bk_portfolio_single_featured_image_popup_enabled', true ) == true )
				$content .= '<a data-rel="prettyPhoto[gallery' . $post_id . ']" href="' . $link . '">';

			$content .= '<img class="wp-post-image" src="' . $image[0] . '">';

			if( get_post_meta( $post_id, '_bk_portfolio_single_featured_image_popup_enabled', true ) == true )
				$content .= '</a>';
		} else if( ('gallery' == $format) && ('slider' == get_post_meta( $post_id, '_bk_portfolio_gallery_type', true ))  ) {

			$nav_type = get_post_meta( $post_id, '_bk_portfolio_slider_nav_type', true );
			$auto_play = get_post_meta( $post_id, '_bk_portfolio_slider_autoplay_enabled', true );
			$smooth_height_enabled = 1;
			$is_inside_blog_grid = false;
			$gallery_id = '_bk_portfolio_gallery';

			$content .= bk_get_post_featured_slider( $post_id, $gallery_id, $auto_play, $smooth_height_enabled, $is_inside_blog_grid );

		}  else if( ('gallery' == $format) && ('gallery' == get_post_meta( $post_id, '_bk_portfolio_gallery_type', true ))  ) {

			$content .= bk_get_single_portfolio_item_gallery( $post_id );

		}  else if( ('gallery' == $format) && ('stacked_gallery' == get_post_meta( $post_id, '_bk_portfolio_gallery_type', true ))  ) {

			$content .= bk_get_single_portfolio_item_stacked_gallery( $post_id );

		}   else if( 'video' == $format )	{

			$video_link = '';
			$width = get_post_meta( $post_id, '_bk_portfolio_video_width', true );
			$height = get_post_meta( $post_id, '_bk_portfolio_video_height', true );
			$video_type = get_post_meta( $post_id, '_bk_portfolio_video_type', true );
			switch( $video_type ) {
				case 'vimeo_link':
					$video_link = get_post_meta( $post_id, '_bk_portfolio_vimeo_link', true );

					if( preg_match("/\b(?:vimeo)\.com\b/i", $video_link) ) {
						$content .= bk_get_vimeo_markup_from_link( $video_link, $width, $height );
					}
					break;

				case 'youtube_link':
					$video_link = get_post_meta( $post_id, '_bk_portfolio_youtube_link', true );

					if( preg_match("/\b(?:youtube)\.com\b/i", $video_link) ) {
						$content .= bk_get_youtube_markup_from_link( $video_link, $width, $height);
					}
					break;

				case 'self_hosted':
					$video_link = array( get_post_meta( $post_id, '_bk_portfolio_self_hosted_m4v_link', true ),
					get_post_meta( $post_id, '_bk_portfolio_self_hosted_ogv_link', true ) );
						
					if(  preg_match("/\b\.m4v\b/i", $video_link[0] ) ||
							preg_match("/\b\.mp4\b/i", $video_link[0] ) ||
							preg_match("/\b\.ogv\b/i", $video_link[1] ) ||
							preg_match("/\b\.webm\b/i", $video_link[1] ) ) {
						$poster_src = get_post_meta( $post_id, '_bk_portfolio_self_hosted_video_poster', true );
						$content .= bk_get_self_hosted_video_markup_from_link( $video_link, $poster_src, $post_id, $width, $height );
					}
					break;
			}
		} else if ( ('audio' == $format) && ('selfhosted_audio' == get_post_meta( $post_id, '_bk_portfolio_audio_type', true )) ) {
			$audio_link = get_post_meta( $post_id, '_bk_portfolio_audio_link', true );
			$audio_poster = get_post_meta( $post_id, '_bk_portfolio_audio_poster', true );
				
			if( !empty($audio_poster) ) {
				$content .= '<img class="wp-post-image" src="' . $audio_poster . '">';
			}
				
			$content .= bk_get_audio_markup_from_link( $audio_link, $post_id );
		} else if( ($format == 'audio') && ('soundcloud_audio' == get_post_meta( $post_id, '_bk_portfolio_audio_type', true )) ) {
			$url = get_post_meta( $post_id, '_bk_portfolio_audio_soundcloud_link', true );
			$comments_enabled = get_post_meta( $post_id, '_bk_portfolio_audio_soundcloud_comments_enabled', true );
			$show_artwork = get_post_meta( $post_id, '_bk_portfolio_audio_soundcloud_show_artwork', true );
			$auto_play = get_post_meta( $post_id, '_bk_portfolio_audio_soundcloud_auto_play', true );
			$color = get_post_meta( $post_id, '_bk_portfolio_audio_soundcloud_color', true );

			$content .= bk_get_soundcloud_audio_markup_from_link( $url, $auto_play, $comments_enabled, $show_artwork, $color );
		}

		return $content;
	}
}

/*
 * Get post or portfolio featured slider
*
* @params (integer) - $post_id - id of post for which gallery will be printed out
* 					  $gallery_id - id of gallery for which items will be printed out
* 					  $auto_play - falg that indicates if autoplay should be enabled
* 					  $smooth_height_enabled - if smooth height enabled - for portfolio always, for posts in blog grid can be disbaled cropped images will be used
* 					  $is_inside_blog_grid - if inside blog grid then smaller images will be used - otherwise fullwidth images will be used.
*
* @return (string) - $content - html markup of gallery
*/
if ( !function_exists( 'bk_get_post_featured_slider' ) ) {
	function bk_get_post_featured_slider( $post_id, $gallery_id, $auto_play, $smooth_height_enabled, $is_inside_blog_grid ) {
		$content = '';

		$gallery_items = Bk_Post_Featured_Gallery_Manager::getInstance()->get_gallery_items($post_id,  $gallery_id, 'popup-gallery-thumb');

		if( empty($gallery_items) ) {
			return $content;
		}

		$content .= '<div class="flexslider" data-autoplay="' . $auto_play . '">';
		$content .= '<ul class="slides">';

		foreach( $gallery_items as $item ) {
			switch( $item['item_type'] ) {
				case 'bk_gallery_image':
					$image_url = $item['image_url'];
					if( $is_inside_blog_grid ) {
						if( $smooth_height_enabled ) {
							$image_url = Bk_Post_Featured_Gallery_Manager::getInstance()->get_other_size_image_url($image_url, 'blog-thumb-prop');
						} else {
							$image_url = Bk_Post_Featured_Gallery_Manager::getInstance()->get_other_size_image_url($image_url, 'blog-thumb-cropped');
						}
					}


					$content .= '<li>';

					$content .= '<div class="bk-img-wrap">';
					$content .= '<img class="wp-post-image bk-stacked-gallery-image" src="' . $image_url . '">';

					$content .= bk_get_image_link_wrap( $item, $post_id );

					$content .= '<div class="bk-img-border"></div>';
					$content .= '</div>';
					$content .= '</li>';

					break;

				case 'bk_gallery_video':
					$content .= '<li>';

					if( preg_match("/\b(?:vimeo)\.com\b/i", $item['video_link']) ) {
						$content .= bk_get_vimeo_markup_from_link( $item['video_link'], $item['video_width'], $item['video_height'] );
					} else if( preg_match("/\b(?:youtube)\.com\b/i", $item['video_link']) ) {
						$content .= bk_get_youtube_markup_from_link( $item['video_link'], $item['video_width'], $item['video_height'] );
					}

					$content .= '</li>';
					break;

				case 'bk_gallery_selfhosted_video':
					$video_link = array( $item['primary_video_link'], $item['secondary_video_link'] );
						
					if(  preg_match("/\b\.m4v\b/i", $video_link[0] ) ||
							preg_match("/\b\.mp4\b/i", $video_link[0] ) ||
							preg_match("/\b\.ogv\b/i", $video_link[1] ) ||
							preg_match("/\b\.webm\b/i", $video_link[1] ) ) {

						$content .= '<li>';

						$content .= bk_get_self_hosted_video_markup_from_link( $video_link, $item['video_poster'], $post_id, $item['video_width'], $item['video_height'] );

						$content .= '</li>';
					}

					break;
			}

		}

		$content .= '</ul>';
		$content .= '</div>';


		return $content;
	}
}

/*
 * Helper function - get image link wrap used in single portfolio gallery and slider
*
* @params - (array) - $item - description of whole gallery item - it will be image with possible links to fullsize image, another image, vidoes etc...
*
* @retunrs - (string) - $content - HTML markup of link that with proper jackbox values such as link, description, thumb
*/
if ( !function_exists( 'bk_get_image_link_wrap' ) ) {
	function bk_get_image_link_wrap( $item, $post_id ) {
		$content = '';

		switch ( $item['link_type'] ) {
			case "fullsize_image":
				$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['image_url'] . '" data-thumbnail="' . $item['thumb_url'] . '"></a>';
				break;

			case "other_image":
				$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['other_image_link'] . '" data-thumbnail="' . $item['thumb_url'] . '"></a>';
				break;

			case "video":
				$video_thumb_url = $item['video_thumbnail'];
				if( !empty($video_thumb_url) ) {
					$video_thumb_url = Bk_Featured_Gallery_Manager::getInstance()->get_other_size_image_url($video_thumb_url, 'popup-gallery-thumb');
				}
				$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['video_link'] . '" data-thumbnail="' . $video_thumb_url . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-autoplay="' . $item['video_autoplay'] . '"></a>';
				break;

			case "selfhosted_video":
				$video_thumb_url = $item['video_thumbnail'];
				if( !empty($video_thumb_url) ) {
					$video_thumb_url = Bk_Featured_Gallery_Manager::getInstance()->get_other_size_image_url($video_thumb_url, 'popup-gallery-thumb');
				}
				$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['selfhosted_video_link'] . '" data-thumbnail="' . $video_thumb_url . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-autoplay="' . $item['video_autoplay'] . '"  data-poster="' . $item['video_poster'] . '"></a>';
				break;
				
			case "external_link":
				$content .= '<a class="bk-portfolio-thumb-link" href="' . $item['external_link'] . '"></a>';
			break;
		}

		return $content;
	}
}

/*
 * Get single portfolio item stacked gallery
*
*/
if ( !function_exists( 'bk_get_single_portfolio_item_stacked_gallery' ) ) {
	function bk_get_single_portfolio_item_stacked_gallery( $post_id ) {
		$content = '';

		$gallery_items = Bk_Featured_Gallery_Manager::getInstance()->get_gallery_items($post_id,  '_bk_portfolio_gallery', 'popup-gallery-thumb');

		if( empty($gallery_items) ) {
			return $content;
		}

		$image_effect = get_post_meta( $post_id, '_bk_portfolio_gallery_image_effect', true );
		$image_overlay = get_post_meta( $post_id, '_bk_portfolio_gallery_image_overlay', true );

		$content .= '<ul class="bk-stacked-gallery">';

		foreach( $gallery_items as $item ) {
			$thumbnail_size = 'fullsize';  // default equal grid style

			switch( $item['item_type'] ) {
				case 'bk_gallery_image':
					$content .= '<li class="bk-stacked-gallery-item ' . $thumbnail_size . '">';
					$content .= '<div class="bk-img-wrap ' . $image_effect . '">';

					$image_src = Bk_Featured_Gallery_Manager::getInstance()->get_other_size_image_url($item['image_url'], $thumbnail_size);

					$content .= '<img class="bk-portfolio-thumb" src="' . $image_src . '">';

					$content .= bk_get_image_link_wrap( $item, $post_id );

					$content .= '<div class="bk-img-border"></div>';

					if( $image_overlay != 'none' ) {
						$content .= '<div class="bk-img-overlay ' . $image_overlay . '">';
						$content .= '<div class="bk-img-overlay-outer">';
						$content .= '<div class="bk-img-overlay-inner">';

						$content .= '</div>'; // end of .bk-img-overlay-inner
						$content .= '</div>'; // end of .bk-img-overlay-outer
						$content .= '</div>'; // end of .bk-img-overlay
					}

					$content .= '</div>';
					$content .= '</li>';

					break;

				case 'bk_gallery_video':
					$content .= '<li class="bk-stacked-gallery-item ' . $thumbnail_size . '">';
					$content .= '<div class="bk-img-wrap">';

					if( preg_match("/\b(?:vimeo)\.com\b/i", $item['video_link']) ) {
						$content .= bk_get_vimeo_markup_from_link( $item['video_link'], $item['video_width'], $item['video_height'] );
					} else if( preg_match("/\b(?:youtube)\.com\b/i", $item['video_link']) ) {
						$content .= bk_get_youtube_markup_from_link( $item['video_link'], $item['video_width'], $item['video_height'] );
					}

					$content .= '</div>';
					$content .= '</li>';
					break;

				case 'bk_gallery_selfhosted_video':
					$video_link = array( $item['primary_video_link'], $item['secondary_video_link'] );
						
					if(  preg_match("/\b\.m4v\b/i", $video_link[0] ) ||
							preg_match("/\b\.mp4\b/i", $video_link[0] ) ||
							preg_match("/\b\.ogv\b/i", $video_link[1] ) ||
							preg_match("/\b\.webm\b/i", $video_link[1] ) ) {

						$content .= '<li class="bk-stacked-gallery-item ' . $thumbnail_size . '">';
						$content .= '<div class="bk-img-wrap">';

						$content .= bk_get_self_hosted_video_markup_from_link( $video_link, $item['video_poster'], $post_id, $item['video_width'], $item['video_height'] );

						$content .= '</div>';
						$content .= '</li>';
					}

					break;
			}
		}

		$content .= '</ul>';

		return $content;
	}
}

/*
 * Get single portfolio item gallery
*
* @params (integer) - $post_id - id of post for which gallery will be printed out
*
* @return (string) - $content - html markup of gallery
*/
if ( !function_exists( 'bk_get_single_portfolio_item_gallery' ) ) {
	function bk_get_single_portfolio_item_gallery( $post_id ) {

		$content = '';

		$layout = get_post_meta( $post_id, '_bk_portfolio_gallery_layout', true );
		$base_size = get_post_meta( $post_id, '_bk_portfolio_gallery_base_size', true );
		$gutter = get_post_meta( $post_id, '_bk_portfolio_gallery_gutter', true );
		$image_effect = get_post_meta( $post_id, '_bk_portfolio_gallery_image_effect', true );
		$image_overlay = get_post_meta( $post_id, '_bk_portfolio_gallery_image_overlay', true );

		$portfolio_id = 'bk-portfolio-wrap-' . $post_id;

		$gallery_items = Bk_Featured_Gallery_Manager::getInstance()->get_gallery_items($post_id,  '_bk_portfolio_gallery', 'popup-gallery-thumb');

		if( empty($gallery_items) ) {
			return $content;
		}

		if( $gutter > 0 ) {
			$content .= '<style type="text/css">';
			//$content .= '#' . $quick_gallery_unique_id . ' ul.filterable-grid.metro li.portfolio-item.portfolio-grid-2-2 img.bk-portfolio-thumb { margin-top: -' . round( $gutter / 2 ) . 'px; }';
			//$content .= '#' . $quick_gallery_unique_id . ' ul.filterable-grid.metro li.portfolio-item.portfolio-grid-1-2 img.bk-portfolio-thumb { margin-left: -' . round( $gutter / 2 ) . 'px; }';
			$content .= '#' . $portfolio_id . ' li.portfolio-item { margin-bottom: ' . $gutter . 'px; }';
			//$content .= '#' . $portfolio_id . ' ul.filterable-grid { margin-top: ' . $gutter . 'px; }';
			//$content .= '#' . $portfolio_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
			$content .= '</style>';
		}

		$content .= '<div class="bk-portfolio-wrap bk-single-portfolio-item" data-portfolio_layout="' . $layout . '" id="' . $portfolio_id . '" data-portfolio_base_size="' . $base_size . '" data-portfolio_gutter="' . $gutter . '">';
		$content .= '<ul class="filterable-grid clearfix ' . $layout . '">';

		foreach( $gallery_items as $item ) {
			$thumbnail_size = 'portfolio-grid-1-1';  // default equal grid style

			//var_dump($item);

			if( $layout == "masonry") {
				$thumbnail_size = 'portfolio-masonry-small';
			} else if ( $layout == "mixed_masonry" ) {
				$thumbnail_size = 'portfolio-masonry-' . $item['mixed_masonry_thumbnail_size'];
			} else if ( $layout == "metro" ) {
				$thumbnail_size = 'portfolio-grid-' . $item['thumbnail_size'];
			}

			switch( $item['item_type'] ) {
				case 'bk_gallery_image':
					$content .= '<li class="portfolio-item ' . $thumbnail_size . '">';
					$content .= '<div class="bk-img-wrap ' . $image_effect . '">';

					$image_src = Bk_Featured_Gallery_Manager::getInstance()->get_other_size_image_url($item['image_url'], $thumbnail_size);

					$content .= '<img class="bk-portfolio-thumb" src="' . $image_src . '">';

					$content .= bk_get_image_link_wrap( $item, $post_id );

					$content .= '<div class="bk-img-border"></div>';

					if( $image_overlay != 'none' ) {
						$content .= '<div class="bk-img-overlay ' . $image_overlay . '">';
						$content .= '<div class="bk-img-overlay-outer">';
						$content .= '<div class="bk-img-overlay-inner">';
							
						$content .= '</div>'; // end of .bk-img-overlay-inner
						$content .= '</div>'; // end of .bk-img-overlay-outer
						$content .= '</div>'; // end of .bk-img-overlay
					}

					$content .= '</div>';
					$content .= '</li>';

					break;

				case 'bk_gallery_video':
					$content .= '<li class="portfolio-item ' . $thumbnail_size . '">';
					$content .= '<div class="bk-img-wrap">';

					if( preg_match("/\b(?:vimeo)\.com\b/i", $item['video_link']) ) {
						$content .= bk_get_vimeo_markup_from_link( $item['video_link'], $item['video_width'], $item['video_height'] );
					} else if( preg_match("/\b(?:youtube)\.com\b/i", $item['video_link']) ) {
						$content .= bk_get_youtube_markup_from_link( $item['video_link'], $item['video_width'], $item['video_height'] );
					}

					$content .= '</div>';
					$content .= '</li>';
					break;

				case 'bk_gallery_selfhosted_video':
					$video_link = array( $item['primary_video_link'], $item['secondary_video_link'] );
						
					if(  preg_match("/\b\.m4v\b/i", $video_link[0] ) ||
							preg_match("/\b\.mp4\b/i", $video_link[0] ) ||
							preg_match("/\b\.ogv\b/i", $video_link[1] ) ||
							preg_match("/\b\.webm\b/i", $video_link[1] ) ) {
							
						$content .= '<li class="portfolio-item ' . $thumbnail_size . '">';
						$content .= '<div class="bk-img-wrap">';
							
						$content .= bk_get_self_hosted_video_markup_from_link( $video_link, $item['video_poster'], $post_id, $item['video_width'], $item['video_height'] );
							
						$content .= '</div>';
						$content .= '</li>';
					}

					break;
			}
		}

		$content .= '</ul>';
		$content .= '</div>';

		return $content;
	}
}

/*----------------------------------------------------------------------------------
 helper function get portfolio entry layout classes - used to specify portfolio entry
layout classes ( grid_??, push_?? pull_?? ) for main featured container wrap, and description

@params
$entry_layout (string) - id of portfolio entry layout -
fullwidth_no_meta"
"fullwidth_meta_bottom"
"fullwidth_meta_top"
"fullwidth_meta_left"
"fullwidth_meta_right"
"description_left_no_meta"
"description_right_no_meta"
"description_left_with_meta"
"description_right_with_meta
$container (string) - for which container should classes be returned?
'featured' - featured area where image, vidoes, audio etcc are placed
'content' - wrap for whole content ( excluding featured wrap ) where title, text and meta are placed
'description' - wrap for text, description of portfolio item
'meta' - wrap for meta tags

$description_width - width of description field in case if description is on left or right side
if description is below featured content - in case of fullwidth featured area then this parameter is used to
specify width of meta tags and description e.g @param = 3 => meta tags .span3, description .span9 ( .span12 - .span3 )
it can be

3 - one fourth 25%
4 - one third 33%
5 - 42%
6 - 50%
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_portfolio_entry_layout_classes' ) ) {
	function bk_get_portfolio_entry_layout_classes( $entry_layout, $container, $meta_width )	{
		$layout_classes = '';

		$meta_width = intval($meta_width);
		$description_width = 12 - $meta_width; // featured area width in case of left or right description

		switch( $entry_layout ) {
			case 'fullwidth_no_meta':
			case 'fullwidth_meta_bottom':
			case 'fullwidth_meta_top':
				if( $container == 'featured' ) {
					$layout_classes = '';
				} else if( $container == 'content' ) {
					$layout_classes = '';
				} else if( $container == 'description' ) {
					$layout_classes = '';
				} else if( $container == 'meta' ) {
					$layout_classes = '';
				}
				break;

			case 'fullwidth_meta_left':
				if( $container == 'featured' ) {
					$layout_classes = '';
				} else if( $container == 'content' ) {
					$layout_classes = 'row-fluid';
				} else if( $container == 'description' ) {
					$layout_classes = 'span' . $description_width . ' pull-right';
				} else if( $container == 'meta' ) {
					$layout_classes = 'span' . $meta_width . ' pull-left';
				}
				break;

			case 'fullwidth_meta_right':
				if( $container == 'featured' ) {
					$layout_classes = '';
				} else if( $container == 'content' ) {
					$layout_classes = 'row-fluid';
				} else if( $container == 'description' ) {
					$layout_classes = 'span' . $description_width . ' pull-left';
				} else if( $container == 'meta' ) {
					$layout_classes = 'span' . $meta_width . ' pull-right';
				}
					
				break;

			case 'description_left_no_meta':
			case 'description_left_with_meta':
				if( $container == 'featured' ) {
					$layout_classes = 'span' . $description_width . ' pull-right';
				} else if( $container == 'content' ) {
					$layout_classes = 'span' . $meta_width . ' pull-left';
				} else if( $container == 'description' ) {
					$layout_classes = '';
				} else if( $container == 'meta' ) {
					$layout_classes = '';
				}
				break;

			case 'description_right_no_meta':
			case 'description_right_with_meta':
				if( $container == 'featured' ) {
					$layout_classes = 'span' . $description_width . ' pull-left';
				} else if( $container == 'content' ) {
					$layout_classes = 'span' . $meta_width . ' pull-right';
				} else if( $container == 'description' ) {
					$layout_classes = '';
				} else if( $container == 'meta' ) {
					$layout_classes = '';
				}
					
				break;
		}

		return $layout_classes;
	}
}
/*----------------------------------------------------------------------------------
 Get related portfolio items
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_portfolio_related_by_taxonomy' ) ) {
	function bk_get_portfolio_related_by_taxonomy($post_id, $taxonomy) {
		$args = array();
		$terms = wp_get_object_terms($post_id, $taxonomy);
		if (count($terms)) {
			// Assumes only one term for per post in this taxonomy
			$post_ids = get_objects_in_term($terms[0]->term_id,$taxonomy);
			$count = get_option('bk_related_portfolio_items_count') + 1;
			$args = wp_parse_args($args,array(
					'post__in' => $post_ids,
					'post__not_in' => $post_id,
					'taxonomy' => $taxonomy,
					'term' => $terms[0]->slug,
					'posts_per_page' => $count
			));
		}
		return $args;
	}
}
/*----------------------------------------------------------------------------------
 Get related posts by tags
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_posts_related_by_tags' ) ) {
	function bk_get_posts_related_by_tags($post_id) {
		$query = new WP_Query();
		$tags = wp_get_post_tags($post_id, array( 'fields' => 'ids' ));
		if ($tags) {
			//$first_tag = $tags[0]->term_id;
			$count = get_option('related_posts_count');
			$args=array(
					'tag__in' => $tags,
					'post__not_in' => array($post_id),
					'posts_per_page' => $count,
					'ignore_sticky_posts'=> 1 //'caller_get_posts'=> 1
			);
			$query = new WP_Query($args);
		}
		return $query;
	}
}
/*----------------------------------------------------------------------------------
 Get related posts by categories
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_posts_related_by_categories' ) ) {
	function bk_get_posts_related_by_categories($post_id) {
		$query = new WP_Query();
		$cats = wp_get_post_categories($post_id, array( 'fields' => 'ids' ));
		if ($cats) {
			$count = get_option('related_posts_count');
			$args=array(
					'category__in' => $cats,
					'post__not_in' => array($post_id),
					'posts_per_page' => $count,
					'ignore_sticky_posts'=> 1 //'caller_get_posts'=> 1
			);
			$query = new WP_Query($args);
		}
		return $query;
	}
}
/*
 * Get single post summary for blog view
*
* @params:
*
* $post_id - ( integer ) id of post to be pulled
* $is_inside_grid - ( boolean ) - if it is inside grid layout then smaller images are used - if it is inside one column forced view then full width images are used
*
*/
if ( !function_exists( 'bk_get_post_summary' ) ) {
	function bk_get_post_summary($post_id = 0, $is_inside_grid = true) {
		$content = '';
		$additional_classes = 'bk-post-summary';

		$format = get_post_format( $post_id );
		if ( false === $format )
			$format = 'standard';

		$content .= '<article id="post-' . $post_id . '" class="' . implode(" ", get_post_class($additional_classes, $post_id)) . '">';

		$content .= bk_get_post_featured_content($post_id, true, $is_inside_grid);  // get featured content for standard, image, gallery, video, audio,
		$content .= '<div class="bk-blog-content-wrap">';

		if( $format == 'standard' || $format == 'image' || $format == 'gallery' || $format == 'video' || $format == 'audio' ) {  // standard, image, gallery, video, audio,
			$content .= '<h3 class="post-entry-title">';
			$content .= '<a rel="bookmark" href="'. get_permalink( $post_id ) .'" title="' . __('Permanent Link to ', 'corpora_theme') . '\'' . get_the_title( $post_id ) . '\'">';
			$content .= get_the_title($post_id);
			$content .= '</a>';
			$content .= '</h3>';
			$content .= bk_get_post_meta($post_id);
		}


		if( $format == 'standard' || $format == 'image' || $format == 'gallery' || $format == 'video' || $format == 'audio' || $format == 'aside' ) {  // standard, image, gallery, video, audio, aside -> get regular excerpt of post entry
			$content .= apply_filters('the_excerpt', get_the_excerpt());
		}

		//$content .= bk_get_post_date_and_format( $post_id );
		$content .= '<div class="bk-post-summary-actions">';

		$content .= '<span class="left"><a title="' . __("Permalink to ", 'corpora_theme') . '\'' . get_the_title($post_id) . '\'' . '" href="' . get_permalink( $post_id ) . '">' . get_option("bk_post_read_more_link_text", "Read More...") . '</a></span>';

		global $post;
		if( $post->post_type == 'post' || $post->post_type == 'portfolio' ) {
			$content .= '<span class="right">';
			//$content .= '<a class="dark" title="' . __("Comment on ", 'corpora_theme') . '\'' . get_the_title($post_id) . '\'' . '" href="' . get_comments_link( $post_id ) . '"><span class="comment-icon"></span>' . get_comments_number( $post_id ) . '</a>';
			$content .= bk_get_love_port_button( $post_id, 'dark' );
			$content .= '</span>';
		}

		$content .= '<div class="clearfix"></div>';
		$content .= '</div>';
		$content .= '</div>';

		$content .= '</article>';
		return $content;
	}
}
/*
 * Get post page content wrap classes - get additional content wrap classes based on position and
* presence of sidebars.
*
*/
if ( !function_exists( 'bk_get_post_page_content_wrap_classes' ) ) {
	function bk_get_post_page_content_wrap_classes() {
		global $post;
		$page_sidebars = "fullwidth";

		if( bk_is_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
			$page_sidebars = get_option("bk_shop_archives_entry_layout", "fullwidth");
		} else if( is_page() || is_single() ) {
			$page_sidebars = get_post_meta( $post->ID, '_bk_single_entry_layout', true );
		} else if(is_search() || is_category() || is_day() || is_month() || is_year() || is_tag() || is_author() || is_tax('post_format') ) {
			$page_sidebars = get_option("bk_blog_archives_entry_layout", "fullwidth");
		} else if( is_tax('filter') || is_tax('portfolio_skills') ) {
			$page_sidebars = get_option("bk_portfolio_archives_entry_layout", "fullwidth");
		} else if( is_404() ) {
			$page_sidebars = get_post_meta( get_option("bk_404_page_id"), '_bk_single_entry_layout', true );
		}

		$wrap_classes = 'no-sidebar span12';
		
		switch( $page_sidebars ) {
			case "left_sidebar":
				$wrap_classes = 'with-sidebar-left span8 pull-right';
				break;

			case "right_sidebar":
				$wrap_classes = 'with-sidebar-right span8';
				break;

			case "fullwidth":
				$wrap_classes = 'no-sidebar span12';
				break;
		}

		return $wrap_classes;
	}
}
/*----------------------------------------------------------------------------------
 Get single post entry content

@params:	$post_id - id of post entry to get
	
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_post_entry' ) ) {
	function bk_get_post_entry( $post_id = 0, $is_summary = true ) {
		$additional_classes = 'row-fluid';

		$format = get_post_format( $post_id );
		if ( false === $format ) {
			$format = 'standard';
		}

		echo  '<article id="post-' . $post_id . '" class="' . implode(" ", get_post_class($additional_classes, $post_id)) . '">';

		echo  bk_get_post_featured_content($post_id, $is_summary, false);  // get featured content for standard, image, gallery, video, audio,

		echo  '<h1 class="post-entry-title">';
		echo  get_the_title($post_id);
		echo  '</h1>';

		$content_wrap_classes = bk_get_post_page_content_wrap_classes();

		echo  '<div class="bk-post-page-content-outer-wrap ' . $content_wrap_classes . '">';
		echo  '<div class="bk-blog-content-wrap">';

		echo  bk_get_the_content( $post_id );

		echo  '<div class="bk-post-summary-actions">';


		if( get_option('bk_blog_show_tweet_button') == true ) {
			echo  '<span class="bk_blog_social_button_wrap">';
			echo  bk_get_twitter_button_markup( get_the_title($post_id), get_permalink( $post_id ), get_option('bk_blog_tweet_button_count') );
			echo  '</span>';
		}

		if( get_option('bk_blog_show_google_plus_button') == true ) {
			echo  '<span class="bk_blog_social_button_wrap">';
			echo  bk_get_google_plus_button_markup( get_permalink( $post_id ), get_option('bk_blog_google_plus_button_count') );
			echo  '</span>';
		}

		if( get_option('bk_blog_show_facebook_button') == true ) {
			echo  '<span class="bk_blog_social_button_wrap">';
			echo  bk_get_facebook_button_markup( get_permalink( $post_id ), get_option('bk_blog_facebook_button_count') );
			echo  '</span>';
		}

		if( get_option('bk_blog_show_pinterest_button') == true &&  has_post_thumbnail( $post_id ) ) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'fullsize' );

			echo  '<span class="bk_blog_social_button_wrap">';
			echo  bk_get_pinterest_social_button( get_the_title($post_id), get_permalink( $post_id ), $featured_image[0] , get_option('bk_blog_show_pinterest_button_count') );
			echo  '</span>';
		}

		global $post;
		if( $post->post_type == 'post' || $post->post_type == 'portfolio' ) {
			echo  '<span class="right">';
			echo  '<a class="dark" title="' . __("Comment on ", 'corpora_theme') . '\'' . get_the_title($post_id) . '\'' . '" href="' . get_comments_link( $post_id ) . '"><span class="comment-icon"></span>' . get_comments_number( $post_id ) . '</a>';
			echo  bk_get_love_port_button( $post_id, 'dark' );
			echo  '</span>';
		}

		echo  '<div class="clearfix"></div>';
		echo  '</div>';
		echo  '</div>';
		comments_template( '', true );

		echo  '</div>';

		get_sidebar();

		if( get_option("show_next_prev_posts_link") ) {
			echo  '<div class="clearfix"></div>';
			bk_post_prev_next_links();
		}

		echo  '</article>';
	}
}
/*
 * Print single post prev next links
*/
if ( !function_exists( 'bk_post_prev_next_links' ) ) {
	function bk_post_prev_next_links() {

		echo '<div class="next_prev_post_links clearfix">';
		echo '<div class="one_half">';
		$format = '';
		$prev_post = get_adjacent_post( false, '', true );
		if( $prev_post ) {
			$prev_post_id = $prev_post->ID;
			if( get_post_format( $prev_post_id ) )
				$format = ' (' . get_post_format_string(get_post_format( $prev_post_id )) . ') ';

			$title = bk_get_custom_text( get_the_title($prev_post_id) );

			previous_post_link('<span class="left">%link</span>', '&laquo; ' . $title . $format );
		}

		echo '</div>';
		echo '<div class="one_half last">';

		$format = '';
		$next_post = get_adjacent_post( false, '', false );
		if( $next_post ) {
			$next_post_id = $next_post->ID;
			if( get_post_format( $next_post_id ) )
				$format = '(' . get_post_format_string(get_post_format( $next_post_id )) . ') ';

			$title = bk_get_custom_text( get_the_title($next_post_id) );

			next_post_link('<span class="right">%link</span>', $title . ' ' . $format . '&raquo;');
		}

		echo '</div>';
		echo '</div>';
	}
}
/*----------------------------------------------------------------------------------
 Get single blog post featured content

@params:

$post_id - id of post to be pulled
$is_summary - if it is inside blog view - then it is summary if it sinlg epost then false
$is_inside_grid - if it is summary and displayed inside blog grid ( except one column forced blog width ) this parameter is true
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_post_featured_content' ) ) {
	function bk_get_post_featured_content($post_id = 0, $is_summary = false, $is_inside_grid = false ) {
		$content = '';

		$format = get_post_format( $post_id );
		if ( false === $format )
			$format = 'standard';

		if( $format == 'standard' || $format == 'image' ) {
			$featured_image_behaviour = get_post_meta( $post_id, '_bk_post_featured_image_behavior', true );
			$featured_image_link = '';

			$featured_image_size = 'fullwidth'; // default for single post layout
			if( $is_summary && $is_inside_grid && $format == 'standard') {
				$featured_image_size = 'blog-thumb-cropped';
			} else if( $is_summary && $is_inside_grid && $format == 'image' ) {
				$featured_image_size = 'blog-thumb-prop';
			}

			if( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail() ) {
				$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $featured_image_size);
				$image_effect = get_post_meta($post_id, '_bk_post_featured_image_effect', true);
					
					
				$content .= '<div class="bk-img-wrap ' . $image_effect . '">';
				$content .= '<img class="bk-post-thumb" src="' . $image_src[0] . '">';
					
				$video_width = get_post_meta($post_id, '_bk_popup_video_width', true);
				$video_height = get_post_meta($post_id, '_bk_popup_video_height', true);

				switch( $featured_image_behaviour ) {
					case 'image_popup':
						$content .= '<a class="bk-post-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post_id . '" href="' . get_post_meta($post_id, '_bk_post_featured_image_link', true) . '"></a>';
						break;

					case 'vimeo_popup':
						$content .= '<a class="bk-post-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post_id . '" href="' . get_post_meta($post_id, '_bk_post_featured_image_vimeo_link', true) . '"></a>';
						break;

					case 'youtube_popup':
						$content .= '<a class="bk-post-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post_id . '" href="' . get_post_meta($post_id, '_bk_post_featured_image_youtube_link', true) . '"></a>';
						break;

					case 'gallery_popup':
						$gallery_items = Bk_Popup_Gallery_Manager::getInstance()->get_gallery_items($post_id,  '_bk_post_featured_image_popup_gallery', 'popup-gallery-thumb');
							
						$link_classes = 'bk-post-thumb-link';  // only first on will have this class to make it clickable - rest of links will be hidden
						foreach( $gallery_items as $item ) {
							switch( $item['item_type'] ) {
								case 'bk_gallery_image':
									$content .= '<a class="' . $link_classes . ' jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['image_url'] . '" data-thumbnail="' . $item['thumb_url'] . '"></a>';
									break;
										
								case 'bk_gallery_video':
									$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '"></a>';
									break;
										
								case 'bk_gallery_selfhosted_video':
									$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post_id . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '" data-poster="' . $item['video_poster'] . '"></a>';
									break;
							}
								
							$link_classes = 'bk-hidden-post-link';
						}
							
						break;
							
					case 'content_link':
						if( $is_summary ) {
							$content .= '<a class="bk-post-thumb-link" href="' . get_permalink($post_id) . '"></a>';
						}
						break;
				}
					
				$content .= '<div class="bk-img-border"></div>';
					
				$image_overlay = get_post_meta($post_id, '_bk_post_featured_image_overlay', true);
					
				if( $image_overlay != 'none' ) {
					$content .= '<div class="bk-img-overlay ' . $image_overlay . '">';
					$content .= '<div class="bk-img-overlay-outer">';
					$content .= '<div class="bk-img-overlay-inner">';
						
					$content .= '</div>'; // end of .bk-img-overlay-inner
					$content .= '</div>'; // end of .bk-img-overlay-outer
					$content .= '</div>'; // end of .bk-img-overlay
				}
					
				$content .= '</div>'; // end of .bk-img-wrap
			}
		} else if( 'gallery' == $format ) {

			$smooth_height_enabled = get_post_meta( $post_id, '_bk_post_slider_smooth_height_enabled', true ) == 'on' ? 1 : 0;

			//$nav_type = get_post_meta( $post_id, '_bk_post_slider_nav_type', true );
			$auto_play = get_post_meta( $post_id, '_bk_post_slider_autoplay_enabled', true );

			$is_inside_blog_grid = $is_inside_grid;
			$gallery_id = '_bk_post_gallery';

			$content .= bk_get_post_featured_slider( $post_id, $gallery_id, $auto_play, $smooth_height_enabled, $is_inside_blog_grid );
		} else if( $format == 'video' ) {
			$video_link = '';
			$width = get_post_meta( $post_id, '_bk_post_video_width', true );
			$height = get_post_meta( $post_id, '_bk_post_video_height', true );
			$video_type = get_post_meta( $post_id, '_bk_post_video_type', true );
			switch( $video_type ) {
				case 'vimeo_link':
					$video_link = get_post_meta( $post_id, '_bk_post_vimeo_link', true );

					if( preg_match("/\b(?:vimeo)\.com\b/i", $video_link) ) {
						$content .= bk_get_vimeo_markup_from_link( $video_link, $width, $height );
					}
					break;

				case 'youtube_link':
					$video_link = get_post_meta( $post_id, '_bk_post_youtube_link', true );

					if( preg_match("/\b(?:youtube)\.com\b/i", $video_link) ) {
						$content .= bk_get_youtube_markup_from_link( $video_link, $width, $height);
					}
					break;

				case 'self_hosted':
					$video_link = array( get_post_meta( $post_id, '_bk_post_self_hosted_m4v_link', true ),
					get_post_meta( $post_id, '_bk_post_self_hosted_ogv_link', true ) );
						
					if(  preg_match("/\b\.m4v\b/i", $video_link[0] ) ||
							preg_match("/\b\.mp4\b/i", $video_link[0] ) ||
							preg_match("/\b\.ogv\b/i", $video_link[1] ) ||
							preg_match("/\b\.webm\b/i", $video_link[1] ) ) {
						$poster_src = get_post_meta( $post_id, '_bk_post_self_hosted_video_poster', true );
						$content .= bk_get_self_hosted_video_markup_from_link( $video_link, $poster_src, $post_id, $width, $height );
					}
					break;
			}
		} else if( ($format == 'audio') && ('selfhosted_audio' == get_post_meta( $post_id, '_bk_post_audio_type', true )) ) {
			$audio_link = get_post_meta( $post_id, '_bk_post_audio_link', true );
			$audio_poster = get_post_meta( $post_id, '_bk_post_audio_poster', true );

			if( !empty($audio_poster) ) {
				$content .= '<div class="bk-img-wrap">';
				$content .= '<img class="wp-post-image" src="' . $audio_poster . '">';
				$content .= '<div class="bk-img-border"></div>';
				$content .= '</div>';
			}

			$content .= bk_get_audio_markup_from_link( $audio_link, $post_id );
		} else if( ($format == 'audio') && ('soundcloud_audio' == get_post_meta( $post_id, '_bk_post_audio_type', true )) ) {
			$url = get_post_meta( $post_id, '_bk_post_audio_soundcloud_link', true );
			$comments_enabled = get_post_meta( $post_id, '_bk_post_audio_soundcloud_comments_enabled', true );
			$show_artwork = get_post_meta( $post_id, '_bk_post_audio_soundcloud_show_artwork', true );
			$auto_play = get_post_meta( $post_id, '_bk_post_audio_soundcloud_auto_play', true );
			$color = get_post_meta( $post_id, '_bk_post_audio_soundcloud_color', true );

			$content .= bk_get_soundcloud_audio_markup_from_link( $url, $auto_play, $comments_enabled, $show_artwork, $color );
		} else if ( $format == 'link' ) {
			$primary_text = get_post_meta( $post_id, '_bk_post_link_primary_text', true );
			$secondary_text = get_post_meta( $post_id, '_bk_post_link_secondary_text', true );
			$link = get_post_meta( $post_id, '_bk_post_link_href', true );

			$content .= '<div class="bk-link-post-format-featured-inner-wrap">';

			if( !empty($primary_text) ) {
				$content .= '<h3 class="bk-link-primary"><a href="' . $link . '">' . $primary_text . '</a></h3>';
			}

			if( !empty($secondary_text) ) {
				$content .= '<span class="bk-link-secondary"><a href="' . $link . '">' . $secondary_text . '</a></span>';
			}

			$content .= '</div>';
		}	else if ( $format == 'quote' ) {
			$quote_text = get_post_meta( $post_id, '_bk_post_quote_text', true );
			$quote_author = get_post_meta( $post_id, '_bk_post_quote_author', true );

			$content .= '<div class="bk-quote-post-format-featured-inner-wrap">';

			if( !empty($quote_text) ) {
				$content .= '<h3 class="bk-quote-text">' . $quote_text . '</h3>';
			}

			if( !empty($quote_author) ) {
				$content .= '<span class="bk-quote-author">' . $quote_author . '</span>';
			}

			$content .= '</div>';
		}	else if ( $format == 'status' ) {
			$content .= '<div class="bk-status-post-format-featured-inner-wrap">';

			$tweet_link = get_post_meta( $post_id, '_bk_post_status_link', true ); // tweet to embedd

			$tweet_object = bk_get_tweet_object($post_id, $tweet_link);

			if( !empty($tweet_object) && is_array($tweet_object) ) {
				if(preg_match("/<p>(.*)<\/p>/i", $tweet_object['html'], $matches)) { // if tweet body was found
					$tweet_text = $matches[1];
					$author_url = $tweet_object['author_url'];
					$author_name = $tweet_object['author_name'];

					$content .= '<h3 class="tweet-text">' . $tweet_text . '</h3>';
					$content .= '<a class="tweet-author-link" href="' . $author_url . '">' . $author_name . '</a>';
				}
			}

			//$content .= apply_filters('the_content', $tweet_link);

			$content .= '</div>';
		} else if ( $format == 'chat' ) {
			$content .= '<div class="bk-chat-post-format-featured-inner-wrap">';
			$content .= apply_filters('the_content', get_post_meta( $post_id, '_bk_post_chat_text', true ));
			$content .= '</div>';
		}

		if( !empty($content) ) {
			$content = '<div class="bk-post-featured-content-wrap">' . $content . '</div>';
		}

		return $content;
	}
}
/*
 * Get soundlcoud iframe audio markup from url to track set or group
* @params
*
* $url - link to track set or group from soundcloud
* $show_comments - indicates if comments should be visible
* $show_artwork - indicates if author logo would be visible
* $color - general theme color - play button and selections
*
* @returns - iframe markup of soundlcoud widget
*
*/
if ( !function_exists( 'bk_get_soundcloud_audio_markup_from_link' ) ) {
	function bk_get_soundcloud_audio_markup_from_link( $url, $auto_play, $show_comments, $show_artwork, $color ) {
		$options = array(
	   'url' => $url,
	   'auto_play' => ( $auto_play == 'on' ) ? 'true' : 'false',
	   'show_comments' => ( $show_comments == 'on' ) ? 'true' : 'false',
	   'show_artwork' => ( $show_artwork == 'on' ) ? 'true' : 'false',
	   'color' => trim($color, "#")
		);

		// Build URL
		$soundcloud_url = 'http://w.soundcloud.com/player?' . http_build_query($options);
		// Set default width if not defined
		$width = '100%';
		// Set default height if not defined
		$height =  preg_match('/^(.+?)\/(sets|groups|playlists)\/(.+?)$/', $url) ? '450' : '166'; // if playlists sets or group then use bigger player

		return sprintf('<iframe width="%s" height="%s" scrolling="no" frameborder="no" src="%s"></iframe>', $width, $height, $soundcloud_url);
	}
}
/*----------------------------------------------------------------------------------
 helper method get tweet from oembed service or from cache
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_tweet_id_from_url' ) ) {
	function bk_get_tweet_id_from_url( $tweet_link ) {
		if( preg_match("/([0-9]*)$/i", trim($tweet_link), $matches) )
			return $matches[1];

		return '';
	}
}

if ( !function_exists( 'bk_get_tweet_object' ) ) {
	function bk_get_tweet_object($post_id, $tweet_link) {
		$cached_tweet = get_post_meta( $post_id, '_bk_post_status_cache', true );

		if( !empty($cached_tweet) && is_array($cached_tweet) && ( bk_get_tweet_id_from_url( $cached_tweet['url'] ) == bk_get_tweet_id_from_url( $tweet_link ) ) ) {
			return $cached_tweet;
		}

		$provider = 'http://api.twitter.com/1/statuses/oembed.json';
		$provider = add_query_arg( 'id', bk_get_tweet_id_from_url( $tweet_link ), $provider );
		$provider = add_query_arg( 'omit_script', 'true', $provider );
		$provider = add_query_arg( 'format', 'json', $provider  );

		$response = wp_remote_get( $provider );
		if ( !is_wp_error( $result ) ) {
			if ( $body = wp_remote_retrieve_body( $response ) ) {
				$tweet_object = json_decode($body, true);
				update_post_meta($post_id, '_bk_post_status_cache', $tweet_object);
				return $tweet_object;
			}
		}

		return null;
	}
}
/*----------------------------------------------------------------------------------
 get post publish date
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_post_publish_date' ) ) {
	function bk_get_post_publish_date( $post_id ) {
		$content = '';

		$year = get_the_time( 'Y' );
		$month = get_the_time( 'm' );
		$day = get_the_time( 'd' );

		$monhly_archive_link = sprintf('<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
				get_month_link( $year, $month ),
				esc_attr( sprintf( __( 'Archive for %s', 'corpora_theme' ), esc_attr( get_the_time( 'F Y' ) ) ) ),
				get_the_time( 'M' ) );

		$daily_archive_link = sprintf('<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
				get_day_link( $year, $month, $day ),
				esc_attr( sprintf( __( 'Archive for %s', 'corpora_theme' ), esc_attr( get_the_time( 'F d, Y' ) ) ) ),
				$day );

		$yearly_archive_link = sprintf('<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
				get_year_link( $year ),
				esc_attr( sprintf( __( 'Archive for %s', 'corpora_theme' ), esc_attr( $year ) ) ),
				$year );

		$content .= $monhly_archive_link . ' ' . $daily_archive_link . ' ' . $yearly_archive_link;

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 get post meta
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_post_meta' ) ) {
	function bk_get_post_meta($post_id) {
		$content = '';
		$content .= '<span class="bk-post-entry-meta">';

		$author_link = sprintf('<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				esc_attr( sprintf( __( 'Posts by %s', 'corpora_theme' ), get_the_author_meta( 'display_name' ) ) ),
				get_the_author_meta( 'display_name' ) );

		$content .= __('Posted by ', 'corpora_theme') . $author_link ;

		/* AC - Removed categories and tags from meta data
$categories = get_the_category($post_id);
		if( !empty($categories) ) {
			$categories_string =  __('in ', 'corpora_theme');
			$separator = ' / ';
			foreach($categories as $category) {
				$categories_string .= '<a href="'.get_category_link($category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in '%s'", 'corpora_theme' ), $category->name ) ) . '">'.$category->name.'</a>'.$separator;
			}
			$content .= ' ' . trim($categories_string, $separator);
		}

		$tags = get_the_tags($post_id);
		if( !empty($tags) ) {
			$tags_string =  __('tagged with ', 'corpora_theme');
			$separator = ' / ';
			foreach($tags as $tag) {
				$tags_string .= '<a href="'.get_tag_link($tag->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts with tags '%s'", 'corpora_theme' ), $tag->name ) ) . '">'.$tag->name.'</a>'.$separator;
			}
			$content .= ' ' . trim($tags_string, $separator);
		}
*/

		$content .= __(' On ', 'corpora_theme') . bk_get_post_publish_date($post_id);

		$content .= '</span>';

		return $content;
	}
}

/*----------------------------------------------------------------------------------
 Get single portfolio entry content

@params:	$post_id - id of portfolio entry to get
$is_ajax_call - wheater to show prev next link - force mode only for ajax content
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_portfolio_entry' ) ) {
	function bk_get_portfolio_entry( $post_id = 0, $is_ajax_call = false ) {
		$content = '';
		$entry_layout = get_post_meta( $post_id, '_bk_portfolio_entry_layout', true );
		$meta_desc_width = get_post_meta( $post_id, '_bk_portfolio_meta_description_width', true );

		echo '<article id="portfolio-' . $post_id . '" class="row-fluid ' . implode(" ", get_post_class("bk-portfolio-item", $post_id)) . ' ' . $entry_layout . '">';

		if( get_post_meta( $post_id, '_bk_portfolio_hide_featured_content', true ) == false ) {
			$featured_content_classes = bk_get_portfolio_entry_layout_classes( $entry_layout, 'featured', $meta_desc_width );
	
			echo '<div class="bk-portfolio-featured-wrap ' . $featured_content_classes . '">';
			//echo '<div class="bk-portfolio-featured-inner-wrap ' . get_post_meta( $post_id, '_bk_portfolio_featured_area_type', true ) .'">';
			echo '<div class="bk-portfolio-featured-inner-wrap">';
			echo bk_get_portfolio_entry_featured_content( $post_id );		// get featured content - image, gallery, video, audio???
			echo '</div>';
			echo '</div>';
		}

		$content_wrap_classes = bk_get_portfolio_entry_layout_classes( $entry_layout, 'content', $meta_desc_width );

		echo '<div class="bk-portfolio-content-wrap ' . $content_wrap_classes . '">';
		echo '<div class="bk-portfolio-title-wrap"><h1 class="bk-portfolio-title">' . get_the_title($post_id) . '</h1>';

		if( get_option("bk_portfolio_show_navigation") ) {
			$exclude_cats = '';
			$portfolio_home_page_id = get_option("bk_portfolio_homepage_link");

			echo '<ul class="portfolio-top-nav">';

			if( get_adjacent_post( false, $exclude_cats, true ) ) {
				$prev_post = get_adjacent_post( false, $exclude_cats, true );
				echo '<li><a title="' . $prev_post->post_title . '" class="prev-portfolio-entry-link" href="' . get_permalink( $prev_post->ID ) . '"></a></li>';
			}

			if( $portfolio_home_page_id != -1 ) {
				echo '<li><a title="' . get_option("bk_portfolio_ajax_close_entry_text", "Close") . '" class="back-to-portfolio-link" href="'. get_permalink( $portfolio_home_page_id ) .'"></a></li>';
			}
			
			if( get_adjacent_post( false, $exclude_cats, false ) ) {
				$next_post = get_adjacent_post( false, $exclude_cats, false );
				echo '<li><a title="' . $next_post->post_title . '" class="next-portfolio-entry-link" href="' . get_permalink( $next_post->ID ) . '"></a></li>';
			}

			echo '</ul>';
		}

		echo '</div>';

		if( $entry_layout == 'fullwidth_meta_top' ) {
			echo bk_get_port_meta( $post_id, $entry_layout, $meta_desc_width );
		}

		$description_content_classes = bk_get_portfolio_entry_layout_classes( $entry_layout, 'description', $meta_desc_width );

		echo '<div class="bk-portfolio-description-wrap clearfix ' . $description_content_classes . '">';
		echo '<div class="bk-portfolio-description-inner-wrap">';
		echo bk_get_the_content( $post_id );

		echo '</div>';
		echo '</div>';

		if( $entry_layout == 'fullwidth_meta_bottom' ||
				$entry_layout == 'fullwidth_meta_left' ||
				$entry_layout == 'fullwidth_meta_right' ||
				$entry_layout == 'description_left_with_meta' ||
				$entry_layout == 'description_right_with_meta'	 ) {
			echo bk_get_port_meta( $post_id, $entry_layout, $meta_desc_width );
		}

		echo '<div class="clearfix"></div>';
		
		comments_template( '', true );
		echo '</div>';

		echo '</article>';
	}
}
/*----------------------------------------------------------------------------------
 Get port meta info
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_port_meta' ) ) {
	function bk_get_port_meta( $post_id, $entry_layout, $meta_width ) {
		$content = '';

		$meta_wrap_classes = bk_get_portfolio_entry_layout_classes( $entry_layout, 'meta', $meta_width );

		$content .= '<div class="bk-portfolio-meta-wrap clearfix ' . $meta_wrap_classes . '">';

		$content .= '<ul class="bk-portfolio-meta clearfix">';
		if ( $keys = get_post_custom_keys( $post_id ) ) {
			foreach ( (array) $keys as $key ) {
				$keyt = trim($key);
				if ( '_' == $keyt{0} )
					continue;
				$values = array_map('trim', get_post_custom_values($key, $post_id));
				$value = implode($values,', ');
				$custom_meta_key = '<li><span class="post-meta-key">' . $key . ':</span>' . $value . '</li>';
				//$content .= apply_filters('the_meta_key', $custom_meta_key);
			}
		}

		if( get_post_meta( $post_id, '_bk_portfolio_external_link_enabled', true ) == true ) {
			$link = get_post_meta( $post_id, '_bk_portfolio_external_link', true );
			$link_text = get_post_meta( $post_id, '_bk_portfolio_external_link_text', true );

			if( !empty($link) && !empty($link_text) )
				$content .= '<li><span class="link-icon"></span><a href="' . $link . '">' . $link_text  . '</a></li>';
		}

		$porttags = get_the_terms( $post_id, 'portfolio_skills' );
		if ( $porttags && ! is_wp_error( $porttags ) ) {
			$tag_links = '';
			foreach ( $porttags as $porttag ) {
				$tag_links .= '<a href="' . get_term_link($porttag->slug, 'portfolio_skills' ) . '">' . $porttag->name . '</a>, ';
			}
			$tag_links = trim($tag_links, ', ');

			$content .= '<li><span class="tag-icon"></span>' . $tag_links  . '</li>';
		}

		$content .= '<li>';
		$content .= bk_get_love_port_button( $post_id, 'dark' );
		$content .= '</li>';

		if( get_option('bk_portfolio_show_views') == true ) {
			$content .= '<li><span class="eye-icon"></span>';
			$content .= bk_get_view_portfolio_counter( $post_id );
			$content .= '</li>';
		}

		if( get_option('bk_portfolio_show_facebook_button') == true ) {
			$content .= '<li class="bk_facebook_share_button_wrap bk_social_button_wrap">';
			$content .= bk_get_facebook_button_markup( get_permalink( $post_id ), get_option('bk_portfolio_facebook_button_count') );
			$content .= '</li>';
		}

		if( get_option('bk_portfolio_show_tweet_button') == true ) {
			$content .= '<li class="bk_tweet_share_button_wrap bk_social_button_wrap">';
			$content .= bk_get_twitter_button_markup( get_the_title($post_id), get_permalink( $post_id ), get_option('bk_portfolio_tweet_button_count') );
			$content .= '</li>';
		}

		if( get_option('bk_portfolio_show_google_plus_button') == true ) {
			$content .= '<li class="bk_google_plus_share_button_wrap bk_social_button_wrap">';
			$content .= bk_get_google_plus_button_markup( get_permalink( $post_id ), get_option('bk_portfolio_google_plus_button_count') );
			$content .= '</li>';
		}

		if( get_option('bk_portfolio_show_pinterest_button') == true &&  has_post_thumbnail( $post_id ) ) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'fullsize' );

			$content .= '<li class="bk_pinterest_button_wrap bk_social_button_wrap">';
			$content .= bk_get_pinterest_social_button( get_the_title($post_id), get_permalink( $post_id ), $featured_image[0] , get_option('bk_portfolio_show_pinterest_button_count') );
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</div>';

		return $content;
	}
}
/*
 * Get pinterest button
*/
if ( !function_exists( 'bk_get_pinterest_social_button' ) ) {
	function bk_get_pinterest_social_button($description, $url, $media, $show_counts) {
		if( $show_counts ) {
			$show_counts = 'beside';
		} else {
			$show_counts = 'none';
		}

		return '<a href="//pinterest.com/pin/create/button/?url=' . urlencode($url) . '&media=' . urlencode($media) . '&description=' . urlencode($description) . '" data-pin-do="buttonPin" data-pin-config="' . $show_counts . '"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>';
	}
}
/*----------------------------------------------------------------------------------
 Get google plus button markup
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_google_plus_button_markup' ) ) {
	function bk_get_google_plus_button_markup($link_to_share ,$show_counts) {
		if( $show_counts ) {
			$show_counts = 'bubble';
		} else {
			$show_counts = 'none';
		}

		return '<div class="g-plusone" data-size="medium" data-annotation="' . $show_counts . '" data-href="' . htmlspecialchars($link_to_share, ENT_QUOTES) . '"></div>';
	}
}
/*----------------------------------------------------------------------------------
 Get facebook button markup
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_facebook_button_markup' ) ) {
	function bk_get_facebook_button_markup($link_to_share ,$show_counts) {
		$height = 21;
		$width = 95;
		if( $show_counts ) {
			$show_counts = 'button_count';
			$height = 21;
			$width = 95;
		} else {
			$show_counts = 'standard';
			$height = 35;
			$width = 225;
		}

		return '<iframe src="//www.facebook.com/plugins/like.php?href=' . urlencode($link_to_share) . '&amp;send=false&amp;layout=' . $show_counts . '&amp;width=' . $width . '&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=' . $height . '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $width . 'px; height:' . $height . 'px;" allowTransparency="true"></iframe>';
	}
}
/*----------------------------------------------------------------------------------
 Get twitter button markup
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_twitter_button_markup' ) ) {
	function bk_get_twitter_button_markup($tweet_text, $link_to_share ,$show_counts) {
		if( $show_counts ) {
			$show_counts = 'horizontal';
		} else {
			$show_counts = 'none';
		}

		return '<a class="twitter-share-button" href="http://twitter.com/share" data-text="' . $tweet_text . '" data-url="' . $link_to_share . '" data-count="' . $show_counts . '"></a>';
	}
}
/*----------------------------------------------------------------------------------
 Set portfolio view counter
----------------------------------------------------------------------------------*/
$bk_portfolio_view_count;  // global variable

if ( !function_exists( 'bk_set_view_portfolio_counter' ) ) {
	function bk_set_view_portfolio_counter( $post_id ) {
		$content = '';

		$timeout = 24; // set cookie for 24 hours by defualt
		$timoeut_hours = get_option('bk_portfolio_show_views_timeout', 24);
		if($timoeut_hours == -1 || $timoeut_hours == 0) {
			$timeout = 24 * 365 * 10;  // set timout for 10 years
		} else {
			$timeout = $timoeut_hours;
		}

		global $bk_portfolio_view_count;
		$bk_portfolio_view_count = get_post_meta( $post_id, '_bk_portfolio_item_view_count', true );
		if( empty( $bk_portfolio_view_count ) ) {
			$bk_portfolio_view_count = 0;
		}

		if( !isset($_COOKIE['bk_view_post_counter_'. $post_id]) ) {
			$bk_portfolio_view_count++;
			update_post_meta($post_id, '_bk_portfolio_item_view_count', $bk_portfolio_view_count );
			setcookie('bk_view_post_counter_'. $post_id, true, time() + ( 3600 * $timeout ), '/');  // set cookie for 24 hours
		}
	}
}
/*----------------------------------------------------------------------------------
 Get portfolio view counter
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_view_portfolio_counter' ) ) {
	function bk_get_view_portfolio_counter( $post_id ) {
		global $bk_portfolio_view_count;

		$bk_portfolio_view_count = bk_get_number_to_human_readable_string( $bk_portfolio_view_count );
		return $bk_portfolio_view_count;
	}
}
/*----------------------------------------------------------------------------------
 Get portfolio love button
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_love_port_button' ) ) {
	function bk_get_love_port_button( $post_id, $color_class ) {
		$content = '';

		$post_type = get_post($post_id)->post_type;

		if( ($post_type == 'portfolio') && (get_option('bk_portfolio_show_love_button') == false) )
			return $content;

		if( ($post_type == 'post') && (get_option('bk_post_show_love_button') == false) )
			return $content;

		$love_count = get_post_meta( $post_id, '_bk_portfolio_item_love_count', true );
		if( empty( $love_count ) ) {
			$love_count = 0;
		}

		$love_count = bk_get_number_to_human_readable_string( $love_count );

		$love_status_class = '';

		if($post_type == 'portfolio') {
			$like_title = get_option("bk_portfolio_ajax_like_active_text", 'Love this');
			$like_inactive_title = get_option("bk_portfolio_ajax_like_inactive_text", 'You already love this');
		} elseif($post_type == 'post') {
			$like_title = get_option("bk_post_ajax_like_active_text", 'Like this post');
			$like_inactive_title = get_option("bk_post_ajax_like_inactive_text", 'You already love this post');
		}

		if( isset($_COOKIE['bk_love_post_'. $post_id]) ) {
			$love_status_class = 'inactive';
			$like_title = $like_inactive_title;
		}

		$content .= '<a title="' . $like_title .'" data-like_inactive_title="' . $like_inactive_title . '" data-post-id="' . $post_id . '" class="bk-love-button ' . $love_status_class . ' ' . $color_class . '"><span class="heart-icon"></span><span class="love-counter">' . $love_count . '</span></a>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Get page number if any
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_page_number' ) ) {
	function bk_get_page_number() {
		if ( get_query_var('paged') ) {
			print ' | ' . __( 'Page ' , 'corpora_theme') . get_query_var('paged');
		}
	}
}
/*----------------------------------------------------------------------------------
 Print testimonials widget
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_testimonial_items_widget' ) ) {
	function bk_get_testimonial_items_widget( $args, $autoplay ) {
		$content = '';

		$content .= '<div class="bk-testimonials-wrap flexslider" data-bullet_enabled="false" data-smooth_height="on" data-autoplay="' . $autoplay . '">';

		$content .= '<ul class="bk-testimonials-widget slides">';

		global $post;

		query_posts($args);
		if ( have_posts() ) : while ( have_posts() ) : the_post();

		$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' );
		$large_image = $large_image[0];

		$author = get_post_meta($post->ID, '_bk_testimonial_author_name', true);
		$position = get_post_meta($post->ID, '_bk_testimonial_author_position', true);
		$link = get_post_meta($post->ID, '_bk_testimonial_author_link', true);
		$link_target = get_post_meta($post->ID, '_bk_testimonial_author_link_target', true);

		$content .= '<li data-author="' . $author . '" data-link="' . $link . '">';
		$content .= '<div class="bk-testimonial-body">';
		$content .= '<span class="bk-testinonial-title">'. get_the_title() .'</span>';
		$content .= '<span class="bk-testimonial-content">'. get_the_content() .'</span>';
		$content .= '<div class="testimonial-tip"></div>';
		$content .= '</div>';

		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
			$content .= '<div class="testimonial-avatar-wrap">';
			if( !empty($link) )
				$content .= '<a target="' . $link_target . '" rel="bookmark" href="' . $link. '">';

			$content .= get_the_post_thumbnail();

			if( !empty($link) )
				$content .= '</a>';
			$content .= '</div>';
		}

		$content .= '<span class="author-name">';

		if( !empty($link) )
			$content .= '<a target="' . $link_target . '" rel="bookmark" href="' . $link. '">';

		$content .= $author;

		if( !empty($link) )
			$content .= '</a>';

		if( !empty($position) )
			$content .= ',';

		$content .= '</span>';

		$content .= '<span class="author-position">';

		$content .= $position;

		$content .= '</span>';

		$content .= '</li>';

		endwhile;
		endif;

		wp_reset_query();

		$content .= '</ul>';
		$content .= '</div>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print recent portfolio items according to passed args and settings
----------------------------------------------------------------------------------*/
$unique_portfolio_widget_id = 0;

if ( !function_exists( 'bk_get_recent_portfolio_items_widget' ) ) {
	function bk_get_recent_portfolio_items_widget( $args, $prettyphoto_enabled = true ) {

		global $unique_portfolio_widget_id;
		$unique_portfolio_widget_id++;

		$content = '';

		$content .= '<ul class="bk-recent-portfolio-widget clearfix">';

		query_posts($args);
		if ( have_posts() ) : while ( have_posts() ) : the_post();

		$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' );
		$link = $large_image[0];

		//$video_link = trim( get_post_meta(get_the_ID(), '_bk_featured_image_video_link', true) );
		//if( !empty($video_link) && preg_match ("/\b(?:vimeo|youtube)\.com\b/i", $video_link) )
		//			 $link = $video_link;


		$content .= '<li class="clearfix">';

		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
				
			if( $prettyphoto_enabled )
				$content .= '<a class="jackbox" data-group="recentportfolio_' . $unique_portfolio_widget_id . '" href="' . $link . '">';
			else
				$content .= '<a rel="bookmark" href="' . get_permalink() . '">';

			$content .= get_the_post_thumbnail();
			$content .= '</a>';
		}

		$content .= '</li>';

		endwhile;
		endif;

		wp_reset_query();

		$content .= '</ul>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print recent comments according to passed args and settings
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_recent_comments_widget' ) ) {
	function bk_get_recent_comments_widget( $args ) {
		$content = '';

		$content .= '<ul class="bk-recent-posts-widget bk-recent-comments-widget">';

		$comments = get_comments($args);

		foreach($comments as $comment) {

			$content .= '<li class="clearfix">';
			$content .= 	'<div class="widget-thumb img-frame left">';

			if( !empty($comment->comment_author_url) )
				$content .= 		'<a href="' . $comment->comment_author_url . '">';

			$content .=  			get_avatar( $comment, $size = '120' );


			if( !empty($comment->comment_author_url) )
				$content .= 		'</a>';
			$content .= 	'</div>';

			$content .= '<div class="bk-recent-comment">';
			$content .=	'<span class="widget-post-title">';

			if( !empty($comment->comment_author_url) )
				$content .=	 '<a href="' . $comment->comment_author_url . '">';

			$content .=	 $comment->comment_author;

			if( !empty($comment->comment_author_url) )
				$content .=	 '</a>';


			$content .=		 __(' on ', 'corpora_theme');
			$content .=		'<a href="' . get_permalink( $comment->comment_post_ID ) . '">' . bk_get_custom_text( get_the_title($comment->comment_post_ID), 5 ) . '</a>';
			$content .=	'</span>';
			$content .=	'<span class="widget-time"><span aria-hidden="true" class="icon-clock-2 bk-widget-small-icon"></span>' . human_time_diff( strtotime("{$comment->comment_date_gmt} GMT"), current_time('timestamp') ) . __(' ago', 'corpora_theme') . '</span>';
			$content .=	'<span class="widget-comment-excerpt"><a href="' . get_comment_link( $comment ) . '">';
			$content .=	 get_comment_excerpt( $comment->comment_ID );
			$content .=	'</a>';
			$content .=	'</span>';
			$content .= '</div>';

			$content .= '</li>';
		}

		$content .= '</ul>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print recent posts according to passed args and settings
----------------------------------------------------------------------------------*/
$unique_recent_posts_id = 0;

if ( !function_exists( 'bk_get_recent_posts_widget' ) ) {
	function bk_get_recent_posts_widget( $args, $prettyphoto_enabled ) {
		global $unique_recent_posts_id;
		$unique_recent_posts_id++;

		$content = '';

		$content .= '<ul class="bk-recent-posts-widget">';

		query_posts($args);
		if ( have_posts() ) : while ( have_posts() ) : the_post();

		$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' );
		$link = $large_image[0];

		if( $prettyphoto_enabled )
			$image_link = '<a class="jackbox" data-group="recentposts_' . $unique_recent_posts_id . '" href="' . $link . '">';
		else
			$image_link = '<a rel="bookmark" href="' . get_permalink() . '">';

		$content .= '<li class="clearfix">';

		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
			$content .= '<div class="widget-thumb img-frame left">';
			$content .= $image_link;
			$content .= get_the_post_thumbnail();
			$content .= '</a>';
			$content .= '</div>';
		}

		$content .= '<span class="widget-post-title"><a href="'. get_permalink() .'">'. bk_get_custom_text( get_the_title(), 5 ) .'</a></span>';
		$content .= '<span class="widget-time"><span aria-hidden="true" class="icon-clock-2 bk-widget-small-icon"></span>'. get_the_time( get_option('date_format') ) .'</span>';

		if( comments_open() ) {

			$content .= '<span class="widget-comment"><span aria-hidden="true" class="icon-comments bk-widget-small-icon"></span>';
			$content .= bk_get_comments_link( __('No comments yet', 'corpora_theme'),
					__('1 comment', 'corpora_theme'),
					__('%d comments', 'corpora_theme'),
					__('comments-link', 'corpora_theme'),
					__('Comments are off for this post', 'corpora_theme'));
			$content .= '</span>';

		}

		$content .= '</li>';

		endwhile;
		endif;

		wp_reset_query();

		$content .= '</ul>';

		return $content;
	}
}
/**
 * Link a Twitter user mentioned in the tweet text to the user's page on Twitter.
 *
 * @param array $matches regex match
 * @return string Tweet text with inserted @user link
 */
if ( !function_exists( '_wpcom_widget_twitter_username' ) ) {
	function _wpcom_widget_twitter_username( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]@<a href='" . esc_url( 'http://twitter.com/' . urlencode( $matches[3] ) ) . "'>$matches[3]</a>";
	}
}
/**
 * Link a Twitter hashtag with a search results page on Twitter.com
 *
 * @param array $matches regex match
 * @return string Tweet text with inserted #hashtag link
 */
if ( !function_exists( '_wpcom_widget_twitter_hashtag' ) ) {
	function _wpcom_widget_twitter_hashtag( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]<a href='" . esc_url( 'http://twitter.com/search?q=%23' . urlencode( $matches[3] ) ) . "'>#$matches[3]</a>";
	}
}
/*----------------------------------------------------------------------------------
 Print recent popular posts according to passed args and settings
----------------------------------------------------------------------------------*/
if( !function_exists('bk_get_tweets') ) {
	function bk_get_tweets($rest, $params, $args = array() ) {
		$auth = new TwitterOAuth(
				get_option('bk_twitter_consumer_key'),
				get_option('bk_twitter_consumer_secret'),
				get_option('bk_twitter_access_token'),
				get_option('bk_twitter_access_token_secret')
		);
		$get = $auth->get( $rest, $params );
		$res = array();
	
		if( !$get ) {
			$res['errors'] = __('An error occurs while reading the feed, please check your connection or settings', 'corpora_theme');
			return $res;
		}
	
		if( isset( $get->errors ) ) {
			foreach( $get->errors as $key => $val )
				$res['errors'] = $val->message;
	
			return $res;
		}
	
		if( isset( $get->error ) ) {
			$res['errors'] = $get->error;
			return $res;
		}
		
		$res = $get;
			
		return $res;
	}
}



if ( !function_exists( 'bk_get_twitter_widget' ) ) {
	function bk_get_twitter_widget( $account, $count, $follow_us_text ) {
		global $bk_unique_twitter_widget_id;
		$bk_unique_twitter_widget_id++;
		$content = '';

		$params = array(
				'screen_name' => $account, // Twitter account name
				'trim_user' => true, // only basic user data (slims the result)
				'include_entities' => false, // as of Sept 2010 entities were not included in all applicable Tweets. regex still better
				'exclude_replies' => false,
				'count' => $count,
				'include_rts' => false
		);
		
		$rest = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		
		$tweets = bk_get_tweets($rest, $params);

		if( $tweets ) {
			if( isset( $tweets['errors'] ) ) {
				$content .= $tweets['errors'];
		
			} else {
				$content .=  '<ul class="bk-recent-posts-widget bk-twitter-widget">';
				
				$tweets_out = 0;
				
				foreach ( (array) $tweets as $tweet ) {
					if ( $tweets_out >= $count )
						break;
				
					if ( empty( $tweet->text ) )
						continue;
				
					$text = make_clickable( esc_html( $tweet->text ) );
				
					$text = preg_replace_callback('/(^|[^0-9A-Z&\/]+)(#|\xef\xbc\x83)([0-9A-Z_]*[A-Z_]+[a-z0-9_\xc0-\xd6\xd8-\xf6\xf8\xff]*)/iu',  '_wpcom_widget_twitter_hashtag', $text);
					$text = preg_replace_callback('/([^a-zA-Z0-9_]|^)([@\xef\xbc\xa0]+)([a-zA-Z0-9_]{1,20})(\/[a-zA-Z][a-zA-Z0-9\x80-\xff-]{0,79})?/u', '_wpcom_widget_twitter_username', $text);
				
					if ( isset($tweet->id_str) )
						$tweet_id = urlencode($tweet->id_str);
					else
						$tweet_id = urlencode($tweet->id);
					$content .=  '<li>' . $text . '<span class="widget-time"><a href="' . esc_url( "http://twitter.com/" . $account . "/statuses/" . $tweet_id  ) . '" class="tweettime">' .  human_time_diff( strtotime($tweet->created_at), current_time('timestamp') ) . __(' ago', 'corpora_theme') . "</a></span></li>";
					unset($tweet_id);
					$tweets_out++;
				}
				
				if( !empty($follow_us_text) )
					$content .=  '<li><a target="_blank" href="' . esc_url( "http://twitter.com/" . $account ) . '">'  . $follow_us_text . '</a></li>';
				
				$content .=  "</ul>";
			}
		
		}
				
		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print recent popular posts according to passed args and settings
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_flickr_widget' ) ) {
	function bk_get_flickr_widget( $account, $count, $display ) {
		$content = '';

		$content .= '<div class="flickr-photos clearfix">';
		$content .= '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?user=' . $account . '&amp;display=' . $display . '&amp;source=user&amp;layout=h&amp;count=' . $count . '&amp;size=s"></script>';
		$content .= '</div>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Get dribble shots
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_dribble_shots' ) ) {
	function bk_get_dribble_shots($username) {
		$key = 'bk_dribbble_widget' . $username;
		$cache = get_transient($key);

		$dribbble_shots = '';

		if( $cache === false ) {
			$url 		= 'http://api.dribbble.com/players/' . $username . '/shots/?per_page=15';
			$response 	= wp_remote_get( $url );

			if( is_wp_error( $response ) )
				return;

			$xml = wp_remote_retrieve_body( $response );

			if( is_wp_error( $xml ) )
				return;

			if( $response['headers']['status'] == 200 ) {

				$json = json_decode( $xml );
				$dribbble_shots = $json->shots;

				set_transient($key, $dribbble_shots, 60*10);
			}
		} else {
			$dribbble_shots = $cache;
		}

		return $dribbble_shots;
	}
}
/*----------------------------------------------------------------------------------
 Get dribbble widget
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_dribbble_widget' ) ) {
	function bk_get_dribbble_widget( $account, $count ){
		$dribbble_shots = bk_get_dribble_shots($account);

		$content = '';

		if (!empty($dribbble_shots))  {
			$content .= '<ul class="bk-dribbble-shots clearfix">';
			$i = 0;

			foreach( $dribbble_shots as $dribbble_shot ) {
				if( $i == $count )
					break;

				$content .= '<li>';
				$content .= '<a target="_blank" href="' . $dribbble_shot->url . '">';
				$content .= '<img src="' . $dribbble_shot->image_teaser_url . '" alt="' . $dribbble_shot->title . '" /><div class="bk-img-overlay hoverable"></div>';
				$content .= '</a>';
				$content .= '</li>';

				$i++;
			}

			$content .= '</ul>';
		}

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print recent popular posts according to passed args and settings
----------------------------------------------------------------------------------*/
$unique_popular_posts_id = 0;

if ( !function_exists( 'bk_get_popular_posts_widget' ) ) {
	function bk_get_popular_posts_widget( $args, $prettyphoto_enabled ) {
		global $unique_popular_posts_id;
		$unique_popular_posts_id++;

		$content = '';

		$content .= '<ul class="bk-recent-posts-widget">';

		query_posts($args);
		if ( have_posts() ) : while ( have_posts() ) : the_post();

		$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' );
		$link = $large_image[0];

		if( $prettyphoto_enabled )
			$image_link = '<a class="jackbox" data-group="popularposts_' . $unique_popular_posts_id . '" href="' . $link . '">';
		else
			$image_link = '<a rel="bookmark" href="' . get_permalink() . '">';

		$content .= '<li class="clearfix">';

		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
			$content .= '<div class="widget-thumb img-frame left">';
			$content .= $image_link;
			$content .= get_the_post_thumbnail();
			$content .= '</a>';
			$content .= '</div>';
		}

		$content .= '<span class="widget-post-title"><a href="'. get_permalink() .'">'. bk_get_custom_text( get_the_title(), 5 ) .'</a></span>';
		$content .= '<span class="widget-time"><span aria-hidden="true" class="icon-clock-2 bk-widget-small-icon"></span>'. get_the_time( get_option('date_format') ) .'</span>';

		if( comments_open() ) {

			$content .= '<span class="widget-comment"><span aria-hidden="true" class="icon-comments bk-widget-small-icon"></span>';
			$content .= bk_get_comments_link( __('No comments yet', 'corpora_theme'),
					__('1 comment', 'corpora_theme'),
					__('%d comments', 'corpora_theme'),
					__('comments-link', 'corpora_theme'),
					__('Comments are off for this post', 'corpora_theme'));
			$content .= '</span>';

		}

		$content .= '</li>';

		endwhile;
		endif;

		wp_reset_query();

		$content .= '</ul>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print contact form widget
----------------------------------------------------------------------------------*/
if ( !function_exists( 'get_contact_form_widget' ) ) {
	function get_contact_form_widget( $button_text ) {

		$content = '';
		$content .= '<div class="contact-wrap clearfix">';
		$content .= '<div class="contact-response-wrap"></div>';
		$content .= '<div class="contact-form-wrap">';
		$content .= '<form class="bk-contact-form" action="#" method="post">';
		$content .= '<label for="firstname">' . __("Name", "corpora_theme") . '<span class="bk-contact-form-required">' . __("This field is required", "corpora_theme") . '</span></label>';
		$content .= '<input id="firstname" name="firstname" type="text" value=""/>';
		$content .= '<label for="subject">' . __("Subject", "corpora_theme") . '<span class="bk-contact-form-required">' . __("This field is required", "corpora_theme") . '</span></label>';
		$content .= '<input id="subject" name="subject" type="text" value=""/>';
		$content .= '<label for="email">' . __("Email", "corpora_theme") . '<span class="bk-contact-form-required bk-empty">' . __("This field is required", "corpora_theme") . '</span><span class="bk-contact-form-required bk-invalid">' . __("Please type in valid e-mail address", "corpora_theme") . '</span></label>';
		$content .= '<input id="email" name="email" type="text" value=""/>';
		$content .= '<label for="message">' . __("Message", "corpora_theme") . '<span class="bk-contact-form-required">' . __("This field is required", "corpora_theme") . '</span></label>';
		$content .= '<textarea id="message" name="message" ></textarea>';
		$content .= '<a href="#" class="submit bk_button normal square">' . $button_text . '<span class="ajax-loading-icon"></span></a>';
		$content .= '</form>';
		$content .= '</div>';
		$content .= '</div>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Print google map widget
----------------------------------------------------------------------------------*/
if ( !function_exists( 'get_google_map_widget' ) ) {
	function get_google_map_widget($google_map_code, $left_textarea, $right_textarea) {
		$content = '';
		$content .= '<div class="bk-google-map-wrap clearfix">';
		$content .= '<div class="bk-google-map-frame">' . $google_map_code . '</div>';

		$content .= '<div class="left-textarea"><p>' . $left_textarea . '</p></div>';
		$content .= '<div class="right-textarea"><p>' . $right_textarea . '</p></div>';

		$content .= '</div>';

		return $content;
	}
}

/*----------------------------------------------------------------------------------
 aspect ratio for fluid container
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_aspect_ratio_style' ) ) {
	function bk_get_aspect_ratio_style($width, $height) {
		$aspect = $height / $width * 100;
		$aspect = str_replace(",",".",$aspect);
		$aspect = 'style="padding-top: ' . $aspect . '%;"';
		return $aspect;
	}
}
/*----------------------------------------------------------------------------------
 Creates HTML5 or flash video markup from source file
----------------------------------------------------------------------------------*/
$bk_unique_increment_id = 0;

if ( !function_exists( 'bk_get_self_hosted_video_markup_from_link' ) ) {
	function bk_get_self_hosted_video_markup_from_link( $video_link = '', $poster_src = '', $post_id  = 0, $width = 0, $height = 0 ) {
		global $bk_unique_increment_id;
		$bk_unique_increment_id++;

		$aspect_ratio = '';
		if( ($width > 0) && ($height > 0) ) {
			$aspect_ratio = bk_get_aspect_ratio_style($width, $height);
		}

		$post_id = $post_id . '_' . $bk_unique_increment_id;

		$content = '';

		$supplied_formats = '';

		$content .= '<script type="text/javascript">';
		$content .= 'jQuery(document).ready(function(){';
		$content .= 'jQuery("#jquery_jplayer_' . $post_id . '").jPlayer({';
		$content .= 'ready: function () {';
		$content .= 'jQuery(this).jPlayer("setMedia", {';

		$settings = '';

		if( preg_match("/\b\.m4v\b/i", $video_link[0] ) ) {
			$settings .= 'm4v: "' . $video_link[0] . '",';
			$supplied_formats .= "m4v,";
		}

		if( preg_match("/\b\.mp4\b/i", $video_link[0] ) ) {
			$settings .= 'm4v: "' . $video_link[0] . '",';
			$supplied_formats .= "m4v,";
		}

		if( preg_match("/\b\.ogv\b/i", $video_link[1] ) ) {
			$settings .= 'ogv: "' . $video_link[1] . '",';
			$supplied_formats .= "ogv";
		}

		if( preg_match("/\b\.webm\b/i", $video_link[1] ) ) {
			$settings .= 'webm: "' . $video_link[1] . '",';
			$supplied_formats .= "webm";
		}

		if( !empty($poster_src) )
			$settings .= 'poster: "' . $poster_src . '",';

		$supplied_formats = trim($supplied_formats, ",");
		$settings = trim($settings, ",");

		$content .= $settings;

		$content .= '});';
		$content .= '},';
		$content .= 'swfPath: "' . get_template_directory_uri() . '/js",';
		$content .= 'cssSelectorAncestor: "#jp_interface_' . $post_id . '",';
		$content .= 'size: {width: "100%", height: "100%"},';
		$content .= 'solution: "html, flash",';
		$content .= 'supplied: "' . $supplied_formats . '"';
		$content .= '});';
		$content .= '});';
		$content .= '</script>';

		$content .= '<div ' . $aspect_ratio . ' class="fluid-width-video-wrapper self-hosted"><div class="fluid-width-video-contaner"><div id="jquery_jplayer_' . $post_id . '" class="jp-jplayer"></div></div></div>';
		$content .= '<div class="jp-video">';
		$content .= '<div class="jp-type-single">';
		$content .= '<div id="jp_interface_' . $post_id . '" class="jp-interface">';
		$content .= '<ul class="jp-controls">';
		$content .= '<li><a href="#" class="jp-play" tabindex="1">play</a></li>';
		$content .= '<li><a href="#" class="jp-pause" tabindex="1">pause</a></li>';
		$content .= '<li><a href="#" class="jp-mute" tabindex="1">mute</a></li>';
		$content .= '<li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>';
		$content .= '</ul>';
		$content .= '<div class="jp-progress-wrapper">';
		$content .= '<div class="jp-progress">';
		$content .= '<div class="jp-seek-bar">';
		$content .= '<div class="jp-play-bar"></div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="jp-volume-bar-wrapper">';
		$content .= '<div class="jp-volume-bar">';
		$content .= '<div class="jp-volume-bar-value"></div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="jp-divider left-side"></div>';
		$content .= '<div class="jp-divider right-side"></div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Creates audio jplayer markup form source file
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_audio_markup_from_link' ) ) {
	function bk_get_audio_markup_from_link( $audio_link = '', $post_id = 0 ) {
		global $bk_unique_increment_id;
		$bk_unique_increment_id++;

		$post_id = $post_id . '_' . $bk_unique_increment_id;

		$content = '';

		$content .= '<script type="text/javascript">';
		$content .= 'jQuery(document).ready(function(){';
		$content .= 'jQuery("#jquery_jplayer_' . $post_id . '").jPlayer({';
		$content .= 'ready: function () {';
		$content .= 'jQuery(this).jPlayer("setMedia", {';
		$content .= 'mp3: "' . $audio_link . '"';
		$content .= '});';
		$content .= '},';
		$content .= 'swfPath: "' . get_template_directory_uri() . '/js",';
		$content .= 'cssSelectorAncestor: "#jp_interface_' . $post_id . '",';
		$content .= 'supplied: "mp3"';
		$content .= '});';
		$content .= '});';
		$content .= '</script>';

		$content .= '<div id="jquery_jplayer_' . $post_id . '" class="jp-jplayer"></div>';
		$content .= '<div class="jp-audio">';
		$content .= '<div class="jp-type-single">';
		$content .= '<div id="jp_interface_' . $post_id . '" class="jp-interface">';
		$content .= '<ul class="jp-controls">';
		$content .= '<li><a href="#" class="jp-play" tabindex="1">play</a></li>';
		$content .= '<li><a href="#" class="jp-pause" tabindex="1">pause</a></li>';
		$content .= '<li><a href="#" class="jp-mute" tabindex="1">mute</a></li>';
		$content .= '<li><a href="#" class="jp-unmute" tabindex="1">unmute</a></li>';
		$content .= '</ul>';
		$content .= '<div class="jp-progress-wrapper">';
		$content .= '<div class="jp-progress">';
		$content .= '<div class="jp-seek-bar">';
		$content .= '<div class="jp-play-bar"></div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="jp-volume-bar-wrapper">';
		$content .= '<div class="jp-volume-bar">';
		$content .= '<div class="jp-volume-bar-value"></div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '<div class="jp-divider left-side"></div>';
		$content .= '<div class="jp-divider right-side"></div>';
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Creates youtube markup in iframe from link extracting all arguments like width, height etc
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_youtube_markup_from_link' ) ) {
	function bk_get_youtube_markup_from_link( $video_link = '', $width = 16, $height = 9 ) {
		$content = '';

		$query = parse_url($video_link, PHP_URL_QUERY);					// whole query like v=dfklasfjsdlafjsd?width=600&height=400 for Youtube
		$additional_args = parse_url($query, PHP_URL_QUERY);    // additional args like width=600&height=400 for Youtube

		preg_match("/^v=([a-z0-9_-]*)/i", $query, $matches);		// get video ID
		$video_id = $matches[1];

		$rel = '1';
		if( preg_match("/rel=([0-9]{1})/i", $additional_args, $matches) )		// get video rel
			$rel = $matches[1];

		$autoplay = '0';
		if( preg_match("/autoplay=([0-9]{1})/i", $additional_args, $matches) )		// get video autoplay flag
			$autoplay = $matches[1];


		$aspect_ratio = '';
		if( ($width > 0) && ($height > 0) ) {
			$aspect_ratio = bk_get_aspect_ratio_style($width, $height);
		}

		$content .= '<div ' . $aspect_ratio . ' class="fluid-width-video-wrapper bk-youtube-video">';
		$content .= '<iframe src="http://www.youtube.com/embed/' . $video_id . '?enablejsapi=1&amp;wmode=transparent&amp;rel=' . $rel . '&amp;autoplay=' . $autoplay . '" width="100%" height="100%" frameborder="no"></iframe>';
		$content .= '</div>';

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Creates vimeo markup in iframe from link extracting all arguments like width, height etc
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_vimeo_markup_from_link' ) ) {
	function bk_get_vimeo_markup_from_link( $video_link = '', $width = 16, $height = 9 ) {

		$additional_args = parse_url($video_link, PHP_URL_QUERY);    // additional args like width=600&height=400 for Youtube

		preg_match("/(vimeo)\.com\/([0-9]*)/i", $video_link, $matches);		// get video ID
		$video_id = $matches[2];

		$color = '00adef';
		if( preg_match("/color=([0-9a-f]{6})/i", $additional_args, $matches) )		// get video color param
			$color = $matches[1];

		$autoplay = '0';
		if( preg_match("/autoplay=([0-9]{1})/i", $additional_args, $matches) )		// get video autoplay flag
			$autoplay = $matches[1];

		$content = '';

		$aspect_ratio = '';
		if( ($width > 0) && ($height > 0) ) {
			$aspect_ratio = bk_get_aspect_ratio_style($width, $height);
		}

		$content .= '<div ' . $aspect_ratio . ' class="fluid-width-video-wrapper bk-vimeo-video">';
		$content .= '<iframe src="http://player.vimeo.com/video/' . $video_id . '?api=1&amp;title=0&amp;byline=0&amp;portrait=0&amp;autoplay=' . $autoplay . '&amp;color=' . $color . '" width="100%" height="100%" frameborder="no"></iframe>';
		$content .= '</div>';

		return $content;
	}
}
/*-----------------------------------------------------------------------------------
 Change default comment excerpt length
-----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_custom_text' ) ) {
function bk_get_custom_text($excerpt = null, $max_words_count = 10) {

	$retval = $excerpt;
	$array = explode(" ", $excerpt);
	if (count($array)<=$max_words_count) {
		$retval = $excerpt;
	} else {
		array_splice($array, $max_words_count);
		$retval = implode(" ", $array) . " ...";
	}
	return $retval;
}
}

if ( !function_exists( 'bk_custom_comment_excerpt' ) ) {
function bk_custom_comment_excerpt($excerpt)
{
	$max_words_count = 7;
	return bk_get_custom_text($excerpt, $max_words_count);
}
}

add_filter('get_comment_excerpt', 'bk_custom_comment_excerpt');

/*-----------------------------------------------------------------------------------
 Change default excerpt length
-----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_excerpt_length' ) ) {
function bk_excerpt_length($length) {
	global $post;
	if ($post->post_type == 'post')
		return get_option('bk_blog_post_excerpt_length', 50);
	else if ($post->post_type == 'portfolio')
		return 20;
	else
		return 55;
}
}
add_filter('excerpt_length', 'bk_excerpt_length', 999);

/*-----------------------------------------------------------------------------------
 Change default excerpt string
-----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_excerpt_more' ) ) {
function bk_excerpt_more($excerpt) {
	return str_replace('[...]', '...', $excerpt);
}
}
add_filter('wp_trim_excerpt', 'bk_excerpt_more');

/*----------------------------------------------------------------------------------
 get image id from original upload url
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_image_id' ) ) {
function bk_get_image_id($image_url) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url));
	if( isset( $attachment[0] ) ) {
		return $attachment[0];
	} else {
      return $image_url;
	}
}
}

/*----------------------------------------------------------------------------------
 Get post publish time - returns post formated according to global date settings with links to archives
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_publish_time' ) ) {
function bk_get_publish_time() {
	$year = get_the_time( 'Y' );
	$month = get_the_time( 'm' );
	$day = get_the_time( 'd' );
	$out = '';

	/* Add a link to the monthly archive. */
	$out .= '<a href="' . get_month_link( $year, $month ) . '" title="Archive for ' . esc_attr( get_the_time( 'F Y' ) ) . '">' . get_the_time( 'F' ) . '</a>';

	/* Add a link to the daily archive. */
	$out .= ' <a href="' . get_day_link( $year, $month, $day ) . '" title="Archive for ' . esc_attr( get_the_time( 'F d, Y' ) ) . '">' . $day . '</a>';

	/* Add a link to the yearly archive. */
	$out .= ', <a href="' . get_year_link( $year ) . '" title="Archive for ' . esc_attr( $year ) . '">' . $year . '</a>';

	print $out;
}
}

/*----------------------------------------------------------------------------------
 Pagination
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_pagination' ) ) {
function bk_pagination($pages = '', $range = 2, $echo = true, $classes = '')
{
	$showitems = ($range * 2)+1;

	$paged = 1;
	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	}

	if($pages == '')
	{
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if(!$pages)
		{
			$pages = 1;
		}
	}

	$content = '';

	if(1 != $pages)
	{
		$content .= '<div class="bk-pagination-wrap clearfix bk-hidden ' . $classes . '">';

		$content .= '<ul class="bk-pagination ' . $classes . '">';

		if($paged > 1)
			$content .= '<li><a href="'.get_pagenum_link($paged - 1) .'">' . __("Prev", "corpora_theme") . '</a></li>';
			
		if($paged > 2 && $paged > $range+1 && $showitems < $pages) {
			$content .= '<li><a href="'.get_pagenum_link(1) .'">1</a></li>';
			$content .= '<li class="no-hover"><span class="pagination-divider">...</span></li>';
		}

		for ($i=1; $i <= $pages; $i++)
		{
			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ) )
			{
				if( $paged == $i )
					$content .= '<li class="current"><a>'.$i.'</a></li>';
				else
					$content .= '<li><a href="'.get_pagenum_link($i) .'" class="inactive" >'.$i.'</a></li>';
			}
		}

		if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
			$content .= '<li class="no-hover"><span class="pagination-divider">...</span></li>';
			$content .= '<li><a href="'.get_pagenum_link($pages) .'">' . $pages . '</a></li>';
		}

		if ($paged < $pages )
			$content .= '<li><a href="'.get_pagenum_link($paged + 1) .'">' . __("Next", "corpora_theme") . '</a></li>';


		$content .= '</ul>';


		$content .= '</div>';

		//$content .= "<span class='pages'>". __('Page', 'corpora_theme') ." $paged ". __('of', 'corpora_theme') . " $pages</span>";
	}

	if( $echo )
		echo $content;
	else
		return $content;
}
}

/*----------------------------------------------------------------------------------
 Get comments link funtcion
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_comments_link' ) ) {
function bk_get_comments_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	$id = get_the_ID();
	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() )
		return '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $none . '</span>';

	if ( post_password_required() )
		return __('Enter your password to view comments', 'corpora_theme');

	$output = '';

	$output .= '<a href="';

	if ( 0 == $number )
		$output .= get_permalink() . '#respond';
	else
		$output .= get_comments_link();

	$output .= '"';

	if ( !empty( $css_class ) )
		$output .= ' class="'.$css_class.'" ';

	$title = the_title_attribute( array('echo' => 0 ) );

	$output .= ' title="' . esc_attr( sprintf( __('Comment on %s', 'corpora_theme' ), $title ) ) . '">';

	if($number == 0){
		$output .= $zero;
	} elseif($number > 1){
		$output .= esc_attr( sprintf( $more, $number ) );
	} else{
		$output .= $one;
	}

	$output .= '</a>';
	return $output;
}
}

/*
 get post featured image
*/
if ( !function_exists( 'bk_get_featured_image' ) ) {
function bk_get_featured_image($post_id) {
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	if ($post_thumbnail_id) {
		$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
		return $post_thumbnail_img[0];
	}
}
}

if ( !function_exists( 'bk_comments' ) ) {
function bk_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
	case 'pingback' :
	case 'trackback' :
		?>
<li class="post pingback">
	<p>
		<?php _e( 'Pingback:', 'corpora_theme' ); ?>
		<?php comment_author_link(); ?>
		<?php edit_comment_link( __( '[ Edit ]', 'corpora_theme' ), '<span class="edit-link right">', '</span>' ); ?>
	</p> <?php
	break;
default :
	?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment clear_both">
		<?php
		$avatar_size = 120;
		if ( '0' != $comment->comment_parent )
			$avatar_size = 80;


		echo '<div class="comment-avatar left">';
		echo get_avatar( $comment, $avatar_size );
		echo '</div>';
		?>
		<div class="content-wrap">
			<span class="comment-tip"></span>
			<div class="comment-meta">
				<?php
				/* translators: 1: comment author, 2: date and time */
				printf( __( '%1$s on %2$s ', 'corpora_theme' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							/* translators: 1: date, 2: time */
							sprintf( __( '%1$s at %2$s', 'corpora_theme' ), get_comment_date(), get_comment_time() )
							)
						);
						?>

				<?php edit_comment_link( __( 'Edit', 'corpora_theme' ), '<span class="edit-link right">', '</span>' ); ?>
			</div>
			<!-- .comment-author .vcard -->

			<?php if ( $comment->comment_approved == '0' ) : ?>
			<p>
				<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'corpora_theme' ); ?>
				</em>
			</p>
			<?php endif; ?>

			<p>
				<?php comment_text(); ?>
			</p>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'corpora_theme' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div>
			<!-- .reply -->
		</div>
	</div> <!-- #comment-## --> <?php
	break;
	endswitch;
}
}

/*
 * Query to see if woocommerce plugin is activated
 */
if ( ! function_exists( 'bk_is_woocommerce_activated' ) ) {
	function bk_is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { 
			return true; 
		} else { 
			return false; 
		}
	}
}


/*----------------------------------------------------------------------------------
 Enable post thumbnails theme support ver 2.9 +
----------------------------------------------------------------------------------*/

if ( function_exists( 'add_theme_support' ) ) {

	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio', 'link', 'quote', 'aside', 'status', 'chat' ) );
	add_post_type_support( 'post', 'post-formats', array( 'image', 'gallery', 'video', 'audio', 'link', 'quote', 'aside', 'status', 'chat' ) );
	add_post_type_support( 'portfolio', 'post-formats', array( 'gallery', 'video', 'audio' ) );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150, true ); // default post thumbnail

	add_image_size( 'portfolio-grid-1-1', 480, 297, true ); // portfolio grid, portfolio metro
	add_image_size( 'portfolio-grid-1-2', 480, 594, true ); // portfolio metro
	add_image_size( 'portfolio-grid-2-1', 960, 297, true ); // portfolio metro
	add_image_size( 'portfolio-grid-2-2', 960, 594, true ); // portfolio metro

	add_image_size( 'portfolio-masonry-small', 480, 9999, false ); // portfolio masonry - small
	add_image_size( 'portfolio-masonry-large', 960, 9999, false ); // portfolio masonry - large

	add_image_size( 'blog-thumb-cropped', 480, 297, true ); // blog cropped
	add_image_size( 'blog-thumb-prop', 480, 9999, false ); // blog prop

	add_image_size( 'popup-gallery-thumb', 150, 150, true );
}

/*----------------------------------------------------------------------------------
 Includes
----------------------------------------------------------------------------------*/
if( is_admin() ) {
	// Add filed factory class
	include("admin/bk_field_factory.class.php");

	// Add shortcode manager class
	include("admin/bk_shortcode_manager.class.php");

	// Add admin interface
	include("admin/admin.php");
}

if( bk_is_woocommerce_activated() ) {
    // Add WooCommerce functions
    include("includes/functions-woocommerce.php");
}


// Add twiter oauth lib
include('includes/twitteroauth/twitteroauth.php');

// Add admin theme customizer
include("admin/theme-customizer.php");

// Add page builder class
include("admin/bk_page_builder.class.php");

// Add gallery manager class
include("admin/bk_gallery_manager.class.php");

// Add post page portfolio common custome metaboxes
include("includes/post-page-custom-fields.php");

// Add post page portfolio common custome metaboxes
include("includes/portfolio-type.php");

// Add post page portfolio common custome metaboxes
include("includes/gallery-type.php");

// Add post page portfolio common custome metaboxes
include("includes/quick-gallery-type.php");

// Add post common custome metaboxes
include("includes/post-type.php");

// Add testimonial type
include("includes/testimonial-type.php");

// Add pricing table type
include("includes/pricing-table-type.php");

// Add post page portfolio common custome metaboxes
include("includes/shortcodes.php");

// Add widgets
include("includes/widget-recent-portfolio.php");

// Add portfolio widget filter
include("includes/widget-filter.php");

// Add Recent Posts Widget
include("includes/widget-recent-posts.php");

// Add Recent Posts Widget
include("includes/widget-popular-posts.php");

// Add Recent Posts Widget
include("includes/widget-recent-comments.php");

// Add Twitter Widget
include("includes/widget-twitter.php");

// Add Flickr Widget
include("includes/widget-flickr.php");

// Add Pinterest Widget
include("includes/widget-pinterest.php");

// Add Dribbble Widget
include("includes/widget-dribbble.php");

// Add Testimonials Widgets
include("includes/widget-testimonials.php");

// Add Newsletter Widget
include("includes/widget-contact-form.php");

// Add Newsletter Widget
include("includes/widget-company-map.php");
/*----------------------------------------------------------------------------------

----------------------------------------------------------------------------------*/


?>