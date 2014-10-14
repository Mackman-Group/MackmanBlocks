<?php
/*
 * Bk_Field_Factory singleton class
 * 
 * this class outputs proper editing field based on type and parameters that can be used
 * in theme admin, pagebuilder editors and posts custom metaboxes
 * 
 */

class Bk_Field_Factory {
	private static $instance;  // reference to singleton class

	/*
	 * Get instance of singleton Bk_Page_Builder class
	 *
	 * this method is used to get reference to singleton and call other functions
	 * if it's being called first time during application it creates new instance of
	 * Bk_Field_Factory class
	 * usage Bk_Field_Factory::getInstance()->somefunction();
	 *
	 * @param - void
	 * @return - (Bk_Field_Factory class) instance
	 *
	 */
	
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	/*
	 * print setting field markup
	 * 
	 * this function outputs setting filed html markup based on type and parameters
	 * it can additionally add prefix and suffix to filed id - only used in pagebuilder editors
	 * in other cases prefix and suffix are not used
	 * 
	 * @param - (array) $setting - array of settings - $setting['type'] is always - other keys depends on this value
	 * @param - (mixed) $value that will be used to populate field - if no value then $setting['default'] will be used
	 * @param - (string) $prefix - prefix used for setting id, only used for pagebuilder editors 
	 * 			e.g if pagebuilder portfolio editor will be printed out its fields will have id of portfolio module as prefix
	 * 			bk_portfolio_show_layout_switcher_editor - bk_portfolio -> id of portfolio module
	 * 													   show_layout_switcher -> id of field
	 * 													   editor -> suffix just to make sure that whole field id is unique
	 * 
	 * @param - (string) $suffix - suffix used for setting id to make sure that whole id is unique see above - only used for pagebuilder block editors	
	 * 
	 */
	
