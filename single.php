<?php

/**
 * The single post template file
 *
 * Displays individual post content
 */

get_header(); ?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<?php get_template_part('template-parts/content'); ?>

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
