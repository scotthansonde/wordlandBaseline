<?php

/**
 * The home template file
 *
 * Displays blog posts index
 */

get_header(); ?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<div class="divStory">
			<div class="divStoryTitle">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
			<div class="divLineUnderStoryTitle">
				<?php echo get_the_date(); ?> by <?php echo get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name'); ?>
			</div>
			<div class="divStoryBody">
				<?php the_content(); ?>
			</div>
			<?php $categories = get_filtered_categories(); ?>
			<?php if ($categories) : ?>
				<div class="divCategories">
					Categories: <?php echo join(', ', array_map(function ($cat) {
									return $cat->name;
								}, $categories)); ?>.
				</div>
			<?php endif; ?>
		</div>
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
