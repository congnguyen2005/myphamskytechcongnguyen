<?php
/**
 * The sidebar template
 * 
 * @package MyPhamTheme
 */
?>

<div class="sidebar-widgets">
    <!-- Search Widget -->
    <div class="sidebar-widget widget_search">
        <h3 class="widget-title">Tìm kiếm</h3>
        <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
            <input type="search" name="s" placeholder="Tìm kiếm..." value="<?php echo get_search_query(); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    
    <!-- Categories Widget -->
    <div class="sidebar-widget widget_categories">
        <h3 class="widget-title">Danh mục</h3>
        <ul>
            <?php
            wp_list_categories(array(
                'title_li' => '',
                'show_count' => true,
                'orderby' => 'count',
                'order' => 'DESC'
            ));
            ?>
        </ul>
    </div>
    
    <!-- Recent Posts Widget -->
    <div class="sidebar-widget widget_recent_posts">
        <h3 class="widget-title">Bài viết mới</h3>
        <ul>
            <?php
            $recent_posts = new WP_Query(array(
                'posts_per_page' => 5,
                'post_type' => 'post'
            ));
            
            while ($recent_posts->have_posts()) : $recent_posts->the_post();
            ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <span class="post-date"><?php echo get_the_date('d/m/Y'); ?></span>
                </li>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
    </div>
    
    <!-- Product Categories Widget -->
    <div class="sidebar-widget widget_product_categories">
        <h3 class="widget-title">Danh mục sản phẩm</h3>
        <ul>
            <?php
            $product_cats = get_terms(array(
                'taxonomy' => 'product_category',
                'hide_empty' => false
            ));
            
            if (!empty($product_cats) && !is_wp_error($product_cats)) :
                foreach ($product_cats as $cat) :
            ?>
                    <li>
                        <a href="<?php echo get_term_link($cat); ?>">
                            <?php echo $cat->name; ?> (<?php echo $cat->count; ?>)
                        </a>
                    </li>
            <?php
                endforeach;
            endif;
            ?>
        </ul>
    </div>
    
    <!-- Tags Widget -->
    <div class="sidebar-widget widget_tags">
        <h3 class="widget-title">Thẻ phổ biến</h3>
        <div class="tagcloud">
            <?php
            wp_tag_cloud(array(
                'smallest' => 12,
                'largest' => 12,
                'unit' => 'px',
                'number' => 20
            ));
            ?>
        </div>
    </div>
    
    <!-- Newsletter Widget -->
    <div class="sidebar-widget widget_newsletter">
        <h3 class="widget-title">Đăng ký nhận tin</h3>
        <p>Nhận thông tin khuyến mãi mới nhất</p>
        <form class="newsletter-form" method="POST">
            <input type="email" name="email" placeholder="Email của bạn" required>
            <button type="submit" class="btn btn-primary btn-sm">Đăng ký</button>
        </form>
    </div>
</div>