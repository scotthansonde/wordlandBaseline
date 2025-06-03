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

// Control Jetpack features
function control_jetpack_features() {
	// Get settings
	$disable_sharing = get_theme_mod('baseline_disable_sharing', true);
	$disable_likes = get_theme_mod('baseline_disable_likes', true);
	$disable_related = get_theme_mod('baseline_disable_related', true);

	// Control sharing
	if ($disable_sharing) {
		add_filter('sharing_show', '__return_false', 100);
		add_filter('jetpack_sharing_show', '__return_false', 100);
		add_filter('jetpack_sharing_display', '__return_false', 100);
	}

	// Control likes
	if ($disable_likes) {
		add_filter('wpl_is_likes_visible', '__return_false', 100);
		add_filter('jetpack_likes_enabled', '__return_false', 100);
	}

	// Control related posts
	if ($disable_related) {
		add_filter('jetpack_relatedposts_enabled', '__return_false', 100);
		add_filter('jetpack_enable_related_posts', '__return_false', 100);
		// WordPress.com specific filters
		add_filter('jp_relatedposts_enabled', '__return_false', 100);
		add_filter('wpcom_disable_related_posts', '__return_true', 100);

		function baseline_disable_related_posts($options) {
			$options['enabled'] = false;
			$options['show_headline'] = false;
			return $options;
		}
		add_filter('jetpack_relatedposts_filter_options', 'baseline_disable_related_posts', 100);

		// Remove the related posts stylesheet
		add_action('wp_enqueue_scripts', function () {
			wp_dequeue_style('jetpack_related-posts');
			wp_deregister_style('jetpack_related-posts');
		}, 100);
	}
}
add_action('init', 'control_jetpack_features');

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

	// Settings: Jetpack Features
	$wp_customize->add_setting('baseline_disable_sharing', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_setting('baseline_disable_likes', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_setting('baseline_disable_related', [
		'default'           => true,
		'sanitize_callback' => 'baseline_sanitize_checkbox',
	]);

	$wp_customize->add_control('baseline_show_comments', [
		'label'    => __('Show Comments on Posts', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	// Controls: Jetpack Features
	$wp_customize->add_control('baseline_disable_sharing', [
		'label'    => __('Disable Jetpack Sharing Buttons', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	$wp_customize->add_control('baseline_disable_likes', [
		'label'    => __('Disable Jetpack Likes', 'baseline'),
		'section'  => 'baseline_options',
		'type'     => 'checkbox',
	]);

	$wp_customize->add_control('baseline_disable_related', [
		'label'    => __('Disable Jetpack Related Posts', 'baseline'),
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

	// Enqueue baseline playground styles
	wp_enqueue_style('baseline-playground', get_template_directory_uri() . '/css/baselinePlayground.css', array());

	// Enqueue theme stylesheet
	wp_enqueue_style('baseline-style', get_stylesheet_uri(), array('baseline-playground'));

	wp_enqueue_script(
		'load-more-posts',
		get_template_directory_uri() . '/js/load-more.js',
		['jquery'],
		null,
		true
	);

	wp_localize_script('load-more-posts', 'wpApiSettings', [
		'root'  => esc_url_raw(rest_url()),
		'nonce' => wp_create_nonce('wp_rest'),
		'postsPerPage' => get_option('posts_per_page')
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

/**
 * Remove og:image from Open Graph tags if it matches the site icon
 **/
add_filter(
	'jetpack_open_graph_tags',
	function ($tags) {
		// If we're not looking at a single post, bail early.
		if (! is_single()) {
			return $tags;
		}

		// If we have an image, check if that's the site icon.
		if (
			! empty($tags['og:image'])
			&& has_site_icon()
		) {
			$site_icon = wp_get_attachment_image_src(get_option('site_icon'), 'full');

			// If our OG image is the site icon, remove it.
			if (
				is_array($site_icon)
				&& $site_icon[0] === $tags['og:image']
			) {
				unset($tags['og:image']);
				unset($tags['og:image:width']);
				unset($tags['og:image:height']);
				unset($tags['og:image:alt']);
				unset($tags['twitter:image']);
				unset($tags['twitter:image.alt']);
			}
		}
		return $tags;
	},
	99
);
