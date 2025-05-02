<?php

/**
 * Get categories excluding Uncategorized
 * Returns false if only Uncategorized exists
 */
function get_filtered_categories() {
    $categories = get_the_category();
    $filtered_cats = array_filter($categories, function($cat) {
        return $cat->slug !== 'uncategorized';
    });
    
    return !empty($filtered_cats) ? $filtered_cats : false;
}

/**
 * Theme functions and definitions
 *
 * @package WordlandBaselineMockup
 */

/**
 * Enqueue scripts and styles.
 */
function wordland_enqueue_scripts() {
	// Enqueue Google Fonts
	wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&family=Rancho&display=swap', array(), null);

	// Enqueue theme stylesheet
	wp_enqueue_style('wordland-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'wordland_enqueue_scripts');

/**
 * Get the date of the last modified post
 *
 * @return string Formatted date string or empty if no posts found
 */
function wordland_get_last_modified_date() {
	$args = array(
		'posts_per_page' => 1,
		'orderby' => 'modified',
		'order' => 'DESC'
	);
	$latest_post = get_posts($args);
	if ($latest_post) {
		$post = $latest_post[0];
		return get_the_modified_date('n/j/y; g:i:s A', $post);
	}
	return '';
}
