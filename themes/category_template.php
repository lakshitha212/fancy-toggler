<?php
global $post;
$terms = get_terms('fancy_portfolio_category');
if (isset($pro_category['slug'])) {
    $get_slug = $pro_category['slug'];
    $orderby = $pro_category['order'];
    $category_array = explode(",", $get_slug); ?>
    <ul class="ep_filter ep_group">
        <?php if (isset($pro_category['slug'])) { ?>
            <li class="current all">
                <a href="#" rel="all">All</a></li>
        <?php } ?>
        <?php foreach ($terms as $val) {
            if (in_array($val->slug, $category_array)) {
                ?>
                <li class="<?php echo $val->slug; ?>">
                    <a title="View all posts filed under <?php echo $val->slug; ?>"
                       href="<?php echo get_term_link($val->slug, 'fancy_portfolio_category'); ?>"
                       rel="<?php echo $val->slug; ?>"><?php echo $val->name; ?></a>
                </li>
            <?php }
        } ?>
    </ul>
    <ul class="ep_portfolio ep_group">
        <?php
        $i = 1;
        $sql = new WP_Query(array('post_type' => 'fancy_portfolio', 'posts_per_page' => -1, 'post_status' => 'publish', 'fancy_portfolio_category' => $pro_category['slug'], 'order' => $orderby));
        if ($sql->have_posts()) : while ($sql->have_posts()) : $sql->the_post();
            $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "Full");
            $portfoliourl = get_post_meta($post->ID, '_ep_portfoliourl', true); ?>
            <li class="ep_item" data-id="id-<?php echo $i; ?>"
                data-type="<?php ep_portfolio_get_item_slug(get_the_ID()); ?>">
                <?php if (get_the_post_thumbnail($post->ID, 'medium')) { ?>
                    <?php echo get_the_post_thumbnail($post->ID, 'medium', array('class' => 'portfolio_img')); ?>
                <?php } else { ?>
                    <img width="150" height="150" class="attachment-thumbnail wp-post-image"
                         src="<?php echo TOGGLER_URL; ?>/images/no_images.jpg"/>
                <?php } ?>
                <div class="ep_portfoliourl">
                    <div class="ep_portfoliourl_wrap">
                        <div class="ep_portfoliourl_cont">
                            <h4 class="item_title"><?php the_title(); ?></h4>
                            <div class="item_more">
                                <span><a class="zoom" target="_blank" href="<?php echo $imgsrc[0]; ?>"
                                         rel="prettyPhoto[portfolio]"></a></span>
                                <span><a class="link_post" href="<?php echo get_permalink($post->ID); ?>"></a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <?php $i++; ?>
        <?php endwhile; endif; ?>
    </ul>
<?php } else { ?>
    <h2>Portfolio/Shortcode Not Found</h2>
<?php } ?>