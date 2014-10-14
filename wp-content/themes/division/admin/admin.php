<?php
/*
 BillyKid's Admin Interface v 2.0
*/

/*
 * Some global variables
*/

$bk_default_theme_settings_restored = false;
$bk_force_old_media_uploader = false;
$bk_google_apis_webfonts_key = 'AIzaSyAslOi6Kk8nWVIKw4t2SFpu-xWwI0gj900';
$bk_theme_version = '2.0';

// add admin page under appearance
function bk_add_admin() {
	if( isset($_POST["bk-reset-default-key"]) && "bk-restore-default-theme-settings" == $_POST["bk-reset-default-key"] ) {
		bk_admin_reset_theme_settings();
		global $bk_default_theme_settings_restored;
		$bk_default_theme_settings_restored = true;
	}

	add_theme_page( 'Division', 'Theme Settings', 'edit_theme_options', 'bk_theme_settings', 'bk_theme_settings_interface');
}
add_action( 'admin_menu', 'bk_add_admin' );

/*
 set width of visual editor
*/
add_action( 'after_setup_theme', 'bk_admin_theme_setup' );
function bk_admin_theme_setup() {
	set_user_setting( 'dfw_width', 962 );
}

/*
 * Remove view link form admin and edit permalink for following custom post types
 * 
 */
/**
 * removes the 'view' link for CPT in the admin bar
 */
function bk_admin_bar_custom_posts_remove_view_link() {
	global $wp_admin_bar;

	$remove_for = array('testimonial', 'pricing_table', 'quick_gallery', 'gallery');
	
	if( in_array(get_post_type(), $remove_for) ){
		$wp_admin_bar->remove_menu('view');
	}

}

add_action( 'wp_before_admin_bar_render', 'bk_admin_bar_custom_posts_remove_view_link' );

/*
 * remove post row actions view link for following posts
 */
function bk_remove_row_actions( $actions )
{
	$remove_for = array('testimonial', 'pricing_table', 'quick_gallery', 'gallery');
	
	if( in_array(get_post_type(), $remove_for) ){
		unset( $actions['view'] );
	}
		
	return $actions;
}

add_filter( 'post_row_actions', 'bk_remove_row_actions', 10, 1 );

/*
 * hide permalink, edit permalink button, view and preview button for following posts
 */
function bk_post_type_admin_css() {
	$remove_for = array('testimonial', 'pricing_table', 'quick_gallery', 'gallery');
	
	if( in_array(get_post_type(), $remove_for) ){
		echo '<style type="text/css">#post-preview, #edit-slug-box{display: none;}</style>';
	}	
}
add_action( 'admin_head-post-new.php', 'bk_post_type_admin_css' );
add_action( 'admin_head-post.php', 'bk_post_type_admin_css' );

/*
 * Add custom column with thumbnail for gallery post type
 */

/*
 add new column
*/
function bk_add_gallery_custom_post_type_columns($columns) {
	unset($columns['date']);
	$columns['bk-thumbnail'] = __('Image', 'corpora_theme');
	$columns['date'] = __('Date');
	return $columns;
}

/*
 Make post thumbnails visible on slides manage page
*/
function bk_gallery_custom_post_type_columns($column_name, $post_id) {
	if ($column_name == 'bk-thumbnail') {
		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail($post_id)) ) {
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail');
				echo '<img class="bk-manage-posts-thumb" src="' . $thumb[0] . '" />';
		}		
	}
}

add_filter('manage_gallery_posts_columns', 'bk_add_gallery_custom_post_type_columns');
add_action('manage_gallery_posts_custom_column', 'bk_gallery_custom_post_type_columns', 10, 2);
/*
 Shortcode generatoc tiny mce intergation
*/
add_action('admin_init', 'bk_shortcode_button');
function bk_shortcode_button() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
		return;
	}

	if ( get_user_option('rich_editing') == 'true' ) {
		add_filter( 'mce_external_plugins', 'add_bk_mce_plugin' );
		add_filter( 'mce_buttons', 'register_bk_button' );
	}

	add_editor_style('admin/tinymce/css/custom-editor-style.css');
}

function add_bk_mce_plugin( $plugin_array ) {
	$plugin_array['bk_shortcodes'] = get_template_directory_uri() . '/admin/tinymce/plugin.js';
	return $plugin_array;
}

function register_bk_button( $buttons ) {
	array_push( $buttons, "|", "bk_button" );
	return $buttons;
}

