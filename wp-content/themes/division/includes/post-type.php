<?php
/*
 Add custom meta box for images, external links to project and other options
*/
function bk_post_type_add_metabox() {
	add_meta_box(
	'bk_post_type_custom_settings',
	__( 'Post Entry Settings', 'corpora_theme' ),
	'bk_post_type_inner_custom_box',
	'post',
	'normal',
	'high'
			);
}

/* Post type custom settings */
$bk_post_type_item_settings = array(
		array(
				'description' => __( 'Standard & Image Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Post Image Behavior', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_behavior',
				'description' => __( 'Choose how featured image will behave when clicked by user. Choose between fullsize image popup, video popup, post link or none.', 'corpora_theme' ),
				'type' => 'stacked_combobox',
				'default' => 'content_link',
				'all_values' => array(
						"none" => array( "label" => "None", "buddy" => "#buddy_wrap_bk_post_none_desc" ),
						"content_link" => array( "label" => "Content Link", "buddy" => "#buddy_wrap_bk_post_content_link_desc" ),
						"image_popup" => array( "label" => "Image Popup", "buddy" => "#buddy_wrap_bk_post_featured_image_link" ),
						"vimeo_popup" => array( "label" => "Vimeo Popup", "buddy" => "#buddy_wrap_bk_post_featured_image_vimeo_link, #buddy_wrap_bk_popup_video_height, #buddy_wrap_bk_popup_video_width" ),
						"youtube_popup" => array( "label" => "Youtube Popup", "buddy" => "#buddy_wrap_bk_post_featured_image_youtube_link, #buddy_wrap_bk_popup_video_height, #buddy_wrap_bk_popup_video_width" ),
						"gallery_popup" => array( "label" => "Gallery Popup", "buddy" => "#buddy_wrap_bk_post_featured_image_popup_gallery" ),
				)
		),
		array(
				'name' => __( 'Image Link', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_link',
				'description' => __( 'Image Link', 'corpora_theme' ),
				'type' => 'browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Vimeo Link', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_vimeo_link',
				'description' => __( 'Vimeo Link', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Youtube Link', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_youtube_link',
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
				'name' => __( 'Content Link', 'corpora_theme' ),
				'id' => '_bk_post_content_link_desc',
				'description' => __( 'This is additional info for above setting.', 'corpora_theme' ),
				'additional_info' => __( 'Post featuered image will link to post fullsize entry.', 'corpora_theme' ),
				'type' => 'description_field'
		),
		array(
				'name' => __( 'None', 'corpora_theme' ),
				'id' => '_bk_post_none_desc',
				'description' => __( 'This is additional info for above setting.', 'corpora_theme' ),
				'additional_info' => __( 'Nothing will happen when user clicks on featured image.', 'corpora_theme' ),
				'type' => 'description_field'
		),
		array(
				'name' => __( 'Post Popup Gallery', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_popup_gallery',
				'description' => __( 'Use button on the right to add images and vidoes to post popup gallery. Drag and drop to change order of images and videos.', 'corpora_theme' ),
				'type' => 'gallery',
				'sub_type' => 'popup_gallery',
				'default' => 0,
				'images' => array()
		),
		array(
				'name' => __( 'Thumb Effect', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_effect',
				'description' => __( 'Choose optional post featured image effect, it will be visible when user hovers over image.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'bk_zoom',
				'all_values' => array('bk_none' => __( 'None', 'corpora_theme' ),
									  'bk_zoom' => __( 'Zoom Image', 'corpora_theme' ),
									  'bk_grayscale' => __( 'Grayscale', 'corpora_theme' ),
									  'bk_reverse_grayscale' => __( 'Reverse Grayscale', 'corpora_theme' ) ),
		),
		array(
				'name' => __( 'Image Overlay', 'corpora_theme' ),
				'id' => '_bk_post_featured_image_overlay',
				'description' => __( 'Choose optional image overlay, it will be visible when user hovers over image.', 'corpora_theme' ),
				'type' => 'combobox',
				'default' => 'icon',
				'all_values' => array('none' => __( 'None', 'corpora_theme' ),
									  'icon' => __( 'Icon', 'corpora_theme' ) )
		),
		array(
				'description' => __( 'Gallery Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Variable Height', 'corpora_theme' ),
				'id' => '_bk_post_slider_smooth_height_enabled',
				'description' => __( 'If enabled stacked gallery / slider will use images with variable height ( original aspect ratio of images will be preserved ). This will cause slider to change its height acording to currently displayed image. Otherwise all images inside slider / stacked gallery will be cropped and sclaed to use same height.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Autoplay', 'corpora_theme' ),
				'id' => '_bk_post_slider_autoplay_enabled',
				'description' => __( 'If enbled slideshow will start to switch slides automatically otherwise slider navigation will allow to do so.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => true
		),
		array(
			'name' => __( 'Gallery', 'corpora_theme' ),
			'id' => '_bk_post_gallery',
			'description' => __( 'Use button on the right to add images to post gallery. Drag and drop to change order of images. Do not forget to check Format -> Gallery on the right side of page to activate gallery. Use above options to change appearance of gallery.', 'corpora_theme' ),
			'type' => 'gallery',
			'sub_type' => 'post_featured_gallery',	
			'default' => 0,
			'images' => array()
		),
		array(
				'description' => __( 'Video Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Video Type', 'corpora_theme' ),
				'id' => '_bk_post_video_type',
				'description' => __( 'Choose type of video used for post featured content it can be Youtube, Vimeo or self hosted video.', 'corpora_theme' ),
				'type' => 'stacked_combobox',
				'default' => 'vimeo_link',
				'all_values' => array(	"vimeo_link" => array( "label" => "Vimeo Link", "buddy" => "#buddy_wrap_bk_post_vimeo_link" ),
										"youtube_link" => array( "label" => "Youtube Link", "buddy" => "#buddy_wrap_bk_post_youtube_link" ),
										"self_hosted" => array( "label" => "Self Hosted Video", "buddy" => "#buddy_wrap_bk_post_self_hosted_m4v_link, #buddy_wrap_bk_post_self_hosted_ogv_link, #buddy_wrap_bk_post_self_hosted_video_poster" )
				)
		),
		array(
				'name' => __( 'Vimeo Link', 'corpora_theme' ),
				'id' => '_bk_post_vimeo_link',
				'description' => __( 'Link to Vimeo video', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Youtube Link', 'corpora_theme' ),
				'id' => '_bk_post_youtube_link',
				'description' => __( 'Link to Youtube video', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Self Hosted Video (*.m4v)', 'corpora_theme' ),
				'id' => '_bk_post_self_hosted_m4v_link',
				'description' => __( 'Link to selfhosted (*.m4v) video. This is primary video. It is recomended to specify also additonal link to the same vidoe in another format (*.ogv) to make player work on any device if (*.m4v) fails to load.', 'corpora_theme' ),
				'type' => 'simple_browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Self Hosted Video (*.ogv)', 'corpora_theme' ),
				'id' => '_bk_post_self_hosted_ogv_link',
				'description' => __( 'Additional (*.ogv) format of video. This is secondary video link. Note! it is required to specify (*.m4v) first to make player work. If You specify only link to (*.ogv) file it may not work in all browsers.', 'corpora_theme' ),
				'type' => 'simple_browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Video Poster', 'corpora_theme' ),
				'id' => '_bk_post_self_hosted_video_poster',
				'description' => __( 'This image will be used as poster for player filling background before user hit play button.', 'corpora_theme' ),
				'type' => 'browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Video Width', 'corpora_theme' ),
				'id' => '_bk_post_video_width',
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
				'id' => '_bk_post_video_height',
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
				'type' => 'separator'
		),
		array(
				'name' => __( 'Audio Type', 'corpora_theme' ),
				'id' => '_bk_post_audio_type',
				'description' => __( 'Choose type of audio that will be used as post featured content, it can be selfhosted *.mp3 file or link to soundcloud track set or group.', 'corpora_theme' ),
				'type' => 'stacked_combobox',
				'default' => 'selfhosted_audio',
				'all_values' => array(	"selfhosted_audio" => array( "label" => "Selfhosted Audio", "buddy" => "#buddy_wrap_bk_post_audio_link, #buddy_wrap_bk_post_audio_poster" ),
										"soundcloud_audio" => array( "label" => "SoundCloud Audio", "buddy" => "#buddy_wrap_bk_post_audio_soundcloud_link, #buddy_wrap_bk_post_audio_soundcloud_comments_enabled, #buddy_wrap_bk_post_audio_soundcloud_show_artwork, #buddy_wrap_bk_post_audio_soundcloud_color, #buddy_wrap_bk_post_audio_soundcloud_auto_play" )
				)
		),
		array(
				'name' => __( 'SoundCloud Link', 'corpora_theme' ),
				'id' => '_bk_post_audio_soundcloud_link',
				'description' => __( 'Specify link to soundcloud track, set or group.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Auto Play', 'corpora_theme' ),
				'id' => '_bk_post_audio_soundcloud_auto_play',
				'description' => __( 'If enabled soundloud widget will start playback once loaded.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Show Comments', 'corpora_theme' ),
				'id' => '_bk_post_audio_soundcloud_comments_enabled',
				'description' => __( 'If enabled soundloud widget will show users comments about the track.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Show Artwork', 'corpora_theme' ),
				'id' => '_bk_post_audio_soundcloud_show_artwork',
				'description' => __( 'If enabled soundloud widget will show users artwork ( logo / avatar ) at the left top corner.', 'corpora_theme' ),
				'type' => 'checkbox',
				'default' => false
		),
		array(
				'name' => __( 'Color', 'corpora_theme' ),
				'id' => '_bk_post_audio_soundcloud_color',
				'description' => __( 'Choose soundcloud widget theme color - play/pause button and playlist selections. Default soundcloud color is #ff6600', 'corpora_theme' ),
				'type' => 'simple_colorpicker',
				'default' => "#ff6600"
		),
		array(
				'name' => __( 'Audio File (*.mp3)', 'corpora_theme' ),
				'id' => '_bk_post_audio_link',
				'description' => __( 'Specify link to audio (*.mp3) file that You want to be post featured content.', 'corpora_theme' ),
				'type' => 'simple_browse_button_with_label',
				'default' => ''
		),
		array(
				'name' => __( 'Audio Poster', 'corpora_theme' ),
				'id' => '_bk_post_audio_poster',
				'description' => __( 'This image will be used as poster for audio file.', 'corpora_theme' ),
				'type' => 'browse_button_with_label',
				'default' => ''
		),
		array(
				'description' => __( 'Link Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Primary Text', 'corpora_theme' ),
				'id' => '_bk_post_link_primary_text',
				'description' => __( 'Specify text that will show as primary featured content link text. Use descriptive text e.g "Awesome wordpress themes"', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Secondary Text', 'corpora_theme' ),
				'id' => '_bk_post_link_secondary_text',
				'description' => __( 'Specify text that will show as secondary featured content link text. This will show as small tagline it can contain for example name of website like "Envato".', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'name' => __( 'Link', 'corpora_theme' ),
				'id' => '_bk_post_link_href',
				'description' => __( 'Specify link that will be wrapped around above primary and secondary link text.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'description' => __( 'Quote Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Quoted Text', 'corpora_theme' ),
				'id' => '_bk_post_quote_text',
				'description' => __( 'Specify quoted text.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' =>  ''
		),
		array(
				'name' => __( 'Author', 'corpora_theme' ),
				'id' => '_bk_post_quote_author',
				'description' => __( 'Specify author of quoted text.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'description' => __( 'Aside Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Content', 'corpora_theme' ),
				'id' => '_bk_post_aside_content_desc',
				'description' => __( 'This is additional info for aside post format.', 'corpora_theme' ),
				'additional_info' => __( 'Use main post content editor for aside note and it will be displayed as excerpt in Your blog.', 'corpora_theme' ),
				'type' => 'description_field'
		),
		array(
				'description' => __( 'Status (Twitter) Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Link', 'corpora_theme' ),
				'id' => '_bk_post_status_link',
				'description' => __( 'Specify link to tweet that You want to embedd e.g https://twitter.com/BillyKidLab/status/253745094706999296.', 'corpora_theme' ),
				'type' => 'textinput',
				'default' => ''
		),
		array(
				'description' => __( 'Chat Format Settings', 'corpora_theme' ),
				'type' => 'separator'
		),
		array(
				'name' => __( 'Chat Text', 'corpora_theme' ),
				'id' => '_bk_post_chat_text',
				'description' => __( 'Specify record of chat. Each paragraph can be used as separate chat sentence ( paragraph html tags ). You can use bold text to indicate persons nicknames ( strong html tag ). One line can look like this &lt;p&gt;&lt;strong&gt;Bob:&lt;/strong&gt;Lorem ispsum&lt;/p&gt;', 'corpora_theme' ),
				'type' => 'rich_textarea',
				'default' =>  ''
		)
);

/* Prints the box content */
function bk_post_type_inner_custom_box( $post ) {
	global $bk_post_type_item_settings;

	echo '<div class="bk_custom_settings_wrap">';
	echo '<p>' . __( 'Each section below corresponds to post format setting on the right side.', 'corpora_theme' ) . '</p>';

	// Use nonce for verification
	echo '<input type="hidden" name="bk_post_meta_box_nounce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

	echo '<table id="bk_portfolio_item_settings" class="bk_form_wrap">';

	foreach ( $bk_post_type_item_settings as $setting) {
		$value = '';

		if( $setting['type'] == 'gallery' ) {
			if( $setting['sub_type'] == 'popup_gallery' ) {
				Bk_Popup_Gallery_Manager::getInstance()->restore_gallery_state($post->ID, $setting['id'], $setting['name'], $setting['description'], $setting['sub_type']);
			} elseif ( $setting['sub_type'] == 'post_featured_gallery' ) {
				Bk_Post_Featured_Gallery_Manager::getInstance()->restore_gallery_state($post->ID, $setting['id'], $setting['name'], $setting['description'], $setting['sub_type']);
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
function bk_save_post_type_custom_metadata( $post_id ) {
	global $bk_post_type_item_settings;

	// verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times

	if ( !isset($_POST['bk_post_meta_box_nounce']) || !wp_verify_nonce( $_POST['bk_post_meta_box_nounce'], basename(__FILE__) ) )
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

	foreach ($bk_post_type_item_settings as $setting) {
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
			} elseif ( $setting['sub_type'] == 'post_featured_gallery' ) {
				Bk_Post_Featured_Gallery_Manager::getInstance()->save_gallery_state($post_id, $setting['id']);
			}
		}

		update_post_meta($post_id, $setting['id'], $new_value);
	}
}

add_action( 'add_meta_boxes', 'bk_post_type_add_metabox' );
add_action( 'save_post', 'bk_save_post_type_custom_metadata' );
?>