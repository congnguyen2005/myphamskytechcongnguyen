<?php
$categories = get_the_terms(get_the_ID(), 'news_category');
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/400x250/f0f0f0/999?text=News';
$post_views = get_post_meta(get_the_ID(), '_news_views', true) ?: 0;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('news-card'); ?>>
    <div class="card-image">
        <a href="<?php the_permalink(); ?>">
            <img src="<?php echo esc_url($featured_image); ?>" 
                 alt="<?php the_title_attribute(); ?>" 
                 loading="lazy">
        </a>
        <?php if ($categories && !is_wp_error($categories)) : ?>
            <div class="card-category">
                <a href="<?php echo esc_url(get_term_link($categories[0])); ?>">
                    <?php echo esc_html($categories[0]->name); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-content">
        <div class="card-meta">
            <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date('d/m/Y'); ?></span>
            <span><i class="far fa-user"></i> <?php the_author(); ?></span>
            <span><i class="far fa-eye"></i> <?php echo number_format($post_views); ?></span>
        </div>
        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <p class="card-excerpt">
            <?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?>
        </p>
        <a href="<?php the_permalink(); ?>" class="read-more">
            Đọc tiếp <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</article>

<style>
.news-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.news-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.card-image {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.news-card:hover .card-image img {
    transform: scale(1.05);
}

.card-category {
    position: absolute;
    bottom: 12px;
    left: 12px;
}

.card-category a {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
}

.card-content {
    padding: 18px;
}

.card-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
    font-size: 11px;
    color: #999;
}

.card-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.card-meta i {
    font-size: 11px;
}

.card-title {
    font-size: 18px;
    margin-bottom: 10px;
    line-height: 1.4;
}

.card-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}

.card-title a:hover {
    color: #667eea;
}

.card-excerpt {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
    margin-bottom: 15px;
}

.read-more {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #667eea;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: gap 0.3s;
}

.read-more:hover {
    gap: 10px;
}

@media (max-width: 768px) {
    .card-content {
        padding: 15px;
    }
    
    .card-title {
        font-size: 16px;
    }
    
    .card-excerpt {
        font-size: 12px;
    }
}
</style>