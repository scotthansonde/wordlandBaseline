<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

require_once get_template_directory() . '/inc/social-icons.php';

/**
 * Theme functions and definitions
 *
 * @package WordlandBaseline
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function baseline_setup() {
	// Let WordPress manage the document title
	add_theme_support('title-tag');

	// Add default posts and comments RSS feed links to head
	add_theme_support('automatic-feed-links');
}
add_action('after_setup_theme', 'baseline_setup');

/**
 * Sets up customize options
 */

function baseline_customize_register($wp_customize) {

	// Section: Theme Options
	$wp_customize->add_section('baseline_options', [
		'title'    => __('Baseline Theme Options', 'baseline'),
		'priority' => 30,
	]);

	// Setting: Show Comments
	$wp_customize->add_setting('baseline_show_comments', [
		'default'           => false,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_control('baseline_show_comments', [
		'label'    => __('Show Comments on Posts', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);
}
add_action('customize_register', 'baseline_customize_register');

// Sanitize checkboxes
function baseline_sanitize_checkbox($checked) {
	return (isset($checked) && $checked === true) ? true : false;
}



/**
 * Enqueue scripts and styles.
 */
function baseline_enqueue_scripts() {
	// Enqueue Google Fonts
	wp_enqueue_style('google-font-ubuntu', 'https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700', array());
	wp_enqueue_style('google-font-rancho', 'https://fonts.googleapis.com/css?family=Rancho', array());

	// Enqueue external baseline playground styles
	wp_enqueue_style('baseline-playground', 'https://s3.amazonaws.com/scripting.com/code/baselineplayground/styles.css', array());

	// Enqueue theme stylesheet
	wp_enqueue_style('wordland-style', get_stylesheet_uri(), array('baseline-playground'));

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
add_action('wp_enqueue_scripts', 'baseline_enqueue_scripts');

/**
 * Add author meta data to REST API responses
 */
function baseline_register_rest_fields() {
	register_rest_field('post', 'author_meta', [
		'get_callback' => function ($post) {
			$author_id = $post['author'];
			return [
				'first_name' => get_user_meta($author_id, 'first_name', true),
				'last_name'  => get_user_meta($author_id, 'last_name', true)
			];
		}
	]);
}
add_action('rest_api_init', 'baseline_register_rest_fields');

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
 * Get the date of the last modified post
 *
 * @return string Formatted date string or empty if no posts found
 */
function baseline_get_last_modified_date() {
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
