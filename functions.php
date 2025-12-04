<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

require_once get_template_directory() . '/inc/social-icons.php';
require_once get_template_directory() . '/inc/content-links.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/jetpack-control.php';

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
 * Enqueue jQuery before any plugins are loaded
 * This ensures jQuery is available for all plugin scripts
 */
function baseline_enqueue_jquery() {
	wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'baseline_enqueue_jquery');

/**
 * Enqueue scripts and styles.
 */
function baseline_enqueue_scripts() {
	// Get theme version for enqueuing local styles
	$theme_data = wp_get_theme();
	$version = $theme_data->Version;
	// Enqueue Google Fonts
	wp_enqueue_style('google-font-ubuntu', 'https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700', array());
	wp_enqueue_style('google-font-rancho', 'https://fonts.googleapis.com/css?family=Rancho', array());

	// Enqueue theme stylesheet
	wp_enqueue_style('baseline-style', get_stylesheet_uri(), array('baseline-playground'), $version);
	// Enqueue baseline playground styles
	wp_enqueue_style('baseline-playground', get_template_directory_uri() . '/css/baselinePlayground.css', array(), $version);
	// Enqueue baseline playground styles from Scripting.com for development, comment out for production
	// wp_enqueue_style('baseline-playground-scripting', 'https://s3.amazonaws.com/scripting.com/code/baselineplayground/styles.css?t=' . time(), array());
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
				'last_name'  => get_user_meta($author_id, 'last_name', true),
				'website'    => get_the_author_meta('user_url', $author_id)
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
 * Display author name with website link if available
 *
 * @param int|null $author_id Optional. User ID of the author. Default is current post author.
 * @param bool $echo Optional. Whether to echo or return the result. Default true.
 * @return string|void Author name with or without link
 */
function baseline_author_website_link($author_id = null, $echo = true) {
	if (null === $author_id) {
		$author_id = get_the_author_meta('ID');
	}

	$first_name = get_user_meta($author_id, 'first_name', true);
	$last_name = get_user_meta($author_id, 'last_name', true);
	$author_name = $first_name . ' ' . $last_name;

	$website = get_the_author_meta('user_url', $author_id);

	$output = '';
	if (!empty($website)) {
		$output = '<a href="' . esc_url($website) . '">' . $author_name . '</a>';
	} else {
		$output = $author_name;
	}

	if ($echo) {
		echo $output;
	} else {
		return $output;
	}
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

function get_domain_from_url($url) {
	// Parse the host from the URL
	$host = parse_url($url, PHP_URL_HOST);

	if (!$host) {
		return false; // Invalid URL
	}

	// Remove 'www.' or other common subdomains
	$host_parts = explode('.', $host);

	// Handle cases like subdomain.example.co.uk
	$count = count($host_parts);

	if ($count >= 2) {
		// Return the last two parts by default
		$domain = $host_parts[$count - 2] . '.' . $host_parts[$count - 1];

		// Handle known 2-part TLDs (like co.uk, com.au)
		$two_part_tlds = ['co.uk', 'com.au', 'org.uk', 'gov.uk', 'co.jp'];
		$tld = $host_parts[$count - 2] . '.' . $host_parts[$count - 1];

		if (in_array($tld, $two_part_tlds) && $count >= 3) {
			$domain = $host_parts[$count - 3] . '.' . $tld;
		}

		return $domain;
	}

	return $host;
}

// Filter the RSS feed to use the external URL for the <link> element, if available
// Thanks to @jeherve (https://github.com/scotthansonde/wordlandBaseline/issues/49)
add_filter(
	'the_permalink_rss',
	function ($permalink) {
		global $wp_query;
		if (empty($wp_query->post)) {
			return $permalink;
		}
		$post_id = $wp_query->post->ID;
		if (! $post_id) {
			return $permalink;
		}

		// Get the external URL from the post meta.
		$external_url = get_post_meta($post_id, 'wordland_linksTo', true);
		if (empty($external_url)) {
			$external_url = get_post_meta($post_id, 'external_url', true);
		}

		if (! empty($external_url)) {
			return $external_url;
		}

		return $permalink;
	}
);
