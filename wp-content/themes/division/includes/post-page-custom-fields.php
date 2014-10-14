<?php
/*
 Add custom meta box for images, external links to project and other options
*/
function bk_post_page_add_metabox() {
	add_meta_box( 
        'bk_post_page_custom_settings',
        __( 'Post Appearance', 'corpora_theme' ),
        'bk_post_page_inner_custom_box',
        'post',
        'normal',
        'high' 
    );   
    
  add_meta_box( 
        'bk_post_page_custom_settings',
        __( 'Page Appearance', 'corpora_theme' ),
        'bk_post_page_inner_custom_box',
        'page',
        'normal',
        'high' 
    );
    
  add_meta_box( 
        'bk_post_page_custom_settings',
        __( 'Portfolio Appearance', 'corpora_theme' ),
        'bk_post_page_inner_custom_box',
        'portfolio',
        'normal',
        'high' 
    );
  
  if( bk_is_woocommerce_activated() ) {
  	
  	add_meta_box(
	  	'bk_post_page_custom_settings',
	  	__( 'Product Appearance', 'corpora_theme' ),
	  	'bk_post_page_inner_custom_box',
	  	'product',
	  	'normal',
	  	'high'
  	);
  	
  }
          
}

/* Array with fields used for portfolio item custom type */
$bk_post_page_item_settings = array(
		array(
				'name' => __( 'Override Global Header Settings', 'corpora_theme' ),
				'id' => '_bk_override_header_settings',
				'description' => __( 'If enabled global header settings will be changed for this page/post/portfolio.', 'corpora_theme' ),
				'type' => 'checkbox_section',
				'rows' => 2,
				'default' => false
		),
		array(
				'name' => __( 'Collapsible Header', 'corpora_theme' ),
				'id' => '_bk_collapsible_header',
				'description' => __( 'If enabled header will display triangle shaped button at the top which will allow user to collapse header in order to gain more space for content. If fullscreen background slideshow is applied to page/post/portfolio then this button will also toggle slideshow navigation.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Header Collapsed Onload', 'corpora_theme' ),
				'id' => '_bk_header_collapsed_onload',
				'description' => __( 'If enabled header will be collapsed on page load.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Disable Page Title', 'corpora_theme' ),
				'id' => '_bk_page_title_disabled',
				'description' => __( 'If enabled page title will be hidden.', 'corpora_theme' ),
				'type' => 'checkbox',
				'valid_for' => 'page',
				'default' => false
		),
		array(
				'name' => __( 'Page Layout', 'corpora_theme' ),
				'id' => '_bk_page_layout',
				'description' => __( 'Choose layout for page/post/portfolio. Use Fullwidth for full page layout, fixed to show some content and make rest of background visible or no content option to show only background e.g fullscreen background slider.', 'corpora_theme' ),
				'type' => 'stacked_image_radiobuttons',
				'default' => 'fullwidth',
				'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "#buddy_wrap_bk_fullwidht_page_padding"  ),
									  "fixed_centered" => array( "label" => "Fixed Centered", "image" => "fixed-centered.png", "buddy" => "#buddy_wrap_bk_fixed_page_width" ),
									  "fixed_float_right" => array( "label" => "Fixed Float Right", "image" => "fixed-float-right.png", "buddy" => "#buddy_wrap_bk_fixed_page_width" ),
									  "fixed_float_left" => array( "label" => "Fixed Float Left", "image" => "fixed-float-left.png", "buddy" => "#buddy_wrap_bk_fixed_page_width" ),
									  "no_content" => array( "label" => "No Content", "image" => "no-content.png", "buddy" => "" )
				)
		),
		array(
				'name' => __( 'Fullwidth Page Padding', 'corpora_theme' ),
				'id' => '_bk_fullwidht_page_padding',
				'description' => __( 'Specify padding for fullwidth page content. Usefull when inserting galleries and portolios with gutter to match gutter between items.', 'corpora_theme' ),
				'type' => 'slider',
				'valid_for' => 'page, portfolio',
				'slider_class_type' => 'pixel',
				'default' => 0,
				'min' => 0,
				'max' => 100,
				'step' => 1
		),
		array(
				'name' => __( 'Fixed Page Width', 'corpora_theme' ),
				'id' => '_bk_fixed_page_width',
				'description' => __( 'Specify maximum width for fixed page. If device screen size will be smaller than this width page will be resized to use 100% of available space.', 'corpora_theme' ),
				'type' => 'slider',
				'slider_class_type' => 'pixel',
				'default' => 1024,
				'min' => 0,
				'max' => 1920,
				'step' => 1
		),
		array(
				'name' => __( 'Primary Sidebar', 'corpora_theme' ),
				'id' => '_bk_page_sidebar_id',
				'description' => __( 'Choose sidebar for this post/page/portfolio. It will be displayed on the left side below menu.', 'corpora_theme' ),
				'type' => 'sidebar_combobox',
				'default' => -1
		),
		
		array(
				'name' => __( 'Entry Layout', 'corpora_theme' ),
				'id' => '_bk_single_entry_layout',
				'description' => __( 'Choose layout for this single page or post entry. Choose if and where sidebar or sidebars will be displayed.', 'corpora_theme' ),
				'type' => 'stacked_image_radiobuttons',
				'default' => 'fullwidth',
				'valid_for' => 'post, page',
				'all_values' => array("fullwidth" => array( "label" => "Fullwidth", "image" => "fullwidth.png", "buddy" => "" ),
									  "left_sidebar" => array( "label" => "Left Sidebar", "image" => "left-description.png", "buddy" => "#buddy_wrap_bk_page_sidebar_secondary_id"),
						              "right_sidebar" => array( "label" => "Right Sidebar", "image" => "right-description.png", "buddy" => "#buddy_wrap_bk_page_sidebar_secondary_id" )
				)
		),
		array(
				'name' => __( 'Secondary Sidebar', 'corpora_theme' ),
				'id' => '_bk_page_sidebar_secondary_id',
				'description' => __( 'Choose secondary sidebar for this post/page/portfolio. this setting is valid only for both sidebars layout.', 'corpora_theme' ),
				'valid_for' => 'post, page',
				'type' => 'sidebar_combobox',
				'default' => -1
		),
		array(
				'name' => __( 'Fullscreen Background Content', 'corpora_theme' ),
				'id' => '_bk_page_fullscreen_background_content',
				'description' => __( 'Choose fullscreen background for this post/page/portfolio. It can be slideshow, video, google maps or none.', 'corpora_theme' ),
				'type' => 'stacked_combobox',
				'default' => 'image_popup',
				'all_values' => array(	"bk-none" => array( "label" => "None", "buddy" => "" ),
										"bk-fullscreen-slideshow-enabled" => array( "label" => "Fullscreen Slideshow", "buddy" => "#buddy_wrap_bk_fullscreen_background_gallery, #buddy_wrap_bk_fullscreen_gallery_transition_interval, #buddy_wrap_bk_fullscreen_gallery_transition_speed , #buddy_wrap_bk_fullscreen_gallery_transition , #buddy_wrap_bk_fullscreen_gallery_new_window , #buddy_wrap_bk_fullscreen_gallery_overlay , #buddy_wrap_bk_fullscreen_gallery_fit_allways , #buddy_wrap_bk_fullscreen_gallery_autoplay , #buddy_wrap_bk_fullscreen_gallery_pause_on_hover" ),
										"bk-fullscreen-google-maps-enabled" => array( "label" => "Google Maps", "buddy" => "#buddy_wrap_bk_fullscreen_google_map_location, #buddy_wrap_bk_fullscreen_google_map_marker, #buddy_wrap_bk_fullscreen_google_map_marker_title" )
										
				)
		),
		array(
				'name' => __( 'Marker', 'corpora_theme' ),
				'id' => '_bk_fullscreen_google_map_marker',
				'description' => __( 'Choose custom marker icon that will be used to show Your location. Leave empty to use custom google map pin.', 'corpora_theme' ),
				'type' => 'browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Marker Title', 'corpora_theme' ),
				'id' => '_bk_fullscreen_google_map_marker_title',
				'description' => __( 'Specify title for marker - it will be visible once user hovers over it.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Location', 'corpora_theme' ),
				'id' => '_bk_fullscreen_google_map_location',
				'description' => __( 'Specify location on google maps in following format (Latitude, Longitude) e.g 40.758893,-73.985131. To get coordinates of any point on google maps choose Your desired location and then click with right mouse button and choose option "what is here?", then copy values from textfield.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Overlay', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_overlay',
				'description' => __( 'Choose optional semi-transparent overlay that will be applied to fullscreen gallery images. It will be visible only if header will be expanded. If header will be collapsed overlay will fade out so controls and thumbs can be visible.', 'corpora_theme' ),
				'type' => 'simple_browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Fit Images', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_fit_allways',
				'description' => __( 'If enabled gallery images will always fit inside browser window ( height and width will never exceeds size of browser windows ), otherwise images will be streched to cover all browser window.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Autoplay', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_autoplay',
				'description' => __( 'If enabled gallery will be in autoplay mode by default.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Pause On Hover', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_pause_on_hover',
				'description' => __( 'If enabled gallery will be paused when user hovers over image even if autoplay mode is on.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => true
		),
		array(
				'name' => __( 'New Window', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_new_window',
				'description' => __( 'If enabled links on slides will open in new window otherwise in the same window.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
			'name' => __( 'Transition', 'corpora_theme' ),
			'id' => '_bk_fullscreen_gallery_transition',
			'description' => __( 'Choose transition between slides.', 'corpora_theme' ),
			'type' => 'combobox',
			'default' => '1',
			'all_values' => array(	"0" => "None", 
									"1" => "Fade",
									"2" => "Slide Top",
									"3" => "Slide Right",
									"4" => "Slide Bottom",
									"5" => "Slide Left",
									"6" => "Carousel Right",
									"7" => "Carousel Left"
								 )
		),
		array(
				'name' => __( 'Transition Speed', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_transition_speed',
				'description' => __( 'Specify speed of transition between slides.', 'corpora_theme' ),
				'type' => 'slider',
				'slider_class_type' => 'ms',
				'default' => 1000,
				'min' => 0,
				'max' => 5000,
				'step' => 100
		),
		array(
				'name' => __( 'Transition Interval', 'corpora_theme' ),
				'id' => '_bk_fullscreen_gallery_transition_interval',
				'description' => __( 'Specify interval between each slide when autoplay mode is on.', 'corpora_theme' ),
				'type' => 'slider',
				'slider_class_type' => 'ms',
				'default' => 3000,
				'min' => 1000,
				'max' => 20000,
				'step' => 100
		),
		array(
				'name' => __( 'Fullscreen Gallery', 'corpora_theme' ),
				'id' => '_bk_fullscreen_background_gallery',
				'description' => __( 'Use button on the right to add images and vidoes to portfolio gallery. Drag and drop to change order of images and videos.', 'corpora_theme' ),
				'type' => 'gallery',
				'sub_type' => 'fullscreen_gallery',
				'default' => 0,
				'images' => array()
		),
		array(
				'name' => __( 'Override Page Background', 'corpora_theme' ),
				'id' => '_bk_override_page_default_background',
				'description' => __( 'If enabled default global page background will be ovveriden by settings below.', 'corpora_theme' ),
				'type' => 'checkbox_section',
				'rows' => 6,
				'default' => false
		),
		array(
				'name' => __( 'Background Color', 'corpora_theme' ),
				'id' => '_bk_page_background_color',
				'description' => __( 'Choose background color of whole page.', 'corpora_theme' ),
				'type' => 'simple_colorpicker',
				'default' => "#EEEEEE"
		),
		array(
				'name' => __( 'Background Image', 'corpora_theme' ),
				'id' => '_bk_page_background_image',
				'description' => __( 'This image will be used as page background.', 'corpora_theme' ),
				'type' => 'browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Retina Background Image', 'corpora_theme' ),
				'id' => '_bk_page_retina_background_image',
				'description' => __( 'This image will be used as body background for devices with retina (HiDpi) displays. It should be Four times bigger than regular background ( two times wider and two times higher ) e.g if regular background is 200px X 200px this one should be 400px X 400px. Levae blank to use default background image.', 'corpora_theme' ),
				'type' => 'browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Background Image Position', 'corpora_theme' ),
				'id' => '_bk_page_background_image_position',
				'description' => __( 'Choose page background image position.', 'corpora_theme' ),
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
				'id' => '_bk_page_background_image_repeat',
				'description' => __( 'Choose how page background image is repeated, horizontally, vertically, both or none.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'repeat',
				'all_values' => array("repeat" => "Cover All Area",
						"repeat-x" => "Repat Horizontally",
						"repeat-y" => "Repeat Vertically",
						"no-repeat" => "No Repeat")
		),
		array(
				'name' => __( 'Background Attachment', 'corpora_theme' ),
				'id' => '_bk_page_background_image_attachment',
				'description' => __( 'Choose how background image will be attached. Should it scroll with the whole page or stay at fixed position.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'fixed',
				'all_values' => array("scroll" => "Scroll with whole page",
									  "fixed" => "Stay at fixed position")
		)
);

/* Prints the box content */
function bk_post_page_inner_custom_box( $post ) {
  global $bk_post_page_item_settings;
  
  // Use nonce for verification
  echo '<input type="hidden" name="bk_meta_post_page_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
  
  if( $post->post_type != 'product' ) {
  	  bk_get_page_builder( $post );
  }
  
  Bk_Popup_Gallery_Manager::getInstance()->print_gallery_manager_editors();
  Bk_Featured_Gallery_Manager::getInstance()->print_gallery_manager_editors();
  Bk_Fullscreen_Gallery_Manager::getInstance()->print_gallery_manager_editors();
  Bk_Post_Featured_Gallery_Manager::getInstance()->print_gallery_manager_editors();
   
  echo '<div class="bk_custom_settings_wrap">';
  echo '<p>' . __( 'This section can be used to set per post/page/portfolio appearance settings such as layout, sidebars, featured caption and slider.', 'corpora_theme' ) . '</p>';
  
  echo '<table id="bk_post_page_settings" class="bk_form_wrap">';
  
  foreach ($bk_post_page_item_settings as $setting) { 
    $value = '';
    
    if( isset($setting['valid_for']) && strpos( $setting['valid_for'], get_post_type($post->ID) ) === false ) {
    	continue;
    }
    
    if( $setting['type'] == 'gallery' ) {
    	if( $setting['sub_type'] == 'fullscreen_gallery' ) {
    		Bk_Fullscreen_Gallery_Manager::getInstance()->restore_gallery_state($post->ID, $setting['id'], $setting['name'], $setting['description'], $setting['sub_type']);
    	}	
    } else {
    	if( isset($setting['id']) ) {
    		$value = get_post_meta($post->ID, $setting['id'], true);
    	}
    
    	Bk_Field_Factory::getInstance()->print_setting_field( $setting, $value );
    } 
  }
  
  echo '</table>';
  echo '</div>';
}

/*
  Get page builder
*/
function bk_get_page_builder( $post ) {
	echo '<div class="bk_page_builder_wrap">';
	echo '<p>' . __( 'This is page builder, if enabled standard content from default wordpress editor will be replaced.', 'corpora_theme' ) . '</p>';
	
	$value = get_post_meta($post->ID, '_bk_page_builder_enabled', true);
	$default = false;
	$page_builder_class = '';
	
	if( isset($value) ) {
			$checked = $value ? 'checked="checked"' : '';
			$page_builder_class = $value ? '' : 'hidden'; 
	} else {
			$checked = $default ? 'checked="checked"' : '';
			$page_builder_class = $value ? '' : 'hidden'; 	
	}		 
					
  echo '<div class="setting_description"><strong>' . __( 'Page Builder Enabled', 'corpora_theme' ) . '</strong><span class="bk-field-description">' . __( 'If enabled default page content will be repalced with settings below.', 'corpora_theme' )  . '</span></div>';
	echo '<div class="setting_control"><input type="checkbox" id="_bk_page_builder_enabled" name="_bk_page_builder_enabled" ' . $checked . ' class="fancy_checkbox"/><label class="bk-toggle-checkbox" for="_bk_page_builder_enabled"></label></div>';
  echo '<div class="clearfix"></div>';
  
  echo '<div id="bk_page_builder_inner_wrap">';
  
  $all_items = Bk_Page_Builder::getInstance()->get_all_pagebuilder_items();
  
  $selected_item = get_post_meta($post->ID, '_bk_page_builder_items_combobox', true);
 	
  $all_sizes = Bk_Page_Builder::getInstance()->get_all_pagebuilder_sizes();
  $selected_size = get_post_meta($post->ID, '_bk_page_builder_item_size_combobox', true);
  
  echo '<div class="setting_description">';
  echo '<select class="" id="_bk_page_builder_item_size_combobox" name="_bk_page_builder_item_size_combobox">';
		foreach ($all_sizes as $key => $label)	{
			if( $key == $selected_size )
				echo '<option value="' . $key . '" selected="selected">' . $label . '</option>';
			else
			  echo '<option value="' . $key . '">' . $label . '</option>';	
		}
	echo "</select>";
  
  echo '<select class="" id="_bk_page_builder_items_combobox" name="_bk_page_builder_items_combobox">';
		foreach ($all_items as $key => $label)	{
			if( $key == $selected_item )
				echo '<option value="' . $key . '" selected="selected">' . $label . '</option>';
			else
			  echo '<option value="' . $key . '">' . $label . '</option>';	
		}
	echo "</select>";
	echo '<label for="#_bk_page_builder_items_combobox"><span class="bk-field-description">' . __( 'Choose element that You want to add and click button below.', 'corpora_theme' )  . '</span></label></div>';
	echo '<div class="clearfix"></div>';
	
	echo '<div class="bk-big-button-wrap"><div data-loading-text="' . __( 'Loading', 'corpora_theme' ) . '" id="bk_page_builder_add_item_button"><span class="bk_button_text">' .  __( 'Add Item', 'corpora_theme' ) . '<span class="bk_loading_icon"></span></span></div></div>';
	
	$page_builder_items_count = get_post_meta($post->ID, '_bk_page_builder_items_count', true);
	if( empty($page_builder_items_count) )
		$page_builder_items_count = 0;
	
	echo '<input type="hidden" id="_bk_page_builder_items_count" name="_bk_page_builder_items_count" value="' . $page_builder_items_count . '" >';
  echo '<div id="bk_page_builder_items_wrap">';
  
  echo Bk_Page_Builder::getInstance()->load_pagebuilder_items( $post->ID );
  
  echo '</div>';
  
  echo '<div class="clearfix"></div>'; 
  echo '</div>';
  echo '</div>';
	
  Bk_Page_Builder::getInstance()->print_pagebuilder_editors();
  Bk_Shortcode_Manager::getInstance()->print_shortcode_manager_editors();

  //bk_print_shortcode_editors();
}

/*-----------------------------------------------------------------------
  prints out page builder items - dialogs used to edit page builder items
-----------------------------------------------------------------------*/

/* When the post is saved, saves our custom data */
function bk_save_post_page_custom_metadata( $post_id ) {
  global $bk_post_page_item_settings;
  
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !isset($_POST['bk_meta_post_page_nounce']) || !wp_verify_nonce( $_POST['bk_meta_post_page_nounce'], basename(__FILE__) ) )
      return $post_id;

  
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return $post_id;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  }

  // OK, we're authenticated: we need to find and save the data

  Bk_Page_Builder::getInstance()->save_page_builder_state($post_id);
  
  foreach ($bk_post_page_item_settings as $setting) {
		
  		if( 'separator' == $setting['type'] )
  			continue;
  	
  	    if( isset($setting['valid_for']) && strpos( $setting['valid_for'], get_post_type($post_id) ) === false ) {
		    continue;
		}
		
		$old_value = get_post_meta($post_id, $setting['id'], true);
	    $new_value = false;
	    if( isset($_POST[$setting['id']]) ) {
	    	$new_value = $_POST[$setting['id']];
	    }

	    if( $setting['type'] == 'gallery' ) {
	    	if( $setting['sub_type'] == 'fullscreen_gallery' ) {
	    		Bk_Fullscreen_Gallery_Manager::getInstance()->save_gallery_state($post_id, $setting['id']);
	    	}
	    }	
	    
		update_post_meta($post_id, $setting['id'], $new_value);
	}
}

/*
 Add actions
*/

add_action( 'add_meta_boxes', 'bk_post_page_add_metabox' );
add_action( 'save_post', 'bk_save_post_page_custom_metadata' );

/*------------------------------------------------------------------------------------------
 Helper function converts size to readable string
------------------------------------------------------------------------------------------*/
function bk_size_to_string( $item_size ) {
	$sizes = array();
	$sizes['one_one'] = '1 / 1';
	$sizes['one_half'] = '1 / 2';
	$sizes['one_third'] = '1 / 3';
	$sizes['two_third'] = '2 / 3';
	$sizes['three_fourth'] = '3 / 4';
	$sizes['one_fourth'] = '1 / 4';
	$sizes['one_fifth'] = '1 / 5';
	$sizes['two_fifth'] = '2 / 5';
	$sizes['three_fifth'] = '3 / 5';
	$sizes['four_fifth'] = '4 / 5';
	$sizes['one_sixth'] = '1 / 6';
	$sizes['five_sixth'] = '5 / 6';
	return $sizes[$item_size];
}
?>