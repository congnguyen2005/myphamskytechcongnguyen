<div class="sidebar-widget">
    <h3 class="widget-title">Danh mục</h3>
    <ul class="category-list">
        <?php
        $cats = get_terms(array(
            'taxonomy' => 'news_category', 
            'hide_empty' => true,
        ));
        if (!empty($cats) && !is_wp_error($cats)) :
            foreach ($cats as $c) :
        ?>
            <li>
                <a href="<?php echo esc_url(get_term_link($c)); ?>">
                    <?php echo esc_html($c->name); ?>
                    <span class="count">(<?php echo $c->count; ?>)</span>
                </a>
            </li>
        <?php endforeach; endif; ?>
    </ul>
</div>

<div class="sidebar-widget">
    <h3 class="widget-title">Bài viết mới</h3>
    <ul class="recent-list">
        <?php
        $recent = new WP_Query(array(
            'post_type' => 'news', 
            'posts_per_page' => 5,
            'post_status' => 'publish',
        ));
        while ($recent->have_posts()) : $recent->the_post(); ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <span class="date"><?php echo get_the_date('d/m/Y'); ?></span>
            </li>
        <?php endwhile; wp_reset_postdata(); ?>
    </ul>
</div>

<div class="sidebar-widget">
    <h3 class="widget-title">Bài viết xem nhiều</h3>
    <ul class="popular-list">
        <?php
        $popular = new WP_Query(array(
            'post_type' => 'news', 
            'posts_per_page' => 5,
            'meta_key' => '_news_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ));
        while ($popular->have_posts()) : $popular->the_post(); 
            $views = get_post_meta(get_the_ID(), '_news_views', true) ?: 0;
        ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <span class="views"><i class="far fa-eye"></i> <?php echo number_format($views); ?></span>
            </li>
        <?php endwhile; wp_reset_postdata(); ?>
    </ul>
</div>

<style>
.sidebar-widget {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.widget-title {
    font-size: 18px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
    display: inline-block;
}

.category-list,
.recent-list,
.popular-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-list li,
.recent-list li,
.popular-list li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.category-list li:last-child,
.recent-list li:last-child,
.popular-list li:last-child {
    border-bottom: none;
}

.category-list a,
.recent-list a,
.popular-list a {
    display: block;
    color: #666;
    text-decoration: none;
    transition: color 0.3s;
    font-size: 14px;
}

.category-list a:hover,
.recent-list a:hover,
.popular-list a:hover {
    color: #667eea;
}

.category-list .count,
.recent-list .date,
.popular-list .views {
    display: block;
    font-size: 11px;
    color: #999;
    margin-top: 4px;
}

.popular-list .views i {
    margin-right: 3px;
}
</style>