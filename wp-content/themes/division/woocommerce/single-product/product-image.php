<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;

?>
<div class="images">
	<?php
		if ( has_post_thumbnail() ) {

			$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
			
			$add_popup_link = get_option("bk_shop_single_item_popup", true);
			
			$attachment_count   = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
			  
			  $attachment_ids = $product->get_gallery_attachment_ids();
				
			  echo '<div class="flexslider bk_woo_product">';
			  echo '<ul class="slides">';
			  
			  foreach ( $attachment_ids as $attachment_id ) {
			  	$thumb_image = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			  	$large_image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			  	
			  	$large_image_url = wp_get_attachment_image_src( $attachment_id, 'fullsize' );
			  	
			  	$title = trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) ));
			  	
			  	echo '<li data-thumb="' . $thumb_image[0] . '">';
			  	echo '<div class="bk-img-wrap">';
			  	echo $large_image;
			  	
			  	if( $add_popup_link ) {
			  		echo '<a class="bk-portfolio-thumb-link jackbox" data-title="' . $title . '" data-group="gallery_' . $post->ID . '" href="' . $large_image_url[0] . '" data-thumbnail="' . $thumb_image[0] . '"></a>';
			  	}
			  	
			  	echo '<div class="bk-img-border"></div>';
			  	echo '</div>';
			  	echo '</li>';
			  	
			  }
			  
			  echo '</ul>';
			  echo '</div>';	
				
				
			} else {
			  if( $add_popup_link )	{
			  	  echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image jackbox zoom" title="%s" data-title="%s" data-group="gallery_' . $post->ID . '">%s</a>', $image_link, $image_title, $image_title, $image ), $post->ID );
			  } else {
			  	  echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '%s', $image ), $post->ID );
			  }
			}

		} else {
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', woocommerce_placeholder_img_src() ), $post->ID );
		}
		
		if ($product->is_on_sale()) {
			echo apply_filters('woocommerce_sale_flash', '<span class="bk_product_badge onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product);
		} else if(!$product->is_in_stock()) {
			echo '<span class="bk_product_badge out_of_stock">'. __( 'Out of stock', 'woocommerce' ) . '</span>';
		}
	?>
</div>

