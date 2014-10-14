<?php
/*
 Column layout shortcodes
*/
if ( !function_exists( 'bk_make_column' ) ) {
	function bk_make_column($atts, $content = null, $code) {
		return '<div class="' . $code . '">' . do_shortcode( $content )  . '</div>';
	}
}
if ( !function_exists( 'bk_make_column_last' ) ) {
	function bk_make_column_last($atts, $content = null, $code) {
		return '<div class="' . str_replace('_last',' last',$code) . '">' . do_shortcode( $content ) . '</div><div class="clear_both"></div>';
	}
}
if ( !function_exists( 'bk_make_fullwidth_column' ) ) {
	function bk_make_fullwidth_column($atts, $content = null, $code) {
		return do_shortcode( $content );
	}
}
if ( !function_exists( 'bk_make_column_content' ) ) {
	function bk_make_column_content($atts, $content = null, $code) {
		return do_shortcode( $content );
	}
}
add_shortcode('bk_column', 'bk_make_column_content');

add_shortcode('one_one', 'bk_make_fullwidth_column');
add_shortcode('one_one_last', 'bk_make_fullwidth_column');
add_shortcode('one_one_first', 'bk_make_fullwidth_column');

add_shortcode('one_half', 'bk_make_column');
add_shortcode('one_third', 'bk_make_column');
add_shortcode('one_fourth', 'bk_make_column');
add_shortcode('one_fifth', 'bk_make_column');
add_shortcode('one_sixth', 'bk_make_column');

add_shortcode('two_sixth', 'bk_make_column');

add_shortcode('two_third', 'bk_make_column');
add_shortcode('three_fourth', 'bk_make_column');
add_shortcode('two_fifth', 'bk_make_column');
add_shortcode('three_fifth', 'bk_make_column');
add_shortcode('four_fifth', 'bk_make_column');
add_shortcode('five_sixth', 'bk_make_column');

add_shortcode('one_half_last', 'bk_make_column_last');
add_shortcode('one_third_last', 'bk_make_column_last');
add_shortcode('one_fourth_last', 'bk_make_column_last');
add_shortcode('one_fifth_last', 'bk_make_column_last');
add_shortcode('one_sixth_last', 'bk_make_column_last');

add_shortcode('two_sixth_last', 'bk_make_column');

add_shortcode('two_third_last', 'bk_make_column_last');
add_shortcode('three_fourth_last', 'bk_make_column_last');
add_shortcode('two_fifth_last', 'bk_make_column_last');
add_shortcode('three_fifth_last', 'bk_make_column_last');
add_shortcode('four_fifth_last', 'bk_make_column_last');
add_shortcode('five_sixth_last', 'bk_make_column_last');


/*
 contact form widget widget
*/
if ( !function_exists( 'bk_contact_form_widget' ) ) {
	function bk_contact_form_widget($atts, $content=null){

		extract(shortcode_atts( array('button_text' => 'Send'), $atts));

		return get_contact_form_widget($button_text);
	}
}
add_shortcode('bk_contact_form_widget', 'bk_contact_form_widget');


/*
 subscribe newsletter widget
*/
if ( !function_exists( 'bk_subscribe_newsletter_widget' ) ) {
	function bk_subscribe_newsletter_widget($atts, $content=null){

		extract(shortcode_atts( array('text' => null,
		'button' => 'gray',
		'button_text' => 'Subscribe'), $atts));

		return get_subscribe_newsletter_widget($text, $button, $button_text);
	}
}
add_shortcode('bk_subscribe_newsletter_widget', 'bk_subscribe_newsletter_widget');

/*
 testimonials widget
*/
if ( !function_exists( 'bk_testimonials_widget' ) ) {
	function bk_testimonials_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'autoplay' => 0), $atts));

		$args= array(
				'posts_per_page' => $count,
				'post_type' => 'testimonial'
		);

		return bk_get_testimonial_items_widget( $args, $autoplay );
	}
}
add_shortcode('bk_testimonials_widget', 'bk_testimonials_widget');

/*
 Recent posts widget
*/
if ( !function_exists( 'bk_recent_posts_widget' ) ) {
	function bk_recent_posts_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'prettyphoto_enabled' => 0), $atts));

		$args= array(
				'posts_per_page' => $count,
				'post_type' => 'post'
		);

		return bk_get_recent_posts_widget( $args, $prettyphoto_enabled );
	}
}
add_shortcode('bk_recent_posts_widget', 'bk_recent_posts_widget');

/*
 Recent portfolio widget
*/
if ( !function_exists( 'bk_recent_portfolio_widget' ) ) {
	function bk_recent_portfolio_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'prettyphoto_enabled' => 0), $atts));

		$args= array(
				'posts_per_page' => $count,
				'post_type' => 'portfolio'
		);

		return bk_get_recent_portfolio_items_widget( $args, $prettyphoto_enabled );
	}
}
add_shortcode('bk_recent_portfolio_widget', 'bk_recent_portfolio_widget');
/*
 Recent comments widget
*/
if ( !function_exists( 'bk_recent_comments_widget' ) ) {
	function bk_recent_comments_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3), $atts));

		$args = array(
				'status' => 'approve',
				'number' => $count,
		);

		return bk_get_recent_comments_widget( $args );
	}
}
add_shortcode('bk_recent_comments_widget', 'bk_recent_comments_widget');

/*
 Popular posts widget
*/
if ( !function_exists( 'bk_popular_posts_widget' ) ) {
	function bk_popular_posts_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'prettyphoto_enabled' => 0), $atts));

		$args= array(
				'posts_per_page' => $count,
				'post_type' => 'post',
				'orderby' => 'comment_count',
				'order' => 'DESC'
		);

		return bk_get_popular_posts_widget( $args, $prettyphoto_enabled );
	}
}
add_shortcode('bk_popular_posts_widget', 'bk_popular_posts_widget');

/*
 Twitter widget
*/
if ( !function_exists( 'bk_twitter_widget' ) ) {
	function bk_twitter_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'account' => '',
		'follow_us_text' => ''), $atts));



		return bk_get_twitter_widget( $account, $count, $follow_us_text );
	}
}
add_shortcode('bk_twitter_widget', 'bk_twitter_widget');

/*
 Twitter widget
*/
if ( !function_exists( 'bk_flickr_widget' ) ) {
	function bk_flickr_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'account' => '',
		'display' => ''), $atts));



		return bk_get_flickr_widget( $account, $count, $display );
	}
}
add_shortcode('bk_flickr_widget', 'bk_flickr_widget');

/*
 Dribbble Widget
*/
if ( !function_exists( 'bk_dribbble_widget' ) ) {
	function bk_dribbble_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'account' => ''), $atts));



		return bk_get_dribbble_widget( $account, $count );
	}
}
add_shortcode('bk_dribbble_widget', 'bk_dribbble_widget');

/*
 Pinterest Widget
*/
if ( !function_exists( 'bk_pinterest_widget' ) ) {
	function bk_pinterest_widget($atts, $content=null){

		extract(shortcode_atts( array('count' => 3,
		'account' => ''), $atts));



		return bk_get_pinterest_widget($account, $count);
	}
}
add_shortcode('bk_pinterest_widget', 'bk_pinterest_widget');


/*
 dropcap
*/
if ( !function_exists( 'bk_dropcap' ) ) {
	function bk_dropcap($atts, $content=null, $code){
		extract(shortcode_atts( array('dropcap_custom_colors' => 1,
		'color' => '#ffffff',
		'background_color' => '#393737',
		'type' => 'round') , $atts));

		if( $type == 'plain' ) {
			$background_color = 'transparent';
		}

		$style = '';
		if( $dropcap_custom_colors )
			$style = ' style="color:' . $color . '; background-color:' . $background_color . ';"';

		return '<span ' . $style . ' class="dropcap ' . $type . '">' . $content . "</span>";
	}
}
add_shortcode('bk_dropcap', 'bk_dropcap');

