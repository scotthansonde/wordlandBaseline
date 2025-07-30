<?php

/**
 * Template part for displaying linkblog posts
 */
?>

<?php
// Get the external URL from either wordland_linksTo or external_url custom field
$external_url = get_post_meta(get_the_ID(), 'wordland_linksTo', true);
if (empty($external_url)) {
	$external_url = get_post_meta(get_the_ID(), 'external_url', true);
}

// Fallback to post permalink if no external URL
$link_url = $external_url ? esc_url($external_url) : get_permalink();
?>

<div class="divLinkblogEntry">
	<?php
	// Add filter to append link to content by inserting it into the last paragraph
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

	add_filter('the_content', $append_link_filter);

	// Display the content with our appended link
	the_content();

	// Remove our filter to avoid affecting other content
	remove_filter('the_content', $append_link_filter);
	?>
</div>
