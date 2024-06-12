<?php
/**
 * This file adds functions to the Mackman WordPress theme.
 *
 * @package MackmanBlocks
 * @author  Adam Chamberlin
 * @link    https://mackman.co.uk
 */

namespace MG\MGBlocks;

/**
 * Theme setup
 */
function theme_setup() {

  /**
  * Let WordPress manage the document title.
  * This theme does not use a hard-coded <title> tag in the document head,
  * WordPress will provide it for us.
  */
  add_theme_support( 'title-tag' );

  /*
  * Enable support for Post Thumbnails on posts and pages.
  */
  add_theme_support( 'post-thumbnails' );

  /*
  * Enable support for Post excerpts on pages.
  */
  add_post_type_support( 'page', 'excerpt' );

  /*
  * Remove core block patterns.
  */
  remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\theme_setup' );

/**
 * Enqueue scripts and styles.
 */
function assets_and_scripts() {

  // Get the asset maps.
  $script_asset = include get_parent_theme_file_path( 'assets/js/main.asset.php' );
  $style_asset  = include get_parent_theme_file_path( 'assets/css/screen.asset.php' );

  // Load our main stylesheet.
  wp_enqueue_style(
    'evac-style',
    get_parent_theme_file_uri( 'assets/css/screen.css' ),
    $style_asset['dependencies'],
    $style_asset['version']
  );

  // Load our main JavaScript.
  wp_enqueue_script(
    'evac-script',
    get_template_directory_uri() . '/assets/js/main.js',
    $script_asset['dependencies'],
    $script_asset['version'],
    true
  );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\assets_and_scripts' );