/*
 content separator
*/
if ( !function_exists( 'bk_separator' ) ) {
	function bk_separator($atts, $content=null, $code){

		extract(shortcode_atts( array('text' => '',
		'type' => 'content-separator',
		'margin' => 30), $atts));

		if( '' != $text && $type == 'content-separator' )
			$inner = '<span class="title">' . $text . '</span>';

		$style = ' style="margin-bottom:' . $margin . 'px"';

		return '<div ' . $style . ' class="clearfix ' . $type . '">' . $inner . '</div>';
	}
}
add_shortcode('bk_separator', 'bk_separator');

/*
 buttons
*/
$bk_button_unique_id = 0;

if ( !function_exists( 'bk_button' ) ) {
	function bk_button($atts, $content=null, $code){

		extract(shortcode_atts( array( 'size' => 'normal',						 // size - small, normal, large => default normal
		'button_custom_colors' => 0,				 // if custom colors
		'color' => '#ffffff',
		'hover_color' => '#ffffff',
		'link' => '',
		'target' => '_self' ), $atts));

		$classes = array();
		$classes[] = $size;

		$classes = implode(" ", $classes);

		global $bk_button_unique_id;
		$bk_button_unique_id++;

		$bk_button_unique_id_string = 'bk_button_' . $bk_button_unique_id;

		$content = '<a id="' . $bk_button_unique_id_string . '" href="' . $link . '" target="' . $target . '" class="bk_button ' . $classes . '">' . $content . '</a>';
		if( $button_custom_colors ) {
			$content .= '<style type="text/css">#' . $bk_button_unique_id_string . '{color:' . $color . '; border-color:' . $color . ';} #' . $bk_button_unique_id_string . ':hover{color:' . $hover_color . '; border-color:' . $hover_color . ';}</style>';
		}

		return $content;
	}
}
add_shortcode('bk_button', 'bk_button');

/*
 buttons
*/
if ( !function_exists( 'bk_big_text' ) ) {
	function bk_big_text($atts, $content=null, $code){

		extract(shortcode_atts( array( 'size' => 60						 // size in pixels
		), $atts));

		$style = 'style="font-size: ' . $size . 'px; line-height: ' . $size . 'px;"';

		return '<span ' . $style . ' class="bk_big_text">' . $content . '</span>';
	}
}
add_shortcode('bk_big_text', 'bk_big_text');

/*
 highlight
*/
if ( !function_exists( 'bk_highlight' ) ) {
	function bk_highlight($atts, $content=null, $code){

		extract(shortcode_atts( array( 'highlight_custom_colors' => 0,					 // if custom colors
		'color' => '#ffffff',
		'background_color' => '#555555',						 // color - gray, dark_gray, red, orange, green, blue, purple, teal, navy, black => default gray
		), $atts));

		$style = '';

		if( $highlight_custom_colors )
			$style = 'style="color:' . $color . '; background-color:' . $background_color . ';"';

		return '<em ' . $style . ' class="bk_highlight">' . do_shortcode( $content ) . '</em>';
	}
}
add_shortcode('bk_highlight', 'bk_highlight');

/*
 social icons
*/
if ( !function_exists( 'bk_social' ) ) {
	function bk_social($atts, $content=null, $code){

		extract(shortcode_atts( array( 'icon' => '',							 // color - gray, dark_gray, red, orange, green, blue,
		'text' => '',
		'link' => '#',
		'target' => '_self'
				), $atts));

		return '<a class="bk-social-icon ' . $icon . '" title="' . $text . '" target="' . $target . '" href="' . $link . '"></a>';
	}
}
add_shortcode('bk_social', 'bk_social');

/*
 pricing table
*/
if ( !function_exists( 'bk_pricing_table' ) ) {
	function bk_pricing_table($atts, $content=null, $code){

		extract(shortcode_atts( array( 'pricing_table_id' => -1							 // post id of custom pricing table post format
		), $atts));

		$markup = '';

		if( $pricing_table_id != -1 ) {
			$pricing_table = get_post_meta($pricing_table_id, '_bk_pricing_table' , true);
			if( !empty( $pricing_table ) ) {
				$markup .= '<div class="bk-pricing-table-outer-wrap clearfix">';
				$recommended_id = intval($pricing_table['recommended']);
				$columns_count = count($pricing_table['columns']);
				$regular_column = 100 / $columns_count;  // regular column 100% divided by column count
				$recommended_column = $regular_column + 5; // regula plus 10%
				if( $recommended_column > 100 )
					$recommended_column = 100;

				if( $columns_count > 1 )
					$regular_column = ( 100 - $recommended_column	) / ( $columns_count - 1 );

				foreach( $pricing_table['columns'] as $column_index => $column ) {
					$additional_classes = array();
					$style = 'style="width: ' . $regular_column . '%;"';
					if( $column_index == $recommended_id ) {
						$additional_classes[] = 'bk-recommended-column';
						$style = 'style="width: ' . $recommended_column . '%;"';
					}	else if( $column_index == ( $recommended_id - 1 ) ) {
						$additional_classes[] = 'bk-before-recommended-column';
					}	else if( $column_index == ( $recommended_id + 1 ) ) {
						$additional_classes[] = 'bk-after-recommended-column';
					}

					if( $column_index == 0 ) {
						$additional_classes[] = 'bk-first-column';
					} else if( $column_index == ( $columns_count - 1 ) ) {
						$additional_classes[] = 'bk-last-column';
					}

					$markup .= '<div ' . $style . ' class="bk-pricing-table-column-wrap ' . implode(' ', $additional_classes) . '">';

					if( $column_index == $recommended_id ) {
						$markup .= '<div class="bk-recommended-header-branding-line"></div>';
					}

					$markup .= '<div class="bk-pricing-table-header">';
					$markup .= '<h4 class="bk-pricing-table-title">' . $column['title'] . '</h4>';
					$markup .= '<h5 class="bk-pricing-table-price">' . $column['price'] . '</h5>';
					$markup .= '<span class="bk-pricing-table-description">' . $column['description'] . '</span>';
					$markup .= '</div>';

					foreach( $column['features'] as $key => $feature ) {
						if( !empty($feature) ) {
							if( $key % 2 != 1 ) {
								$markup .= '<div class="bk-pricing-table-feature bk_alternate">' . $feature . '</div>';
							} else {
								$markup .= '<div class="bk-pricing-table-feature">' . $feature . '</div>';
							}

						}
					}

					$markup .= '<div class="bk-pricing-table-footer">';
					$markup .= '<a href="' . $column['button_link'] . '" class="bk_button normal theme-default">' . $column['button_text'] . '</a>';
					$markup .= '</div>';

					$markup .= '</div>';
				}
				$markup .= '</div>';
			}
		}

		return $markup;
	}
}
add_shortcode('bk_pricing_table', 'bk_pricing_table');

