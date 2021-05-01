<?php
// If this file is called directly, abort.
defined( 'ABSPATH' ) or die();


/**
 * Render all Posts that under the draft status with shortcode "guest_posts".
 */
function gps_multidots_list_all_guest_posts() {
   
    if ( is_user_logged_in() && current_user_can('administrator') ) {
        
        global $post;

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $posts = new WP_Query( array (
            'post_type' => 'guest_posts',
            'post_status' => 'draft',
            'posts_per_page' => 10,
            'order' => 'DESC',
            'paged' => $paged
        ) );

        if ( $posts->have_posts() ) {
            $output = '<div>';
            while ( $posts->have_posts() ) { $posts->the_post();
                $output .= '<div>';
                $output .= '<ul style="padding: 0px;"><li>';
                $output .= '<div style="display:flex;gap:1em;align-items:start;">';
                if ( has_post_thumbnail() ):
                    $thumbnail_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
                    if ( ! empty( $thumbnail_image_url[0] ) ) {
                        $output .= '<a href="' . esc_url( get_permalink() ) . '" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">';
                        $output .= get_the_post_thumbnail( get_the_ID(), 'thumbnail', 'style=width:100px;height:auto;' );
                        $output .= '</a>';
                    }
                endif;
                $output .= '<div><a href="' . esc_url( get_permalink() ) . '"><h4>' . esc_html( get_the_title() ) . '</h4></a>';
                if (strlen( get_the_excerpt() ) >= 100):
                    $output .= '<p>' . esc_html( substr( get_the_excerpt(), 0, 100 ) ) . '...</p><div>';
                else:
                    $output .= '<p>' . esc_html( get_the_excerpt() ) . '</p>';
                endif;
                $output .= '</div>';
                $output .= '<a href="'. esc_url( admin_url() ) . 'post.php?post=' . get_the_ID() . '&action=edit' .'">Approve</a>';
                $output .= '</div></li></ul>';
                $output .= '</div>';
            }
            $output .= paginate_links( array(
                'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'total'        => $posts->max_num_pages,
                'current'      => max( 1, get_query_var( 'paged' ) ),
                'format'       => '?paged=%#%'
            ) );
            wp_reset_postdata();
            $output .= '</div>';

            return $output;
        }

        return sprintf( '<div><h4>%1s</h4></div>', __("No posts found.", 'guest-posts-submission') );
    }

    return sprintf( '<div><h4>%1s</h4></div>', __("You don't have permission for this page, only admin privilege users can view this page.", 'guest-posts-submission') );
}