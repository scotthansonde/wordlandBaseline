<?php

/**
 * The home template file
 *
 * Displays blog posts index
 */

get_header(); ?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<?php get_template_part('template-parts/content'); ?>
	<?php endwhile; ?>
	<div class="divPagination">
		<?php the_posts_pagination(array(
			'mid_size' => 2,
			'prev_text' => __('Previous', 'wordlandBaselineMockup'),
			'next_text' => __('Next', 'wordlandBaselineMockup'),
		)); ?>
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
