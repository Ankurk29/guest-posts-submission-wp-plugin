<?php
// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();


/**
 * Send email notification when post submited.
 */
function gps_send_mails_on_post_create( $post_id, $post, $update ) {
    if ( $post->post_type === 'guest_posts' ) {
        // If an old post is being updated, exit
        if ( $update ) {
            return;
        }
        // Get admin users which have admin privileges.
        $admin_email = get_option( 'admin_email' );
        // $all_admins = get_users( array ( 'role' => 'administrator' ) );
        // $admin_email = array();
        // foreach ( $all_admins as $admin ) {
        //     $admin_email[] = $admin->user_email;
        // }
        

        $author_id = $post->post_author; // Current author id.
        
        // Get domain with http protocol for from email.
        $url_parts = parse_url(home_url());
        $domain = $url_parts['host'];
        // $from_email = get_option( 'admin_email' );
        $from_email = 'wordpress@' . $domain;
        $blogname = get_option( 'blogname' ); // Website name.
        $post_title = get_the_title( $post_id ); // Current post title.
        $headers = "MIME-Version: 1.0\r\n" . "From: " . $blogname . " " . "<" . $from_email . ">\n" . "Content-Type: text/HTML; charset=\"" . get_option( 'blog_charset' ) . "\"\r\n";
        $message = __( 'Hello there,', 'guest-posts-submission' ) . '<br/><br/>' .
                __( 'A new post has been submitted via Guest Post Submission plugin in ', 'guest-posts-submission' ) . $blogname . ' site.' . __( ' Please find details below:', 'guest-posts-submission' ) . '<br/><br/>' .
                'Post title: ' . $post_title . '<br/><br/>';
        $post_author_name = get_the_author_meta( 'display_name', $author_id);
        $post_author_email = get_the_author_meta( 'user_email', $author_id);
        $post_author_url = get_the_author_meta( 'user_url', $author_id);

        if ( $post_author_name != '' ) {
            $message .= 'Post Author Name: ' . $post_author_name . '<br/><br/>';
        }
        if ( $post_author_email != '' ) {
            $message .= 'Post Author Email: ' . $post_author_email . '<br/><br/>';
        }
        if ( $post_author_url != '' ) {
            $message .= 'Post Author URL: ' . $post_author_url . '<br/><br/>';
        }


        $message .= '____<br/><br/>
                    ' . __( 'To take action (approve/reject)- please go here:', 'guest-posts-submission' ) . '<br/>'
                . admin_url() . 'post.php?post=' . $post_id . '&action=edit <br/><br/>

                    ' . __( 'Thank You', 'guest-posts-submission' );
        $subject = __( 'New Post Submission - via Guest Post Submission', 'guest-posts-submission' );
        
        wp_mail( $admin_email, $subject, $message, $headers );
    }
}

add_action( 'save_post_guest_posts', 'gps_send_mails_on_post_create', 10, 3 );