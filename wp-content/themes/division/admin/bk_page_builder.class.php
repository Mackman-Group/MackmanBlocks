<?php
/*
 * Bk_Page_Builder singleton class
 * 
 * responsibel for loading, saving, resotering page builder state for given post
 * as well as for handling ajax requests from frontend - adding new items
 * plus outputing proper editiors for each page builder item type
 * 
 */

class Bk_Page_Builder {
	private static $instance;  // reference to singleton class
	private $items;		// all items available in pagebuilder
	private $all_sizes; // all available sizes of items
	
	/*
	 * Get instance of singleton Bk_Page_Builder class
	*
	* this method is used to get reference to singleton and call other functions
	* if it's being called first time during application it creates new instance of
	* Bk_Page_Builder class
	* usage Bk_Page_Builder::getInstance()->somefunction();
	*
	* @param - void
	* @return - (Bk_Page_Builder class) instance
	*
	*/
	
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	/*
	 * Class Constructor
	 * 
	 * $items contains all page builder availabel blocks along with descriptions, filed type etc..
	 * it is used to generate proper editors for page builder blocks as blocks itself via ajax call from admin
	 * 
	 * id of each module is also a shortcode that will be genrated and handled inside shortcodes.php file
	 * e.g for Portfolio module id is bk_portfolio thus generated shortcode will be [bk_portfolio ... /] where
	 * each one of fileds will be attribute of this shortcode e.g for Show Layout Switcher filed it will result
	 * with following scenario - [bk_portfolio show_layout_switcher=0 /] 
	 * 
	 * @params - void
	 * @return - void
	 * 
	 */
	
