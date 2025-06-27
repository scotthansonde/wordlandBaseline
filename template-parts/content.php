<?php

/**
 * Template part for displaying posts
 */
?>
<div class="divStory">
	<?php $has_title = get_the_title(); ?>
	<div class="divStoryTitle">
		<?php if ($has_title) : ?>
			<?php if (is_home() || is_archive()) : ?>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php else : ?>
				<?php the_title(); ?>
			<?php endif; ?>
		<?php else : ?>
			<?php if (is_home() || is_archive()) : ?>
				<a href="<?php the_permalink(); ?>"><?php echo get_the_date(); ?> by <span class="author-link"><?php baseline_author_website_link(); ?></span></a>
			<?php else : ?>
				<?php echo get_the_date(); ?> by <?php baseline_author_website_link(); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php if ($has_title) : ?>
		<div class="divLineUnderStoryTitle">
			<?php echo get_the_date(); ?> by <?php baseline_author_website_link(); ?>
		</div>
	<?php endif; ?>

	<div class="divStoryBody">
		<?php if (has_post_thumbnail()) : ?>
			<div class="divFeaturedImage">
				<?php if (is_home() || is_archive()) : ?>
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail('large', array('class' => 'featuredImage')); ?>
					</a>
				<?php else : ?>
					<?php the_post_thumbnail('large', array('class' => 'featuredImage')); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php the_content(); ?>
		<!-- Comments disabled by default, can be enabled in Customizer -->
		<?php if (is_single() && get_theme_mod('baseline_show_comments', false)) : ?>
			<?php if (comments_open() || get_comments_number()) : ?>
				<div class="divComments">
					<?php comments_template(); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php if (is_single()) : ?>
		<?php $categories = get_filtered_categories(); ?>
		<?php if ($categories) : ?>
			<div class="divCategories">
				Categories: <?php echo join(', ', array_map(function ($cat) {
								return '<a href="' . esc_url(get_category_feed_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a>';
							}, $categories)); ?>.
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>
