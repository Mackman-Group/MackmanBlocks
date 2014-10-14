<?php
/*
 * Bk_Gallery_Manager base class
 * 
 * Responsible for managing galleries that can be attached to any post, page or portfolio
 * Saves and restores backend, prints out proper editors for thumbs ( image, selfhosted video or youtube vimeo link )
 * 
 */

class Bk_Gallery_Manager {
	
	protected $items;			// all items (thumbs) available in gallery manager
	protected $editors_out; 	// flag indicating that editors were already printed - in case if more than one gallery will be used on oe page
	protected $gallery_subtype; // subtype of gallery e.g 'popup_gallery' or 'featured_gallery' used to distinguish different editors with different options
	
	/*
	 * Hanlde gallery add button
	 * 
	 * This function takes as an argument url specified by user and 
	 * outputs thumb according to its type image or video
	 * 
	 */
	
	public function bk_handle_gallery_upload_button_ajax() {
		$url = $_POST['url'];
		$id = $_POST['id'];
		$count = intval($_POST['count']);
		
		$link_type = $this->get_link_type($url);
		
		if( $link_type ) {  // if media type was found
			$data['success'] = true;
			$data['markup'] = $this->get_common_item_markup( $link_type, $id, $count );
			$data['markup'] .= $this->get_item_thumb_markup( $link_type, $url, $id, $count );
			
			$fields = $this->items[$link_type]['fields'];
			
			if( $link_type == 'bk_gallery_video' || 
				$link_type == 'bk_gallery_selfhosted_video' ) {	// if it is video type / vimeo, youtube or self hosted add link as default value
				$fields[0]['default'] = $url;
			} 
			
			$data['markup'] .= $this->get_gallery_item_fields($fields, $count, $id, $link_type);
		} else {
			$data = array( 'success' => false, 'error' => __( 'Unknow media type. Make sure that vimeo/youtube link is correct. Please note that popup gallery only supports *.mp4 video files. Featured Gallery supports only *.mp4, *.m4v, *.ogv, *.webm video files.', 'corpora_theme' ) );
		}
		
		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);
	
