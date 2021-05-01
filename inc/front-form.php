<?php
// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();


/**
 * Post form for shortcode "guest-posts-form".
 */
function gps_multidots_add_post_form() {
    ob_start();
    $form = '
        <div class="col-sm-12">
        <h3>Add New Post</h3>
        <form class="form-horizontal" id="post-submission-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="post_type" id="post_type" value="guest_posts" />
            <input type="hidden" name="userid" id="user_id" value="'.get_current_user_id().'" />
            
            <div class="col-md-12">
                <label for="title">'.__( 'Post Title', 'guest-posts-submission' ).'</label>
                <input type="text" id="title" value="" name="title" required aria-required="true" />
            </div>

            <div class="col-md-12">
                <label for="post_types">'.__( 'Choose Post Type', 'guest-posts-submission' ).'</label>
                <select name="post_types" id="post_types" required aria-required="true">';
                $args = array(
                    'public'   => true,
                    '_builtin' => false
                );
                $post_types = get_post_types( $args, 'objects' );
                
                foreach($post_types as $post_type_obj) {
                    $labels = get_post_type_labels( $post_type_obj );
                    $form .= '<option value="'.esc_attr( $post_type_obj->name ).'">'.esc_html( $labels->name ).'</option>'; 
                }
                $form .= '</select>
                </div>

                <div class="col-md-12">
                    <label for="description">'.__( 'Description', 'guest-posts-submission' ).'</label>
                    <textarea class="tinymce_class" id="description" rows="8" name="description" required aria-required="true"></textarea>
                </div>

                <div class="col-md-12">
                    <label for="excerpt">'.__( 'Excerpt', 'guest-posts-submission' ).'</label>
                    <textarea id="excerpt" rows="4" name="excerpt" required aria-required="true"></textarea>
                </div>

                <div class="col-md-12">
                    <label for="featured_image_from_frontend">'.__( 'Upload Featured Image', 'guest-posts-submission' ).'</label>
                    <input type="file" name="featured_image_from_frontend" id="featured_image_from_frontend" accept="image/*" required aria-required="true" value="" />
                </div>

                <div class="col-md-12">
                    <input type="submit" class="btn btn-primary" value="Submit " id="submit" name="submit" />
                    <img src="' . GPS_IMG_DIR . '/gps-ajax-loader.gif" class="post-ajax-loader" style="display:none;" />
                </div>
            </form>
            <div class="clearfix"></div>
        </div>
    ';
    $form .= ob_get_clean();
    
    if ( is_user_logged_in() ) {
        if ( current_user_can( 'author' ) ) {
            return $form;
        }
        return sprintf( '<div class="col-sm-12"><h4>%1s</h4></div>', __("You don't have permission to create post from front-side of website, use author account to create posts. ", 'guest-posts-submission') );
    }
    
    return sprintf( '<div class="col-sm-12"><h4>%1s<br><a href="%2s">%3s</a></h4></div>', __("Please log in to create post from front-side of website. ", 'guest-posts-submission'), esc_url( wp_login_url() ), __( 'Click Here To Login', 'guest-posts-submission' ) );
}