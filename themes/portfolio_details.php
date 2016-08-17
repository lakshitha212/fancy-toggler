<?php

/* Template Name: Easy-Portfolio Details Page */

get_header();
function ep_add_meta_tags()
{
    global $post;
    $metatitle = get_post_meta($post->ID, '_ep_metatitle', true);
    $metadesc = get_post_meta($post->ID, '_ep_metadesc', true); ?>
    <meta name="title" content="<?php echo $metatitle; ?>"/>
    <meta name="description" content="<?php echo $metadesc; ?>"/>
<?php } ?>
<div id="primary" class="site-content">
    <div id="content" role="main">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="ep_title"><?php the_title(); ?></div>

                <div class="ep_entry-content">
                    <?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve')); ?>
                    <?php wp_link_pages(array('before' => '<div class="page-links">' . __('Pages:', 'twentytwelve'), 'after' => '</div>')); ?>
                </div>
                <div class="ep_content_details">
                    <?php
                    $portfoliourl = get_post_meta($post->ID, '_ep_portfoliourl', true);
                    $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "Full");
                    $terms = wp_get_object_terms($post->ID, 'fancy_portfolio_category');
                    ?>
                    <b>Portfolio Categories:</b>
                    <p><?php foreach ($terms as $val) {
                            echo $val->name . '&nbsp;';
                        } ?></p><br/>
                    <?php if ($portfoliourl) { ?><b>Portfolio URL:</b>
                        <p>
                        <a target="_blank" href="<?php echo $portfoliourl; ?>">Go To Project</a></p><?php } ?>
                    <div class="ep_featured-image" id="ep_detail_img">
                        <?php if (get_the_post_thumbnail($post->ID, 'thumbnail')) { ?>
                            <a href="<?php echo $imgsrc[0]; ?>" rel="prettyPhoto[portfolio]">
                                <?php echo get_the_post_thumbnail($post->ID, 'thumbnail'); ?>
                            </a>
                        <?php } else { ?>
                            <img width="150" height="150" class="attachment-thumbnail wp-post-image"
                                 src="<?php echo TOGGLER_URL; ?>/images/no_images.jpg"/>
                        <?php } ?>
                    </div>
                </div>
                <nav class="nav-single">
                    <h3 class="assistive-text"><?php _e('Post navigation', 'twentytwelve'); ?></h3>
                    <span
                        class="nav-previous"><?php previous_post_link('%link', '<span class="meta-nav">' . _x('&larr;', 'Previous post link', 'twentytwelve') . '</span> %title'); ?></span>
                    <span
                        class="nav-next"><?php next_post_link('%link', '%title <span class="meta-nav">' . _x('&rarr;', 'Next post link', 'twentytwelve') . '</span>'); ?></span>
                </nav><!-- .nav-single -->
            </article>
            <?php comments_template('', true); ?>
        <?php endwhile; ?>
    </div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
<style>
    .pp_hoverContainer {
        display: none !important;
    }
</style>