/*
 * progress bars
*
*/
if ( !function_exists( 'bk_progress_bar' ) ) {
	function bk_progress_bar($atts, $content=null, $code){
		extract(shortcode_atts( array('progress_bar_custom_colors' => 0,
		'progress' => 50,
		'animated' => 1,
		'overlay' => 1,
		'text' => '',
		'color' => '#FF4629',
		'background_color' => '#f8f8f8'), $atts));

		$progress_bar_animated = ( $animated == 1 ) ? 'animated' : '';

		$progress_bar = '<div data-progress="' . $progress . '" class="bk-progress-bar-wrap ' . $progress_bar_animated . '">';
		$progress_bar .= '<span class="bk-progress-bar-text">' . $text . '</span>';

		$progress_bar_counter = ( $animated == 1 ) ? '0' : $progress;

		$progress_bar .= '<span class="bk-progress-bar-counter">' . $progress_bar_counter . '</span><span class="bk-progress-bar-suffix">%</span>';

		$base_style = '';
		$bar_style = '';

		if( $progress_bar_custom_colors ) {
			$base_style = 'background-color:' . $background_color . ';';
			$bar_style = 'background-color:' . $color . ';';
		}

		if( !$animated ) {
			$bar_style .= 'width:' . $progress . '%;';
		}

		if( !empty($base_style) ) {
			$base_style = 'style="' . $base_style . '"';
		}

		if( !empty($bar_style) ) {
			$bar_style = 'style="' . $bar_style . '"';
		}

		$progress_bar .= '<div ' . $base_style . ' class="bk-progress-bar-base"><div ' . $bar_style . ' class="bk-progress-bar">';

		$progress_bar .= ($overlay == 1) ? '<div class="bk-progress-bar-overlay"></div>' : '';

		$progress_bar .= '</div></div>';

		$progress_bar .= '</div>';

		return $progress_bar;
	}
}
add_shortcode('bk_progress_bar', 'bk_progress_bar');

/*
 tabs
*/
$tab_cnt_1 = 0;
$tab_cnt_2 = 0;
$bk_tabs_titles = array();

if ( !function_exists( 'bk_tabs' ) ) {
	function bk_tabs($atts, $content=null, $code){
		extract(shortcode_atts( array('default_icon' => 'none'), $atts));

		global $tab_cnt_1;
		global $tab_cnt_2;
		global $bk_tabs_titles;

		$out = '</ul>';
		$out .= do_shortcode( $content );
		$out .= '</div>';

		$top = '<div class="bk-tabs"><ul class="bk-tabs-nav clearfix">';

		$count = 0; // local number used to build relation title => icon

		foreach( $bk_tabs_titles as $tab_name ) {
			$tab_cnt_1++;
			$count++;
			$top .= '<li><a href="#tabs-' . $tab_cnt_1 . '">' . $tab_name . '</a></li>';
		}

		$out = $top . $out;

		if( $tab_cnt_1 > $tab_cnt_2 )
			$tab_cnt_2 = $tab_cnt_1;
		else if( $tab_cnt_2 > $tab_cnt_1 )
			$tab_cnt_1 = $tab_cnt_2;

		$bk_tabs_titles = array();

		return $out;

	}
}
add_shortcode('bk_tabs', 'bk_tabs');

/*
 single tab -> called from tabs
*/
if ( !function_exists( 'bk_tab' ) ) {
	function bk_tab($atts, $content=null, $code){
		extract(shortcode_atts( array('title' => ''), $atts));
		global $bk_tabs_titles;
		$bk_tabs_titles[] = $title;

		global $tab_cnt_2;
		$tab_cnt_2++;
		return '<div id="tabs-' . $tab_cnt_2 . '">' . do_shortcode( $content ) . '</div>';
	}
}
add_shortcode('bk_tab', 'bk_tab');

/*
 accordions
*/
if ( !function_exists( 'bk_accordion' ) ) {
	function bk_accordion($atts, $content=null, $code){
		extract(shortcode_atts( array( 'closeable' => 0), $atts));

		$out  = '';
		$out .= '<div class="bk-accordion" data-closeable="' . $closeable . '">';
		$out .= do_shortcode( $content );
		$out .= '</div>';

		return $out;

	}
}
add_shortcode('bk_accordion', 'bk_accordion');

/*
 single tab -> called from tabs
*/
if ( !function_exists( 'bk_accordion_section' ) ) {
	function bk_accordion_section($atts, $content=null, $code){
		extract(shortcode_atts( array( 'title' => '' ), $atts));

		return '<div class="bk-toggle-header content-separator"><span class="title">' . $title .'</span><span class="bk-header-button"></span></div>' .
				'<div class="bk-accordion-outer-wrap">' . do_shortcode(  $content ) . '</div>';
	}
}
add_shortcode('bk_accordion_section', 'bk_accordion_section');

/*
 toggle
*/
if ( !function_exists( 'bk_toggle' ) ) {
	function bk_toggle($atts, $content=null, $code){
		extract(shortcode_atts( array( 'title' => '',
		'closed' => 0), $atts));
		$classes = '';
		if( $closed ) {
			$classes = 'bk-toggle-closed';
		}


		$toggle = '<div class="bk-toggle ' . $classes . '">';
		$toggle .= '<div class="bk-toggle-header content-separator"><span class="title">' . $title .'</span><span class="bk-header-button"></span></div>';
		$toggle .= '<div class="bk-toggle-content-outer-wrap">' . do_shortcode( $content ) . '</div>';
		$toggle .= '</div>';

		return $toggle;
	}
}
add_shortcode('bk_toggle', 'bk_toggle');

/*
 yotube video
*/
if ( !function_exists( 'bk_video' ) ) {
	function bk_video($atts, $content=null, $code){

		extract(shortcode_atts( array( 'video_link' => '',
		'width' => 16,
		'height' => 9 ), $atts));

		if( !empty($video_link) && preg_match("/\b(?:youtu|youtube)\.(com|be)\b/i", $video_link) ) {
			return bk_get_youtube_markup_from_link($video_link, $width, $height);
		}	else if( !empty($video_link) && preg_match("/vimeo\.com/i", $video_link) ) {
			return bk_get_vimeo_markup_from_link($video_link, $width, $height);
		}
	}
}
add_shortcode('bk_video', 'bk_video');

/*
 sitemap
*/
if ( !function_exists( 'bk_sitemap' ) ) {
	function bk_sitemap($atts, $content=null, $code){

		extract(shortcode_atts( array( 'depth' => 0 ), $atts));
		return wp_nav_menu( array( 'walker' => new bk_sitemap_walker(), 'theme_location' => 'primary-menu', 'menu_class' => 'sitemap-links', 'container' => 'div', 'depth' => $depth, 'echo' => 0 ) );
	}
}
add_shortcode('sitemap', 'bk_sitemap');


/*----------------------------------------------------------------------------------
 Blog Shortcode
----------------------------------------------------------------------------------*/
$bk_global_unique_blog_id = 0;

