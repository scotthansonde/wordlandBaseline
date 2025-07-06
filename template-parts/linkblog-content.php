<?php

/**
 * Template part for displaying linkblog posts
 */
?>
<div class="divStory">
	<?php $has_title = get_the_title();

	// Get the external URL custom field
	$external_url = get_post_meta(get_the_ID(), 'external_url', true);

	// Fallback to post permalink if no external URL
	$link_url = $external_url ? esc_url($external_url) : get_permalink();
	?>
	<div class="divStoryTitle">
		<?php if ($has_title) : ?>
			<?php the_title(); ?>
		<?php endif; ?>
	</div>
	<?php if ($has_title) : ?>
		<div class="XXXdivLineUnderStoryTitle">
			<?php echo get_the_date(); ?> by <?php baseline_author_website_link(); ?>
		</div>
	<?php endif; ?>

	<div class="divStoryBody">
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
		<!-- Comments disabled by default, can be enabled in Customizer -->
		<?php if (is_single() && get_theme_mod('baseline_show_comments', false)) : ?>
			<?php if (comments_open() || get_comments_number()) : ?>
				<div class="divComments">
					<?php comments_template(); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php if (is_single()) : ?>
		<?php $categories = get_filtered_categories(); ?>
		<?php if ($categories) : ?>
			<div class="divCategories">
				Categories: <?php echo join(', ', array_map(function ($cat) {
								return '<a href="' . esc_url(get_category_feed_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a>';
							}, $categories)); ?>.
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
