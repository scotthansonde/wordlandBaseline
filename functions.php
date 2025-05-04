<?php

/**
 * Get categories excluding Uncategorized
 * Returns false if only Uncategorized exists
 */
function get_filtered_categories() {
	$categories = get_the_category();
	$filtered_cats = array_filter($categories, function ($cat) {
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
 * Add author meta data to REST API responses
 */
function wordland_register_rest_fields() {
	register_rest_field('post', 'author_meta', [
		'get_callback' => function($post) {
			$author_id = $post['author'];
			return [
				'first_name' => get_user_meta($author_id, 'first_name', true),
				'last_name'  => get_user_meta($author_id, 'last_name', true)
			];
		}
	]);
}
add_action('rest_api_init', 'wordland_register_rest_fields');

/**
 * Enqueue scripts and styles.
 */
function wordland_enqueue_scripts() {
	// Enqueue local fonts
	wp_enqueue_style('wordland-fonts', get_template_directory_uri() . '/fonts/fonts.css', array(), '1.0.0');

	// Enqueue theme stylesheet
	wp_enqueue_style('wordland-style', get_stylesheet_uri());

	wp_enqueue_script(
		'load-more-posts',
		get_template_directory_uri() . '/js/load-more.js',
		['jquery'],
		null,
		true
	);

	wp_localize_script('load-more-posts', 'wpApiSettings', [
		'root'  => esc_url_raw(rest_url()),
		'nonce' => wp_create_nonce('wp_rest')
	]);
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
