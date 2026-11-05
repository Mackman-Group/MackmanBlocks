<?php
/**
 * This file adds functions to the Mackman WordPress theme.
 *
 * @format
 * @package MackmanBlocks
 * @author  Adam Chamberlin
 * @link    https://mackman.co.uk
 */

namespace MGBlocks;

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
 * Prevent loading of remote block patterns, which otherwise calls out to
 * wordpress.org on every editor load.
 */
add_filter('should_load_remote_block_patterns', '__return_false');

/**
 * Remove the "Archive:"/"Category:" prefix WordPress adds to archive titles.
 */
add_filter('get_the_archive_title_prefix', '__return_false');

/**
 * Drop jQuery Migrate on the front end. Everything that loads jQuery here
 * works with jQuery 3 without it.
 *
 * @param	\WP_Scripts	$scripts	the script registry being filtered
 */
function remove_jquery_migrate($scripts) {
  if (is_admin() || empty($scripts->registered['jquery'])) {
    return;
  }

  $scripts->registered['jquery']->deps = array_diff(
    $scripts->registered['jquery']->deps,
    array('jquery-migrate'),
  );
}
add_action('wp_default_scripts', __NAMESPACE__ . '\remove_jquery_migrate');

/**
 * Drop Dashicons for logged-out visitors. Registered as an empty style
 * rather than dequeued, so a plugin that lists it as a dependency won't error.
 */
