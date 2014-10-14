<?php
/*
 Register testimonial, create labels setup etc...
*/
function bk_register_pricing_table() {
    $labels = array(
			'name' => __('Pricing Table', 'corpora_theme'),
			'singular_name' => __('Pricing Table', 'corpora_theme'),
    		'all_items' => __('All Pricing Tables', 'corpora_theme'),
			'add_new' => __('Add Pricing Table', 'corpora_theme'),
    		'add_new_item' => __('Add Pricing Table', 'corpora_theme'),
			'edit_item' => __('Edit Pricing Table', 'corpora_theme'),
			'new_item' => __('New Pricing Table', 'corpora_theme'),
			'view_item' => __('View Pricing Table', 'corpora_theme'),
			'search_items' => __('Search Pricing Tables', 'corpora_theme'),
			'not_found' =>  __('No Pricing Tables Found', 'corpora_theme'),
			'not_found_in_trash' => __('No Pricing Tables Found In Trash', 'corpora_theme'),
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
			'supports' => array( 'title' )
		); 

    register_post_type( 'pricing_table' , $args );
}

/*
 Custom testimonial messages
*/
function bk_pricing_table_messages($messages)
{
	global $post, $post_ID;
	
	$messages['pricing_table'] =
		array(
				0 => '',
				1 => __('Pricing Table Updated.', 'corpora_theme'),
				2 => __('Custom Field Updated.', 'corpora_theme'),
				3 => __('Custom Field Deleted.', 'corpora_theme'),
				4 => __('Pricing Table Updated.', 'corpora_theme'),
				5 => isset($_GET['revision']) ? sprintf( __('Pricing Table Restored To Revision From %s', 'corpora_theme'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
				6 => __('Pricing Table Published.', 'corpora_theme'),
				7 => __('Pricing Table Saved.', 'corpora_theme'),
				8 => __('Pricing Table Submitted.', 'corpora_theme'),
				9 => sprintf(__('Pricing Table Scheduled For: <strong>%1$s</strong>. ', 'corpora_theme'), date_i18n( __( 'M j, Y @ G:i', 'corpora_theme' ), strtotime($post->post_date))),
				10 => __('Pricing Table Draft Updated.', 'corpora_theme')
		);
	return $messages;
}

/*
 Add actions
*/
add_action( 'init', 'bk_register_pricing_table' ); 
add_filter( 'post_updated_messages', 'bk_pricing_table_messages' );


/*
Add meta box for author and authors link handling
*/

function bk_pricing_table_add_metabox() {
	add_meta_box( 
        'bk_pricing_table_custom_settings',
        __( 'Pricing Table', 'corpora_theme' ),
        'bk_pricing_table_inner_custom_box',
        'pricing_table',
        'normal',
        'high' 
    );
}
   
/* Prints the box content */
function bk_pricing_table_inner_custom_box( $post ) {
  // Use nonce for verification
  echo '<input type="hidden" name="bk_meta_pricing_table_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
  
  $pricing_table = get_post_meta($post->ID, '_bk_pricing_table' , true);
  $column_count = 0;
  if( !empty( $pricing_table ) ) {
  	//echo var_dump($pricing_table);
  	$column_count = count($pricing_table['columns']);
  }	
  
  echo '<div id="bk_pricing_table_outer_wrap">';
  echo '<input type="hidden" name="_bk_pricing_table_column_count" value="' . $column_count . '" />';
  echo '<div class="bk-big-button-wrap"><div class="bk_add_pricing_table_column">' . __("Add Column", "corpora_theme") . '</div></div>';
  echo '<div id="bk_pricing_table_wrap">';
  
  if( !empty( $pricing_table ) ) {
  	bk_print_pricing_table_markup( $pricing_table );
  }	
  
  echo '</div>';
  echo '<div class="clearfix"></div>';
  echo '</div>';
}
/*
  Prints pricing table markup
*/
function bk_print_pricing_table_markup( $pricing_table ) {
	$recommended_id = intval($pricing_table['recommended']);
	$column_index = 0;
	foreach( $pricing_table['columns'] as $column ) {
		
		$radio_checked = '';
	 	if( $column_index == $recommended_id ) {
	 		$radio_checked = 'checked="checked"';
	 	}	
	 	
	 	echo '<div class="bk_pricing_table_column_wrap">';
	 	echo '<div class="bk-pricing-table-titlebar">Column<a title="Remove Column" class="bk_pricing_table_remove_column"></a></div>';
	 	echo '<div class="bk_pricing_table_column_inner_wrap">';
	 	echo '<label><input ' . $radio_checked . ' type="radio" name="_bk_pricing_table_column_recommended" value="' . $column_index . '">Recommended</label>';
	 	echo '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_title" name="_bk_pricing_table_column_title_' . $column_index . '" value="' . $column['title'] . '" placeholder="Title" title="Title">';
	 	echo '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_price" name="_bk_pricing_table_column_price_' . $column_index . '" value="' . $column['price'] . '" placeholder="Price" title="Price">';
	 	echo '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_description" name="_bk_pricing_table_column_description_' . $column_index . '" value="' . $column['description'] . '" placeholder="Description" title="Description">';
	 	echo '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_button_text" name="_bk_pricing_table_column_button_text_' . $column_index . '" value="' . $column['button_text'] . '" placeholder="Button Text" title="Button Text">';
	 	echo '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_button_link" name="_bk_pricing_table_column_button_link_' . $column_index . '" value="' . $column['button_link'] . '" placeholder="Button Link" title="Button Link">';
	 	echo '<input class="bk_column_global_setting" type="hidden" data-core="bk_pricing_table_column_features_count" name="_bk_pricing_table_column_features_count_' . $column_index . '" value="' . count($column['features']) . '">';
	 	echo '<div class="bk_pricing_features_title_bar clearfix">Features<a class="bk_pricing_table_add_feature">Add</a></div>';
	 	echo '<div class="bk_pricing_table_features_wrap">';
	 	
	 	$features_count = 0;
	 	foreach( $column['features'] as $feature ) {
	 		echo '<div class="bk_pricing_table_feature">';
			echo '<span class="bk_pricing_column_feature_handle"></span>';
			echo '<input type="text" data-core="bk_pricing_table_feature" name="_bk_pricing_table_feature_' . $column_index . '_' . $features_count . '" value="' . $feature . '" placeholder="Feature" title="Feature">';
			echo '<a title="Remove Feature" class="bk_pricing_column_remove_feature"></a>';
			echo '</div>';
			$features_count++;
	 	}	
	 	
	 	echo '</div>';
	 	
	 	echo '</div>';
	 	echo '</div>';
		
		$column_index++;
	}	
}	

/* When the post is saved, saves our custom data */
function bk_save_pricing_table_custom_metadata( $post_id ) {
  global $bk_testimonial_item_settings;
  
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !isset($_POST['bk_meta_pricing_table_nounce']) || !wp_verify_nonce( $_POST['bk_meta_pricing_table_nounce'], basename(__FILE__) ) )
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
  // First cleanup of old valuse
  
  $column_count = $_POST['_bk_pricing_table_column_count'];
  $pricing_table = array();
  $pricing_table['recommended'] = $_POST['_bk_pricing_table_column_recommended'];
  $columns = array();
  for( $index = 0; $index <  $column_count; $index++ ) {
  		$column = array();
  		$column['title'] = $_POST['_bk_pricing_table_column_title_' . $index];
  		$column['price'] = $_POST['_bk_pricing_table_column_price_' . $index];
  		$column['description'] = $_POST['_bk_pricing_table_column_description_' . $index];
  		$column['button_text'] = $_POST['_bk_pricing_table_column_button_text_' . $index];
  		$column['button_link'] = $_POST['_bk_pricing_table_column_button_link_' . $index];
  		
  		$features_count = $_POST['_bk_pricing_table_column_features_count_' . $index];
  		$features = array();
  		for( $feature = 0; $feature < $features_count; $feature++ ) {
  			$features[] = $_POST['_bk_pricing_table_feature_' . $index . '_' . $feature];	
  		}
  		
  		$column['features'] = $features;
  		$columns[] = $column;
  }
  
  $pricing_table['columns'] = $columns;
  update_post_meta($post_id, '_bk_pricing_table' , $pricing_table);		
}

/*
 Add actions
*/

add_action( 'add_meta_boxes', 'bk_pricing_table_add_metabox' );
add_action( 'save_post', 'bk_save_pricing_table_custom_metadata' );
?>