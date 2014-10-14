/*
  Handle porfolio custom post type thickbox uploader
*/

jQuery(document).ready(function(){
	var clicked_button = null;
	var use_new_media_uploader = !!bkajax.use_new_media_uploader;
	
	jQuery('table a.bk_remove_button').click(function() {
		jQuery(this).parents('td').first().find('input.bk_button_textinput').val(null);
		return false;
	});
	
	/*
	 * remove sinlge image setting button handle 
	 */
	jQuery('table a.bk_remove_image_button').each(function() {
			if( !jQuery(this).parent('div.bk_image_placeholder').find('img.bk_image_preview').length ) {
				jQuery(this).parent('div.bk_image_placeholder').hide();
			}	
	}); 
	  
	jQuery('table a.bk_remove_image_button').click(function() {
			jQuery(this).parents('td').first().find('input.bk_button_textinput').val(null);
			jQuery(this).parent('div.bk_image_placeholder').fadeOut( function() {
			  	jQuery(this).find('img.bk_image_preview').remove();
			});
			return false;
	});
	
	/*
	 * Handle browse button used for single image fields as well as for galleries
	 * depending on settings and wordpress version uses old thickbox media uploader or new WP 3.5 media manager
	 */
	
	var new_media_manager_frame;
	var selected_gallery_items_count = 0;
	jQuery('table a.bk_browse_button').click(function() {
			clicked_button = jQuery(this);
			if( clicked_button.hasClass("bk_during_ajax") ) {
				return false;
			}
			
			if( clicked_button.hasClass("gallery") && clicked_button.hasClass("insert_video") ) {  // if insert video was clicked
				var url = prompt("Please enter Youtube/Vimeo link");
				if ( url != null && url != "" ) {
					bk_handle_selected_images_and_media( url );
				}
				
				return false;
			}
			
			if( use_new_media_uploader ) {
				
				if (typeof(new_media_manager_frame)!=="undefined") {
					new_media_manager_frame.close();
			    }
			     
			    
				//Create WP media frame.
				new_media_manager_frame = wp.media.frames.customHeader = wp.media({
			         //Title of media manager frame
			         title: "Choose file(s)",
			         frame:  'select',
			         button: {
			            //Button text
			            text: "Insert"
			         },
			         //for galleries allow multiple files selection
			         multiple: clicked_button.hasClass("gallery")
			    });
				
				
				new_media_manager_frame.on('select', function() {
				   var selection = new_media_manager_frame.state().get('selection');
				   
				   if( clicked_button.hasClass("gallery") ) {  // handle gallery
					   
					   selection.map(function(attachment) {
						   attachment = attachment.toJSON();
						   selected_gallery_items_count++;
						   bk_handle_selected_images_and_media( attachment.url );
					   });
					   
				   } else if( clicked_button.hasClass("simple") ) {
					   var attachment = new_media_manager_frame.state().get('selection').first().toJSON();
					   selected_gallery_items_count = 1;
					   bk_handle_selected_images_and_media( attachment.url );
				   }
				});
				
				//Open modal
				new_media_manager_frame.open();
				
			} else {
			  tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			}
			return false;
    });	
	
	/*
	 * Helper function - closes tb_frame when old manager is used and sets clicked button to null - beacuse only one item can be selected at a time
	 * if new media manager is used it decreases counter of selected files by one each time and releases if zero
	 * it also removes bk_during_ajax class from button
	 * 
	 */
	
	function bk_handle_gallery_image_button() {
		if( !use_new_media_uploader ) {
			tb_remove();
			clicked_button.removeClass("bk_during_ajax");
			clicked_button = null;
		} else {
			selected_gallery_items_count--;
			if( selected_gallery_items_count <= 0 ) {
				selected_gallery_items_count = 0;
				clicked_button.removeClass("bk_during_ajax");
				clicked_button = null;
			}
		}	
		
	}
	
	/*
	 * Function handles selected image/media via old thickbox or new WP 3.5 media manager based on settings
	 * 
	 * uses: global clicked_button variable
	 * @param: url -> it can be image, link or media file or an array of images in case of multiple choices in gallery
	 * 
	 */
	
	function bk_handle_selected_images_and_media( url ) {
		if( clicked_button && clicked_button.hasClass("simple") ) {
			clicked_button.parent().find('input.bk_button_textinput').val(url);
			if( clicked_button.parent().find('div.bk_image_placeholder').length ) {  // preview image placeholder was found
				clicked_button.parent().find('div.bk_image_placeholder img.bk_image_preview').remove();
				clicked_button.parent().find('div.bk_image_placeholder').append('<img class="bk_image_preview" src="' + url + '">');
				clicked_button.parent().find('div.bk_image_placeholder').fadeIn();
			}	 
			
			bk_handle_gallery_image_button();
	    } else if( clicked_button && clicked_button.hasClass("gallery") ) {  // handle gallery upload button - ajax call
	    
		    clicked_button.addClass("bk_during_ajax");
		    var gallery_subtype = clicked_button.data("gallery_subtype");
		  	
		    var $count_field = clicked_button.siblings("input.bk_gallery_images_count");
		  	var count = $count_field.val();
		  	
		  	jQuery.post(
					bkajax.ajaxurl,
					{
						action : 'bk_handle_upload_' + gallery_subtype, // e.g bk_handle_upload_popup_gallery
						url : url,
						count : count,
						use_new_media_uploader : use_new_media_uploader,
						id : $count_field.data("core-field-id")
					},
					function( data ) {
						if( data.success ) {
							var $gallery = clicked_button.siblings("div.bk_gallery_placeholder");
							
							jQuery(data.markup).hide().appendTo( $gallery ).fadeIn('slow');
							$count_field.val( $gallery.children("div.bk_gallery_item").length );
							bk_update_gallery_items_order( $gallery.find("div.bk_gallery_item") );
						} else {
							alert(data.error);
						} 
						
						bk_handle_gallery_image_button();
					}
				).error(function() { 
					bk_handle_gallery_image_button();
					alert("Error. Make sure that You when You click 'insert into post' button for image 'fullsize' radiobutton is checked."); 
			});
	  } else {
		  window.restore_send_to_editor(html);
	  }
	}
	
	/*
     *ThickBox send to editor media library handle - old way
     */

	if( !use_new_media_uploader ) {
		window.restore_send_to_editor = window.send_to_editor;
	  
	    window.send_to_editor = function(html) {
		  var url = '';
		  
		  if( clicked_button && ( clicked_button.hasClass("simple") || clicked_button.hasClass("gallery") ) ) {
			  var $inserted_object = jQuery(html); 
			  if( $inserted_object.is("img") ) {
			  	url = $inserted_object.attr('src'); 
			  } else if(  $inserted_object.is("a") ) {
			  	url = $inserted_object.attr('href');
			  }
		  }		
			
		  if( url != '' ) {
			 bk_handle_selected_images_and_media( url );
		  }
		}
	}
	
	/*
	google font preview hanlde whats new
	*/
	
	jQuery('select.bk-googlefont-select').change(function() {
  	var id = jQuery(this).attr("id");
  	var $parent = jQuery(this).parent("div.bk-googlefont-select-wrap");
  	$parent.find("style, link").remove();
  	
  	var font = jQuery(this).val();
  	if( font != -1 ) {
  		$parent.append('<link href="http://fonts.googleapis.com/css?family=' + font + '" rel="stylesheet" type="text/css">');
  		var css_markup = '#bk-googlefont-preview-wrap-' + id + '{';
  		var font_family = font.substring(0, font.indexOf(":"));
  		var font_weight = font.substring(font.indexOf(":") + 1);
  		css_markup += 'font-family:"' + font_family + '";';
  		if( font_weight.indexOf("italic") != -1 ) {
  			css_markup += 'font-style:italic;';
  			font_weight = font_weight.substring(0, font_weight.indexOf("italic"));
  		}

			if( !font_weight.length )
				font_weight = 'normal';	

  		css_markup += 'font-weight:' + font_weight + ';';
  		
  		css_markup += '}';
  		
  		$parent.append('<style rel="stylesheet" type="text/css">' + css_markup + '</style>');
  	}	
	});
	
	jQuery('select.bk-googlefont-select').trigger("change");
  
  /*
   create and handle social icons dialog
  */
  jQuery("div#social_icons_wrap").dialog({
		resizable: true,
		zIndex: 90,
		width: 800,
		height: "auto",
		dialogClass: "bk-dialog-box",
		autoOpen: false,
		modal: false,
		position: 'center',
		buttons: {
			Cancel: function() {
				jQuery( this ).dialog( "close" );
			}
		},
		open: function( event, ui ) {
			bk_show_dialog_overaly();
		},
		close: function( event, ui ) {
			bk_hide_dialog_overaly();
		}
	});
  
  jQuery('table input#bk_social_icons_list').click(function() {
		jQuery("div#social_icons_wrap").dialog( "open" );
		return false;
  });
  
  jQuery('div#social_icons ul.icon_list li').click(function() {
  	var icon_class = jQuery(this).attr("class");
  	
  	var item_markup = '<div class="bk-sortable-item"><div class="bk-sortable-item-inner-wrap"><span class="float-left bk-icon-preview ' + icon_class + '"></span>';
  	item_markup += '<div class="data-fields-wrap float-left"><input type="hidden" name="bk-social-item-icon" value="' + icon_class + '"/>';
  	
  	item_markup += '<table><thead><tr><th class="name"></th><th></th></tr></thead><tbody>';
  	
  	item_markup += '<tr><td>Text <span class="bk-field-description">This text will be shown in tooltip when user hovers over icon e.g follow us on twitter</span></td>';
  	item_markup += '<td><input type="text" name="bk-social-item-text" value=""/></td></tr>';
  	
  	item_markup += '<tr><td>Link <span class="bk-field-description">Specify link for this social icon which can be for example Your facebook or twitter profile page</span></td>';
  	item_markup += '<td><input type="text" name="bk-social-item-link" value=""/></td></tr>';
  	
  	item_markup += '<tr><td>Target <span class="bk-field-description">Specify link target - this window or new tab in browser</span></td>';
  	item_markup += '<td><select name="bk-social-item-link-target"><option value="_blank">Open in new window/tab</option><option value="_self">Open in the same window</option></select></td></tr>';
  	
  	item_markup += '</tbody></table>';
  	
  	item_markup += '</div>';
  	item_markup += '<div class="clearfix"></div><span title="Remove Item" class="remove"></span></div></div>';

  	jQuery("div#social_sortable_list").append(item_markup);
  	
  	init_descriptions(jQuery( "div#social_sortable_list div.bk-sortable-item:last-child span.bk-field-description" ));
  	
  	jQuery("div#social_icons_wrap").dialog( "close" );
  });
  
  jQuery('table input#bk_add_new_sidebar').click(function() {
  	var item_markup = '<div class="bk-sortable-item"><div class="bk-sortable-item-inner-wrap">';
  	item_markup += '<input type="hidden" name="bk-sidebar-id" value=""/>';
  	
  	item_markup += '<table>';
  	
  	item_markup += '<tr><td>Sidebar Name <span class="bk-field-description">This name will be shown in widgets admin page. Use descriptive names like "Blog Left Sidebar" or "Page Sidebar"</span></td>';
  	item_markup += '<td><input type="text" name="bk-sidebar-name" value=""/></td></tr>';
  	
  	item_markup += '</table>';
  	item_markup += '<span title="Remove Sidebar" class="remove"></span></div></div>';

  	jQuery("div#bk_sidebars_list").append(item_markup);
  	
  	init_descriptions(jQuery( "div#bk_sidebars_list div.bk-sortable-item:last-child span.bk-field-description" ));
  });
  
  
  jQuery(document).delegate("div.bk-sortable-item span.remove", "click", function() { 
  	var $item = jQuery(this).parents("div.bk-sortable-item").first();
  	
  	$item.fadeOut('slow', function() {
  			$item.remove();	
  	}); 
  });
  
  /*
   * Reset button like or show counter
   * 
   */
  jQuery('a.bk-reset-button').click(function() {
		var token = jQuery(this).data('token');
		
		var answer = confirm("This will reset counter to its initial state. Are You sure?");
		if (answer){
		jQuery.post(
				bkajax.ajaxurl,
				{
					action : 'bk_reset_counter_ajax',
					token : token,
				},
				function( data ) {
					if( data.success ) {
						alert("Counter reset ok");
					} else {
						alert("Counter reset failed. Try again.");
					}		
				}
			).error(function() { alert("Counter reset failed. Try again."); });
		}	
  }); 
  
  
  /*
   * Handle for removing gallery item via button click
   * 
   */
  
  jQuery("div.bk_gallery_placeholder").delegate("a.bk_remove_gallery_image_button", "click", function () {
		var $count_field = jQuery(this).parents('td').first().find('input.bk_gallery_images_count');
		var count = $count_field.val();
		if( !isNaN( count ) && ( count > 0 ) ) {
				$count_field.val( count - 1 );
		} else {
				count = jQuery(this).parents('div.bk_gallery_placeholder').find("div.bk_gallery_item").length;
				if( count > 0 )
					$count_field.val( count - 1 );
		}
		
		var $gallery = jQuery(this).parents('div.bk_gallery_placeholder');
					
		jQuery(this).parent('div.bk_gallery_item').fadeOut( function() {
		  	jQuery(this).remove();
		  	bk_update_gallery_items_order( $gallery.find("div.bk_gallery_item") );
		});
		return false;
  });
  
  /*
   * Handle gallery item edit button
   * 
   */
  jQuery("div.bk_gallery_placeholder").delegate("a.bk_edit_gallery_image_button", "click", function () {
     $item = jQuery(this).parents("div.bk_gallery_item").first();
     
     bk_edit_gallery_item($item);
  });
  
  
  /*
   * Edit gallery item
   * 
   */
  var $bk_currently_edited_gallery_item = null;
 
  function bk_edit_gallery_item($item) {
 	 var type = $item.find('input[data-dialog_id]').data("dialog_id");		// type of edited block e.g bk_portfolio
 	 $bk_currently_edited_gallery_item = $item;	// jquery object that represents edited object
 	 var dialog_id = '#' + type;				// unique id( selector ) of HTML dom element that represents dialog used to edit settings of this object
 	 
 	 $item.find('input[data-editor-id], textarea[data-editor-id]').each(function(index) {
	 	var field_value = jQuery(this).val();		// value taken from item being edited
	 	var field_type = jQuery(this).data('field-type'); // type of field - can be input, textarea, rich-textarea, slider etc.. different types have different init methods
	 	var editor_id = jQuery(this).data('editor-id');	// id of filed inside dialog that should be populated with value taken from var field_value
	 	
	 	switch( field_type ) {
	 		case 'rich_textarea' :
	 			tinyMCE.get(editor_id).setContent(field_value);
	 		break;
	 		
	 		case 'checkbox' :
	 		case 'checkbox_section':	
	 			if( field_value == 1 )
	 				jQuery('#' + editor_id).attr("checked", "checked");
	 			else
	 				jQuery('#' + editor_id).removeAttr("checked");
	 				
	 			jQuery('#' + editor_id).button("refresh");
	 			if( field_type == 'checkbox_section' ) {
	 				jQuery('#' + editor_id).trigger("change");
	 			}
	 		break;
	 		  
	 		case 'combobox' :
	 		case 'post_combobox':
	 		case 'taxonomy_combobox':	
	 			jQuery('#' + editor_id).val(field_value);
	 		break;
	 		

	 		case 'stacked_combobox' :	
	 			jQuery('#' + editor_id).val(field_value);
	 			jQuery('#' + editor_id).trigger("change");
	 		break;
	 		
	 		case 'multiple_taxonomy_combobox':
	 			jQuery('#' + editor_id).val( field_value.split(",") );
	 		break;
	 		
	 		case 'slider' :
	 			//console.log("slider value " + field_value);
	 			jQuery('#' + editor_id).slider("value", field_value);
	 		break;
	 		
	 		case 'textinput':
	 		case 'simple_browse_button_with_label':
	 			jQuery('#' + editor_id).val( field_value );
	 		break;
	 	}	
 	 });
 	 
 	 jQuery(dialog_id).dialog("open");
  }
/*------------------------------------------------------------------------------ 
Save page builder item
------------------------------------------------------------------------------*/  
function bk_save_gallery_item() {
		$bk_currently_edited_gallery_item.find('input[data-editor-id], textarea[data-editor-id]').each(function(index) {
	 	var field_type = jQuery(this).data('field-type'); // type of field - can be input, textarea, rich-textarea, slider etc.. different types have different init methods
	 	var editor_id = jQuery(this).data('editor-id');	// id of filed inside dialog that should be populated with value taken from var field_value
	 	
	 	switch( field_type ) {
	 		case 'rich_textarea' :
	 			jQuery(this).html(tinyMCE.get(editor_id).getContent());
	 			jQuery(this).val(tinyMCE.get(editor_id).getContent());
	 		break;
	 		
	 		case 'checkbox' :
	 		case 'checkbox_section':
	 			if( jQuery('#' + editor_id).is(":checked") )
	 				jQuery(this).val(1);
	 			else
	 				jQuery(this).val(0);	
	 		break;
	 		  
	 		case 'combobox' :
	 		case 'stacked_combobox' :
	 		case 'post_combobox':
	 		case 'taxonomy_combobox':
	 			jQuery(this).val( jQuery('#' + editor_id).val() );
	 		break;
	 		
	 		case 'multiple_taxonomy_combobox':
	 			if( jQuery('#' + editor_id).val() )
	 					jQuery(this).val( jQuery('#' + editor_id).val().join() );
	 			else 
	 				  jQuery(this).val('');		
	 		break;
	 		
	 		case 'slider' :
	 			jQuery(this).val( jQuery('#' + editor_id).slider("value") );
	 		break;
	 		
	 		case 'textinput':
	 		case 'simple_browse_button_with_label':	
	 			jQuery(this).val( jQuery('#' + editor_id).val() );
	 		break;
	 	}	
	 });
  }	

  
  
  
  /*
   * This function updates all hidden inputs ( which have data-core attribute set ) names inside gallery item 
   * when new item is added, removed or order was changed
   * 
   */
  function bk_update_gallery_items_order( $items ) {
	$items.each( function(index) {
		var $item = jQuery(this);
		$item.find('input[data-core]').each(function(field_index) {
	 	 		var new_field_name = jQuery(this).data('core') + '_' + index;
	 	 		jQuery(this).attr('name', new_field_name);
	 	});
	});
  }	
  
  /*
   * Run sortable on all gallery placeholders
   * 
   */
  jQuery("div.bk_gallery_placeholder").each( function () {
		var $gallery = jQuery(this);
		
		$gallery.sortable({
					forcePlaceholderSize: false,
					placeholder: 'bk-gallery-highlight',
					stop: function(event, ui) { bk_update_gallery_items_order( $gallery.find("div.bk_gallery_item") ); }
		});
  });
  
  
  /*
   * Create gallery item dialogs to edit
   * 
   */
  jQuery("div.bk-gallery-item-editor").each(function(index) {
 	var $parent = jQuery(window);
 	jQuery(this).dialog({
		resizable: true,
		zIndex: 80,
		width: $parent.width() * 0.5,
		height: "auto",
		dialogClass: "bk-dialog-box",
		autoOpen: false,
		modal: false,
		position: 'center',
		buttons: {
			"Save": function() {
				bk_save_gallery_item();
				jQuery( this ).dialog( "close" );
			},
			Cancel: function() {
				jQuery( this ).dialog( "close" );
			}
		},
		open: function( event, ui ) {
		  bk_show_dialog_overaly();
		},
		close: function( event, ui ) {
		  bk_hide_dialog_overaly();
		}
	});
  });
  
  
  /*
   * Bind and init colorpicker function
   * runs colorpicker plugin on element and syncs with hidden input field
   * 
   */
  function bind_and_init_colorpicker( colorpicker ) {
			var value = jQuery( colorpicker ).siblings('input.bk_colorpicker_value').attr('value');
			
			jQuery( colorpicker ).ColorPicker({
							color: value,
							onShow: function (colpkr) {
								jQuery(colpkr).fadeIn(500);
								return false;
							},
							onHide: function (colpkr) {
								jQuery(colpkr).fadeOut(500);
								return false;
							},
							onChange: function (hsb, hex, rgb) {
								jQuery(colorpicker).children('div').css('backgroundColor', '#' + hex);
								jQuery(colorpicker).siblings('input.bk_colorpicker_value').attr('value', '#' + hex);
							}
						});
						
			jQuery(colorpicker).children('div').css('backgroundColor', value );			
	}	
		
	/*
	 * Init all colorpickers
	 * 
	 */
    jQuery('table div.bk-colorpicker').each( 
				function(){
				  bind_and_init_colorpicker( jQuery(this) );
			  }
	);
	
	/*
	 * Init all fancy checkboxes
	 * 
	 */
    jQuery('input:checkbox.fancy_checkbox').each( function(){
		jQuery(this).button();
	});
	
  /*
   * Checkbox toggle section handler
   * 
   */
    
  jQuery('input:checkbox.toggle_section').each( function(){
			var $checkbox = jQuery(this);
			var toggle_count = parseInt($checkbox.data("toggle_count"));
			
			$checkbox.bind('change', function() {
			   toggle_section( $checkbox, toggle_count, 800 );
			});
			
			toggle_section( jQuery(this), toggle_count, 0 );	  
  });
 
  /*
   * Toggle section function. Toggles given amount of section below checkbox
   * 
   */
  function toggle_section( $top_checkbox, toggle_count, speed ) {
  	var $sections = $top_checkbox.parents("tr").first().nextAll().slice(0, toggle_count);
  	
  	if( toggle_count == -1 )
  			$sections = $top_checkbox.parents("tr").first().nextAll();
  	
  	if( $top_checkbox.is(':checked') ) {
  			if( speed == 0 )
  			  $sections.show();
  			else
  				$sections.fadeIn(speed);
  	} else {
  		  if( speed == 0 )
  			  $sections.hide();
  			else
  		  	$sections.fadeOut(speed);
  	}		
  }
  
  /*
   * Image radiobutton click handle  bk-stacked-radio
   * 
   * function stacked_combobox_changed( $combobox ) {
  	 var active_buddy = $combobox.find("option:selected").data("buddy-id");
  	 $combobox.find("option").not(":selected").each( function(){
				  var buddy = jQuery(this).data("buddy-id");
				  jQuery(buddy).hide();
		 });
		 
		 jQuery(active_buddy).stop().show();
  }	
  
  jQuery("select.bk-stacked-combobox").each( function(){
				  stacked_combobox_changed( jQuery(this) );
				  jQuery(this).change(function() { 
				  	 stacked_combobox_changed( jQuery(this) );
				  });
  });
   * 
   * 
   */
  
  jQuery('div.bk-image-radiobutton-wrap img.bk-radio-thumb').click(function() {
			jQuery(this).parent().siblings().find("img.bk-radio-thumb").removeClass("current");  // remove current class from all except clicked
			jQuery(this).parent().siblings().find("input.bk-image-radio").removeAttr("checked");  // remove current class from all except clicked
			
			jQuery(this).addClass("current");
			jQuery(this).siblings("input.bk-image-radio").attr("checked", "checked");
			
			if( jQuery(this).parent().hasClass("bk-stacked-radio") ) {  // extended functionality stacked combobox radiobuttons handle toggling buddy
				jQuery(this).parent().siblings().each( function() {
					var buddy = jQuery(this).data("buddy-id");
					jQuery(buddy).hide();
				});
				
				var active_buddy = jQuery(this).parent().data("buddy-id");
				jQuery(active_buddy).stop().show();
			}
  });
  
  // trigger all stacked image radiobuttons on page load to show proper buddy options
  jQuery('div.bk-image-radiobutton-wrap.bk-stacked-radio img.bk-radio-thumb.current').each(function() {
	  jQuery(this).trigger("click");
  });
  
  /*
   * Init all sliders and sync with hidden input field
   * 
   */
  function bind_and_init_slider( slider ) {
			
			var value = jQuery(slider).parent().siblings('.slider_hidden_value').attr('value');
			
			var min = 0;
			var max = 1000;
			var step = 1;
			var range = "min";
			
			if( jQuery(slider).data("max") != undefined ) {
				  max = jQuery(slider).data("max");
			}	
			
			if( jQuery(slider).data("min") != undefined ) {
				  min = jQuery(slider).data("min");
			}	
			
			if( jQuery(slider).data("step") != undefined ) {
				  step = jQuery(slider).data("step");
			}	
			
			jQuery(slider).slider( {min: min, max: max, step: step, value: value, range: range});
							
			qtip_slider = jQuery( slider );		
			
			jQuery( slider ).bind( 'slidechange slide', function(event, ui) {
				var value = jQuery(this).slider('value');
				
				jQuery(this).parent().siblings('.slider_hidden_value').attr('value', value);
				if( jQuery(this).hasClass('pixel') )
				    {
						 value += " px";
						} 
				else if( jQuery(this).hasClass('percentage') )
					  {
					   value += " %";	
					  }
				else if( jQuery(this).hasClass('time_ms') )
					  {
					   value = ( value / 1000 );
					   value += " s";		
					  }	  			
				
				jQuery(this).parent().siblings('.slider_value').text( value );
				
				
			});
			
		  jQuery(slider).trigger( 'slide' );
	}
	
    /*
     * Run each slider
     * 
     */
	jQuery("div.bk-slider").each( 
				function(){
				  bind_and_init_slider( jQuery(this) );
			  }
	);
	
	
	/*
	 * Init sortable on social icon list
	 * 
	 */
	jQuery("div#social_sortable_list").sortable({
					axis: 'y',
					forcePlaceholderSize: true,
					handle: 'span.bk-icon-preview',
					placeholder: 'bk-state-highlihgt'
	});

	jQuery("div.slide_sortable_list").sortable({
					axis: 'y',
					forcePlaceholderSize: true,
					handle: 'div.bk-slide-featured-image',
					placeholder: 'bk-state-highlihgt'
	});
	
	/*
	 * Handles main thme settings ajax save
	 * 
	 */
	jQuery('a#bk-save-theme-settings').click(function() {
		tinyMCE.triggerSave();
		var form_data = jQuery("form#bk-theme-settings-form").serializeArray();
		
		$ajax_info = jQuery(this).siblings("span.ajax_info").first();
		$ajax_info.addClass("loading");
		
		jQuery.post(
			bkajax.ajaxurl,
			{
				action : 'bk_update_theme_settings_ajax',
				form : form_data
			},
			function( data ) {
				$ajax_info.removeClass("loading");
				jQuery("div.wrap div#bk-notification-wrap").empty().html(data);
				jQuery("div#bk-theme-notification").delay(5000).fadeOut("slow");
			}
		);
	});
  /*
   * Main theme settings tab logic
   * 
   */
  var current_section = jQuery( "#bk-theme-settings-navigation li.bk-submenu-item.current-section > a" ).attr("href");
  jQuery( "#bk-theme-settings-tabs" ).find("div" + current_section).addClass("current").show();
  
  /*
    handle ckick on submenu item - switch beeteeen tabs etc...
  */
  jQuery( "#bk-theme-settings-navigation li.bk-submenu-item > a" ).click( function() {
  	var new_section_id = jQuery(this).attr("href");
  	jQuery( "#bk-theme-settings-navigation li.bk-submenu-item.current-section").removeClass("current-section");
  	jQuery(this).parent("li.bk-submenu-item").addClass("current-section");
  	
  	if( !jQuery(this).parents("li.bk-big-section-tab").hasClass("current-tab") ) {		// if subsection in another tab than current was clicked
  	  jQuery( "#bk-theme-settings-navigation li.bk-big-section-tab.current-tab" ).removeClass("current-tab").find("ul.bk-section-submenu").stop().slideUp();
  	  jQuery(this).parents("li.bk-big-section-tab").addClass("current-tab");
    }
  	
  	jQuery( "#bk-theme-settings-tabs" ).find("div.section.current").fadeOut( function() {
  		jQuery(this).removeClass("current");
  		jQuery( "#bk-theme-settings-tabs" ).find("div" + new_section_id).addClass("current").fadeIn();
  	});
  	return false;
  });
  
  jQuery("#bk-theme-settings-navigation").delegate("li.bk-big-section-tab:not(.current-tab)", "click", function () {
	    jQuery(this).find("ul.bk-section-submenu li.bk-submenu-item > a").first().click();
	});
  
  jQuery("#bk-theme-settings-navigation").delegate("li.bk-big-section-tab:not(.current-tab)", "mouseenter", function () {
	    jQuery(this).find("ul.bk-section-submenu").stop().show();
	});
	
	jQuery("#bk-theme-settings-navigation").delegate("li.bk-big-section-tab:not(.current-tab)", "mouseleave", function () {
	    jQuery(this).find("ul.bk-section-submenu").stop().hide();
	});
  
  //jQuery( "div#bk-theme-settings-tabs" ).tabs({fx: { opacity: 'toggle' }});
  
  
  function init_descriptions(description) {
  	description.qtip({
				content: {
					      text: function(api) {
									// Retrieve content from custom attribute of the $('.selector') elements.
									return jQuery(this).text();
								}
			   		 },	      	    	 
	      position: {
	         my: 'bottom left',
	         at: 'top center',
	         adjust: {
							x: -4,
							y: -2
					 }
	      },
	      show: {
					effect: function(offset) {
						jQuery(this).fadeIn(300); // "this" refers to the tooltip
					}
				},
	      hide: {
	         effect: function(offset) {
						jQuery(this).fadeOut(100); // "this" refers to the tooltip
					}
	      },
	      style: {
	         classes: 'ui-tooltip-shadow ui-tooltip-light ui-tooltip-rounded',
	         tip: {
							mimic: 'bottom center',
							offset: 10 // Give it 5px offset from the side of the tooltip
					 }
	      }
   		});
  }
  
  init_descriptions(jQuery( "span.bk-field-description" ));
  
  function stacked_combobox_changed( $combobox ) {
  	 var active_buddy = $combobox.find("option:selected").data("buddy-id");
  	 $combobox.find("option").not(":selected").each( function(){
				  var buddy = jQuery(this).data("buddy-id");
				  jQuery(buddy).hide();
		 });
		 
		 jQuery(active_buddy).stop().show();
  }	
  
  jQuery("select.bk-stacked-combobox").each( function(){
				  stacked_combobox_changed( jQuery(this) );
				  jQuery(this).change(function() { 
				  	 stacked_combobox_changed( jQuery(this) );
				  });
  });
  
  jQuery(document).delegate("input#bk_shortcode_add_tab, input#bk_shortcode_add_tab_accordion", "click", function() { 
  	var icon_class = 'dribble';
  	
  	var item_markup = '<div class="bk-sortable-item"><div class="bk-sortable-item-inner-wrap">';
  	
  	item_markup += '<table><thead><tr><th class="name"></th><th></th></tr></thead><tbody>';
  	
  	item_markup += '<tr><td>Title <span class="description">Specify title of this tab</span></td>';
  	item_markup += '<td><input type="text" name="title" value=""/></td></tr>';
  	
  	item_markup += '<tr><td>Icon <span class="description">Specify icon for this tab or leave empty to use default global icon if any. You have to type in icon "slug" which is icon name not capitalized with "-" instead of space eg "Icon Pencil 2" => icon-pencil-2 </span></td>';
  	item_markup += '<td><input type="text" name="icon" value=""/></td></tr>';
  	
  	item_markup += '<tr><td>Text <span class="description">Type in text displayed in this tab. You can still place any content inside this tab like other shortcode, images and so on. To do so insert this shortcode place cursor inside desired tab and add any content via shortcode generator or any other way.</span></td>';
  	item_markup += '<td><textarea name="text"></textarea></td></tr>';

  	item_markup += '</tbody></table>';
  	
  	item_markup += '<div class="clearfix"></div><span title="Remove Item" class="remove"></span></div></div>';

  	jQuery("#TB_window div.bk_tabs_sortable_list").append(item_markup);
  	
  	init_descriptions(jQuery( "#TB_window div.bk_tabs_sortable_list div.bk-sortable-item:last-child span.description" ));
  });	
  
  
  function bk_generate_shortcode( $form ) { 
  	var form_data = new Object();
		
	  	$form.find("input, select, textarea").each(function(index) {
	    	if( jQuery(this).is('input[type=checkbox]') ) {
	    		if( jQuery(this).is(":checked") )
	    				form_data[jQuery(this).attr("name")] = 1;
	    		else 
	    				form_data[jQuery(this).attr("name")] = 0;		
	    	} else {	
	    		form_data[jQuery(this).attr("name")] = jQuery(this).val();
	      }
			});
	  
		var shortcode = null;
  	
  	if( $form.hasClass("bk_button") ) {
  		shortcode = '[bk_button ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'text' == key ) {
        	text = value;
        } else if( 'button_custom_colors' == key ) {
        	shortcode += 'button_custom_colors=' + value + ' ';
        }	else if( 'undefined' != key ) {
        	shortcode += key + '="' + value + '" ';
        }	
      });
      
      shortcode += ']' + text + '[/bk_button]';
  	} else if( $form.hasClass("bk_dropcap") ) {
  		shortcode = '[bk_dropcap ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'text' == key ) {
        	text = value;
        }	else if( 'color' == key || 'background_color' == key || 'type' == key ) {
        	shortcode += key + '="' + value + '" ';	
        }	else if( 'dropcap_custom_colors' == key ) {
        	shortcode += 'custom_colors=' + value + ' ';
        } else if( 'undefined' != key ) {
        	shortcode += key + '=' + value + ' ';
        }	
      });
      
      shortcode += ']' + text + '[/bk_dropcap]';
  	} else if( $form.hasClass("bk_big_text") )	{
  		shortcode = '[bk_big_text ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'text' == key ) {
        	text = value;
        }	else if( 'undefined' != key ) {
        	shortcode += key + '="' + value + '" ';
        }	
      });
      
      shortcode += ']' + text + '[/bk_big_text]';
  	} else if( $form.hasClass("bk_highlight") )	{
  		shortcode = '[bk_highlight ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'text' == key ) {
        	text = value;
        }	else if( 'highlight_custom_colors' == key ) {
        	shortcode += 'custom_colors=' + value + ' ';
        } else if( 'undefined' != key ) {
        	shortcode += key + '=' + value + ' ';
        }	
      });
      
      shortcode += ']' + text + '[/bk_highlight]'; 
  	} else if( $form.hasClass("bk_social") )	{
  		shortcode = '[bk_social ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'undefined' != key ) {
        	shortcode += key + '="' + value + '" ';
        }	
      });
      
      shortcode += ' /]';
  	} else if( $form.hasClass("toggles") ) {
  		shortcode = '[toggle ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'text' == key ) {
        	text = value;
        }	else if( 'width' == key ) {
        	shortcode += key + '="' + value + '%" ';
        }	else if( 'icon_toggle' == key ) {
        	shortcode += 'icon="' + value + '" ';
        }	else if( 'undefined' != key ) {
        	shortcode += key + '="' + value + '" ';
        }	
      });
      
      shortcode += ']' + "<p>" + text + "</p>" + '[/toggle]';
  	} else if( $form.hasClass("bk_video") ) {
  		shortcode = '[bk_video ';
	  	jQuery.each(form_data, function(key, value){
	      if( 'undefined' != key  ) {
	        shortcode += key + '="' + value + '" ';
	      }
	    });
	    shortcode += ' /]';	
  	} else if( $form.hasClass("bk_progress_bar") ) {
  		shortcode = '[bk_progress_bar ';
  		var text = '';

  		jQuery.each(form_data, function(key, value){
        if( 'text' == key ) {
        	shortcode += key + '="' + value + '" ';
        } else if( 'progress_bar_custom_colors' == key || 'animated' == key || 'overlay' == key ) {
        	shortcode += key + '=' + value + ' ';
        }	else if( 'undefined' != key ) {
        	shortcode += key + '="' + value + '" ';
        }	
      });
      
      shortcode += ' /]';
  	}  		  					
  	
  	if( shortcode ) {
  		tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcode);
  	}	
  }
  

  jQuery('#bk-settings-footer-wrap form#bk-reset-settings-form').submit(function() {
	  return confirm("All setting will be restored to default values. Are You sure??");
  });
	
  jQuery("div#bk-theme-notification").delay(5000).fadeOut("slow");
  
  jQuery("select.bk-googlefont-select").chosen();
  
  jQuery(document).delegate("div.bk_custom_settings_wrap select.bk-icon-combobox", "change", function(e) { 
  	var icon_class = jQuery(this).val();
  	jQuery(this).siblings("span.bk-icon-preview").attr("class", "bk-icon-preview " + icon_class); 
  });
  
  jQuery(document).delegate("div.bk_custom_settings_wrap select.bk-icon-combobox", "bk_hover", function(e, data) { 
  	var icon_class = data.hovered;
  	jQuery(this).siblings("span.bk-icon-preview").attr("class", "bk-icon-preview " + icon_class); 
  });
  
  
