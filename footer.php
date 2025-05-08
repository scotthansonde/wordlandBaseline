<?php

/**
 * The template for displaying the footer
 */
?>
<div class="divFooter">
	<nav class="divFooterSocialMenu">
		<ul>
			<li><a href="<?php echo esc_url(get_feed_link()); ?>" target="_blank" rel="noopener"><?php echo baseline_social_link_services('feed', 'icon'); ?></a></li>
		</ul>
	</nav>
	<p>Last update: <?php echo baseline_get_last_modified_date(); ?>.</p>
</div>
</div><!-- .divPageBody -->

<?php wp_footer(); ?>
</body>

</html>
