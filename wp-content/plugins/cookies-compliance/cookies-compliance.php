<?php
// Plugin Name: Cookie Compliance Tool
// Version: 2.0
// Plugin URI: http://www.mackmangroup.co.uk
// Description: Wraps Google Analytics tags in a permissions based web banner to better comply with EU legislation.
// Author: Luke Roberts @ Mackman Group
// Author URI: http://www.mackmangroup.co.uk

// Add plugins to <head>
add_action( 'wp_enqueue_scripts', 'add_cookies_scripts' );

function add_cookies_scripts() {
  wp_enqueue_style( 'facybox', plugins_url( '/facybox/facybox.css', __FILE__ ) );
  wp_enqueue_style( 'compliance', plugins_url( '/cookies-compliance.css', __FILE__ ) );
  wp_enqueue_script( 'facybox-js', plugins_url( '/facybox/facybox.js', __FILE__ ), array( 'jquery') );
  wp_enqueue_script( 'jscookie', plugins_url( '/jsCookie-1.2.min.js', __FILE__ ) );
  wp_enqueue_script( 'compliance-js', plugins_url( '/cookies-compliance.js', __FILE__ ), array( 'jquery', 'jscookie', 'facybox-js' ) );
}
?>