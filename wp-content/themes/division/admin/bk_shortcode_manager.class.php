<?php
/*
 * Bk_Shortcode_Manager singleton class
 * 
 * responsible for creating editor for all defined shortcodes
 * 
 */

class Bk_Shortcode_Manager {
	private static $instance;  // reference to singleton class
	private $items;		// all items available in pagebuilder
	
	/*
	 * Get instance of singleton Bk_Shortcode_Manager class
	*
	* this method is used to get reference to singleton and call other functions
	* if it's being called first time during application it creates new instance of
	* Bk_Shortcode_Manager class
	* usage Bk_Shortcode_Manager::getInstance()->somefunction();
	*
	* @param - void
	* @return - (Bk_Shortcode_Manager class) instance
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
	* $items contains all shortcode manager availabel blocks along with descriptions, filed type etc..
	* it is used to generate proper editors for shortcode manager
	*
	* id of each module is also a shortcode that will be genrated and handled inside shortcodes.php file
	* e.g for "button" id is "bk_button" thus generated shortcode will be [bk_button ... /] where
	* each one of fileds will be attribute or value of this shortcode e.g for color field it will result
	* with following scenario - [bk_button color="#ffffff" /]
	*
	* @params - void
	* @return - void
	*
	*/
	
	private function __construct() {
		$this->items = array(
				'bk_dropcap' => array(
						'name' => __('Dropcap', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Character', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify dropcap character, usually first letter of first word in paragraph or number.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Type', 'corpora_theme' ),
										'id' => 'type',
										'description' => __( 'Choose dropcap type. Round siuts number and plain characters but it is not a rule You have to follow.d.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'round',
										'all_values' => array('plain' => __( 'Plain', 'corpora_theme' ),
															  'round' => __( 'Round', 'corpora_theme' ),
															  'square' => __( 'Square', 'corpora_theme' ) 
										)
								),
								array(
										'name' => __( 'Custom Colors', 'corpora_theme' ),
										'id' => 'dropcap_custom_colors',
										'description' => __( 'If enabled custom colors will be applied to dropcap otherwise colors will siut theme globl settings.', 'corpora_theme' ),
										'type' => 'checkbox_section',
										'rows' => 2,
										'default' => false,
								),
								array(
										'name' => __( 'Text Color', 'corpora_theme' ),
										'id' => 'color',
										'description' => __( 'Choose dropcap text color. Default for round dropcaps is white ( #FFFFFF ) and for plain is #404040.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#FFFFFF"
								),
								array(
										'name' => __( 'Background Color', 'corpora_theme' ),
										'id' => 'background_color',
										'description' => __( 'Choose background dropcap color. This setting only takes effect for round dropcap.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#404040"
								)
						)
				),
				'bk_video' => array(
						'name' => __('Video', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Link', 'corpora_theme' ),
										'id' => 'video_link',
										'description' => __( 'Specify link to youtube or vimeo video.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Width', 'corpora_theme' ),
										'id' => 'width',
										'description' => __( 'Choose original width of video e.g 400. This setting along with height will calculate aspect ratio of video so You can specify units e.g for 4:3 aspect ratio video set width to 4 and height to 3.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 800,
										'min' => 0,
										'max' => 1920,
										'step' => 1
								),
								array(
										'name' => __( 'Height', 'corpora_theme' ),
										'id' => 'height',
										'description' => __( 'Choose original width of video e.g 300. This setting along with width will calculate aspect ratio of video so You can specify units e.g for 4:3 aspect ratio video set width to 4 and height to 3.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 600,
										'min' => 0,
										'max' => 1080,
										'step' => 1
								)
						)
				),
				'bk_big_text' => array(
						'name' => __('Big Text', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Text', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify big text. e.g 100%. If You want to change color or style of text use wordrpess editor.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Size', 'corpora_theme' ),
										'id' => 'size',
										'description' => __( 'Choose size of text described in pixels.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 60,
										'min' => 1,
										'max' => 200,
										'step' => 1
								)
						)
				),
				'bk_highlight' => array(
						'name' => __('Highlight', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Text', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify highlighted text.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Custom Colors', 'corpora_theme' ),
										'id' => 'highlight_custom_colors',
										'description' => __( 'If enabled custom colors will be applied to highlight otherwise colors will siut theme globl settings.', 'corpora_theme' ),
										'type' => 'checkbox_section',
										'rows' => 2,
										'default' => false,
								),
								array(
										'name' => __( 'Text Color', 'corpora_theme' ),
										'id' => 'color',
										'description' => __( 'Choose highlight text color.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#FFFFFF"
								),
								array(
										'name' => __( 'Background Color', 'corpora_theme' ),
										'id' => 'background_color',
										'description' => __( 'Choose highlight background color.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#404040"
								)
						)
				),
				'bk_social' => array(
						'name' => __('Social Icon', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Icon', 'corpora_theme' ),
										'id' => 'icon',
										'description' => __( 'Choose social icon.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => '',
										'all_values' => array( 'aim' => 'aim',
															   'behance' => 'behance',
															   'blogger' => 'blogger',
															   'digg' => 'digg',
															   'dribble' => 'dribble',
															   'ember' => 'ember',
															   'evernote' => 'evernote',
															   'facebook' => 'facebook',
															   'flickr' => 'flickr',
															   'forrst' => 'forrst',
															   'github' => 'github',
															   'google' => 'google',
															   'last-fm' => 'last-fm',
															   'linkedin' => 'linkedin',
															   'paypal' => 'paypal',
															   'quora' => 'quora',
															   'rss' => 'rss',
															   'share-this' => 'share-this',
															   'skype' => 'skype',
															   'tumblr' => 'tumblr',
															   'twitter' => 'twitter',
															   'vimeo' => 'vimeo',
															   'yahoo' => 'yahoo',
															   'you-tube' => 'you-tube',
															   'pinterest' => 'pinterest',
															   'email' => 'email'
										)
								),
								array(
										'name' => __( 'Text', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify hover text for icon e.g Follow Us!.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Link', 'corpora_theme' ),
										'id' => 'link',
										'description' => __( 'Specify link for social icon e.g http://www.twitter.com/account/xyz.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Target', 'corpora_theme' ),
										'id' => 'target',
										'description' => __( 'Choose how link should be opened for scial icon.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => '_self',
										'all_values' => array( '_self' => __( 'Open in the same window', 'corpora_theme' ),
															  '_blank' => __( 'Open in new window/tab', 'corpora_theme' ),
										)
								)
						)
				),
				'bk_progress_bar' => array(
						'name' => __('Highlight', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Text', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify progressbar text e.g "Progress - ".', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => 'Progress - '
								),
								array(
										'name' => __( 'Progress', 'corpora_theme' ),
										'id' => 'progress',
										'description' => __( 'Choose percentage progress of progress bar.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'percent',
										'default' => 50,
										'min' => 0,
										'max' => 100,
										'step' => 1
								),
								array(
										'name' => __( 'Animated', 'corpora_theme' ),
										'id' => 'animated',
										'description' => __( 'If enabled progressbar will load up with smooth animation from zero to maximum progress once visible in viewport, otherwise its progress ( width ) will be set on pageload.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => true,
								),
								array(
										'name' => __( 'Overlay', 'corpora_theme' ),
										'id' => 'overlay',
										'description' => __( 'If enabled progress bar will have animated scrolling bar overlay applied, otherwise it will be clean.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => true,
								),
								array(
										'name' => __( 'Custom Colors', 'corpora_theme' ),
										'id' => 'progress_bar_custom_colors',
										'description' => __( 'If enabled custom colors will be applied to progress bar otherwise colors will siut theme globl settings.', 'corpora_theme' ),
										'type' => 'checkbox_section',
										'rows' => 2,
										'default' => false,
								),
								array(
										'name' => __( 'Color', 'corpora_theme' ),
										'id' => 'color',
										'description' => __( 'Choose progress bar color.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#FF4629"
								),
								array(
										'name' => __( 'Background Color', 'corpora_theme' ),
										'id' => 'background_color',
										'description' => __( 'Choose progressbar background color.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#f8f8f8"
								)
						)
				),
				'bk_button' => array(
						'name' => __('Button', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Text', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify text for button e.g "Click Me"', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Link', 'corpora_theme' ),
										'id' => 'link',
										'description' => __( 'Specify link for button e.g "http://www.google.com"', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => ''
								),
								array(
										'name' => __( 'Target', 'corpora_theme' ),
										'id' => 'target',
										'description' => __( 'Choose button link target.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => '_self',
										'all_values' => array('_self' => __( 'Open link in the same window', 'corpora_theme' ),
															  '_blank' => __( 'Open link in new window/tab', 'corpora_theme' )
										)
								),
								array(
										'name' => __( 'Size', 'corpora_theme' ),
										'id' => 'size',
										'description' => __( 'Choose button size.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'round',
										'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
															  'normal' => __( 'Normal', 'corpora_theme' ),
															  'large' => __( 'Large', 'corpora_theme' )
										)
								),
								array(
										'name' => __( 'Custom Colors', 'corpora_theme' ),
										'id' => 'button_custom_colors',
										'description' => __( 'If enabled custom colors will be applied to button otherwise colors will siut theme globl settings.', 'corpora_theme' ),
										'type' => 'checkbox_section',
										'rows' => 2,
										'default' => false,
								),
								array(
										'name' => __( 'Color', 'corpora_theme' ),
										'id' => 'color',
										'description' => __( 'Choose button color.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#909090"
								),
								array(
										'name' => __( 'Hover Color', 'corpora_theme' ),
										'id' => 'hover_color',
										'description' => __( 'Choose button hover color.', 'corpora_theme' ),
										'type' => 'simple_colorpicker',
										'default' => "#27CCC0"
								)
						)
				),
		);		
	}
	
	
	/*
	 * Prints out editors for each shortcode
	 * @param - void
	 * @return - ()
	 *
	 */
	
	public function print_shortcode_manager_editors() {
	
		echo '<div class="hiddenn">'; // wrap it up and hide
	
		foreach( $this->items as $item_id => $item) {  // loop through all shortcode items
			$editor_id = $item_id . '_editor'; // create unique editor id
				
			echo '<div id="' . $editor_id . '" class="bk-shortcode-generator-editor" title="'. $item['name'] .'">';
			echo '<table class="bk_form_wrap ' . $item_id . '">';
				
			foreach( $item['fields'] as $item_field ) {
				Bk_Field_Factory::getInstance()->print_setting_field($item_field, $item_field['default']);
			}
				
			echo '</table>';
			echo '</div>';
		}
	
		echo '</div>'; // end of wrap
	}
}	
?>