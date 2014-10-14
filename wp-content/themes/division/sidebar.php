<?php
/*
Prints out sidebar
*/
$sidebar_classes = '';

global $post;

$entry_layout = '';

$page_sidebars = "fullwidth";
$sidebar_id = -1;

if( bk_is_woocommerce_activated() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
	$page_sidebars = get_option("bk_shop_archives_entry_layout", "fullwidth");
	$sidebar_id = get_option("bk_shop_archives_sidebar_secondary_id");
} else if( is_page() || is_single() ) {
	$page_sidebars = get_post_meta( $post->ID, '_bk_single_entry_layout', true );
	$sidebar_id = get_post_meta($post->ID, '_bk_page_sidebar_secondary_id', true);
} else if(is_search() || is_category() || is_day() || is_month() || is_year() || is_tag() || is_author() || is_tax('post_format') ) {
	$page_sidebars = get_option("bk_blog_archives_entry_layout", "fullwidth");
	$sidebar_id = get_option("bk_blog_archives_sidebar_secondary_id");
} else if( is_tax('filter') || is_tax('portfolio_skills') ) {
	$page_sidebars = get_option("bk_portfolio_archives_entry_layout", "fullwidth");
	$sidebar_id = get_option("bk_portfolio_archives_sidebar_secondary_id");
} else if( is_404() ) {
	$page_sidebars = get_post_meta( get_option("bk_404_page_id"), '_bk_single_entry_layout', true );
	$sidebar_id = get_post_meta(get_option("bk_404_page_id"), '_bk_page_sidebar_secondary_id', true);
}

switch( $page_sidebars ) {	
	case "left_sidebar":
			$sidebar_classes = 'sidebar-left span4 pull-left';
	break;
		
	case "right_sidebar":
			$sidebar_classes = 'sidebar-right span4';
	break;
}

if( ( $page_sidebars == "left_sidebar" || $page_sidebars == "right_sidebar" ) && ($sidebar_id != -1) ) :

?>
<div class="bk-sidebar bk-secondary-sidebar <?php echo $sidebar_classes ?>">
	<?php if ( is_active_sidebar( $sidebar_id ) ) : ?>
	<ul class="sidebar-content clearfix">
		<?php dynamic_sidebar( $sidebar_id ); ?>
	</ul>
	<?php endif; ?>
</div>
<?php endif; ?>