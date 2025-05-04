<?php
/**
 * Template part for displaying posts
 */
?>
<div class="divStory">
    <div class="divStoryTitle">
        <?php if (is_home() || is_archive()) : ?>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <?php else : ?>
            <?php the_title(); ?>
        <?php endif; ?>
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
