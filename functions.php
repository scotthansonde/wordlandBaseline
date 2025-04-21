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


function mytheme_register_filtered_categories_block() {
    register_block_type( 'mytheme/filtered-categories', array(
        'render_callback' => 'mytheme_render_filtered_categories_block'
    ) );
}
add_action( 'init', 'mytheme_register_filtered_categories_block' );


function mytheme_render_filtered_categories_block( $attributes, $content ) {
    if ( ! is_singular() ) {
        return '';
    }

    $categories = get_the_category();
    if ( empty( $categories ) ) return '';

    $filtered = array_filter( $categories, fn( $cat ) => $cat->slug !== 'uncategorized' );
    if ( empty( $filtered ) ) return '';

    $output = '<div class="wp-block-post-terms has-text-color has-theme-3-color" style="color: var(--wp--preset--color--theme-3);">';
    $output .= 'Categories: ';

    // Collect linked category names into an array
    $linked_names = array_map(function($cat) {
        return '<a href="' . esc_url(get_category_link($cat)) . '" style="color: var(--wp--preset--color--theme-3);">' . esc_html($cat->name) . '</a>';
    }, $filtered);

    // Join them with commas
    $output .= implode(', ', $linked_names);
    $output .= '</div>';

    return $output;
}