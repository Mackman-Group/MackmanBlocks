<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
?>

<?php 

do_action("bk_woocommerce_custom_before_loop");

$content = '';

$base_size = get_option('bk_shop_archives_base_size', 'large');
$gutter = get_option('bk_shop_archives_gutter', 7);
$remove_side_gutter = 0;

if( $gutter > 0 ) {
	$content .= '<style type="text/css">';
	$content .= '.no-js .woocommerce ul.products li.product, .no-js .woocommerce-page ul.products li.product { margin-right: ' . $gutter . 'px; }';
	$content .= '.woocommerce ul.products li.product, .woocommerce-page ul.products li.product { margin-bottom: ' . $gutter . 'px; }';
	$content .= '.bk_products_wrap div.bk-pagination-wrap { margin-bottom: ' . $gutter . 'px; }';
	
	if( !$remove_side_gutter ) {
		$content .= '.bk_products_wrap { margin-left: ' . $gutter . 'px; margin-right: ' . $gutter . 'px; }';
	}
	$content .= '</style>';
}

echo $content;
echo '<div class="bk_products_wrap" data-products_base_size="' . $base_size . '" data-products_gutter="' . $gutter . '">';
?>
	<ul class="products">