/*
 Add scripts for admin area
*/
function bk_print_admin_scripts() {

	wp_register_script('admin-functions', get_template_directory_uri() . '/admin/js/admin-functions.js', array( 'jquery', 'thickbox', 'media-upload' )  );
	wp_register_script('qtip2', get_template_directory_uri() . '/admin/js/jquery.qtip.min.js', 'jquery'  );
	wp_register_script('colorpicker-js', get_template_directory_uri() . '/admin/js/colorpicker.js', array('jquery') );
	wp_register_script('choosen-js', get_template_directory_uri() . '/admin/js/chosen.jquery.min.js', array('jquery') );

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-mouse');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script('jquery-ui-button');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('qtip2');
	wp_enqueue_script('colorpicker-js');
	wp_enqueue_script('choosen-js');
	wp_enqueue_script('admin-functions');

	global $bk_force_old_media_uploader;
	$use_new_media_uploader = true;

	if( function_exists( 'wp_enqueue_media' ) && version_compare(bk_get_wp_version(), '3.5', '>=') && !$bk_force_old_media_uploader ) {
		wp_enqueue_media();
		$use_new_media_uploader = true;
	} else {
		wp_enqueue_style('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		$use_new_media_uploader = false;
	}

	wp_localize_script( 'admin-functions', 'bkajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
	'use_new_media_uploader' => $use_new_media_uploader ) );
	wp_localize_script( 'jquery', 'bkglobal', array( 'tinymce_plugin_root_dir' => get_template_directory_uri() .'/admin/tinymce',
	'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'admin_print_scripts', 'bk_print_admin_scripts' );

/*
 * Get wordpress version
*
*/
function bk_get_wp_version() {
	global $wp_version;
	return $wp_version;
}


/*
 Add styles for admin
*/
function bk_print_admin_styles() {
	wp_register_style('admin-style', get_template_directory_uri() . '/admin/css/bk_admin.css' );
	wp_register_style('jqueryui_style', get_template_directory_uri() . '/admin/css/smoothness/jquery-ui-1.8.24.custom.css' );
	wp_register_style('colorpicker-style', get_template_directory_uri() . '/admin/css/colorpicker.css' );
	wp_register_style('qtip2-style', get_template_directory_uri() . '/admin/css/jquery.qtip.min.css' );
	wp_register_style('choosen-style', get_template_directory_uri() . '/admin/css/chosen.css' );
	 
	//Enqueue styles
	wp_enqueue_style('qtip2-style');
	wp_enqueue_style('jqueryui_style');
	wp_enqueue_style('colorpicker-style');
	wp_enqueue_style('choosen-style');
	wp_enqueue_style('admin-style');
}

add_action( 'admin_print_styles', 'bk_print_admin_styles' );

/*
 create unique sidebar id based on sidebar name - lowercase spaces and all wierd signs stripped str
*/
function get_unique_sidebar_id( $sidebars, $sidebar_name ) {
	$sidebar_id = str_replace(' ', '_', strtolower( $sidebar_name ) );		// lowercase replace spaces with hard sapces
	$sidebar_proposal = $sidebar_id;
	$counter = 0;
	while( sidebar_key_exists( $sidebars, $sidebar_id ) ) {
		$counter++;
		$sidebar_id = $sidebar_proposal . '_' . $counter;
	}

	return $sidebar_id;
}

function sidebar_key_exists( $sidebars, $sidebar_id ) {
	foreach( $sidebars as $sidebar ) {
		if( $sidebar['bk-sidebar-id'] == $sidebar_id ) {
			return true;
		}
	}
	return false;
}

$bk_theme_settings = array(
		array( 'name' => __( 'General', 'corpora_theme' ),
				'icon' => 'general-settings',
				'sections' => array(
						array(
								'name' => __( 'General', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Admin Email', 'corpora_theme' ),
												'id' => 'bk_admin_email',
												'description' => __( 'Type in e-mail address that You want to use as admin email for all contact forms on this page or leave it blank to use wordress admin email', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => ''
										),
										array(
												'name' => __( 'Email Subject Prefix', 'corpora_theme' ),
												'id' => 'bk_contactform_email',
												'description' => __( 'Type in email subject prefix that will be use for all contact form messages.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => '[Division Contact Form] '
										),
										array( 'name' => __( 'Email Succes Info', 'corpora_theme' ),
												'id' => 'bk_email_success_info',
												'description' => __( 'Place here content that will appear as message for user in case of successfully send email via contact form e.g "Thank You very much, Your message was sent."', 'corpora_theme' ),
												'type' => 'rich_textarea',
												'default' =>  'Thank You very much, Your message was sent.'
										),
										array( 'name' => __( 'Email Fail Info', 'corpora_theme' ),
												'id' => 'bk_email_fail_info',
												'description' => __( 'Place here content that will appear as message for user in case of contact form failure e.g "We are very sorry something went wrong. Please try again later."', 'corpora_theme' ),
												'type' => 'rich_textarea',
												'default' =>  'We are very sorry something went wrong. Please try again later.'
										),
										array( 'name' => __( 'Protected Page Text', 'corpora_theme' ),
												'id' => 'bk_protected_page_text',
												'description' => __( 'Place here content that will appear above form for protected page.', 'corpora_theme' ),
												'type' => 'rich_textarea',
												'default' =>  'This page is password protected. To view it please enter your password below:'
										),
								)
						),
						array( 'name' => __( 'Sidebars', 'corpora_theme' ),
								'type'	=> 'sidebars_list',
								'options' => array(
										array(
												'name' => __( 'Add Sidebar', 'corpora_theme' ),
												'id' => 'bk_add_new_sidebar',
												'description' => __( 'Add new sidebar. Create sidebars add widgets to each sidebar under Appearance -> Widgets and assign any sidebar to any post / page / portfolio entry.', 'corpora_theme' ),
												'type' => 'tb_browse_button',
										)
								)
						),
						array( 'name' => __( '404 Page', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( '404 page content', 'corpora_theme' ),
												'id' => 'bk_404_page_id',
												'description' => __( 'Choose which page should be used as 404 page. If no page will be selecetd standard text will be shown.', 'corpora_theme' ),
												'type' => 'post_combobox',
												'post_type' => 'page',
												'default' => -1
										)
								)
						),
						array( 'name' => __( 'Twitter', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Consumer Key', 'corpora_theme' ),
												'id' => 'bk_twitter_consumer_key',
												'description' => __( 'Provide Consumer Key of Your twitter application. Please read docs on how to register Your twitter application.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => ''
										),
										array(
												'name' => __( 'Consumer Secret', 'corpora_theme' ),
												'id' => 'bk_twitter_consumer_secret',
												'description' => __( 'Provide Consumer Secret of Your twitter application. Please read docs on how to register Your twitter application.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => ''
										),
										array(
												'name' => __( 'Access Token', 'corpora_theme' ),
												'id' => 'bk_twitter_access_token',
												'description' => __( 'Provide Access Token of Your twitter application. Please read docs on how to register Your twitter application.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => ''
										),
										array(
												'name' => __( 'Access Token Secret', 'corpora_theme' ),
												'id' => 'bk_twitter_access_token_secret',
												'description' => __( 'Provide Access Token Secret of Your twitter application. Please read docs on how to register Your twitter application.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => ''
										)
										
								)
						),
						array( 'name' => __( 'Advanced', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Custom style', 'corpora_theme' ),
												'id' => 'bk_custom_stylesheet',
												'description' => __( 'Place here any custom stylesheet that You would like to add to Your site. Do it at Your own risk. Do not include style tags!!!', 'corpora_theme' ),
												'type' => 'textarea',
												'default' =>  ''
										),
										array(
												'name' => __( 'Custom script', 'corpora_theme' ),
												'id' => 'bk_custom_javascript',
												'description' => __( 'Place here any custom javascript like Google tracking code or so on. Do it at Your own risk. Do not include script tags!!!', 'corpora_theme' ),
												'type' => 'textarea',
												'default' =>  ''
										)
								)
						)
				)
		),
		array( 'name' => __( 'Header', 'corpora_theme' ),
				'icon' => 'general-settings',
				'sections' => array(
						array( 'name' => __( 'General', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Header Layout', 'corpora_theme' ),
												'id' => 'bk_header_layout',
												'description' => __( 'Choose header layout and position.', 'corpora_theme' ),
												'type' => 'stacked_combobox',
												'default' => 'bk-fixed-header-left',
												'all_values' => array(
														"bk-fixed-header-left" => array( "label" => "Fixed Left Header", "buddy" => "#buddy_wrapbk_header_tagline, #buddy_wrapbk_header_menu, #buddy_wrapbk_header_logo_top_margin, #buddy_wrapbk_header_logo_bottom_margin" ),
														"bk-classic-header-top" => array( "label" => "Classic Header Top", "buddy" => "#buddy_wrapbk_header_menu_float, #buddy_wrapbk_header_logo_top_margin_horizontal_header, #buddy_wrapbk_navigation_top_margin, #buddy_wrapbk_navigation_bottom_margin, #buddy_wrapbk_social_icons_top_margin" ),
														"bk-fixed-header-top" => array( "label" => "Fixed Header Top", "buddy" => "#buddy_wrapbk_header_menu_float, #buddy_wrapbk_header_logo_top_margin_horizontal_header, #buddy_wrapbk_navigation_top_margin, #buddy_wrapbk_navigation_bottom_margin, #buddy_wrapbk_social_icons_top_margin" )
												)
										),
										array(
												'name' => __( 'Header Menu', 'corpora_theme' ),
												'id' => 'bk_header_menu',
												'description' => __( 'Choose header menu.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'bk-stacked-menu',
												'all_values' => array(	"bk-sfmenu" => "Dropdown Navigation",
														"bk-stacked-menu" => "Stacked Navigation"
												)
										),
										array(
												'name' => __( 'Menu Float', 'corpora_theme' ),
												'id' => 'bk_header_menu_float',
												'description' => __( 'Choose how horizontal menu will be floated to the left or right side.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'right',
												'all_values' => array(	"left" => "Left",
														"right" => "Right"
												)
										),
										array( 'name' => __( 'Header Tagline', 'corpora_theme' ),
												'id' => 'bk_header_tagline',
												'description' => __( 'Place here content that will appear fixed at the bottom of navigation bar e.g Your copyrights or links.', 'corpora_theme' ),
												'type' => 'rich_textarea',
												'default' =>  ''
										),
										array(
												'name' => __( 'Favicon', 'corpora_theme' ),
												'id' => 'bk_header_favicon_url',
												'description' => __( 'This image will be used as favicon that will be displayed on browser tab next to page name. Please use 16x16, 32x32, 48x48, or 64x64 pixels in size, and 8-bit, 24-bit, or 32-bit in color depth *.ico file. PNG, JPG, GIF are also possible but may not be displayed correctly in older browsers.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Apple Touch Icons', 'corpora_theme' ),
												'id' => 'bk_header_apple_touch_icon_url',
												'description' => __( 'This image will be used as apple touch icon and will be displayed if user saves bookmark of Your page to their apple/android(at least some of android) device desktop. Please use 57x57, 72x72, 114x114, 144x144 pixels in size *.png file. Bigger image sizes are used for retina devices and will be scaled down on non-retina so please use bigger sizes to get best effects.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Collapsible Header', 'corpora_theme' ),
												'id' => 'bk_collapsible_header',
												'description' => __( 'If enabled header will display triangle shaped button at the top which will allow user to collapse header in order to gain more space for content. If fullscreen background slideshow is applied to page/post/portfolio then this button will also toggle slideshow navigation. This option can be overridden per post/page/portfolio basis.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => true
										),
										array(
												'name' => __( 'Header Collapsed Onload', 'corpora_theme' ),
												'id' => 'bk_header_collapsed_onload',
												'description' => __( 'If enabled header will be collapsed on page load. This option can be overridden per post/page/portfolio basis.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Navigation Top Margin', 'corpora_theme' ),
												'id' => 'bk_navigation_top_margin',
												'description' => __( 'Specify top margin of site navigation.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 30,
												'min' => 1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Navigation Bottom Margin', 'corpora_theme' ),
												'id' => 'bk_navigation_bottom_margin',
												'description' => __( 'Specify bottom margin site navigation.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 30,
												'min' => 1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Social Icons', 'corpora_theme' ),
												'id' => 'bk_social_icons_top_margin',
												'description' => __( 'Specify top margin for social icons.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 20,
												'min' => 1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Logo Top Margin', 'corpora_theme' ),
												'id' => 'bk_header_logo_top_margin_horizontal_header',
												'description' => __( 'Specify top margin of header logo.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 10,
												'min' => 1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Logo Top Margin', 'corpora_theme' ),
												'id' => 'bk_header_logo_top_margin',
												'description' => __( 'Specify top margin of header logo.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 30,
												'min' => 1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Logo Bottom Margin', 'corpora_theme' ),
												'id' => 'bk_header_logo_bottom_margin',
												'description' => __( 'Specify bottom margin of header logo.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 30,
												'min' => 1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Logo', 'corpora_theme' ),
												'id' => 'bk_header_logo_url',
												'description' => __( 'This image will be used as header logo.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Retina(HiDPI) Logo', 'corpora_theme' ),
												'id' => 'bk_header_retina_logo_url',
												'description' => __( 'This image will be used as header logo for retina (HiDPI) displays. Leave it blank to use default logo. This image should be four times bigger than standard logo e.g if standar logo is 100px x 100px this image should have size 200px x 200px.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Mobile Logo Top Margin', 'corpora_theme' ),
												'id' => 'bk_responsive_header_logo_top_margin',
												'description' => __( 'Specify top margin for header responsive navigation (active while site is browsed on mobile devices).', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 10,
												'min' => 1,
												'max' => 200,
												'step' => 1
										),
										array(
												'name' => __( 'Mobile Logo Bottom Margin', 'corpora_theme' ),
												'id' => 'bk_responsive_header_logo_bottom_margin',
												'description' => __( 'Specify bottom margin for header responsive navigation (active while site is browsed on mobile devices).', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 10,
												'min' => 1,
												'max' => 200,
												'step' => 1
										),

								)
						),
						array( 'name' => __( 'Social', 'corpora_theme' ),
								'type'	=> 'social_sortable_list',
								'options' => array(
										array(
												'name' => __( 'Add Icon', 'corpora_theme' ),
												'id' => 'bk_social_icons_list',
												'description' => __( 'Add social icons to the list with this button. Drag&drop to change order of social icons.', 'corpora_theme' ),
												'type' => 'tb_browse_button',
										)
								)
						)
				)
		),
		array( 'name' => __( 'Blog', 'corpora_theme' ),
				'icon' => 'general-settings',
				'sections' => array(
						array(
								'name' => __( 'General', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Show Love Button', 'corpora_theme' ),
												'id' => 'bk_post_show_love_button',
												'description' => __( 'If enabled love/like button wil be visible for post items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 2,
												'default' => true
										),
										array(
												'name' => __( 'Love Button Timeout', 'corpora_theme' ),
												'id' => 'bk_post_love_button_timeout',
												'description' => __( 'Specify timeout for love button. It is time ( described in hours ) that have to pass before same user can click love button again on the same post and make counter increase. Set to -1 to allow user to click only once in lifetime on any post.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 24,
												'min' => -1,
												'max' => 720,
												'step' => 1
										),
										array(
												'name' => __( 'Reset Love Counter', 'corpora_theme' ),
												'id' => 'bk_reset_blog_love_counter',
												'description' => __( 'Click button on the right side to reset blog love counter', 'corpora_theme' ),
												'token' => 'bk_reset_blog_love_counter',
												'type' => 'reset_button',
										),
										array(
												'name' => __( 'Excerpt Length', 'corpora_theme' ),
												'id' => 'bk_blog_post_excerpt_length',
												'description' => __( 'Specify lenght of excerpt for Your blog entries in word count.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 50,
												'min' => 1,
												'max' => 300,
												'step' => 1
										),
										array(
												'name' => __( 'Show Next/Prev Links', 'corpora_theme' ),
												'id' => 'show_next_prev_posts_link',
												'description' => __( 'If enabled links to next and previous posts will be shown for single posts.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => true
										),
										array(
												'name' => __( 'Blog Homepage Link', 'corpora_theme' ),
												'id' => 'bk_blog_homepage_link',
												'description' => __( 'Choose which page will be treated as blog hompage. It will be use to style main menu link (highlight) when single post is displayed.', 'corpora_theme' ),
												'type' => 'post_combobox',
												'post_type' => 'page',
												'default' => -1
										),
										array( 'name' => __( 'Protected Post Text', 'corpora_theme' ),
												'id' => 'bk_protected_post_text',
												'description' => __( 'Place here content that will appear above form for protected posts.', 'corpora_theme' ),
												'type' => 'rich_textarea',
												'default' =>  'This post is password protected. To view it please enter your password below:'
										),
								)
						),
						array(
								'name' => __( 'Social', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Show Facebook Button', 'corpora_theme' ),
												'id' => 'bk_blog_show_facebook_button',
												'description' => __( 'If enabled facebook button will be visible in single blog items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Facbook Count', 'corpora_theme' ),
												'id' => 'bk_blog_facebook_button_count',
												'description' => __( 'If enabled facebook button will show like count otherwise it will show social text.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Show Tweet Button', 'corpora_theme' ),
												'id' => 'bk_blog_show_tweet_button',
												'description' => __( 'If enabled twitter button will be visible in single blog items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Tweet Count', 'corpora_theme' ),
												'id' => 'bk_blog_tweet_button_count',
												'description' => __( 'If enabled twitter button will show tweet count.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Show Google +1 Button', 'corpora_theme' ),
												'id' => 'bk_blog_show_google_plus_button',
												'description' => __( 'If enabled google +1 button will be visible in single blog items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Google +1 Count', 'corpora_theme' ),
												'id' => 'bk_blog_google_plus_button_count',
												'description' => __( 'If enabled google +1 button will show tweet count.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Show Pinterest Button', 'corpora_theme' ),
												'id' => 'bk_blog_show_pinterest_button',
												'description' => __( 'If enabled pinterest button will be visible in single post items. To make it work correctly make sure to specify featured image that will be pinned for each post.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Pinterest Count', 'corpora_theme' ),
												'id' => 'bk_blog_show_pinterest_button_count',
												'description' => __( 'If enabled pinterest button will show pin count count.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										)
								)
						),
						array(
								'name' => __( 'Search/Archives', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Page Layout', 'corpora_theme' ),
												'id' => 'bk_blog_archives_page_layout',
												'description' => __( 'Choose layout for blog archives/search page.', 'corpora_theme' ),
												'type' => 'stacked_image_radiobuttons',
												'default' => 'fullwidth',
												'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fullwidht_page_padding"  ),
														"fixed_centered" => array( "label" => "Fixed Centered", "image" => "fixed-centered.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" ),
														"fixed_float_right" => array( "label" => "Fixed Float Right", "image" => "fixed-float-right.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" ),
														"fixed_float_left" => array( "label" => "Fixed Float Left", "image" => "fixed-float-left.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" )
												)
										),
										array(
												'name' => __( 'Show Page Title', 'corpora_theme' ),
												'id' => 'bk_blog_archives_show_page_title',
												'description' => __( 'If enabled default page title e.g "Search results for..." , "Archives for 2013" will be displayed otherwise not.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => true
										),
										array(
												'name' => __( 'Fullwidth Page Padding', 'corpora_theme' ),
												'id' => 'bk_blog_archives_fullwidht_page_padding',
												'description' => __( 'Specify padding for fullwidth blog archives/search page. Usually 0 for fullwidth page.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 0,
												'min' => 0,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Fixed Page Width', 'corpora_theme' ),
												'id' => 'bk_blog_archives_fixed_page_width',
												'description' => __( 'Specify maximum width for blog archives/search fixed page. If device screen size will be smaller than this width page will be resized to use 100% of available space.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 1024,
												'min' => 0,
												'max' => 1920,
												'step' => 1
										),
										array(
												'name' => __( 'Primary Sidebar', 'corpora_theme' ),
												'id' => 'bk_blog_archives_sidebar_id',
												'description' => __( 'Choose primary sidebar for blog archives/search page. It will be displayed on the left side below menu.', 'corpora_theme' ),
												'type' => 'sidebar_combobox',
												'default' => -1
										),

										array(
												'name' => __( 'Entry Layout', 'corpora_theme' ),
												'id' => 'bk_blog_archives_entry_layout',
												'description' => __( 'Choose layout for blog archives/search page. Choose if and where sidebar will be displayed.', 'corpora_theme' ),
												'type' => 'stacked_image_radiobuttons',
												'default' => 'fullwidth',
												'valid_for' => 'post, page',
												'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "" ),
														"left_sidebar" => array( "label" => "Left Sidebar", "image" => "left-description.png", "buddy" => "#buddy_wrapbk_portfolio_archives_sidebar_secondary_id"),
														"right_sidebar" => array( "label" => "Right Sidebar", "image" => "right-description.png", "buddy" => "#buddy_wrapbk_portfolio_archives_sidebar_secondary_id" )
												)
										),
										array(
												'name' => __( 'Secondary Sidebar', 'corpora_theme' ),
												'id' => 'bk_blog_archives_sidebar_secondary_id',
												'description' => __( 'Choose secondary sidebar for portfolio archives page.', 'corpora_theme' ),
												'valid_for' => 'post, page',
												'type' => 'sidebar_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Base Size', 'corpora_theme' ),
												'id' => 'bk_blog_archives_base_size',
												'description' => __( 'Choose blog archives layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'large',
												'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
														'medium' => __( 'Medium', 'corpora_theme' ),
														'large' => __( 'Large', 'corpora_theme' ),
														'bk-classic-blog' => __( 'Classic One Column', 'corpora_theme' ) )
										),
										array(
												'name' => __( 'Gutter', 'corpora_theme' ),
												'id' => 'bk_blog_archives_gutter',
												'description' => __( 'Choose gutter size which will be visible between blog elements.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 7,
												'min' => 0,
												'max' => 40,
												'step' => 1
										)
								)
						),
						array(
								'name' => __( 'Translations', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Read More Link Text', 'corpora_theme' ),
												'id' => 'bk_post_read_more_link_text',
												'description' => __( 'This is text that will be displayed as "Read More" link to full blog post under blog excerpt on your blog page.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Read More...'
										),
										array(
												'name' => __( 'Like Button Active Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_like_active_text',
												'description' => __( 'This is text displayed for user as tooltip over blog post entry like button when it is active - user have not clicked on it yet e.g "Love It!"', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Love this'
										),
										array(
												'name' => __( 'Like Button Inactive Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_like_inactive_text',
												'description' => __( 'This is text displayed for user as tooltip over blog post like button when it is inactive - user already clicked it e.g "You already love it"', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'You already love this'
										),
										array(
												'name' => __( 'Load More Button Active Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_load_more_button_active_text',
												'description' => __( 'This is text displayed on active (when there are still some blog posts to load) ajax "Load More Items" button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Load More...'
										),
										array(
												'name' => __( 'Load More Button Loading Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_load_more_button_loading_text',
												'description' => __( 'This is text displayed on loading (when blog posts are being loaded) ajax "Load More Items" button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Loading...'
										),
										array(
												'name' => __( 'Load More Button Inactive Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_load_more_button_inactive_text',
												'description' => __( 'This is text displayed on inactive (when all blog posts were loaded) ajax "Load More Items" button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'No more items'
										),
										array(
												'name' => __( 'Infinite Scroll Active Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_infinite_scroll_active_text',
												'description' => __( 'This is text displayed on active (when there are still some blog posts to load) ajax "Load More Items" infinite scroll popup button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Load More...'
										),
										array(
												'name' => __( 'Infinite Scroll Inactive Text', 'corpora_theme' ),
												'id' => 'bk_post_ajax_infinite_scroll_inactive_text',
												'description' => __( 'This is text displayed on inactive (when all blog posts items were loaded) ajax "Load More Items" infinite scroll popup button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'No more items'
										)
								)
						)
				)
		),
		array( 'name' => __( 'Portfolio', 'corpora_theme' ),
				'icon' => 'general-settings',
				'sections' => array(
						array(
								'name' => __( 'General', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Show Love Button', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_love_button',
												'description' => __( 'If enabled love/like button wil be visible for portfolio items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 2,
												'default' => true
										),
										array(
												'name' => __( 'Love Button Timeout', 'corpora_theme' ),
												'id' => 'bk_portfolio_love_button_timeout',
												'description' => __( 'Specify timeout for love button. It is time ( described in hours ) that have to pass before same user can click love button again on the same portfolio and make counter increase. Set to -1 to allow user to click only once in lifetime on any portfolio.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 24,
												'min' => -1,
												'max' => 720,
												'step' => 1
										),
										array(
												'name' => __( 'Reset Love Counter', 'corpora_theme' ),
												'id' => 'bk_reset_portfolio_love_counter',
												'description' => __( 'Click button on the right side to reset portfolio love counter', 'corpora_theme' ),
												'token' => 'bk_reset_portfolio_love_counter',
												'type' => 'reset_button',
										),
										array(
												'name' => __( 'Show Views', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_views',
												'description' => __( 'If enabled portfolio views icon will be visible.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 2,
												'default' => true
										),
										array(
												'name' => __( 'Show Views Timeout', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_views_timeout',
												'description' => __( 'Specify timeout for show counter. It is time ( described in hours ) that have to pass before same users page refresh on the same portfolio make counter increase. Set to -1 if You want one user to increase counter with pageview only once in lifetime.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 24,
												'min' => -1,
												'max' => 720,
												'step' => 1
										),
										array(
												'name' => __( 'Reset View Counter', 'corpora_theme' ),
												'id' => 'bk_reset_portfolio_view_counter',
												'description' => __( 'Click button on the right side to reset portfolio view counter', 'corpora_theme' ),
												'token' => 'bk_reset_portfolio_view_counter',
												'type' => 'reset_button',
										),
										array(
												'name' => __( 'Show Portfolio Navigation', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_navigation',
												'description' => __( 'If enabled single portfolio will display navigation that will allow user to go to next/prev portfolio item or back to portfolio main page.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => true
										),
										array(
												'name' => __( 'Portfolio Homepage Link', 'corpora_theme' ),
												'id' => 'bk_portfolio_homepage_link',
												'description' => __( 'Choose which page will be treated as portfolio hompage and its link will be displayed in single portfolio navigation. It will be use also to style main menu link (highlight) when single portfolio is displayed.', 'corpora_theme' ),
												'type' => 'post_combobox',
												'post_type' => 'page',
												'default' => -1
										),
										array( 'name' => __( 'Protected Portfolio Text', 'corpora_theme' ),
												'id' => 'bk_protected_portfolio_text',
												'description' => __( 'Place here content that will appear above form for protected portfolios.', 'corpora_theme' ),
												'type' => 'rich_textarea',
												'default' =>  'This portfolio is password protected. To view it please enter your password below:'
										),
								)
						),
						array(
								'name' => __( 'Social', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Show Facebook Button', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_facebook_button',
												'description' => __( 'If enabled facebook button will be visible in single portfolio items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Facbook Count', 'corpora_theme' ),
												'id' => 'bk_portfolio_facebook_button_count',
												'description' => __( 'If enabled facebook button will show like count otherwise it will show social text.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Show Tweet Button', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_tweet_button',
												'description' => __( 'If enabled twitter button will be visible in single portfolio items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Tweet Count', 'corpora_theme' ),
												'id' => 'bk_portfolio_tweet_button_count',
												'description' => __( 'If enabled twitter button will show tweet count.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Show Google +1 Button', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_google_plus_button',
												'description' => __( 'If enabled google +1 button will be visible in single portfolio items.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Google +1 Count', 'corpora_theme' ),
												'id' => 'bk_portfolio_google_plus_button_count',
												'description' => __( 'If enabled google +1 button will show tweet count.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										),
										array(
												'name' => __( 'Show Pinterest Button', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_pinterest_button',
												'description' => __( 'If enabled pinterest button will be visible in single portfolio items. To make it work correctly make sure to specify featured image that will be pinned for each portfolio entry.', 'corpora_theme' ),
												'type' => 'checkbox_section',
												'rows' => 1,
												'default' => false
										),
										array(
												'name' => __( 'Show Pinterest Count', 'corpora_theme' ),
												'id' => 'bk_portfolio_show_pinterest_button_count',
												'description' => __( 'If enabled pinterest button will show pin count count.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => false
										)
								)
						),
						array(
								'name' => __( 'Search/Archives', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Page Layout', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_page_layout',
												'description' => __( 'Choose layout for portfolio archives page.', 'corpora_theme' ),
												'type' => 'stacked_image_radiobuttons',
												'default' => 'fullwidth',
												'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fullwidht_page_padding"  ),
														"fixed_centered" => array( "label" => "Fixed Centered", "image" => "fixed-centered.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" ),
														"fixed_float_right" => array( "label" => "Fixed Float Right", "image" => "fixed-float-right.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" ),
														"fixed_float_left" => array( "label" => "Fixed Float Left", "image" => "fixed-float-left.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" )
												)
										),
										array(
												'name' => __( 'Show Page Title', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_show_page_title',
												'description' => __( 'If enabled default page title for portfolio archives e.g "Portfolios for category Media" , "Portfolios tagged Phottoshop" will be displayed otherwise not.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => true
										),
										array(
												'name' => __( 'Fullwidth Page Padding', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_fullwidht_page_padding',
												'description' => __( 'Specify padding for fullwidth archives portfolio page. Usually 0 for fullwidth page.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 0,
												'min' => 0,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Fixed Page Width', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_fixed_page_width',
												'description' => __( 'Specify maximum width for portfolio archives fixed page. If device screen size will be smaller than this width page will be resized to use 100% of available space.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 1024,
												'min' => 0,
												'max' => 1920,
												'step' => 1
										),
										array(
												'name' => __( 'Primary Sidebar', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_sidebar_id',
												'description' => __( 'Choose primary sidebar for portfolio archives page. It will be displayed on the left side below menu.', 'corpora_theme' ),
												'type' => 'sidebar_combobox',
												'default' => -1
										),
											
										array(
												'name' => __( 'Entry Layout', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_entry_layout',
												'description' => __( 'Choose layout for portfolio archives page. Choose if and where sidebar will be displayed.', 'corpora_theme' ),
												'type' => 'stacked_image_radiobuttons',
												'default' => 'fullwidth',
												'valid_for' => 'post, page',
												'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "" ),
														"left_sidebar" => array( "label" => "Left Sidebar", "image" => "left-description.png", "buddy" => "#buddy_wrapbk_portfolio_archives_sidebar_secondary_id"),
														"right_sidebar" => array( "label" => "Right Sidebar", "image" => "right-description.png", "buddy" => "#buddy_wrapbk_portfolio_archives_sidebar_secondary_id" )
												)
										),
										array(
												'name' => __( 'Secondary Sidebar', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_sidebar_secondary_id',
												'description' => __( 'Choose secondary sidebar for portfolio archives page.', 'corpora_theme' ),
												'valid_for' => 'post, page',
												'type' => 'sidebar_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Portfolios Per Page', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_portfolios_per_page',
												'description' => __( 'Specify how many portfolio items will be displayed in archives for one page. If more items will match archives query then pagination will be used to navigate between pages. Use -1 to show all matched items.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 10,
												'min' => -1,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Archives Layout', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_layout',
												'description' => __( 'Choose layout for portfolio archives page.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'grid',
												'all_values' => array('grid' => __( 'Grid', 'corpora_theme' ),
														'metro' => __( 'Metro', 'corpora_theme' ),
														'masonry' => __( 'Masonry', 'corpora_theme' ),
														'mixed_masonry' => __( 'Mixed Masonry', 'corpora_theme' )
												)
										),
										array(
												'name' => __( 'Base Size', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_base_size',
												'description' => __( 'Choose portfolio archives layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'small',
												'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
														'medium' => __( 'Medium', 'corpora_theme' ),
														'large' => __( 'Large', 'corpora_theme' ) )
										),
										array(
												'name' => __( 'Description', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_description',
												'description' => __( 'Choose optional description for each portfolio item, it will be displayed below portfolio thumb.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'title',
												'all_values' => array('none' => __( 'None', 'corpora_theme' ),
														'title' => __( 'Title', 'corpora_theme' ),
														'excerpt' => __( 'Excerpt', 'corpora_theme' ),
														'title_and_excerpt' => __( 'Title & Excerpt', 'corpora_theme' ) )
										),
										array(
												'name' => __( 'Thumb Effect', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_image_effect',
												'description' => __( 'Choose optional portfolio thumb effect, it will be visible when user hovers over image.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'bk_zoom',
												'all_values' => array('bk_none' => __( 'None', 'corpora_theme' ),
														'bk_zoom' => __( 'Zoom Image', 'corpora_theme' ),
														'bk_grayscale' => __( 'Grayscale', 'corpora_theme' ),
														'bk_reverse_grayscale' => __( 'Reverse Grayscale', 'corpora_theme' ) )
										),
										array(
												'name' => __( 'Image Overlay', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_image_overlay',
												'description' => __( 'Choose optional image overlay, it will be visible when user hovers over image.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'title_and_tags',
												'all_values' => array('none' => __( 'None', 'corpora_theme' ),
														'title' => __( 'Title', 'corpora_theme' ),
														'title_and_tags' => __( 'Title & Tags', 'corpora_theme' ),
														'title_and_cats' => __( 'Title & Categories', 'corpora_theme' ),
														'tags' => __( 'Tags', 'corpora_theme' ),
														'cats' => __( 'Categories', 'corpora_theme' ),
														'icon' => __( 'Icon', 'corpora_theme' ) )
										),
										array(
												'name' => __( 'Gutter', 'corpora_theme' ),
												'id' => 'bk_portfolio_archives_gutter',
												'description' => __( 'Choose gutter size which will be visible between portfolio elements.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 10,
												'min' => 0,
												'max' => 40,
												'step' => 1
										)
								)
						),
						array(
								'name' => __( 'Translations', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Single Portfolo Slug', 'corpora_theme' ),
												'id' => 'bk_portfolio_single_slug',
												'description' => __( 'This setting controls how Your single portfolio URL will look like when permalinks are enabled e.g http://www.my-site.com/portfolio/my-single-project . To change URL to something else e.g "projects" or "works "simply type in "projects", "works" into this field. Rebuild permalinke structure after saving theme settings!', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'portfolio'
										),
										array(
												'name' => __( 'Like Button Active Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_like_active_text',
												'description' => __( 'This is text displayed for user as tooltip over portfolio entry like button when it is active - user have not clicked on it yet e.g "Love It!"', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Love this'
										),
										array(
												'name' => __( 'Like Button Inactive Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_like_inactive_text',
												'description' => __( 'This is text displayed for user as tooltip over portfolio entry like button when it is inactive - user already clicked it e.g "You already love it"', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'You already love this'
										),
										array(
												'name' => __( 'Next Entry Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_next_entry_text',
												'description' => __( 'This is text displayed for user as tooltip for next button when portfolio content or excerpt is displayed in ajax manner (without refreshing whole page) above all grid thumbnail.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Next'
										),
										array(
												'name' => __( 'Previous Entry Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_prev_entry_text',
												'description' => __( 'This is text displayed for user as tooltip for prev button when portfolio content or excerpt is displayed in ajax manner (without refreshing whole page) above all grid thumbnail.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Previous'
										),
										array(
												'name' => __( 'Close Entry Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_close_entry_text',
												'description' => __( 'This is text displayed for user as tooltip for close button when portfolio content or excerpt is displayed in ajax manner (without refreshing whole page) above all grid thumbnail.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Close'
										),
										array(
												'name' => __( 'Load More Button Active Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_load_more_button_active_text',
												'description' => __( 'This is text displayed on active (when there are still some portfolio entries to load) ajax "Load More Items" button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Load More...'
										),
										array(
												'name' => __( 'Load More Button Loading Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_load_more_button_loading_text',
												'description' => __( 'This is text displayed on loading (when portfolio items are being loaded) ajax "Load More Items" button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Loading...'
										),
										array(
												'name' => __( 'Load More Button Inactive Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_load_more_button_inactive_text',
												'description' => __( 'This is text displayed on inactive (when all portfolio items were loaded) ajax "Load More Items" button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'No more items'
										),
										array(
												'name' => __( 'Infinite Scroll Active Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_infinite_scroll_active_text',
												'description' => __( 'This is text displayed on active (when there are still some portfolio entries to load) ajax "Load More Items" infinite scroll popup button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'Load More...'
										),
										array(
												'name' => __( 'Infinite Scroll Inactive Text', 'corpora_theme' ),
												'id' => 'bk_portfolio_ajax_infinite_scroll_inactive_text',
												'description' => __( 'This is text displayed on inactive (when all portfolio items were loaded) ajax "Load More Items" infinite scroll popup button.', 'corpora_theme' ),
												'type' => 'textinput',
												'default' => 'No more items'
										)
								)
						)
				)
		),
		array( 'name' => __( 'Shop', 'corpora_theme' ),
			   'icon' => 'general-settings',
			   'sections' => array(
			   			array(
				   				'name' => __( 'General', 'corpora_theme' ),
				   				'options' => array(
				   						array(
				   								'name' => __( 'Enable Single Item Popup', 'corpora_theme' ),
				   								'id' => 'bk_shop_single_item_popup',
				   								'description' => __( 'If enbaled single item image and galleries can be opened inside popup by clicking on image.', 'corpora_theme' ),
				   								'type' => 'checkbox',
				   								'default' => true
				   						),
				   						array(
												'name' => __( 'Related Product Count', 'corpora_theme' ),
												'id' => 'bk_shop_related_products_count',
												'description' => __( 'Choose how many related products will be visible for single product at most.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 3,
												'min' => 0,
												'max' => 20,
												'step' => 1
										)
				   				)
			   			),
				   		array(
				   				'name' => __( 'Social', 'corpora_theme' ),
				   				'options' => array(
				   						array(
				   								'name' => __( 'Show Facebook Button', 'corpora_theme' ),
				   								'id' => 'bk_shop_show_facebook_button',
				   								'description' => __( 'If enabled facebook button will be visible in single shop items.', 'corpora_theme' ),
				   								'type' => 'checkbox_section',
				   								'rows' => 1,
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Facbook Count', 'corpora_theme' ),
				   								'id' => 'bk_shop_facebook_button_count',
				   								'description' => __( 'If enabled facebook button will show like count otherwise it will show social text.', 'corpora_theme' ),
				   								'type' => 'checkbox',
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Tweet Button', 'corpora_theme' ),
				   								'id' => 'bk_shop_show_tweet_button',
				   								'description' => __( 'If enabled twitter button will be visible in single shop items.', 'corpora_theme' ),
				   								'type' => 'checkbox_section',
				   								'rows' => 1,
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Tweet Count', 'corpora_theme' ),
				   								'id' => 'bk_shop_tweet_button_count',
				   								'description' => __( 'If enabled twitter button will show tweet count.', 'corpora_theme' ),
				   								'type' => 'checkbox',
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Google +1 Button', 'corpora_theme' ),
				   								'id' => 'bk_shop_show_google_plus_button',
				   								'description' => __( 'If enabled google +1 button will be visible in single shop items.', 'corpora_theme' ),
				   								'type' => 'checkbox_section',
				   								'rows' => 1,
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Google +1 Count', 'corpora_theme' ),
				   								'id' => 'bk_shop_google_plus_button_count',
				   								'description' => __( 'If enabled google +1 button will show tweet count.', 'corpora_theme' ),
				   								'type' => 'checkbox',
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Pinterest Button', 'corpora_theme' ),
				   								'id' => 'bk_shop_show_pinterest_button',
				   								'description' => __( 'If enabled pinterest button will be visible in single shop items. To make it work correctly make sure to specify featured image that will be pinned for each shop entry.', 'corpora_theme' ),
				   								'type' => 'checkbox_section',
				   								'rows' => 1,
				   								'default' => false
				   						),
				   						array(
				   								'name' => __( 'Show Pinterest Count', 'corpora_theme' ),
				   								'id' => 'bk_shop_show_pinterest_button_count',
				   								'description' => __( 'If enabled pinterest button will show pin count count.', 'corpora_theme' ),
				   								'type' => 'checkbox',
				   								'default' => false
				   						)
				   				)
				   		),
						array(
								'name' => __( 'Layout', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Page Layout', 'corpora_theme' ),
												'id' => 'bk_shop_archives_page_layout',
												'description' => __( 'Choose layout for blog archives/search page.', 'corpora_theme' ),
												'type' => 'stacked_image_radiobuttons',
												'default' => 'fullwidth',
												'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fullwidht_page_padding"  ),
														"fixed_centered" => array( "label" => "Fixed Centered", "image" => "fixed-centered.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" ),
														"fixed_float_right" => array( "label" => "Fixed Float Right", "image" => "fixed-float-right.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" ),
														"fixed_float_left" => array( "label" => "Fixed Float Left", "image" => "fixed-float-left.png", "buddy" => "#buddy_wrapbk_portfolio_archives_fixed_page_width" )
												)
										),
										array(
												'name' => __( 'Show Page Title', 'corpora_theme' ),
												'id' => 'bk_shop_archives_show_page_title',
												'description' => __( 'If enabled default page title e.g "Search results for..." , "Archives for 2013" will be displayed otherwise not.', 'corpora_theme' ),
												'type' => 'checkbox',
												'default' => true
										),
										array(
												'name' => __( 'Fixed Page Width', 'corpora_theme' ),
												'id' => 'bk_shop_archives_fixed_page_width',
												'description' => __( 'Specify maximum width for blog archives/search fixed page. If device screen size will be smaller than this width page will be resized to use 100% of available space.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 1024,
												'min' => 0,
												'max' => 1920,
												'step' => 1
										),
										array(
												'name' => __( 'Primary Sidebar', 'corpora_theme' ),
												'id' => 'bk_shop_archives_sidebar_id',
												'description' => __( 'Choose primary sidebar for blog archives/search page. It will be displayed on the left side below menu.', 'corpora_theme' ),
												'type' => 'sidebar_combobox',
												'default' => -1
										),
				
										array(
												'name' => __( 'Entry Layout', 'corpora_theme' ),
												'id' => 'bk_shop_archives_entry_layout',
												'description' => __( 'Choose layout for blog archives/search page. Choose if and where sidebar will be displayed.', 'corpora_theme' ),
												'type' => 'stacked_image_radiobuttons',
												'default' => 'fullwidth',
												'valid_for' => 'post, page',
												'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "" ),
														"left_sidebar" => array( "label" => "Left Sidebar", "image" => "left-description.png", "buddy" => "#buddy_wrapbk_portfolio_archives_sidebar_secondary_id"),
														"right_sidebar" => array( "label" => "Right Sidebar", "image" => "right-description.png", "buddy" => "#buddy_wrapbk_portfolio_archives_sidebar_secondary_id" )
												)
										),
										array(
												'name' => __( 'Secondary Sidebar', 'corpora_theme' ),
												'id' => 'bk_shop_archives_sidebar_secondary_id',
												'description' => __( 'Choose secondary sidebar for portfolio archives page.', 'corpora_theme' ),
												'valid_for' => 'post, page',
												'type' => 'sidebar_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Base Size', 'corpora_theme' ),
												'id' => 'bk_shop_archives_base_size',
												'description' => __( 'Choose blog archives layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'large',
												'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
														'medium' => __( 'Medium', 'corpora_theme' ),
														'large' => __( 'Large', 'corpora_theme' ) )
										),
										array(
												'name' => __( 'Gutter', 'corpora_theme' ),
												'id' => 'bk_shop_archives_gutter',
												'description' => __( 'Choose gutter size which will be visible between blog elements.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 7,
												'min' => 0,
												'max' => 40,
												'step' => 1
										),
										array(
												'name' => __( 'Cross Sells', 'corpora_theme' ),
												'id' => 'bk_shop_cross_sells_count',
												'description' => __( 'Choose maximum amount of cross sells items that will be displayed inside cart page based on customer cart content and individual cross-sells items settings. Use -1 to show all.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'integer',
												'default' => 3,
												'min' => -1,
												'max' => 20,
												'step' => 1
										)
								)
						)
				)	
		),
		array( 'name' => __( 'Fonts', 'corpora_theme' ),
				'icon' => 'general-settings',
				'sections' => array(
						array(
								'name' => __( 'Navigation', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Primary Font Type', 'corpora_theme' ),
												'id' => 'bk_navigation_primary_font_type',
												'description' => __( 'Choose font for top level site navigation.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Primary Font Size', 'corpora_theme' ),
												'id' => 'bk_navigation_primary_font_size',
												'description' => __( 'Choose default size of top level site navigation font.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 14,
												'min' => 1,
												'max' => 30,
												'step' => 1
										),
										array(
												'name' => __( 'Secondary Font Type', 'corpora_theme' ),
												'id' => 'bk_navigation_secondary_font_type',
												'description' => __( 'Choose font for optional descriptions under top level site navigation links.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Secondary Font Size', 'corpora_theme' ),
												'id' => 'bk_navigation_secodnary_font_size',
												'description' => __( 'Choose default size of font used for descriptions under top level site navigation links.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 12,
												'min' => 1,
												'max' => 30,
												'step' => 1
										),
										array(
												'name' => __( 'Tertiary Font Type', 'corpora_theme' ),
												'id' => 'bk_navigation_tertiary_font_type',
												'description' => __( 'Choose font for dropdown or stacked lower level navigation links.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Tertiary Font Size', 'corpora_theme' ),
												'id' => 'bk_navigation_tertiary_font_size',
												'description' => __( 'Choose default size of font used for dropdown or stacked lower level navigation links.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 12,
												'min' => 1,
												'max' => 30,
												'step' => 1
										),
										array(
												'name' => __( 'Pagination Font Type', 'corpora_theme' ),
												'id' => 'bk_pagination_font_type',
												'description' => __( 'Choose font for pagination/load more links.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Pagination Font Size', 'corpora_theme' ),
												'id' => 'bk_pagination_font_size',
												'description' => __( 'Choose font size for pagination/load more links.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 13,
												'min' => 1,
												'max' => 30,
												'step' => 1
										)

								)
						),
						array(
								'name' => __( 'Body', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Body Font Type', 'corpora_theme' ),
												'id' => 'bk_body_font_type',
												'description' => __( 'Choose font for main body text.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Body Font Size', 'corpora_theme' ),
												'id' => 'bk_body_font_size',
												'description' => __( 'Choose default size of body font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 13,
												'min' => 1,
												'max' => 30,
												'step' => 1
										),
										array(
												'name' => __( 'Body Font Line Height', 'corpora_theme' ),
												'id' => 'bk_body_line_height',
												'description' => __( 'Choose line height of body text. Line height is amount of space that is consumed by one line of text inside paragraph and it should be bigger than body font size.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 18,
												'min' => 1,
												'max' => 40,
												'step' => 1
										),
										array(
												'name' => __( 'Page Title Font Type', 'corpora_theme' ),
												'id' => 'bk_page_title_font_type',
												'description' => __( 'Choose font type that will be used for page titles.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Page Title Font Size', 'corpora_theme' ),
												'id' => 'bk_page_title_font_size',
												'description' => __( 'Choose size of font used for page titles.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 24,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'Divider / Widget Title Font Type', 'corpora_theme' ),
												'id' => 'bk_divider_font_type',
												'description' => __( 'Choose font type used for dividers on your page. Dividers are widget titles, separators, toggle, accordion and tab headers.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Divider / Widget Title Font Size', 'corpora_theme' ),
												'id' => 'bk_divider_font_size',
												'description' => __( 'Choose size of divider font. Dividers are widget titles, separators, toggle, accordion and tab headers.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 13,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'Widget Post Title Font Type', 'corpora_theme' ),
												'id' => 'bk_widget_post_font_type',
												'description' => __( 'Choose font used for post titles that are place inside widgets e.g recent or popular post.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Widget Post Title Font Size', 'corpora_theme' ),
												'id' => 'bk_widget_post_font_size',
												'description' => __( 'Choose size of font used for post titles that are place inside widgets e.g recent or popular post.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 13,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'H1 Font Type', 'corpora_theme' ),
												'id' => 'bk_h1_font_type',
												'description' => __( 'Choose which font will be used as heading h1.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'H1 Font Size', 'corpora_theme' ),
												'id' => 'bk_h1_font_size',
												'description' => __( 'Choose size of heading h1 font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 30,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'H2 Font Type', 'corpora_theme' ),
												'id' => 'bk_h2_font_type',
												'description' => __( 'Choose which font will be used as heading h2.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'H2 Font Size', 'corpora_theme' ),
												'id' => 'bk_h2_font_size',
												'description' => __( 'Choose size of heading h2 font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 26,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'H3 Font Type', 'corpora_theme' ),
												'id' => 'bk_h3_font_type',
												'description' => __( 'Choose which font will be used as heading h3.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'H3 Font Size', 'corpora_theme' ),
												'id' => 'bk_h3_font_size',
												'description' => __( 'Choose size of heading h3 font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 22,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'H4 Font Type', 'corpora_theme' ),
												'id' => 'bk_h4_font_type',
												'description' => __( 'Choose which font will be used as heading h4.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'H4 Font Size', 'corpora_theme' ),
												'id' => 'bk_h4_font_size',
												'description' => __( 'Choose size of heading h4 font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 18,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'H5 Font Type', 'corpora_theme' ),
												'id' => 'bk_h5_font_type',
												'description' => __( 'Choose which font will be used as heading h5.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'H5 Font Size', 'corpora_theme' ),
												'id' => 'bk_h5_font_size',
												'description' => __( 'Choose size of heading h5 font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 16,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'H6 Font Type', 'corpora_theme' ),
												'id' => 'bk_h6_font_type',
												'description' => __( 'Choose which font will be used as heading h6.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'H6 Font Size', 'corpora_theme' ),
												'id' => 'bk_h6_font_size',
												'description' => __( 'Choose size of heading h6 font', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 14,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),

								)
						),
						array(
								'name' => __( 'Portfolio', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Portfolio Title Font Type', 'corpora_theme' ),
												'id' => 'bk_portfolio_title_font_type',
												'description' => __( 'Choose font type that will be used for single portfolio titles.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Portfolio Title Font Size', 'corpora_theme' ),
												'id' => 'bk_portfolio_title_font_size',
												'description' => __( 'Choose size of font used for single portfolio titles.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 24,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'Thumb Title Font Type', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_title_font_type',
												'description' => __( 'Choose font type for portfolio thumbs title when displayed inside masonry/grid layout - this font will be applied to portfolio thumb titles when user hovers over image.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Thumb Title Font Size', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_title_font_size',
												'description' => __( 'Choose size of font portfolio thumbs title when displayed inside masonry/grid layout.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 18,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'Thumb Tags Font Type', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_tags_font_type',
												'description' => __( 'Choose font type for portfolio thumbs tags when displayed inside masonry/grid layout - this font will be applied to portfolio thumb tags that are under thumb title when user hovers over image.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Thumb Tags Font Size', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_tags_font_size',
												'description' => __( 'Choose size of font portfolio thumbs tags when displayed inside masonry/grid layout.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 10,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'Thumb Description Title Font Type', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_desc_title_font_type',
												'description' => __( 'Choose font type for portfolio thumbs description title when displayed inside masonry/grid layout - this font will be applied to portfolio thumb titles that are displayed below the thumb.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Thumb Description Title Font Size', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_desc_title_font_size',
												'description' => __( 'Choose size of font portfolio thumbs description title when displayed inside masonry/grid layout under thumbs.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 14,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
								)
						),
						array(
								'name' => __( 'Blog', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Blog Title Font Type', 'corpora_theme' ),
												'id' => 'bk_blog_title_font_type',
												'description' => __( 'Choose font type that will be used for single blog titles.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Blog Title Font Size', 'corpora_theme' ),
												'id' => 'bk_blog_title_font_size',
												'description' => __( 'Choose size of font used for single blog titles.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 24,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
										array(
												'name' => __( 'Thumb Title Font Type', 'corpora_theme' ),
												'id' => 'bk_blog_thumb_title_font_type',
												'description' => __( 'Choose font type for blog post thumb title when displayed inside masonry.', 'corpora_theme' ),
												'type' => 'font_type_combobox',
												'default' => -1
										),
										array(
												'name' => __( 'Thumb Title Font Size', 'corpora_theme' ),
												'id' => 'bk_blog_thumb_title_font_size',
												'description' => __( 'Choose size of font blog post thumb title when displayed inside masonry/grid layout.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'pixel',
												'default' => 16,
												'min' => 1,
												'max' => 60,
												'step' => 1
										),
								)
						)

				)
		),
		array( 'name' => __( 'Colors', 'corpora_theme' ),
				'icon' => 'general-settings',
				'sections' => array(
						array(
								'name' => __( 'Navigation', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Background Color', 'corpora_theme' ),
												'id' => 'bk_header_background_color',
												'description' => __( 'Choose background color of navigation section', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Background Image', 'corpora_theme' ),
												'id' => 'bk_header_background_image',
												'description' => __( 'This image will be used as navigation section background.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Retina Background Image', 'corpora_theme' ),
												'id' => 'bk_header_retina_background_image',
												'description' => __( 'This image will be used as navigation section background for devices with retina (HiDpi) displays. It should be Four times bigger than regular background ( two times wider and two times higher ) e.g if regular background is 200px X 200px this one should be 400px X 400px. Levae blank to use default background image.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Background Image Position', 'corpora_theme' ),
												'id' => 'bk_header_background_image_position',
												'description' => __( 'Choose navigation section background image position', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'left top',
												'all_values' => array("left top" => "Left Top",
														"left center" => "Left Center",
														"left bottom" => "Left Bottom",
														"right top" => "Right Top",
														"right center" => "Right Center",
														"right bottom" => "Right Bottom",
														"center top" => "Center Top",
														"center" => "Center",
														"center bottom" => "Center Bottom")
										),
										array(
												'name' => __( 'Background Image Repeat', 'corpora_theme' ),
												'id' => 'bk_header_background_image_repeat',
												'description' => __( 'Choose how background image is repeated, horizontally, vertically, both or none', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'repeat',
												'all_values' => array("repeat" => "Cover All Area",
														"repeat-x" => "Repat Horizontally",
														"repeat-y" => "Repeat Vertically",
														"no-repeat" => "No Repeat")
										),
										array(
												'name' => __( 'Font Color', 'corpora_theme' ),
												'id' => 'bk_header_font_color',
												'description' => __( 'Choose color of font for navigation section.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Link Color', 'corpora_theme' ),
												'id' => 'bk_header_link_color',
												'description' => __( 'Choose color of links for navigation section.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#909090"
										),
										array(
												'name' => __( 'Link Hover Color', 'corpora_theme' ),
												'id' => 'bk_header_link_hover_color',
												'description' => __( 'Choose hover color of links for navigation section.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Menu Text Color', 'corpora_theme' ),
												'id' => 'bk_navigation_text_color',
												'description' => __( 'Choose text color for menu items.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#808080"
										),
										array(
												'name' => __( 'Menu Text Hover Color', 'corpora_theme' ),
												'id' => 'bk_navigation_text_hover_color',
												'description' => __( 'Choose text color for current menu item / hover menu item state.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Submenu Background', 'corpora_theme' ),
												'id' => 'bk_navigation_submenu_color',
												'description' => __( 'Choose background color for submenu.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Pagination Background', 'corpora_theme' ),
												'id' => 'bk_pagination_background',
												'description' => __( 'Choose background for pagination/load more button.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Pagination Color', 'corpora_theme' ),
												'id' => 'bk_pagination_text_color',
												'description' => __( 'Choose default text color for pagination links.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#909090"
										),
										array(
												'name' => __( 'Pagination Hover Color', 'corpora_theme' ),
												'id' => 'bk_pagination_text_hover_color',
												'description' => __( 'Choose text color for hover/active pagination/load more links.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										)
								)
						),
						array(
								'name' => __( 'Page', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Background Color', 'corpora_theme' ),
												'id' => 'bk_page_background_color',
												'description' => __( 'Choose background color of whole page - only visible when boxed layout is selected.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#EEEEEE"
										),
										array(
												'name' => __( 'Background Image', 'corpora_theme' ),
												'id' => 'bk_page_background_image',
												'description' => __( 'This image will be used as page background - only visible when boxed layout is selected.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Retina Background Image', 'corpora_theme' ),
												'id' => 'bk_page_retina_background_image',
												'description' => __( 'This image will be used as body background for devices with retina (HiDpi) displays. It should be Four times bigger than regular background ( two times wider and two times higher ) e.g if regular background is 200px X 200px this one should be 400px X 400px. Levae blank to use default background image - only visible when boxed layout is selected.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Background Image Position', 'corpora_theme' ),
												'id' => 'bk_page_background_image_position',
												'description' => __( 'Choose page background image position - only visible when boxed layout is selected - only visible when boxed layout is selected.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'left top',
												'all_values' => array("left top" => "Left Top",
														"left center" => "Left Center",
														"left bottom" => "Left Bottom",
														"right top" => "Right Top",
														"right center" => "Right Center",
														"right bottom" => "Right Bottom",
														"center top" => "Center Top",
														"center" => "Center",
														"center bottom" => "Center Bottom")
										),
										array(
												'name' => __( 'Background Image Repeat', 'corpora_theme' ),
												'id' => 'bk_page_background_image_repeat',
												'description' => __( 'Choose how page background image is repeated, horizontally, vertically, both or none - only visible when boxed layout is selected.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'repeat',
												'all_values' => array("repeat" => "Cover All Area",
														"repeat-x" => "Repat Horizontally",
														"repeat-y" => "Repeat Vertically",
														"no-repeat" => "No Repeat")
										),
										array(
												'name' => __( 'Background Attachment', 'corpora_theme' ),
												'id' => 'bk_page_background_image_attachment',
												'description' => __( 'Choose how background image will be attached. Should it scroll with the whole page or stay at fixed position  - only visible when boxed layout is selected.', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'fixed',
												'all_values' => array("scroll" => "Scroll with whole page",
														"fixed" => "Stay at fixed position")
										)
								)
						),
						array(
								'name' => __( 'Body', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Background Color', 'corpora_theme' ),
												'id' => 'bk_body_background_color',
												'description' => __( 'Choose background color of page body', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Background Image', 'corpora_theme' ),
												'id' => 'bk_body_background_image',
												'description' => __( 'This image will be used as body background.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Retina Background Image', 'corpora_theme' ),
												'id' => 'bk_body_retina_background_image',
												'description' => __( 'This image will be used as body background for devices with retina (HiDpi) displays. It should be Four times bigger than regular background ( two times wider and two times higher ) e.g if regular background is 200px X 200px this one should be 400px X 400px. Levae blank to use default background image.', 'corpora_theme' ),
												'type' => 'browse_button_with_label',
												'default' => ''
										),
										array(
												'name' => __( 'Background Image Position', 'corpora_theme' ),
												'id' => 'bk_body_background_image_position',
												'description' => __( 'Choose body background image position', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'left top',
												'all_values' => array("left top" => "Left Top",
														"left center" => "Left Center",
														"left bottom" => "Left Bottom",
														"right top" => "Right Top",
														"right center" => "Right Center",
														"right bottom" => "Right Bottom",
														"center top" => "Center Top",
														"center" => "Center",
														"center bottom" => "Center Bottom")
										),
										array(
												'name' => __( 'Background Image Repeat', 'corpora_theme' ),
												'id' => 'bk_body_background_image_repeat',
												'description' => __( 'Choose how background image is repeated, horizontally, vertically, both or none', 'corpora_theme' ),
												'type' => 'combobox',
												'default' => 'repeat',
												'all_values' => array("repeat" => "Cover All Area",
														"repeat-x" => "Repat Horizontally",
														"repeat-y" => "Repeat Vertically",
														"no-repeat" => "No Repeat")
										),
										array(
												'name' => __( 'Font Color', 'corpora_theme' ),
												'id' => 'bk_body_font_color',
												'description' => __( 'Choose color of font for page body.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Link Color', 'corpora_theme' ),
												'id' => 'bk_body_link_color',
												'description' => __( 'Choose color for links inside page body.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#909090"
										),
										array(
												'name' => __( 'Link Hover Color', 'corpora_theme' ),
												'id' => 'bk_body_link_hover_color',
												'description' => __( 'Choose color for links in hover state inside page body.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FF4629"
										),
										array(
												'name' => __( 'Separator Color', 'corpora_theme' ),
												'id' => 'bk_separator_color',
												'description' => __( 'Choose line color for separator and widget title.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Image Overlay Background', 'corpora_theme' ),
												'id' => 'bk_image_overlay_background',
												'description' => __( 'Choose background color for image overlay which is used for portfolio, gallery, blog thumbs and featured images.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#000000"
										),
										array(
												'name' => __( 'Image Overlay Opacity', 'corpora_theme' ),
												'id' => 'bk_image_overlay_opacity',
												'description' => __( 'Specify opacity of overlay background when user hovers over image.', 'corpora_theme' ),
												'type' => 'slider',
												'slider_class_type' => 'percentage',
												'default' => 75,
												'min' => 0,
												'max' => 100,
												'step' => 1
										),
										array(
												'name' => __( 'Image Overlay Text Color', 'corpora_theme' ),
												'id' => 'bk_image_overlay_text_color',
												'description' => __( 'Choose text color for image overlay when user hovers over image.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Image Overlay Tag Color', 'corpora_theme' ),
												'id' => 'bk_image_overlay_tag_color',
												'description' => __( 'Choose text color for image overlay tags which are being displayed under portfolio, gallery thumb title when user hovers over image.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#CCCCCC"
										),
										array(
												'name' => __( 'Image Overlay Tag Hover Color', 'corpora_theme' ),
												'id' => 'bk_image_overlay_tag_hover_color',
												'description' => __( 'Choose hover text color for image overlay tags which are being displayed under portfolio, gallery thumb title when user hovers over image.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Odd Background', 'corpora_theme' ),
												'id' => 'bk_odd_background',
												'description' => __( 'Choose odd background color used for testimonials and comments base.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#F8F8F8"
										),
										array(
												'name' => __( 'Textfield Border', 'corpora_theme' ),
												'id' => 'bk_body_input_text_area_border',
												'description' => __( 'Choose border for any textfield or textarea.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#F8F8F8"
										),
										array(
												'name' => __( 'Textfield Background', 'corpora_theme' ),
												'id' => 'bk_body_input_text_area_background',
												'description' => __( 'Choose background for any  textfield or textarea.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#F8F8F8"
										),
										array(
												'name' => __( 'Textfield Focus Border', 'corpora_theme' ),
												'id' => 'bk_body_input_text_area_border_focus',
												'description' => __( 'Choose border for any focused textfield or textarea.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#E4E4E4"
										),
										array(
												'name' => __( 'Textfield Focus Background', 'corpora_theme' ),
												'id' => 'bk_body_input_text_area_background_focus',
												'description' => __( 'Choose background for any focused textfield or textarea.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										)
								)
						),
						array(
								'name' => __( 'Portfolio', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Navigation Filter Text Color', 'corpora_theme' ),
												'id' => 'bk_navigation_portfolio_filter_text_color',
												'description' => __( 'Choose text color for portfolio filter inside header.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#808080"
										),
										array(
												'name' => __( 'Navigation Filter Text Hover Color', 'corpora_theme' ),
												'id' => 'bk_navigation_portfolio_filter_text_hover_color',
												'description' => __( 'Choose text color for current portfolio filter item / hover filter item state inside header.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Filter Text Color', 'corpora_theme' ),
												'id' => 'bk_portfolio_filter_text_color',
												'description' => __( 'Choose text color for portfolio filter when displayed above portfolio.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#808080"
										),
										array(
												'name' => __( 'Filter Text Hover Color', 'corpora_theme' ),
												'id' => 'bk_portfolio_filter_text_hover_color',
												'description' => __( 'Choose text color for current portfolio filter item / hover filter item state when displayed above portfolio.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Thumb Description Background', 'corpora_theme' ),
												'id' => 'bk_portfolio_thumb_description_background_color',
												'description' => __( 'Choose background color for portfolio thumb description inside masonry / grid layout which is under featured image.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										)
								)
						),
						array(
								'name' => __( 'Blog', 'corpora_theme' ),
								'options' => array(
										array(
												'name' => __( 'Thumb Description Background', 'corpora_theme' ),
												'id' => 'bk_blog_thumb_description_background_color',
												'description' => __( 'Choose background color for blog thumb description inside masonry layout which is under featured content.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Post Title Color', 'corpora_theme' ),
												'id' => 'bk_post_title_color',
												'description' => __( 'Choose color of post title when it is being displayed inside blog grid and contains link to full length article. It will be also applied for widgets with popular, recent, comments posts titles inside main body.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Post Title Hover Color', 'corpora_theme' ),
												'id' => 'bk_post_title_hover_color',
												'description' => __( 'Choose color of post title when it is being displayed inside blog grid and contains link to full length article in hover mode. It will be also applied for widgets with popular, recent, comments posts titles inside main body.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#808080"
										),
										array(
												'name' => __( 'Quote Format Background', 'corpora_theme' ),
												'id' => 'bk_quote_post_format_background',
												'description' => __( 'Choose background color of quote post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Quote Format Text', 'corpora_theme' ),
												'id' => 'bk_quote_post_format_text',
												'description' => __( 'Choose background color of quote post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Link Format Background', 'corpora_theme' ),
												'id' => 'bk_link_post_format_background',
												'description' => __( 'Choose background color of link post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Link Format Text', 'corpora_theme' ),
												'id' => 'bk_link_post_format_text',
												'description' => __( 'Choose text color for link post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Link Format Text Hover', 'corpora_theme' ),
												'id' => 'bk_link_post_format_text_hover',
												'description' => __( 'Choose text color for link post format when hovered.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FF4629"
										),
										array(
												'name' => __( 'Tweet Format Background', 'corpora_theme' ),
												'id' => 'bk_tweet_post_format_background',
												'description' => __( 'Choose background color of tweet/status post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Tweet Format Text', 'corpora_theme' ),
												'id' => 'bk_tweet_post_format_text',
												'description' => __( 'Choose text color for tweet post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Tweet Format Link', 'corpora_theme' ),
												'id' => 'bk_tweet_post_format_link',
												'description' => __( 'Choose link color for tweet post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FF4629"
										),
										array(
												'name' => __( 'Tweet Format Link Hover', 'corpora_theme' ),
												'id' => 'bk_tweet_post_format_link_hover',
												'description' => __( 'Choose link hover color for tweet post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Chat Format Background', 'corpora_theme' ),
												'id' => 'bk_chat_post_format_background',
												'description' => __( 'Choose background color of chat post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Chat Format Text', 'corpora_theme' ),
												'id' => 'bk_chat_post_format_text',
												'description' => __( 'Choose text color of chat post format.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
								)
						),
						array(
								'name' => __( 'Shortcodes', 'corpora_theme' ),
								'options' => array(

										array(
												'name' => __( 'Button Color', 'corpora_theme' ),
												'id' => 'bk_button_background',
												'description' => __( 'Choose color of button. It will be applied to theme default buttons and tag cloud links.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#909090"
										),
										array(
												'name' => __( 'Button Hover Color', 'corpora_theme' ),
												'id' => 'bk_button_hover_background',
												'description' => __( 'Choose color of button in hover state. It will be applied to theme default buttons and tag cloud links.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										),
										array(
												'name' => __( 'Higlight Background', 'corpora_theme' ),
												'id' => 'bk_highlight_background',
												'description' => __( 'Choose background color of highlight.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FF4629"
										),
										array(
												'name' => __( 'Highlight Text Color', 'corpora_theme' ),
												'id' => 'bk_highlight_text_color',
												'description' => __( 'Choose highlight text color.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Progressbar Background', 'corpora_theme' ),
												'id' => 'bk_progressbar_background',
												'description' => __( 'Choose background color of progressbar.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#F8F8F8"
										),
										array(
												'name' => __( 'Progressbar Color', 'corpora_theme' ),
												'id' => 'bk_progressbar_color',
												'description' => __( 'Choose fill color of progressbar.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FF4629"
										),
										array(
												'name' => __( 'Price Table Background', 'corpora_theme' ),
												'id' => 'bk_price_table_background',
												'description' => __( 'Choose pricing table background color.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#FFFFFF"
										),
										array(
												'name' => __( 'Price Table Border', 'corpora_theme' ),
												'id' => 'bk_price_table_border',
												'description' => __( 'Choose pricing table outer border color.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#808080"
										),
										array(
												'name' => __( 'Price Table Alternate Color', 'corpora_theme' ),
												'id' => 'bk_price_table_alternate_color',
												'description' => __( 'Choose price table alternate row background color.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#F8F8F8"
										),
										array(
												'name' => __( 'Toggle Header Color', 'corpora_theme' ),
												'id' => 'bk_toggle_header_color',
												'description' => __( 'Choose default color for closed/not active toggle, tabs and accordion headers.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#909090"
										),
										array(
												'name' => __( 'Toggle Header Hover Color', 'corpora_theme' ),
												'id' => 'bk_toggle_header_hover_color',
												'description' => __( 'Choose color for hover/active toggle, tabs and accordion headers.', 'corpora_theme' ),
												'type' => 'simple_colorpicker',
												'default' => "#404040"
										)
								)
						)
				)
		)
);

/*
 Print out settings interface
*/
function bk_theme_settings_interface() {
	global $bk_theme_settings;
	global $bk_default_theme_settings_restored;

	global $bk_theme_version;

	echo '<div class="wrap">';
	echo '<h2>Division</h2>';
	echo '<span class="bk-theme-subtitle">Version ' . $bk_theme_version . ' by <a href="http://themeforest.net/user/BillyKid">BillyKid</a></span>';

	echo '<div id="bk-notification-wrap">';

	if( $bk_default_theme_settings_restored )
		echo '<div id="bk-theme-notification" class="updated fade"><p>' . __( 'Theme settings restored to default.', 'corpora_theme' ) . '</p></div>';

	echo '</div>';
	echo '<div id="bk-settings-wrap">';


	echo '<div id="bk-settings-header-wrap">';
	echo '<span class="icon-options"></span><h2 class="settings-header">Theme Settings</h2>';
	echo '</div>';


	echo '<div id="bk-theme-settings-tabs">';
	echo '<form action="" id="bk-theme-settings-form">';
	echo '<input type="hidden" id="bk_theme_settings_nonce" name="bk_theme_settings_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

	$tab_num = 1;
	$navigation = array();									//whole navigation with big sections and subsections
	foreach ($bk_theme_settings as $big_section) {
		$navigation_secitons = array();				// navigation subsection where key is tab id and value is tab name
		foreach ($big_section['sections'] as $section) {
			$tab_id = 'bk-tab-' . $tab_num;
			$tab_name = $section['name'];
				
			$navigation_secitons[$tab_id] = $tab_name;
				
			echo '<div class="section" id="' . $tab_id . '">';
				
			echo '<table class="bk_form_wrap">';
				
			foreach ($section['options'] as $setting) {

				if( 'separator' != $setting['type'] && 'newsletter_list' != $setting['type'] && 'tb_browse_button' != $setting['type'] && 'reset_button' != $setting['type'])
					$value = get_option($setting['id'], $setting['default']);

				//bk_get_setting_field( $setting, $value );
				Bk_Field_Factory::getInstance()->print_setting_field($setting, $value);
			}

			echo '</table>';
				
			if(isset($section["type"]) && $section["type"] == "social_sortable_list" ) {
				echo social_sortable_list();
			} elseif( isset($section["type"]) && $section["type"] == "slides_sortable_list" ) {
				echo slides_sortable_list();
			}	elseif( isset($section["type"]) && $section["type"] == "sidebars_list" ) {
				echo sidebars_list();
			}
				
			echo '</div>';
				
			$tab_num++;
		}

		$navigation[] = array('name' => $big_section['name'],
				'icon' => $big_section['icon'],
				'sections' => $navigation_secitons );
	}

	echo '<div id="bk-theme-settings-navigation-wrap">';
	echo '<ul id="bk-theme-settings-navigation">';

	$current_theme_setting_tab_name = 'bk-tab-1';

	foreach( $navigation as $big_section ) {
		$current = '';

		if(array_key_exists($current_theme_setting_tab_name, $big_section['sections']))
			$current = 'current-tab';

		echo '<li class="bk-big-section-tab ' . $current . ' ' . $big_section['icon'] . '"><span class="bk-big-section-tab-title">' . $big_section['name'] . '</span><div class="bk-section-arrow"><div></div></div>';
		echo '<ul class="bk-section-submenu">';
		foreach( $big_section['sections'] as $tab_id => $tab_name ) {
			if( $tab_id == $current_theme_setting_tab_name ) {
				echo '<li class="bk-submenu-item current-section"><a href="#' . $tab_id . '">' . $tab_name . '</a></li>';
			} else {
				echo '<li class="bk-submenu-item"><a href="#' . $tab_id . '">' . $tab_name . '</a></li>';
			}
		}
		echo '</ul>';
		echo '</li>';
	}

	echo '</ul>';
	echo '<div id="bk-theme-settings-navigation-shadow"></div>';
	echo '</div>';

	echo '</form>';
	echo '</div>';
	echo '<div id="bk-settings-footer-wrap">';
	echo '<form id="bk-reset-settings-form" method="post" action"' . $_SERVER['PHP_SELF'] . '">';
	echo '<input type="submit" name="bk-reset-default-button" class="float-left button-secondary" value="' . __( 'Restore Defaults', 'corpora_theme' ) . '" />';
	echo '<input type="hidden" name="bk-reset-default-key" value="bk-restore-default-theme-settings" />';
	echo '</form>';
	echo '<div class="float-right"><a id="bk-save-theme-settings" class="float-left button-primary">'  . __( 'Save Changes', 'corpora_theme' ) . '</a><span class="ajax_info"></span></div>';
	echo '<div class="clearfix" />';
	echo '</div>';
	echo '</div>';


	echo '<div id="social_icons_wrap" title="' . __("Social Icons", "corpora_theme") . '">';
	echo '<div id="social_icons"><p>' . __("Click on any icon to add to collection , drag and drop to change order.", "corpora_theme") . '</p>';
	echo '<ul class="icon_list">';
	echo '<li title="aim" class="aim"></li>';
	echo '<li title="behance" class="behance"></li>';
	echo '<li title="blogger" class="blogger"></li>';
	echo '<li title="digg" class="digg"></li>';
	echo '<li title="dribble" class="dribble"></li>';
	echo '<li title="ember" class="ember"></li>';
	echo '<li title="evernote" class="evernote"></li>';
	echo '<li title="facebook" class="facebook"></li>';
	echo '<li title="flickr" class="flickr"></li>';
	echo '<li title="forrst" class="forrst"></li>';
	echo '<li title="github" class="github"></li>';
	echo '<li title="google" class="google"></li>';
	echo '<li title="last-fm" class="last-fm"></li>';
	echo '<li title="linkedin" class="linkedin"></li>';
	echo '<li title="paypal" class="paypal"></li>';
	echo '<li title="quora" class="quora"></li>';
	echo '<li title="rss" class="rss"></li>';
	echo '<li title="share-this" class="share-this"></li>';
	echo '<li title="skype" class="skype"></li>';
	echo '<li title="tumblr" class="tumblr"></li>';
	echo '<li title="twitter" class="twitter"></li>';
	echo '<li title="vimeo" class="vimeo"></li>';
	echo '<li title="yahoo" class="yahoo"></li>';
	echo '<li title="you-tube" class="you-tube"></li>';
	echo '<li title="pinterest" class="pinterest"></li>';
	echo '<li title="email" class="email"></li>';
	echo '</ul>';
	echo '</div>';
	echo '</div>';

	echo '<div class="hiddenn" style="display: none;">';
	
	echo '</div>';

	echo '</div>';
}

/*
 add created sidebars list
*/
function sidebars_list() {
	$sidebars = get_option("bk_sidebars_list");
	$content = '';
	$content .= '<div class="title">' . __( 'This section shows available sidebars. Click Add Sidebar to create new sidebar. Go to ', 'corpora_theme' ) . '<a target="_blank" href="' . admin_url( 'widgets.php' ) . '">' . __( 'widgets', 'corpora_theme' ) . '</a>' . __( ' section to add any widget to sidebar', 'corpora_theme' ) . '</div><br />';
	$content .= '<div id="bk_sidebars_list">';
	if( is_array( $sidebars ) ) {
		foreach( $sidebars as $sidebar ) {
			$content .= '<div class="bk-sortable-item"><div class="bk-sortable-item-inner-wrap">';
			$content .= '<input type="hidden" name="bk-sidebar-id" value="' . $sidebar["bk-sidebar-id"] . '"/>';
			$content .= '<table>';
			$content .= '<tr><td>Sidebar Name <span class="bk-field-description">This name will be shown in widgets admin page. Use descriptive names like "Blog Left Sidebar" or "Page Sidebar"</span></td>';
			$content .= '<td><input type="text" name="bk-sidebar-name" value="' . $sidebar["bk-sidebar-name"] . '"/></td></tr>';
			$content .= '</table>';
			$content .= '<span title="Remove Sidebar" class="remove"></span></div></div>';
		}
	}
	$content .= '</div>';
	return $content;
}


/*
 add social sortable list
*/
function slides_sortable_list() {
	$content = '';
	$terms = get_terms('slide_filter');
	$count = count($terms);
	if ($count > 0) {
		$content .= '<div class="title">' .__( 'This section shows different slides filled under particular categories. Drag and drop items to change order of slides. Click edit on slide to get more details.', 'corpora_theme' ) . '</div><br />';
		foreach ($terms  as $term) {
				
			$content .= '<div class="title"><strong>' . __( 'Slides filled under category: ', 'corpora_theme' ) .  $term->name . '</strong></div>';
			$content .= '<div class="slide_sortable_list">';

			$args = array(
					'posts_per_page' => -1,
					'post_type' => 'slide',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'taxonomy' => 'slide_filter',
					'term' => $term->slug
			);

			global $post;
			query_posts($args);
			if ( have_posts() ) : while ( have_posts() ) : the_post();

			$post_id = $post->ID;
			$image = '';

			if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) {
				$image = get_the_post_thumbnail($post_id, 'admin-thumb');
			}

			$item_markup = '<div class="bk-sortable-item"><div class="bk-sortable-item-inner-wrap"><div class="float-left bk-slide-featured-image">' . $image . '</div>';
			$item_markup .= '<div class="data-fields-wrap float-left"><input type="hidden" name="bk-slide-post-id" value="' . $post_id . '"/>';

			$item_markup .= '<p>' . get_the_title() . '</p><p>' . bk_get_custom_text(get_the_excerpt(), 20) . '</p><div><a href="' . get_edit_post_link( $post_id ) . '" target="_blank">' . __( '[ Settings ]', 'corpora_theme' ) . '</a></div>';

			$item_markup .= '</div>';
			$item_markup .= '<div class="clearfix"></div></div></div>';

			$content .= $item_markup;

			endwhile;
			endif;
			wp_reset_query();
				
			$content .= '</div>';
		}
	}

	return $content;
}

/*
 add social sortable list
*/
function social_sortable_list() {
	$list = '<div id="social_sortable_list">';

	$socials = get_option('bk_social_icons');

	if( is_array($socials) ) {
		foreach( $socials as $social_item ) {
				
			$icon_class = $social_item["bk-social-item-icon"];
			$icon_text = $social_item["bk-social-item-text"];
			$icon_link = $social_item["bk-social-item-link"];
			$icon_link_target = $social_item["bk-social-item-link-target"];
				
			$item_markup = '<div class="bk-sortable-item"><div class="bk-sortable-item-inner-wrap"><span class="float-left bk-icon-preview ' . $icon_class . '"></span>';
			$item_markup .= '<div class="data-fields-wrap float-left"><input type="hidden" name="bk-social-item-icon" value="' . $icon_class . '"/>';

			$item_markup .= '<table>';

			$item_markup .= '<tr><td>Text <span class="bk-field-description">This text will be shown in tooltip when user hovers over icon e.g follow us on twitter</span></td>';
			$item_markup .= '<td><input type="text" name="bk-social-item-text" value="'. stripslashes(htmlspecialchars(( $icon_text ), ENT_QUOTES)) .'"/></td></tr>';

			$item_markup .= '<tr><td>Link <span class="bk-field-description">Specify link for this social icon which can be for example Your facebook or twitter profile page</span></td>';
			$item_markup .= '<td><input type="text" name="bk-social-item-link" value="'. stripslashes($icon_link) .'"/></td></tr>';

			$item_markup .= '<tr><td>Target <span class="bk-field-description">Specify link target - this window or new tab in browser</span></td>';
			$item_markup .= '<td><select name="bk-social-item-link-target"><option ' . (( $icon_link_target == '_blank' ) ? 'selected="selected"' : '' ) . ' value="_blank">' . __( 'Open in new window/tab', 'corpora_theme' ) . '</option>';
			$item_markup .= '<option ' . (( $icon_link_target == '_self' ) ? 'selected="selected"' : '' ) . ' value="_self">' . __( 'Open in the same window', 'corpora_theme' ) . '</option></select></td></tr>';

			$item_markup .= '</table>';

			$item_markup .= '</div>';
			$item_markup .= '<div class="clearfix"></div><span title="Remove Item" class="remove"></span></div></div>';


			$list .= $item_markup;
		}
	}

	$list .= '</div>';
	return $list;
}

/*
 Update all theme settings ajax hook
*/
add_action( 'wp_ajax_bk_update_theme_settings_ajax', 'bk_update_theme_settings_ajax' );

function bk_update_theme_settings_ajax() {
	$form_data = $_POST['form'];
	global $bk_theme_settings;
		
	$form = array();
	$socials = array();
	$sidebars = array();
	$sidebar = array();

	$social_item = array();

	$portfolio_exclude_cats = array();

	$skip = 0;
	$menu_order = 0;  // used for slide order
		
	foreach( $form_data as $form_field ) {
		if( strpos($form_field['name'], 'bk-social-item-') !== false ) {
			$social_item[$form_field['name']] = $form_field['value'];
			$skip++;
			if( $skip == 4 ) {
				$socials[] = $social_item;
				$skip = 0;
			}
		} else if( strpos($form_field['name'], 'bk-sidebar-') !== false ) {
			$sidebar[$form_field['name']] = trim($form_field['value']);
			$skip++;
			if( $skip == 2 ) {
				$sidebar['bk-sidebar-name'] = str_replace(array(',','.','"',"'",'/',"\\",'+','=',')','(','*','&','^','%','$','#','@','!','~','`','<','>','?','[',']','{','}','|',':',),'',$sidebar['bk-sidebar-name']); // strip all wierd signes

				if( $sidebar['bk-sidebar-name'] == '' ) {
					$sidebar['bk-sidebar-name'] = 'Sidebar';
				}
				if( $sidebar['bk-sidebar-id'] == '' ) {
					$sidebar['bk-sidebar-id'] = get_unique_sidebar_id( $sidebars, $sidebar['bk-sidebar-name'] );
				}

				$sidebars[] = $sidebar;
				$skip = 0;
			}
		}  else {
			$form[$form_field['name']] = $form_field['value'];
		}
	}

	$form['bk_portfolio_nav_exclude_cats'] = implode(",", $portfolio_exclude_cats);

	$nonce = $form['bk_theme_settings_nonce'];

	
	if( wp_verify_nonce( $nonce, basename(__FILE__) ) && current_user_can( 'edit_theme_options' ) ) {
		update_option('bk_social_icons', $socials);
		update_option('bk_sidebars_list', $sidebars);
			
		if( is_array($form_data) ) {
			foreach ($bk_theme_settings as $big_section) {
				foreach ($big_section['sections'] as $section) {
					foreach ($section['options'] as $setting) {

						if( 'separator' != $setting['type'] && 'newsletter_list' != $setting['type'] && 'tb_browse_button' != $setting['type'] && 'reset_button' != $setting['type']) {

							$old_value = get_option($setting["id"]);

							if( $setting["type"] == "checkbox" || $setting["type"] == "checkbox_section" ) {
								if( isset( $form[$setting["id"]] ) && "on" == $form[$setting["id"]] ) {
									$new_value = true;
								}	else {
									$new_value = false;
								}
							} elseif( $setting["type"] == "rich_textarea" ) {
								$new_value = stripslashes($form[$setting["id"]]);
							} else {
								$new_value = $form[$setting["id"]];
							}

							if( $setting["type"] == "slider" ) {
								$new_value = intval($new_value);
							}

							if( isset($new_value) && $new_value != $old_value ) {
								update_option($setting["id"], $new_value);
							}

						}
					}
				}
			}
		}
			
		$message = '<div id="bk-theme-notification" class="updated fade"><p>' . __( 'Theme settings saved', 'corpora_theme' ) . '</p></div>';

	} else {
		$message = '<div id="bk-theme-notification" class="error"><p>' . __( 'Verification failed!', 'corpora_theme' ) . '</p></div>';
	}

	header( "Content-Type: text/html" );
	//$message .= var_dump($socials);

	echo $message;
	//echo var_dump( $_POST['bk_multiple_category_test'] );
	//echo var_dump($form_data);

	exit;
}


/*
 restore default settings
*/
function bk_admin_reset_theme_settings() {
	global $bk_theme_settings;
	foreach ($bk_theme_settings as $big_section) {
		foreach ($big_section['sections'] as $section) {
			foreach ($section['options'] as $setting) {
				if(isset($setting['id']) && isset($setting['default']) ) {
					update_option($setting['id'], $setting['default']);
				}
			}
		}
	}
}
?>