		exit;
	}
	
	/*
	 * Save gallery state - saves gallery for given post and gallery id
	 * 
	 * @params: (integer) - $post_id - id of post for which gallery should be saved e.g 4010
	 *          (string) - $gallery_id - id of gallery that should be saved - e.g '_bk_portfolio_gallery'
	 */
	
	public function save_gallery_state( $post_id, $gallery_id ) {
		$current_items_count = get_post_meta($post_id, $gallery_id . '_images_count', true);   // get current items count
		if( empty($current_items_count) )
			$current_items_count = 0;
			
		// get all modules id into flat array
		$item_ids = array_keys($this->items);
		
		//Pre save cleanup
		for($i = 0; $i < $current_items_count; $i++) { // loop through all items that are saved now
			delete_post_meta($post_id, $gallery_id . '_image_' . $i);  // delete image if any
			delete_post_meta($post_id, $gallery_id . '_thumb_' . $i);  // delete thumb if any
			
			foreach($item_ids as $item_id) {  // loop through all modules
				$data_core_names = $this->get_data_core_item_names($item_id, $gallery_id); // get core names of each module
				foreach( $data_core_names as $data_core_name ) {
					delete_post_meta($post_id, $data_core_name . $i); // remove all data e.g _bk_portfolio_gallery_image_description__5 for $i = 5
				}
			}
		}
		
		$gallery_images_count = intval($_POST[ $gallery_id . '_images_count' ]);	// number of images inside gallery
		update_post_meta($post_id, $gallery_id . '_images_count', $gallery_images_count);
		
		//Loop through all images inside gallery and save data properly
		for( $i = 0; $i < $gallery_images_count; $i++ ) {
			$item_type = $_POST[$gallery_id . '_item_type_' . $i];  // get common value - item type e.g 'bk_gallery_image'

			//save common values - each block have those values set
			update_post_meta($post_id, $gallery_id . '_item_type_' . $i , $item_type); // save common value
			if( $item_type == 'bk_gallery_image' ) {  // for images we have to additionally save choosen image and thumb
				$image = $_POST[$gallery_id . '_image_' . $i];  // get url of image
				$thumb = $_POST[$gallery_id . '_thumb_' . $i];  // get url of thumb
				
				update_post_meta($post_id, $gallery_id . '_image_' . $i , $image); // save image
				update_post_meta($post_id, $gallery_id . '_thumb_' . $i , $thumb); // save thumb
			}
			
			//save item specific fields
			$data_core_names = $this->get_data_core_item_names($item_type, $gallery_id); // get core names
			foreach( $data_core_names as $data_core_name ) {
				update_post_meta($post_id, $data_core_name . $i , $_POST[$data_core_name . $i]);
			}
		}
	}
	
	/*
	 * Helper function - gets all data-core fields names for given item(module) id and gallery_id
	 * used in save and load pagebuilder functions
	 *
	 * @param - (string) $item_id - id of item module to query e.g bk_gallery_image
	 * 			(string) $gallery_id - id of gallery that was inserted into post e.g _bk_portfolio_gallery
	 *
	 * @return - (array) - array which contains all data-core ids / names of module for given e.g ['_bk_portfolio_gallery_image_description', ''_bk_portfolio_gallery_link_type', ...]
	 */
	
	protected function get_data_core_item_names($item_id, $gallery_id) {
		$data_core_names = array();
	
		if( isset($this->items[$item_id]) ) { // check if given id exists
			foreach( $this->items[$item_id]['fields'] as $field ) {	// loop through all fields
				if( isset($field['id']) ) { // check if field have id
					$data_core_names[] = $this->get_data_core_name($gallery_id, $field['id']);
				}
			}
		}
	
		return $data_core_names;
	}
	
	/*
	 * Helper function creates single data-core setting name e.g _bk_portfolio_gallery_image_description_
	*
	* @param (string) $item_id - unique id of gallery e.g _bk_portfolio_gallery
	* @param (string) $field_id - unique id of field e.g image_description
	*
	* @return (string) - data core name e.g _bk_portfolio_gallery_image_description_
	*
	*/
	
	protected function get_data_core_name($gallery_id, $field_id) {
		return  $gallery_id . '_' . $field_id . '_';
	}
	
	/*
	 * Get gallery items - this function loads data for given post id and gallery id and returns array with all items data
	 * 
	 * @param (integer) - $post_id - id of post for which gallery data should be loaded e.g 4000
	 * @param (string) - $gallery_id - id of gallery that should be loaded - e.g '_bk_portfolio_gallery'
	 * @param (string) - $thumbnail_size - id of desired thumbnail size - e.g 'thumbnail'
	 * 
	 * @return (array) - with all item data for given gallery in format
	 * 
	 * array(
	 *   array( 'item_type' => 'bk_gallery_image'
	 *   							'image_url' => 'path/to/fullsize/image',
	 *   							'thumb_url' => 'path/to/image/thumb',
	 *   	   	 					'image_description'	=> image deciprtion,
	 *   							'link_type'	=> ....
	 *   
	 *   							REST OF FIELDS DEFINED IN ITEMS ARRAY FOR GIVEN TYPE
	 *   				
	 *   ),
	 *   array( 'item_type' => 'bk_gallery_video'
	 *   							'video_link' => 'http://youtube.com/video=989348asds or http://vimeo.com/video/3345345345'
	 *          					'thumb_url' => 'path/to/video/thumb/if/any or blank if auto thumbnails will be used'
	 *          					'video_description' => 'description of vidoe'
	 *          
	 *          					.....
	 *          					.....
	 *          					....
	 *          
	 *          					REST OF FIELDS DEFINED IN ITEMS ARRAY FOR GIVEN TYPE
	 *   )
	 * )
	 */
	
	public function get_gallery_items( $post_id, $gallery_id, $thumbnail_size ) {
		$gallery_images_count = get_post_meta($post_id, $gallery_id . '_images_count', true);  // get stored value - see how many gallery items we have
		if( empty( $gallery_images_count ) ) {  // if no setting found make it zero
			$gallery_images_count = 0;
		}
		
		$items = array();
		
		for( $i = 0; $i < $gallery_images_count; $i++ ) {
			$item = array();
			$item_type = get_post_meta($post_id, $gallery_id . '_item_type_' . $i, true); // get common value - item type e.g 'bk_gallery_image'
				
			if( isset($this->items[$item_type]) ) { // check if given id exists
				foreach( $this->items[$item_type]['fields'] as $field ) {	// loop through all fields
					if( isset($field['id']) ) {
						$this_field_id = $field['id'];
						$this_data_core_name = $this->get_data_core_name($gallery_id, $this_field_id);
						$field_value = trim(get_post_meta($post_id, $this_data_core_name . $i, true));
						
						$item[$this_field_id] = $field_value; // get field value
						
						if( $item_type == 'bk_gallery_video' && $this_field_id == 'video_thumbnail' && !empty($field_value) ) { // if it is video, try to get custom video thumbnail image
							$item[$this_field_id] = $this->get_other_size_image_url($field_value, $thumbnail_size);
						}
					}
				} // link_type, video_thumbnail
				
				if( $item_type == 'bk_gallery_image' ) {  // for images we have to additionally get thumb and image
					$image_url = get_post_meta($post_id, $gallery_id . '_image_' . $i , true); // get image
					$thumb_url = $this->get_other_size_image_url($image_url, $thumbnail_size);
					
					$item['image_url'] = $image_url;
					$item['thumb_url'] = $thumb_url;
				}
				
				$item['item_type'] = $item_type;
					
				$items[] = $item;
			}		
				
		}

		return $items;
	}
	
	/*
	 * Helper function - get other attachement image size src form given parameters or original url if not found
	 * 
	 * @param (string) - $url - path/to/hopefully/fullsize/image
	 * 		  (string) - $image_size - desired size e.g thumbnail
	 * 
	 * @return - thumbnail image src on success or $url on failure
	 * 
	 */
	
	public function get_other_size_image_url( $url, $image_size ) {
		$img_id = bk_get_image_id($url);
		$image = wp_get_attachment_image_src( $img_id, $image_size );
		if( $image ) { // if we have attachement image - in case if new media uploader was used WP 3.5+ or in old media uploader fullsize image was used
			return $image[0];
		}
		
		return $url;
	}
	
	/*
	 * Restore gallery state -  this function prints out gallery setting field as well as all saved data for given post and gallery id
	 * 
	 * @params:  (integer) - $post_id - id of post for which gallery should be saved e.g 4010
	 *           (string) - $gallery_id - id of gallery that should be saved - e.g '_bk_portfolio_gallery'
	 *           (string) - $name - name of setting field
	 *           (string) - $description - description of field
	 *           (string) - $sub_type - subtype of gallery -> depending on subtype different superclass handles gallery thus allowing different types of fields
	 */
	
	public function restore_gallery_state( $post_id, $gallery_id, $name, $description, $sub_type ) {
		
		$gallery_images_count = get_post_meta($post_id, $gallery_id . '_images_count', true);  // get stored value - see how many gallery items we have
		if( empty( $gallery_images_count ) ) {  // if no setting found make it zero
			$gallery_images_count = 0;
		}
		
		$label = __( 'Add Images & Selfhosted Video', 'corpora_theme' );
		
		if( $sub_type == 'fullscreen_gallery' ) {
			$label = __( 'Add Images', 'corpora_theme' );
		}
		
		echo '<tr class="bk-gallery-manager ' . $sub_type . '" id="buddy_wrap' . $gallery_id . '">';
		echo '<td><label for="' . $gallery_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		echo '<td><input type="hidden" class="bk_gallery_images_count" data-core-field-id="' . $gallery_id . '" id="' . $gallery_id . '_images_count" name="' . $gallery_id . '_images_count" value="' . $gallery_images_count . '"/>';
		echo '<a data-gallery_subtype="' . $sub_type . '" class="button bk_browse_button gallery">' .  $label  . '</a>';
		echo '<a data-gallery_subtype="' . $sub_type . '" class="button bk_browse_button gallery insert_video">' .  __( 'Add Youtube/Vimeo Video', 'corpora_theme' )  . '</a>';
		echo '<div class="clearfix"></div>';
		echo '<div class="bk_gallery_placeholder">';
			
		for( $i = 0; $i < $gallery_images_count; $i++ ) {
			$item_type = get_post_meta($post_id, $gallery_id . '_item_type_' . $i, true); // get common value - item type e.g 'bk_gallery_image'
			
			echo $this->get_common_item_markup( $item_type, $gallery_id, $i );
			
			if( $item_type == 'bk_gallery_image' ) {  // for images we have to additionally get thumb and image
				$image_src = get_post_meta($post_id, $gallery_id . '_image_' . $i , true); // get image
				$thumb_src = get_post_meta($post_id, $gallery_id . '_thumb_' . $i , true); // get thumb
				
				echo $this->get_image_type_thumb_markup( $image_src, $thumb_src, $gallery_id, $i );
			} else if( $item_type == 'bk_gallery_video' || $item_type == 'bk_gallery_selfhosted_video' ) {
				$video_link = get_post_meta($post_id, $gallery_id . '_video_link_' . $i , true);
				
				echo $this->get_item_thumb_markup( $item_type, $video_link, $gallery_id, $i );
			}
			
			$fields = array();
			
			foreach( $this->items[$item_type]['fields'] as $field ) {
				//id, type, default
				$this_field = array();
				$this_field['id'] = $field['id'];
				$this_field['type'] = $field['type'];
					
				$this_data_core_name = $this->get_data_core_name($gallery_id, $field['id']);
				$this_field['default'] = get_post_meta($post_id, $this_data_core_name . $i, true);
					
				$fields[] = $this_field;
			}
			
			echo $this->get_gallery_item_fields($fields, $i, $gallery_id, $item_type);
		}	
			
		echo '</div>';
		echo '<div class="clearfix"></div>';
		echo '</td>';
		echo '</tr>';
	}
	
	/*
	 * Get common item markup like wrap and buttons to remove and edit item
	 * 
	 * dialog_id -> $link_type ( bk_gallery_image ) + "_editor_" + gallery subtype(popup_gallery) => 'bk_gallery_image_editor_popup_gallery'
	 * used by jquery logic to call proper dialog for given item
	 * 
	 */
	
	protected function get_common_item_markup( $link_type, $id, $count ) {
		$dialog_id = $link_type . '_editor_' . $this->gallery_subtype;
		
		$markup = '<div class="bk_gallery_item"><a class="bk_remove_gallery_image_button" title="Remove"></a><a class="bk_edit_gallery_image_button" title="Edit"></a>';
		$markup .= '<input data-dialog_id="' . $dialog_id . '" data-core="' . $id . '_item_type" type="hidden" name="' . $id . '_item_type_' . $count . '" value="' . $link_type . '">';
		return $markup;
	}
	
	/*
	 * Get item thumb markup - outputs markup with proper thumb for images or icon ( vimeo, youtube, video ) for other
	 * 
	 * @params: (string) - $url - url of item
	 * 			(string) - $link_type - type of link it can be 
	 * 			(string) - $id - id of setting e.g _bk_portfolio_gallery
	 * 			(integer) - $count - suffix that will be added to item name _bk_portfolio_gallery_image_5 for coutn = 5
	 * 
	 * @return: (string) - HTML markup	
	 */
	protected function get_item_thumb_markup( $link_type, $url, $id, $count ) {
		$markup = '';
		switch( $link_type ) {
			case 'bk_gallery_image':
				$img_id = bk_get_image_id($url);
				$thumb = wp_get_attachment_image_src( $img_id, 'thumbnail' );
				$thumb_src = $url;  //default to url if thumb will not be found
				$large_image_src = $url;	//default to whatever user choosed
				if( $thumb ) { // if we have attachement image - in case if new media uploader was used WP 3.5+ or in old media uploader fullsize image was used
					$large_image = wp_get_attachment_image_src( $img_id, 'fullsize' );
					$thumb_src = $thumb[0];
					$large_image_src = $large_image[0] ;
				}	
				
				$markup = $this->get_image_type_thumb_markup( $large_image_src, $thumb_src, $id, $count );
				
			break;
			
			case 'bk_gallery_video':
				$video_subtype = 'youtube';
				if( preg_match ("/\b(?:vimeo)\.com\b/i", $url) ) {
					$video_subtype = 'vimeo';
				}
				
				$markup = '<div class="bk_gallery_item_thumb bk_image_preview ' . $video_subtype . '"></div>';
				
			break;

			case 'bk_gallery_selfhosted_video':
				$markup = '<div class="bk_gallery_item_thumb bk_image_preview bk_selfhosted_video"></div>';
			break;	
		}
		
		return $markup;
	}
	
	/*
	 * Helper function -> prints gallery image thumb and corresponding hidden fields for image and thumb
	 * 
	 */
	
	protected function get_image_type_thumb_markup( $large_image_src, $thumb_src, $id, $count ) {
		$markup = '<img class="bk_gallery_item_thumb bk_image_preview" src="' . $thumb_src . '">';
		$markup .= '<input data-core="' . $id . '_image" type="hidden" name="' . $id . '_image_' . $count . '" value="' . $large_image_src . '">';
		$markup .= '<input data-core="' . $id . '_thumb" type="hidden" name="' . $id . '_thumb_' . $count . '" value="' . $thumb_src . '">';
		
		return $markup;
	}
	
	/*
	 * Get gallery items fields
	 * 
	 * This function creates fields for desired module e.g 'bk_gallery_image' described in $this->items array under uniqe key
	 * e.g for gallery image module item_id is 'bk_gallery_image' so this function will loop through all fields described in
	 * $this->items['bk_gallery_image']['fields'] array and will create proper input ( data placeholders that can be changed via editor )
	 * 
	 * @param - (array) $fields - subarray taken from global settings that describes all fields required by this module
	 * @param - (integer) $index - index ( position ) of item which is used to create unique input names e.g
	 * 			bk_portfolio_gallery_image_link_type_5 - index is 5
	 * @param - (string) $item_id - unique id of item e.g 'bk_portfolio_gallery'
	 * @param - (string) $item_type - type of item for which fields are taken e.g 'bk_gallery_image' used to generate proper editor id
	 * 
	 * @return - (string) $markup - HTML markup of all generated fields
	 * 
	 */
	
	protected function get_gallery_item_fields($fields, $index, $item_id, $item_type) {
		$markup = '';
		
		foreach($fields as $field) {
			$field_id = $field['id']; // id of filed e.g 'link_type'
			$data_core = $item_id . '_' . $field_id; // core unique input name e.g '_bk_portfolio_gallery_link_type' used by jQuery logic
			$name = $data_core . '_' . $index; // name of html input, textarea element e.g '_bk_portfolio_gallery_link_type_5'
			$data_editor_id = '_' . $item_type . '_' . $field_id . '_editor_' . $this->gallery_subtype; // id of editor for this filed e.g '_bk_gallery_image_link_type_editor_popup_gallery'
			$data_field_type = $field['type'];
			$value = $field['default'];
			
			if( is_array($value) ) {  // if value is array e.g multi_select filed then turn into string
				$value = join(',', $value);
			}
			
			$markup .= '<input type="hidden" data-field-type="' . $data_field_type . '" data-editor-id="' . $data_editor_id . '" data-core="' . $data_core . '" name="' . $name . '" value="' . $value . '">';

		}
		
		$markup .= '</div>';
		return $markup;
	}
	
	/*
	 * Get link type 
	 * 
	 * this function takes as argument single url and return it's type - it can be image, selfhosted video or youtube / vimeo video
	 * @params: (string) $url - link to image, video or youtube / vimeo
	 * returns: (mixed) - string that describes media type if found or false if unknown format
	 * 
	 * 
	 */
	
	protected function get_link_type( $url ) {
		if( preg_match ("/\b(?:vimeo|youtube)\.com\b/i", $url) ) {
			return 'bk_gallery_video';
		} else if( preg_match("/\b\.(jpg|jpeg|png|bmp|gif)\b/i", $url) ) {
			return 'bk_gallery_image';
		} else if( preg_match("/\b\.(m4v|ogv)\b/i", $url) ) {
			return 'bk_gallery_selfhosted_video';
		} else {
			return false;
		}
	}
	
	/*
	 * Prints 
	 * 
	 */
	public function print_gallery_manager_editors() {
	
		if( $this->editors_out ) { // if editors out then return
			//return;
		}
		
		echo '<div class="hiddenn">'; // wrap it up and hide
	
		//var_dump($this->items);
	
		foreach( $this->items as $item_id => $item) {  // loop through all page builder items
			$editor_id = $item_id . '_editor_' . $this->gallery_subtype; // create unique editor id
				
			echo '<div id="' . $editor_id . '" class="bk-gallery-item-editor" title="'. $item['name'] .'">';
			echo '<table class="bk_form_wrap">';
				
			foreach( $item['fields'] as $item_field ) {
	
				/*
				*  create prefix for page builder editor filed e.g portolio module have id bk_portfolio
				*  prefix will be in this case _bk_portfolio_ and will be prepended to all setting fields creating unique
				*  fields id e.g _bk_portfolio_show_layout_switcher_editor - _editor is $suffix
				*/
				$prefix = '_' . $item_id . '_';
				$suffix = '_editor_' . $this->gallery_subtype;
	
				Bk_Field_Factory::getInstance()->print_setting_field($item_field, $item_field['default'], $prefix, $suffix);
			}
				
			echo '</table>';
			echo '</div>';
		}
	
		echo '</div>'; // end of wrap
		
		$this->editors_out = true;
	}
}


