<?php
/*
Plugin Name: Fancy Toggler
Plugin URI: #
Description: Fancy Toggler plugin is a wordpress plugin designed to create interactive portfolio functionality with category filteration in to your wordpress website.
Version: 1.0
Author: Lakshitha212
Author URI: https://github.com/lakshitha212
*/
define('TOGGLER_DIR', dirname(__FILE__));
define('TOGGLER_THEMES_DIR', TOGGLER_DIR . "/themes");
define('TOGGLER_URL', WP_PLUGIN_URL . "/" . basename(TOGGLER_DIR));
define('FA_TOGGLER_VERSION', '1.0');


//All ShortCode are show
add_shortcode('fancy_toggler', 'fa_toggler');
add_shortcode('fa_latest_portfolio', 'fa_portfolio_latest_item');
add_shortcode('fa_portfolio_category', 'fa_portfolio_cat');
add_shortcode('fa_latest_portfolio_list', 'fa_latest_portfolio_list');
//Method And Action Are Call
add_filter('manage_edit-portfolio_columns', 'fa_add_new_portfolio_columns');
add_action('manage_portfolio_posts_custom_column', 'fa_manage_portfolio_columns', 5, 2);
add_action('init', 'fa_toggler_register');
add_action('add_meta_boxes', 'fa_add_toggler_metaboxes');
add_action('template_redirect', 'fa_template_post_detailspage');
add_action('wp_enqueue_scripts', 'fancy_toggler_scripts');

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');

//All js and Css Are call
function fancy_toggler_scripts()
{
    wp_enqueue_script('isotope', TOGGLER_URL . '/isotope-3.0.1/isotope.pkgd.min.js', array('jquery'), FA_TOGGLER_VERSION);
    wp_enqueue_script('fancyBox_script', TOGGLER_URL . '/fancyapps-fancyBox/source/jquery.fancybox.pack.js', array('jquery'), FA_TOGGLER_VERSION);
    wp_enqueue_script('main_js', TOGGLER_URL . '/custom/main.js', array('jquery'), FA_TOGGLER_VERSION);
    wp_enqueue_style('fancyBox_script_style', TOGGLER_URL . "/fancyapps-fancyBox/source/jquery.fancybox.css");
    wp_enqueue_style('main_style', TOGGLER_URL . '/custom/styles.css');
}

//Register Post Type
function fa_toggler_register()
{
    $labels = array(
        'name' => __('Fancy Toggler'),
        'singular_name' => __('Fancy Toggler'),
        'add_new' => __('Add Fancy-Portfolio Item'),
        'add_new_item' => __('Add New Fancy-Portfolio Item'),
        'edit_item' => __('Edit Fancy-Portfolio Item'),
        'new_item' => __('New Fancy-Portfolio Item'),
        'view_item' => __('View Fancy-Portfolio Item'),
        'search_items' => __('Search Fancy-Portfolio Item'),
        'not_found' => __('No Fancy-Portfolio Items found'),
        'not_found_in_trash' => __('No Fancy-Portfolio Items found in Trash'),
        'parent_item_colon' => '',
        'menu_name' => __('Fancy Toggler')
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => true,
        'rewrite' => array('slug' => 'fancy_portfolio'),
        'supports' => array(
            'title',
            'thumbnail',
            'editor',
            // 'excerpt',
            //'author',
            //'trackbacks',
            //'custom-fields',
            //'revisions',
            'page-attributes'
        ),
        'menu_position' => 23,
        'register_meta_box_cb' => 'fa_add_toggler_metaboxes',
        'menu_icon' => 'dashicons-portfolio',
        'taxonomies' => array('fancy_portfolio_category')
    );
    register_post_type('fancy_portfolio', $args);
    fa_toggler_register_taxonomies();
}

//Register Taxonomies
function fa_toggler_register_taxonomies()
{
    register_taxonomy('fancy_portfolio_category', 'fancy_portfolio', array('hierarchical' => true, 'label' => 'Fancy-Portfolio Category', 'query_var' => true, 'rewrite' => array('slug' => 'fancy-portfolio-type')));
    if (count(get_terms('fancy_portfolio_category', 'hide_empty=0')) == 0) {
        register_taxonomy('type', 'fancy_portfolio', array('hierarchical' => true, 'label' => 'Item Type'));
        $_categories = get_categories('taxonomy=type&title_li=');
        foreach ($_categories as $_cat) {
            if (!term_exists($_cat->name, 'fancy_portfolio_category'))
                wp_insert_term($_cat->name, 'fancy_portfolio_category');
        }
        $portfolio = new WP_Query(array('post_type' => 'fancy_portfolio', 'posts_per_page' => '-1'));
        while ($portfolio->have_posts()) : $portfolio->the_post();
            $post_id = get_the_ID();
            $_terms = wp_get_post_terms($post_id, 'type');
            $terms = array();
            foreach ($_terms as $_term) {
                $terms[] = $_term->term_id;
            }
            wp_set_post_terms($post_id, $terms, 'fancy_portfolio_category');
        endwhile;
        wp_reset_query();
        register_taxonomy('type', array());
    }
}

