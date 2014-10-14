<?php
add_action( 'widgets_init', create_function( '', 'register_widget( "bk_filter_widget" );' ) );

$bk_unique_flickr_widget_id = 0;
/**
 * Adds Foo_Widget widget.
 */
class Bk_Filter_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'bk_filter_widget', // Base ID
			'Division Portfolio Filter Widget', // Name
			array( 'description' => __( 'This widget can be used as filter for portfolio works that will appear in page sidebar under menu.', 'corpora_theme' ), ) // Args
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

	$filter = $instance['filter'];
	       
    echo $before_widget;

	if($title){
		echo $before_title;
		echo $title;
		echo $after_title;
	}

	$content = '';
	
	echo '<ul class="portfolio-filter clearfix bk-sidebar-portfolio-filter">';
	$filter_markup = '';
	if( in_array(-1, $filter) || ( count( $filter ) > 1 ) ) {
		echo '<li class="current"><span></span><a href="javascript:void(0)" class="all">' . __( 'All' , 'corpora_theme') . '</a></li>';
	}	
	
	$terms = get_terms('filter');
	
	$count = count($terms);
	
	if ($count > 0) {
		foreach ($terms as $term) {
			if( in_array(-1, $filter) ||  in_array($term->slug, $filter) ) {
				echo '<li><span></span><a href="javascript:void(0)" class="'.  $term->slug .'">' . $term->name . '</a></li>';
			}
		}
	}
	
	echo '</ul>';
		
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
		$instance['filter'] = $new_instance['filter'];

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
			$title = __( 'Filter', 'corpora_theme' );
		}
		
		if ( isset( $instance[ 'filter' ] ) ) {
			$display = $instance[ 'filter' ];
		}
		else {
			$display = '-1';
		}
		
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'corpora_theme' ) ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<label for="<?php echo $this->get_field_id( 'Filter' ); ?>"><?php _e( 'Which categories should be visible in filter - make sure that same categories will be checked to display portfolio on page to make filter work properly.', 'corpora_theme' ) ?></label> 
		
		<?php 
		
		$select_option .= '<select multiple="multiple" class="bk-choosen-dropdown" id="' . $this->get_field_id( 'filter' ) . '" name="' . $this->get_field_name( 'filter' ) . '[]">';
			
		$label = __( 'All Categories', 'corpora_theme' );
		$taxonomy_slug = 'filter';
		
		if( is_string( $display ) ) {
			$display = array($display);
		}
			
		if( in_array( -1, $display ) )
			$select_option .= "<option value='-1' selected='selected'>$label</option>";
		else
			$select_option .= "<option value='-1'>$label</option>";
			
		$terms = get_terms($taxonomy_slug);
		$count = count($terms);
		if ($count > 0) {
			foreach ($terms  as $term) {
				if( in_array( $term->slug, $display ) )
					$select_option .= '<option value="' . $term->slug . '" selected="selected">' . $term->name . '</option>';
				else
					$select_option .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
			}
		}
		$select_option .=		"</select></td></tr>";
		
		echo $select_option;
		?>
		
		</p>
	
		<?php 
	}

} // class Foo_Widget
?>
