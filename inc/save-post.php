<?php
// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();


/**
 * Save the post data.
 */
function gps_multidots_save_post_into_db() {
    
    if( 'guest_posts' == $_POST["post_type"] && 'POST' == $_SERVER['REQUEST_METHOD'] && current_user_can( 'author' ) && is_user_logged_in() ) {

        $post_title = sanitize_text_field( $_POST["title"] );
        $post_content = sanitize_text_field( $_POST['description'] );
        $post_excerpt = sanitize_text_field( $_POST['excerpt'] );
        $user_id = $_POST['user_id'];
        $post_type_by_author = sanitize_text_field( $_POST['post_types'] );
        $featured_image = $_FILES['files'];

        $new_post = array(
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_excerpt' => $post_excerpt,
            'post_status' => 'draft',
            'post_date' => date( 'Y-m-d H:i:s', time() ),
            'post_author' => $user_id,
            'post_type' => 'guest_posts',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'meta_input' => array(
                '_post_type_by_author' => $post_type_by_author
            )
        );
        
        if ( ! is_admin() ) {
            require_once( ABSPATH . 'wp-admin/includes/post.php' );
        }
        
        if ( post_exists( $post_title,'','','guest_posts') == 0 ) {
            
            $post_id = wp_insert_post( $new_post );
            if ( $featured_image ) {
                $upload_dir = wp_upload_dir(); // Set upload folder
                $upload = wp_upload_bits($featured_image['name'][0], null, file_get_contents($featured_image['tmp_name'][0]));
                $filename = $upload['file'];
                
                // Check folder permission and define file location
                if (wp_mkdir_p($upload_dir['path'])) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }
                $image_data = file_get_contents($upload['url']);
                // Create the image file on the server.
                file_put_contents($file, $image_data);

                $wp_filetype = wp_check_filetype($filename, null);

                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name(explode(".",array_reverse(explode("/",$filename))[0])[0]),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment($attachment, $filename, $post_id);

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
                set_post_thumbnail($post_id, $attach_id);
            }
            if( ! is_wp_error( $post_id ) ) {
                wp_send_json_success( array(
                    'status' => 'OK',
                    'message' => 'Your '. $post_title .' post submitted successfully.'
                ), 200 );
            } else {
                wp_send_json_error( $post_id->get_error_message() );
            }

        } else {
            wp_send_json_error( 'Seems post already exists with same title.' );
        }
    }
}
add_action("wp_ajax_gps_multidots_save_post_into_db", "gps_multidots_save_post_into_db");