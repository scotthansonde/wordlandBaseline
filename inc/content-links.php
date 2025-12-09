<?php

/**
 * Appends an external link to post content if available
 *
 * @param int|null $post_id Optional post ID. Defaults to current post.
 * @return callable|null Function to remove the filter or null if no link found
 */
function wordland_append_external_link($post_id = null) {
	if (!$post_id) {
		$post_id = get_the_ID();
	}

	// Get the external URL from either wordland_linksTo or external_url custom field
	$external_url = get_post_meta($post_id, 'wordland_linksTo', true);
	if (empty($external_url)) {
		$external_url = get_post_meta($post_id, 'external_url', true);
	}

	// Only proceed if we have an external URL
	if (!empty($external_url)) {
		$link_url = esc_url($external_url);

		// Create filter function to append link
		$append_link_filter = function ($content) use ($link_url) {
			$domain = get_domain_from_url($link_url);
			$link_html = ' <a href="' . $link_url . '" target="_blank" rel="noopener">' . $domain . '</a>';

			// Check if content ends with a closing paragraph tag
			if (preg_match('/<\/p>(\s*)$/i', $content)) {
				// Insert link before the closing paragraph tag
				$content = preg_replace('/<\/p>(\s*)$/i', $link_html . '</p>$1', $content);
			} else {
				// If no closing paragraph tag, just append
				$content .= $link_html;
			}

			return $content;
		};

		// Add filter
		add_filter('the_content', $append_link_filter);

		// Return a function that will remove the filter when called
		return function () use ($append_link_filter) {
			remove_filter('the_content', $append_link_filter);
		};
	}

	// If no external URL, check if post has no title
	$post_title = get_the_title($post_id);
	if (empty(trim($post_title))) {
		$link_url = get_permalink($post_id);

		// Create filter function to append pound sign link
		$append_link_filter = function ($content) use ($link_url) {
			$link_html = ' <span class="spPermaLink"><a href="' . esc_url($link_url) . '" title="Direct link to this item.">#</a></span>';

			// Check if content ends with a closing paragraph tag
			if (preg_match('/<\/p>(\s*)$/i', $content)) {
				// Insert link before the closing paragraph tag
				$content = preg_replace('/<\/p>(\s*)$/i', $link_html . '</p>$1', $content);
			} else {
				// If no closing paragraph tag, just append
				$content .= $link_html;
			}

			return $content;
		};

		// Add filter
		add_filter('the_content', $append_link_filter);

		// Return a function that will remove the filter when called
		return function () use ($append_link_filter) {
			remove_filter('the_content', $append_link_filter);
		};
	}

	return null;
}
