<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';


?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<?php
			global $post;
	
			$thumbnail_size = 'shop_catalog';
			
	        $image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $thumbnail_size);
			$image_effect = 'bk_none';
			
			
			echo '<div class="bk-img-wrap ' . $image_effect . '">';
			echo '<img class="bk-product-thumb" src="' . $image_src[0] . '">';
			
			echo '<a class="bk-post-thumb-link" href="' . get_permalink($post->ID) . '"></a>';
			
			if ($product->is_on_sale()) { 
		    	echo apply_filters('woocommerce_sale_flash', '<span class="bk_product_badge onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product);
			} else if(!$product->is_in_stock()) {
				echo '<span class="bk_product_badge out_of_stock">'. __( 'Out of stock', 'woocommerce' ) . '</span>';
            }
			
			
			echo '<div class="bk-img-border"></div>';
			
			echo '</div>';
			
			echo '<div class="product-thumb-description-wrap clearfix">';
			
			echo '<h4 class="product-thumb-description-title"><a href="' . get_permalink($post->ID) . '">' . get_the_title() . '</a></h4>';
			
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
			
			do_action( 'woocommerce_after_shop_loop_item' );
			
			echo '<div class="clearfix"></div>';
			
			echo '</div>';
	?>

</li>