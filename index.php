<?php

/**
 * The home template file
 *
 * Displays blog posts index
 * 
 * Query modification and post sorting (titleless first) handled via hooks in functions.php:
 * - baseline_modify_home_query (pre_get_posts)
 * - baseline_sort_posts_titleless_first (the_posts)
 */

get_header(); ?>

<?php $has_blogroll = shortcode_exists('feedland-blogroll'); ?>
<div class="divContentWrapper <?php echo $has_blogroll ? 'has-sidebar' : 'no-sidebar'; ?>">

	<?php if (have_posts()) : ?>
		<div id="idStories">
			<?php
			$current_day = '';
			$show_headlines = get_theme_mod('baseline_show_date_headlines', true);

			while (have_posts()) : the_post();
				$post_day = get_the_date('l, F j, Y');

				// New day? Close previous group and start new one
				if ($show_headlines && $post_day !== $current_day) {
					if ($current_day !== '') {
						echo '</div>'; // Close previous day's div
					}
					echo '<div class="divDayGroup">';
					echo '<div class="divDayTitle"><a href="' . esc_url(get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d'))) . '">' . esc_html($post_day) . '</a></div>';
					$current_day = $post_day;
				}

				get_template_part('template-parts/content', get_post_type());
			endwhile;

			if ($show_headlines && $current_day !== '') {
				echo '</div>'; // Close last day's div
			}
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

	<?php if (shortcode_exists('feedland-blogroll')) : ?>
		<div class="divSidebar">
			<?php echo do_shortcode('[feedland-blogroll]'); ?>
		</div>
	<?php endif; ?>

</div><!-- .divContentWrapper -->

<?php get_footer(); ?>
