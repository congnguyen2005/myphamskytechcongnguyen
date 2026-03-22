<?php
/**
 * Single News Template - Premium Layout
 * @package MyPhamTheme
 */

get_header();

while (have_posts()) : the_post();
    $categories = get_the_terms(get_the_ID(), 'news_category');
    $tags = get_the_terms(get_the_ID(), 'news_tag');
    $author_id = get_the_author_meta('ID');
    $author_avatar = get_avatar_url($author_id, ['size' => 120]);
    $author_name = get_the_author();
    $author_bio = get_the_author_meta('description');
    $author_url = get_author_posts_url($author_id);
    $post_views = get_post_meta(get_the_ID(), '_news_views', true) ?: 0;
    update_post_meta(get_the_ID(), '_news_views', $post_views + 1);
    
    // Lấy bài viết liên quan cùng category
    $related_args = array(
        'post_type' => 'news',
        'posts_per_page' => 4,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    
    if ($categories && !is_wp_error($categories)) {
        $cat_ids = wp_list_pluck($categories, 'term_id');
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'news_category',
                'field' => 'term_id',
                'terms' => $cat_ids,
            ),
        );
    }
    
    $related_query = new WP_Query($related_args);
    
    // Lấy bài viết phổ biến
    $popular_args = array(
        'post_type' => 'news',
        'posts_per_page' => 5,
        'meta_key' => '_news_views',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'post__not_in' => array(get_the_ID())
    );
    $popular_query = new WP_Query($popular_args);
    
    // Tính thời gian đọc
    $content = strip_tags(get_the_content());
    $word_count = str_word_count($content);
    $reading_time = ceil($word_count / 200);
?>

