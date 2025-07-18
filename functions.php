<?php
/**
 * This file adds functions to the Mackman WordPress theme.
 *
 * @package MackmanBlocks
 * @author  Adam Chamberlin
 * @link    https://mackman.co.uk
 */

namespace MG\MGBlocks;

//  Prevents directly accessing plugin files
if (!function_exists("add_action")) {
  echo "Seems like you got here by accident. 😛";
  exit();
}

/**
 * Theme setup
 */
function theme_setup() {

  /*
   * Enable support for Post Thumbnails on posts and pages.
   */
  add_theme_support("post-thumbnails");
  
  /*
   * Enable support for Post excerpts on pages.
   */
  add_post_type_support("page", "excerpt");

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
  $script_asset = include get_parent_theme_file_path(
    "assets/js/main.asset.php"
  );
  $style_asset = include get_parent_theme_file_path(
    "assets/css/screen.asset.php"
  );

  // Load our main stylesheet.
  wp_enqueue_style(
    "mackman-blocks-style",
    get_parent_theme_file_uri("assets/css/screen.css"),
    $style_asset["dependencies"],
    $style_asset["version"]
  );

  // Load our main JavaScript.
  wp_enqueue_script(
    "mackman-blocks-script",
    get_template_directory_uri() . "/assets/js/main.js",
    $script_asset["dependencies"],
    $script_asset["version"],
    true
  );
}
add_action("wp_enqueue_scripts", __NAMESPACE__ . "\assets_and_scripts");

/**
 * Load the editor assets
 */
function editor_assets() {
  // Get the asset maps.
  $script_asset = include get_parent_theme_file_path(
    "assets/js/editor.asset.php"
  );
  $style_asset = include get_parent_theme_file_path(
    "assets/css/editor.asset.php"
  );
  $block_variations_asset = include get_parent_theme_file_path(
    "assets/js/block-variations.asset.php"
  );

  // Enqueue the editor script.
  wp_enqueue_script(
    "mackman-blocks-editor-script",
    get_parent_theme_file_uri("assets/js/editor.js"),
    $script_asset["dependencies"],
    $script_asset["version"],
    true
  );

  // Enqueue the editor styles.
  wp_enqueue_style(
    "mackman-blocks-editor-style",
    get_parent_theme_file_uri("assets/css/editor.css"),
    $style_asset["dependencies"],
    $style_asset["version"]
  );
}
add_action("enqueue_block_editor_assets", __NAMESPACE__ . '\editor_assets');

/**
 * Add the tracking snippets for the head in here
 */
function tracking_code_head() {
  ?>

<!-- Google Tag Manager -->
<!-- End Google Tag Manager -->

<?php
}
add_action("wp_head", __NAMESPACE__ . '\tracking_code_head');

/**
 * Add the tracking snippets for the body in here
 */
function tracking_code_body() {
  ?>

<!-- Google Tag Manager (noscript) -->
<!-- End Google Tag Manager (noscript) -->

<?php
}
add_action("wp_body_open", __NAMESPACE__ . '\tracking_code_body');