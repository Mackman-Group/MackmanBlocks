<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_recent_comments_widget" );' ) );

/**
 * Adds Foo_Widget widget.
 */
class Bk_Recent_Comments_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_recent_comments_widget', // Base ID
			'Division Recent Comments', // Name
			array( 'description' => __( 'This widget shows recent comments on blog Your blog posts with gravatar of author, human friendly time e.g "4 hours ago" and comment excerpt', 'corpora_theme' ), ) // Args
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
	     $count = 5;

	echo $before_widget;
	if ( ! empty( $title ) )
	echo $before_title . $title . $after_title;

	$args = array(
	'status' => 'approve',
	'number' => $count,
	);
	
	echo bk_get_recent_comments_widget( $args );

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
			$title = __( 'Recent Comments', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'count' ] ) ) {
			$count = $instance[ 'count' ];
		}
		else {
			$count = 5;
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'How many comments to show ?', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>
		<?php 
	}

} // class Foo_Widget
?>