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