if ( !function_exists( 'bk_blog' ) ) {
	function bk_blog($atts, $inner_content=null, $code){
		extract(shortcode_atts( array( 'base_size' => 'small', // three base sizes aval -> small, medium, large -> specifies to how many columns space will be divided
		'post_per_page' => 12, // number of posts per page or on pageload -> uuse -1 to show all
		'blog_navigation' => 'load_more_button',  // load_more_button , infinite_scroll, pagination
		'ajax_load_more_count' => 4, // how many posts to load when user clicks
		'gutter' => 0, // gutter for portfolio items
		'show_cats' => -1, // if -1 then all categroies are shown, otherwise it require filter slug e.g 'seo' for one category or string 'seo, social-media' for few cats
		'pagination_range' => 1 ), $atts));

		$content = '';

		global $bk_global_unique_blog_id;
		$bk_global_unique_blog_id++;

		$blog_id = 'bk-blog-outer-wrap-' . $bk_global_unique_blog_id;

		if( $gutter > 0 ) {
			$content .= '<style type="text/css">';
			$content .= '.no-js #' . $blog_id . ' li.bk-blog-entry-wrap { margin-right: ' . $gutter . 'px; }';
			$content .= '#' . $blog_id . ' li.bk-blog-entry-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $blog_id . ' div.bk-load-more-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $blog_id . ' div.bk-pagination-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $blog_id . ' ul.bk-blog-wrap { margin-top: ' . $gutter . 'px; }';
			$content .= '#' . $blog_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
			$content .= '</style>';
		}

		$content .= '<div class="bk-blog-outer-wrap ' . $blog_navigation . ' ' . $base_size . '" id="' . $blog_id . '"  data-blog_base_size="' . $base_size . '" data-blog_gutter="' . $gutter . '" >';
		$content .= '<ul class="bk-blog-wrap">';

		/*
		 global $paged;
		if( !isset($paged) ) {
		$paged = get_query_var('paged');
		}
		*/

		$paged = 1;
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}

		$args= array(
				'paged' => $paged,
				'posts_per_page' => $post_per_page,
				'post_type' => 'post',
				'post_status' => 'publish'
		);

		if( $show_cats != -1 ) {
			$args['category_name'] = $show_cats;
		}

		$loaded_items_count = 0;
		$total_items_matched = 0;

		$content .= bk_get_blog_items( $args, $show_cats, $loaded_items_count, $total_items_matched );

		$content .= '</ul>';

		$content .= '<div class="clearfix"></div>';

		if( $post_per_page > -1 && $loaded_items_count < $total_items_matched ) {
			if( $blog_navigation == 'pagination' ) {
				$content .= bk_pagination('', $pagination_range, false, 'portfolio');
			} elseif( $blog_navigation == 'load_more_button' ) {
				$content .= '<div class="bk-load-more-wrap portfolio bk-hidden">';
				$content .= '<a class="bk-load-more-button button theme-default" data-offset="' . $loaded_items_count . '" data-show_cats="' . $show_cats . '" data-load_more_count="' . $ajax_load_more_count . '" data-active_text="' . get_option("bk_post_ajax_load_more_button_active_text", "Load More...") . '" data-loading_text="' . get_option("bk_post_ajax_load_more_button_loading_text", "Loading...") . '" data-inactive_text="' . get_option("bk_post_ajax_load_more_button_inactive_text", "No more items") . '" ><span class="ajax-loading-text">' . get_option("bk_post_ajax_load_more_button_active_text", "Load More...") . '</span><span class="ajax-loading-icon"></span><span class="ajax-loading-info">(' . $loaded_items_count . '/' . $total_items_matched . ')</span></a>';
				$content .= '</div>';
			}	elseif( $blog_navigation == 'infinite_scroll' ) {
				wp_enqueue_script('infinite_scroll');
				$content .= bk_pagination('', $pagination_range, false, 'portfolio');
				$content .= '<div class="bk-infinite-scroll-info-wrap bk-hidden">';
				$content .= '<a class="bk-load-more-button" data-offset="' . $loaded_items_count . '" data-show_cats="' . $show_cats . '" data-load_more_count="' . $ajax_load_more_count . '" data-inactive_text="' . get_option("bk_post_ajax_infinite_scroll_inactive_text", "No more items") . '" data-active_text="' . get_option("bk_post_ajax_infinite_scroll_active_text", "Loading...") . '"></a>';
				$content .= '</div>';
			}
		}

		$content .= '</div>';

		wp_reset_query();

		return $content;
	}
}
add_shortcode('bk_blog', 'bk_blog');
/*----------------------------------------------------------------------------------
 Helper Function get_blog_items

if $args are empty array it means that it is wordpress built in query such as taxonomy
or archives
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_blog_items' ) ) {
	function bk_get_blog_items( $args, $show_cats, &$loaded_items_count, &$total_items_matched ) {

		if( !empty($args) ) {
			query_posts($args);
			global $wp_query;
			$total_items_matched = $wp_query->found_posts;
		}

		$is_inside_grid = true;

		$content = '';
		global $post;
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$loaded_items_count++;		// this is var passed by reference - used outside this function

				if( is_search() && $post->post_type == "portfolio" ) {	
						
					$description = get_option('bk_portfolio_archives_description');
					$image_effect = get_option('bk_portfolio_archives_image_effect');
					$image_overlay = get_option('bk_portfolio_archives_image_overlay');
					
					$thumbnail_size = 'portfolio-grid-1-1';  // default equal grid style
						
					$content .= '<li class="bk-blog-entry-wrap portfolio-item ' . $thumbnail_size . '">';
					if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
						$thumbnail_behavior = get_post_meta($post->ID, '_bk_portfolio_thumbnail_behavior', true);
							
						$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $thumbnail_size);
						$content .= '<div class="bk-img-wrap ' . $image_effect . '">';
						$content .= '<img class="bk-portfolio-thumb" src="' . $image_src[0] . '">';
							
						$video_width = get_post_meta($post->ID, '_bk_popup_video_width', true);
						$video_height = get_post_meta($post->ID, '_bk_popup_video_height', true);
							
						switch( $thumbnail_behavior ) {
							case 'featured_image_popup':
								$fullsize_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'fullsize');
									
								$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . $fullsize_image[0] . '"></a>';
								break;
									
							case 'image_popup':
								$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_image_link', true) . '"></a>';
								break;
									
							case 'vimeo_popup':
								$content .= '<a class="bk-portfolio-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_vimeo_link', true) . '"></a>';
								break;
									
							case 'youtube_popup':
								$content .= '<a class="bk-portfolio-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_youtube_link', true) . '"></a>';
								break;
									
							case 'gallery_popup':
								$gallery_items = Bk_Popup_Gallery_Manager::getInstance()->get_gallery_items($post->ID,  '_bk_portfolio_popup_gallery', 'popup-gallery-thumb');
									
								$link_classes = 'bk-portfolio-thumb-link';  // only first on will have this class to make it clickable - rest of links will be hidden
								foreach( $gallery_items as $item ) {
									switch( $item['item_type'] ) {
										case 'bk_gallery_image':
											$content .= '<a class="' . $link_classes . ' jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['image_url'] . '" data-thumbnail="' . $item['thumb_url'] . '"></a>';
											break;
												
										case 'bk_gallery_video':
											$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '"></a>';
											break;
												
										case 'bk_gallery_selfhosted_video':
											$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '" data-poster="' . $item['video_poster'] . '"></a>';
											break;
									}
										
									$link_classes = 'bk-hidden-portfolio-link';
								}
									
								break;
									
							case 'external_link':
								$content .= '<a class="bk-portfolio-thumb-link" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_external_link', true) . '"></a>';
								break;
									
							case 'content_link':
								$content .= '<a class="bk-portfolio-thumb-link" href="' . get_permalink($post->ID) . '"></a>';
								break;
						}
							
						$content .= '<div class="bk-img-border"></div>';
							
						if( $image_overlay != 'none' ) {
							$content .= '<div class="bk-img-overlay ' . $image_overlay . '">';
							$content .= '<div class="bk-img-overlay-outer">';
							$content .= '<div class="bk-img-overlay-inner">';
								
							$content .= '<div class="portfolio-thumb-title-meta-wrap">';
								
							if( $image_overlay == 'title' || $image_overlay == 'title_and_tags' || $image_overlay == 'title_and_cats' ) {// title, title_and_tags, title_and_cats
								$content .= '<h4 class="portfolio-thumb-title">' . get_the_title() . '</h4>';
							}
								
							if( $image_overlay == 'cats' || $image_overlay == 'tags' || $image_overlay == 'title_and_tags' || $image_overlay == 'title_and_cats' ) {
								$source = 'portfolio_skills';  // default to tags
									
								if( $image_overlay == 'cats' || $image_overlay == 'title_and_cats' ) {
									$source = 'filter';
								}
									
								$porttags = get_the_terms( $post->ID, $source );
								$port_tags_string = '';
								if ( $porttags && ! is_wp_error( $porttags ) ) {
									$tag_links = '';
									foreach ( $porttags as $porttag ) {
										$port_tags_string .= '<a href="' . get_term_link($porttag->slug, $source ) . '">' . $porttag->name . '</a> - ';
									}
								}
									
								$port_tags_string = trim($port_tags_string, " -");
									
								if( !empty($port_tags_string) )
									$content .= '<span class="porfolio-thumb-tags">' . $port_tags_string . '</span>';
									
							}
								
							$content .= '</div>'; // end of .portfolio-thumb-title-meta-wrap
							$content .= '</div>'; // end of .bk-img-overlay-inner
							$content .= '</div>'; // end of .bk-img-overlay-outer
							$content .= '</div>'; // end of .bk-img-overlay
						}
						$content .= '</div>'; // end of .bk-img-wrap
							
						if( $description != 'none' ) {
							$excerpt = get_the_excerpt();
							$title = get_the_title();
								
							$content .= '<div class="portfolio-thumb-description-wrap">';
							if( ( $description == 'title' || $description == 'title_and_excerpt' ) && !empty($title) ) {
								$content .= '<h4 class="portfolio-thumb-description-title">' . $title . '</h4>';
							}
								
							if( ( $description == 'excerpt' || $description == 'title_and_excerpt' ) && !empty($excerpt) ) {
								$content .= '<p class="portfolio-thumb-description-excerpt">' . $excerpt . '</p>';
							}
							$content .= '</div>';
						}
							
					}
					$content .= '</li>';
				} else {
					$content .= '<li class="bk-blog-entry-wrap">';
					$content .= bk_get_post_summary( $post->ID, $is_inside_grid );  // get single post entry, params post_id & is_summary
					$content .= '</li>';
				}

			}
		}
		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Portfolio Shortcode
----------------------------------------------------------------------------------*/
$bk_global_unique_portfolio_id = 0;

