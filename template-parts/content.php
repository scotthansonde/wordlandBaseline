<?php

/**
 * Template part for displaying posts
 */
?>
<div class="divStory">
	<?php $has_title = get_the_title(); ?>
	<?php if ($has_title) : ?>
		<div class="divStoryTitle">
			<?php if (is_home() || is_archive()) : ?>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php else : ?>
				<?php the_title(); ?>
			<?php endif; ?>
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

		<?php 
		// Use our reusable function to append the link
		$cleanup = wordland_append_external_link();
		
		// Display the content with our appended link
		the_content();
		
		// Remove our filter if it was added
		if ($cleanup) $cleanup();
		?>
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