/*----------------------------------------------------------------------------------------------------------------------------------------
  Page Builder 
----------------------------------------------------------------------------------------------------------------------------------------*/

 function bk_show_page_builder() {
		jQuery("div#bk_page_builder_inner_wrap").slideDown();
		jQuery("div#postdivrich").slideUp();
 }
		
 function bk_hide_page_builder() {
 	  jQuery("div#bk_page_builder_inner_wrap").slideUp();
 	  jQuery("div#postdivrich").slideDown();
 }	
 
 function toggle_page_builder( $page_builder_toggle ) {
   if($page_builder_toggle.is(":checked")) {
   	 bk_show_page_builder();
   } else {
   	 bk_hide_page_builder();
   }
 }
 
 toggle_page_builder( jQuery('#_bk_page_builder_enabled') );		
 
 jQuery('#_bk_page_builder_enabled').bind('change', function() {
   toggle_page_builder( jQuery(this) );		
 });

/*------------------------------------------------------------------------------
  Handle add item click
------------------------------------------------------------------------------*/ 
 jQuery('#bk_page_builder_add_item_button').bind('click', function() { 
	 if(jQuery(this).hasClass("bk-ajax-loading")) {
		 return false;
	 }
	 
	 var $button = jQuery(this);
	 $button.addClass("bk-ajax-loading")
	 var item_id = jQuery("#_bk_page_builder_items_combobox").val();
	 var $builder = jQuery("div#bk_page_builder_items_wrap");
 	 var default_size = jQuery("#_bk_page_builder_item_size_combobox").val();
 	 var index = $builder.children("div.bk_page_builder_item").length;
	 jQuery.post(
			bkajax.ajaxurl,
			{
				action : 'add_new_pagebuilder_item_ajax',
				item_id : item_id,
				default_size: default_size,
				index: index
			},
			function( data ) {
				if( data.success ) {
					bk_add_item_to_page_builder( data.markup );
				} else {
					alert("Request failed - item '" + item_id + "' not found!");
				}
				
				$button.removeClass("bk-ajax-loading");
			}
		).error(function(xhr, textStatus, errorThrown) { 
			alert(xhr.responseText + '\n' + textStatus + '\n' + errorThrown);
			$button.removeClass("bk-ajax-loading");
	 });	
 });
 
 /*
  * Adds item to page builder 
  * 
  * @param - (string) - HTML markup of item that need to be inserted into pagebuilder
  * 
  * recalculates sizes of each item in pagebuilder and updates order suffix of input fields
  * 
  */
 
 function bk_add_item_to_page_builder( markup ) {
	 jQuery(markup).hide().appendTo('div#bk_page_builder_items_wrap').fadeIn();
 	 bk_recalculate_sizes();
 	 bk_update_order_suffix();	
 }

