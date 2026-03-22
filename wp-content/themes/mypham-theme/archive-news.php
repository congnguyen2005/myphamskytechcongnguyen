<?php
/**
 * Archive News Template - Compact Layout
 * @package MyPhamTheme
 */

get_header();

// Lấy thông tin category nếu có
$current_category = '';
$category_image = ''; // Khởi tạo biến

if (is_tax('news_category')) {
    $current_category = get_queried_object();
    $page_title = $current_category->name;
    $page_description = $current_category->description;
    $category_image = get_term_meta($current_category->term_id, 'category_image', true);
} else {
    $page_title = 'Tin tức & Bài viết';
    $page_description = 'Cập nhật những xu hướng làm đẹp mới nhất';
}

// Enqueue styles và scripts
wp_enqueue_style('news-page-style', get_template_directory_uri() . '/assets/css/archive-news.css', array(), '1.0.0');
wp_enqueue_script('news-page-script', get_template_directory_uri() . '/assets/js/archive-news.js', array('jquery'), '1.0.0', true);
wp_localize_script('news-page-script', 'news_ajax', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('load_more_news')
));
?>

<!-- Header Section - Compact -->
<div class="news-header">
    <div class="container">
        <div class="news-header-content">
            <div class="header-left">
                <h1 class="page-title"><?php echo esc_html($page_title); ?></h1>
                <?php if ($page_description) : ?>
                    <p class="page-description"><?php echo esc_html($page_description); ?></p>
                <?php endif; ?>
            </div>
            <div class="header-right">
                <form role="search" method="get" class="compact-search" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" name="s" placeholder="Tìm kiếm bài viết..." value="<?php echo get_search_query(); ?>">
                    <input type="hidden" name="post_type" value="news">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="news-page">
    <div class="container">
        <div class="news-wrapper">
            <div class="news-main">
                <?php
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'news',
                    'posts_per_page' => 6,
                    'paged' => $paged,
                );
                
                if ($current_category) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'news_category',
                            'field'    => 'term_id',
                            'terms'    => $current_category->term_id,
                        ),
                    );
                }
                
                $news_query = new WP_Query($args);
                
                // Category Filter - Compact
                $all_cats = get_terms(array('taxonomy' => 'news_category', 'hide_empty' => true));
                if (!empty($all_cats) && !is_wp_error($all_cats)) :
                ?>
                <div class="category-filter">
                    <a href="<?php echo get_post_type_archive_link('news'); ?>" class="filter-link <?php echo !$current_category ? 'active' : ''; ?>">
                        Tất cả
                    </a>
                    <?php foreach ($all_cats as $cat) : ?>
                        <a href="<?php echo get_term_link($cat); ?>" class="filter-link <?php echo ($current_category && $current_category->term_id == $cat->term_id) ? 'active' : ''; ?>">
                            <?php echo esc_html($cat->name); ?>
                            <span class="count">(<?php echo $cat->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($news_query->have_posts()) : ?>
                    <div class="news-grid">
                        <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                            <?php get_template_part('template-parts/news', 'card'); ?>
                        <?php endwhile; ?>
                    </div>

                    <?php if ($news_query->max_num_pages > 1) : ?>
                    <div class="pagination">
                        <?php
                        echo paginate_links(array(
                            'total' => $news_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '<i class="fas fa-chevron-left"></i>',
                            'next_text' => '<i class="fas fa-chevron-right"></i>',
                            'type' => 'list',
                            'end_size' => 1,
                            'mid_size' => 1,
                        ));
                        ?>
                    </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="no-news">
                        <i class="fas fa-newspaper"></i>
                        <h3>Chưa có bài viết nào</h3>
                        <p>Chúng tôi sẽ cập nhật tin tức mới sớm nhất cho bạn.</p>
                    </div>
                <?php
                endif;
                wp_reset_postdata();
                ?>
            </div>

            <aside class="news-sidebar">
                <?php get_sidebar('news'); ?>
            </aside>
        </div>
    </div>
</div>

<style>
/* Compact News Page Styles */
.news-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 0;
    margin-bottom: 40px;
}

.news-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 32px;
    color: white;
    margin: 0 0 8px;
    font-weight: 700;
}

.page-description {
    color: rgba(255,255,255,0.9);
    margin: 0;
    font-size: 14px;
}

.compact-search {
    display: flex;
    background: white;
    border-radius: 30px;
    overflow: hidden;
    min-width: 280px;
}

.compact-search input {
    flex: 1;
    border: none;
    padding: 10px 18px;
    font-size: 14px;
    outline: none;
}

.compact-search button {
    background: #667eea;
    border: none;
    padding: 0 18px;
    color: white;
    cursor: pointer;
    transition: background 0.3s;
}

.compact-search button:hover {
    background: #5a67d8;
}

/* Category Filter */
.category-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.filter-link {
    padding: 5px 12px;
    background: #f5f5f5;
    border-radius: 20px;
    color: #666;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s;
}

.filter-link .count {
    font-size: 11px;
    opacity: 0.7;
}

.filter-link.active,
.filter-link:hover {
    background: #667eea;
    color: white;
}

/* News Grid */
.news-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

/* Compact Pagination */
.pagination {
    text-align: center;
}

.pagination ul {
    display: flex;
    justify-content: center;
    gap: 5px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination a,
.pagination .current {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    color: #666;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
}

.pagination a:hover,
.pagination .current {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

.pagination .dots {
    background: transparent;
    border: none;
}

/* No News */
.no-news {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
}

.no-news i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 15px;
}

.no-news h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #666;
}

.no-news p {
    color: #999;
}

/* Sidebar - Compact */
.news-sidebar {
    flex: 0 0 300px;
}

/* Responsive */
@media (max-width: 992px) {
    .news-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .compact-search {
        width: 100%;
        max-width: 400px;
    }
    
    .news-wrapper {
        flex-direction: column;
    }
    
    .news-sidebar {
        flex: auto;
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .news-header {
        padding: 30px 0;
    }
    
    .page-title {
        font-size: 24px;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .category-filter {
        gap: 6px;
    }
    
    .filter-link {
        font-size: 12px;
        padding: 4px 10px;
    }
}
</style>

<?php get_footer(); ?>