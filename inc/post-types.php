<?php
// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();


/**
 * Register the "Guest Posts" custom post type
 */
function gps_multidots_setup_post_type() {

    $labels = [
        "name" => __( "Guest Posts", "guest-posts-submission" ),
        "singular_name" => __( "Guest Post", "guest-posts-submission" ),
    ];

    $args = [
        "label" => __( "Guest Posts", "guest-posts-submission" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => false,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => true,
        "show_in_menu" => true, 
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "menu_icon" => 'dashicons-admin-page',
        "rewrite" => [ "slug" => "guest_posts", "with_front" => true ],
        "query_var" => true,
        "supports" => [ "title", "editor", "excerpt", "thumbnail", "author" ],
        'register_meta_box_cb' => 'gps_register_custom_field'
    ];

    register_post_type( "guest_posts", $args );

} 
add_action( 'init', 'gps_multidots_setup_post_type' );


/**
 * Register the "Custom Post Type" custom field for author select from frontend.
 */
function gps_register_custom_field() {
    add_meta_box( 'custom-post-type-fields', __( 'Custom Fields', 'guest-posts-submission' ), 'gps_custom_field_callback', 'guest_posts', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'gps_register_custom_field');
 

/**
 * View the custom field at post page in dashboard.
 */
function gps_custom_field_callback( $meta_id ) {
 
    $output = '<label for="post_type_by_author" style="width:150px; display:inline-block;">'. esc_html__('Custom Post Type', 'guest-posts-submission') .'</label>';
    $post_type_by_author = get_post_meta( $meta_id->ID, '_post_type_by_author', true );
    $output .= '<input type="text" name="_post_type_by_author" id="post_type_by_author" class="post_type_by_author" value="'. esc_attr( $post_type_by_author ) .'" style="width: 100%; margin-top: 10px;" readonly />';
 
    echo $output;
}