<?php

/**
 * The template for displaying the header
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<?php
	$has_blogroll = shortcode_exists('feedland-blogroll');
	$is_index = is_home() || (is_front_page() && !is_page());
	$add_blogroll_class = $has_blogroll && $is_index;
	?>
	<div class="divPageBody<?php echo $add_blogroll_class ? ' has-sidebar' : ''; ?>">
		<div class="divPageTop">
			<div class="divSiteTitle">
				<?php if (!is_front_page() && !is_home()) : ?>
					<a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
				<?php else : ?>
					<?php bloginfo('name'); ?>
				<?php endif; ?>
			</div>
			<div class="divSiteDescription"><?php bloginfo('description'); ?></div>
		</div>