/*------------------------------------------------------------------------------
  Handle page builder item buttons
------------------------------------------------------------------------------*/ 
 
 jQuery("div#bk_page_builder_items_wrap").delegate("div.bk_page_builder_block span.bk_enlarge_builder_item", "click", function() {
	if( bk_is_page_builder_during_ajax() ) {
		return false;
	}
	 
	bk_change_item_size( jQuery(this).parent("div.bk_page_builder_block"), 1 );
    return false;
 });
 
 jQuery("div#bk_page_builder_items_wrap").delegate("div.bk_page_builder_block span.bk_shrink_builder_item", "click", function() {
	if( bk_is_page_builder_during_ajax() ) {
		return false;
	}
	 
	bk_change_item_size( jQuery(this).parent("div.bk_page_builder_block"), -1 );
 	return false;
 });
 
 jQuery("div#bk_page_builder_items_wrap").delegate("div.bk_page_builder_block span.bk_edit_builder_item", "click", function() {
	if( bk_is_page_builder_during_ajax() ) {
		return false;
	}
	 
	var $item = jQuery(this).parent("div.bk_page_builder_block");
	bk_edit_page_builder_item($item);
 	return false;
 });
 
 jQuery("div#bk_page_builder_items_wrap").delegate("div.bk_page_builder_block span.bk_remove_builder_item", "click", function() {
	if( bk_is_page_builder_during_ajax() ) {
		return false;
	}
	 
	if( confirm("Click 'OK' to remove this block") ) {
		jQuery(this).parent("div.bk_page_builder_block").fadeOut( function() {
			jQuery(this).remove();
			bk_recalculate_sizes();
			bk_update_order_suffix();
		});
    }
 	return false;
 });
 
