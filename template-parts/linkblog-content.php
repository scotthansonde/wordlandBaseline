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
$link_url = $external_url ? esc_url($external_url) : ''
?>

<div class="divLinkblogEntry">
	<?php
	// Use our reusable function to append the link
	$cleanup = wordland_append_external_link();
	
	// Display the content with our appended link
	the_content();
	
	// Remove our filter if it was added
	if ($cleanup) $cleanup();
	?>
</div>
