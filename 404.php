<?php

/**
 * The template for displaying 404 pages (not found)
 *
 * Displays error page when content cannot be found
 */

get_header(); ?>

<div class="divStory">
	<div class="divStoryTitle">
		<?php esc_html_e("That page can't be found.", 'wordlandbaseline'); ?>
	</div>
	<div class="divStoryBody">
		<p><?php esc_html_e('It looks like nothing was found at this location. Try the', 'wordlandbaseline'); ?> <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('home page', 'wordlandbaseline'); ?></a>?</p>
	</div>
</div>

<?php get_footer(); ?>