/*
 * Helper function - checks if page builder is during ajax call to request new item
 * if so other actions are forbidden like editing items, changing size and removing - just to be
 * 100% sure that this doesn't break anything
 * 
 * retunrs true or false
 */ 
 function bk_is_page_builder_during_ajax() {
	 return jQuery('#bk_page_builder_add_item_button').hasClass("bk-ajax-loading"); 
 } 		 

/*------------------------------------------------------------------------------ 
  Edit page builder item
------------------------------------------------------------------------------*/ 
 var $bk_currently_edited_page_builder_item = null;
 
 function bk_edit_page_builder_item($item) {
 	 var type = $item.find('input[data-core="bk_page_builder_item_type"]').val();		// type of edited block e.g bk_portfolio
 	 $bk_currently_edited_page_builder_item = $item;	// jquery object that represents edited object
 	 var dialog_id = '#' + type + '_editor';	// unique id( selector ) of HTML dom element that represents dialog used to edit settings of this object
 	 
 	 jQuery(dialog_id).dialog("open");
 	 
 	 $item.find('input[data-editor-id], textarea[data-editor-id]').each(function(index) {
 	 	 	var field_value = jQuery(this).val();		// value taken from item being edited
 	 	 	var field_type = jQuery(this).data('field-type'); // type of field - can be input, textarea, rich-textarea, slider etc.. different types have different init methods
 	 	 	var editor_id = jQuery(this).data('editor-id');	// id of filed inside dialog that should be populated with value taken from var field_value
 	 	 	
 	 	 	switch( field_type ) {
 	 	 		case 'rich_textarea' :
 	 	 			tinyMCE.get(editor_id).setContent(field_value);
 	 	 		break;
 	 	 		
 	 	 		case 'checkbox' :
 	 	 		case 'checkbox_section':	
 	 	 			if( field_value == 1 )
 	 	 				jQuery('#' + editor_id).attr("checked", "checked");
 	 	 			else
 	 	 				jQuery('#' + editor_id).removeAttr("checked");
 	 	 				
 	 	 			jQuery('#' + editor_id).button("refresh");		
 	 	 		break;
 	 	 		  
 	 	 		case 'combobox' :
 	 	 		case 'stacked_combobox' :
 	 	 		case 'post_combobox':
 	 	 		case 'taxonomy_combobox':	
 	 	 			jQuery('#' + editor_id).val(field_value);
 	 	 		break;
 	 	 		
 	 	 		case 'multiple_taxonomy_combobox':
 	 	 			jQuery('#' + editor_id).val( field_value.split(",") );
 	 	 		break;
 	 	 		
 	 	 		case 'slider' :
 	 	 			//console.log("slider value " + field_value);
 	 	 			jQuery('#' + editor_id).slider("value", field_value);
 	 	 		break;
 	 	 		
 	 	 		case 'textinput':
 	 	 			jQuery('#' + editor_id).val( field_value );
 	 	 		break;
 	 	 	}	
 	 });
 }
