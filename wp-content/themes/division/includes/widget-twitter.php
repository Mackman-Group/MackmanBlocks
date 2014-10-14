<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_twitter_widget" );' ) );

function bk_enqueue_twitter_widget_scripts() {
	if ( is_active_widget( false, false, 'bk_twitter_widget', true ) ) {
		//wp_enqueue_script('prettyPhoto');
		//wp_enqueue_style( 'prettyPhotoStyle' );
	}
}

add_action('wp_enqueue_scripts', 'bk_enqueue_twitter_widget_scripts');

$bk_unique_twitter_widget_id = 0;
/**
 * Adds Foo_Widget widget.
 */
class Bk_Twitter_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_twitter_widget', // Base ID
			'Division Twitter Widget', // Name
			array( 'description' => __( 'This widget shows latest tweets from Your account', 'corpora_theme' ), ) // Args
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
	     
	if( $count > 200 )
		  $count = 200;
		  
	$account = $instance['account'];
	$follow_us_text = $instance['follow_us_text'];
	       

	echo $before_widget;
	if ( ! empty( $title ) )
	echo $before_title . $title . $after_title;

	echo bk_get_twitter_widget( $account, $count, $follow_us_text );
  
	echo $after_widget;
}

	/**
	 * Link a Twitter user mentioned in the tweet text to the user's page on Twitter.
	 *
	 * @param array $matches regex match
	 * @return string Tweet text with inserted @user link
	 */
	function _wpcom_widget_twitter_username( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]@<a href='" . esc_url( 'http://twitter.com/' . urlencode( $matches[3] ) ) . "'>$matches[3]</a>";
	}

	/**
	 * Link a Twitter hashtag with a search results page on Twitter.com
	 *
	 * @param array $matches regex match
	 * @return string Tweet text with inserted #hashtag link
	 */
	function _wpcom_widget_twitter_hashtag( $matches ) { // $matches has already been through wp_specialchars
		return "$matches[1]<a href='" . esc_url( 'http://twitter.com/search?q=%23' . urlencode( $matches[3] ) ) . "'>#$matches[3]</a>";
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
		$instance['follow_us_text'] = strip_tags( $new_instance['follow_us_text'] );

		wp_cache_delete( 'bk-twitter-widget-' . $this->number , 'widget' );

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
			$title = __( 'Twitter', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'count' ] ) ) {
			$count = $instance[ 'count' ];
		}
		else {
			$count = 5;
		}
		
		if ( isset( $instance[ 'account' ] ) ) {
			$account = $instance[ 'account' ];
		}
		else {
			$account = '';
		}
		
		if ( isset( $instance['follow_us_text'] ) ) {
			$follow_us_text = $instance[ 'follow_us_text' ];
		}
		else {
			$follow_us_text = 'Follow Us';
		}
		
		
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'How many tweets to show ?', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'account' ); ?>"><?php _e( 'Twitter Username', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'account' ); ?>" name="<?php echo $this->get_field_name( 'account' ); ?>" type="text" value="<?php echo esc_attr( $account ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'follow_us_text' ); ?>"><?php _e( 'Follow us text displayed under tweets', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'follow_us_text' ); ?>" name="<?php echo $this->get_field_name( 'follow_us_text' ); ?>" type="text" value="<?php echo esc_attr( $follow_us_text ); ?>" />
		</p>
		
		<?php 
	}

} // class Foo_Widget
?>