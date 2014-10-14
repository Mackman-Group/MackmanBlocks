<?php
/**
 * Creates control with media upload popup box ability to change current image or upload new
 */
/*
 class Bk_Customize_Image_Control extends WP_Customize_Control {
public $type = 'bk_image';
public $statuses;

public function __construct( $manager, $id, $args = array() ) {
$this->statuses = array( '' => __('Default') );
parent::__construct( $manager, $id, $args );
}

public function enqueue() {
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');


wp_enqueue_script(
		'bk_customize_controls', //Give the script an ID
		get_template_directory_uri().'/admin/js/bk_customize_controls.js', //Define it's JS file
		array( 'jquery','media-upload', 'thickbox' ), //Define dependencies
		'', //Define a version (optional)
		false //Specify whether to put in footer (leave this true)
);
}

public function to_json() {
parent::to_json();
$this->json['statuses'] = $this->statuses;
}

public function render_content() {
?>
<label>
<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
<div class="customize-control-content">
<input class="bk_customize_image_control_input" readonly="readonly" type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
<a class="bk_customize_image_control_button">Change</a>
</div>
</label>
<?php
}
}
*/

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since Bk_Theme 1.0
 */
class Bk_Theme_Customize
{
	 
	/**
	 * This hooks into 'customize_register' (available as of WP 3.4) and allows
	 * you to add new sections and controls to the Theme Customize screen.
	 *
	 * Note: To enable instant preview, we have to actually write a bit of custom
	 * javascript. See live_preview() for more.
	 *
	 * @see add_action('customize_register',$func)
	 * @param \WP_Customize_Manager $wp_customize
	 * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
	 * @since Bk_Theme 1.0
	 */
	public static function register ( $wp_customize )
	{
		$background_positions = array("left top" => "Left Top",
				"left center" => "Left Center",
				"left bottom" => "Left Bottom",
				"right top" => "Right Top",
				"right center" => "Right Center",
				"right bottom" => "Right Bottom",
				"center top" => "Center Top",
				"center" => "Center",
				"center bottom" => "Center Bottom");

		$background_repeats = array("repeat" => "Cover All Area",
				"repeat-x" => "Repat Horizontally",
				"repeat-y" => "Repeat Vertically",
				"no-repeat" => "No Repeat");

		/*--------------------------------------------------------------
		 NAVIGATION SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		$wp_customize->add_section( 'bk_navigation_theme_customizer_section',
				array(
						'title' => __( 'Color: Navigation', 'corpora_theme' ), //Visible title of section
						'priority' => 120, //Determines what order this appears in
						'capability' => 'edit_theme_options', //Capability needed to tweak
						'description' => __('Allows you to customize colors/backgrounds used for header navigation.', 'corpora_theme'), //Descriptive tooltip
				)
		);
			
		// register header background color setting
		$wp_customize->add_setting( 'bk_header_background_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_header_background_color', //Set a unique ID for the control
				array(
						'label' => __( 'Background Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_header_background_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 1, //Determines the order this control appears in for the specified section
				)
		));
			
		// register header background image setting
		$wp_customize->add_setting( 'bk_header_background_image', //
				array(
						'default' => '', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background image
		$wp_customize->add_control( new WP_Customize_Image_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_header_background_image', //Set a unique ID for the control
				array(
						'label' => __( 'Background Image', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_header_background_image', //Which setting to load and manipulate (serialized is okay)
						'priority' => 2, //Determines the order this control appears in for the specified section
				)
		));
			
		// register header background image position setting
		$wp_customize->add_setting( 'bk_header_background_image_position', //
				array(
						'default' => 'left top', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background image position
		$wp_customize->add_control( 'bk_header_background_image_position', array(
				'settings' => 'bk_header_background_image_position',
				'label'   => 'Background Position:',
				'section' => 'bk_navigation_theme_customizer_section',
				'type'    => 'select',
				'choices' => $background_positions,
				'priority' => 3, //Determines the order this control appears in for the specified section
		));

		// register header background image repeat setting
		$wp_customize->add_setting( 'bk_header_background_image_repeat', //
				array(
						'default' => 'repeat', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background image repeat
		$wp_customize->add_control( 'bk_header_background_image_repeat', array(
				'settings' => 'bk_header_background_image_repeat',
				'label'   => 'Background Repeat:',
				'section' => 'bk_navigation_theme_customizer_section',
				'type'    => 'select',
				'choices' => $background_repeats,
				'priority' => 4, //Determines the order this control appears in for the specified section
		));

		// register header background color setting
		$wp_customize->add_setting( 'bk_header_font_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_header_font_color', //Set a unique ID for the control
				array(
						'label' => __( 'Font Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_header_font_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 6, //Determines the order this control appears in for the specified section
				)
		));
			
		// register header background color setting
		$wp_customize->add_setting( 'bk_header_link_color', //
				array(
						'default' => '#909090', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_header_link_color', //Set a unique ID for the control
				array(
						'label' => __( 'Link Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_header_link_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 7, //Determines the order this control appears in for the specified section
				)
		));
			
		// register header background color setting
		$wp_customize->add_setting( 'bk_header_link_hover_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_header_link_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Link Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_header_link_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 8, //Determines the order this control appears in for the specified section
				)
		));
		
		// register header background color setting
		$wp_customize->add_setting( 'bk_navigation_text_color', //
				array(
						'default' => '#808080', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_navigation_text_color', //Set a unique ID for the control
				array(
						'label' => __( 'Menu Text Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_navigation_text_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 9, //Determines the order this control appears in for the specified section
				)
		));
		
		// register header background color setting
		$wp_customize->add_setting( 'bk_navigation_text_hover_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_navigation_text_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Menu Text Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_navigation_text_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 10, //Determines the order this control appears in for the specified section
				)
		));
		
		// register header background color setting
		$wp_customize->add_setting( 'bk_navigation_submenu_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for header background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_navigation_submenu_color', //Set a unique ID for the control
				array(
						'label' => __( 'Submenu Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_navigation_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_navigation_submenu_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 11, //Determines the order this control appears in for the specified section
				)
		));
		
		
			
		/*--------------------------------------------------------------
		 END OF NAVIGATION SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/



		/*--------------------------------------------------------------
		 PAGE SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		$wp_customize->add_section( 'bk_page_theme_customizer_section',
				array(
						'title' => __( 'Color: Page', 'corpora_theme' ), //Visible title of section
						'priority' => 130, //Determines what order this appears in
						'capability' => 'edit_theme_options', //Capability needed to tweak
						'description' => __('Allows you to customize colors/backgrounds used for page background.', 'corpora_theme'), //Descriptive tooltip
				)
		);
			
		// register page background color setting
		$wp_customize->add_setting( 'bk_page_background_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);


		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_page_background_color', //Set a unique ID for the control
				array(
						'label' => __( 'Page Background Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_page_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_page_background_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 1, //Determines the order this control appears in for the specified section
				)
		));

		// register page background image setting
		$wp_customize->add_setting( 'bk_page_background_image', //
				array(
						'default' => '', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background image
		$wp_customize->add_control( new WP_Customize_Image_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_page_background_image', //Set a unique ID for the control
				array(
						'label' => __( 'Page Background Image', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_page_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_page_background_image', //Which setting to load and manipulate (serialized is okay)
						'priority' => 2, //Determines the order this control appears in for the specified section
				)
		));

		// register page background image position setting
		$wp_customize->add_setting( 'bk_page_background_image_position', //
				array(
						'default' => 'left top', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background image position
		$wp_customize->add_control( 'bk_page_background_image_position', array(
				'settings' => 'bk_page_background_image_position',
				'label'   => 'Page Background Position:',
				'section' => 'bk_page_theme_customizer_section',
				'type'    => 'select',
				'choices' => $background_positions,
				'priority' => 3, //Determines the order this control appears in for the specified section
		));
			
		// register page background image repeat setting
		$wp_customize->add_setting( 'bk_page_background_image_repeat', //
				array(
						'default' => 'repeat', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background image repeat
		$wp_customize->add_control( 'bk_page_background_image_repeat', array(
				'settings' => 'bk_page_background_image_repeat',
				'label'   => 'Page Background Repeat:',
				'section' => 'bk_page_theme_customizer_section',
				'type'    => 'select',
				'choices' => $background_repeats,
				'priority' => 4, //Determines the order this control appears in for the specified section
		));
			
		// register page background image attachement setting
		$wp_customize->add_setting( 'bk_page_background_image_attachment', //
				array(
						'default' => 'repeat', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background image repeat
		$wp_customize->add_control( 'bk_page_background_image_attachment', array(
				'settings' => 'bk_page_background_image_attachment',
				'label'   => 'Page Background Attachment:',
				'section' => 'bk_page_theme_customizer_section',
				'type'    => 'select',
				'choices' => array("scroll" => "Scroll with whole page",
								   "fixed" => "Stay at fixed position"),
				'priority' => 5, //Determines the order this control appears in for the specified section
		));

		/*--------------------------------------------------------------
		 END OF PAGE SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/


		/*--------------------------------------------------------------
		 BODY SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		$wp_customize->add_section( 'bk_body_theme_customizer_section',
				array(
						'title' => __( 'Color: Body', 'corpora_theme' ), //Visible title of section
						'priority' => 140, //Determines what order this appears in
						'capability' => 'edit_theme_options', //Capability needed to tweak
						'description' => __('Allows you to customize colors/backgrounds used for page body - where the content is.', 'corpora_theme'), //Descriptive tooltip
				)
		);

		// register body background color setting
		$wp_customize->add_setting( 'bk_body_background_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);


		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_background_color', //Set a unique ID for the control
				array(
						'label' => __( 'Background Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_background_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 1, //Determines the order this control appears in for the specified section
				)
		));

		// register body background image setting
		$wp_customize->add_setting( 'bk_body_background_image', //
				array(
						'default' => '', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background image
		$wp_customize->add_control( new WP_Customize_Image_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_background_image', //Set a unique ID for the control
				array(
						'label' => __( 'Background Image', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_background_image', //Which setting to load and manipulate (serialized is okay)
						'priority' => 2, //Determines the order this control appears in for the specified section
				)
		));

		// register body background image position setting
		$wp_customize->add_setting( 'bk_body_background_image_position', //
				array(
						'default' => 'left top', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background image position
		$wp_customize->add_control( 'bk_body_background_image_position', array(
				'settings' => 'bk_body_background_image_position',
				'label'   => 'Background Position:',
				'section' => 'bk_body_theme_customizer_section',
				'type'    => 'select',
				'choices' => $background_positions,
				'priority' => 3, //Determines the order this control appears in for the specified section
		));
			
		// register body background image repeat setting
		$wp_customize->add_setting( 'bk_body_background_image_repeat', //
				array(
						'default' => 'repeat', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background image repeat
		$wp_customize->add_control( 'bk_body_background_image_repeat', array(
				'settings' => 'bk_body_background_image_repeat',
				'label'   => 'Background Repeat:',
				'section' => 'bk_body_theme_customizer_section',
				'type'    => 'select',
				'choices' => $background_repeats,
				'priority' => 4, //Determines the order this control appears in for the specified section
		));
			
		// register body background color setting
		$wp_customize->add_setting( 'bk_body_font_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_font_color', //Set a unique ID for the control
				array(
						'label' => __( 'Font Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_font_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 6, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_body_link_color', //
				array(
						'default' => '#909090', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_link_color', //Set a unique ID for the control
				array(
						'label' => __( 'Link Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_link_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 7, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_body_link_hover_color', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_link_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Link Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_link_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 8, //Determines the order this control appears in for the specified section
				)
		));

		$wp_customize->add_setting( 'bk_separator_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_separator_color', //Set a unique ID for the control
				array(
						'label' => __( 'Separator Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_separator_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 10, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_image_overlay_background', //
				array(
						'default' => '#000000', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_image_overlay_background', //Set a unique ID for the control
				array(
						'label' => __( 'Image Overlay Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_image_overlay_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 11, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_image_overlay_text_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_image_overlay_text_color', //Set a unique ID for the control
				array(
						'label' => __( 'Image Overlay Text Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_image_overlay_text_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 12, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_image_overlay_tag_color', //
				array(
						'default' => '#CCCCCC', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_image_overlay_tag_color', //Set a unique ID for the control
				array(
						'label' => __( 'Image Overlay Tag Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_image_overlay_tag_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 13, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_image_overlay_tag_hover_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_image_overlay_tag_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Image Overlay Tag Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_image_overlay_tag_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 14, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_odd_background', //
				array(
						'default' => '#F0F0F0', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_odd_background', //Set a unique ID for the control
				array(
						'label' => __( 'Odd Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_odd_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 15, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_body_input_text_area_border', //
				array(
						'default' => '#F8F8F8', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_input_text_area_border', //Set a unique ID for the control
				array(
						'label' => __( 'Textfield Border', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_input_text_area_border', //Which setting to load and manipulate (serialized is okay)
						'priority' => 16, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_body_input_text_area_background', //
				array(
						'default' => '#F8F8F8', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		 
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_input_text_area_background', //Set a unique ID for the control
				array(
						'label' => __( 'Textfield Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_input_text_area_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 17, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_body_input_text_area_border_focus', //
				array(
						'default' => '#F8F8F8', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_input_text_area_border_focus', //Set a unique ID for the control
				array(
						'label' => __( 'Textfield Focus Border', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_input_text_area_border_focus', //Which setting to load and manipulate (serialized is okay)
						'priority' => 18, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_body_input_text_area_background_focus', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
			
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_body_input_text_area_background_focus', //Set a unique ID for the control
				array(
						'label' => __( 'Textfield Focus Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_body_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_body_input_text_area_background_focus', //Which setting to load and manipulate (serialized is okay)
						'priority' => 19, //Determines the order this control appears in for the specified section
				)
		));


		/*--------------------------------------------------------------
		 END OF BODY SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/

		/*--------------------------------------------------------------
		 PORTFOLIO SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		$wp_customize->add_section( 'bk_portfolio_theme_customizer_section',
				array(
						'title' => __( 'Color: Portfolio', 'corpora_theme' ), //Visible title of section
						'priority' => 150, //Determines what order this appears in
						'capability' => 'edit_theme_options', //Capability needed to tweak
						'description' => __('Allows you to customize colors used for some of portfolio elements.', 'corpora_theme'), //Descriptive tooltip
				)
		);
			
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_navigation_portfolio_filter_text_color', //
				array(
						'default' => '#808080', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_navigation_portfolio_filter_text_color', //Set a unique ID for the control
				array(
						'label' => __( 'Navigation Filter Text Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_portfolio_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_navigation_portfolio_filter_text_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 1, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_navigation_portfolio_filter_text_hover_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_navigation_portfolio_filter_text_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Navigation Filter Text Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_portfolio_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_navigation_portfolio_filter_text_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 2, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_portfolio_filter_text_color', //
				array(
						'default' => '#808080', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_portfolio_filter_text_color', //Set a unique ID for the control
				array(
						'label' => __( 'Filter Text Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_portfolio_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_portfolio_filter_text_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 3, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_portfolio_filter_text_hover_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_portfolio_filter_text_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Filter Text Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_portfolio_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_portfolio_filter_text_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 4, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_portfolio_thumb_description_background_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_portfolio_thumb_description_background_color', //Set a unique ID for the control
				array(
						'label' => __( 'Thumb Description Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_portfolio_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_portfolio_thumb_description_background_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 5, //Determines the order this control appears in for the specified section
				)
		));

		/*--------------------------------------------------------------
		 END OF PORTFOLIO SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		
		/*--------------------------------------------------------------
		 BLOG SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		$wp_customize->add_section( 'bk_blog_theme_customizer_section',
				array(
						'title' => __( 'Color: Blog', 'corpora_theme' ), //Visible title of section
						'priority' => 160, //Determines what order this appears in
						'capability' => 'edit_theme_options', //Capability needed to tweak
						'description' => __('Allows you to customize colors/backgrounds used for some of blog elements.', 'corpora_theme'), //Descriptive tooltip
				)
		);
			
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_blog_thumb_description_background_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_blog_thumb_description_background_color', //Set a unique ID for the control
				array(
						'label' => __( 'Thumb Description Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_blog_thumb_description_background_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 1, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_post_title_color', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_post_title_color', //Set a unique ID for the control
				array(
						'label' => __( 'Post Title Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_post_title_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 2, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_post_title_hover_color', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_post_title_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Post Title Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_post_title_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 3, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_quote_post_format_background', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_quote_post_format_background', //Set a unique ID for the control
				array(
						'label' => __( 'Quote Post Format Backrgound', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_quote_post_format_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 4, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_quote_post_format_text', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_quote_post_format_text', //Set a unique ID for the control
				array(
						'label' => __( 'Quote Post Format Text', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_quote_post_format_text', //Which setting to load and manipulate (serialized is okay)
						'priority' => 5, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_link_post_format_background', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_link_post_format_background', //Set a unique ID for the control
				array(
						'label' => __( 'Link Post Format Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_link_post_format_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 6, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_link_post_format_text', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_link_post_format_text', //Set a unique ID for the control
				array(
						'label' => __( 'Link Post Format Text', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_link_post_format_text', //Which setting to load and manipulate (serialized is okay)
						'priority' => 7, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_link_post_format_text_hover', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_link_post_format_text_hover', //Set a unique ID for the control
				array(
						'label' => __( 'Link Post Format Text Hover', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_link_post_format_text_hover', //Which setting to load and manipulate (serialized is okay)
						'priority' => 8, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_tweet_post_format_background', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_tweet_post_format_background', //Set a unique ID for the control
				array(
						'label' => __( 'Tweet Post Format Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_tweet_post_format_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 9, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_tweet_post_format_text', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_tweet_post_format_text', //Set a unique ID for the control
				array(
						'label' => __( 'Tweet Post Format Text', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_tweet_post_format_text', //Which setting to load and manipulate (serialized is okay)
						'priority' => 10, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_tweet_post_format_link', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_tweet_post_format_link', //Set a unique ID for the control
				array(
						'label' => __( 'Tweet Post Format Link', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_tweet_post_format_link', //Which setting to load and manipulate (serialized is okay)
						'priority' => 11, //Determines the order this control appears in for the specified section
				)
		));

		// register page background color setting
		$wp_customize->add_setting( 'bk_tweet_post_format_link_hover', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_tweet_post_format_link_hover', //Set a unique ID for the control
				array(
						'label' => __( 'Tweet Post Format Link Hover', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_tweet_post_format_link_hover', //Which setting to load and manipulate (serialized is okay)
						'priority' => 12, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_chat_post_format_background', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_chat_post_format_background', //Set a unique ID for the control
				array(
						'label' => __( 'Chat Format Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_chat_post_format_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 13, //Determines the order this control appears in for the specified section
				)
		));
		
		// register page background color setting
		$wp_customize->add_setting( 'bk_chat_post_format_text', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for page background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_chat_post_format_text', //Set a unique ID for the control
				array(
						'label' => __( 'Chat Format Text', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_blog_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_chat_post_format_text', //Which setting to load and manipulate (serialized is okay)
						'priority' => 14, //Determines the order this control appears in for the specified section
				)
		));

		/*--------------------------------------------------------------
		 END OF BLOG SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/

		/*--------------------------------------------------------------
		 SHORTCODES SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		$wp_customize->add_section( 'bk_shortcode_theme_customizer_section',
				array(
						'title' => __( 'Color: Shortcodes', 'corpora_theme' ), //Visible title of section
						'priority' => 170, //Determines what order this appears in
						'capability' => 'edit_theme_options', //Capability needed to tweak
						'description' => __('Allows to customize some colors used for shortcodes such as buttons, highlihgts progressbars.', 'corpora_theme'), //Descriptive tooltip
				)
		);
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_button_background', //
				array(
						'default' => '#909090', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_button_background', //Set a unique ID for the control
				array(
						'label' => __( 'Button Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_button_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 1, //Determines the order this control appears in for the specified section
				)
		));
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_button_hover_background', //
				array(
						'default' => '#404040', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_button_hover_background', //Set a unique ID for the control
				array(
						'label' => __( 'Button Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_button_hover_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 2, //Determines the order this control appears in for the specified section
				)
		));
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_highlight_background', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_highlight_background', //Set a unique ID for the control
				array(
						'label' => __( 'Higlight Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_highlight_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 3, //Determines the order this control appears in for the specified section
				)
		));
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_highlight_text_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_highlight_text_color', //Set a unique ID for the control
				array(
						'label' => __( 'Highlight Text Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_highlight_text_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 4, //Determines the order this control appears in for the specified section
				)
		));
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_highlight_text_color', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_highlight_text_color', //Set a unique ID for the control
				array(
						'label' => __( 'Highlight Text Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_highlight_text_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 4, //Determines the order this control appears in for the specified section
				)
		));
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_progressbar_background', //
				array(
						'default' => '#F8F8F8', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_progressbar_background', //Set a unique ID for the control
				array(
						'label' => __( 'Progressbar Background', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_progressbar_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 5, //Determines the order this control appears in for the specified section
				)
		));
		
		// register footer background color setting
		$wp_customize->add_setting( 'bk_progressbar_color', //
				array(
						'default' => '#FF4629', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		
		// define control for footer background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_progressbar_color', //Set a unique ID for the control
				array(
						'label' => __( 'Progressbar Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_progressbar_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 6, //Determines the order this control appears in for the specified section
				)
		));
		
		/*--------------------------------------------------------------
		 END OF SHORTCODES SECTION OF THEME CUSTMIZER
		---------------------------------------------------------------*/
		
		
		/*--------------------------------------------------------------
		 PRICING TABLE SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/

		// register body background color setting
		$wp_customize->add_setting( 'bk_price_table_background', //
				array(
						'default' => '#FFFFFF', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_price_table_background', //Set a unique ID for the control
				array(
						'label' => __( 'Pricng Table Background Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_price_table_background', //Which setting to load and manipulate (serialized is okay)
						'priority' => 7, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_price_table_border', //
				array(
						'default' => '#808080', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_price_table_border', //Set a unique ID for the control
				array(
						'label' => __( 'Pricng Table Boarder', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_price_table_border', //Which setting to load and manipulate (serialized is okay)
						'priority' => 8, //Determines the order this control appears in for the specified section
				)
		));

		// register body background color setting
		$wp_customize->add_setting( 'bk_price_table_alternate_color', //
				array(
						'default' => '#F8F8F8', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);

		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_price_table_alternate_color', //Set a unique ID for the control
				array(
						'label' => __( 'Pricing Table Alternate Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_price_table_alternate_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 9, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_toggle_header_color', //
				array(
						'default' => '#909090', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_toggle_header_color', //Set a unique ID for the control
				array(
						'label' => __( 'Toggle Header Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_toggle_header_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 10, //Determines the order this control appears in for the specified section
				)
		));
		
		// register body background color setting
		$wp_customize->add_setting( 'bk_toggle_header_hover_color', //
				array(
						'default' => '#F8F8F8', //Default setting/value to save
						'type' => 'option', //Is this an 'option' or a 'theme_mod'?
						'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
						'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				)
		);
		
		// define control for body background color
		$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'bk_toggle_header_hover_color', //Set a unique ID for the control
				array(
						'label' => __( 'Toggle Header Hover Color', 'corpora_theme' ), //Admin-visible name of the control
						'section' => 'bk_shortcode_theme_customizer_section', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => 'bk_toggle_header_hover_color', //Which setting to load and manipulate (serialized is okay)
						'priority' => 11, //Determines the order this control appears in for the specified section
				)
		));
		
		
		/*--------------------------------------------------------------
		 END OF PRICING TABLE SECITION OF THEME CUSTMIZER
		---------------------------------------------------------------*/


		$wp_customize->remove_section( 'title_tagline');
	}

	 
	/**
	 * This will output custom style wraps with unique id's so theme-customizer.js
	 * can hook into this wrap and inject new css so style can be visible in live
	 * those are settings that aren't possible to target via inline css injection
	 * don't want to override by mistake some settings
	 *
	 * Used by hook: 'wp_head'
	 *
	 * @see add_action('wp_head',$func)
	 * @since Bk_Theme 1.0
	 */
	public static function header_output()
	{
		 
	}
	 