	private function __construct() {
		$this->items = array(
					  'bk_portfolio' => array(
												'name' => __('Portfolio', 'corpora_theme'),
												'fields' => array(
																	array(
																			'name' => __( 'Show Filter', 'corpora_theme' ),
																			'id' => 'show_filter',
																			'description' => __( 'If enabled filter based on selected categories will be visible to allow sorting portfoli items.', 'corpora_theme' ),
																			'type' => 'checkbox',
																			'default' => true,
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Filter Mode', 'corpora_theme' ),
																			'id' => 'filter_mode',
																			'description' => __( 'Choose how filtered portfolio items will behave. Shuffle -> hides not matched items, Fade -> partially fades out not matched elements, Fade & Shuffle -> partially fades out not matched elements and moves matched items to the top. ', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'shuffle',
																			'all_values' => array('shuffle' => __( 'Shuffle', 'corpora_theme' ),
																								  'fade' => __( 'Fade', 'corpora_theme' ),
																					 			  'fade_and_shuffle' => __( 'Fade & Shuffle', 'corpora_theme' )),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Portfolio Layout', 'corpora_theme' ),
																			'id' => 'layout',
																			'description' => __( 'Choose layout for portfolio items.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'grid',
																			'all_values' => array('grid' => __( 'Grid', 'corpora_theme' ),
																								  'metro' => __( 'Metro', 'corpora_theme' ),
																								  'masonry' => __( 'Masonry', 'corpora_theme' ),
																								  'mixed_masonry' => __( 'Mixed Masonry', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Base Size', 'corpora_theme' ),
																			'id' => 'base_size',
																			'description' => __( 'Choose portfolio layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'small',
																			'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
																								  'medium' => __( 'Medium', 'corpora_theme' ),
																								  'large' => __( 'Large', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Description', 'corpora_theme' ),
																			'id' => 'description',
																			'description' => __( 'Choose optional description for each portfolio item, it will be displayed below portfolio thumb.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'title',
																			'all_values' => array('none' => __( 'None', 'corpora_theme' ),
																								  'title' => __( 'Title', 'corpora_theme' ),
																								  'excerpt' => __( 'Excerpt', 'corpora_theme' ),
																								  'title_and_excerpt' => __( 'Title & Excerpt', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Thumb Effect', 'corpora_theme' ),
																			'id' => 'image_effect',
																			'description' => __( 'Choose optional portfolio thumb effect, it will be visible when user hovers over image.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'bk_zoom',
																			'all_values' => array('bk_none' => __( 'None', 'corpora_theme' ),
																								  'bk_zoom' => __( 'Zoom Image', 'corpora_theme' ),
																								  'bk_grayscale' => __( 'Grayscale', 'corpora_theme' ),
																								  'bk_reverse_grayscale' => __( 'Reverse Grayscale', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Image Overlay', 'corpora_theme' ),
																			'id' => 'image_overlay',
																			'description' => __( 'Choose optional image overlay, it will be visible when user hovers over image.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'title',
																			'all_values' => array('none' => __( 'None', 'corpora_theme' ),
																								  'title' => __( 'Title', 'corpora_theme' ),
																								  'title_and_tags' => __( 'Title & Tags', 'corpora_theme' ),
																								  'title_and_cats' => __( 'Title & Categories', 'corpora_theme' ),
																								  'tags' => __( 'Tags', 'corpora_theme' ),
																								  'cats' => __( 'Categories', 'corpora_theme' ),
																								  'icon' => __( 'Icon', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Gutter', 'corpora_theme' ),
																			'id' => 'gutter',
																			'description' => __( 'Choose gutter size which will be visible between portfolio elements and on both sides.', 'corpora_theme' ),
																			'type' => 'slider',
																			'slider_class_type' => 'pixel',
																			'default' => 0,
																			'min' => 0,
																			'max' => 40,
																			'step' => 1,
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Remove Side Gutter', 'corpora_theme' ),
																			'id' => 'remove_side_gutter',
																			'description' => __( 'If enabled gutter on the left and right side of portfolio gallery if any will be removed. This allows to line up any addiotnal content like text that can be above or below gallery.', 'corpora_theme' ),
																			'type' => 'checkbox',
																			'default' => false,
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Portfolio Filter', 'corpora_theme' ),
																			'id' => 'show_cats',
																			'description' => __( 'Choose portfolio categories to show. Choose "All Categories" to show whole portfolio or any other single or multiple categories. Use ctrl/cmd key to select more than one category.', 'corpora_theme' ),
																			'type' => 'multiple_taxonomy_combobox',
																			'default' => array(-1),
																			'taxonomy_slug' => 'filter',
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Order Portfolio By', 'corpora_theme' ),
																			'id' => 'portfolio_order_by',
																			'description' => __( 'Choose order of appearnace for portfolio items.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'date',
																			'all_values' => array('ID' => __( 'Post id', 'corpora_theme' ),
																					'author' => __( 'Author', 'corpora_theme' ),
																					//'rand' => __( 'Random', 'corpora_theme' ),
																					'title' => __( 'Title', 'corpora_theme' ),
																					'name' => __( 'Name (post slug)', 'corpora_theme' ),
																					'date' => __( 'Date', 'corpora_theme' ),
																					'modified' => __( 'Last modified date', 'corpora_theme' ),
																					'comment_count' => __( 'Number of comments', 'corpora_theme' ),
																					'menu_order' => __( 'Page Order attribute (custom order)', 'corpora_theme' )),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Order', 'corpora_theme' ),
																			'id' => 'portfolio_order',
																			'description' => __( 'Choose order of portfolio items.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'DESC',
																			'all_values' => array('ASC' => __( 'Ascending', 'corpora_theme' ),
																								  'DESC' => __( 'Descending', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Portfolio Navigation', 'corpora_theme' ),
																			'id' => 'port_navigation',
																			'description' => __( 'Choose navigation of portfolio.', 'corpora_theme' ),
																			'type' => 'combobox',
																			'default' => 'load_more_button',
																			'all_values' => array('load_more_button' => __( 'Load More Button', 'corpora_theme' ),
																								  'infinite_scroll' => __( 'Infinite_Scroll', 'corpora_theme' ),
																								  'pagination' => __( 'Pagination', 'corpora_theme' ),
																								  'none' => __( 'None', 'corpora_theme' ) ),
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Portfolio Per Page', 'corpora_theme' ),
																			'id' => 'port_per_page',
																			'description' => __( 'Choose how many portfolio entries will be displayed on one page. For "Load More Button", "Infinite Scroll" and "None" navigation this is first subset of elements ( visible on pageload ). How many items will be loaded later via button or scroll is specifed by below option. For "Pagination" navigation type this is count of elements for each page. Use -1 to load all items.', 'corpora_theme' ),
																			'type' => 'slider',
																			'slider_class_type' => 'integer',
																			'default' => 12,
																			'min' => -1,
																			'max' => 100,
																			'step' => 1,
																			'shortcode_target' => 'attribute'
																	),
																	array(
																			'name' => __( 'Load More Count', 'corpora_theme' ),
																			'id' => 'ajax_load_more_count',
																			'description' => __( 'Choose how many portfolio items will be loaded when user scrolls the page to the bottom or click "Load More" button.', 'corpora_theme' ),
																			'type' => 'slider',
																			'slider_class_type' => 'integer',
																			'default' => 6,
																			'min' => 1,
																			'max' => 50,
																			'step' => 1,
																			'shortcode_target' => 'attribute'
																	)
																)
				),
				'bk_gallery' => array(
						'name' => __('Gallery', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Gallery Layout', 'corpora_theme' ),
										'id' => 'layout',
										'description' => __( 'Choose layout for gallery items.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'grid',
										'all_values' => array('grid' => __( 'Grid', 'corpora_theme' ),
												'metro' => __( 'Metro', 'corpora_theme' ),
												'masonry' => __( 'Masonry', 'corpora_theme' ),
												'mixed_masonry' => __( 'Mixed Masonry', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Base Size', 'corpora_theme' ),
										'id' => 'base_size',
										'description' => __( 'Choose gallery layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'small',
										'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
												'medium' => __( 'Medium', 'corpora_theme' ),
												'large' => __( 'Large', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Thumb Effect', 'corpora_theme' ),
										'id' => 'image_effect',
										'description' => __( 'Choose optional gallery thumb effect, it will be visible when user hovers over image.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'bk_zoom',
										'all_values' => array('bk_none' => __( 'None', 'corpora_theme' ),
												'bk_zoom' => __( 'Zoom Image', 'corpora_theme' ),
												'bk_grayscale' => __( 'Grayscale', 'corpora_theme' ),
												'bk_reverse_grayscale' => __( 'Reverse Grayscale', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Image Overlay', 'corpora_theme' ),
										'id' => 'image_overlay',
										'description' => __( 'Choose optional image overlay, it will be visible when user hovers over image.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'title_and_tags',
										'all_values' => array('none' => __( 'None', 'corpora_theme' ),
															  'title' => __( 'Title', 'corpora_theme' ),
															  'icon' => __( 'Icon', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gutter', 'corpora_theme' ),
										'id' => 'gutter',
										'description' => __( 'Choose gutter size which will be visible between gallery elements and on both gallery sides.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 0,
										'min' => 0,
										'max' => 40,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Remove Side Gutter', 'corpora_theme' ),
										'id' => 'remove_side_gutter',
										'description' => __( 'If enabled gutter on the left and right side of gallery if any will be removed. This allows to line up any addiotnal content like text that can be above or below gallery.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gallery Category', 'corpora_theme' ),
										'id' => 'show_cats',
										'description' => __( 'Choose gallery categories to show. Choose "All Categories" to show all gallery items or any other single or multiple categories. Use ctrl/cmd key to select more than one category.', 'corpora_theme' ),
										'type' => 'multiple_taxonomy_combobox',
										'default' => array(-1),
										'taxonomy_slug' => 'gallery_category',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Order Gallery By', 'corpora_theme' ),
										'id' => 'gallery_order_by',
										'description' => __( 'Choose order of appearnace for gallery items.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'date',
										'all_values' => array('ID' => __( 'Post id', 'corpora_theme' ),
												'author' => __( 'Author', 'corpora_theme' ),
												'title' => __( 'Title', 'corpora_theme' ),
												'name' => __( 'Name (post slug)', 'corpora_theme' ),
												'date' => __( 'Date', 'corpora_theme' ),
												'modified' => __( 'Last modified date', 'corpora_theme' ),
												'comment_count' => __( 'Number of comments', 'corpora_theme' ),
												'menu_order' => __( 'Page Order attribute (custom order)', 'corpora_theme' )),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Order', 'corpora_theme' ),
										'id' => 'gallery_order',
										'description' => __( 'Choose order of gallery items.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'DESC',
										'all_values' => array('ASC' => __( 'Ascending', 'corpora_theme' ),
												'DESC' => __( 'Descending', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gallery Navigation', 'corpora_theme' ),
										'id' => 'port_navigation',
										'description' => __( 'Choose navigation of gallery.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'load_more_button',
										'all_values' => array('load_more_button' => __( 'Load More Button', 'corpora_theme' ),
												'infinite_scroll' => __( 'Infinite_Scroll', 'corpora_theme' ),
												'pagination' => __( 'Pagination', 'corpora_theme' ),
												'none' => __( 'None', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gallery Images Per Page', 'corpora_theme' ),
										'id' => 'port_per_page',
										'description' => __( 'Choose how many gallery items will be displayed on one page. For "Load More Button", "Infinite Scroll" and "None" navigation this is first subset of elements ( visible on pageload ). How many items will be loaded later via button or scroll is specifed by below option. For "Pagination" navigation type this is count of elements for each page. Use -1 to load all items.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 12,
										'min' => -1,
										'max' => 100,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Load More Count', 'corpora_theme' ),
										'id' => 'ajax_load_more_count',
										'description' => __( 'Choose how many gallery items will be loaded when user scrolls the page to the bottom or click "Load More" button.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 6,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_blog' => array(
						'name' => __('Blog', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Base Size', 'corpora_theme' ),
										'id' => 'base_size',
										'description' => __( 'Choose grid blog layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'small',
										'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
															  'medium' => __( 'Medium', 'corpora_theme' ),
															  'large' => __( 'Large', 'corpora_theme' ),
															  'bk-classic-blog' => __( 'Classic One Column', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gutter', 'corpora_theme' ),
										'id' => 'gutter',
										'description' => __( 'Choose gutter size which will be visible between blog elements while displayed inside grid.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 0,
										'min' => 0,
										'max' => 40,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Blog Navigation', 'corpora_theme' ),
										'id' => 'blog_navigation',
										'description' => __( 'Choose navigation of blog.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'load_more_button',
										'all_values' => array('load_more_button' => __( 'Load More Button', 'corpora_theme' ),
												'infinite_scroll' => __( 'Infinite_Scroll', 'corpora_theme' ),
												'pagination' => __( 'Pagination', 'corpora_theme' ),
												'none' => __( 'None', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Posts Per Page', 'corpora_theme' ),
										'id' => 'post_per_page',
										'description' => __( 'Choose how many posts entries will be displayed on one page. For "Load More Button", "Infinite Scroll" and "None" navigation this is first subset of elements ( visible on pageload ). How many posts will be loaded later via button or scroll is specifed by below option. For "Pagination" navigation type this is count of elements for each page. Use -1 to load all items.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 12,
										'min' => -1,
										'max' => 100,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Load More Count', 'corpora_theme' ),
										'id' => 'ajax_load_more_count',
										'description' => __( 'Choose how many posts will be loaded when user scrolls the page to the bottom or click "Load More" button.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 4,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_quick_gallery' => array(
						'name' => __('Quick Gallery', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Gallery', 'corpora_theme' ),
										'id' => 'quick_gallery_id',
										'description' => __( 'Choose which gallery will be displayed.', 'corpora_theme' ),
										'type' => 'post_combobox',
										'post_type' => 'quick_gallery',
										'default' => -1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gallery Layout', 'corpora_theme' ),
										'id' => 'layout',
										'description' => __( 'Choose layout for gallery items.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'grid',
										'all_values' => array('grid' => __( 'Grid', 'corpora_theme' ),
												'metro' => __( 'Metro', 'corpora_theme' ),
												'masonry' => __( 'Masonry', 'corpora_theme' ),
												'mixed_masonry' => __( 'Mixed Masonry', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Base Size', 'corpora_theme' ),
										'id' => 'base_size',
										'description' => __( 'Choose gallery layout base size - it determines how big items will be and thus to how many columns layout will be divided.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'small',
										'all_values' => array('small' => __( 'Small', 'corpora_theme' ),
												'medium' => __( 'Medium', 'corpora_theme' ),
												'large' => __( 'Large', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Thumb Effect', 'corpora_theme' ),
										'id' => 'image_effect',
										'description' => __( 'Choose optional gallery thumb effect, it will be visible when user hovers over image.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'bk_zoom',
										'all_values' => array('bk_none' => __( 'None', 'corpora_theme' ),
												'bk_zoom' => __( 'Zoom Image', 'corpora_theme' ),
												'bk_grayscale' => __( 'Grayscale', 'corpora_theme' ),
												'bk_reverse_grayscale' => __( 'Reverse Grayscale', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Image Overlay', 'corpora_theme' ),
										'id' => 'image_overlay',
										'description' => __( 'Choose optional image overlay, it will be visible when user hovers over image.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'icon',
										'all_values' => array('none' => __( 'None', 'corpora_theme' ),
												'icon' => __( 'Icon', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Gutter', 'corpora_theme' ),
										'id' => 'gutter',
										'description' => __( 'Choose gutter size which will be visible between gallery elements.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'pixel',
										'default' => 0,
										'min' => 0,
										'max' => 40,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Remove Side Gutter', 'corpora_theme' ),
										'id' => 'remove_side_gutter',
										'description' => __( 'If enabled gutter on the left and right side of quick gallery if any will be removed. This allows to line up any addiotnal content like text that can be above or below gallery. Just use fullwidth page padding.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								),
						)
				),
				'bk_column' => array(
						'name' => __('Content', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Content', 'corpora_theme' ),
										'id' => 'content',
										'description' => __( 'Place here content that will show inside column', 'corpora_theme' ),
										'type' => 'rich_textarea',
										'default' => '',
										'shortcode_target' => 'value'
								)
						)
				),
				'bk_recent_posts_widget' => array(
						'name' => __('Recent Posts', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many recent posts will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 5,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Popup Enabled', 'corpora_theme' ),
										'id' => 'prettyphoto_enabled',
										'description' => __( 'If enabled post featured image will be displayed inside popup when thumbnail will be clicked, otherwise image will be wrapped with link to post.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_popular_posts_widget' => array(
						'name' => __('Popular Posts', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many popular posts will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 5,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Popup Enabled', 'corpora_theme' ),
										'id' => 'prettyphoto_enabled',
										'description' => __( 'If enabled post featured image will be displayed inside popup when thumbnail will be clicked, otherwise image will be wrapped with link to post.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_recent_portfolio_widget' => array(
						'name' => __('Recent Portfolio', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many recent portfolios will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 9,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Popup Enabled', 'corpora_theme' ),
										'id' => 'prettyphoto_enabled',
										'description' => __( 'If enabled post featured image will be displayed inside popup when thumbnail will be clicked, otherwise image will be wrapped with link to portfolio.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_recent_comments_widget' => array(
						'name' => __('Recent Comments', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many recent comments will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 5,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_testimonials_widget' => array(
						'name' => __('Testimonials', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many testimonials will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 5,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								)
						)
				),
				'bk_twitter_widget' => array(
						'name' => __('Twitter', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Account', 'corpora_theme' ),
										'id' => 'account',
										'description' => __( 'Specify tweeter username/account.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => '',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many tweets will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 5,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Follow Us Text', 'corpora_theme' ),
										'id' => 'follow_us_text',
										'description' => __( 'Specify follow us text that will be displayed under tweets wrapped with link to tweeter profile.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => '',
										'shortcode_target' => 'attribute'
								)
								
						)
				),
				'bk_flickr_widget' => array(
						'name' => __('Flickr', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Account', 'corpora_theme' ),
										'id' => 'account',
										'description' => __( 'Specify flickr username id e.g "52617155@N08" - go to http://idgettr.com/.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => '',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many flickr photos will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 8,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Display', 'corpora_theme' ),
										'id' => 'display',
										'description' => __( 'Choose which photos will be displayed.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'latest',
										'all_values' => array('latest' => __( 'Latest', 'corpora_theme' ),
															  'random' => __( 'Random', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
				
						)
				),
				'bk_dribbble_widget' => array(
						'name' => __('Dribbble', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Account', 'corpora_theme' ),
										'id' => 'account',
										'description' => __( 'Specify dribbble username.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => '',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many dribbble shots will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 12,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
						)
				),
				'bk_pinterest_widget' => array(
						'name' => __('Pinterest', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Account', 'corpora_theme' ),
										'id' => 'account',
										'description' => __( 'Specify pinterest username.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => '',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Count', 'corpora_theme' ),
										'id' => 'count',
										'description' => __( 'Choose how many pinterest images will be displayed.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 12,
										'min' => 1,
										'max' => 50,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
						)
				),
				'bk_separator' => array(
						'name' => __('Separator', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Type', 'corpora_theme' ),
										'id' => 'type',
										'description' => __( 'Choose type of separator - line with optional text or plain blank separator.', 'corpora_theme' ),
										'type' => 'combobox',
										'default' => 'content-separator',
										'all_values' => array('content-separator' => __( 'Line', 'corpora_theme' ),
															  'plain' => __( 'Plain', 'corpora_theme' ) ),
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Title', 'corpora_theme' ),
										'id' => 'text',
										'description' => __( 'Specify separator title - only valid for line separator type.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => 'Separator',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Margin', 'corpora_theme' ),
										'id' => 'margin',
										'description' => __( 'Choose bootom margin of separator.', 'corpora_theme' ),
										'type' => 'slider',
										'slider_class_type' => 'integer',
										'default' => 30,
										'min' => 1,
										'max' => 300,
										'step' => 1,
										'shortcode_target' => 'attribute'
								),
						)
				),
				'bk_toggle' => array(
						'name' => __('Toggle', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Title', 'corpora_theme' ),
										'id' => 'title',
										'description' => __( 'Specify toggle title.', 'corpora_theme' ),
										'type' => 'textinput',
										'default' => '',
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Closed', 'corpora_theme' ),
										'id' => 'closed',
										'description' => __( 'If enabled toggle will be closed by default on pageload.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Content', 'corpora_theme' ),
										'id' => 'content',
										'description' => __( 'Place here content that will show inside toggle.', 'corpora_theme' ),
										'type' => 'rich_textarea',
										'default' => '',
										'shortcode_target' => 'value'
								)
						)
				),
				'bk_accordion' => array(
						'name' => __('Accordion', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Closeable', 'corpora_theme' ),
										'id' => 'closeable',
										'description' => __( 'If enabled accordion can be collapsed by user, otherwise current section cannot be collapsed.', 'corpora_theme' ),
										'type' => 'checkbox',
										'default' => false,
										'shortcode_target' => 'attribute'
								),
								array(
										'name' => __( 'Content', 'corpora_theme' ),
										'id' => 'content',
										'description' => __( 'Place here content that will appear inside accordion. Use built in shortcode generator (wrench icon) to add accordion sections and fill it with content.', 'corpora_theme' ),
										'type' => 'rich_textarea',
										'default' => '',
										'shortcode_target' => 'value'
								)
						)
				),
				'bk_tabs' => array(
						'name' => __('Tabs', 'corpora_theme'),
						'fields' => array(
								array(
										'name' => __( 'Content', 'corpora_theme' ),
										'id' => 'content',
										'description' => __( 'Place here content that will appear inside tabs. Use built in shortcode generator (wrench icon) to add tabs and fill it with content.', 'corpora_theme' ),
										'type' => 'rich_textarea',
										'default' => '',
										'shortcode_target' => 'value'
								)
						)
				),
				'bk_pricing_table' => array(
						'name' => __('Pricing Table', 'corpora_theme'),
						'fields' => array(
								array(
									    'name' => __( 'Pricing Table', 'corpora_theme' ),
									    'id' => 'pricing_table_id',
									    'description' => __( 'Choose which pricing table to display.', 'corpora_theme' ),
									    'type' => 'post_combobox',
									    'post_type' => 'pricing_table',
									    'default' => -1,
										'shortcode_target' => 'attribute'
								 ) 
						)
				),
				
				
		);
		
		$this->all_sizes = array();
		$this->all_sizes["one_sixth"] = __( 'One Sixth (1/6)', 'corpora_theme' );
		$this->all_sizes["one_fifth"] = __( 'One Fifth (1/5)', 'corpora_theme' );
		$this->all_sizes["one_fourth"] = __( 'One Fourth (1/4)', 'corpora_theme' );
		$this->all_sizes["one_third"] = __( 'One Third (1/3)', 'corpora_theme' );
		$this->all_sizes["two_fifth"] = __( 'Two Fifth (2/5)', 'corpora_theme' );
		$this->all_sizes["one_half"] = __( 'One Half (1/2)', 'corpora_theme' );
		$this->all_sizes["three_fifth"] = __( 'Three Fifth (3/5)', 'corpora_theme' );
		$this->all_sizes["two_third"] = __( 'Two Thirds (2/3)', 'corpora_theme' );
		$this->all_sizes["three_fourth"] = __( 'Three Fourth (3/4)', 'corpora_theme' );
		$this->all_sizes["four_fifth"] = __( 'Four Fifth (4/5)', 'corpora_theme' );
		$this->all_sizes["five_sixth"] = __( 'Five Sixths (5/6)', 'corpora_theme' );
		$this->all_sizes["one_one"] = __( 'Fullwidth (1/1)', 'corpora_theme' );
	}
	
	/*
	 * This function loads pagebuilder state for given posts and converts it to displayable conten on the fronend ( website )
	 * what it does is simply loads data and converts each module into shortcode wrapped inside proper column shortcode to apply 
	 * visual layout form pagebuilder. Immedietely after this do_shortcode function is issued on each module which is handled
	 * inside shortcodes.php files and converst shortcodes into HTML markup which can be displayed on page
	 * 
	 * @param (integer) - $post_id - id of post /page from which content have to be loaded
	 * 
	 * @return (string) - $content HTML markup of ready to be displayed page content
	 * 
	 */
	
	public function bk_get_page_builder_content( $post_id ) {
		$content = ''; // this is simply whole markup created from shortcodes which will be converted to html and returned later on
		$page_builder_items_count = get_post_meta($post_id, '_bk_page_builder_items_count', true);
	
		for( $i = 0; $i < $page_builder_items_count; $i++ ) { 	// loop through all items where $key is number and order of element and $item is array with items settings
			$item_id = get_post_meta($post_id, '_bk_page_builder_item_type_' . $i, true);		// get common value
			$item_size = get_post_meta($post_id, '_bk_page_builder_item_size_' . $i, true);		// get common value
			$item_position = get_post_meta($post_id, '_bk_page_builder_item_position_' . $i, true); // get common value
				
			if( isset( $this->items[$item_id] ) ) {  // if item found
				$grid = $item_size;
				$item = '';
		
				if( $item_position == 'last' )
					$grid .= '_' . $item_position;
		
				if( $item_size != "one_one" )
					$item .= '[' . $grid . ']'; // create grid wrap e.g '[one_half]'
			    
				$item .= '[' . $item_id . ' ';  // create item shortcode e.g '[bk_portfolio '
				
				$shortcode_value = false; // shortcode value can be only one while attributes can be many - shortcode value is placed between shortocde tags e.g [shortcode]shortcode_value[/shortcode]
				
				foreach( $this->items[$item_id]['fields'] as $field ) {	
					$this_data_core_name = $this->get_data_core_name($item_id, $field['id']); // get data core name
					
					$value = get_post_meta($post_id, $this_data_core_name . $i, true); // get value of stored in setting
					if( $field['shortcode_target'] == 'attribute' )	{
						$item .= $this->get_shortcode_attribute($field['id'], $field['type'], $value);
					} elseif ($field['shortcode_target'] == 'value') {
						$shortcode_value = $value;
					}
					
				}
				
				//TO DO: add case for accordion , tabs empty <p> cleanup!!!!
				if($shortcode_value) { // if we have shortcode value e.g in case of column_content
					$item .= ']' . str_replace(']]>', ']]&gt;', apply_filters('the_content', $shortcode_value, true)) . '[/' . $item_id . ']';
				} else {
					$item .= '/]'; // close shortcode
				}
				
				if( $item_size != "one_one" )
					$item .= '[/' . $grid . ']';
					
				$content .= do_shortcode($item);
			}
		}	
		return $content;
	}

	/*
	 * Returns shortcdoe attribut quoted or plain
	 * 
	 * helper function used function bk_get_page_builder_content( $post_id )
	 * based on field type shortcode attribute is returned either double quoted - strings etc...
	 * or without quotes - usually numbers or boolean 
	 * 
	 * @param (string) - $field_id - id of filed used as shortcode attribue name e.g 'show_layout_switcher'
	 * @param (string) - $field_type - type of setting field e.g 'combobox'
	 * @param (mixed) - $value - value of shortcode attribue e.g 0
	 * 
	 * @return (string) - whole shortcode attribute ( name and value ) e.g 'show_layout_switcher=0 ' or 'port_navigation="load_more_button"'
	 * 					  with space at the end to make space for next attribute
	 * 
	 */
	private function get_shortcode_attribute($field_id, $field_type, $value) {
		switch($field_type) {
			case 'combobox' :
 	 	 	case 'stacked_combobox' :
 	 	 	case 'post_combobox':
 	 	 	case 'taxonomy_combobox':
 	 	 	case 'multiple_taxonomy_combobox':
 	 	 	case 'textinput':
 	 	 		return $field_id . '="' . $value .'" ';	
 	 	 	break;	
 	 	 		
 	 	 	case 'slider' :
 	 	 	case 'checkbox' :
 	 	 	case 'checkbox_section':
 	 	 		return $field_id . '=' . $value .' ';
 	 	 	break;	
		}
	}
	
	
	/*
	 * this function handles ajax request for new page builder item
	 * all parameters are passed via $_POST[] php variable
	 * 
	 * ajax action: wp_ajax_add_new_pagebuilder_item_ajax
	 * 
	 * @param (integer) - item_id - item id e.g bk_portfolio
	 * @param (integer) - default_size - default size of item in page builder
	 * @param (integer) - index - item index in moment of ajax request ( e.g 5 ). items are always appended so this
	 * 						is always equal to current amount of pagebuilder lelements on page - needed to create default
	 * 						markup with proper input names e.g bk_portfolio_show_layout_switcher_5
	 * 
	 * @return (json) - array via ajax that contains at least $data['success'] flag set to either true or false depending if module was found
	 * 					  and $data['markup'] - HTML markup of pagebuilder block in case of success
	 * 
	 */
	
	public function add_new_pagebuilder_item_ajax() {
		$data = array();	// reponse - status and markup if found
		$data['success'] = false;	// set to false by default
			
		$item_id = $_POST['item_id'];
		$default_size = $_POST['default_size'];
		$index = $_POST['index'];
		
		if( isset( $this->items[$item_id] ) ) {  // if item found
			$data['success'] = true;
			$item = $this->items[$item_id];
			
			/*
			 * get item common markup - buttons item type, position, etc...
			 * 
			 * note: set item position param to '' because once inserted pagebuilder jQuery logic will assignproper classes 'first', 'last' or ''
			 *  
			 */
			$markup = $this->get_pagebuilder_item_common_markup($item_id, $default_size, '', $index, $item['name']);
			$markup .= $this->get_pagebuilder_item_fields($item['fields'], $index, $item_id);
			
			$data['markup'] = $markup;
		}
		
		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($data);
		exit;
	}
	

	/*
	 * Helper function converts size to readable string
	 * 
	 * @param - (string) $item_size - size of item e.g 'one_half'
	 * 
	 * @return - (string) - corresponding string e.g for 'one_half' input it will return '1 / 2' string
	 * 
	 */
	private function size_to_string( $item_size ) {
		$sizes = array();
		$sizes['one_one'] = '1 / 1';
		$sizes['one_half'] = '1 / 2';
		$sizes['one_third'] = '1 / 3';
		$sizes['two_third'] = '2 / 3';
		$sizes['three_fourth'] = '3 / 4';
		$sizes['one_fourth'] = '1 / 4';
		$sizes['one_fifth'] = '1 / 5';
		$sizes['two_fifth'] = '2 / 5';
		$sizes['three_fifth'] = '3 / 5';
		$sizes['four_fifth'] = '4 / 5';
		$sizes['one_sixth'] = '1 / 6';
		$sizes['five_sixth'] = '5 / 6';
		return $sizes[$item_size];
	}
	
	/*
	 * Get common pagebuilder item markup such as edit buttons and common fields
	 * 
	 * @param - (string) $item_id - unique id of item e.g 'bk_portfolio'
	 * @param - (string) $item_size - size of item e.g 'one_half'
	 * @param - (string) $item_position - position of item - it can be 'frist', 'last' or empty string if somewhere in the middle
	 * @param - (integer) $index - index ( position ) of item which is used to create unique input names e.g
	 * 			bk_portfolio_show_layout_switcher_5 - index is 5
	 * @param - (string) $label - label that will be displayed on item e.g 'Portfolio'
	 * 
	 * @return - (string) $markup - HTML markup of common pagebuilder item structure
	 * 
	 */
	private function get_pagebuilder_item_common_markup( $item_id, $item_size, $item_position, $index, $label ) {
		$markup = '<div class="bk_page_builder_item bk_page_builder_block ' . $item_size . '">';
		$markup .= '<span class="bk_enlarge_builder_item" title="' . __('Enlarge','corpora_theme') . '">+</span>';
		$markup .= '<span class="bk_shrink_builder_item" title="' . __('Shrink','corpora_theme') . '">-</span>';
		$markup .= '<span class="bk_edit_builder_item" title="' . __('Edit','corpora_theme') . '">Edit</span>';
		$markup .= '<span class="bk_remove_builder_item" title="' . __('Remove','corpora_theme') . '">Remove</span>';
		$markup .= '<span class="bk_page_builder_item_description"><span class="bk_size_palceholder">' . bk_size_to_string( $item_size ) . '</span> ' . $label . '</span>';
		$markup .= '<input type="hidden" data-core="bk_page_builder_item_size" name="_bk_page_builder_item_size_' . $index . '" value="' . $item_size . '">';
		$markup .= '<input type="hidden" data-core="bk_page_builder_item_position" name="_bk_page_builder_item_position_' . $index . '" value="' . $item_position . '">';
		$markup .= '<input type="hidden" data-core="bk_page_builder_item_type" name="_bk_page_builder_item_type_' . $index . '" value="' . $item_id . '">';
	
		return $markup;
	}
	
	/*
	 * Get pagebuilder items fields
	 * 
	 * This function creates fields for desired module e.g 'bk_portfolio' described in $this->items array under uniqe key
	 * e.g for Portfolio module item_id is 'bk_portfolio' so this function will loop through all fields described in
	 * $this->items['bk_portfolio']['fields'] array and will create proper input ( data placeholders that can be changed via editor )
	 * 
	 * @param - (array) $fields - subarray taken from global settings that describes all fields required by this module
	 * @param - (integer) $index - index ( position ) of item which is used to create unique input names e.g
	 * 			bk_portfolio_show_layout_switcher_5 - index is 5
	 * @param - (string) $item_id - unique id of item e.g 'bk_portfolio'
	 * 
	 * @return - (string) $markup - HTML markup of all generated fields
	 * 
	 */
	
	private function get_pagebuilder_item_fields($fields, $index, $item_id) {
		$markup = '';
		
		foreach($fields as $field) {
			$field_id = $field['id']; // id of filed e.g 'show_layout_switcher'
			$data_core = $item_id . '_' . $field_id; // core unique input name e.g 'bk_portfolio_show_layout_switcher' used by jQuery logic
			$name = '_' . $data_core . '_' . $index; // name of html input, textarea element e.g '_bk_portfolio_show_layout_switcher_5'
			$data_editor_id = '_' . $data_core . '_editor'; // id of editor for this filed e.g '_bk_portfolio_show_layout_switcher_editor'
			$data_field_type = $field['type'];
			$value = $field['default'];
			
			if( is_array($value) ) {  // if value is array e.g multi_select filed then turn into string
				$value = join(',', $value);
			}
			//rich_textarea
			switch( $data_field_type ) {
				case 'rich_textarea':
					$markup .= '<textarea class="hidden" data-field-type="' . $data_field_type . '" data-editor-id="' . $data_editor_id . '" data-core="' . $data_core . '" name="' . $name . '">' . $value . '</textarea>';
				break;	
				
				default:
					$markup .= '<input type="hidden" data-field-type="' . $data_field_type . '" data-editor-id="' . $data_editor_id . '" data-core="' . $data_core . '" name="' . $name . '" value="' . $value . '">';
				break;	
			}
			
		}
		
		$markup .= '</div>';
		return $markup;
	}
	
	/*
	 * Get all page builder blocks in array item_id => item_name 
	 * used to populate page builder items combobox
	 * 
	 * @param - void
	 * @return - (array) item_id => item_name
	 * 
	 */
	
	public function get_all_pagebuilder_items() {
		$all_items = array();
		foreach( $this->items as $item_id => $item) {
			$all_items[$item_id] = $item['name'];
		}
		
		return $all_items;
	}
	
	/*
	 * Get all possible page builder items sizes
	 *  
	 * @param - void
	 * @retunr - (array) item_size_id => size_name
	 * 
	 */
	
	public function get_all_pagebuilder_sizes() {
		return $this->all_sizes;
	}
	
	/*
	 * Loads page builder state for given post id
	 * 
	 * @param - (integer) $post_id - id of post from which data should be loaded
	 * @return - (string) $content - html markup of all page builder elements
	 */
	
	public function load_pagebuilder_items($post_id) {
		$content = '';
		
		$page_builder_items_count = get_post_meta($post_id, '_bk_page_builder_items_count', true);
		
		for( $i = 0; $i < $page_builder_items_count; $i++ ) { 	
			$item_id = get_post_meta($post_id, '_bk_page_builder_item_type_' . $i, true);		// get common value
			$item_size = get_post_meta($post_id, '_bk_page_builder_item_size_' . $i, true);		// get common value
			$item_position = get_post_meta($post_id, '_bk_page_builder_item_position_' . $i, true);		// get common value
			
			if( isset($this->items[$item_id]) ) {
				$item_name = $this->items[$item_id]['name'] ;
				
				$content .= $this->get_pagebuilder_item_common_markup($item_id, $item_size, $item_position, $i, $item_name); 
				
				$fields = array();
				
				foreach( $this->items[$item_id]['fields'] as $field ) {
					//id, type, default
					$this_field = array();
					$this_field['id'] = $field['id'];
					$this_field['type'] = $field['type'];
					
					$this_data_core_name = $this->get_data_core_name($item_id, $field['id']);
					$this_field['default'] = get_post_meta($post_id, $this_data_core_name . $i, true);
					
					$fields[] = $this_field;
				}
				
				$content .= $this->get_pagebuilder_item_fields($fields, $i, $item_id);
			}
			
		}			
		
		return $content;
	}
	
	/*
	 * Saves pagebuilder state for givend post id
	 * 
	 * @param - (integer) $post_id - id of post that data will be saved
	 * 
	 */
	
	public function save_page_builder_state($post_id) {
		$current_items_count = get_post_meta($post_id, '_bk_page_builder_items_count', true);  // get current items count
		if( empty($current_items_count) )
			$current_items_count = 0;
			
		// get all modules id into flat array
		$item_ids = array_keys($this->items); 
		

		//Pre save cleanup
		for($i = 0; $i < $current_items_count; $i++) {
			foreach($item_ids as $item_id) {
				$data_core_names = $this->get_data_core_item_names($item_id); // get core names
				foreach( $data_core_names as $data_core_name ) {
					delete_post_meta($post_id, $data_core_name . $i); // remove all data e.g _bk_portfolio_show_layout_switcher_5 for $i = 5
				}
			}
		}
		
		//Save common page builder settings e.g is enabled? which items were selected when saved, how many items were inside etc...
		$page_builder_enabled = false;
		if( isset( $_POST['_bk_page_builder_enabled'] ) )
			$page_builder_enabled = $_POST['_bk_page_builder_enabled'];
		
		update_post_meta($post_id, '_bk_page_builder_enabled', $page_builder_enabled); // save switch
		update_post_meta($post_id, '_bk_page_builder_item_size_combobox', $_POST['_bk_page_builder_item_size_combobox']); // save combobox state
		update_post_meta($post_id, '_bk_page_builder_items_combobox', $_POST['_bk_page_builder_items_combobox']); // save combobox state
		
		$page_builder_items_count = intval($_POST['_bk_page_builder_items_count']);	// number of items inside page builder
		update_post_meta($post_id, '_bk_page_builder_items_count', $page_builder_items_count);
		

		//Loop through all items and save each one properly
		for( $i = 0; $i < $page_builder_items_count; $i++ ) {
			$item_id = $_POST['_bk_page_builder_item_type_' . $i];  // get common value - item type/ item id
			$item_size = $_POST['_bk_page_builder_item_size_' . $i];  // get common value - item type
			$item_position = $_POST['_bk_page_builder_item_position_' . $i];  // get common value - item position first or last - neede to layout columns properly
		
			//save common values - each block have those values set
			update_post_meta($post_id, '_bk_page_builder_item_type_' . $i , $item_id);
			update_post_meta($post_id, '_bk_page_builder_item_size_' . $i , $item_size);
			update_post_meta($post_id, '_bk_page_builder_item_position_' . $i , $item_position);
		
			//save item specific fields
			$data_core_names = $this->get_data_core_item_names($item_id); // get core names
			foreach( $data_core_names as $data_core_name ) {
				update_post_meta($post_id, $data_core_name . $i , $_POST[$data_core_name . $i]);
			}
		}			
	}
	
	/*
	 * Helper function - gets all data-core fields names for given item(module) id
	 * used in save and load pagebuilder functions
	 * 
	 * @param - (string) $item_id - id of item to query e.g bk_portolio
	 * 
	 * @return - (array) - array which contains all data-core ids / names of module e.g ['_bk_portfolio_show_layout_switcher_', '_bk_portfolio_show_cats_', ...]
	 */
	
	private function get_data_core_item_names($item_id) {
		$data_core_names = array();
		
		if( isset($this->items[$item_id]) ) {
			foreach( $this->items[$item_id]['fields'] as $field ) {
				if( isset($field['id']) ) {
					$data_core_names[] = $this->get_data_core_name($item_id, $field['id']);
				}
			}
		}
		
		return $data_core_names;
	}
	
	/*
	 * Helper function creates single data-core setting name e.g _bk_portfolio_show_layout_switcher_
	 * 
	 * @param (string) $item_id - unique id of item e.g bk_portolio
	 * @param (string) $field_id - unique id of field e.g show_layout_switcher
	 * 
	 * @return (string) - data core name e.g _bk_portfolio_show_layout_switcher_ 
	 * 
	 */
	
	private function get_data_core_name($item_id, $field_id) {
		return '_' . $item_id . '_' . $field_id . '_';
	}
	
	/*
	 * Prints out editors for each type of pagebuilder blocks
	 * editors are used inside jquery ui dialogs to change values 
	 * of page builder blocks parameters when user clicks edit icon
	 * 
	 * @param - void
	 * @return - ()
	 * 
	 */
	
	public function print_pagebuilder_editors() {
		
		echo '<div class="hiddenn">'; // wrap it up and hide
		
		//var_dump($this->items);
		
		foreach( $this->items as $item_id => $item) {  // loop through all page builder items
			$editor_id = $item_id . '_editor'; // create unique editor id
			
			echo '<div id="' . $editor_id . '" class="bk-page-builder-editor" title="'. $item['name'] .'">';
			echo '<table class="bk_form_wrap">';
			
			foreach( $item['fields'] as $item_field ) {
				
				
				/*
				 *  create prefix for page builder editor filed e.g portolio module have id bk_portfolio
				 *  prefix will be in this case _bk_portfolio_ and will be prepended to all setting fields creating unique
				 *  fields id e.g _bk_portfolio_show_layout_switcher_editor - _editor is $suffix
				 */
				$prefix = '_' . $item_id . '_'; 
				$suffix = '_editor';
								
				Bk_Field_Factory::getInstance()->print_setting_field($item_field, $item_field['default'], $prefix, $suffix);
			}
			
			echo '</table>';
			echo '</div>';
		}
		
		echo '</div>'; // end of wrap
	}
}

/*
 * Handle ajax new item request in function add_new_pagebuilder_item_ajax()
*
*/
add_action('wp_ajax_add_new_pagebuilder_item_ajax', array(Bk_Page_Builder::getInstance(), 'add_new_pagebuilder_item_ajax'));
?>