function remove_dashicons() {
  if (is_user_logged_in()) {
    return;
  }

  wp_deregister_style('dashicons');
  wp_register_style('dashicons', false);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\remove_dashicons', 100);

/**
 * Preload the body font theme.json's global styles actually use, so it
 * follows the design system rather than a hard-coded path. Prints nothing
 * if the default font has no self-hosted fontFace (for example a system
 * font stack, or no regular/400 weight registered).
 */
function preload_fonts() {
  if (!class_exists('\WP_Font_Face_Resolver')) {
    return;
  }

  $typography = wp_get_global_styles(array('typography'));

  if (empty($typography['fontFamily']) || !preg_match('/--wp--preset--font-family--([a-z0-9-]+)/', $typography['fontFamily'], $slug_match)) {
    return;
  }

  $default_family = '';

  foreach ((array) wp_get_global_settings(array('typography', 'fontFamilies')) as $font) {
    if (($font['slug'] ?? '') === $slug_match[1]) {
      $default_family = strtolower(trim($font['fontFamily'] ?? '', '"\' '));
      break;
    }
  }

  if ('' === $default_family) {
    return;
  }

  foreach (\WP_Font_Face_Resolver::get_fonts_from_theme_json() as $faces) {
    foreach ($faces as $face) {
      $family = strtolower(trim($face['font-family'] ?? '', '"\' '));

      if ($family !== $default_family
        || '400' !== (string) ($face['font-weight'] ?? '400')
        || 'normal' !== ($face['font-style'] ?? 'normal')
        || empty($face['src'])) {
        continue;
      }

      $src = (string) reset($face['src']);
      $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));

      printf(
        '<link rel="preload" href="%s" as="font" type="font/%s" crossorigin>' . "\n",
        esc_url($src),
        esc_attr($extension),
      );
      return;
    }
  }
}
add_action('wp_head', __NAMESPACE__ . '\preload_fonts', 1);

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
        __('WARNING: You should not upload images larger than %d KB. Please reduce the image file size and try again.', 'mackman-blocks'),
        $image_limit,
      );
    } elseif ($is_image == false && $size > $others_limit) {
      $file['error'] = sprintf(
        __('WARNING: You should not upload files larger than %d KB. Please reduce the file size and try again.', 'mackman-blocks'),
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
 * @param	int	$bytes	the default upload size limit, in bytes
 * @return int the filtered upload size limit, in bytes
 */
function filter_site_upload_size_limit($bytes) {
  // Raise the upload limit to 2MB for any user able to read content (every logged-in user).
  // Swap to !current_user_can('manage_options') to limit everyone except admins instead.
  if (current_user_can('read')) {
    // 1024 bytes in a KB, so times by the required limit in KB.
    return 1024 * 2048;
  }

  return $bytes;
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
 * Add the post type and slug to the body class, for example "page-about",
 * so a single page can be targeted in SCSS without extra markup.
 *
 * @param	array	$classes	the existing body classes
 * @return array the filtered body classes
 */
function add_slug_body_class($classes) {
  if (is_archive()) {
    return $classes;
  }

  global $post;

  if (isset($post)) {
    $classes[] = $post->post_type . '-' . $post->post_name;
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\add_slug_body_class');

/**
 * Work out an image's natural size by reading it from disk, for a local
 * file only. For an SVG, read its viewBox, and only when its inline style
 * sets the shown size, since without one its size would change.
 *
 * @param	string	$src	the image src attribute to resolve
 * @return array{0: int, 1: int}|array{} the [width, height] pair, or empty if it could not be resolved
 */
function local_image_size($src) {
  static $sizes = array();

  if (isset($sizes[$src])) {
    return $sizes[$src];
  }

  $path = '';
  $uploads = wp_get_upload_dir();
  $url = strtok($src, '?');

  if (str_starts_with($url, $uploads['baseurl'] . '/')) {
    $path = $uploads['basedir'] . substr($url, strlen($uploads['baseurl']));
  } elseif (str_starts_with($url, '/wp-content/') || str_starts_with($url, content_url('/'))) {
    $path = WP_CONTENT_DIR . substr($url, strpos($url, '/wp-content/') + strlen('/wp-content'));
  }

  $size = array();

  if ($path && !str_contains($path, '..') && is_readable($path)) {
    if (str_ends_with(strtolower($path), '.svg')) {
      $svg = file_get_contents($path, false, null, 0, 4096);

      if ($svg && preg_match('/<svg\b[^>]*>/i', $svg, $tag)) {
        if (preg_match('/\bwidth="([\d.]+)(?:px)?"/', $tag[0], $w) && preg_match('/\bheight="([\d.]+)(?:px)?"/', $tag[0], $h)) {
          $size = array($w[1], $h[1]);
        } elseif (preg_match('/\bviewBox="[\d.\-]+[\s,]+[\d.\-]+[\s,]+([\d.]+)[\s,]+([\d.]+)"/', $tag[0], $box)) {
          $size = array($box[1], $box[2]);
        }
      }
    } else {
      $info = wp_getimagesize($path);
      $size = $info ? array($info[0], $info[1]) : array();
    }
  }

  if ($size) {
    $size = array(max(1, (int) round($size[0])), max(1, (int) round($size[1])));
  }

  return $sizes[$src] = $size;
}

/**
 * Add missing width/height to an image block that references a local file,
 * so the browser can reserve the space before it loads.
 *
 * @param	string	$block_content	the image block's rendered HTML
 * @return string the filtered block HTML
 */
function image_block_dimensions($block_content) {
  if (!class_exists('\WP_HTML_Tag_Processor')) {
    return $block_content;
  }

  $processor = new \WP_HTML_Tag_Processor($block_content);

  if (!$processor->next_tag('IMG') || $processor->get_attribute('width') || $processor->get_attribute('height')) {
    return $block_content;
  }

  $src = (string) $processor->get_attribute('src');
  $style = (string) $processor->get_attribute('style');

  if (str_ends_with(strtolower(strtok($src, '?')), '.svg') && !preg_match('/(?:^|;)\s*(?:width|height)\s*:\s*(?!auto)[\d.]/i', $style)) {
    return $block_content;
  }

  $size = local_image_size($src);

  if (!$size) {
    return $block_content;
  }

  $processor->set_attribute('width', (string) $size[0]);
  $processor->set_attribute('height', (string) $size[1]);

  return $processor->get_updated_html();
}
add_filter('render_block_core/image', __NAMESPACE__ . '\image_block_dimensions');

/**
 * Leave out anything wider than WordPress's own 2560px upload limit from an
 * image's srcset, so an original uploaded larger than that (and never
 * scaled down) does not get served to every browser.
 *
 * @param	array	$sources	the srcset sources, keyed by width
 * @return array the filtered srcset sources
 */
function cap_srcset_width($sources) {
  $capped = array_filter(
    $sources,
    function ($width) {
      return (int) $width <= 2560;
    },
    ARRAY_FILTER_USE_KEY,
  );

  return $capped ?: $sources;
}
add_filter('wp_calculate_image_srcset', __NAMESPACE__ . '\cap_srcset_width');

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

/**
 * Contact Form 7 + Cloudflare Turnstile, the standard form stack for sites
 * built from this theme. Only load CF7's own JS/CSS on pages that actually
 * render a form, and hold Turnstile back until a visitor starts using one,
 * rather than loading either on every page by default. Every function here
 * is inert if Contact Form 7 is not installed and active.
 */
add_filter('wpcf7_load_js', '__return_false');
add_filter('wpcf7_load_css', '__return_false');

/**
 * @param	bool	$found	true once a contact form has been seen on this request
 * @return bool whether a contact form has been seen on this request
 */
function has_contact_form($found = false) {
  static $has_form = false;
  if ($found) {
    $has_form = true;
  }
  return $has_form;
}

/**
 * @param	mixed	$output	the shortcode's rendered output, passed through unchanged
 * @param	string	$tag	the shortcode tag being rendered
 * @return mixed the unchanged shortcode output
 */
function flag_contact_form($output, $tag) {
  if (in_array($tag, array('contact-form-7', 'contact-form'), true)) {
    has_contact_form(true);
  }
  return $output;
}
add_filter('do_shortcode_tag', __NAMESPACE__ . '\flag_contact_form', 10, 2);

/**
 * Enqueue Contact Form 7's assets only on a page that rendered a form,
 * otherwise drop Turnstile too, since it has nothing to protect.
 */
function contact_form_assets() {
  if (!function_exists('wpcf7_enqueue_scripts')) {
    return;
  }

  if (!has_contact_form()) {
    wp_dequeue_script('cloudflare-turnstile');
    return;
  }

  wpcf7_enqueue_scripts();
  wpcf7_enqueue_styles();
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\contact_form_assets', 20);

/**
 * @param	bool	$delayed	true once Turnstile's script tag has been held back on this request
 * @return bool whether Turnstile's script tag has been held back on this request
 */
function turnstile_delayed($delayed = false) {
  static $has_delayed = false;
  if ($delayed) {
    $has_delayed = true;
  }
  return $has_delayed;
}

/**
 * Make Turnstile's script tag inert; turnstile_loader() below loads it for
 * real on a visitor's first interaction with a form.
 *
 * @param	string	$tag	the script tag being printed
 * @param	string	$handle	the registered handle the tag belongs to
 * @return string the filtered script tag
 */
function delay_turnstile($tag, $handle) {
  if ('cloudflare-turnstile' !== $handle || !class_exists('\WP_HTML_Tag_Processor')) {
    return $tag;
  }

  $script = new \WP_HTML_Tag_Processor($tag);

  while ($script->next_tag('script')) {
    $src = $script->get_attribute('src');

    if ('cloudflare-turnstile-js' !== $script->get_attribute('id') || !$src) {
      continue;
    }

    $script->remove_attribute('src');
    $script->set_attribute('type', 'text/plain');
    $script->set_attribute('data-mgblocks-delayed-src', $src);
    turnstile_delayed(true);
    break;
  }

  return $script->get_updated_html();
}
add_filter('script_loader_tag', __NAMESPACE__ . '\delay_turnstile', 10, 2);

/**
 * Print the script that swaps Turnstile's held-back tag for a real one on
 * first interaction with any form.
 */
function turnstile_loader() {
  if (!turnstile_delayed()) {
    return;
  }
  ?>

<script>
( () => {
  const events = [ 'focusin', 'pointerdown', 'keydown', 'input' ];
  const load = ( event ) => {
    if ( ! event.target.closest || ! event.target.closest( 'form' ) ) {
      return;
    }
    events.forEach( ( type ) => document.removeEventListener( type, load, true ) );
    document.querySelectorAll( 'script[data-mgblocks-delayed-src]' ).forEach( ( held ) => {
      const script = document.createElement( 'script' );
      script.src = held.dataset.mgblocksDelayedSrc;
      script.async = true;
      held.replaceWith( script );
    } );
  };
  events.forEach( ( type ) => document.addEventListener( type, load, true ) );
} )();
</script>

<?php
}
add_action('wp_footer', __NAMESPACE__ . '\turnstile_loader', 100);
