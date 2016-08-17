<?php
if (isset($portfolio_cal)) {
    $limit = $portfolio_cal['limit'];
    $order_by = $portfolio_cal['order'];
    $portfolio = new WP_Query(array('post_type' => 'fancy_portfolio', 'posts_per_page' => $limit, 'order' => $order_by, 'post_status' => 'publish'));
} else {
    $portfolio = new WP_Query(array('post_type' => 'fancy_portfolio', 'posts_per_page' => -1, 'post_status' => 'publish'));
    ?>
    <section class="filter-section" id="ourwork">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <h1>Our Work</h1>
                </div>
                <div class="col-sm-7 col-xs-7">
                    <div class="filter-container isotopeFilters">
                        <ul class="list-inline filter">
                            <li class="active"><a href="#" data-filter="*">All </a></li>
                            <?php fa_portfolio_list_categories(); ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
        </div>
    </section>
<?php } ?>
<section class="portfolio-section port-col no-padding">
    <div class="container">
        <div class="row">
            <div class="isotopeContainer">
                <?php
                while ($portfolio->have_posts()) : $portfolio->the_post();
                    $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "Full");
                    ?>
                    <div class="col-sm-3 isotopeSelector <?php fa_portfolio_get_item_slug(get_the_ID()); ?>">
                        <article class="">
                            <figure>
                                <?php echo get_the_post_thumbnail($post->ID, 'medium'); ?>
                                <div class="overlay-background">
                                    <div class="inner"><?php the_title(); ?></div>
                                </div>
                                <div class="overlay">
                                    <div class="inner-overlay">
                                        <div class="inner-overlay-content with-icons">
                                            <a title="<?php the_title(); ?>" class="fancybox-pop" rel="portfolio-1"
                                               href="<?php echo $imgsrc[0]; ?>"><i
                                                    class="glyphicon glyphicon-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </figure>
                        </article>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
