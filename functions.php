<?php
/**
 * This file adds functions to the Mackman WordPress theme.
 *
 * @format
 * @package MackmanBlocks
 * @author  Adam Chamberlin
 * @link    https://mackman.co.uk
 */

namespace MG\MGBlocks;

// Prevents directly accessing plugin files
if (!function_exists('add_action')) {
  echo 'Seems like you got here by accident. 😛';
  exit();
}

/**
 * Theme setup
 */
function theme_setup() {
  /*
   * Enable support for Post Thumbnails on posts and pages.
   */
  add_theme_support('post-thumbnails');

  /*
   * Enable support for Post excerpts on pages.
   */
  add_post_type_support('page', 'excerpt');

  /*
   * Remove core block patterns.
   */
  remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', __NAMESPACE__ . '\theme_setup');

/**
 * Filter to limit media uploader maximum file size
 *
 * @param	mixed	$file	the uploaded file item to filter
 * @return array $file the filtered file item with response
 */
function limit_upload_file_size($file) {
  // Set the filesize limits
  $image_limit = 1024; // size in KB
  $others_limit = 2048; // size in KB

  // exclude admins
  if (!current_user_can('manage_options')) {
    // Get the filesize of upload
    $size = $file['size'] / 1024; // Calculate down to KB

    // Set a flag
    $is_image = false;

    // Check to see if we are uploading an image
    if (strpos($file['type'], 'image') !== false) {
      $is_image = true;
    }

    // Logic to work out if we should be uploading or not
    if ($is_image == true && $size > $image_limit) {
      $file['error'] = sprintf(
        __('WARNING: You should not upload images larger than %d KB. Please reduce the image file size and try again.'),
        $image_limit,
      );
    } elseif ($is_image == false && $size > $others_limit) {
      $file['error'] = sprintf(
        __('WARNING: You should not upload files larger than %d KB. Please reduce the file size and try again.'),
        $others_limit,
      );
    }
  }

  // Return the file
  return $file;
}
add_filter('wp_handle_upload_prefilter', __NAMESPACE__ . '\limit_upload_file_size');

/**
 * Filter the upload size limit for all users
 *
 * @param string $size Upload size limit (in bytes).
 * @return int (maybe) Filtered size limit.
 */
function filter_site_upload_size_limit($size) {
  // Set the upload size limit to 100 KB for users who are able to read a post (every user). Swap to !current_user_can(manage_options) for everyone except admins.
  if (current_user_can('read')) {
    // 1024 bytes in a kb, so times by required limit.
    $size = 1024 * 2048;
  }
  return $size;
}
add_filter('upload_size_limit', __NAMESPACE__ . '\filter_site_upload_size_limit', 20);

/**
 * Enqueue scripts and styles.
 */
function assets_and_scripts() {
  // Get the asset maps.
  $script_asset = include get_parent_theme_file_path('assets/js/main.asset.php');
  $style_asset = include get_parent_theme_file_path('assets/css/screen.asset.php');

  // Load our main stylesheet.
  wp_enqueue_style(
    'mackman-blocks-style',
    get_parent_theme_file_uri('assets/css/screen.css'),
    $style_asset['dependencies'],
    $style_asset['version'],
  );

  // Load our main JavaScript.
  wp_enqueue_script(
    'mackman-blocks-script',
    get_template_directory_uri() . '/assets/js/main.js',
    $script_asset['dependencies'],
    $script_asset['version'],
    true,
  );
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\assets_and_scripts');

/**
 * Load the editor assets
 */
function editor_assets() {
  // Get the asset maps.
  $script_asset = include get_parent_theme_file_path('assets/js/editor.asset.php');
  $style_asset = include get_parent_theme_file_path('assets/css/editor.asset.php');
  $block_variations_asset = include get_parent_theme_file_path('assets/js/block-variations.asset.php');

  // Enqueue the editor script.
  wp_enqueue_script(
    'mackman-blocks-editor-script',
    get_parent_theme_file_uri('assets/js/editor.js'),
    $script_asset['dependencies'],
    $script_asset['version'],
    true,
  );

  // Enqueue the editor styles.
  wp_enqueue_style(
    'mackman-blocks-editor-style',
    get_parent_theme_file_uri('assets/css/editor.css'),
    $style_asset['dependencies'],
    $style_asset['version'],
  );
}
add_action('enqueue_block_editor_assets', __NAMESPACE__ . '\editor_assets');

/**
 * Add the tracking snippets for the head in here
 */
function tracking_code_head() {
  ?>

<!-- Google Tag Manager -->
<!-- End Google Tag Manager -->

<?php
}
add_action('wp_head', __NAMESPACE__ . '\tracking_code_head');

/**
 * Add the tracking snippets for the body in here
 */
function tracking_code_body() {
  ?>

<!-- Google Tag Manager (noscript) -->
<!-- End Google Tag Manager (noscript) -->

<?php
}
add_action('wp_body_open', __NAMESPACE__ . '\tracking_code_body');
