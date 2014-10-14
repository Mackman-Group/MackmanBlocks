<?php

include_once(ABSPATH . WPINC . '/feed.php');

// Register the widget.
add_action('widgets_init', create_function('', 'register_widget("bk_dribbble_widget");'));


class Bk_Dribbble_Widget extends WP_Widget {

    /**
     * Widget settings.
     */
    protected $widget = array(
            // Default title for the widget in the sidebar.
            'title' => 'Recent Shots',

            // Default widget settings.
            'username' => '',
            'shots' => 9,
    );

    public function __construct() {
        parent::__construct(
                'bk_dribbble_widget',
                'Division Dribbble Widget',
                array( 'description' => __( 'This widget shows recent Dribbble shots', 'corpora_theme' ), )
        );
        
    }
    
    function form($instance) {
        // load current values or set to default.
        $title = array_key_exists('title', $instance) ? esc_attr($instance['title']) : $this->widget['title'];
        $username = array_key_exists('username', $instance) ? esc_attr($instance['username']) : $this->widget['username'];
        $shots = array_key_exists('shots', $instance) ? esc_attr($instance['shots']) : $this->widget['shots'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'corpora_theme'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username:', 'corpora_theme'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('shots'); ?>"><?php _e('Number of pins:', 'corpora_theme'); ?></label>
            <input id="<?php echo $this->get_field_id('shots'); ?>" name="<?php echo $this->get_field_name('shots'); ?>" type="text" value="<?php echo $shots; ?>" size="3" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['shots'] = strip_tags($new_instance['shots']);
        return $instance;
    }
    
    function widget($args, $instance) {
        extract($args);
        echo($before_widget);
        $title = apply_filters('widget_title', $instance['title']);
        echo($before_title . $title . $after_title);
        
        // Get the RSS.
        $username = $instance['username'];
        $count = $instance['shots'];
        
        echo bk_get_dribbble_widget( $username, $count );
        
        echo($after_widget);
    }
}
?>
