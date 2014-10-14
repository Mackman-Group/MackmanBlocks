<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_flickr_widget" );' ) );

//add_action('wp_enqueue_scripts', 'bk_enqueue_flickr_widget_scripts');

$bk_unique_flickr_widget_id = 0;
/**
 * Adds Foo_Widget widget.
 */
class Bk_Flickr_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_flickr_widget', // Base ID
			'Division Flickr Widget', // Name
			array( 'description' => __( 'This widget shows latest photos from Your Flickr account', 'corpora_theme' ), ) // Args
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
	     
	if( $count > 10 )
		  $count = 10;
		  
	$account = $instance['account'];
	$display = $instance['display'];
	       
	if (!empty($account)) {

		echo $before_widget;

		if($title){
			echo $before_title;
			echo $title;
			echo $after_title;
		}

		$content = '';
		
		echo bk_get_flickr_widget( $account, $count, $display );
		
		echo $after_widget;
	}
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
		$instance['account'] = strip_tags( $new_instance['account'] );
		$instance['display'] = $new_instance['display'];

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
			$title = __( 'Flickr', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'count' ] ) ) {
			$count = $instance[ 'count' ];
		}
		else {
			$count = 8;
		}
		
		if ( isset( $instance[ 'account' ] ) ) {
			$account = $instance[ 'account' ];
		}
		else {
			$account = '';
		}
		
		if ( isset( $instance[ 'display' ] ) ) {
			$display = $instance[ 'display' ];
		}
		else {
			$display = 'latest';
		}
		
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'How many photos to show ?', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Which photos to show ?', 'corpora_theme' ) ?></label> 
		
		<?php 
		$select_option = '';			
		$select_option .= '<select id="' . $this->get_field_id( 'display' ) . '" name="' . $this->get_field_name( 'display' ) . '">';
					
		$all_values = array();
		$all_values[] = 'latest';
		$all_values[] = 'random';

		foreach ($all_values as $key => $value)	{
						 $label = $key;
									
									if(is_int($key))
										 $label = $value;
									
									if( $display == $value )
										  $select_option .= '<option value="' . $value . '" selected="selected">' . $label . '</option>';
									else
											$select_option .= '<option value="' . $value . '">' . $label . '</option>'; 		
		}							
											
		$select_option .=		"</select>";
		echo $select_option;
		?>
		</p>
		
		
		<p>
		<label for="<?php echo $this->get_field_id( 'account' ); ?>"><?php _e( 'Flickr Username ID. ', 'corpora_theme' ) ?><a target="_blank" href="http://idgettr.com/">idgettr</a></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'account' ); ?>" name="<?php echo $this->get_field_name( 'account' ); ?>" type="text" value="<?php echo esc_attr( $account ); ?>" />
		</p>
	
		<?php 
	}

} // class Foo_Widget
?>