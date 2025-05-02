<?php

/**
 * The single post template file
 *
 * Displays individual post content
 */

get_header(); ?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<div class="divStory">
			<div class="divStoryTitle">
				<?php the_title(); ?>
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
				Categories: <?php echo join(', ', array_map(function($cat) { return $cat->name; }, $categories)); ?>.
			</div>
			<?php endif; ?>
		</div>

		<!-- Comments disabled -->
		<!-- <?php if (comments_open() || get_comments_number()) : ?>
			<div class="divComments">
				<?php comments_template(); ?>
			</div>
		<?php endif; ?> -->

		<!-- Navigation disabled -->
		<!-- <div class="divNavigation">
			<?php
			$prev_post = get_previous_post();
			$next_post = get_next_post();
			if ($prev_post || $next_post) :
			?>
				<div class="divPrevNext">
					<?php if ($prev_post) : ?>
						<div class="divPrev">
							Previous: <a href="<?php echo get_permalink($prev_post); ?>"><?php echo get_the_title($prev_post); ?></a>
						</div>
					<?php endif; ?>
					<?php if ($next_post) : ?>
						<div class="divNext">
							Next: <a href="<?php echo get_permalink($next_post); ?>"><?php echo get_the_title($next_post); ?></a>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div> -->
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
