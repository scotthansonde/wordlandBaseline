<?php

/**
 * The template for displaying the footer
 */
?>
<div class="divFooter">
	<nav class="divFooterSocialMenu">
		<ul>
			<li><a href="<?php echo esc_url(get_feed_link()); ?>" target="_blank" rel="noopener"><?php echo baseline_social_link_services('feed', 'icon'); ?></a></li>
			<li><?php echo baseline_social_link_services('bluesky', 'icon'); ?></li>
			<li><?php echo baseline_social_link_services('mastodon', 'icon'); ?></li>
			<li><?php echo baseline_social_link_services('threads', 'icon'); ?></li>
			<li><?php echo baseline_social_link_services('github', 'icon'); ?></li>
		</ul>
	</nav>
	<p>Last update: <?php echo baseline_get_last_modified_date(); ?>.</p>
	<p>Written in <a href="https://wordland.social/" rel="nofollow">WordLand</a>, running on the <a href="https://wordpress.org/" rel="nofollow">WordPress</a>
		platform using the <a href="https://github.com/scotthansonde/wordlandBaseline" rel="nofollow">Baseline</a> theme.</p>
</div>
</div><!-- .divPageBody -->

<?php wp_footer(); ?>
</body>

</html>
