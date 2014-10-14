<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_contact_form_widget" );' ) );

function bk_enqueue_contact_form_widget_scripts() {
	if ( is_active_widget( false, false, 'bk_contact_form_widget', true ) ) {
		//wp_enqueue_script('validate');
	}
}

add_action('wp_enqueue_scripts', 'bk_enqueue_contact_form_widget_scripts');


/**
 * Adds Foo_Widget widget.
 */
class Bk_Contact_Form_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_contact_form_widget', // Base ID
			'Division Contact Form', // Name
			array( 'description' => __( 'This widget shows small contact form which can be used by visitros to send messages', 'corpora_theme' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	extract( $args );
	$title = apply_filters( 'widget_title', $instance['title'] );

	echo $before_widget;

	if($title){
		echo $before_title;
		echo $title;
		echo $after_title;
	}

	$button_text = $instance['button_text'];
	
	echo get_contact_form_widget($button_text);

	echo $after_widget;
  }

	
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['button_text'] = strip_tags( $new_instance['button_text'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Contact', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'button_text' ] ) ) {
			$button_text = $instance[ 'button_text' ];
		}
		else {
			$button_text = 'Send';
		}
		
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>" />
		</p>
		
		<?php 
	}

} // class Foo_Widget
?>