<?php

/**
 * The linkblog category template file
 *
 * Displays archive of linkblog posts with pagination
 */

get_header(); ?>

<div class="divStory">
	<div class="divStoryTitle">
		Linkblog
	</div>
</div>

<?php if (have_posts()) : ?>
	<div id="idStories">
		<?php
		while (have_posts()) : the_post();
			get_template_part('template-parts/linkblog-content', get_post_type()); ?>

		<?php
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