<!-- Article Hero Section -->
<section class="article-hero">
    <div class="hero-background">
        <?php if (has_post_thumbnail()) : ?>
            <div class="hero-bg-image" style="background-image: url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>')"></div>
            <div class="hero-overlay gradient-overlay"></div>
        <?php else : ?>
            <div class="hero-gradient default-gradient"></div>
        <?php endif; ?>
    </div>
    <div class="container">
        <div class="hero-content">

            
            <?php if ($categories && !is_wp_error($categories)) : ?>
                <div class="article-category-badge">
                    <?php foreach ($categories as $cat) : ?>
                        <a href="<?php echo get_term_link($cat); ?>" class="category-link">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <h1 class="article-title"><?php the_title(); ?></h1>
            
            <div class="article-meta-hero">
                <div class="meta-author">
                    <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar">
                    <div class="author-info">
                        <span class="author-name"><?php echo esc_html($author_name); ?></span>
                        <span class="author-title">Tác giả</span>
                    </div>
                </div>
                <div class="meta-stats">
                    <div class="stat">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo get_the_date('d/m/Y'); ?></span>
                    </div>
                    <div class="stat">
                        <i class="far fa-clock"></i>
                        <span><?php echo $reading_time; ?> phút đọc</span>
                    </div>
                    <div class="stat">
                        <i class="far fa-eye"></i>
                        <span><?php echo number_format($post_views); ?> lượt xem</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<div class="single-news-wrapper">
    <div class="container">
        <div class="news-layout">
            <!-- Main Content -->
            <main class="news-main-content">
                <!-- Table of Contents -->
                <?php
                $headings = array();
                $content_blocks = get_the_content();
                if (preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $content_blocks, $matches)) {
                    if (!empty($matches[1])) {
                ?>
                <div class="table-of-contents">
                    <div class="toc-header">
                        <i class="fas fa-list-ul"></i>
                        <span>Mục lục bài viết</span>
                        <button class="toc-toggle"><i class="fas fa-chevron-down"></i></button>
                    </div>
                    <ul class="toc-list">
                        <?php foreach ($matches[1] as $index => $heading) : ?>
                            <li><a href="#toc-<?php echo $index; ?>"><?php echo strip_tags($heading); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php } } ?>
                
                <!-- Featured Image -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="article-featured-image">
                        <?php the_post_thumbnail('full', ['class' => 'featured-image']); ?>
                        <?php if (get_post(get_post_thumbnail_id())->post_excerpt) : ?>
                            <div class="image-caption">
                                <i class="fas fa-camera"></i>
                                <?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Article Content -->
                <article class="article-content" id="article-content">
                    <?php 
                    // Thêm ID vào các heading H2
                    $content = get_the_content();
                    $heading_index = 0;
                    $content = preg_replace_callback('/<h2[^>]*>(.*?)<\/h2>/i', function($matches) use (&$heading_index) {
                        $id = 'toc-' . $heading_index++;
                        return '<h2 id="' . $id . '">' . $matches[1] . '</h2>';
                    }, $content);
                    echo apply_filters('the_content', $content);
                    ?>
                </article>
                
                <!-- Tags Section -->
                <?php if ($tags && !is_wp_error($tags)) : ?>
                    <div class="article-tags-section">
                        <div class="tags-title">
                            <i class="fas fa-tags"></i>
                            <span>Thẻ bài viết</span>
                        </div>
                        <div class="tags-list">
                            <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo get_term_link($tag); ?>" class="tag-item">
                                    #<?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Share Section -->
                <div class="article-share-section">
                    <div class="share-label">
                        <i class="fas fa-share-alt"></i>
                        <span>Chia sẻ bài viết</span>
                    </div>
                    <div class="share-buttons-group">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                           class="share-btn facebook" target="_blank" rel="nofollow">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" 
                           class="share-btn twitter" target="_blank" rel="nofollow">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" 
                           class="share-btn linkedin" target="_blank" rel="nofollow">
                            <i class="fab fa-linkedin-in"></i>
                            <span>LinkedIn</span>
                        </a>
                        <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&media=<?php echo urlencode(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>&description=<?php echo urlencode(get_the_title()); ?>" 
                           class="share-btn pinterest" target="_blank" rel="nofollow">
                            <i class="fab fa-pinterest-p"></i>
                            <span>Pinterest</span>
                        </a>
                        <button class="share-btn copy-link" data-url="<?php the_permalink(); ?>">
                            <i class="fas fa-link"></i>
                            <span>Sao chép link</span>
                        </button>
                    </div>
                </div>
                
                <!-- Author Box Premium -->
                <div class="author-box-premium">
                    <div class="author-avatar-wrapper">
                        <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar-large">
                        <div class="author-social">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="author-info-detail">
                        <div class="author-name-wrapper">
                            <h3 class="author-name"><?php echo esc_html($author_name); ?></h3>
                            <span class="author-role">Tác giả chuyên mục</span>
                        </div>
                        <?php if ($author_bio) : ?>
                            <p class="author-bio"><?php echo esc_html($author_bio); ?></p>
                        <?php endif; ?>
                        <div class="author-stats">
                            <div class="stat-item">
                                <i class="fas fa-newspaper"></i>
                                <span><?php echo count_user_posts($author_id, 'news'); ?> bài viết</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span><?php echo number_format(get_user_meta($author_id, 'total_views', true) ?: 0); ?> lượt xem</span>
                            </div>
                        </div>
                        <a href="<?php echo $author_url; ?>" class="author-link">
                            Xem tất cả bài viết <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Navigation Posts -->
                <div class="post-navigation">
                    <div class="nav-prev">
                        <?php 
                        $prev_post = get_previous_post();
                        if ($prev_post) : 
                            $prev_thumbnail = get_the_post_thumbnail_url($prev_post->ID, 'thumbnail');
                        ?>
                            <a href="<?php echo get_permalink($prev_post); ?>" class="nav-link">
                                <span class="nav-label"><i class="fas fa-arrow-left"></i> Bài trước</span>
                                <div class="nav-content">
                                    <?php if ($prev_thumbnail) : ?>
                                        <img src="<?php echo esc_url($prev_thumbnail); ?>" alt="<?php echo esc_attr($prev_post->post_title); ?>" class="nav-thumb">
                                    <?php endif; ?>
                                    <span class="nav-title"><?php echo get_the_title($prev_post); ?></span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="nav-next">
                        <?php 
                        $next_post = get_next_post();
                        if ($next_post) : 
                            $next_thumbnail = get_the_post_thumbnail_url($next_post->ID, 'thumbnail');
                        ?>
                            <a href="<?php echo get_permalink($next_post); ?>" class="nav-link">
                                <span class="nav-label">Bài sau <i class="fas fa-arrow-right"></i></span>
                                <div class="nav-content">
                                    <span class="nav-title"><?php echo get_the_title($next_post); ?></span>
                                    <?php if ($next_thumbnail) : ?>
                                        <img src="<?php echo esc_url($next_thumbnail); ?>" alt="<?php echo esc_attr($next_post->post_title); ?>" class="nav-thumb">
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Related Articles -->
                <?php if ($related_query->have_posts()) : ?>
                    <div class="related-articles-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="fas fa-layer-group"></i>
                                Bài viết liên quan
                            </h3>
                            <a href="<?php echo get_post_type_archive_link('news'); ?>" class="view-all">
                                Xem tất cả <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="related-grid">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <div class="related-card">
                                    <a href="<?php the_permalink(); ?>" class="related-image">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                        <?php else : ?>
                                            <img src="https://placehold.co/400x300/f0f0f0/999?text=No+Image" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                        <?php endif; ?>
                                        <div class="image-overlay"></div>
                                    </a>
                                    <div class="related-content">
                                        <h4 class="related-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                        <div class="related-meta">
                                            <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date('d/m/Y'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; wp_reset_postdata(); ?>
            </main>
            
            <!-- Sidebar -->
            <aside class="news-sidebar">
                <!-- Sticky Sidebar -->
                <div class="sticky-sidebar">
                    <!-- Search Widget -->
                    <div class="sidebar-widget search-widget">
                        <h3 class="widget-title">Tìm kiếm</h3>
                        <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                            <input type="search" name="s" placeholder="Tìm kiếm bài viết..." value="<?php echo get_search_query(); ?>">
                            <input type="hidden" name="post_type" value="news">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    
                    <!-- Table of Contents Widget (Mobile) -->
                    <div class="sidebar-widget toc-widget mobile-toc">
                        <h3 class="widget-title">Mục lục</h3>
                        <div class="toc-widget-content"></div>
                    </div>
                    
                    <!-- Popular Posts Widget -->
                    <?php if ($popular_query->have_posts()) : ?>
                        <div class="sidebar-widget popular-widget">
                            <h3 class="widget-title">
                                <i class="fas fa-fire"></i> Bài viết nổi bật
                            </h3>
                            <ul class="popular-list">
                                <?php 
                                $rank = 1;
                                while ($popular_query->have_posts()) : $popular_query->the_post(); 
                                    $views = get_post_meta(get_the_ID(), '_news_views', true) ?: 0;
                                ?>
                                    <li class="popular-item">
                                        <span class="popular-rank rank-<?php echo $rank; ?>"><?php echo $rank; ?></span>
                                        <div class="popular-content">
                                            <a href="<?php the_permalink(); ?>" class="popular-title"><?php the_title(); ?></a>
                                            <div class="popular-meta">
                                                <span><i class="far fa-eye"></i> <?php echo number_format($views); ?></span>
                                            </div>
                                        </div>
                                    </li>
                                <?php $rank++; endwhile; wp_reset_postdata(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Newsletter Widget -->
                    <div class="sidebar-widget newsletter-widget">
                        <h3 class="widget-title">Nhận tin mới</h3>
                        <div class="newsletter-box">
                            <p>Đăng ký để nhận thông báo về bài viết mới nhất</p>
                            <form class="sidebar-newsletter">
                                <input type="email" placeholder="Email của bạn" required>
                                <button type="submit">Đăng ký</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- Reading Progress Bar -->
<div class="reading-progress-container">
    <div class="reading-progress-bar"></div>
</div>

<!-- Floating Action Buttons -->
<div class="floating-actions">
    <button class="floating-btn toc-floating" title="Mục lục">
        <i class="fas fa-list-ul"></i>
        <span class="btn-label">Mục lục</span>
    </button>
    <button class="floating-btn share-floating" title="Chia sẻ">
        <i class="fas fa-share-alt"></i>
        <span class="btn-label">Chia sẻ</span>
    </button>
    <button class="floating-btn scroll-top-floating" title="Lên đầu trang">
        <i class="fas fa-arrow-up"></i>
        <span class="btn-label">Lên đầu</span>
    </button>
</div>

<style>
/* Article Hero Section */
.article-hero {
    position: relative;
    min-height: 500px;
    display: flex;
    align-items: center;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.hero-bg-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    transform: scale(1.05);
    animation: heroZoom 20s ease-out;
}

@keyframes heroZoom {
    from { transform: scale(1.05); }
    to { transform: scale(1); }
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.gradient-overlay {
    background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 100%);
}

.default-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    padding: 80px 0;
    max-width: 900px;
    margin: 0 auto;
}

.article-breadcrumb {
    font-size: 14px;
    margin-bottom: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.article-breadcrumb a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: color 0.3s;
}

.article-breadcrumb a:hover {
    color: white;
}

.article-breadcrumb i {
    font-size: 10px;
    color: rgba(255,255,255,0.5);
}

.article-category-badge {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.category-link {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    padding: 6px 18px;
    border-radius: 30px;
    color: white;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s;
}

.category-link:hover {
    background: white;
    color: #667eea;
    transform: translateY(-2px);
}

.article-title {
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 30px;
    line-height: 1.3;
}

.article-meta-hero {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    flex-wrap: wrap;
}

.meta-author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid white;
}

.author-info {
    text-align: left;
}

.author-name {
    display: block;
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 4px;
}

.author-title {
    font-size: 12px;
    opacity: 0.8;
}

.meta-stats {
    display: flex;
    gap: 25px;
}

.stat {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.stat i {
    font-size: 14px;
    opacity: 0.8;
}

.hero-wave {
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    line-height: 0;
}

/* Main Content Layout */
.single-news-wrapper {
    padding: 60px 0;
    background: #f8f9fa;
}

.news-layout {
    display: flex;
    gap: 50px;
}

.news-main-content {
    flex: 2.5;
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.news-sidebar {
    flex: 1;
    min-width: 320px;
}

/* Table of Contents */
.table-of-contents {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 40px;
    border-left: 4px solid #667eea;
}

.toc-header {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 16px;
    color: #333;
    cursor: pointer;
}

.toc-header i {
    color: #667eea;
    font-size: 18px;
}

.toc-toggle {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
    color: #999;
    transition: transform 0.3s;
}

.toc-toggle.open {
    transform: rotate(180deg);
}

.toc-list {
    list-style: none;
    margin-top: 15px;
    padding-left: 0;
    display: none;
}

.toc-list.show {
    display: block;
}

.toc-list li {
    margin-bottom: 10px;
}

.toc-list a {
    color: #666;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
    display: block;
    padding: 5px 0 5px 20px;
    border-left: 2px solid transparent;
}

.toc-list a:hover,
.toc-list a.active {
    color: #667eea;
    border-left-color: #667eea;
    padding-left: 20px;
}

/* Featured Image */
.article-featured-image {
    margin-bottom: 40px;
}

.featured-image {
    width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.image-caption {
    margin-top: 12px;
    font-size: 13px;
    color: #999;
    text-align: center;
}

.image-caption i {
    margin-right: 5px;
}

/* Article Content */
.article-content {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
}

.article-content h2 {
    font-size: 28px;
    margin: 40px 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.article-content h3 {
    font-size: 22px;
    margin: 30px 0 15px;
}

.article-content p {
    margin-bottom: 20px;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 20px 0;
}

.article-content blockquote {
    margin: 30px 0;
    padding: 20px 30px;
    background: #f8f9fa;
    border-left: 4px solid #667eea;
    font-style: italic;
    color: #666;
}

.article-content code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 14px;
}

/* Tags Section */
.article-tags-section {
    margin: 40px 0;
    padding-top: 30px;
    border-top: 1px solid #e0e0e0;
}

.tags-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
    font-weight: 600;
    color: #333;
}

.tags-title i {
    color: #667eea;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-item {
    background: #f0f0f0;
    padding: 5px 12px;
    border-radius: 20px;
    color: #666;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s;
}

.tag-item:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

/* Share Section */
.article-share-section {
    margin: 40px 0;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 16px;
    text-align: center;
}

.share-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    color: #333;
}

.share-buttons-group {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 40px;
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.share-btn.facebook { background: #1877f2; }
.share-btn.twitter { background: #1da1f2; }
.share-btn.linkedin { background: #0077b5; }
.share-btn.pinterest { background: #e60023; }
.share-btn.copy-link { background: #666; }

/* Author Box Premium */
.author-box-premium {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 20px;
    padding: 30px;
    margin: 40px 0;
    display: flex;
    gap: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.author-avatar-wrapper {
    position: relative;
    flex-shrink: 0;
}

.author-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.author-social {
    position: absolute;
    bottom: 0;
    right: 0;
    display: flex;
    gap: 5px;
}

.social-icon {
    width: 30px;
    height: 30px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.social-icon:hover {
    background: #667eea;
    color: white;
    transform: scale(1.1);
}

.author-info-detail {
    flex: 1;
}

.author-name-wrapper {
    margin-bottom: 15px;
}

.author-name {
    font-size: 24px;
    margin: 0 0 5px;
    color: #333;
}

.author-role {
    font-size: 13px;
    color: #667eea;
    font-weight: 500;
}

.author-bio {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
}

.author-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
}

.author-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
}

.author-stats .stat-item i {
    color: #667eea;
}

.author-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: gap 0.3s;
}

.author-link:hover {
    gap: 12px;
}

/* Post Navigation */
.post-navigation {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin: 40px 0;
    padding-top: 30px;
    border-top: 1px solid #e0e0e0;
}

.nav-prev,
.nav-next {
    flex: 1;
}

.nav-link {
    text-decoration: none;
    display: block;
    transition: transform 0.3s;
}

.nav-link:hover {
    transform: translateX(-5px);
}

.nav-next .nav-link:hover {
    transform: translateX(5px);
}

.nav-label {
    font-size: 13px;
    color: #999;
    margin-bottom: 10px;
    display: block;
}

.nav-content {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-thumb {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
}

.nav-title {
    font-weight: 600;
    color: #333;
    font-size: 15px;
    line-height: 1.4;
}

.nav-next .nav-content {
    justify-content: flex-end;
}

/* Related Articles */
.related-articles-section {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid #e0e0e0;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #667eea;
}

.view-all {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: gap 0.3s;
}

.view-all:hover {
    gap: 10px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

.related-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}

.related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.related-image {
    position: relative;
    display: block;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.related-card:hover .related-image img {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    opacity: 0;
    transition: opacity 0.3s;
}

.related-card:hover .image-overlay {
    opacity: 1;
}

.related-content {
    padding: 20px;
}

.related-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.4;
}

.related-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}

.related-title a:hover {
    color: #667eea;
}

.related-meta {
    font-size: 12px;
    color: #999;
}

.related-meta i {
    margin-right: 4px;
}

/* Sidebar Widgets */
.sidebar-widget {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.widget-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.widget-title i {
    color: #667eea;
}

/* Search Widget */
.search-form {
    display: flex;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
}

.search-form:focus-within {
    border-color: #667eea;
}

.search-form input {
    flex: 1;
    padding: 12px 15px;
    border: none;
    outline: none;
    font-size: 14px;
}

.search-form button {
    background: #667eea;
    border: none;
    padding: 0 20px;
    color: white;
    cursor: pointer;
    transition: background 0.3s;
}

.search-form button:hover {
    background: #5a67d8;
}

/* Popular Widget */
.popular-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.popular-item {
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.popular-item:last-child {
    border-bottom: none;
}

.popular-rank {
    width: 30px;
    height: 30px;
    background: #f0f0f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.rank-1 { background: #ff6b6b; color: white; }
.rank-2 { background: #ffa502; color: white; }
.rank-3 { background: #ffb347; color: white; }

.popular-content {
    flex: 1;
}

.popular-title {
    display: block;
    color: #333;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    margin-bottom: 5px;
    transition: color 0.3s;
}

.popular-title:hover {
    color: #667eea;
}

.popular-meta {
    font-size: 11px;
    color: #999;
}

.popular-meta i {
    margin-right: 3px;
}

/* Newsletter Widget */
.newsletter-box p {
    font-size: 13px;
    color: #666;
    margin-bottom: 15px;
}

.sidebar-newsletter {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sidebar-newsletter input {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s;
}

.sidebar-newsletter input:focus {
    border-color: #667eea;
}

.sidebar-newsletter button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.sidebar-newsletter button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(102,126,234,0.3);
}

/* Reading Progress Bar */
.reading-progress-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: rgba(0,0,0,0.05);
    z-index: 1000;
}

.reading-progress-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
}

/* Floating Action Buttons */
.floating-actions {
    position: fixed;
    right: 30px;
    bottom: 30px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    z-index: 999;
}

.floating-btn {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s;
    border: none;
    position: relative;
    color: #667eea;
}

.floating-btn:hover {
    transform: scale(1.1);
    background: #667eea;
    color: white;
}

.btn-label {
    position: absolute;
    right: 60px;
    background: #333;
    color: white;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.floating-btn:hover .btn-label {
    opacity: 1;
    visibility: visible;
    right: 70px;
}

/* Sticky Sidebar */
.sticky-sidebar {
    position: sticky;
    top: 100px;
}

/* Responsive */
@media (max-width: 1200px) {
    .news-layout {
        gap: 30px;
    }
    
    .news-main-content {
        padding: 30px;
    }
}

@media (max-width: 992px) {
    .news-layout {
        flex-direction: column;
    }
    
    .news-sidebar {
        min-width: auto;
    }
    
    .sticky-sidebar {
        position: static;
    }
    
    .article-title {
        font-size: 36px;
    }
    
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .article-hero {
        min-height: 400px;
    }
    
    .hero-content {
        padding: 60px 0;
    }
    
    .article-title {
        font-size: 28px;
    }
    
    .article-meta-hero {
        flex-direction: column;
        gap: 20px;
    }
    
    .news-main-content {
        padding: 20px;
    }
    
    .author-box-premium {
        flex-direction: column;
        text-align: center;
    }
    
    .author-avatar-wrapper {
        margin: 0 auto;
    }
    
    .author-stats {
        justify-content: center;
    }
    
    .post-navigation {
        flex-direction: column;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
    }
    
    .share-buttons-group {
        flex-direction: column;
    }
    
    .share-btn {
        justify-content: center;
    }
    
    .floating-actions {
        right: 15px;
        bottom: 15px;
    }
    
    .floating-btn {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 480px) {
    .article-title {
        font-size: 24px;
    }
    
    .category-link {
        font-size: 11px;
        padding: 4px 12px;
    }
    
    .meta-stats {
        flex-direction: column;
        gap: 8px;
        align-items: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Table of Contents Toggle
    $('.toc-header').on('click', function() {
        $('.toc-list').toggleClass('show');
        $('.toc-toggle').toggleClass('open');
    });
    
    // Active TOC on scroll
    $(window).on('scroll', function() {
        var scrollPos = $(window).scrollTop();
        $('.toc-list a').each(function() {
            var target = $(this).attr('href');
            var element = $(target);
            if (element.length && element.offset().top - 150 < scrollPos && element.offset().top + element.height() > scrollPos) {
                $('.toc-list a').removeClass('active');
                $(this).addClass('active');
            }
        });
    });
    
    // Reading Progress
    $(window).on('scroll', function() {
        var winScroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var scrolled = (winScroll / height) * 100;
        $('.reading-progress-bar').css('width', scrolled + '%');
    });
    
    // Copy Link
    $('.copy-link').on('click', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        navigator.clipboard.writeText(url).then(function() {
            alert('Đã sao chép link!');
        });
    });
    
    // Floating Actions
    $('.toc-floating').on('click', function() {
        $('.table-of-contents')[0].scrollIntoView({ behavior: 'smooth' });
    });
    
    $('.share-floating').on('click', function() {
        $('.article-share-section')[0].scrollIntoView({ behavior: 'smooth' });
    });
    
    $('.scroll-top-floating').on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
    
    // Newsletter
    $('.sidebar-newsletter').on('submit', function(e) {
        e.preventDefault();
        var email = $(this).find('input').val();
        if (email) {
            alert('Cảm ơn bạn đã đăng ký!');
            $(this).find('input').val('');
        }
    });
    
    // Smooth scroll for TOC links
    $('.toc-list a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $(target)[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    
    // Add reading time to meta
    var readingTime = <?php echo $reading_time; ?>;
    if (readingTime) {
        $('.stat:has(.fa-clock) span').text(readingTime + ' phút đọc');
    }
});
</script>

<?php
endwhile;
get_footer();
?>