if ( !function_exists( 'bk_portfolio' ) ) {
	function bk_portfolio($atts, $inner_content=null, $code){

		extract(shortcode_atts( array( 'show_filter' => 1,	// show filter
		'filter_mode' => 'fade_and_shuffle', // how filtered elements will behave
		// shuffle - only matched elements are visible
		// fade - matched elements visible and interactive, not matched faded - all items stay in the same place
		// fade_and_shuffle -  matched elements visible, interactive and moved to top of portfolio, not matched faded and pushed at the bottom
		'layout' => 'grid',	// grid => equal grid,
		// metro => windows 8 like layout with 4 thumb sizes 1x1, 1x2, 2x1, 2x2
		// masonry => masonry layout where each image hae different height so natural aspect ratio is preserved
		// mixed_masonry => masonry layout where each image have its own height and two widths are allowed small and large ( two times bigger than small )
		'base_size' => 'small', // three base sizes aval -> small, medium, large -> specifies to how many columns space will be divided
		'description' => 'none', // optional description displayed below thumb
		// none, title, excerpt, title&excerpt
		'image_effect' => 'bk_zoom', // optional hover image effect
		// bk_none, bk_zoom, bk_grayscale, bk_reverse_grayscale
		'image_overlay' => 'icon',	// optional hover overlay for image
		// none, title, title_and_tags, title_and_cats, tags, cats, icon
		'gutter' => 10, // gutter for portfolio items
		'remove_side_gutter' => 0,
		'show_cats' => -1, // if -1 then all categroies are shown, otherwise it require filter slug e.g 'seo' for one category or string 'seo, social-media' for few cats
		'portfolio_order_by' => 'date',  //'ID' - Order by post id. Note the captialization.
		//'author' - Order by author.
		//'title' - Order by title.
		//'name' - Order by post name (post slug).
		//'date' - Order by date.
		//'modified' - Order by last modified date.
		//'comment_count' - Order by number of comments (available with Version 2.9).
		//'menu_order' - Order by Page Order attribute
		'portfolio_order' => 'ASC', // 'ASC', 'DESC'
		'port_navigation' => 'load_more_button',  // load_more_button , infinite_scroll, pagination, none
		'port_per_page' => 12,	// number of posts per page or on pageload -> uuse -1 to show all
		'ajax_load_more_count' => 3, // how many posts to load when user clicks
		'pagination_range' => 2), $atts));

		$content = '';

		global $bk_global_unique_portfolio_id;
		$bk_global_unique_portfolio_id++;

		$portfolio_id = 'bk-portfolio-wrap-' . $bk_global_unique_portfolio_id;

		if( $gutter > 0 ) {
			$content .= '<style type="text/css">';
			$content .= '.no-js #' . $portfolio_id . ' li.portfolio-item { margin-right: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' ul.filterable-grid.metro li.portfolio-item.portfolio-grid-1-2 img.bk-portfolio-thumb { margin-left: -' . round( $gutter / 2 ) . 'px; }';
			$content .= '#' . $portfolio_id . ' li.portfolio-item { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' div.bk-load-more-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' div.bk-pagination-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' ul.filterable-grid { margin-top: ' . $gutter . 'px; }';

			if( !$remove_side_gutter ) {
				$content .= '#' . $portfolio_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
			}
			$content .= '</style>';
		}

		$content .= '<div class="bk-portfolio-wrap ' . $port_navigation . '" data-portfolio_filter_mode="' . $filter_mode . '" data-portfolio_image_effect="' . $image_effect . '" data-portfolio_image_overlay="' . $image_overlay . '" data-portfolio_layout="' . $layout . '" data-portfolio_description="' . $description . '" id="' . $portfolio_id . '" data-portfolio_base_size="' . $base_size . '" data-portfolio_gutter="' . $gutter . '">';

		if( $show_filter ) {
			$content .= '<ul class="portfolio-filter clearfix">';
			if( $show_cats == -1 || ( count( explode(",", $show_cats) ) > 1 ) )
				$content .= '<li class="current"><a href="javascript:void(0)" class="all">' . __( 'All' , 'corpora_theme') . '</a><span></span></li>';

			$terms = get_terms('filter');

			$count = count($terms);

			if ($count > 0) {
				foreach ($terms as $term) {
					if( $show_cats == -1 || strpos($show_cats, $term->slug) !== false ) {
						$content .= '<li><a href="javascript:void(0)" class="'.  $term->slug .'">' . $term->name . '</a><span></span></li>';
					}
				}
			}

			$content .= '</ul>';
		}

		$content .= '<ul class="filterable-grid clearfix ' . $layout . '">';

		/*
		 global $paged;
		if( !isset($paged) ) {
		$paged = get_query_var('paged');
		}
		*/

		$paged = 1;
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}

		$args= array(
				'paged' => $paged,
				'posts_per_page' => $port_per_page,
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

		$content .= bk_get_portfolio_items( $args, $layout, $base_size, $show_cats, $description, $image_effect, $image_overlay, $loaded_items_count, $total_items_matched );

		$content .= '</ul>';

		if( $port_per_page > -1 && $loaded_items_count < $total_items_matched ) {
			if( $port_navigation == 'pagination' ) {
				$content .= bk_pagination('', $pagination_range, false, 'portfolio');
			} elseif( $port_navigation == 'load_more_button' ) {
				$content .= '<div class="bk-load-more-wrap portfolio bk-hidden">';
				$content .= '<a class="bk-load-more-button" data-offset="' . $loaded_items_count . '" data-show_cats="' . $show_cats . '" data-portfolio_order_by="' . $portfolio_order_by . '" data-portfolio_order="' . $portfolio_order . '" data-load_more_count="' . $ajax_load_more_count . '" data-active_text="' . get_option("bk_portfolio_ajax_load_more_button_active_text", "Load More...") . '" data-loading_text="' . get_option("bk_portfolio_ajax_load_more_button_loading_text", "Loading")  . '" data-inactive_text="' . get_option("bk_portfolio_ajax_load_more_button_inactive_text", "No more items") . '" ><span class="ajax-loading-text">' . get_option("bk_portfolio_ajax_load_more_button_active_text", "Load More...") . '</span><span class="ajax-loading-icon"></span><span class="ajax-loading-info">(' . $loaded_items_count . '/' . $total_items_matched . ')</span></a>';
				$content .= '</div>';
			}	elseif( $port_navigation == 'infinite_scroll' ) {
				wp_enqueue_script('infinite_scroll');
				$content .= bk_pagination('', $pagination_range, false, 'portfolio');
				$content .= '<div class="bk-infinite-scroll-info-wrap bk-hidden">';
				$content .= '<a class="bk-load-more-button" data-offset="' . $loaded_items_count . '" data-show_cats="' . $show_cats . '" data-portfolio_order_by="' . $portfolio_order_by . '" data-portfolio_order="' . $portfolio_order . '" data-load_more_count="' . $ajax_load_more_count . '" data-inactive_text="' . get_option("bk_portfolio_ajax_infinite_scroll_inactive_text", "No more items") . '" data-active_text="' . get_option("bk_portfolio_ajax_infinite_scroll_active_text", "Loading...") . '"></a>';
				$content .= '</div>';
			}
		}

		$content .= '</div>';

		wp_reset_query();

		return $content;
	}
}
add_shortcode('bk_portfolio', 'bk_portfolio');

