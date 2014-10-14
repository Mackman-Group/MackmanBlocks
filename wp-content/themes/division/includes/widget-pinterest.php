<?php

include_once(ABSPATH . WPINC . '/feed.php');

// Register the widget.
add_action('widgets_init', create_function('', 'register_widget("bk_pinterest_widget");'));

function bk_get_pinterest_widget($username, $pins_count) {
	$pins = bk_get_pinterest_pins($username, $pins_count);
	
	$content = '';
	if (!is_null($pins))  {
		$content .= '<ul class="bk-pinterest-pins clearfix">';
		foreach ($pins as $pin) {
			$title = $pin['title'];
			$url = $pin['url'];
			$image = $pin['image'];
			$content .= '<li><a target="_blank" href="'. $url . '"><img src="' . $image . '" alt="' . $title . '" title="' . $title . '" /><div class="bk-img-overlay hoverable"></div></a></li>';
		}
		$content .= '</ul>';
	}
	
	return $content;
}

/**
* Retrieve RSS feed for username, and parse the data needed from it.
* Returns null on error, otherwise a hash of pins.
*/
function bk_get_pinterest_pins($username, $nrpins) {

	// Set caching.
	add_filter('wp_feed_cache_transient_lifetime', create_function('$a', 'return '. 900 .';'));

	// Get the RSS feed.
	$url = 'http://pinterest.com' .'/'. $username .'/feed.rss';
	$rss = fetch_feed($url);
	if (is_wp_error($rss)) {
		return null;
	}

	$maxitems = $rss->get_item_quantity($nrpins);
	$rss_items = $rss->get_items(0, $maxitems);

	$pins;
	if (is_null($rss_items)) {
		$pins = null;
	} else {
		// Pattern to replace for the images.
		$search = array('_b.jpg');
		$replace = array('_t.jpg');
		// Add http replace is running secure.
		if (bk_is_secure_conn()) {
			array_push($search, 'http://');
			array_push($replace, $this->protocol);
		}
		$pins = array();
		foreach ($rss_items as $item) {
			$title = $item->get_title();
			$description = $item->get_description();
			$url = $item->get_permalink();
			if (preg_match_all('/<img src="([^"]*)".*>/i', $description, $matches)) {
				$image = str_replace($search, $replace, $matches[1][0]);
			}
			array_push($pins, array(
			'title' => $title,
			'image' => $image,
			'url' => $url
			));
		}
	}
	return $pins;
}

/**
* Check if the server is running SSL.
*/
function bk_is_secure_conn() {
	return !empty($_SERVER['HTTPS'])
	&& $_SERVER['HTTPS'] !== 'off'
	|| $_SERVER['SERVER_PORT'] == 443;
}


class Bk_Pinterest_Widget extends WP_Widget {

    /**
     * Widget settings.
     */
    protected $widget = array(
            // Default title for the widget in the sidebar.
            'title' => 'Recent pins',

            // Default widget settings.
            'username' => '',
            'pins' => 9,
    );
    
    var $start_time;
    var $protocol;

    public function __construct() {
        parent::__construct(
                'bk_pinterest_widget',
                'Division Pinterest Widget',
                array( 'description' => __( 'This widget shows latest pinteres pins', 'corpora_theme' ), )
        );
        $this->start_time = microtime(true);
        $this->protocol = bk_is_secure_conn() ? 'https://' : 'http://';
    }
    
    function form($instance) {
        // load current values or set to default.
        $title = array_key_exists('title', $instance) ? esc_attr($instance['title']) : $this->widget['title'];
        $username = array_key_exists('username', $instance) ? esc_attr($instance['username']) : $this->widget['username'];
        $pins = array_key_exists('pins', $instance) ? esc_attr($instance['pins']) : $this->widget['pins'];
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
            <label for="<?php echo $this->get_field_id('pins'); ?>"><?php _e('Number of pins:', 'corpora_theme'); ?></label>
            <input id="<?php echo $this->get_field_id('pins'); ?>" name="<?php echo $this->get_field_name('pins'); ?>" type="text" value="<?php echo $pins; ?>" size="3" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['username'] = strip_tags($new_instance['username']);
        $instance['pins'] = strip_tags($new_instance['pins']);
        return $instance;
    }
    
    function widget($args, $instance) {
        extract($args);
        echo($before_widget);
        $title = apply_filters('widget_title', $instance['title']);
        echo($before_title . $title . $after_title);
        
        // Get the RSS.
        $username = $instance['username'];
        $pins_count = $instance['pins'];
        
        echo bk_get_pinterest_widget($username, $pins_count);

        echo($after_widget);
    }

}
?>
