<?php
/*
 Register quick_gallery, create labels setup etc...
*/
function bk_register_quick_gallery() {
    $labels = array(
			'name' => __('Quick Gallery', 'corpora_theme'),
			'singular_name' => __('Quick Gallery', 'corpora_theme'),
    		'all_items' => __('All Quick Galleries', 'corpora_theme'),
			'add_new' => __('Add Quick Gallery', 'corpora_theme'),
    		'add_new_item' => __('Add Quick Gallery', 'corpora_theme'),
			'edit_item' => __('Edit Quick Gallery', 'corpora_theme'),
			'new_item' => __('New Quick Gallery', 'corpora_theme'),
			'view_item' => __('View Quick Gallery', 'corpora_theme'),
			'search_items' => __('Search Quick Gallery', 'corpora_theme'),
			'not_found' =>  __('No Quick Galleries Found', 'corpora_theme'),
			'not_found_in_trash' => __('No Quick Galleries Found In Trash', 'corpora_theme'),
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
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title' )
		); 

    register_post_type( 'quick_gallery' , $args );
}

/*
 Custom portfolio messages
*/
function bk_quick_gallery_messages($messages)
{
	global $post, $post_ID;
	
	$messages['quick_gallery'] =
		array(
			0 => '',
			1 => __('Quick Gallery Updated.', 'corpora_theme'),
			2 => __('Custom Field Updated.', 'corpora_theme'),
			3 => __('Custom Field Deleted.', 'corpora_theme'),
			4 => __('Quick Gallery Updated.', 'corpora_theme'),
			5 => isset($_GET['revision']) ? sprintf( __('Quick Gallery Restored To Revision From %s', 'corpora_theme'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
			6 => __('Quick Gallery Published.', 'corpora_theme'),
			7 => __('Quick Gallery Saved.', 'corpora_theme'),
			8 => __('Quick Gallery Submitted.', 'corpora_theme'),
			9 => sprintf(__('Quick Gallery Scheduled For: <strong>%1$s</strong>. ', 'corpora_theme'), date_i18n( __( 'M j, Y @ G:i', 'corpora_theme' ), strtotime($post->post_date))),
			10 => __('Quick Gallery Draft Updated.', 'corpora_theme')
		);
	return $messages;
}

/* Array with fields used for quick gallery item custom type */
$bk_quick_gallery_item_settings = array(
	array(
		'name' => __( 'Quick Gallery', 'corpora_theme' ),
		'id' => '_bk_quick_gallery',
		'description' => __( 'Use button on the right to add images or videos to quick gallery. Drag and drop to change order of images, clik edit to change appearance of gallery items. Once saved quick gallery can be inserted to any page, post or portfolio via page builder.', 'corpora_theme' ),
		'type' => 'gallery',
		'sub_type' => 'featured_gallery',	
		'default' => 0,
		'images' => array()
	)
);

/*
 Add custom meta box for images, external links to project and other options
*/
function bk_quick_gallery_add_metabox( $post_type ) {
	if ( ! get_post_type_object( $post_type ) ) {
		// It's a comment or a link, or something else
		return;
	}

	add_meta_box(
		'bk_quick_gallery_custom_settings',
		__( 'Quick Gallery Settings', 'corpora_theme' ),
		'bk_quick_gallery_inner_custom_box',
		'quick_gallery',
		'normal',
		'high'
	);
}


/* Prints the box content */
function bk_quick_gallery_inner_custom_box( $post ) {
  global $bk_quick_gallery_item_settings;
  
  echo '<div class="bk_custom_settings_wrap">';
  echo '<p>' . __( 'Use setting below to create quick gallery. Quick gallery can be inserted into any post, page or portfolio item via page builder.', 'corpora_theme' ) . '</p>';
  
  // Use nonce for verification
  echo '<input type="hidden" name="bk_portfolio_meta_box_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
  
  Bk_Featured_Gallery_Manager::getInstance()->print_gallery_manager_editors();
  
  echo '<table id="bk_portfolio_item_settings" class="bk_form_wrap">';
  
  foreach ($bk_quick_gallery_item_settings as $setting) {
    $value = '';

    if( $setting['type'] == 'gallery' ) {
	  if ( $setting['sub_type'] == 'featured_gallery' ) {
        Bk_Featured_Gallery_Manager::getInstance()->restore_gallery_state($post->ID, $setting['id'], $setting['name'], $setting['description'], $setting['sub_type']);
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
function bk_save_quick_gallery_custom_metadata( $post_id ) {
  global $bk_quick_gallery_item_settings;
  
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

  foreach ($bk_quick_gallery_item_settings as $setting) {
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
			} elseif ( $setting['sub_type'] == 'featured_gallery' ) {
                Bk_Featured_Gallery_Manager::getInstance()->save_gallery_state($post_id, $setting['id']);
            }
		}	
 
	  update_post_meta($post_id, $setting['id'], $new_value);
	}
}

/*
 Add actions
*/
add_action( 'init', 'bk_register_quick_gallery' ); 
add_filter( 'post_updated_messages', 'bk_quick_gallery_messages' );
add_action( 'add_meta_boxes', 'bk_quick_gallery_add_metabox' );
add_action( 'save_post', 'bk_save_quick_gallery_custom_metadata' );
?>