	/**
	 * This outputs the javascript needed to automate the live settings preview.
	 * Also keep in mind that this function isn't necessary unless your settings
	 * are using 'transport'=>'postMessage' instead of the default 'transport'
	 * => 'refresh'
	 *
	 * Used by hook: 'customize_preview_init'
	 *
	 * @see add_action('customize_preview_init',$func)
	 * @since Bk_Theme 1.0
	 */
	public static function live_preview()
	{
		wp_enqueue_script(
		'Bk_Theme-themecustomizer', //Give the script an ID
		get_template_directory_uri().'/admin/js/theme-customizer.js', //Define it's JS file
		array( 'jquery','customize-preview' ), //Define dependencies
		'', //Define a version (optional)
		true //Specify whether to put in footer (leave this true)
		);
	}

	/**
	 * This will generate a line of CSS for use in header output. If the setting
	 * ($mod_name) has no defined value, the CSS will not be output.
	 *
	 * @uses get_theme_mod()
	 * @param string $selector CSS selector
	 * @param string $style The name of the CSS *property* to modify
	 * @param string $mod_name The name of the 'theme_mod' option to fetch
	 * @param string $prefix Optional. Anything that needs to be output before the CSS property
	 * @param string $postfix Optional. Anything that needs to be output after the CSS property
	 * @param bool $echo Optional. Whether to print directly to the page (default: true).
	 * @return string Returns a single line of CSS with selectors and a property.
	 * @since Bk_Theme 1.0
	 */
	public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true)
	{
		$return = '';
		$mod = get_theme_mod($mod_name);
		if ( ! empty( $mod ) )
		{
			$return = sprintf('%s { %s:%s; }',
					$selector,
					$style,
					$prefix.$mod.$postfix
			);
			if ( $echo )
			{
				echo $return;
			}
		}

		return $return;
	}

}

//Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'Bk_Theme_Customize' , 'register' ) );

//Output custom CSS to live site
add_action( 'wp_head' , array( 'Bk_Theme_Customize' , 'header_output' ), 100 ); // make sure that it will be printed last - after custom stylesheet printed by theme so it can be overriden

//Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , array( 'Bk_Theme_Customize' , 'live_preview' ) );