/*----------------------------------------------------------------------------------
 Helper function - takes query args and initial grid size - masonry or grid and
default grid equal grid size as parameter and returns found elements - used also
in ajax load more portfolio items function
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_portfolio_items' ) ) {
	function bk_get_portfolio_items( $args, $layout, $base_size, $show_cats, $description, $image_effect, $image_overlay, &$loaded_items_count, &$total_items_matched ) {

		if( !empty($args) ) {
			query_posts($args);
			global $wp_query;
			$total_items_matched = $wp_query->found_posts;
		}

		global $post;

		$content = '';
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$terms = get_the_terms( get_the_ID(), 'filter' );

				$found = false;
				$item_terms = '';
				if ( $terms && !is_wp_error($terms) ) {
					foreach ($terms as $term) {
						$item_terms .= $term->slug . ' ';
					}
				}
				$loaded_items_count++;		// this is var passed by reference - used outside this function


				$thumbnail_size = 'portfolio-grid-1-1';  // default equal grid style

				if( $layout == "masonry") {
					$thumbnail_size = 'portfolio-masonry-small';
				} else if ( $layout == "mixed_masonry" ) {
					$thumbnail_size = 'portfolio-masonry-small';
					$thumb_size = get_post_meta($post->ID, '_bk_portfolio_mixed_masonry_thumbnail_size', true);
					if( !empty($thumb_size) ) {
						$thumbnail_size = 'portfolio-masonry-' . $thumb_size;
					}
				} else if ( $layout == "metro" ) {
					$thumb_size = get_post_meta($post->ID, '_bk_portfolio_thumbnail_size', true);
					if( !empty($thumb_size) ) {
						$thumbnail_size = 'portfolio-grid-' . $thumb_size;
					}
				}

				$original_index = $loaded_items_count;  // only for items loaded initially on pageload

				if( isset($args["offset"]) ) { // this happens if user clicks load more button and ajax call is issued - we have additional arg offset
					$original_index = intval($args["offset"]) + $loaded_items_count;
				}

				$content .= '<li class="portfolio-item all ' . $item_terms . ' ' . $thumbnail_size . '" data-original_index="' . $original_index . '">';
				if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
					$thumbnail_behavior = get_post_meta($post->ID, '_bk_portfolio_thumbnail_behavior', true);

					$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $thumbnail_size);
					$content .= '<div class="bk-img-wrap ' . $image_effect . '">';
					$content .= '<img class="bk-portfolio-thumb" src="' . $image_src[0] . '">';

					$video_width = get_post_meta($post->ID, '_bk_popup_video_width', true);
					$video_height = get_post_meta($post->ID, '_bk_popup_video_height', true);

					switch( $thumbnail_behavior ) {
						case 'featured_image_popup':
							$fullsize_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'fullsize');

							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . $fullsize_image[0] . '"></a>';
							break;

						case 'image_popup':
							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_image_link', true) . '"></a>';
							break;

						case 'vimeo_popup':
							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_vimeo_link', true) . '"></a>';
							break;

						case 'youtube_popup':
							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_youtube_link', true) . '"></a>';
							break;

						case 'gallery_popup':
							$gallery_items = Bk_Popup_Gallery_Manager::getInstance()->get_gallery_items($post->ID,  '_bk_portfolio_popup_gallery', 'popup-gallery-thumb');

							$link_classes = 'bk-portfolio-thumb-link';  // only first on will have this class to make it clickable - rest of links will be hidden
							foreach( $gallery_items as $item ) {
								switch( $item['item_type'] ) {
									case 'bk_gallery_image':
										$content .= '<a class="' . $link_classes . ' jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['image_url'] . '" data-thumbnail="' . $item['thumb_url'] . '"></a>';
										break;

									case 'bk_gallery_video':
										$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '"></a>';
										break;

									case 'bk_gallery_selfhosted_video':
										$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '" data-poster="' . $item['video_poster'] . '"></a>';
										break;
								}

								$link_classes = 'bk-hidden-portfolio-link';
							}

							break;

						case 'external_link':
							$content .= '<a class="bk-portfolio-thumb-link" href="' . get_post_meta($post->ID, '_bk_portfolio_thumbnail_external_link', true) . '"></a>';
							break;

						case 'content_link':
							$content .= '<a class="bk-portfolio-thumb-link" href="' . get_permalink($post->ID) . '"></a>';
							break;
					}

					$content .= '<div class="bk-img-border"></div>';

					if( $image_overlay != 'none' ) {
						$content .= '<div class="bk-img-overlay ' . $image_overlay . '">';
						$content .= '<div class="bk-img-overlay-outer">';
						$content .= '<div class="bk-img-overlay-inner">';

						$content .= '<div class="portfolio-thumb-title-meta-wrap">';

						if( $image_overlay == 'title' || $image_overlay == 'title_and_tags' || $image_overlay == 'title_and_cats' ) {// title, title_and_tags, title_and_cats
							$content .= '<h4 class="portfolio-thumb-title">' . get_the_title() . '</h4>';
						}

						if( $image_overlay == 'cats' || $image_overlay == 'tags' || $image_overlay == 'title_and_tags' || $image_overlay == 'title_and_cats' ) {
							$source = 'portfolio_skills';  // default to tags

							if( $image_overlay == 'cats' || $image_overlay == 'title_and_cats' ) {
								$source = 'filter';
							}

							$porttags = get_the_terms( $post->ID, $source );
							$port_tags_string = '';
							if ( $porttags && ! is_wp_error( $porttags ) ) {
								$tag_links = '';
								foreach ( $porttags as $porttag ) {
									$port_tags_string .= '<a href="' . get_term_link($porttag->slug, $source ) . '">' . $porttag->name . '</a> - ';
								}
							}

							$port_tags_string = trim($port_tags_string, " -");

							if( !empty($port_tags_string) )
								$content .= '<span class="porfolio-thumb-tags">' . $port_tags_string . '</span>';

						}

						$content .= '</div>'; // end of .portfolio-thumb-title-meta-wrap
						$content .= '</div>'; // end of .bk-img-overlay-inner
						$content .= '</div>'; // end of .bk-img-overlay-outer
						$content .= '</div>'; // end of .bk-img-overlay
					}
					$content .= '</div>'; // end of .bk-img-wrap

					if( $description != 'none' ) {
						$excerpt = get_the_excerpt();
						$title = get_the_title();

						$content .= '<div class="portfolio-thumb-description-wrap">';
						if( ( $description == 'title' || $description == 'title_and_excerpt' ) && !empty($title) ) {
							$content .= '<h4 class="portfolio-thumb-description-title">' . $title . '</h4>';
						}

						if( ( $description == 'excerpt' || $description == 'title_and_excerpt' ) && !empty($excerpt) ) {
							$content .= '<p class="portfolio-thumb-description-excerpt">' . $excerpt . '</p>';
						}
						$content .= '</div>';
					}

				}
				$content .= '</li>';
			}
		}

		return $content;
	}
}
/*----------------------------------------------------------------------------------
 Portfolio Shortcode
----------------------------------------------------------------------------------*/
$bk_global_unique_gallery_id = 0;

