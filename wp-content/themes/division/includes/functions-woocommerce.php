<?php
/*----------------------------------------------------------------------------------
 Add WooCommerce support
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_woocommerce_support' ) ) {
	function bk_woocommerce_support() {
		add_theme_support( 'woocommerce' );
	}
}
add_action( 'after_setup_theme', 'bk_woocommerce_support' );

if ( !function_exists( 'bk_disable_default_woo_dashboard_widgets' ) ) {
	function bk_disable_default_woo_dashboard_widgets() {
		remove_meta_box('woocommerce_dashboard_sales', 'dashboard', 'core');
	}
}

add_action('admin_menu', 'bk_disable_default_woo_dashboard_widgets');

/*----------------------------------------------------------------------------------
 Use default woocommerce style
----------------------------------------------------------------------------------*/

/*----------------------------------------------------------------------------------
 * Remove rating from thumb default place and add after add to cart button on the top left corner
 ----------------------------------------------------------------------------------*/
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 20 );

/*
 * Add clearfix div after shop loop item title and price and before add to cart button
 */
add_action( 'woocommerce_after_shop_loop_item', 'bk_custom_after_shop_add_clearfix', 5 );

if ( !function_exists( 'bk_custom_after_shop_add_clearfix' ) ) {
	  function bk_custom_after_shop_add_clearfix() { 
	  	echo '<div class="clearfix"></div>'; 
	  }
}

/*----------------------------------------------------------------------------------
 * Remove breadcrumbs
 ----------------------------------------------------------------------------------*/
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/*----------------------------------------------------------------------------------
 * Remove Sale flash from single product
 ----------------------------------------------------------------------------------*/
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/*----------------------------------------------------------------------------------
 * Move single product title to the top
 ----------------------------------------------------------------------------------*/
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_title', 10 );  

/*----------------------------------------------------------------------------------
 * Move single product notification lover - below title. 
 * 
 ----------------------------------------------------------------------------------*/
remove_action( 'woocommerce_before_single_product', 'woocommerce_show_messages', 10 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_messages', 10 );


/*----------------------------------------------------------------------------------
 * Remove archive and shop description
 ----------------------------------------------------------------------------------*/
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

/*----------------------------------------------------------------------------------
 * Hook into share action for single products - If You don't wand this social icons to appear comment out line below.
 ----------------------------------------------------------------------------------*/
add_action( 'woocommerce_share', 'bk_print_product_social', 10 );

/*----------------------------------------------------------------------------------
 print social icons for single products
----------------------------------------------------------------------------------*/
if ( !function_exists( 'bk_print_product_social' ) ) {
	function bk_print_product_social() {
		
		global $post;
		$post_id = $post->ID;
		
		$content = '';
		
		$content .= '<div class="bk-portfolio-meta-wrap clearfix">';

		$content .= '<ul class="bk-portfolio-meta clearfix">';
		
		/*
		$content .= '<li>';
		$content .= bk_get_love_port_button( $post_id, 'dark' );
		$content .= '</li>';

		if( get_option('bk_portfolio_show_views') == true ) {
			$content .= '<li><span class="eye-icon"></span>';
			$content .= bk_get_view_portfolio_counter( $post_id );
			$content .= '</li>';
		}
		*/

		if( get_option('bk_shop_show_facebook_button') == true ) {
			$content .= '<li class="bk_facebook_share_button_wrap bk_social_button_wrap">';
			$content .= bk_get_facebook_button_markup( get_permalink( $post_id ), get_option('bk_shop_facebook_button_count') );
			$content .= '</li>';
		}

		if( get_option('bk_shop_show_tweet_button') == true ) {
			$content .= '<li class="bk_tweet_share_button_wrap bk_social_button_wrap">';
			$content .= bk_get_twitter_button_markup( get_the_title($post_id), get_permalink( $post_id ), get_option('bk_shop_tweet_button_count') );
			$content .= '</li>';
		}

		if( get_option('bk_shop_show_google_plus_button') == true ) {
			$content .= '<li class="bk_google_plus_share_button_wrap bk_social_button_wrap">';
			$content .= bk_get_google_plus_button_markup( get_permalink( $post_id ), get_option('bk_shop_google_plus_button_count') );
			$content .= '</li>';
		}

		if( get_option('bk_shop_show_pinterest_button') == true &&  has_post_thumbnail( $post_id ) ) {
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'fullsize' );

			$content .= '<li class="bk_pinterest_button_wrap bk_social_button_wrap">';
			$content .= bk_get_pinterest_social_button( get_the_title($post_id), get_permalink( $post_id ), $featured_image[0] , get_option('bk_shop_show_pinterest_button_count') );
			$content .= '</li>';
		}

		$content .= '</ul>';

		$content .= '</div>';

		echo $content;
	}
}

/*
 * Custom shop sidebar is hooked here
 */

add_action( 'bk_woocommerce_custom_after_loop', 'bk_woocommerce_custom_sidebar', 10 );

if ( !function_exists( 'bk_woocommerce_custom_sidebar' ) ) {
	function bk_woocommerce_custom_sidebar() {
		get_sidebar();
	}
}

//remove_filter( 'woocommerce_show_page_title', true );

/*
 * Remove default result text - showing producs 1-9 of 12 and order by dropdown in order to hook those to custom hook into lower div
 */

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

add_action( 'bk_woocommerce_custom_before_loop', 'woocommerce_result_count', 20 );
add_action( 'bk_woocommerce_custom_before_loop', 'woocommerce_catalog_ordering', 30 );

/*
 * Remove default navigation just to hook it into custom hook into lower div
 */

/**
 * move cross sells in cart below shipping calculator and cart totals
 *
 * @hooked woocommerce_pagination - 10
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10 );


/*
 * Custom filter to alter number of items show in cart as cross-sells
 */
add_filter('woocommerce_cross_sells_total', 'bk_custom_cross_sells_count');

if ( !function_exists( 'bk_custom_cross_sells_count' ) ) {
	 function bk_custom_cross_sells_count($count) { 
		return get_option("bk_shop_cross_sells_count", 3);
	 }
}

/*
 * Show/hide shop title filter
 */
add_filter('woocommerce_show_page_title', 'bk_custom_shop_archives_show_page_title');

if ( !function_exists( 'bk_custom_shop_archives_show_page_title' ) ) {
	function bk_custom_shop_archives_show_page_title($count) {
		return get_option("bk_shop_archives_show_page_title", true);
	}
}


/*
 * Custom filter for title on my account page -> addresses adress
 */
add_filter('woocommerce_my_account_my_address_title', 'bk_custom_my_account_my_address_title');

if ( !function_exists( 'bk_custom_my_account_my_address_title' ) ) {
	  function bk_custom_my_account_my_address_title($title) {
		 return '<h3 class="content-separator"><span class="title">' . $title . '</span></h3>';
	  }
}

?>