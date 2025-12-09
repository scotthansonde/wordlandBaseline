<?php

/**
 * The archive template file
 *
 * Displays archive of blog posts with pagination
 */

get_header(); ?>

<div class="divStory">
	<div class="divStoryTitle">
		<?php
		if (is_day()) {
			// For day archives, use custom format: "Monday, December 9, 2024"
			echo get_the_date('l, F j, Y');
		} else {
			// For other archives, use default archive title
			the_archive_title();
		}
		?>
	</div>
</div>

<?php if (have_posts()) : ?>
	<div id="idStories">
		<?php
		while (have_posts()) : the_post();
			get_template_part('template-parts/content', get_post_type());
		endwhile;
		wp_reset_postdata();
		?>
	</div>

	<div class="divPagination">
		<?php
		the_posts_pagination(array(
			'mid_size' => 2,
			'prev_text' => __('Previous Page', 'wordlandbaseline'),
			'next_text' => __('Next Page', 'wordlandbaseline'),
			'screen_reader_text' => __('Posts navigation', 'wordlandbaseline')
		));
		?>
	</div>

<?php else : ?>
	<div class="divStory">
		<div class="divStoryTitle">
			No posts found
		</div>
		<div class="divStoryBody">
			<p>Sorry, no posts were found.</p>
		</div>
	</div>
<?php endif; ?>

<?php get_footer(); ?>
