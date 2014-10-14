<?php
/*
 Register portfolio, create labels setup etc...
*/
function bk_register_portfolio() {
    $labels = array(
			'name' => __('Portfolio', 'corpora_theme'),
			'singular_name' => __('Portfolio', 'corpora_theme'),
    		'all_items' => __('All Portfolios', 'corpora_theme'),
			'add_new' => __('Add Portfolio Item', 'corpora_theme'),
    		'add_new_item' => __('Add Portfolio Item', 'corpora_theme'),
			'edit_item' => __('Edit Portfolio Item', 'corpora_theme'),
			'new_item' => __('New Portfolio Item', 'corpora_theme'),
			'view_item' => __('View Portfolio', 'corpora_theme'),
			'search_items' => __('Search Portfolio', 'corpora_theme'),
			'not_found' =>  __('No Portfolio Items Found', 'corpora_theme'),
			'not_found_in_trash' => __('No Portfolio Items Found In Trash', 'corpora_theme'),
			'parent_item_colon' => ''
		);
    
    $portfolio_slug = str_replace(" ", "-", get_option("bk_portfolio_single_slug", "portfolio"));

    if( empty($portfolio_slug) ) {
    	$portfolio_slug = "portfolio";
    }
    
    $args = array(
			'labels' => $labels,
			'public' => true,
    		'show_in_nav_menus' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => $portfolio_slug
			),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt', 'page-attributes') // add 'comments' if needed
		); 

    register_post_type( 'portfolio' , $args );
}

