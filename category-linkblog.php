<?php

/**
 * The linkblog category template file
 *
 * Displays archive of linkblog posts with pagination
 */

get_header(); ?>

<!-- <div class="divStory">
	<div class="divStoryTitle">
		Linkblog
	</div>
</div> -->

<?php
// Override the main query to show all posts
$temp = $wp_query;
$wp_query = null;
$wp_query = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => -1,
	'category_name' => 'linkblog'
));

if (have_posts()) : ?>
	<div id="idStories">
		<?php
		while (have_posts()) : the_post();
			get_template_part('template-parts/linkblog-content', get_post_type()); ?>

		<?php
		endwhile;
		?>
	</div>

	<!-- Pagination removed to show all posts -->

	<?php
	// Restore original query
	wp_reset_postdata();
	$wp_query = $temp;
	?>

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