/*
 * Singleton class based on Bk_Gallery_Manager class
 * 
 */
class Bk_Popup_Gallery_Manager extends Bk_Gallery_Manager {
	private static $instance;  // reference to singleton class
	
	/*
	 * Get instance of singleton Bk_Gallery_Manager class
	*
	* this method is used to get reference to singleton and call other functions
	* if it's being called first time during application it creates new instance of
	* Bk_Gallery_Manager class
	* usage Bk_Gallery_Manager::getInstance()->somefunction();
	*
	* @param - void
	* @return - (Bk_Gallery_Manager class) instance
	*
	*/
	
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/*
	 * Class Constructor
	*
	* $items contains all gallery manager blocks along with descriptions, filed type etc..
	* it is used to generate proper editors for gallery thumb blocks as blocks itself via ajax call from admin
	*
	* id of each module corresponds to thumb type that will be handled e.g bk_gallery_image, bk_gallery_video
	* each editor field in module will have uniqe id e.g bk_gallery_image_link_type_editor
	*
	* @params - void
	* @return - void
	*
	*/
	
	private function __construct() {
		$this->editors_out = false;
		$this->gallery_subtype = 'popup_gallery';
		
		$this->items = array(
				'bk_gallery_image' => array(
						'name' => __('Image', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Image Description', 'corpora_theme' ),
										'id' => 'image_description',
										'description' => __( 'Place description that will appear under image when user clicks on it.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								)
						)
				),
				'bk_gallery_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Youtube / Vimeo Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Place here link to Youtube or Vimeo that will be displayed inside gallery.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Autoplay', 'corpora_theme' ),
										'id' => 'video_autoplay',
										'description' => __( 'If enabled video will start playback once visible.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false
								),
								array(
										'name' => __( 'Video Description', 'corpora_theme' ),
										'id' => 'video_description',
										'description' => __( 'Place description that will appear under video when user clicks on thumb.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Thumbnail', 'corpora_theme' ),
										'id' => 'video_thumbnail',
										'description' => __( 'Select video thumbnail that will appear in popup gallery for this video. If no image will be selected then thumbnails will be generated automaticaly.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				),
				'bk_gallery_selfhosted_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Video Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Choose *.mp4 video file that will be displayed in popup gallery. To provide full cross browser support it is necessary that *.webm version of this video will be placed with the same name( e.g .../video.mp4 -> .../video.webm ) in the same media directory (it is good idea to upload both files at the same time to make sure that wordpress will place them in same directory) ', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Autoplay', 'corpora_theme' ),
										'id' => 'video_autoplay',
										'description' => __( 'If enabled video will start playback once visible.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false
								),
								array(
										'name' => __( 'Video Description', 'corpora_theme' ),
										'id' => 'video_description',
										'description' => __( 'Place description that will appear under video when user clicks on thumb.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Thumbnail', 'corpora_theme' ),
										'id' => 'video_thumbnail',
										'description' => __( 'Select video thumbnail that will appear in popup gallery for this video. If no image will be selected then default thumbnail will be used.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Poster', 'corpora_theme' ),
										'id' => 'video_poster',
										'description' => __( 'Poster will be applied for mobile devices that do not support autoplay and it is not possible to grab first frame of video.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				)
	
		);
	}
	
	/*
	 * Get link type
	*
	* this function takes as argument single url and return it's type - it can be image, selfhosted video or youtube / vimeo video
	* @params: (string) $url - link to image, video or youtube / vimeo
	* returns: (mixed) - string that describes media type if found or false if unknown format
	*
	*
	*/
	
	protected function get_link_type( $url ) {
		if( preg_match ("/\b(?:vimeo|youtube)\.com\b/i", $url) ) {
			return 'bk_gallery_video';
		} else if( preg_match("/\b\.(jpg|jpeg|png|bmp|gif)\b/i", $url) ) {
			return 'bk_gallery_image';
		} else if( preg_match("/\b\.(mp4)\b/i", $url) ) {
			return 'bk_gallery_selfhosted_video';
		} else {
			return false;
		}
	}
}

/*
 * Featured gallery manager - handles galleries that are featured for given portfolio
 * 
 */
class Bk_Featured_Gallery_Manager extends Bk_Gallery_Manager {
	private static $instance;  // reference to singleton class
	
	/*
	 * Get instance of singleton Bk_Gallery_Manager class
	*
	* this method is used to get reference to singleton and call other functions
	* if it's being called first time during application it creates new instance of
	* Bk_Gallery_Manager class
	* usage Bk_Gallery_Manager::getInstance()->somefunction();
	*
	* @param - void
	* @return - (Bk_Gallery_Manager class) instance
	*
	*/

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 * Class Constructor
	*
	* $items contains all gallery manager blocks along with descriptions, filed type etc..
	* it is used to generate proper editors for gallery thumb blocks as blocks itself via ajax call from admin
	*
	* id of each module corresponds to thumb type that will be handled e.g bk_gallery_image, bk_gallery_video
	* each editor field in module will have uniqe id e.g bk_gallery_image_link_type_editor
	*
	* @params - void
	* @return - void
	*
	*/

	private function __construct() {
		$this->editors_out = false;
		$this->gallery_subtype = 'featured_gallery';

		$this->items = array(
				'bk_gallery_image' => array(
						'name' => __('Image', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Gallery "METRO" Thumbnail Size', 'corpora_theme' ),
										'id' => 'thumbnail_size',
										'description' => __( 'Choose size of thumbnail used for this image while displayed inside "METRO" gallery style layout.', 'corpora_theme' ),
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
										'id' => 'mixed_masonry_thumbnail_size',
										'description' => __( 'Choose size of thumbnail used for this portfolio entry while displayed inside "MIXED MASONRY" gallery style layout', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'small',
										'all_values' => array(	"small" => "Small",
																"large" => "Large",
																//"x-large" => "Extra Large"
										)
								),
								array(
										'name' => __( 'Image Description', 'corpora_theme' ),
										'id' => 'image_description',
										'description' => __( 'Place description that will appear under image when user clicks on it.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array( 'name' => __( 'Image Link', 'corpora_theme' ),
										'id' => 'link_type',
										'description' => __( 'Choose link type for this image that will be opened in popup if user cliks on it.', 'corpora_theme' ),
										'type' => 'stacked_combobox',
										'default' => 'fullsize_image',
										'all_values' => array(	"none" => array( "label" => "None", "buddy" => "" ),
																"fullsize_image" => array( "label" => "Fullsize Image", "buddy" => "" ),
																"other_image" => array( "label" => "Other Image", "buddy" => "#buddy_wrap_bk_gallery_image_other_image_link_editor_featured_gallery" ),
																"video" => array( "label" => "Video", "buddy" => "#buddy_wrap_bk_gallery_image_video_link_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_width_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_height_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_autoplay_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_thumbnail_editor_featured_gallery" ),
																"selfhosted_video" => array( "label" => "Selfhosted Video", "buddy" => "#buddy_wrap_bk_gallery_image_selfhosted_video_link_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_width_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_height_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_autoplay_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_thumbnail_editor_featured_gallery, #buddy_wrap_bk_gallery_image_video_poster_editor_featured_gallery" ),
																"external_link" => array( "label" => "External Link", "buddy" => "#buddy_wrap_bk_gallery_image_external_link_editor_featured_gallery" )	
												
										)
								),
								array(
										'name' => __( 'Other Image Link', 'corpora_theme' ),
										'id' => 'other_image_link',
										'description' => __( 'Select which image will be opened in popup when user clicks on thumb.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Youtube / Vimeo Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Place here link to Youtube or Vimeo that will be opened in popup.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'External Link', 'corpora_theme' ),
										'id' => 'external_link',
										'description' => __( 'Place here link to extrenal site e.g "http://www.google.com" .', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Video Link', 'corpora_theme' ),
										'id' => 'selfhosted_video_link',
										'description' => __( 'Choose *.mp4 video file that will be displayed in popup gallery. To provide full cross browser support it is necessary that *.webm version of this video will be placed with the same name( e.g .../video.mp4 -> .../video.webm ) in the same media directory (it is good idea to upload both files at the same time to make sure that wordpress will place them in same directory) ', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Thumbnail', 'corpora_theme' ),
										'id' => 'video_thumbnail',
										'description' => __( 'Select video thumbnail that will appear in popup gallery for this video. If no image will be selected then default thumbnail will be used for selfhosted vide and autmatically generated thumbnail for youtube/vimeo videos.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Poster', 'corpora_theme' ),
										'id' => 'video_poster',
										'description' => __( 'Poster will be applied for mobile devices that do not support autoplay and it is not possible to grab first frame of video.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Autoplay', 'corpora_theme' ),
										'id' => 'video_autoplay',
										'description' => __( 'If enabled video will start playback once visible.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for popup video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for popup video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				),
				'bk_gallery_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Youtube / Vimeo Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Place here link to Youtube or Vimeo that will be displayed inside gallery.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Gallery "METRO" Thumbnail Size', 'corpora_theme' ),
										'id' => 'thumbnail_size',
										'description' => __( 'Choose size of thumbnail used for this image while displayed inside "METRO" gallery style layout.', 'corpora_theme' ),
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
										'id' => 'mixed_masonry_thumbnail_size',
										'description' => __( 'Choose size of thumbnail used for this portfolio entry while displayed inside "MIXED MASONRY" gallery style layout', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'small',
										'all_values' => array(	"small" => "Small",
																"large" => "Large",
																//"x-large" => "Extra Large"
																
										)
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				),
				'bk_gallery_selfhosted_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Video Link (*.m4v, *.mp4)', 'corpora_theme' ),
										'id' => 'primary_video_link',
										'description' => __( 'Link to selfhosted (*.m4v) video. This is primary video. It is recomended to specify also additonal link to the same vidoe in another format (*.ogv) to make player work on any device if (*.m4v) fails to load.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Link (*.ogv, *.webm)', 'corpora_theme' ),
										'id' => 'secondary_video_link',
										'description' => __( 'Additional (*.ogv, *.webm) format of video. This is secondary video link. Note! it is required to specify (*.m4v, *.mp4) first to make player work. If You specify only link to (*.ogv, *.webm) file it may not work in all browsers.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Gallery "METRO" Thumbnail Size', 'corpora_theme' ),
										'id' => 'thumbnail_size',
										'description' => __( 'Choose size of thumbnail used for this image while displayed inside "METRO" gallery style layout.', 'corpora_theme' ),
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
										'id' => 'mixed_masonry_thumbnail_size',
										'description' => __( 'Choose size of thumbnail used for this portfolio entry while displayed inside "MIXED MASONRY" gallery style layout', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'small',
										'all_values' => array(	"small" => "Small",
																"large" => "Large",
																//"x-large" => "Extra Large"
										)
								),
								array(
										'name' => __( 'Poster', 'corpora_theme' ),
										'id' => 'video_poster',
										'description' => __( 'This image will be used as poster for player filling background before user hit play button.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				)

		);
	}

	/*
	 * Get link type
	*
	* this function takes as argument single url and return it's type - it can be image, selfhosted video or youtube / vimeo video
	* @params: (string) $url - link to image, video or youtube / vimeo
	* returns: (mixed) - string that describes media type if found or false if unknown format
	*
	*
	*/

	protected function get_link_type( $url ) {
		if( preg_match ("/\b(?:vimeo|youtube)\.com\b/i", $url) ) {
			return 'bk_gallery_video';
		} else if( preg_match("/\b\.(jpg|jpeg|png|bmp|gif)\b/i", $url) ) {
			return 'bk_gallery_image';
		} else if( preg_match("/\b\.(mp4|m4v|ogv|webm)\b/i", $url) ) {
			return 'bk_gallery_selfhosted_video';
		} else {
			return false;
		}
	}
}


/*
 * Post Featured gallery manager - handles galleries that are featured for given post
*
*/
class Bk_Post_Featured_Gallery_Manager extends Bk_Gallery_Manager {
	private static $instance;  // reference to singleton class

	/*
	 * Get instance of singleton Bk_Gallery_Manager class
	*
	* this method is used to get reference to singleton and call other functions
	* if it's being called first time during application it creates new instance of
	* Bk_Gallery_Manager class
	* usage Bk_Gallery_Manager::getInstance()->somefunction();
	*
	* @param - void
	* @return - (Bk_Gallery_Manager class) instance
	*
	*/

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 * Class Constructor
	*
	* $items contains all gallery manager blocks along with descriptions, filed type etc..
	* it is used to generate proper editors for gallery thumb blocks as blocks itself via ajax call from admin
	*
	* id of each module corresponds to thumb type that will be handled e.g bk_gallery_image, bk_gallery_video
	* each editor field in module will have uniqe id e.g bk_gallery_image_link_type_editor
	*
	* @params - void
	* @return - void
	*
	*/

	private function __construct() {
		$this->editors_out = false;
		$this->gallery_subtype = 'post_featured_gallery';

		$this->items = array(
				'bk_gallery_image' => array(
						'name' => __('Image', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Image Description', 'corpora_theme' ),
										'id' => 'image_description',
										'description' => __( 'Place description that will appear under image when user clicks on it.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array( 'name' => __( 'Image Link', 'corpora_theme' ),
										'id' => 'link_type',
										'description' => __( 'Choose link type for this image that will be opened in popup if user cliks on it.', 'corpora_theme' ),
										'type' => 'stacked_combobox',
										'default' => 'fullsize_image',
										'all_values' => array(	"none" => array( "label" => "None", "buddy" => "" ),
												"fullsize_image" => array( "label" => "Fullsize Image", "buddy" => "" ),
												"other_image" => array( "label" => "Other Image", "buddy" => "#buddy_wrap_bk_gallery_image_other_image_link_editor_post_featured_gallery" ),
												"video" => array( "label" => "Video", "buddy" => "#buddy_wrap_bk_gallery_image_video_link_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_width_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_height_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_autoplay_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_thumbnail_editor_post_featured_gallery" ),
												"selfhosted_video" => array( "label" => "Selfhosted Video", "buddy" => "#buddy_wrap_bk_gallery_image_selfhosted_video_link_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_width_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_height_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_autoplay_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_thumbnail_editor_post_featured_gallery, #buddy_wrap_bk_gallery_image_video_poster_editor_post_featured_gallery" ),

										)
								),
								array(
										'name' => __( 'Other Image Link', 'corpora_theme' ),
										'id' => 'other_image_link',
										'description' => __( 'Select which image will be opened in popup when user clicks on thumb.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Youtube / Vimeo Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Place here link to Youtube or Vimeo that will be opened in popup.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Video Link', 'corpora_theme' ),
										'id' => 'selfhosted_video_link',
										'description' => __( 'Choose *.mp4 video file that will be displayed in popup gallery. To provide full cross browser support it is necessary that *.webm version of this video will be placed with the same name( e.g .../video.mp4 -> .../video.webm ) in the same media directory (it is good idea to upload both files at the same time to make sure that wordpress will place them in same directory) ', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Thumbnail', 'corpora_theme' ),
										'id' => 'video_thumbnail',
										'description' => __( 'Select video thumbnail that will appear in popup gallery for this video. If no image will be selected then default thumbnail will be used for selfhosted vide and autmatically generated thumbnail for youtube/vimeo videos.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Poster', 'corpora_theme' ),
										'id' => 'video_poster',
										'description' => __( 'Poster will be applied for mobile devices that do not support autoplay and it is not possible to grab first frame of video.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Autoplay', 'corpora_theme' ),
										'id' => 'video_autoplay',
										'description' => __( 'If enabled video will start playback once visible.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for popup video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for popup video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				),
				'bk_gallery_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Youtube / Vimeo Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Place here link to Youtube or Vimeo that will be displayed inside gallery.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				),
				'bk_gallery_selfhosted_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Video Link (*.m4v, *.mp4)', 'corpora_theme' ),
										'id' => 'primary_video_link',
										'description' => __( 'Link to selfhosted (*.m4v) video. This is primary video. It is recomended to specify also additonal link to the same vidoe in another format (*.ogv) to make player work on any device if (*.m4v) fails to load.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Link (*.ogv, *.webm)', 'corpora_theme' ),
										'id' => 'secondary_video_link',
										'description' => __( 'Additional (*.ogv, *.webm) format of video. This is secondary video link. Note! it is required to specify (*.m4v, *.mp4) first to make player work. If You specify only link to (*.ogv, *.webm) file it may not work in all browsers.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Poster', 'corpora_theme' ),
										'id' => 'video_poster',
										'description' => __( 'This image will be used as poster for player filling background before user hit play button.', 'corpora_theme' ),
										'type' => 'simple_browse_button_with_label',
										'default' => ''
								),
								array(
										'name' => __( 'Video Width', 'corpora_theme' ),
										'id' => 'video_width',
										'description' => __( 'Choose width for video.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 640,
										'min' => 1,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Video Height', 'corpora_theme' ),
										'id' => 'video_height',
										'description' => __( 'Choose height for video', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 360,
										'min' => 1,
										'max' => 1080,
										'step' => 1
								)
						)
				)

		);
	}

	/*
	 * Get link type
	*
	* this function takes as argument single url and return it's type - it can be image, selfhosted video or youtube / vimeo video
	* @params: (string) $url - link to image, video or youtube / vimeo
	* returns: (mixed) - string that describes media type if found or false if unknown format
	*
	*
	*/

	protected function get_link_type( $url ) {
		if( preg_match ("/\b(?:vimeo|youtube)\.com\b/i", $url) ) {
			return 'bk_gallery_video';
		} else if( preg_match("/\b\.(jpg|jpeg|png|bmp|gif)\b/i", $url) ) {
			return 'bk_gallery_image';
		} else if( preg_match("/\b\.(mp4|m4v|ogv|webm)\b/i", $url) ) {
			return 'bk_gallery_selfhosted_video';
		} else {
			return false;
		}
	}
}

/*
 * Fullscreen gallery manager - handles galleries that are displayed as fullscreen page,post,portfolio background galleries
*
*/
class Bk_Fullscreen_Gallery_Manager extends Bk_Gallery_Manager {
	private static $instance;  // reference to singleton class

	/*
	 * Get instance of singleton Bk_Gallery_Manager class
	*
	* this method is used to get reference to singleton and call other functions
	* if it's being called first time during application it creates new instance of
	* Bk_Gallery_Manager class
	* usage Bk_Gallery_Manager::getInstance()->somefunction();
	*
	* @param - void
	* @return - (Bk_Gallery_Manager class) instance
	*
	*/

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 * Class Constructor
	*
	* $items contains all gallery manager blocks along with descriptions, filed type etc..
	* it is used to generate proper editors for gallery thumb blocks as blocks itself via ajax call from admin
	*
	* id of each module corresponds to thumb type that will be handled e.g bk_gallery_image, bk_gallery_video
	* each editor field in module will have uniqe id e.g bk_gallery_image_link_type_editor
	*
	* @params - void
	* @return - void
	*
	*/

	private function __construct() {
		$this->editors_out = false;
		$this->gallery_subtype = 'fullscreen_gallery';

		$this->items = array(
				'bk_gallery_image' => array(
						'name' => __('Image', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Title', 'corpora_theme' ),
										'id' => 'image_title',
										'description' => __( 'Place title that will appear for this image.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Description', 'corpora_theme' ),
										'id' => 'image_description',
										'description' => __( 'Place description that will appear for this image.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Link', 'corpora_theme' ),
										'id' => 'image_link',
										'description' => __( 'Place link that will be applied for this image.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
						)
				)
		);
	}

	/*
	 * Get link type
	*
	* this function takes as argument single url and return it's type - it can be image, selfhosted video or youtube / vimeo video
	* @params: (string) $url - link to image, video or youtube / vimeo
	* returns: (mixed) - string that describes media type if found or false if unknown format
	*
	*
	*/

	protected function get_link_type( $url ) {
		if( preg_match("/\b\.(jpg|jpeg|png|bmp|gif)\b/i", $url) ) {
			return 'bk_gallery_image';
		} else {
			return false;
		}
	}
}

/*
 * Handle ajax new gallery item request
*
*/
add_action('wp_ajax_bk_handle_upload_fullscreen_gallery', array(Bk_Fullscreen_Gallery_Manager::getInstance(), 'bk_handle_gallery_upload_button_ajax'));
add_action('wp_ajax_bk_handle_upload_popup_gallery', array(Bk_Popup_Gallery_Manager::getInstance(), 'bk_handle_gallery_upload_button_ajax'));
add_action('wp_ajax_bk_handle_upload_featured_gallery', array(Bk_Featured_Gallery_Manager::getInstance(), 'bk_handle_gallery_upload_button_ajax'));
add_action('wp_ajax_bk_handle_upload_post_featured_gallery', array(Bk_Post_Featured_Gallery_Manager::getInstance(), 'bk_handle_gallery_upload_button_ajax'));
?>