/*------------------------------------------------------------------------------ 
  Save page builder item
------------------------------------------------------------------------------*/  
 function bk_save_page_builder_item() {
 	 $bk_currently_edited_page_builder_item.find('input[data-editor-id], textarea[data-editor-id]').each(function(index) {
 	 	 	var field_type = jQuery(this).data('field-type'); // type of field - can be input, textarea, rich-textarea, slider etc.. different types have different init methods
 	 	 	var editor_id = jQuery(this).data('editor-id');	// id of filed inside dialog that should be populated with value taken from var field_value
 	 	 	
 	 	 	switch( field_type ) {
 	 	 		case 'rich_textarea' :
 	 	 			jQuery(this).html(tinyMCE.get(editor_id).getContent());
 	 	 			jQuery(this).val(tinyMCE.get(editor_id).getContent());
 	 	 		break;
 	 	 		
 	 	 		case 'checkbox' :
 	 	 		case 'checkbox_section':
 	 	 			if( jQuery('#' + editor_id).is(":checked") )
 	 	 				jQuery(this).val(1);
 	 	 			else
 	 	 				jQuery(this).val(0);	
 	 	 		break;
 	 	 		  
 	 	 		case 'combobox' :
 	 	 		case 'stacked_combobox' :
 	 	 		case 'post_combobox':
 	 	 		case 'taxonomy_combobox':
 	 	 			jQuery(this).val( jQuery('#' + editor_id).val() );
 	 	 		break;
 	 	 		
 	 	 		case 'multiple_taxonomy_combobox':
 	 	 			if( jQuery('#' + editor_id).val() )
 	 	 					jQuery(this).val( jQuery('#' + editor_id).val().join() );
 	 	 			else 
 	 	 				  jQuery(this).val('');		
 	 	 		break;
 	 	 		
 	 	 		case 'slider' :
 	 	 			jQuery(this).val( jQuery('#' + editor_id).slider("value") );
 	 	 		break;
 	 	 		
 	 	 		case 'textinput':
 	 	 			jQuery(this).val( jQuery('#' + editor_id).val() );
 	 	 		break;
 	 	 	}	
 	 });
 }	

