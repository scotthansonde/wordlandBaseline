<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Jetpack Feature Control
 *
 * @package WordlandBaseline
 */

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
