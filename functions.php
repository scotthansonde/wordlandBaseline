<?php
add_action( 'wp_enqueue_scripts', function() {
    // Enqueue parent theme's stylesheet
    wp_enqueue_style( 'retrospect-parent-style', get_template_directory_uri() . '/style.css' );

    // Enqueue child theme's stylesheet
    wp_enqueue_style( 'wordland-child-style', get_stylesheet_uri(), ['retrospect-parent-style'] );
});

add_filter( 'render_block_core/social-link', function( $block_content, $block ) {
    if ( isset( $block['attrs']['service'] ) && $block['attrs']['service'] === 'feed' ) {
        // Replace href="#" or whatever is hardcoded with the dynamic feed link
        $feed_url = esc_url( get_feed_link() );
        $block_content = preg_replace(
            '/<a[^>]+href="[^"]*"([^>]*)>/',
            '<a href="' . $feed_url . '"$1>',
            $block_content
        );
    }
    return $block_content;
}, 10, 2 );

// Register categories filter
add_filter( 'get_the_terms', 'mytheme_filter_uncategorized_terms', 10, 3 );

function mytheme_filter_uncategorized_terms( $terms, $post_id, $taxonomy ) {
    if ( is_admin() || ! is_main_query() || 'category' !== $taxonomy || ! is_singular() ) {
        return $terms;
    }

    if ( empty( $terms ) || ! is_array( $terms ) ) {
        return $terms;
    }

    return array_filter( $terms, function( $term ) {
        return $term->slug !== 'uncategorized';
    } );
}

// Tooltip for next/previous links
add_filter( 'render_block_core/post-navigation-link', function( $block_content, $block ) {
        global $post;

        $is_previous = isset( $block['attrs']['type'] ) && $block['attrs']['type'] === 'previous';
        $adjacent_post = $is_previous ? get_previous_post() : get_next_post();

        if ( ! $adjacent_post ) {
            return ''; // No adjacent post, return nothing
        }

        $arrow = $is_previous ? '←' : '→';
        $title = esc_attr( get_the_title( $adjacent_post ) );
        $link  = get_permalink( $adjacent_post );
        $rel   = $is_previous ? 'prev' : 'next';

        // Return custom HTML — arrow as link, tooltip as title
        return sprintf(
            '<div class="post-navigation-link-%s wp-block-post-navigation-link arrow-only">' .
                '<a href="%s" rel="%s" title="%s">%s</a>' .
            '</div>',
            esc_attr( $is_previous ? 'previous' : 'next' ),
            esc_url( $link ),
            esc_attr( $rel ),
            $title,
            esc_html( $arrow )
        );
    
    return $block_content;
}, 10, 2 );