<?php
/**
 * Single Product title
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="bk-product-title-wrap">
	<h1 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h1>
	
	<?php
	echo '<ul class="product-top-nav">';

	$exclude_cats = '';
	$shop_home_page_id = get_option("woocommerce_shop_page_id");
			
	if( get_adjacent_post( false, $exclude_cats, true ) ) {
		$prev_post = get_adjacent_post( false, $exclude_cats, true );
		echo '<li><a title="' . $prev_post->post_title . '" class="prev-product-entry-link" href="' . get_permalink( $prev_post->ID ) . '"></a></li>';
	}

	if( $shop_home_page_id != -1 ) {
		echo '<li><a title="' . __("Return to Shop", 'corpora_theme') . '" class="back-to-product-link" href="'. get_permalink( $shop_home_page_id ) .'"></a></li>';
	}
			
	if( get_adjacent_post( false, $exclude_cats, false ) ) {
		$next_post = get_adjacent_post( false, $exclude_cats, false );
		echo '<li><a title="' . $next_post->post_title . '" class="next-product-entry-link" href="' . get_permalink( $next_post->ID ) . '"></a></li>';
	}

	echo '</ul>';
	?>		
	
</div>