/*------------------------------------------------------------------------------  
  Create all settings dialgs for page builder items
------------------------------------------------------------------------------ */
  
 jQuery("div.bk-page-builder-editor").each(function(index) {
 	var $parent = jQuery(window);
 	jQuery(this).dialog({
		resizable: true,
		zIndex: 80,
		width: $parent.width() * 0.5,
		height: "auto",
		dialogClass: "bk-dialog-box",
		autoOpen: false,
		modal: false,
		position: 'center',
		buttons: {
			"Save": function() {
				bk_save_page_builder_item();
				jQuery( this ).dialog( "close" );
			},
			Cancel: function() {
				jQuery( this ).dialog( "close" );
			}
		},
		open: function( event, ui ) {
		  bk_show_dialog_overaly();
		},
		close: function( event, ui ) {
			bk_hide_dialog_overaly();
		}
	});
 });
 
 /*
   Creates and add overlay for page builder elements editor because modal dialog was freezing all inputs on page - medial library inputs
   making unable to change attachement size or add caption to image. Now it's not modal but overlay have to be created manually.
 */
 
 var $overlay = jQuery('<div id="bk-modal-dialog" class="bk-fake-modal-dialog-overlay ui-widget-overlay"></div>').hide().appendTo('body');
 $overlay.css({"z-index" : "79"});
 
 var $shortcode_overlay = jQuery('<div id="bk-shortcode-modal-dialog" class="bk-fake-modal-dialog-overlay ui-widget-overlay"></div>').hide().appendTo('body');
 $shortcode_overlay.css({"z-index" : "89"});
 
 jQuery(window).resize(function(){
     bk_resize_dialog_overlay();
     bk_resize_shortcode_overlay();
 });
 
 function bk_show_dialog_overaly() {
 		 bk_resize_dialog_overlay();
 		 jQuery('#bk-modal-dialog').show();
 }
 
 function bk_hide_dialog_overaly() {
 		 jQuery('#bk-modal-dialog').hide();
 }		
 
 function bk_resize_dialog_overlay() {
 	jQuery('#bk-modal-dialog').width(jQuery(document).width());
    jQuery('#bk-modal-dialog').height(jQuery(document).height());
 }
 
 function bk_show_shortcode_overaly() {
	 bk_resize_shortcode_overlay();
	 jQuery('#bk-shortcode-modal-dialog').show();
 }

 function bk_hide_shortcode_overaly() {
	 jQuery('#bk-shortcode-modal-dialog').hide();
 }
 
 function bk_resize_shortcode_overlay() {
	 jQuery('#bk-shortcode-modal-dialog').width(jQuery(document).width());
	 jQuery('#bk-shortcode-modal-dialog').height(jQuery(document).height()); 
 }
 
 /*
   Modal dialog for shortcode generator
 */
 
 jQuery("div.bk-shortcode-generator-editor").each(function(index) {
 	var $parent = jQuery(window);
 	jQuery(this).dialog({
		resizable: true,
		zIndex: 90,
		width: $parent.width() * 0.5,
		height: "auto",
		dialogClass: "bk-dialog-box bk-shortcode-generator-box",
		autoOpen: false,
		modal: false,
		position: 'center',
		buttons: {
			"Add Shortcode": function() {
				bk_generate_shortcode( jQuery( this ).find(".bk_form_wrap") );
				jQuery( this ).dialog( "close" );
			},
			Cancel: function() {
				jQuery( this ).dialog( "close" );
			}
		},
		open: function( event, ui ) {
			bk_show_shortcode_overaly();
		},
		close: function( event, ui ) {
			bk_hide_shortcode_overaly();
		}
	});
 });