if ( !function_exists( 'bk_gallery' ) ) {
	function bk_gallery($atts, $inner_content=null, $code){

		extract(shortcode_atts( array( 'layout' => 'grid',	// grid => equal grid,
		// metro => windows 8 like layout with 4 thumb sizes 1x1, 1x2, 2x1, 2x2
		// masonry => masonry layout where each image hae different height so natural aspect ratio is preserved
		// mixed_masonry => masonry layout where each image have its own height and two widths are allowed small and large ( two times bigger than small )
		'base_size' => 'small', // three base sizes aval -> small, medium, large -> specifies to how many columns space will be divided
		'image_effect' => 'bk_zoom', // optional hover image effect
		// bk_none, bk_zoom, bk_grayscale, bk_reverse_grayscale
		'image_overlay' => 'icon',	// optional hover overlay for image
		// none, title, icon
		'gutter' => 10, // gutter for portfolio items
		'remove_side_gutter' => 0, // allows to remove side gutter
		'show_cats' => -1, // if -1 then all categroies are shown, otherwise it require filter slug e.g 'seo' for one category or string 'seo, social-media' for few cats
		'gallery_order_by' => 'date',  //'ID' - Order by post id. Note the captialization.
		//'author' - Order by author.
		//'title' - Order by title.
		//'name' - Order by post name (post slug).
		//'date' - Order by date.
		//'modified' - Order by last modified date.
		//'comment_count' - Order by number of comments (available with Version 2.9).
		//'menu_order' - Order by Page Order attribute
		'gallery_order' => 'ASC', // 'ASC', 'DESC'
		'port_navigation' => 'load_more_button',  // load_more_button , infinite_scroll, pagination, none
		'port_per_page' => 12,	// number of posts per page or on pageload -> uuse -1 to show all
		'ajax_load_more_count' => 3, // how many posts to load when user clicks
		'pagination_range' => 2), $atts));

		$content = '';

		global $bk_global_unique_gallery_id;
		$bk_global_unique_gallery_id++;

		$portfolio_id = 'bk-portfolio-wrap-' . $bk_global_unique_gallery_id;

		if( $gutter > 0 ) {
			$content .= '<style type="text/css">';
			$content .= '.no-js #' . $portfolio_id . ' li.portfolio-item { margin-right: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' ul.filterable-grid.metro li.portfolio-item.portfolio-grid-1-2 img.bk-portfolio-thumb { margin-left: -' . round( $gutter / 2 ) . 'px; }';
			$content .= '#' . $portfolio_id . ' li.portfolio-item { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' div.bk-load-more-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' div.bk-pagination-wrap { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $portfolio_id . ' ul.filterable-grid { margin-top: ' . $gutter . 'px; }';

			if( !$remove_side_gutter ) {
				$content .= '#' . $portfolio_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
			}

			$content .= '</style>';
		}

		$content .= '<div class="bk-portfolio-wrap bk-gallery-wrap ' . $port_navigation . '" data-portfolio_image_effect="' . $image_effect . '" data-portfolio_image_overlay="' . $image_overlay . '" data-portfolio_layout="' . $layout . '" id="' . $portfolio_id . '" data-portfolio_base_size="' . $base_size . '" data-portfolio_gutter="' . $gutter . '">';

		$content .= '<ul class="filterable-grid clearfix ' . $layout . '">';

		/*
		 global $paged;
		if( !isset($paged) ) {
		$paged = get_query_var('paged');
		}
		*/

		$paged = 1;
		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}


		$args= array(
				'paged' => $paged,
				'posts_per_page' => $port_per_page,
				'post_type' => 'gallery',
				'post_status' => 'publish',
				'order' => $gallery_order,
				'orderby' => $gallery_order_by
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

		$content .= bk_get_gallery_items( $args, $layout, $base_size, $show_cats, $image_effect, $image_overlay, $loaded_items_count, $total_items_matched );

		$content .= '</ul>';

		if( $port_per_page > -1 && $loaded_items_count < $total_items_matched ) {
			if( $port_navigation == 'pagination' ) {
				$content .= bk_pagination('', $pagination_range, false, 'portfolio');
			} elseif( $port_navigation == 'load_more_button' ) {
				$content .= '<div class="bk-load-more-wrap portfolio bk-hidden">';
				$content .= '<a class="bk-load-more-button" data-offset="' . $loaded_items_count . '" data-show_cats="' . $show_cats . '" data-portfolio_order_by="' . $gallery_order_by . '" data-portfolio_order="' . $gallery_order . '" data-load_more_count="' . $ajax_load_more_count . '" data-active_text="' . get_option("bk_portfolio_ajax_load_more_button_active_text", "Load More...") . '" data-loading_text="' . get_option("bk_portfolio_ajax_load_more_button_loading_text", "Loading")  . '" data-inactive_text="' . get_option("bk_portfolio_ajax_load_more_button_inactive_text", "No more items") . '" ><span class="ajax-loading-text">' . get_option("bk_portfolio_ajax_load_more_button_active_text", "Load More...") . '</span><span class="ajax-loading-icon"></span><span class="ajax-loading-info">(' . $loaded_items_count . '/' . $total_items_matched . ')</span></a>';
				$content .= '</div>';
			}	elseif( $port_navigation == 'infinite_scroll' ) {
				wp_enqueue_script('infinite_scroll');
				$content .= bk_pagination('', $pagination_range, false, 'portfolio');
				$content .= '<div class="bk-infinite-scroll-info-wrap bk-hidden">';
				$content .= '<a class="bk-load-more-button" data-offset="' . $loaded_items_count . '" data-show_cats="' . $show_cats . '" data-portfolio_order_by="' . $gallery_order_by . '" data-portfolio_order="' . $gallery_order . '" data-load_more_count="' . $ajax_load_more_count . '" data-inactive_text="' . get_option("bk_portfolio_ajax_infinite_scroll_inactive_text", "No more items") . '" data-active_text="' . get_option("bk_portfolio_ajax_infinite_scroll_active_text", "Loading...") . '"></a>';
				$content .= '</div>';
			}
		}

		$content .= '</div>';

		wp_reset_query();

		return $content;
	}
}
add_shortcode('bk_gallery', 'bk_gallery');