//Admin Dashobord Listing Portfolio Columns Title
function fa_add_new_portfolio_columns()
{
    $columns['cb'] = '<input type="checkbox" />';
    $columns['title'] = __('Title', 'fancy_toggler');
    $columns['thumbnail'] = __('Thumbnail', 'fancy_toggler');
    $columns['author'] = __('Author', 'fancy_toggler');
    $columns['fancy_portfolio_category'] = __('Fancy-Portfolio Categories', 'fancy_toggler');
    $columns['date'] = __('Date', 'fancy_toggler');
    return $columns;
}

//Admin Dashobord Listing Portfolio Columns Manage
function fa_manage_portfolio_columns($columns)
{
    global $post;
    switch ($columns) {
        case 'thumbnail':
            if (get_the_post_thumbnail($post->ID, 'thumbnail')) {
                echo get_the_post_thumbnail($post->ID, 'thumbnail');
            } else {
                echo '<img width="150" height="150" src="' . TOGGLER_URL . '/images/no_images.jpg" class="attachment-thumbnail wp-post-image" alt="Penguins">';
            }
            break;
        case 'fancy_portfolio_category':
            $terms = wp_get_post_terms($post->ID, 'fancy_portfolio_category');
            foreach ($terms as $term) {
                echo $term->name . '&nbsp;&nbsp; ';
            }
            break;
    }
}

//Get All Portfolio Category
function fa_portfolio_list_categories()
{
    $_fancy_categories = get_categories('taxonomy=fancy_portfolio_category');
//    print_r($_fancy_categories);
    foreach ($_fancy_categories as $_fancy_cat) { ?>

        <li>
            <a href="#" title="<?php echo $_fancy_cat->name; ?>" data-filter=".<?php echo $_fancy_cat->slug; ?>"
               rel="<?php echo $_fancy_cat->slug; ?>"><?php echo $_fancy_cat->name; ?></a>
        </li>
        <!---->
        <!--        <li class="--><?php //echo $_fancy_cat->slug; ?><!--">-->
        <!--            <a title="View all posts filed under --><?php //echo $_fancy_cat->name; ?><!--"-->
        <!--               href="--><?php //echo get_term_link($_fancy_cat->slug, 'fancy_portfolio_category'); ?><!--"-->
        <!--               rel="--><?php //echo $_fancy_cat->slug; ?><!--">--><?php //echo $_fancy_cat->name; ?><!--</a>-->
        <!--        </li>-->
    <?php }
}

//Get All Portfolio item Slug Front Side
function fa_portfolio_get_item_slug($post_id = null)
{
    if ($post_id === null)
        return;
    $_terms = wp_get_post_terms($post_id, 'fancy_portfolio_category');
    foreach ($_terms as $_term) {
        echo $_term->slug . ' ';
    }
}

//Get All Portfolio is show
function fa_toggler()
{
    global $post;
    require(TOGGLER_THEMES_DIR . "/default_template.php");
}

//Get All Portfolio is show
function fa_portfolio_latest_item($portfolio_cal)
{
    global $post;
    require(TOGGLER_THEMES_DIR . "/default_template.php");
}

//Get All Portfolio is show
function fa_portfolio_cat($pro_category)
{
    require(TOGGLER_THEMES_DIR . "/category_template.php");
}

//Portfolio Details Page
function fa_template_post_detailspage()
{
    global $post, $posts;
    if ('fancy_portfolio' == get_post_type()) {
        add_action('wp_head', 'fa_add_meta_tags');
        require(TOGGLER_THEMES_DIR . "/portfolio_details.php");
        exit();
    }
}

//Get All Portfolio showing in List View
function fa_latest_portfolio_list($portfolio_list)
{
    $limit = $portfolio_list['limit'];
    $order_by = $portfolio_list['order'];
    $portfolio = new WP_Query(array('post_type' => 'fancy_portfolio', 'posts_per_page' => $limit, 'order' => $order_by)); ?>
    <ul class="ep_portfolio_list">
        <?php
        global $post;
        while ($portfolio->have_posts()) : $portfolio->the_post();
            $portfoliourl = get_post_meta($post->ID, '_ep_portfoliourl', true);
            $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "Full"); ?>
            <li>
                <div class="ep_portfolio_list_img">
                    <?php if (get_the_post_thumbnail($post->ID, 'thumbnail')) { ?>
                        <a href="<?php echo $imgsrc[0]; ?>" rel="prettyPhoto[portfolio]">
                            <?php echo get_the_post_thumbnail($post->ID, 'thumbnail'); ?>
                        </a>
                    <?php } else { ?>
                        <img width="150" height="150" class="attachment-thumbnail wp-post-image"
                             src="<?php echo TOGGLER_URL; ?>/images/no_images.jpg"/>
                    <?php } ?>
                </div>
                <div class="ep_title">
                    <a href="<?php echo get_permalink($post->ID); ?>"><?php the_title(); ?></a>
                </div>
                <div class="ep_portfolio_desc">
                    <?php echo substr($post->post_content, 0, 200) . '...'; ?>
                </div>
                <div class="ep_readmore">
                    <a href="<?php echo get_permalink($post->ID); ?>">Read More →</a>&nbsp;&nbsp;
                    <a target="_blank" href="<?php echo $portfoliourl; ?>">Go To Project</a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
<?php } ?>
