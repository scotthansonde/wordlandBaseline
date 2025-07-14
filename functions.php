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

	// Setting: Disable Pagination
	$wp_customize->add_setting('baseline_disable_pagination', [
		'default'           => true,
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

	// Control: Disable Pagination
	$wp_customize->add_control('baseline_disable_pagination', [
		'label'    => __('Disable Pagination on Home Page', 'baseline'),
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

// Add customizer options for home page template
add_action('customize_register', function ($wp_customize) {
	// Add a section for our template options
	$wp_customize->add_section('linkblog_template_options', array(
		'title'    => __('Linkblog Template Options', 'linkblog-importer'),
		'priority' => 120,
	));

	// Add setting for home page template choice
	$wp_customize->add_setting('linkblog_home_template', array(
		'default'           => 'default',
		'sanitize_callback' => 'sanitize_key',
		'transport'         => 'refresh',
	));

	// Add control for the setting
	$wp_customize->add_control('linkblog_home_template', array(
		'label'    => __('Home Page Template', 'linkblog-importer'),
		'section'  => 'linkblog_template_options',
		'type'     => 'radio',
		'choices'  => array(
			'default'  => __('Default (index.php)', 'linkblog-importer'),
			'linkblog' => __('Linkblog Template (category-linkblog.php)', 'linkblog-importer'),
		),
	));
});

// Filter the home template based on customizer setting
add_filter('home_template', function ($template) {
	$home_template = get_theme_mod('linkblog_home_template', 'default');

	if ($home_template === 'linkblog') {
		$linkblog_template = locate_template('category-linkblog.php');
		if ($linkblog_template) {
			return $linkblog_template;
		}
	}

	return $template;
});