/*------------------------------------------------------------------------------
 Helper function converts size to string
------------------------------------------------------------------------------*/

function bk_size_to_string( size ) {
	 var sizes = new Object();
	 sizes['one_one'] = '1 / 1';
	 sizes['one_half'] = '1 / 2';
	 sizes['one_third'] = '1 / 3';
	 sizes['two_third'] = '2 / 3';
	 sizes['three_fourth'] = '3 / 4';
	 sizes['one_fourth'] = '1 / 4';
	 sizes['one_fifth'] = '1 / 5';
	 sizes['two_fifth'] = '2 / 5';
	 sizes['three_fifth'] = '3 / 5';
	 sizes['four_fifth'] = '4 / 5';
	 sizes['one_sixth'] = '1 / 6';
	 sizes['five_sixth'] = '5 / 6';
	 
	 return sizes[size];
}
 
/*------------------------------------------------------------------------------ 
  Change item size function
------------------------------------------------------------------------------ */  
 
 function bk_change_item_size( $item, direction ) {
 	 var sizes = new Array('one_sixth','one_fifth','one_fourth','one_third','two_fifth','one_half','three_fifth','two_third','three_fourth','four_fifth','five_sixth','one_one');
 	 var current_size = $item.find('input[data-core="bk_page_builder_item_size"]').val();
 	 var current_index = jQuery.inArray(current_size, sizes);
 	 var new_index = current_index + direction;
 	 
 	 if( new_index > ( sizes.length - 1 ) ) {
 	 		 new_index = 0;
 	 } else if ( new_index < 0 ) {
 	 	   new_index = sizes.length - 1;
 	 }	      
 	 
 	 var new_size = sizes[new_index];
 	 $item.find('input[data-core="bk_page_builder_item_size"]').val(new_size);
 	 $item.removeClass(current_size).addClass(new_size);
 	 bk_recalculate_sizes();
 	 	
 	 $item.find("span.bk_size_palceholder").text( bk_size_to_string( new_size ) );   		 
 }	

/*------------------------------------------------------------------------------ 
  Update order suffix - updates suffix of each page builder item input field which is sent to database during save - this suffix defines order of rendered item on page
------------------------------------------------------------------------------ */ 
 function bk_update_order_suffix() {
 	 var $all_items = jQuery("div#bk_page_builder_items_wrap").children("div.bk_page_builder_block:not(div.ui-sortable-helper)");
 	 $all_items.each(function(index) {
 	 	 var $item = jQuery(this);
 	 	 var item_type = $item.find('input[data-core="bk_page_builder_item_type"]').val();
 	 	 var item_index = index;
 	 	 // TO DO - add exception for more complex elements such as tabs, accordions etc...
 	 	 $item.find('input[data-core], textarea[data-core]').each(function(index) {
 	 	 		var new_field_name = '_' + jQuery(this).data('core') + '_' + item_index;
 	 	 		jQuery(this).attr('name', new_field_name);
 	 	 });
 	 });
 	  	 
 	 jQuery("input#_bk_page_builder_items_count").val($all_items.length);
 }
/*------------------------------------------------------------------------------ 
  Recalculate sizes of elements - when added, removed or size of element changed
------------------------------------------------------------------------------ */  
 var bk_current_dragable_item_size = 'one_third';
 // used to calculate where item with 'last' classes should be placed
 function bk_recalculate_sizes() {
 	 var sizes = new Object();
 	 sizes['one_one'] = 1;
 	 sizes['one_half'] = 1 / 2;
 	 sizes['one_third'] = 1 / 3;
 	 sizes['two_third'] = 2 / 3;
 	 sizes['three_fourth'] = 3 / 4;
 	 sizes['one_fourth'] = 1 / 4;
 	 sizes['one_fifth'] = 1 / 5;
 	 sizes['two_fifth'] = 2 / 5;
 	 sizes['three_fifth'] = 3 / 5;
 	 sizes['four_fifth'] = 4 / 5;
 	 sizes['one_sixth'] = 1 / 6;
 	 sizes['five_sixth'] = 5 / 6;
 	 
 	 var total_width = 0;
 	 
 	 jQuery("div#bk_page_builder_items_wrap").children("div.bk_page_builder_block:not(div.ui-sortable-helper)").each(function(index) {
     var size = jQuery(this).find('input[data-core="bk_page_builder_item_size"]').val();
     var current_width = sizes[size];  		 
     if( jQuery(this).hasClass("bk-state-highlight") ) {
     	current_width = sizes[bk_current_dragable_item_size];
     	//console.log("current_width: " + current_width);
     }	
     
     if( total_width == 0 ) {
     	 jQuery(this).addClass('first');
     } else {	
       jQuery(this).removeClass('first');
     } 
     
     total_width += current_width;
     
     if( ( total_width > 0.84 ) && ( total_width <= 1 )  ) {
     	 total_width = 0;
     	 jQuery(this).addClass('last');
     } else {	
       jQuery(this).removeClass('last');
     }
     
     jQuery(this).find('input[data-core="bk_page_builder_item_position"]').val("");
     
     if( jQuery(this).hasClass("first") )
     		 jQuery(this).find('input[data-core="bk_page_builder_item_position"]').val("first");
     		 
     if( jQuery(this).hasClass("last") )
     		 jQuery(this).find('input[data-core="bk_page_builder_item_position"]').val("last");		   
	 });
 }	

