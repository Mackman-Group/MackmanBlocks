<?php
/*
 Register testimonial, create labels setup etc...
*/
function bk_register_testimonial() {
    $labels = array(
			'name' => __('Testimonial', 'corpora_theme'),
			'singular_name' => __('Testimonial', 'corpora_theme'),
    		'all_items' => __('All Testimonials', 'corpora_theme'),
			'add_new' => __('Add Testimonial', 'corpora_theme'),
    		'add_new_item' => __('Add Testimonial', 'corpora_theme'),
			'edit_item' => __('Edit Testimonial', 'corpora_theme'),
			'new_item' => __('New Testimonial', 'corpora_theme'),
			'view_item' => __('View Testimonial', 'corpora_theme'),
			'search_items' => __('Search Testimonial', 'corpora_theme'),
			'not_found' =>  __('No Testimonial Items Found', 'corpora_theme'),
			'not_found_in_trash' => __('No Testimonial Items Found In Trash', 'corpora_theme'),
			'parent_item_colon' => ''
		);
    
    
    $args = array(
			'labels' => $labels,
			'public' => true,
    		'show_in_nav_menus' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail' )
		); 

    register_post_type( 'testimonial' , $args );
}

/*
 Custom testimonial messages
*/
function bk_testimonial_messages($messages)
{
	global $post, $post_ID;
	
	$messages['testimonial'] =
		array(
				0 => '',
				1 => __('Testimonial Updated.', 'corpora_theme'),
				2 => __('Custom Field Updated.', 'corpora_theme'),
				3 => __('Custom Field Deleted.', 'corpora_theme'),
				4 => __('Testimonial Updated.', 'corpora_theme'),
				5 => isset($_GET['revision']) ? sprintf( __('Testimonial Restored To Revision From %s', 'corpora_theme'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
				6 => __('Testimonial Published.', 'corpora_theme'),
				7 => __('Testimonial Saved.', 'corpora_theme'),
				8 => __('Testimonial Submitted.', 'corpora_theme'),
				9 => sprintf(__('Testimonial Scheduled For: <strong>%1$s</strong>. ', 'corpora_theme'), date_i18n( __( 'M j, Y @ G:i', 'corpora_theme' ), strtotime($post->post_date))),
				10 => __('Testimonial Draft Updated.', 'corpora_theme')
		);
	return $messages;
}

/*
 Add actions
*/
add_action( 'init', 'bk_register_testimonial' ); 
add_filter( 'post_updated_messages', 'bk_testimonial_messages' );


/*
Add meta box for author and authors link handling
*/

function bk_testimonial_add_metabox() {
	add_meta_box( 
        'bk_testimonial_custom_settings',
        __( 'Testimonial Author', 'corpora_theme' ),
        'bk_testimonial_inner_custom_box',
        'testimonial',
        'normal',
        'high' 
    );
}

$bk_testimonial_item_settings = array(
   array(
     'name' => __( 'Author', 'corpora_theme' ),
     'id' => '_bk_testimonial_author_name',
     'description' => __( 'Specify authors name of this testimonial e.g Ted Collins', 'corpora_theme' ),
     'type' => 'textinput',
     'default' => ''
   ),
   array(
     'name' => __( 'Position', 'corpora_theme' ),
     'id' => '_bk_testimonial_author_position',
     'description' => __( 'Specify position e.g "Manager", "CEO"', 'corpora_theme' ),
     'type' => 'textinput',
     'default' => ''
   ),
   array(
     'name' => __( 'Authors Site', 'corpora_theme' ),
     'id' => '_bk_testimonial_author_link',
     'description' => __( 'Specify link to authors site if any e.g http://www.envato.com', 'corpora_theme' ),
     'type' => 'textinput',
     'default' => ''
   ),
   array(
     'name' => __( 'Link target', 'corpora_theme' ),
     'id' => '_bk_testimonial_author_link_target',
     'description' => __( 'Specify target for link - new tab/window or same window', 'corpora_theme' ),
     'type' => 'combobox',
     'default' => '_self',
     'all_values' => array("_self" => __( 'Open in the same window/tab', 'corpora_theme' ), 
     											 "_blank" => __( 'Open in new window/tab', 'corpora_theme' ) )
   )
);
   
/* Prints the box content */
function bk_testimonial_inner_custom_box( $post ) {
  global $bk_testimonial_item_settings;
  
  echo '<div class="bk_custom_settings_wrap">';
  echo '<p>' . __( 'This section can be used to specify testimonial authors name and link to external site', 'corpora_theme' ) . '</p>';
  
  // Use nonce for verification
  echo '<input type="hidden" name="bk_meta_testimonial_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
  
  echo '<table id="bk_testimonial_item_settings" class="bk_form_wrap">';
  
  foreach ($bk_testimonial_item_settings as $setting) {
    
    $value = null;
    
    if( isset($setting['id']) ) {
		  $value = get_post_meta($post->ID, $setting['id'], true);
	  }

    Bk_Field_Factory::getInstance()->print_setting_field( $setting, $value );
    
  }
  
  echo '</table>';
  echo '</div>';
  
}


/* When the post is saved, saves our custom data */
function bk_save_testimonial_custom_metadata( $post_id ) {
  global $bk_testimonial_item_settings;
  
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !isset($_POST['bk_meta_testimonial_nounce']) || !wp_verify_nonce( $_POST['bk_meta_testimonial_nounce'], basename(__FILE__) ) )
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

  foreach ($bk_testimonial_item_settings as $setting) {
		if( 'separator' == $setting['type'] )
			continue;
		
		$old_value = get_post_meta($post_id, $setting['id'], true);
		$new_value = false;
    if( isset($_POST[$setting['id']]) ) {
    	$new_value = $_POST[$setting['id']];
    }	
 
		update_post_meta($post_id, $setting['id'], $new_value);
	}
}

/*
 Add actions
*/

add_action( 'add_meta_boxes', 'bk_testimonial_add_metabox' );
add_action( 'save_post', 'bk_save_testimonial_custom_metadata' );
?>