/*----------------------------------------------------------------------------------
 Helper function - takes query args and initial grid size - masonry or grid and
default grid equal grid size as parameter and returns found elements - used also
in ajax load more portfolio items function
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_get_gallery_items' ) ) {
	function bk_get_gallery_items( $args, $layout, $base_size, $show_cats, $image_effect, $image_overlay, &$loaded_items_count, &$total_items_matched ) {

		if( !empty($args) ) {
			query_posts($args);
			global $wp_query;
			$total_items_matched = $wp_query->found_posts;
		}

		global $post;

		$content = '';
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$loaded_items_count++;		// this is var passed by reference - used outside this function


				$thumbnail_size = 'portfolio-grid-1-1';  // default equal grid style

				if( $layout == "masonry") {
					$thumbnail_size = 'portfolio-masonry-small';
				} else if ( $layout == "mixed_masonry" ) {
					$thumbnail_size = 'portfolio-masonry-small';
					$thumb_size = get_post_meta($post->ID, '_bk_gallery_mixed_masonry_thumbnail_size', true);
					if( !empty($thumb_size) ) {
						$thumbnail_size = 'portfolio-masonry-' . $thumb_size;
					}
				} else if ( $layout == "metro" ) {
					$thumb_size = get_post_meta($post->ID, '_bk_gallery_thumbnail_size', true);
					if( !empty($thumb_size) ) {
						$thumbnail_size = 'portfolio-grid-' . $thumb_size;
					}
				}

				$original_index = $loaded_items_count;  // only for items loaded initially on pageload

				if( isset($args["offset"]) ) { // this happens if user clicks load more button and ajax call is issued - we have additional arg offset
					$original_index = intval($args["offset"]) + $loaded_items_count;
				}

				$content .= '<li class="portfolio-item all ' . $thumbnail_size . '" data-original_index="' . $original_index . '">';
				if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
					$thumbnail_behavior = get_post_meta($post->ID, '_bk_gallery_thumbnail_behavior', true);

					$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $thumbnail_size);
					$content .= '<div class="bk-img-wrap ' . $image_effect . '">';
					$content .= '<img class="bk-portfolio-thumb" src="' . $image_src[0] . '">';

					$video_width = get_post_meta($post->ID, '_bk_popup_video_width', true);
					$video_height = get_post_meta($post->ID, '_bk_popup_video_height', true);

					switch( $thumbnail_behavior ) {
						case 'featured_image_popup':
							$fullsize_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'fullsize');

							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . $fullsize_image[0] . '"></a>';
							break;

						case 'image_popup':
							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_gallery_thumbnail_image_link', true) . '"></a>';
							break;

						case 'vimeo_popup':
							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_gallery_thumbnail_vimeo_link', true) . '"></a>';
							break;

						case 'youtube_popup':
							$content .= '<a class="bk-portfolio-thumb-link jackbox" data-width="' . $video_width . '" data-height="' . $video_height . '" data-title="' . get_the_title() . '" data-group="gallery_' . $post->ID . '" href="' . get_post_meta($post->ID, '_bk_gallery_thumbnail_youtube_link', true) . '"></a>';
							break;

						case 'gallery_popup':
							$gallery_items = Bk_Popup_Gallery_Manager::getInstance()->get_gallery_items($post->ID,  '_bk_gallery_popup_gallery', 'popup-gallery-thumb');

							$link_classes = 'bk-portfolio-thumb-link';  // only first on will have this class to make it clickable - rest of links will be hidden
							foreach( $gallery_items as $item ) {
								switch( $item['item_type'] ) {
									case 'bk_gallery_image':
										$content .= '<a class="' . $link_classes . ' jackbox" data-title="' . $item['image_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['image_url'] . '" data-thumbnail="' . $item['thumb_url'] . '"></a>';
										break;

									case 'bk_gallery_video':
										$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '"></a>';
										break;

									case 'bk_gallery_selfhosted_video':
										$content .= '<a class="' . $link_classes . ' jackbox" data-autoplay="' . $item['video_autoplay'] . '" data-width="' . $item['video_width'] . '" data-height="' . $item['video_height'] . '" data-title="' . $item['video_description'] . '" data-group="gallery_' . $post->ID . '" href="' . $item['video_link'] . '"  data-thumbnail="' . $item['video_thumbnail'] . '" data-poster="' . $item['video_poster'] . '"></a>';
										break;
								}

								$link_classes = 'bk-hidden-portfolio-link';
							}

							break;

						case 'external_link':
							$content .= '<a class="bk-portfolio-thumb-link" href="' . get_post_meta($post->ID, '_bk_gallery_thumbnail_external_link', true) . '"></a>';
							break;
					}

					$content .= '<div class="bk-img-border"></div>';

					if( $image_overlay != 'none' ) {
						$content .= '<div class="bk-img-overlay ' . $image_overlay . '">';
						$content .= '<div class="bk-img-overlay-outer">';
						$content .= '<div class="bk-img-overlay-inner">';

						$content .= '<div class="portfolio-thumb-title-meta-wrap">';

						if( $image_overlay == 'title') {// title, title_and_tags, title_and_cats
							$content .= '<h4 class="portfolio-thumb-title">' . get_the_title() . '</h4>';
						}

						$content .= '</div>'; // end of .portfolio-thumb-title-meta-wrap
						$content .= '</div>'; // end of .bk-img-overlay-inner
						$content .= '</div>'; // end of .bk-img-overlay-outer
						$content .= '</div>'; // end of .bk-img-overlay
					}
					$content .= '</div>'; // end of .bk-img-wrap

				}
				$content .= '</li>';
			}
		}

		return $content;
	}
}
/*
 * Get quick gallery shortcode
*
*/
if ( !function_exists( 'bk_quick_gallery' ) ) {
	function bk_quick_gallery( $atts, $inner_content=null, $code ) {

		extract(shortcode_atts( array(  'layout' => 'grid',	// grid => equal grid,
		// metro => windows 8 like layout with 4 thumb sizes 1x1, 1x2, 2x1, 2x2
		// masonry => masonry layout where each image hae different height so natural aspect ratio is preserved
		// mixed_masonry => masonry layout where each image have its own height and two widths are allowed small and large ( two times bigger than small )
		'base_size' => 'small', // three base sizes aval -> small, medium, large -> specifies to how many columns space will be divided
		'image_effect' => 'bk_zoom', // optional hover image effect
		// bk_none, bk_zoom, bk_grayscale, bk_reverse_grayscale
		'image_overlay' => 'icon',	// optional hover overlay for image
		// none, icon
		'gutter' => 10, // gutter for portfolio items
		'remove_side_gutter' => 0,
		'quick_gallery_id' => -1 // quick gallery post id
		), $atts));


		$content = '';

		$quick_gallery_id = intval($quick_gallery_id);

		$quick_gallery_unique_id = 'bk-quick-gallery-wrap-' . $quick_gallery_id;

		$gallery_items = Bk_Featured_Gallery_Manager::getInstance()->get_gallery_items($quick_gallery_id,  '_bk_quick_gallery', 'popup-gallery-thumb');

		if( empty($gallery_items) ) {
			return $content;
		}

		if( $gutter > 0 ) {
			$content .= '<style type="text/css">';
			$content .= '.no-js #' . $quick_gallery_unique_id . ' li.portfolio-item { margin-right: ' . $gutter . 'px; }';
			$content .= '#' . $quick_gallery_unique_id . ' li.portfolio-item { margin-bottom: ' . $gutter . 'px; }';
			$content .= '#' . $quick_gallery_unique_id . ' { margin-top: ' . $gutter . 'px;  }';
			if( !$remove_side_gutter ) {
				$content .= '#' . $quick_gallery_unique_id . ' { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px;}';
			}
			$content .= '</style>';
		}

		$content .= '<div class="bk-portfolio-wrap bk-single-portfolio-item" data-portfolio_layout="' . $layout . '" id="' . $quick_gallery_unique_id . '" data-portfolio_base_size="' . $base_size . '" data-portfolio_gutter="' . $gutter . '">';
		$content .= '<ul class="filterable-grid clearfix ' . $layout . '">';

		foreach( $gallery_items as $item ) {
			$thumbnail_size = 'portfolio-grid-1-1';  // default equal grid style

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

					$content .= bk_get_image_link_wrap( $item, $quick_gallery_id );

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

						$content .= bk_get_self_hosted_video_markup_from_link( $video_link, $item['video_poster'], $quick_gallery_id, $item['video_width'], $item['video_height'] );

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
add_shortcode('bk_quick_gallery', 'bk_quick_gallery');
?>