/*------------------------------------------------------------------------------ 
  Sortable items handler
------------------------------------------------------------------------------ */  
 jQuery("div#bk_page_builder_items_wrap").sortable({
				 forcePlaceholderSize: true,
				 placeholder: 'bk-state-highlight bk_page_builder_block',
				 stop: function(event, ui) { bk_recalculate_sizes(); bk_update_order_suffix(); },
				 change: function(event, ui) { bk_recalculate_sizes(); },
				 sort: function(event, ui) { bk_recalculate_sizes(); },
				 start: function(event, ui) { bk_current_dragable_item_size = ui.item.find('input[data-core="bk_page_builder_item_size"]').val(); }
 });

/*------------------------------------------------------------------------------ 
  Init page builder items
-------------------------------------------------------------------------------*/	
 bk_recalculate_sizes();



/*-------------------------------------------------------------------------------
  Pricing Tables
-------------------------------------------------------------------------------*/
 jQuery("div#bk_pricing_table_custom_settings").delegate("div.bk_add_pricing_table_column", "click", function() {
	var items_count = jQuery("div#bk_pricing_table_wrap").children("div.bk_pricing_table_column_wrap").length;
	
	bk_add_column_to_pricng_table( items_count );
 	
 	jQuery('input[name="_bk_pricing_table_column_count"]').val( items_count + 1 );
 	return false;
 });
 
 jQuery("div#bk_pricing_table_custom_settings").delegate("a.bk_pricing_column_remove_feature", "click", function() {
	jQuery(this).parents("div.bk_pricing_table_feature").first().fadeOut('slow', function() {
	  var features_count = jQuery(this).parents("div.bk_pricing_table_features_wrap").first().children('div.bk_pricing_table_feature').length - 1; // !!!! it hs to be here
	  jQuery(this).parents("div.bk_pricing_table_column_inner_wrap").first().find('input[data-core="bk_pricing_table_column_features_count"]').val(features_count);
	  
	  jQuery(this).remove();
	  bk_update_pricing_table_order_suffix();
	});

 	return false;
 });
 
 jQuery("div#bk_pricing_table_custom_settings").delegate("a.bk_pricing_table_remove_column", "click", function() {
	jQuery(this).parents("div.bk_pricing_table_column_wrap").first().fadeOut('slow', function() {
	  jQuery(this).remove();
	  bk_update_pricing_table_order_suffix();
	  var items_count = jQuery("div#bk_pricing_table_wrap").children("div.bk_pricing_table_column_wrap").length;
	  jQuery('input[name="_bk_pricing_table_column_count"]').val( items_count );
	});
	
 	return false;
 });
 
 jQuery("div#bk_pricing_table_custom_settings").delegate("a.bk_pricing_table_add_feature", "click", function() {
	var $parent_wrap = jQuery(this).parents("div.bk_pricing_table_column_wrap");
	var column_index = $parent_wrap.index();
	
	var $features_wrap = $parent_wrap.find('div.bk_pricing_table_features_wrap');
	var features_count = $features_wrap.find('input[type="text"]').length;
	
	var markup = '<div class="bk_pricing_table_feature">';
	markup += '<span class="bk_pricing_column_feature_handle"></span>';
	markup += '<input type="text" data-core="bk_pricing_table_feature" name="_bk_pricing_table_feature_' + column_index + '_' + features_count +'" value="" placeholder="Feature" title="Feature">';
	markup += '<a title="Remove Feature" class="bk_pricing_column_remove_feature"></a>';
	markup += '</div>';

	$parent_wrap.find('input[data-core="bk_pricing_table_column_features_count"]').val(features_count + 1);
	jQuery(markup).hide().appendTo($features_wrap).fadeIn();
	
 	return false;
 });
 
 function bk_add_column_to_pricng_table( count_suffix ) {
 	var markup;
 	
 	var radio_checked = '';
 	if( count_suffix == 0 ) {
 		radio_checked = 'checked="checked"';
 	}	
 	
 	markup += '<div class="bk_pricing_table_column_wrap">';
 	markup += '<div class="bk-pricing-table-titlebar">Column<a title="Remove Column" class="bk_pricing_table_remove_column"></a></div>';
 	markup += '<div class="bk_pricing_table_column_inner_wrap">';
 	markup += '<label><input ' + radio_checked + ' type="radio" name="_bk_pricing_table_column_recommended" value="' + count_suffix + '">Recommended</label>';
 	markup += '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_title" name="_bk_pricing_table_column_title_' + count_suffix + '" value="" placeholder="Title" title="Title">';
 	markup += '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_price" name="_bk_pricing_table_column_price_' + count_suffix + '" value="" placeholder="Price" title="Price">';
 	markup += '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_description" name="_bk_pricing_table_column_description_' + count_suffix + '" value="" placeholder="Description" title="Description">';
 	markup += '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_button_text" name="_bk_pricing_table_column_button_text_' + count_suffix + '" value="" placeholder="Button Text" title="Button Text">';
 	markup += '<input class="bk_column_global_setting" type="text" data-core="bk_pricing_table_column_button_link" name="_bk_pricing_table_column_button_link_' + count_suffix + '" value="" placeholder="Button Link" title="Button Link">';
 	markup += '<input class="bk_column_global_setting" type="hidden" data-core="bk_pricing_table_column_features_count" name="_bk_pricing_table_column_features_count_' + count_suffix + '" value="0">';
 	markup += '<div class="bk_pricing_features_title_bar clearfix">Features<a class="bk_pricing_table_add_feature">Add</a></div>';
 	markup += '<div class="bk_pricing_table_features_wrap"></div>';
 	
 	markup += '</div>';
 	markup += '</div>';
 	
 	jQuery(markup).hide().appendTo('div#bk_pricing_table_wrap').fadeIn();
 	
 	bk_init_features_sortable( jQuery("div.bk_pricing_table_column_wrap:last-child div.bk_pricing_table_features_wrap") );
 }
 
/*------------------------------------------------------------------------------ 
  Sortable main column handler
------------------------------------------------------------------------------ */  
 jQuery("div#bk_pricing_table_wrap").sortable({
				 forcePlaceholderSize: true,
				 placeholder: 'bk-column-state-highlight',
				 handle: '.bk-pricing-table-titlebar',
				 stop: function(event, ui) { bk_update_pricing_table_order_suffix(); },
				 change: function(event, ui) {},
				 sort: function(event, ui) {}
 });	
 
 function bk_init_features_sortable( $features_wrap ) {
	 $features_wrap.sortable({
					 forcePlaceholderSize: true,
					 handle: 'span.bk_pricing_column_feature_handle',
					 axis: 'y',
					 stop: function(event, ui) { bk_update_pricing_table_order_suffix(); },
					 change: function(event, ui) {},
					 sort: function(event, ui) {}
	 });
 }
 
 	jQuery("div.bk_pricing_table_features_wrap").each( function() {
 	  bk_init_features_sortable( jQuery(this) );	
 	});
 
/*------------------------------------------------------------------------------ 
 Update order suffix for pricing table columns
------------------------------------------------------------------------------ */ 
function bk_update_pricing_table_order_suffix() {
	jQuery("div#bk_pricing_table_wrap").find("div.bk_pricing_table_column_wrap").each( function( column_index ) {
		var $column = jQuery(this);
		$column.find("input.bk_column_global_setting").each( function() {
			var new_field_name = '_' + jQuery(this).data('core') + '_' + column_index;
 	 	 	console.log("new name: " + new_field_name);
 	 	 	jQuery(this).attr('name', new_field_name);
		});
		
		$column.find('input[name="_bk_pricing_table_column_recommended"]').val(column_index);

		$column.find('div.bk_pricing_table_features_wrap input[type="text"]').each( function( feature_index ) {
			var new_field_name = '_' + jQuery(this).data('core') + '_' + column_index + '_' + feature_index;
 	 	 	jQuery(this).attr('name', new_field_name);
		});
	});	
}	 
});