/*
 Custom portfolio messages
*/
function bk_portfolio_messages($messages)
{
	global $post, $post_ID;
	
	$messages['portfolio'] =
		array(
			0 => '',
			1 => sprintf(('Portfolio Updated. <a href="%s">View portfolio</a>'), esc_url(get_permalink($post_ID))),
			2 => __('Custom Field Updated.', 'corpora_theme'),
			3 => __('Custom Field Deleted.', 'corpora_theme'),
			4 => __('Portfolio Updated.', 'corpora_theme'),
			5 => isset($_GET['revision']) ? sprintf( __('Portfolio Restored To Revision From %s', 'corpora_theme'), wp_post_revision_title((int)$_GET['revision'], false)) : false,
			6 => sprintf(__('Portfolio Published. <a href="%s">View Portfolio</a>', 'corpora_theme'), esc_url(get_permalink($post_ID))),
			7 => __('Portfolio Saved.', 'corpora_theme'),
			8 => sprintf(__('Portfolio Submitted. <a target="_blank" href="%s">Preview Portfolio</a>', 'corpora_theme'), esc_url( add_query_arg('preview', 'true', get_permalink($post_ID)))),
			9 => sprintf(__('Portfolio Scheduled For: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Portfolio</a>', 'corpora_theme'), date_i18n( __( 'M j, Y @ G:i', 'corpora_theme' ), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
			10 => sprintf(__('Portfolio Draft Updated. <a target="_blank" href="%s">Preview Portfolio</a>', 'corpora_theme'), esc_url( add_query_arg('preview', 'true', get_permalink($post_ID)))),
		);
	return $messages;
}

/*
 Custom portfolio taxonomy
*/
function bk_register_portfolio_taxonomy($messages)
{
	register_taxonomy(
		"filter",
		"portfolio",
		array(
			"hierarchical" => true,
			"label" => __( "Portfolio Filter", 'corpora_theme' ),
			"singular_label" => __( "Portfolio Filter", 'corpora_theme' ),
			"rewrite" => array(
				'slug' => 'filter',
				'hierarchical' => true
			)
		)
	);
	
	register_taxonomy(
		"portfolio_skills",
		"portfolio",
		array(
			"hierarchical" => false,
			"label" => __( "Skills", 'corpora_theme' ),
			"singular_label" => __( "Skill", 'corpora_theme' ),
			"rewrite" => array(
				'slug' => 'skill-type',
				'hierarchical' => true
			)
		)
	);
}	

/*
 Add custom meta box for images, external links to project and other options
*/
function bk_portfolio_add_metabox( $post_type ) {
	if ( ! get_post_type_object( $post_type ) ) {
        // It's a comment or a link, or something else
        return;
  }
	
	remove_meta_box( 'formatdiv', 'portfolio', 'side' );
	
	add_meta_box( 'bk_custom_portfolio_formatdiv', __( 'Format', 'corpora_theme' ), 'bk_custom_portfolio_format_meta_box', 'portfolio', 'side', 'core' );
	add_meta_box( 
        'bk_portfolio_custom_settings',
        __( 'Portfolio Entry Settings', 'corpora_theme' ),
        'bk_portfolio_inner_custom_box',
        'portfolio',
        'normal',
        'high' 
    );
}

function bk_custom_portfolio_format_meta_box( $post, $box ) {
    if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) :
    $post_formats = get_theme_support( 'post-formats' );

    // This is our extra code
    // If the post type has registered post formats, use those instead
    if ( is_array( $GLOBALS['_wp_post_type_features'][$post->post_type]['post-formats'] ) ) {
        $post_formats = $GLOBALS['_wp_post_type_features'][$post->post_type]['post-formats'];
    }

    if ( is_array( $post_formats[0] ) ) :
        $post_format = get_post_format( $post->ID );
        if ( !$post_format ) {
            $post_format = '0';
        }    
        $post_format_display = get_post_format_string( $post_format );
        // Add in the current one if it isn't there yet, in case the current theme doesn't support it
        if ( $post_format && !in_array( $post_format, $post_formats[0] ) )
            $post_formats[0][] = $post_format;
	    ?>
	    <div id="post-formats-select">
	        <input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" <?php checked( $post_format, '0' ); ?> /> <label for="post-format-0" class="post-format-icon post-format-image"><?php _e('Standard', 'corpora_theme' ); ?></label>
	        <?php foreach ( $post_formats[0] as $format ) : ?>
	        <br /><input type="radio" name="post_format" class="post-format" id="post-format-<?php echo esc_attr( $format ); ?>" value="<?php echo esc_attr( $format ); ?>" <?php checked( $post_format, $format ); ?> /> <label for="post-format-<?php echo esc_attr( $format ); ?>" class="post-format-icon post-format-<?php echo esc_attr( $format ); ?>"><?php echo esc_html( get_post_format_string( $format ) ); ?></label>
	        <?php endforeach; ?><br />
	    </div>
	    <?php 
	    endif; 
	endif;
}


/* Array with fields used for portfolio item custom type */
$bk_portfolio_item_settings = array(
	array(
     'description' => __( 'Thumbnail Settings', 'corpora_theme' ),
     'type' => 'separator',
    ),
	array(
		'name' => __( 'Portfolio "METRO" Thumbnail Size', 'corpora_theme' ),
		'id' => '_bk_portfolio_thumbnail_size',
		'description' => __( 'Choose size of thumbnail used for this portfolio entry while displayed inside "METRO" style layout', 'corpora_theme' ),
		'type' => 'combobox',
		'default' => '1-1',
		'all_values' => array(	"1-1" => "Small ( 1x1 )", 
								"2-1" => "Landscape ( 2x1 )",
								"1-2" => "Portrait ( 1x2 )",
								"2-2" => "Large ( 2x2 )",			
							 )
	),
	array(
		'name' => __( 'Portfolio "MIXED MASONRY" Thumbnail Size', 'corpora_theme' ),
		'id' => '_bk_portfolio_mixed_masonry_thumbnail_size',
		'description' => __( 'Choose size of thumbnail used for this portfolio entry while displayed inside "MIXED MASONRY" style layout', 'corpora_theme' ),
		'type' => 'combobox',
		'default' => 'small',
		'all_values' => array(	"small" => "Small",
								"large" => "Large",
		)
	),
	array(
		'name' => __( 'Portfolio Thumbnail Behavior', 'corpora_theme' ),
		'id' => '_bk_portfolio_thumbnail_behavior',
		'description' => __( 'Choose how thumbnail in portfolio will behave when it\'s clicked by the user. You can have image, video, gallery popups, external links, ajax and no-ajax content portfolio links.', 'corpora_theme' ),
		'type' => 'stacked_combobox',
		'default' => 'content_link',
		'all_values' => array(	"none" => array( "label" => "None", "buddy" => "" ),
								"featured_image_popup" => array( "label" => "Featured Image Popup", "buddy" => "" ),
								"image_popup" => array( "label" => "Image Popup", "buddy" => "#buddy_wrap_bk_portfolio_thumbnail_image_link" ),
								"vimeo_popup" => array( "label" => "Vimeo Popup", "buddy" => "#buddy_wrap_bk_portfolio_thumbnail_vimeo_link, #buddy_wrap_bk_popup_video_height, #buddy_wrap_bk_popup_video_width" ),
								"youtube_popup" => array( "label" => "Youtube Popup", "buddy" => "#buddy_wrap_bk_portfolio_thumbnail_youtube_link, #buddy_wrap_bk_popup_video_height, #buddy_wrap_bk_popup_video_width" ),
								"gallery_popup" => array( "label" => "Gallery Popup", "buddy" => "#buddy_wrap_bk_portfolio_popup_gallery" ),
								"external_link" => array( "label" => "External Link", "buddy" => "#buddy_wrap_bk_portfolio_thumbnail_external_link" ),
								"content_link" => array( "label" => "Content Link", "buddy" => "#buddy_wrap_bk_portfolio_content_link_desc" ) 												
							)
	),
	array(
		'name' => __( 'Image Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_thumbnail_image_link',
		'description' => __( 'Image Link', 'corpora_theme' ),
		'type' => 'browse_button_with_label',
		'default' => ''
		),
	array(
		'name' => __( 'Vimeo Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_thumbnail_vimeo_link',
		'description' => __( 'Vimeo Link', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Youtube Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_thumbnail_youtube_link',
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
		'id' => '_bk_portfolio_popup_gallery',
		'description' => __( 'Use button on the right to add images and vidoes to portfolio gallery. Drag and drop to change order of images and videos.', 'corpora_theme' ),
		'type' => 'gallery',
		'sub_type' => 'popup_gallery',	
		'default' => 0,
		'images' => array()
	),
	array(
		'name' => __( 'External Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_thumbnail_external_link',
		'description' => __( 'Specify external link for this project.', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Content Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_content_link_desc',
		'description' => __( 'This is additional info for above setting.', 'corpora_theme' ),
		'additional_info' => __( 'Thumbnail will be wrapped with simple link to whole portfolio entry so when user clicks on it it will show whole portfolio entry on the new page.', 'corpora_theme' ),
		'type' => 'description_field'
	),
	array(
     'description' => __( 'Portfolio Entry Settings', 'corpora_theme' ),
     'type' => 'separator',
    ),
	array(
		'name' => __( 'Portfolio Entry Layout', 'corpora_theme' ),
		'id' => '_bk_portfolio_entry_layout',
		'description' => __( 'Choose layout for portfolio entry. Decide if description will be on the left, right or at the bottom of entry featured content. Optionally enable/disable meta box with tags, social buttons etc...', 'corpora_theme' ),
		'type' => 'stacked_image_radiobuttons',
		'default' => 'fullwidth_meta_right',
		'all_values' => array("fullwidth_no_meta" => array( "label" => "Fullwidth no meta", "image" => "fullwidth_no_meta.png", "buddy" => "" ),
							  "fullwidth_meta_bottom" => array( "label" => "Fullwidth meta at the bottom", "image" => "fullwidth_meta_bottom.png", "buddy" => "" ),
							  "fullwidth_meta_top" => array( "label" => "Fullwidth meta at the top", "image" => "fullwidth_meta_top.png", "buddy" => "" ),
							  "fullwidth_meta_left" => array( "label" => "Fullwidth meta on the left side", "image" => "fullwidth_meta_left.png", "buddy" => "#buddy_wrap_bk_portfolio_meta_description_width" ),
							  "fullwidth_meta_right" => array( "label" => "Fullwidth meta on the right side", "image" => "fullwidth_meta_right.png", "buddy" => "#buddy_wrap_bk_portfolio_meta_description_width" ),
							  "description_left_no_meta" => array( "label" => "Description left no meta", "image" => "description_left_no_meta.png", "buddy" => "#buddy_wrap_bk_portfolio_meta_description_width" ),
							  "description_right_no_meta" => array( "label" => "Description right no meta", "image" => "description_right_no_meta.png", "buddy" => "#buddy_wrap_bk_portfolio_meta_description_width" ),
							  "description_left_with_meta" => array( "label" => "Description left with meta", "image" => "description_left_with_meta.png", "buddy" => "#buddy_wrap_bk_portfolio_meta_description_width" ),
							  "description_right_with_meta" => array( "label" => "Description right with meta", "image" => "description_right_with_meta.png", "buddy" => "#buddy_wrap_bk_portfolio_meta_description_width"  ),
							 )
	),
	array(
		'name' => __( 'Hide Featured Content', 'corpora_theme' ),
		'id' => '_bk_portfolio_hide_featured_content',
		'description' => __( 'If enabled featured content ( image, video, gallery, audio etc... ) will not be displayed. Usefull for fullscreen background slider portfolio entry or if custom portfolio appearance need to be made.', 'corpora_theme' ),
		'type' => 'checkbox',
		'default' => false
	),
	array(
		'name' => __( 'Portfolio Meta/Description Width', 'corpora_theme' ),
		'id' => '_bk_portfolio_meta_description_width',
		'description' => __( 'Choose width of meta/description field. This setting depends on portfolio entry layout selected above. For fullwidth layouts with meta at the top or bottom have no effect. For fullwidth layouts with meta at left or right side it controls width of meta field. For layouts with description at the left or right side it controls width of whole description field.', 'corpora_theme' ),
		'type' => 'combobox',
		'default' => '4',
		'all_values' => array(	"3" => "25%",
								"4" => "33%",
								"5" => "42%",
								"6" => "50%"
		)
	),
	/*
		array(
		'name' => __( 'Portfolio Featured Area Type', 'corpora_theme' ),
		'id' => '_bk_portfolio_featured_area_type',
		'description' => __( 'Choose how portfolio featured area type. It is container that holds portfolio featured content like images, galleries, videos and so on. Use "Fit featured area" to use whole available page space or "Padded featured area" to leave small blank space around featured area.', 'corpora_theme' ),
		'type' => 'image_radiobuttons',
		'default' => 'padded_featured_area',
		'all_values' => array("fit_featured_area" => array( "label" => "Fit featured area", "image" => "fit_featured_area.png" ),
							  "padded_featured_area" => array( "label" => "Padded featured area", "image" => "padded_featured_area.png" )	  
		)	
	),
	*/
	array(
		'name' => __( 'Portfolio External Link Enabled', 'corpora_theme' ),
		'id' => '_bk_portfolio_external_link_enabled',
		'description' => __( 'If enabled external link to project will be displayed in portfolio meta secition.', 'corpora_theme' ),
		'type' => 'checkbox_section',
		'rows' => 2,
		'default' => false
	),
	array(
		'name' => __( 'Portfolio External Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_external_link',
		'description' => __( 'Link to external project', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Portfolio External Link Text', 'corpora_theme' ),
		'id' => '_bk_portfolio_external_link_text',
		'description' => __( 'Text that will be displayed as link e.g "Visit Project"', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => 'Visit Project'
	),
	
	array(
     'description' => __( 'Standard Format Settings', 'corpora_theme' ),
     'type' => 'separator'
    ),
    array(
		'name' => __( 'Image Popup Enabled', 'corpora_theme' ),
		'id' => '_bk_portfolio_single_featured_image_popup_enabled',
		'description' => __( 'If enabled full size image will be visible in popup when user clicks featured image, otherwise nothing will happen.', 'corpora_theme' ),
		'type' => 'checkbox',
		'default' => false
	),
	array(
     	'description' => __( 'Gallery Format Settings', 'corpora_theme' ),
     	'type' => 'separator',
  	),
	array(
		'name' => __( 'Portfolio Gallery Type', 'corpora_theme' ),
		'id' => '_bk_portfolio_gallery_type',
		'description' => __( 'Choose how portfolio gallery should be displayed as regular slider or as stack of images.', 'corpora_theme' ),
		'type' => 'stacked_combobox',
		'default' => 'slider',
		'all_values' => array(	"slider" => array( "label" => "Slider", "buddy" => "#buddy_wrap_bk_portfolio_slider_transition_type, #buddy_wrap_bk_portfolio_slider_nav_type, #buddy_wrap_bk_portfolio_slider_thumb_col_count, #buddy_wrap_bk_portfolio_slider_autoplay_enabled, #buddy_wrap_bk_portfolio_slider_smooth_height_enabled" ), 
								"gallery" => array( "label" => "Gallery", "buddy" => "#buddy_wrap_bk_portfolio_gallery_layout, #buddy_wrap_bk_portfolio_gallery_base_size, #buddy_wrap_bk_portfolio_gallery_image_effect, #buddy_wrap_bk_portfolio_gallery_image_overlay, #buddy_wrap_bk_portfolio_gallery_gutter" ),
								"stacked_gallery" => array( "label" => "Stacked Gallery", "buddy" => "#buddy_wrap_bk_portfolio_stacked_gallery_desc, #buddy_wrap_bk_portfolio_gallery_image_effect, #buddy_wrap_bk_portfolio_gallery_image_overlay" )
							 )
	),
	array(
		'name' => __( 'Stacked Gallery', 'corpora_theme' ),
		'id' => '_bk_portfolio_stacked_gallery_desc',
		'description' => __( 'This is additional info for above setting.', 'corpora_theme' ),
		'additional_info' => __( 'This is simple gallery where each item is displayed under another.', 'corpora_theme' ),
		'type' => 'description_field'
	),
	array(
		'name' => __( 'Autoplay', 'corpora_theme' ),
		'id' => '_bk_portfolio_slider_autoplay_enabled',
		'description' => __( 'If enbled slideshow will start to switch slides automatically otherwise slider navigation will allow to do so.', 'corpora_theme' ),
		'type' => 'checkbox',
		'default' => true
	),
	array(
				'name' => __( 'Portfolio Layout', 'corpora_theme' ),
				'id' => '_bk_portfolio_gallery_layout',
				'description' => __( 'Choose layout for portfolio gallery items.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'grid',
				'all_values' => array('grid' => __( 'Grid', 'corpora_theme' ),
						'metro' => __( 'Metro', 'corpora_theme' ),
						'masonry' => __( 'Masonry', 'corpora_theme' ),
						'mixed_masonry' => __( 'Mixed Masonry', 'corpora_theme' ) )
	),
	array(
				'name' => __( 'Base Size', 'corpora_theme' ),
				'id' => '_bk_portfolio_gallery_base_size',
				'description' => __( 'Choose portfolio gallery layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'small',
				'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
						'medium' => __( 'Medium', 'corpora_theme' ),
						'large' => __( 'Large', 'corpora_theme' ) )
	),
	array(
				'name' => __( 'Thumb Effect', 'corpora_theme' ),
				'id' => '_bk_portfolio_gallery_image_effect',
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
				'id' => '_bk_portfolio_gallery_image_overlay',
				'description' => __( 'Choose optional image overlay, it will be visible when user hovers over image.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'icon',
				'all_values' => array('none' => __( 'None', 'corpora_theme' ),
									  'icon' => __( 'Icon', 'corpora_theme' ) )
	),
	array(
				'name' => __( 'Gutter', 'corpora_theme' ),
				'id' => '_bk_portfolio_gallery_gutter',
				'description' => __( 'Choose gutter size which will be visible between portfolio elements.', 'corpora_theme' ),
				'type' => 'slider',
				'slider_class_type' => 'pixel',
				'default' => 0,
				'min' => 0,
				'max' => 40,
				'step' => 1
	),
	array(
		'name' => __( 'Porfolio Gallery', 'corpora_theme' ),
		'id' => '_bk_portfolio_gallery',
		'description' => __( 'Use button on the right to add images to portfolio gallery. Drag and drop to change order of images. Do not forget to check Format -> Gallery on the right side of page to activate gallery. Use below options to change appearance of gallery.', 'corpora_theme' ),
		'type' => 'gallery',
		'sub_type' => 'featured_gallery',	
		'default' => 0,
		'images' => array()
	),
	array(
     'description' => __( 'Video Format Settings', 'corpora_theme' ),
     'type' => 'separator',
    ),
    array(
		'name' => __( 'Video Type', 'corpora_theme' ),
		'id' => '_bk_portfolio_video_type',
		'description' => __( 'Choose type of video used for portfolio featured content it can be Youtube, Vimeo or self hosted video.', 'corpora_theme' ),
		'type' => 'stacked_combobox',
		'default' => 'vimeo_link',
		'all_values' => array(	"vimeo_link" => array( "label" => "Vimeo Link", "buddy" => "#buddy_wrap_bk_portfolio_vimeo_link" ),
														"youtube_link" => array( "label" => "Youtube Link", "buddy" => "#buddy_wrap_bk_portfolio_youtube_link" ),
														"self_hosted" => array( "label" => "Self Hosted Video", "buddy" => "#buddy_wrap_bk_portfolio_self_hosted_m4v_link, #buddy_wrap_bk_portfolio_self_hosted_ogv_link, #buddy_wrap_bk_portfolio_self_hosted_video_poster" )												
												 )
	),
	array(
		'name' => __( 'Vimeo Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_vimeo_link',
		'description' => __( 'Link to Vimeo video', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Youtube Link', 'corpora_theme' ),
		'id' => '_bk_portfolio_youtube_link',
		'description' => __( 'Link to Youtube video', 'corpora_theme' ),
		'type' => 'textinput',
		'default' => ''
	),
	array(
		'name' => __( 'Self Hosted Video (*.m4v)', 'corpora_theme' ),
		'id' => '_bk_portfolio_self_hosted_m4v_link',
		'description' => __( 'Link to selfhosted (*.m4v) video. This is primary video. It is recomended to specify also additonal link to the same vidoe in another format (*.ogv) to make player work on any device if (*.m4v) fails to load.', 'corpora_theme' ),
		'type' => 'simple_browse_button_with_label',
		'default' => ''
	),
	array(
		'name' => __( 'Self Hosted Video (*.ogv)', 'corpora_theme' ),
		'id' => '_bk_portfolio_self_hosted_ogv_link',
		'description' => __( 'Additional (*.ogv) format of video. This is secondary video link. Note! it is required to specify (*.m4v) first to make player work. If You specify only link to (*.ogv) file it may not work in all browsers.', 'corpora_theme' ),
		'type' => 'simple_browse_button_with_label',
		'default' => ''
	),
	array(
		'name' => __( 'Video Poster', 'corpora_theme' ),
		'id' => '_bk_portfolio_self_hosted_video_poster',
		'description' => __( 'This image will be used as poster for player filling background before user hit play button.', 'corpora_theme' ),
		'type' => 'browse_button_with_label',
		'default' => ''
	),
	array(
		 'name' => __( 'Video Width', 'corpora_theme' ),
		 'id' => '_bk_portfolio_video_width',
		 'description' => __( 'Choose width for featured video. It will work with below setting ( height ) to calculate video aspect ratio e.g for video with original aspect ratio 16:9 set 16 for width and 9 for height or set original video dimensions e.g 1600px 900px. For videos that You wolud like to show as popup video it is required to set desired size of popup in pixels e.g 800px 450px.', 'corpora_theme' ),
		 'type' => 'slider',
		 'slider_class_type' => 'pixel',
		 'default' => 640,
		 'min' => 1,
		 'max' => 1920,
		 'step' => 1
	),
	array(
		 'name' => __( 'Video Height', 'corpora_theme' ),
		 'id' => '_bk_portfolio_video_height',
		 'description' => __( 'Choose height for featured video. It will work with above setting ( width ) to calculate video aspect ratio e.g for video with original aspect ratio 16:9 set 16 for width and 9 for height or set original video dimensions e.g 1600px 900px. For videos that You wolud like to show as popup video it is required to set desired size of popup in pixels e.g 800px 450px.', 'corpora_theme' ),
		 'type' => 'slider',
		 'slider_class_type' => 'pixel',
		 'default' => 360,
		 'min' => 1,
		 'max' => 1080,
		 'step' => 1
	),
	array(
     'description' => __( 'Audio Format Settings', 'corpora_theme' ),
     'type' => 'separator',
    ),
    array(
		'name' => __( 'Audio Type', 'corpora_theme' ),
		'id' => '_bk_portfolio_audio_type',
		'description' => __( 'Choose type of audio that will be used as post featured content, it can be selfhosted *.mp3 file or link to soundcloud track set or group.', 'corpora_theme' ),
		'type' => 'stacked_combobox',
		'default' => 'selfhosted_audio',
		'all_values' => array(	"selfhosted_audio" => array( "label" => "Selfhosted Audio", "buddy" => "#buddy_wrap_bk_portfolio_audio_link, #buddy_wrap_bk_portfolio_audio_poster" ),
				"soundcloud_audio" => array( "label" => "SoundCloud Audio", "buddy" => "#buddy_wrap_bk_portfolio_audio_soundcloud_link, #buddy_wrap_bk_portfolio_audio_soundcloud_comments_enabled, #buddy_wrap_bk_portfolio_audio_soundcloud_show_artwork, #buddy_wrap_bk_portfolio_audio_soundcloud_color, #buddy_wrap_bk_portfolio_audio_soundcloud_auto_play" )
		)
	),
	array(
			'name' => __( 'SoundCloud Link', 'corpora_theme' ),
			'id' => '_bk_portfolio_audio_soundcloud_link',
			'description' => __( 'Specify link to soundcloud track, set or group.', 'corpora_theme' ),
			'type' => 'textinput',
			'default' => ''
	),
	array(
			'name' => __( 'Auto Play', 'corpora_theme' ),
			'id' => '_bk_portfolio_audio_soundcloud_auto_play',
			'description' => __( 'If enabled soundloud widget will start playback once loaded.', 'corpora_theme' ),
			'type' => 'checkbox',
			'default' => false
	),
	array(
			'name' => __( 'Show Comments', 'corpora_theme' ),
			'id' => '_bk_portfolio_audio_soundcloud_comments_enabled',
			'description' => __( 'If enabled soundloud widget will show users comments about the track.', 'corpora_theme' ),
			'type' => 'checkbox',
			'default' => false
	),
	array(
			'name' => __( 'Show Artwork', 'corpora_theme' ),
			'id' => '_bk_portfolio_audio_soundcloud_show_artwork',
			'description' => __( 'If enabled soundloud widget will show users artwork ( logo / avatar ) at the left top corner.', 'corpora_theme' ),
			'type' => 'checkbox',
			'default' => false
	),
	array(
			'name' => __( 'Color', 'corpora_theme' ),
			'id' => '_bk_portfolio_audio_soundcloud_color',
			'description' => __( 'Choose soundcloud widget theme color - play/pause button and playlist selections. Default soundcloud color is #ff6600', 'corpora_theme' ),
			'type' => 'simple_colorpicker',
			'default' => "#ff6600"
	),
    array(
		'name' => __( 'Audio File (*.mp3)', 'corpora_theme' ),
		'id' => '_bk_portfolio_audio_link',
		'description' => __( 'Specify link to audio (*.mp3) file that You want to be portfolio featured content.', 'corpora_theme' ),
		'type' => 'simple_browse_button_with_label',
		'default' => ''
	),
    array(
		'name' => __( 'Audio Poster', 'corpora_theme' ),
		'id' => '_bk_portfolio_audio_poster',
		'description' => __( 'This image will be used as poster for audio file.', 'corpora_theme' ),
		'type' => 'browse_button_with_label',
		'default' => ''
	)
);

/* Prints the box content */
function bk_portfolio_inner_custom_box( $post ) {
  global $bk_portfolio_item_settings;
  
  echo '<div class="bk_custom_settings_wrap">';
  echo '<p>' . __( 'This section can be used to specify portfolio item external link which will be shown with other custom fields in summary.', 'corpora_theme' ) . '</p>';
  echo '<p>' . __( 'Add images to create portfolio item slider instead of static featured image.', 'corpora_theme' ) . '</p>';
  
  // Use nonce for verification
  echo '<input type="hidden" name="bk_portfolio_meta_box_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
  
  echo '<table id="bk_portfolio_item_settings" class="bk_form_wrap">';
  
  foreach ($bk_portfolio_item_settings as $setting) {
    $value = '';

    if( $setting['type'] == 'gallery' ) {
	  if( $setting['sub_type'] == 'popup_gallery' ) {
		Bk_Popup_Gallery_Manager::getInstance()->restore_gallery_state($post->ID, $setting['id'], $setting['name'], $setting['description'], $setting['sub_type']);
      } elseif ( $setting['sub_type'] == 'featured_gallery' ) {
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
function bk_save_portfolio_custom_metadata( $post_id ) {
  global $bk_portfolio_item_settings;
  
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

  foreach ($bk_portfolio_item_settings as $setting) {
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
add_action( 'init', 'bk_register_portfolio' );
add_action( 'init', 'bk_register_portfolio_taxonomy', 0 );  
add_filter( 'post_updated_messages', 'bk_portfolio_messages' );
add_action( 'add_meta_boxes', 'bk_portfolio_add_metabox' );
add_action( 'save_post', 'bk_save_portfolio_custom_metadata' );
?>