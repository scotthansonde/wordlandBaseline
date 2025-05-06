<?php

/**
 * The home template file
 *
 * Displays blog posts index
 */

get_header(); ?>

<?php if (have_posts()) : ?>
	<div id="idStories">
		<?php
		$query = new WP_Query([
			'post_type' => 'post',
			'posts_per_page' => 6,
		]);
		if ($query->have_posts()) :
			while ($query->have_posts()) : $query->the_post();
				get_template_part('template-parts/content', get_post_type());
			endwhile;
			wp_reset_postdata();
		endif;
		?>
	</div>

	<div id="idScrollTrigger" class="divPagination" style="text-align: center;"></div>

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
