<?php

/**
 * The linkblog category template file
 *
 * Displays archive of linkblog posts with pagination
 */

get_header();

// Override the main query to show all posts
$temp = $wp_query;
$wp_query = null;
$wp_query = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => -1,
	'category_name' => 'linkblog'
));

if (have_posts()) :
	echo '<div class="divLinkblogContainer">';
	$current_day = '';
	while (have_posts()) : the_post();
		$post_day = get_the_date('l, F j, Y'); // Example: Saturday, July 5, 2025
		if ($post_day !== $current_day) {
			if ($current_day !== '') {
				echo '</div>'; // Close previous day's div
			}
			echo '<div class="divLinkblogDay">';
			echo '<div class="divLinkblogDayTitle">' . esc_html($post_day) . '</div>';
			$current_day = $post_day;
		}

		get_template_part('template-parts/linkblog-content', get_post_type());
	endwhile;
	echo '</div>'; // Close final day's div
?>
	</div>
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
