<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Increase loop count
$woocommerce_loop['loop']++;
?>
<li class="product-category product">

	<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

	<?php
			global $post;
	
			$image_effect = 'bk_none';
			
			
			echo '<div class="bk-img-wrap ' . $image_effect . '">';
			do_action( 'woocommerce_before_subcategory_title', $category );
			
			echo '<a class="bk-post-thumb-link" href="' . get_term_link( $category->slug, 'product_cat' ) . '"></a>';
			
			echo '<div class="bk-img-border"></div>';
			
			echo '</div>';
			
			echo '<div class="product-thumb-description-wrap clearfix">';
			
			echo '<h4 class="product-thumb-description-title"><a href="' . get_term_link( $category->slug, 'product_cat' ) . '">' . $category->name . '</a>';
			
			if ( $category->count > 0 )
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			
			echo '</h4>';
			
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			do_action( 'woocommerce_after_subcategory_title', $category );
			
			do_action( 'woocommerce_after_subcategory', $category );
			
			echo '<div class="clearfix"></div>';
			
			echo '</div>';
	?>

</li>
