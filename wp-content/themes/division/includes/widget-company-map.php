<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_company_map_widget" );' ) );

function bk_enqueue_company_map_widget_scripts() {
	if ( is_active_widget( false, false, 'bk_company_map_widget', true ) ) {
		//wp_enqueue_script('validate');
	}
}

add_action('wp_enqueue_scripts', 'bk_enqueue_company_map_widget_scripts');


/**
 * Adds Foo_Widget widget.
 */
class Bk_Company_Map_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_company_map_widget', // Base ID
			'Division Google Map Widget', // Name
			array( 'description' => __( 'This widget shows google map with location of Your company as well as two text areas which can have address, mail or telphone number', 'corpora_theme' ), ) // Args
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

	$google_map_code = $instance['google_map_code'];
	$left_textarea = $instance['left_textarea'];
	$right_textarea = $instance['right_textarea'];
	

	echo $before_widget;

	if($title){
		echo $before_title;
		echo $title;
		echo $after_title;
	}
	
	echo get_google_map_widget($google_map_code, $left_textarea, $right_textarea);

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
		$instance['google_map_code'] = $new_instance['google_map_code'];
		$instance['left_textarea'] = $new_instance['left_textarea'];
		$instance['right_textarea'] = $new_instance['right_textarea'];

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
			$title = __( 'Find Us', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'google_map_code' ] ) ) {
			$google_map_code = $instance[ 'google_map_code' ];
		}
		else {
			$google_map_code = '';
		}
		
		if ( isset( $instance[ 'left_textarea' ] ) ) {
			$left_textarea = $instance[ 'left_textarea' ];
		}
		else {
			$left_textarea = '';
		}
		
		if ( isset( $instance[ 'right_textarea' ] ) ) {
			$right_textarea = $instance[ 'right_textarea' ];
		}
		else {
			$right_textarea = '';
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'google_map_code' ); ?>"><?php _e( 'Paste in here google map embedd code to show map with Your location', 'corpora_theme' ) ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'google_map_code' ); ?>" name="<?php echo $this->get_field_name( 'google_map_code' ); ?>" >
	  <?php echo esc_attr( $google_map_code ); ?>
	  </textarea>	
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'left_textarea' ); ?>"><?php _e( 'Text that will be displayed below map on left side', 'corpora_theme' ) ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'left_textarea' ); ?>" name="<?php echo $this->get_field_name( 'left_textarea' ); ?>" >
	  <?php echo esc_attr( $left_textarea ); ?>
	  </textarea>	
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'right_textarea' ); ?>"><?php _e( 'Text that will be displayed below map on right side', 'corpora_theme' ) ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'right_textarea' ); ?>" name="<?php echo $this->get_field_name( 'right_textarea' ); ?>" >
	  <?php echo esc_attr( $right_textarea ); ?>
	  </textarea>	
		</p>
	
		<?php 
	}

} // class Foo_Widget
?>