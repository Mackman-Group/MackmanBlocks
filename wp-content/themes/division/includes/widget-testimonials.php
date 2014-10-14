<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_testimonials_widget" );' ) );

function bk_enqueue_testimonials_widget_scripts() {
	if ( is_active_widget( false, false, 'bk_testimonials_widget', true ) ) {
		wp_enqueue_script('cycle_slider');
		//wp_enqueue_style( 'prettyPhotoStyle' );
	}
}

add_action('wp_enqueue_scripts', 'bk_enqueue_testimonials_widget_scripts');


/**
 * Adds Foo_Widget widget.
 */
class Bk_Testimonials_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_testimonials_widget', // Base ID
			'Division Testimonials', // Name
			array( 'description' => __( 'This widget shows testimonials from Your clients', 'corpora_theme' ) ) // Args
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

  $count = $instance['count'];
	if ( empty( $count ) )
	     $count = 3;   

	echo $before_widget;
	if ( ! empty( $title ) )
	echo $before_title . $title . $after_title;

	$args= array(
	'posts_per_page' => $count,
	'post_type' => 'testimonial'
	);
	
	$autoplay = 0;
	
	echo bk_get_testimonial_items_widget( $args, $autoplay );

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
		$instance['count'] = strip_tags( $new_instance['count'] );

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
			$title = __( 'Testimonials', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'count' ] ) ) {
			$count = $instance[ 'count' ];
		}
		else {
			$count = 3;
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'How many testimonials to show ?', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>
		
		<?php 
	}

} // class Foo_Widget
?>