	public function print_setting_field( $setting = array(), $value = '', $prefix = '', $suffix = '' ) {	
		
		switch ($setting['type']) {
			case 'separator':
	
				echo $this->add_separator( $setting['description'] );
	
				break;
	
			case 'newsletter_list':
	
				echo $this->add_newsletter_download_link( $setting['name'], $setting['description']  );
	
				break;
	
			case 'checkbox':
	
				echo $this->add_checkbox_option( $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'gallery':
	
				echo $this->add_gallery_option(  $prefix . $setting['id'] . $suffix, $value, $setting['images'], $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'checkbox_section':
	
				echo $this->add_checkbox_section_option(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'], $setting['rows'] );
	
				break;
	
			case 'combobox' :
	
				echo $this->add_list_option(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['all_values'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'stacked_combobox' :
	
				echo $this->add_stacked_combobox_option(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['all_values'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'radiobuttons' :
	
				echo $this->add_radiobuttons_option(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['all_values'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'image_radiobuttons' :
	
				echo $this->add_image_radiobuttons_option(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['all_values'], $setting['name'], $setting['description'] );
	
			break;
			
			case 'stacked_image_radiobuttons' :
			
				echo $this->add_stacked_image_radiobuttons_option(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['all_values'], $setting['name'], $setting['description'] );
			
			break;
	
			case 'reset_button':
	
				echo $this->add_reset_button_option(  $prefix . $setting['id'] . $suffix, $setting['name'], $setting['description'], $setting['token'] );
	
				break;
	
			case 'slider' :
	
				echo $this->add_slider_option(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['slider_class_type'], $setting['name'], $setting['description'], $setting['min'], $setting['max'], $setting['step'] );
	
				break;
	
			case 'textinput':
	
				echo $this->add_textintput(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'textarea':
	
				echo $this->add_textarea(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'rich_textarea':
	
				$this->add_rich_textarea(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'browse_button_with_label':
	
				echo $this->browse_button_with_label(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description']  );
	
				break;
	
			case 'simple_browse_button_with_label':
	
				echo $this->simle_browse_button_with_label(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description']  );
	
				break;
	
			case 'colorpicker_button_with_label':
	
				echo $this->colorpicker_button_with_label(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'simple_colorpicker':
	
				echo $this->simple_colorpicker(  $prefix . $setting['id'] . $suffix, $value, $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'post_combobox':
	
				echo $this->post_combobox(  $prefix . $setting['id'] . $suffix, $setting['post_type'], isset($value) ? $value : $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'tb_browse_button':
	
				echo $this->tb_browse_button(  $prefix . $setting['id'] . $suffix, $setting['name'], $setting['description'] );
	
				break;
	
			case 'taxonomy_combobox':
	
				echo $this->add_taxonomy_combobox(  $prefix . $setting['id'] . $suffix, $setting['taxonomy_slug'], isset($value) ? $value : $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'multiple_taxonomy_combobox':
	
				echo $this->add_multiple_taxonomy_combobox(  $prefix . $setting['id'] . $suffix, $setting['taxonomy_slug'], isset($value) ? $value : $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'font_type_combobox':
	
				echo $this->font_type_combobox(  $prefix . $setting['id'] . $suffix, $value ? $value : $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'sidebar_combobox':
	
				echo $this->sidebar_combobox(  $prefix . $setting['id'] . $suffix, isset($value) ? $value : $setting['default'], $setting['name'], $setting['description'] );
	
				break;
	
			case 'description_field':
	
				echo $this->bk_description_field(  $prefix . $setting['id'] . $suffix, $setting['name'], $setting['description'], $setting['additional_info'] );
	
				break;
	
		}
	}
	
	/*
	 add reset button option
	*/
	private function add_reset_button_option( $setting_id, $name, $description, $token ) {
		$tb_browse_button = '<tr id="buddy_wrap' . $setting_id . '">';
		$tb_browse_button .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$tb_browse_button .= '<td><a data-token="' . $token . '" class="button-secondary bk-reset-button" id="' . $setting_id . '">' .  __( 'Reset', 'corpora_theme' )  . '</a></td>';
		$tb_browse_button .= '</tr>';
	
		return $tb_browse_button;
	}
	
	/*
	 add browse thicbox button
	*/
	private function tb_browse_button( $setting_id, $name, $description ) {
		$tb_browse_button = '<tr id="buddy_wrap' . $setting_id . '">';
		$tb_browse_button .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$tb_browse_button .= '<td><input type="button" class="button-secondary" id="' . $setting_id . '" value="' .  __( 'Browse', 'corpora_theme' )  . '" /></td>';
		$tb_browse_button .= '</tr>';
	
		return $tb_browse_button;
	}
	/*
	 Helper fuctions that construct various settings input types
	*/
	
	
	private function add_newsletter_download_link( $name, $description ) {
		$link_option = '';
		$link_option .=	'<tr><td><label><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$link_option .=	'<td><a href="'  . get_template_directory_uri() . '/export/subscribers.csv' . '">' . __( 'Download Subscribers', 'corpora_theme' ) . '</a></td></tr>';
		return $link_option;
	}
	
	private function add_taxonomy_combobox( $setting_id, $taxonomy_slug, $selected_value, $name, $description ) {
			
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select class="bk-choosen-dropdown" id="' . $setting_id . '" name="' . $setting_id . '">';
			
		$label = __( 'All Categories', 'corpora_theme' );
			
		if( $selected_value == -1 )
			$select_option .= "<option value='-1' selected='selected'>$label</option>";
		else
			$select_option .= "<option value='-1'>$label</option>";
			
		$terms = get_terms($taxonomy_slug);
		$count = count($terms);
		if ($count > 0) {
			foreach ($terms  as $term) {
				if( $selected_value == $term->slug )
					$select_option .= '<option value="' . $term->slug . '" selected="selected">' . $term->name . '</option>';
				else
					$select_option .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
			}
		}
		$select_option .=		"</select></td></tr>";
		return $select_option;
	}
	
	private function add_multiple_taxonomy_combobox( $setting_id, $taxonomy_slug, $selected_values, $name, $description ) {
			
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select multiple="multiple" class="bk-choosen-dropdown" id="' . $setting_id . '" name="' . $setting_id . '[]">';
			
		$label = __( 'All Categories', 'corpora_theme' );
			
		if( is_string( $selected_values ) ) {
			$selected_values = explode(",",$selected_values);
		}
			
		//if( !isset( $remove_default_label ) ) {
			if( in_array( -1, $selected_values ) ) {
				$select_option .= "<option value='-1' selected='selected'>$label</option>";
			} else {
				$select_option .= "<option value='-1'>$label</option>";
			}	
		//}
			
		$terms = get_terms($taxonomy_slug);
		$count = count($terms);
		if ($count > 0) {
			foreach ($terms  as $term) {
				if( in_array( $term->slug, $selected_values ) )
					$select_option .= '<option value="' . $term->slug . '" selected="selected">' . $term->name . '</option>';
				else
					$select_option .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
			}
		}
		$select_option .=		"</select></td></tr>";
		return $select_option;
	}
	
	private function add_checkbox_option( $setting_id, $value, $default, $name, $description ) {
			
		if( isset($value) )
			$checked = $value ? 'checked="checked"' : '';
		else
			$checked = $default ? 'checked="checked"' : '';
			
		$checkbox = '<tr id="buddy_wrap' . $setting_id . '">';
		$checkbox .= '<td><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></td>';
		$checkbox .= '<td><input type="checkbox" id="' . $setting_id . '" name="' . $setting_id . '" ' . $checked . ' class="fancy_checkbox"/><label class="bk-toggle-checkbox" for="' . $setting_id . '"></label></td>';
		$checkbox .= '</tr>';
	
		return $checkbox;
	}
	
	private function add_checkbox_section_option( $setting_id, $value, $default, $name, $description, $rows ) {
			
		if( isset($value) )
			$checked = $value ? 'checked="checked"' : '';
		else
			$checked = $default ? 'checked="checked"' : '';
			
		$checkbox = '<tr id="buddy_wrap' . $setting_id . '">';
		$checkbox .= '<td><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></td>';
		$checkbox .= '<td><input type="checkbox" id="' . $setting_id . '" name="' . $setting_id . '" ' . $checked . ' class="fancy_checkbox toggle_section" data-toggle_count="' . $rows  . '"/><label class="bk-toggle-checkbox" for="' . $setting_id . '"></label></td>';
		$checkbox .= '</tr>';
	
		return $checkbox;
	}
	
	/*
	 add separator
	*/
	private function add_separator( $description ) {
		$separator = '<tr>';
		$separator .= '<td class="separator-desc">' . $description  .'</td><td class="separator-desc"></td>';
		$separator .= '</tr>';
			
		return $separator;
	}
	
	/*
	 add textinput
	*/
	private function add_textintput( $setting_id, $value, $default, $name, $description ) {
	
		$textinput = '<tr id="buddy_wrap' . $setting_id . '">';
		$textinput .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$textinput .= '<td><input type="text" id="' . $setting_id . '" name="' . $setting_id . '" value="' . ( $value ? $value : stripslashes(htmlspecialchars(( $default ), ENT_QUOTES)) ) . '"/></td>';
		$textinput .= '</tr>';
	
		return $textinput;
	}
	
	/*
	 add rich text area
	*/
	private function add_rich_textarea( $setting_id, $value, $default, $name, $description ) {
	
		echo '<tr id="buddy_wrap' . $setting_id . '">';
		echo '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		if( function_exists("wp_editor") ) {
			echo '<td>';
			wp_editor( stripslashes(($value ? $value :  $default)), $setting_id);
			echo '</td>';
		}	else {
			echo '<td><textarea id="' . $setting_id . '" name="' . $setting_id . '">' .  stripslashes(htmlspecialchars(( $value ? $value :  $default ))) . '</textarea></td>';
		}
		echo '</tr>';
	}
	
	/*
	 add textinput
	*/
	private function add_textarea( $setting_id, $value, $default, $name, $description ) {
	
		$textinput = '<tr id="buddy_wrap' . $setting_id . '">';
		$textinput .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$textinput .= '<td><textarea id="' . $setting_id . '" name="' . $setting_id . '">' .  stripslashes(htmlspecialchars(( $value ? $value :  $default ))) . '</textarea></td>';
		$textinput .= '</tr>';
	
		return $textinput;
	}
	
	/*
	 add browse button
	*/
	private function browse_button_with_label( $setting_id, $value, $default, $name, $description ) {
	
		$option = '<tr id="buddy_wrap' . $setting_id . '">';
		$option .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$option .= '<td><input readonly="readonly" class="bk_button_textinput" type="text" id="' . $setting_id . '" name="' . $setting_id . '" value="' . ( $value ? $value : stripslashes(htmlspecialchars(( $default ), ENT_QUOTES)) ) . '"/>';
		$option .= '<a class="button bk_browse_button simple">' .  __( 'Browse', 'corpora_theme' )  . '</a>';
		$option .= '<div class="clearfix"></div>';
		$option .= '<div class="bk_image_placeholder">';
		 
		if( !empty($value) ) {
			$option .= '<img class="bk_image_preview" src="' . $value . '">';
		}
		 
		$option .= '<a class="bk_remove_image_button" title="' .  __( 'Remove', 'corpora_theme' ) . '"></a>';
		$option .= '</div>';
		 
		$option .= '</td>';
		$option .= '</tr>';
	
		return $option;
	}
	/*
	 add gallery option add_gallery_option( $setting['id'], $value, $setting['images'], $setting['default'], $setting['name'], $setting['description'] )
	*/
	private function add_gallery_option( $setting_id, $value, $images, $default, $name, $description ) {
		if( empty($value) )
			$value = $default;
	
		$option = '<tr id="buddy_wrap' . $setting_id . '">';
		$option .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$option .= '<td><input type="hidden" class="bk_gallery_images_count" id="' . $setting_id . '" name="' . $setting_id . '" value="' . $value . '"/>';
		$option .= '<a class="button bk_browse_button gallery">' .  __( 'Browse', 'corpora_theme' )  . '</a>';
		$option .= '<div class="clearfix"></div>';
		$option .= '<div class="bk_gallery_placeholder">';
		 
		if( is_array( $images ) && !empty( $images ) ) {
			$count = 0;
			foreach( $images as $large_img_link ) {
				$img_id = bk_get_image_id($large_img_link);
				$thumb = wp_get_attachment_image_src( $img_id, 'thumbnail' );
				$thumb_src = $thumb[0];
				 
				$option .= bk_get_single_gallery_item_thumb( $thumb_src, $setting_id, $count, $large_img_link  );
				$count++;
			}
		}
		 
		$option .= '</div>';
		$option .= '<div class="clearfix"></div>';

		
		
		$option .= '</td>';
		$option .= '</tr>';
	
		return $option;
	}
	
	
	
	/*
	 add browse button
	*/
	private function simle_browse_button_with_label( $setting_id, $value, $default, $name, $description ) {
	
		$option = '<tr id="buddy_wrap' . $setting_id . '">';
		$option .= '<td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$option .= '<td><input readonly="readonly" class="bk_button_textinput bk_simple" type="text" id="' . $setting_id . '" name="' . $setting_id . '" value="' . ( $value ? $value : stripslashes(htmlspecialchars(( $default ), ENT_QUOTES)) ) . '"/>';
		$option .= '<a type="button" class="button bk_browse_button simple">' .  __( 'Browse', 'corpora_theme' )  . '</a>';
		$option .= '<a type="button" class="button bk_remove_button">' .  __( 'Remove', 'corpora_theme' )  . '</a></td>';
		$option .= '</tr>';
	
		return $option;
	}
	
	
	/*
	 add colorpicker with button and label
	*/
	private function colorpicker_button_with_label( $setting_id, $value, $default, $name, $description ) {
	
		$option = '<tr id="buddy_wrap' . $setting_id . '">';
		$option .= '<td><label for="' .  $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$option .= '<td><input readonly="readonly" class="bk_button_textinput bk_colorpicker_value" type="text" id="' .  $setting_id . '" name="' .  $setting_id . '" value="' . ( $value ? $value : stripslashes(htmlspecialchars(( $default ), ENT_QUOTES)) ) . '"/>';
		$option .= '<div class="bk-colorpicker"><div></div></div>';
		$option .= '<input type="button" class="button bk_remove_button" id="' .  $setting_id. '_remove_button' . '" name="' .  $setting_id. '_remove_button' . '" value="' .  __( 'Default', 'corpora_theme' )  . '" /></td>';
		$option .= '</tr>';
		 
		return $option;
	}
	
	private function simple_colorpicker( $setting_id, $value, $default, $name, $description ) {
	
		$option =  '<tr id="buddy_wrap' . $setting_id . '">';
		$option .= '<td><label for="' .  $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$option .= '<td><input class="bk_colorpicker_value" readonly="readonly" type="text" id="' .  $setting_id . '" name="' .  $setting_id . '" value="' . ( $value ? $value : stripslashes(htmlspecialchars(( $default ), ENT_QUOTES)) ) . '"/>';
		$option .= '<div class="bk-colorpicker simple"><div></div></div>';
		$option .= '</td>';
		$option .= '</tr>';
		 
		return $option;
	}
	/*
	 Additional description
	*/
	private function bk_description_field( $setting_id, $name, $description, $additional_info ) {
	
		$option =  '<tr id="buddy_wrap' . $setting_id . '">';
		$option .= '<td><label><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$option .= '<td>';
		$option .= $additional_info ;
		$option .= '</td>';
		$option .= '</tr>';
		 
		return $option;
	}
	
	/*
	 add font type combobox
	*/
	private function font_type_combobox( $setting_id, $selected_value, $name, $description ) {
		$select_option = '';
		$select_option .=	'<tr><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td>';
	
		global $bk_google_apis_webfonts_key;
	
		$fonts = get_transient('bk-google-webfonts');
		if(!is_array(json_decode($fonts, true))){
			$google_api = 'https://www.googleapis.com/webfonts/v1/webfonts';
			$google_api = add_query_arg( 'key', $bk_google_apis_webfonts_key, $google_api );
				
			$response = wp_remote_get($google_api, array('sslverify' => false));
			if( !is_wp_error( $response ) ) {
				$fonts = wp_remote_retrieve_body($response);
				set_transient('bk-google-webfonts', $fonts, 60 * 60 * 24);
			} else {
				echo $response->get_error_message();
			}
		}
		$google_fonts = json_decode($fonts, true);
	
		if( is_array($google_fonts) ) {
			$select_option .= '<div class="bk-googlefont-select-wrap">';
			$select_option .= '<select class="bk-googlefont-select" id="' . $setting_id . '" name="' . $setting_id . '">';
			$select_option .= '<option value="-1" '. selected($selected_value, -1, false) . '>' . __("Default Font", "corpora_theme") . '</option>';
			foreach($google_fonts['items'] as $cut){
				foreach($cut['variants'] as $variant){
					$select_option .= '<option value="' . $cut['family'] . ':' . $variant . '" ' . selected($selected_value, $cut['family'].':'.$variant, false) . '>' . $cut['family'] . ' - ' . $variant . '</option>';
				}
			}
			$select_option .= '</select>';
			$select_option .= '<div class="bk-google-font-preview" id="bk-googlefont-preview-wrap-' .  $setting_id . '">Grumpy wizards make toxic brew for the evil Queen and Jack</div>';
			$select_option .= '</div>';
		}
		$select_option .= '</td></tr>';
		return $select_option;
	}
	
	/*
	 add image radiobuttons
	*/
	private function add_image_radiobuttons_option ( $setting_id, $selected_value, $all_values, $name, $description ) {
		$radiobuttons_option = '';
		$radiobuttons_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$radiobuttons_option .= '<td>';
			
		foreach ($all_values as $key => $options)	{
			$image = get_template_directory_uri() . '/admin/images/' . $options['image'];
			$label = $options['label'];
			if( $selected_value == $key ) {
				$radiobuttons_option .= '<div class="bk-image-radiobutton-wrap"><img class="bk-radio-thumb current" src="' . $image . '" title="' . $label . '"/><input class="bk-image-radio" type="radio" checked="checked" name="' . $setting_id . '" value="' . $key . '" /><span class="bk-image-radiobutton-desc">' . $label . '</span></div>';
			} else {
				$radiobuttons_option .= '<div class="bk-image-radiobutton-wrap"><img class="bk-radio-thumb" src="' . $image . '" title="' . $label . '"/><input class="bk-image-radio" type="radio" name="' . $setting_id . '" value="' . $key . '" /><span class="bk-image-radiobutton-desc">' . $label . '</span></div>';
			}
		}
			
		$radiobuttons_option .=		'<div class="clearfix" ></div></td></tr>';
	
		return $radiobuttons_option;
	}
	
	/*
	 * Add stacked image radiobuttons set
	 */
	private function add_stacked_image_radiobuttons_option ( $setting_id, $selected_value, $all_values, $name, $description ) {
		$radiobuttons_option = '';
		$radiobuttons_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$radiobuttons_option .= '<td>';
			
		foreach ($all_values as $key => $options)	{
			$image = get_template_directory_uri() . '/admin/images/' . $options['image'];
			$label = $options['label'];
			$buddy_id = $options['buddy'];
			if( $selected_value == $key ) {
				$radiobuttons_option .= '<div data-buddy-id="' . $buddy_id . '" class="bk-image-radiobutton-wrap bk-stacked-radio"><img class="bk-radio-thumb current" src="' . $image . '" title="' . $label . '"/><input class="bk-image-radio" type="radio" checked="checked" name="' . $setting_id . '" value="' . $key . '" /><span class="bk-image-radiobutton-desc">' . $label . '</span></div>';
			} else {
				$radiobuttons_option .= '<div data-buddy-id="' . $buddy_id . '" class="bk-image-radiobutton-wrap bk-stacked-radio"><img class="bk-radio-thumb" src="' . $image . '" title="' . $label . '"/><input class="bk-image-radio" type="radio" name="' . $setting_id . '" value="' . $key . '" /><span class="bk-image-radiobutton-desc">' . $label . '</span></div>';
			}
		}
			
		$radiobuttons_option .=		'<div class="clearfix" ></div></td></tr>';
	
		return $radiobuttons_option;
	}
	
	/*
	 add radiobutton group
	*/
	private function add_radiobuttons_option ( $setting_id, $selected_value, $all_values, $name, $description ) {
		$radiobuttons_option = '';
		$radiobuttons_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$radiobuttons_option .= '<td>';
			
		foreach ($all_values as $key => $value)	{
			$label = $key;
				
			if(is_int($key))
				$label = $value;
				
			if( $selected_value == $value )
				$radiobuttons_option .= '<label class="bk-radiobutton-label" ><input type="radio" checked="checked" class="bk-radiobutton" name="' . $setting_id . '" value="' . $value . '" />' . $label . '</label>';
			else
				$radiobuttons_option .= '<label class="bk-radiobutton-label" ><input type="radio" name="' . $setting_id . '" value="' . $value . '" />' . $label . '</label>';
		}
			
		$radiobuttons_option .=		"</td></tr>";
	
		return $radiobuttons_option;
	}
	/*
	 add combobox list
	*/
	private function add_list_option ( $setting_id, $selected_value, $all_values, $name, $description ) {
			
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select class="bk-choosen-dropdown" id="' . $setting_id . '" name="' . $setting_id . '">';
			
		foreach ($all_values as $key => $value)	{
				
			if( $selected_value == $key )
				$select_option .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
			else
				$select_option .= '<option value="' . $key . '">' . $value . '</option>';
		}
			
		$select_option .=		"</select></td></tr>";
	
		return $select_option;
	}
	
	/*
	 add stacked_combobox
	*/
	private function add_stacked_combobox_option ( $setting_id, $selected_value, $all_values, $name, $description ) {
			
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select class="bk-choosen-dropdown bk-stacked-combobox" id="' . $setting_id . '" name="' . $setting_id . '">';
			
		foreach ($all_values as $key => $options)	{
			$value = $options['label'];
			$buddy = $options['buddy'];
			if( $selected_value == $key )
				$select_option .= '<option data-buddy-id="' . $buddy . '" value="' . $key . '" selected="selected">' . $value . '</option>';
			else
				$select_option .= '<option data-buddy-id="' . $buddy . '" value="' . $key . '">' . $value . '</option>';
		}
			
		$select_option .=		"</select></td></tr>";
	
		return $select_option;
	}
	
	/*
	 add combobox list
	*/
	private function add_multi_list_option ( $setting_id, $selected_value, $all_values, $name, $description ) {
			
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select multiple="multiple" class="bk-choosen-dropdown" id="' . $setting_id . '" name="' . $setting_id . '">';
			
		foreach ($all_values as $key => $value)	{
			$label = $key;
				
			if(is_int($key))
				$label = $value;
				
			if( $selected_value == $value )
				$select_option .= '<option value="' . $value . '" selected="selected">' . $label . '</option>';
			else
				$select_option .= '<option value="' . $value . '">' . $label . '</option>';
		}
			
		$select_option .=		"</select></td></tr>";
	
		return $select_option;
	}
	
	/*
	 add combobox list listing specified post format where label is post title and value is post id
	*/
	
	private function post_combobox( $setting_id, $post_type, $selected_value, $name, $description ) {
		 
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select class="bk-choosen-dropdown" id="' . $setting_id . '" name="' . $setting_id . '">';
			
		$label = __( '-- Choose one --', 'corpora_theme' );
			
		if( $selected_value == -1 )
			$select_option .= "<option value='-1' selected='selected'>$label</option>";
		else
			$select_option .= "<option value='-1'>$label</option>";
			
		$args= array(
				'posts_per_page' => -1,
				'post_type' => $post_type
		);
		global $post;
			
		query_posts($args);
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			
		$post_id = $post->ID;
		$title = get_the_title();
			
		if( $selected_value == $post_id )
			$select_option .= "<option value='$post_id' selected='selected'>$title</option>";
		else
			$select_option .= "<option value='$post_id'>$title</option>";
			
			
		endwhile;
		endif;
		wp_reset_query();
		wp_reset_postdata();
			
		$select_option .=		"</select></td></tr>";
	
		return $select_option;
	}
	
	private function sidebar_combobox( $setting_id, $selected_value, $name, $description ) {
			
		$select_option = '';
		$select_option .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$select_option .= '<td><select class="bk-choosen-dropdown" id="' . $setting_id . '" name="' . $setting_id . '">';
			
		$label = __( '-- Choose one --', 'corpora_theme' );
			
		if( $selected_value == -1 )
			$select_option .= '<option value="-1" selected="selected">' . $label . '</option>';
		else
			$select_option .= '<option value="-1" >' . $label . '</option>';
			
		$sidebars = get_option("bk_sidebars_list");
		if( is_array( $sidebars ) ) {
			foreach( $sidebars as $sidebar ) {
				if( $selected_value == $sidebar["bk-sidebar-id"] )
					$select_option .= '<option value="' . $sidebar["bk-sidebar-id"] . '" selected="selected">' . $sidebar["bk-sidebar-name"] . '</option>';
				else
					$select_option .= '<option value="' . $sidebar["bk-sidebar-id"] . '" >' . $sidebar["bk-sidebar-name"] . '</option>';
			}
		}
	
		$select_option .=		"</select></td></tr>";
	
		return $select_option;
	}
	
	/*
	 add slider option
	*/
	private function add_slider_option ( $setting_id, $value, $slider_class_type, $name, $description, $min, $max, $step ) {
			
		$slider = '';
		$slider .=	'<tr id="buddy_wrap' . $setting_id . '"><td><label for="' . $setting_id . '"><strong>' . $name . '</strong><span class="bk-field-description">' . $description  . '</span></label></td>';
		$slider .=	'<td><input type="hidden" class="slider_hidden_value" value="' . $value .'" name="' . $setting_id .'"/>';
		$slider .=	'<div class="slider_container">';
		$slider .=	'<div class="bk-slider slider ' . $slider_class_type . '" data-min="' . $min . '" data-max="' . $max . '" data-step="' . $step . '" id="' . $setting_id . '"></div>';
		$slider .=	'</div><div class="slider_value"></div><div class="clearfix"></div>';
			
		$slider .=  '<span class="slider-description">' . __( 'Use keyboard arrows to fine tune', 'corpora_theme' ) . ' (&larr; &rarr; &uarr; &darr; )' .'</span>';
		$slider .=  '</td></tr>';
	
		return $slider;
	}
	
}	
?>