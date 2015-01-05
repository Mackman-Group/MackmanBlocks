<?php
/*
Plugin Name: Mackman Staff Profiles
Plugin URI: http://www.mackmangroup.co.uk
Description: WordPress plugin to manage Mackman Staff profiles
Version: 0.1
Author: Adam Chamberlin
Author URI: http://www.mackmangroup.co.uk
License: 
*/

// If we are trying to access directly
if ( ! defined( 'ABSPATH' ) ) exit;


/*

  This plugin also requires some template files in the theme.
  
  Currently we are using the Division theme and so far I have modified the following files:
  single-mackman_staff.php
  template_staff.php

*/

/**
 * mackman_staff_post_type function.
 * 
 * @access public
 * @return void
 */
function mackman_staff_post_type() {
  $args = array(
    'labels' => array(
                      'name'          => __( 'Staff' ),
                      'singular_name' => __( 'Staff' )
                      ),
    'public' => true,
    'menu_position' => 20,
    'rewrite' => array(
                       'slug'       => 'staff',
                       'with_front' => false
                       ),
    'supports' => array(
                        'title',
                        'editor',
                        'excerpt',
                        'thumbnail',
                        'custom-fields',
                        'page-attributes',
                        ),
  );
  
  register_post_type( 'mackman_staff', $args);
}

/**
 * mackman_portfolio_meta function.
 * 
 * @access public
 * @return void
 */
function mackman_portfolio_meta() {
  add_meta_box( 'mackman_portfolio_meta', 'Assign To Staff Member', 'mackman_portfolio_meta_box', 'portfolio', 'side' );
}

/**
 * mackman_portfolio_meta_box function.
 * 
 * @access public
 * @param mixed $post
 * @return void
 */
function mackman_portfolio_meta_box( $post ) {

  // Get the values for the current staff member
  $values = get_post_custom( $post->ID );
  
  // Set the WP user ID
  $wp_id = isset( $values['mg_wp_user'] ) ? esc_attr( $values['mg_wp_user'][0] ) : false;
  
  $user_args = array(
                     'show_option_all'         => null, // string
                     'show_option_none'        => null, // string
                     'hide_if_only_one_author' => null, // string
                     'orderby'                 => 'display_name',
                     'order'                   => 'ASC',
                     'include'                 => null, // string
                     'exclude'                 => null, // string
                     'multi'                   => false,
                     'show'                    => 'display_name',
                     'echo'                    => true,
                     'selected'                => $wp_id,
                     'include_selected'        => false,
                     'name'                    => 'mg_wp_user', // string
                     'id'                      => null, // integer
                     'class'                   => null, // string 
                     'blog_id'                 => $GLOBALS['blog_id'],
                     'who'                     => null // string
                     );
  
  ?>
    <p>
      <label for="mg_wp_user">Link portfolio to user</label>
      <?php wp_dropdown_users( $user_args ); ?>
    </p>
  <?php
}

/**
 * mackman_staff_meta function.
 * 
 * @access public
 * @return void
 */
function mackman_staff_meta() {
  add_meta_box( 'mackman_staff_meta', 'Staff Details', 'mackman_staff_meta_box', 'mackman_staff', 'side' );
}


/**
 * mackman_staff_meta_box function.
 * 
 * @access public
 * @param mixed $post
 * @return void
 */
function mackman_staff_meta_box( $post ) {

  // Get the values for the current staff member
  $values = get_post_custom( $post->ID );
  
  // Set the job title
  $job_title = isset( $values['mg_staff_job'] ) ? esc_attr( $values['mg_staff_job'][0] ) : '';
  
  // Set the WP user ID
  $wp_id = isset( $values['mg_wp_user'] ) ? esc_attr( $values['mg_wp_user'][0] ) : false;
  
  $user_args = array(
                     'show_option_all'         => null, // string
                     'show_option_none'        => 'Do Not Link', // string
                     'hide_if_only_one_author' => null, // string
                     'orderby'                 => 'display_name',
                     'order'                   => 'ASC',
                     'include'                 => null, // string
                     'exclude'                 => null, // string
                     'multi'                   => false,
                     'show'                    => 'display_name',
                     'echo'                    => true,
                     'selected'                => $wp_id,
                     'include_selected'        => false,
                     'name'                    => 'mg_wp_user', // string
                     'id'                      => null, // integer
                     'class'                   => null, // string 
                     'blog_id'                 => $GLOBALS['blog_id'],
                     'who'                     => null // string
                     );
  
  ?>
    <p>
      <label for="mg_staff_job">Job Title</label>
    </p>
    <p>
      <input type="text" name="mg_staff_job" id="mg_staff_job" value="<?php echo $job_title; ?>" />
    </p>
    <p>
      <label for="mg_wp_user">Link to WordPress author</label>
      <?php wp_dropdown_users( $user_args ); ?>
    </p>
  <?php
}

/**
 * mackman_staff_meta_save function.
 * 
 * @access public
 * @param mixed $post_id
 * @return void
 */
function mackman_staff_meta_save( $post_id ) {
  
  // No go if we are autosaving
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }
     
  // No go if the user can not edit
  if ( !current_user_can( 'edit_post' ) ) {
    return;
  }
  
  // If everything is ok then save the job title
  if ( isset( $_POST['mg_staff_job'] ) ) {
    update_post_meta( $post_id, 'mg_staff_job', esc_attr( $_POST['mg_staff_job'] ) ); 
  }
  
  if ( isset( $_POST['mg_wp_user'] ) ) {
    update_post_meta( $post_id, 'mg_wp_user', esc_attr( $_POST['mg_wp_user'] ) ); 
  }

}


// Add the post type
add_action( 'init', 'mackman_staff_post_type' );

// Add the departments meta box to the staff custom post type
add_action( 'add_meta_boxes', 'mackman_staff_meta' );
add_action( 'add_meta_boxes', 'mackman_portfolio_meta' );

// Add save function
add_action( 'save_post', 'mackman_staff_meta_save' );