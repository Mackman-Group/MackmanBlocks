<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> 
    <title><?php
        if ( is_single() ) { single_post_title(); }
        elseif ( is_home() || is_front_page() ) { bloginfo('name'); print ' | '; bloginfo('description'); bk_get_page_number(); }
        elseif ( is_page() ) { single_post_title(''); }
        elseif ( is_search() ) { bloginfo('name'); print ' | Search results for ' . esc_html($s); bk_get_page_number(); }
        elseif ( is_404() ) { bloginfo('name'); print ' | Not Found'; }
        else { bloginfo('name'); wp_title('|'); bk_get_page_number(); }
    ?></title>
    <script src="//cdn.optimizely.com/js/316241203.js"></script>
    <?php
		  $favicon_url = get_option("bk_header_favicon_url");
		  if( !empty($favicon_url) ) :
	?>
    	<link rel="shortcut icon" href="<?php echo $favicon_url; ?>">
    <?php endif; ?>
    
    <?php
		  $apple_touch_icon_url = get_option("bk_header_apple_touch_icon_url");
		  if( !empty($apple_touch_icon_url) ) :
	?>
    	<link rel="apple-touch-icon" href="<?php echo $apple_touch_icon_url; ?>">
    <?php endif; ?>
 
    <!-- RSS and pingbacks-->
    <link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url'); ?>" title="<?php printf( __( '%s latest posts', 'corpora_theme' ), esc_html( get_bloginfo('name'), 1 ) ); ?>" />
    <link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'corpora_theme' ), esc_html( get_bloginfo('name'), 1 ) ); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	  
	<!-- Theme hook-->
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-4666434-1', 'mackmangroup.co.uk');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>

<div class="secondary-nav">

  <div class="wrapper">
  	<div class="tel">Get in touch today by calling <b>01787 388038</b></div>
  	<?php wp_nav_menu( array( 'theme_location' => 'top-menu' ) ); ?>
  </div><!-- .wrapper -->
</div><!-- .secondary-nav -->

  <div id="bk-main-wrap">
	<?php bk_print_header_navigation() ;?>