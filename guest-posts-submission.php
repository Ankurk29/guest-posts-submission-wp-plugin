<?php
/**
 * Plugin Name:       Guest Posts Submission
 * Plugin URI:        https://www.multidots.com/
 * Description:       Handles the posts submission from front-end side of website, so that guest authors can submit posts from front-side.
 * Version:           1.0.0
 * Author:            Ankur Kumar
 * Author URI:        https://www.multidots.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       guest-posts-submission
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// Declartion of necessary constants for plugin
defined( 'GPS_VERSION' ) or define( 'GPS_VERSION', '1.0.0' );
defined( 'GPS_JS_DIR' ) or define( 'GPS_JS_DIR', plugin_dir_url( __FILE__ ) . 'js' );
defined( 'GPS_IMG_DIR' ) or define( 'GPS_IMG_DIR', plugin_dir_url( __FILE__ ) . 'images' );

/**
 * Load all necessary files.
 */
include_once('inc/post-types.php');
include_once('inc/front-form.php');
include_once('inc/save-post.php');
include_once('inc/email-notification.php');
include_once('inc/all-posts.php');

/**
 * Activate the plugin.
 */
function gps_multidots_activate() { 
    gps_multidots_setup_post_type(); // Trigger our function that registers the custom post type.
    gps_multidots_register_shortcodes(); // Trigger our function that create shortcode for add post form.
    flush_rewrite_rules(); // Clear the permalinks after the post type has been registered.
}
register_activation_hook( __FILE__, 'gps_multidots_activate' );


/**
 * Deactivate the plugin.
 */
function gps_multidots_deactivate() {
    unregister_post_type( 'guest_posts' ); // Unregister the post type, so the rules are no longer in memory.
    flush_rewrite_rules(); // Clear the permalinks to remove our post type's rules from the database.
}
register_deactivation_hook( __FILE__, 'gps_multidots_deactivate' );


/**
 * Uninstall the plugin.
 */
function gps_multidots_uninstall() {
    global $post;

    $posts = get_posts( array(
            'posts_per_page' => -1,
            'post_type'      => 'guest_posts',
            'post_status'    => 'any'
        )
    );

    // Delete all posts that's linked with "Guest post" post type.
    foreach ( $posts as $post ){
        wp_delete_post( $post->ID, true );
    }
}
register_uninstall_hook(__FILE__, 'gps_multidots_uninstall');


/**
 * Setup JavaScript
 */
add_action( 'wp_enqueue_scripts', function() {
    
	wp_enqueue_script( 'gps-post-submitter', GPS_JS_DIR . '/gps-post-submitter.js', array( 'jquery' ), GPS_VERSION );

	wp_localize_script( 'gps-post-submitter', 'POST_SUBMITTER', array(
			'root' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		)
	);

});


/**
 * Setup shortcodes for front form and all posts.
 */
function gps_multidots_register_shortcodes(){
    add_shortcode('guest-posts-form', 'gps_multidots_add_post_form');
    add_shortcode('guest-posts', 'gps_multidots_list_all_guest_posts');
}

add_action( 'init', 'gps_multidots_register_shortcodes');

