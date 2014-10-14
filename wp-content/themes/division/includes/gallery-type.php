<?php
/*
 Register gallery post type, create labels setup etc...
*/
function bk_register_gallery() {
    $labels = array(
			'name' => __('Gallery', 'corpora_theme'),
			'singular_name' => __('Gallery', 'corpora_theme'),
    		'all_items' => __('All Gallery Items', 'corpora_theme'),
			'add_new' => __('Add Gallery Item', 'corpora_theme'),
    		'add_new_item' => __('Add Gallery Item', 'corpora_theme'),
			'edit_item' => __('Edit Gallery Item', 'corpora_theme'),
			'new_item' => __('New Gallery Item', 'corpora_theme'),
			'view_item' => __('View Gallery Item', 'corpora_theme'),
			'search_items' => __('Search Gallery', 'corpora_theme'),
			'not_found' =>  __('No Gallery Items Found', 'corpora_theme'),
			'not_found_in_trash' => __('No Gallery Items Found In Trash', 'corpora_theme'),
			'parent_item_colon' => ''
		);
    
    
    $args = array(
			'labels' => $labels,
			'public' => true,
    		'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'gallery'
			),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'thumbnail', 'page-attributes' )
		); 

    register_post_type( 'gallery' , $args );
}

/*
 Custom portfolio messages
*/
function bk_gallery_messages($messages)
{
	global $post, $post_ID;
	
	$messages['gallery'] =
		array(
				0 => '',
				1 => __('Gallery Item Updated.', 'corpora_theme'),
				2 => __('Custom Field Updated.', 'corpora_theme'),
				3 => __('Custom Field Deleted.', 'corpora_theme'),
				4 => __('Gallery Item Updated.', 'corpora_theme'),
				5 => isset($_GET['revision']) ? sprintf( __('Gallery Item Restored To Revision From %s', 'corpora_theme'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
				6 => __('Gallery Item Published.', 'corpora_theme'),
				7 => __('Gallery Item Saved.', 'corpora_theme'),
				8 => __('Gallery Item Submitted.', 'corpora_theme'),
				9 => sprintf(__('Gallery Item Scheduled For: <strong>%1$s</strong>. ', 'corpora_theme'), date_i18n( __( 'M j, Y @ G:i', 'corpora_theme' ), strtotime($post->post_date))),
				10 => __('Gallery Item Draft Updated.', 'corpora_theme')
			);
	return $messages;
}

/*
 Custom gallery taxonomy
*/
function bk_register_gallery_taxonomy($messages)
{
	register_taxonomy(
		"gallery_category",
		"gallery",
		array(
			"hierarchical" => true,
			"show_in_nav_menus" => false,
			"show_tagcloud" => false,
			"label" => __( "Gallery Category", 'corpora_theme' ),
			"singular_label" => __( "Gallery Category", 'corpora_theme' ),
			"rewrite" => array(
				'slug' => 'galler-category',
				'hierarchical' => true
			)
		)
	);
	
	/*
	register_taxonomy(
		"gallery_tag",
		"gallery",
		array(
			"hierarchical" => false,
			"label" => __( "Tags", 'corpora_theme' ),
			"singular_label" => __( "Tags", 'corpora_theme' ),
			"rewrite" => array(
				'slug' => 'gallery-tag',
				'hierarchical' => true
			)
		)
	);
	*/
}	

/*
 Add custom meta box for images, external links to project and other options
*/
function bk_gallery_add_metabox( $post_type ) {
	if ( ! get_post_type_object( $post_type ) ) {
        // It's a comment or a link, or something else
        return;
	}
	
	add_meta_box( 
        'bk_gallery_custom_settings',
        __( 'Gallery Item Settings', 'corpora_theme' ),
        'bk_gallery_inner_custom_box',
        'gallery',
        'normal',
        'high' 
    );
}

/* Array with fields used for gallery item custom type */
$bk_gallery_item_settings = array(
	array(
     'description' => __( 'Thumbnail Settings', 'corpora_theme' ),
     'type' => 'separator',
    ),
	array(
		'name' => __( 'Gallery "METRO" Thumbnail Size', 'corpora_theme' ),
		'id' => '_bk_gallery_thumbnail_size',
		'description' => __( 'Choose size of thumbnail used for this gallery entry while displayed inside "METRO" style layout', 'corpora_theme' ),
		'type' => 'combobox',
		'default' => '1-1',
		'all_values' => array(	"1-1" => "Small ( 1x1 )", 
								"2-1" => "Landscape ( 2x1 )",
								"1-2" => "Portrait ( 1x2 )",
								"2-2" => "Large ( 2x2 )",			
							 )
	),
	array(
		'name' => __( 'Gallery "MIXED MASONRY" Thumbnail Size', 'corpora_theme' ),
		'id' => '_bk_gallery_mixed_masonry_thumbnail_size',
		'description' => __( 'Choose size of thumbnail used for this gallery entry while displayed inside "MIXED MASONRY" style layout', 'corpora_theme' ),
		'type' => 'combobox',
		'default' => 'small',
		'all_values' => array(	"small" => "Small",
								"large" => "Large",
		)
	),
	array(
		'name' => __( 'Gallery Item Thumbnail Behavior', 'corpora_theme' ),
		'id' => '_bk_gallery_thumbnail_behavior',
		'description' => __( 'Choose how thumbnail in gallery will behave when it\'s clicked by the user. You can have image, video, gallery popups and external links', 'corpora_theme' ),
		'type' => 'stacked_combobox',
		'default' => 'featured_image_popup',
		'all_values' => array(	"none" => array( "label" => "None", "buddy" => "" ),
								"featured_image_popup" => array( "label" => "Featured Image Popup", "buddy" => "" ),
								"image_popup" => array( "label" => "Image Popup", "buddy" => "#buddy_wrap_bk_gallery_thumbnail_image_link" ),
								"vimeo_popup" => array( "label" => "Vimeo Popup", "buddy" => "#buddy_wrap_bk_gallery_thumbnail_vimeo_link, #buddy_wrap_bk_popup_video_height, #buddy_wrap_bk_popup_video_width" ),
								"youtube_popup" => array( "label" => "Youtube Popup", "buddy" => "#buddy_wrap_bk_gallery_thumbnail_youtube_link, #buddy_wrap_bk_popup_video_height, #buddy_wrap_bk_popup_video_width" ),
								"gallery_popup" => array( "label" => "Gallery Popup", "buddy" => "#buddy_wrap_bk_gallery_popup_gallery" ),
								"external_link" => array( "label" => "External Link", "buddy" => "#buddy_wrap_bk_gallery_thumbnail_external_link" )												
							)
	),
	array(
		'name' => __( 'Image Link', 'corpora_theme' ),
		'id' => '_bk_gallery_thumbnail_image_link',
		'description' => __( 'Image Link', 'corpora_theme' ),
		'type' => 'browse_button_with_label',
		'default' => ''
		),
	array(
		'name' => __( 'Vimeo Link', 'corpora_theme' ),
		'id' => '_bk_gallery_thumbnail_vimeo_link',
		'description' => __( 'Vimeo Link', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Youtube Link', 'corpora_theme' ),
		'id' => '_bk_gallery_thumbnail_youtube_link',
		'description' => __( 'Youtube Link', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Video Width', 'corpora_theme' ),
		'id' => '_bk_popup_video_width',
		'description' => __( 'Choose width for popup video. This setting will work along with below to calculate aspect ratio so videos on smaller screens can be resized.', 'corpora_theme' ),
		'type' => 'slider',
		'slider_class_type' => 'pixel',
		'default' => 960,
		'min' => 1,
		'max' => 1920,
		'step' => 1
	),
	array(
		'name' => __( 'Video Height', 'corpora_theme' ),
		'id' => '_bk_popup_video_height',
		'description' => __( 'Choose height for popup video. This setting will work along with above to calculate aspect ratio so videos on smaller screens can be resized.', 'corpora_theme' ),
		'type' => 'slider',
		'slider_class_type' => 'pixel',
		'default' => 540,
		'min' => 1,
		'max' => 1080,
		'step' => 1
	),
	array(
		'name' => __( 'Porfolio Gallery', 'corpora_theme' ),
		'id' => '_bk_gallery_popup_gallery',
		'description' => __( 'Use button on the right to add images and vidoes to popup gallery. Drag and drop to change order of images and videos.', 'corpora_theme' ),
		'type' => 'gallery',
		'sub_type' => 'popup_gallery',	
		'default' => 0,
		'images' => array()
	),
	array(
		'name' => __( 'External Link', 'corpora_theme' ),
		'id' => '_bk_gallery_thumbnail_external_link',
		'description' => __( 'Specify external link for this project.', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	)
);

/* Prints the box content */
function bk_gallery_inner_custom_box( $post ) {
  global $bk_gallery_item_settings;
  
  echo '<div class="bk_custom_settings_wrap">';
  
  // Use nonce for verification
  echo '<input type="hidden" name="bk_portfolio_meta_box_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
  
  Bk_Popup_Gallery_Manager::getInstance()->print_gallery_manager_editors();
  
  echo '<table id="bk_portfolio_item_settings" class="bk_form_wrap">';
  
  foreach ($bk_gallery_item_settings as $setting) {
    $value = '';

    if( $setting['type'] == 'gallery' ) {
	  if( $setting['sub_type'] == 'popup_gallery' ) {
		Bk_Popup_Gallery_Manager::getInstance()->restore_gallery_state($post->ID, $setting['id'], $setting['name'], $setting['description'], $setting['sub_type']);
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

/* When the post is saved, saves our custom data */
function bk_save_gallery_custom_metadata( $post_id ) {
  global $bk_gallery_item_settings;
  
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !isset($_POST['bk_portfolio_meta_box_nounce']) || !wp_verify_nonce( $_POST['bk_portfolio_meta_box_nounce'], basename(__FILE__) ) )
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

  foreach ($bk_gallery_item_settings as $setting) {
		if( 'separator' == $setting['type'] )
			continue;
		
		$old_value = get_post_meta($post_id, $setting['id'], true);
		$new_value = false;
    	if( isset($_POST[$setting['id']]) ) {
    		$new_value = $_POST[$setting['id']];
    	}	
		
    	if( $setting['type'] == 'gallery' ) {
			if( $setting['sub_type'] == 'popup_gallery' ) {
				Bk_Popup_Gallery_Manager::getInstance()->save_gallery_state($post_id, $setting['id']);
			} 
		}	
 
	    update_post_meta($post_id, $setting['id'], $new_value);
	}
}


/*
 Add actions
*/
add_action( 'init', 'bk_register_gallery' );
add_action( 'init', 'bk_register_gallery_taxonomy', 0 );  
add_filter( 'post_updated_messages', 'bk_gallery_messages' );
add_action( 'add_meta_boxes', 'bk_gallery_add_metabox' );
add_action( 'save_post', 'bk_save_gallery_custom_metadata' );
?>
