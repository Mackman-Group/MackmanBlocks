<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_recent_portfolio_widget" );' ) );

function bk_enqueue_recent_portfolio_widget_scripts() {
	if ( is_active_widget( false, false, 'bk_recent_portfolio_widget', true ) ) {
		wp_enqueue_script('prettyPhoto');
		wp_enqueue_style( 'prettyPhotoStyle' );
	}
}

add_action('wp_enqueue_scripts', 'bk_enqueue_recent_portfolio_widget_scripts');


/**
 * Adds Foo_Widget widget.
 */
class Bk_Recent_Portfolio_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_recent_portfolio_widget', // Base ID
			'Division Recent Portfolio', // Name
			array( 'description' => __( 'This widget shows Your latest items from portfolio', 'corpora_theme' ), ) // Args
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
	     
	if ( isset( $instance[ 'prettyphoto_enabled' ] ) && 'on' == $instance[ 'prettyphoto_enabled' ] ) {
	     $prettyphoto_enabled = true;
	} else {
		   $prettyphoto_enabled = false;
	}	          

	echo $before_widget;
	if ( ! empty( $title ) )
	echo $before_title . $title . $after_title;

	$args= array(
	'posts_per_page' => $count,
	'post_type' => 'portfolio'
	);
	
	echo bk_get_recent_portfolio_items_widget( $args, $prettyphoto_enabled );

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
		$instance['prettyphoto_enabled'] = strip_tags( $new_instance['prettyphoto_enabled'] );

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
		} else {
			$title = __( 'Recent Portfolio', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'count' ] ) ) {
			$count = $instance[ 'count' ];
		} else {
			$count = 3;
		}
		
		if ( isset( $instance[ 'prettyphoto_enabled' ] ) && 'on' == $instance[ 'prettyphoto_enabled' ] ) {
			$prettyphoto_enabled = 'checked="checked"';
		} else {
			$prettyphoto_enabled = '';
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'How many items to show ?', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>
		
		<p>
		<input class="checkbox" type="checkbox" <?php echo $prettyphoto_enabled ?> id="<?php echo $this->get_field_id( 'prettyphoto_enabled' ); ?>" name="<?php echo $this->get_field_name( 'prettyphoto_enabled' ); ?>">
		<label for="<?php echo $this->get_field_id( 'prettyphoto_enabled' ); ?>"><?php _e( 'Enable popup for images', 'corpora_theme' ) ?></label> 
		</p>
		<?php 
	}

} // class Foo_Widget
?>