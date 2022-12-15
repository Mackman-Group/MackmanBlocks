<?php
/**
 * This file adds functions to the Mackman WordPress theme.
 *
 * @package Mackman
 * @author  Adam Chamberlin
 * @link    https://mackman.co.uk
 */

if ( ! function_exists( 'mackman_setup' ) ) {

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since 0.8.0
	 *
	 * @return void
	 */
	function mackman_setup() {

		// Make theme available for translation.
		load_theme_textdomain( 'mackman', get_template_directory() . '/languages' );

		// Enqueue editor styles and fonts.
		add_editor_style(
			array(
				'./style.css',
			)
		);

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );

	}
}
add_action( 'after_setup_theme', 'mackman_setup' );

// Enqueue style sheet.
add_action( 'wp_enqueue_scripts', 'mackman_enqueue_style_sheet' );
function mackman_enqueue_style_sheet() {

	wp_enqueue_style( 'mackman', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );

}

/**
 * Register block styles.
 *
 * @since 0.9.2
 */
function mackman_register_block_styles() {

	$block_styles = array(
		'core/button'          => array(
			'fill-base'    => __( 'Fill Base', 'mackman' ),
			'outline-base' => __( 'Outline Base', 'mackman' ),
		),
		'core/group'           => array(
			'full-height'  => __( 'Full-height', 'mackman' ),
			'shadow'       => __( 'Shadow', 'mackman' ),
			'shadow-solid' => __( 'Shadow Solid', 'mackman' ),
		),
		'core/list'            => array(
			'no-disc' => __( 'No Disc', 'mackman' ),
		),
		'core/navigation-link' => array(
			'outline' => __( 'Outline', 'mackman' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}
add_action( 'init', 'mackman_register_block_styles' );
