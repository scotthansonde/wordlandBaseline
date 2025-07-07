<?php

/**
 * The home template file
 *
 * Displays blog posts index
 */

get_header(); ?>

<?php
// If pagination is disabled, show current and previous month posts
if (get_theme_mod('baseline_disable_pagination', true)) {
	$current_time = current_time('mysql');
	$last_month = date('Y-m-01 00:00:00', strtotime('-1 month', strtotime($current_time)));

	// First try to get posts from current and previous month
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => -1,
		'date_query' => array(
			array(
				'after' => $last_month,
				'inclusive' => true,
			),
		),
	);
	$query = new WP_Query($args);
	
	// If we have fewer than 5 posts, get the 5 most recent posts regardless of date
	if ($query->post_count < 5) {
		wp_reset_postdata();
		$args = array(
			'post_type' => 'post',
			'posts_per_page' => 5,
		);
		$query = new WP_Query($args);
	}
} else {
	$query = $GLOBALS['wp_query'];
}

if ($query->have_posts()) : ?>
	<div id="idStories">
		<?php
		while ($query->have_posts()) : $query->the_post();
			get_template_part('template-parts/content', get_post_type());
		endwhile;
		wp_reset_postdata();
		?>
	</div>

	<?php if (!get_theme_mod('baseline_disable_pagination', true)) : ?>
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
	<?php endif; ?>

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
