<?php
get_header();

// Lấy thông tin danh mục hiện tại
$current_term = get_queried_object();
$taxonomy = 'product_category';

// Xác định tiêu đề trang
if (is_tax($taxonomy)) {
    $page_title = $current_term->name;
    $current_term_id = $current_term->term_id;
    $current_term_slug = $current_term->slug;
    $term_description = $current_term->description;
} else {
    $page_title = 'Tất cả sản phẩm';
    $current_term_id = 0;
    $current_term_slug = '';
    $term_description = '';
}

// Lấy tổng số sản phẩm hiện tại
$total_products = $wp_query->found_posts;
?>

<!-- Hero Section - Premium Banner -->
<section class="shop-hero premium-hero">
    <div class="hero-bg-animation"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-icon">✨</span>
                <span>Premium Collection 2024</span>
            </div>
            <h1 class="hero-title">
                <?php echo esc_html($page_title); ?>
                <span class="hero-accent">.</span>
            </h1>
            <?php if ($term_description) : ?>
                <p class="hero-description"><?php echo esc_html($term_description); ?></p>
            <?php else : ?>
                <p class="hero-description">Khám phá bộ sưu tập sản phẩm cao cấp, thiết kế tinh tế và chất lượng vượt trội</p>
            <?php endif; ?>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $total_products; ?></span>
                    <span class="stat-label">Sản phẩm</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count(get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false])); ?></span>
                    <span class="stat-label">Danh mục</span>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="rgba(255,255,255,0.95)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<!-- Premium Shop Page -->
<section class="shop-page premium-shop">
    <div class="container">
        <div class="shop-wrapper premium-wrapper">
            
            <!-- Premium Sidebar - Redesigned -->
            <aside class="shop-sidebar premium-sidebar">
                <!-- Floating Action Button for Mobile -->
                <div class="mobile-filter-toggle">
                    <button class="filter-toggle-btn" id="mobileFilterToggle">
                        <i class="fas fa-sliders-h"></i>
                        <span>Bộ lọc</span>
                    </button>
                </div>

                <div class="sidebar-inner" id="sidebarInner">
                    <!-- Close Button for Mobile -->
                    <div class="sidebar-close">
                        <button id="closeSidebar"><i class="fas fa-times"></i></button>
                    </div>

                    <!-- Categories Widget - Modern Design -->
                    <div class="sidebar-widget categories-widget premium-widget">
                        <div class="widget-header">
                            <div class="widget-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h3 class="widget-title premium-title">Danh mục sản phẩm</h3>
                        </div>
                        
                        <div class="category-stats premium-stats" id="category-stats">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="stats-info">
                                    <span class="stats-label">Tổng sản phẩm</span>
                                    <span class="stats-number premium-total" data-total="<?php echo $total_products; ?>">
                                        <?php echo $total_products; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="category-list-wrapper premium-category-list">
                            <!-- Tất cả sản phẩm - Modern Card -->
                            <a href="<?php echo get_post_type_archive_link('product'); ?>" 
                               class="category-item premium-category <?php echo !is_tax($taxonomy) ? 'active' : ''; ?>" 
                               data-slug="all" data-name="Tất cả sản phẩm">
                                <div class="category-content">
                                    <div class="category-icon-wrapper">
                                        <div class="category-icon-bg">
                                            <i class="fas fa-store"></i>
                                        </div>
                                    </div>
                                    <div class="category-details">
                                        <span class="category-name">Tất cả sản phẩm</span>
                                        <span class="category-count"><?php echo $total_products; ?> sản phẩm</span>
                                    </div>
                                </div>
                                <div class="category-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>

                            <?php
                            $categories = get_terms([
                                'taxonomy' => $taxonomy,
                                'hide_empty' => false,
                                'orderby' => 'name',
                                'order' => 'ASC'
                            ]);

                            if ($categories && !is_wp_error($categories)) :
                                foreach ($categories as $category) :
                                    $is_active = (is_tax($taxonomy) && $current_term->term_id == $category->term_id);
                                    $category_image = get_term_meta($category->term_id, 'category_image', true);
                            ?>
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>" 
                                       class="category-item premium-category <?php echo $is_active ? 'active' : ''; ?>" 
                                       data-slug="<?php echo esc_attr($category->slug); ?>"
                                       data-name="<?php echo esc_attr($category->name); ?>"
                                       data-count="<?php echo $category->count; ?>">
                                        <div class="category-content">
                                            <div class="category-icon-wrapper">
                                                <div class="category-icon-bg" style="<?php echo $category_image ? 'background-image: url(' . $category_image . ')' : ''; ?>">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <div class="category-details">
                                                <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                                <span class="category-count"><?php echo $category->count; ?> sản phẩm</span>
                                            </div>
                                        </div>
                                        <div class="category-arrow">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </a>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Premium Price Filter - Enhanced -->
                    <div class="sidebar-widget price-widget premium-price-widget">
                        <div class="widget-header">
                            <div class="widget-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="widget-title premium-title">Lọc theo giá</h3>
                        </div>
                        <div class="price-range premium-price-range">
                            <div class="price-range-chart">
                                <div class="range-track">
                                    <div class="range-fill" id="rangeFill"></div>
                                </div>
                                <div class="price-slider premium-slider">
                                    <input type="range" id="min-price" min="0" max="100000000" step="100000" value="0">
                                    <input type="range" id="max-price" min="0" max="100000000" step="100000" value="100000000">
                                </div>
                            </div>
                            <div class="price-values premium-price-values">
                                <div class="price-value-card">
                                    <span class="price-label">Từ</span>
                                    <span id="min-price-value" class="price-amount">0₫</span>
                                </div>
                                <div class="price-divider">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="price-value-card">
                                    <span class="price-label">Đến</span>
                                    <span id="max-price-value" class="price-amount">100.000.000₫</span>
                                </div>
                            </div>
                            <div class="price-inputs premium-price-inputs">
                                <div class="input-group">
                                    <span class="input-icon">₫</span>
                                    <input type="number" id="min-price-input" placeholder="0" min="0">
                                </div>
                                <div class="input-group">
                                    <span class="input-icon">₫</span>
                                    <input type="number" id="max-price-input" placeholder="100.000.000" min="0">
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button class="filter-btn premium-filter-btn" id="apply-price-filter">
                                    <i class="fas fa-filter"></i>
                                    <span>Áp dụng</span>
                                </button>
                                <button class="filter-btn clear-filter premium-clear-btn" id="clear-price-filter">
                                    <i class="fas fa-undo-alt"></i>
                                    <span>Đặt lại</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Brands / Tags Section -->
                    <div class="sidebar-widget tags-widget premium-tags-widget">
                        <div class="widget-header">
                            <div class="widget-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h3 class="widget-title premium-title">Thương hiệu nổi bật</h3>
                        </div>
                        <div class="tags-cloud">
                            <?php
                            $brands = get_terms([
                                'taxonomy' => 'product_brand',
                                'hide_empty' => true,
                                'number' => 8
                            ]);
                            if ($brands && !is_wp_error($brands)) :
                                foreach ($brands as $brand) : ?>
                                    <a href="<?php echo get_term_link($brand); ?>" class="tag-item">
                                        <?php echo esc_html($brand->name); ?>
                                    </a>
                                <?php endforeach;
                            else : ?>
                                <span class="no-tags">Chưa có thương hiệu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Premium Main Content - Enhanced -->
            <main class="shop-content premium-content">
                <!-- Premium Shop Controls - Modern -->
                <div class="shop-controls premium-controls">
                    <div class="results-info premium-results">
                        <div class="results-badge">
                            <i class="fas fa-sparkles"></i>
                            <span class="results-count" id="results-count">
                                <span id="products-count"><?php echo $total_products; ?></span> sản phẩm
                            </span>
                        </div>
                        <div class="view-toggle premium-view-toggle">
                            <button class="view-btn grid-view active premium-grid-btn" data-view="grid" title="Lưới">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-btn list-view premium-list-btn" data-view="list" title="Danh sách">
                                <i class="fas fa-list-ul"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="shop-sorting premium-sorting">
                        <div class="sorting-wrapper">
                            <i class="fas fa-arrow-up-wide-short sorting-icon"></i>
                            <form method="get" class="sorting-form premium-sorting-form" id="sorting-form">
                                <select name="orderby" id="orderby-select" class="premium-select">
                                    <option value="">Sắp xếp mặc định</option>
                                    <option value="date">🆕 Mới nhất</option>
                                    <option value="price">💰 Giá thấp đến cao</option>
                                    <option value="price-desc">💎 Giá cao đến thấp</option>
                                    <option value="title">🔤 Tên A → Z</option>
                                    <option value="title-desc">🔤 Tên Z → A</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <div class="active-filters" id="activeFilters" style="display: none;">
                    <div class="filters-header">
                        <span><i class="fas fa-sliders-h"></i> Bộ lọc đang áp dụng:</span>
                        <button id="clearAllFilters" class="clear-all-btn">
                            <i class="fas fa-trash-alt"></i> Xóa tất cả
                        </button>
                    </div>
                    <div class="filters-list" id="filtersList"></div>
                </div>

                <!-- Premium Products Container - Enhanced -->
                <div class="products-container premium-products-container">
                    <div class="products-grid premium-grid" id="products-grid">
                        <?php
                        if (have_posts()) :
                            while (have_posts()) : the_post();
                                include_partial_product_card_premium();
                            endwhile;
                        else :
                        ?>
                            <div class="no-products premium-no-products">
                                <div class="no-products-animation">
                                    <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_ysa6e2qv.json" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></lottie-player>
                                </div>
                                <h3>Không tìm thấy sản phẩm</h3>
                                <p>Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm danh mục khác</p>
                                <a href="<?php echo get_post_type_archive_link('product'); ?>" class="btn-explore">
                                    <i class="fas fa-compass"></i>
                                    <span>Khám phá tất cả</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="loading-skeleton premium-skeleton" id="loading-skeleton" style="display: none;">
                        <?php for ($i = 0; $i < 8; $i++) : ?>
                            <div class="skeleton-card premium-skeleton-card">
                                <div class="skeleton-image"></div>
                                <div class="skeleton-content">
                                    <div class="skeleton-title"></div>
                                    <div class="skeleton-price"></div>
                                    <div class="skeleton-button"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Premium Pagination - Enhanced -->
                <nav class="pagination-wrapper premium-pagination" id="pagination-wrapper">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<i class="fas fa-chevron-left"></i><span>Trước</span>',
                        'next_text' => '<span>Sau</span><i class="fas fa-chevron-right"></i>',
                        'screen_reader_text' => ' '
                    ));
                    ?>
                </nav>
            </main>
        </div>
    </div>
</section>

<?php
// PREMIUM PRODUCT CARD FUNCTION - ULTRA MODERN DESIGN
function include_partial_product_card_premium() {
    $product_id = get_the_ID();
    $regular_price = get_post_meta($product_id, '_product_price', true) ?: 0;
    $sale_price = get_post_meta($product_id, '_product_sale_price', true) ?: 0;
    $has_sale = $sale_price && $sale_price < $regular_price;
    $current_price = $has_sale ? $sale_price : $regular_price;
    $has_price = $regular_price > 0;
    $discount_percent = $has_sale ? round(($regular_price - $sale_price) / $regular_price * 100) : 0;
    
    // Get product gallery images
    $gallery_images = get_post_meta($product_id, '_product_gallery', true);
    $gallery_images = $gallery_images ? explode(',', $gallery_images) : [];
    $has_gallery = !empty($gallery_images);
?>
    <article class="product-card premium-card ultra-premium" data-product-id="<?php echo $product_id; ?>">
        <!-- Modern Badge System -->
        <div class="product-badges">
            <?php if ($has_sale) : ?>
                <div class="badge sale-badge">
                    <span>-<?php echo $discount_percent; ?>%</span>
                    <div class="badge-shine"></div>
                </div>
            <?php endif; ?>
            <?php if (is_featured_product($product_id)) : ?>
                <div class="badge featured-badge">
                    <i class="fas fa-crown"></i>
                    <span>Nổi bật</span>
                </div>
            <?php endif; ?>
            <?php if (is_new_product($product_id)) : ?>
                <div class="badge new-badge">
                    <i class="fas fa-star"></i>
                    <span>Mới</span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Premium Image Section with Hover Gallery -->
        <div class="product-image premium-image-section">
            <a href="<?php the_permalink(); ?>" class="product-link premium-link" title="<?php the_title_attribute(); ?>">
                <div class="image-container">
                    <?php if (has_post_thumbnail()) :
                        the_post_thumbnail('medium_large', ['loading' => 'lazy', 'class' => 'product-main-img']);
                    else : ?>
                        <img src="https://placehold.co/600x600/f0f0f0/999999?text=🛍️" 
                             alt="<?php the_title_attribute(); ?>" class="product-main-img">
                    <?php endif; ?>
                    
                    <?php if ($has_gallery && $gallery_images[0]) : ?>
                        <div class="image-hover">
                            <img src="<?php echo wp_get_attachment_url($gallery_images[0]); ?>" 
                                 alt="<?php the_title_attribute(); ?>" class="product-hover-img">
                        </div>
                    <?php endif; ?>
                    
                    <div class="image-overlay premium-overlay">
                        <div class="quick-view-trigger" data-product-id="<?php echo $product_id; ?>">
                            <i class="fas fa-search-plus"></i>
                            <span>Xem nhanh</span>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Modern Quick Actions -->
            <div class="product-actions ultra-actions">
                <button class="action-btn quick-add" data-product-id="<?php echo $product_id; ?>" title="Thêm vào giỏ">
                    <i class="fas fa-shopping-bag"></i>
                </button>
                <button class="action-btn wishlist-btn" data-product-id="<?php echo $product_id; ?>" title="Yêu thích">
                    <i class="far fa-heart"></i>
                </button>
                <button class="action-btn compare-btn" data-product-id="<?php echo $product_id; ?>" title="So sánh">
                    <i class="fas fa-chart-simple"></i>
                </button>
            </div>
        </div>

        <!-- Premium Product Info - Modern -->
        <div class="product-info premium-info-section">
            <div class="product-category">
                <?php
                $categories = wp_get_post_terms($product_id, 'product_category');
                if ($categories && !is_wp_error($categories)) :
                    echo '<a href="' . get_term_link($categories[0]) . '">' . esc_html($categories[0]->name) . '</a>';
                endif;
                ?>
            </div>
            
            <h3 class="product-title premium-title">
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                    <?php the_title(); ?>
                </a>
            </h3>
            
            <!-- Modern Rating -->
            <div class="product-rating ultra-rating">
                <div class="stars">
                    <?php 
                    $rating = get_product_rating($product_id);
                    $full_stars = floor($rating);
                    $has_half = $rating - $full_stars >= 0.5;
                    for ($i = 1; $i <= 5; $i++) :
                        if ($i <= $full_stars) :
                            echo '<i class="fas fa-star"></i>';
                        elseif ($has_half && $i == $full_stars + 1) :
                            echo '<i class="fas fa-star-half-alt"></i>';
                        else :
                            echo '<i class="far fa-star"></i>';
                        endif;
                    endfor;
                    ?>
                </div>
                <span class="rating-score"><?php echo number_format($rating, 1); ?></span>
                <span class="review-count">(<?php echo get_product_review_count($product_id); ?> đánh giá)</span>
            </div>
            
            <!-- Premium Price Display - Modern -->
            <?php if ($has_price) : ?>
                <div class="product-price ultra-price-display">
                    <?php if ($has_sale) : ?>
                        <div class="price-wrapper">
                            <span class="price-old"><?php echo mypham_format_price($regular_price); ?></span>
                            <span class="price-current"><?php echo mypham_format_price($current_price); ?></span>
                        </div>
                        <div class="savings-badge">
                            <i class="fas fa-gift"></i>
                            <span>Tiết kiệm <?php echo mypham_format_price($regular_price - $current_price); ?></span>
                        </div>
                    <?php else : ?>
                        <div class="price-wrapper">
                            <span class="price-current"><?php echo mypham_format_price($current_price); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
                <!-- Modern Action Buttons with Price Highlight -->
<!-- In archive-product.php, update the buy now button section (around line 350-370) -->
                    <div class="product-actions-bottom ultra-actions-bottom">
                        <?php if ($has_price) : ?>
                            <button class="btn-buy-now ultra-buy-btn" 
                                    data-product-id="<?php echo $product_id; ?>" 
                                    data-price="<?php echo $current_price; ?>">
                                <i class="fas fa-bolt"></i>
                                <span class="buy-now-text">Mua ngay</span>
                                <span class="buy-now-price"><?php echo mypham_format_price($current_price); ?></span>
                                <div class="btn-glow"></div>
                            </button>
                            <div class="price-highlight ultra-price-highlight">
                                <div class="highlight-content">
                                    <span class="highlight-label">Giá ưu đãi</span>
                                    <strong class="highlight-price"><?php echo mypham_format_price($current_price); ?></strong>
                                </div>
                                <?php if ($has_sale) : ?>
                                    <div class="installment-badge">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Trả góp 0%</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <button class="btn-buy-now ultra-buy-btn" data-product-id="<?php echo $product_id; ?>">
                                <i class="fas fa-bolt"></i>
                                <span class="buy-now-text">Mua ngay</span>
                                <div class="btn-glow"></div>
                            </button>
                        <?php endif; ?>
                    </div>
        </div>
        
        <!-- Quick View Modal Structure -->
        <div class="quick-view-modal" id="quickViewModal-<?php echo $product_id; ?>" style="display: none;">
            <div class="modal-content">
                <button class="modal-close"><i class="fas fa-times"></i></button>
                <div class="modal-body"></div>
            </div>
        </div>
    </article>
<?php }

// Helper functions
function is_featured_product($product_id) {
    return get_post_meta($product_id, '_featured_product', true) === 'yes';
}

function is_new_product($product_id) {
    $post_date = get_post_datetime($product_id);
    $days_old = $post_date ? $post_date->diff(new DateTime())->days : 30;
    return $days_old <= 7;
}

function get_product_rating($product_id) {
    $rating = get_post_meta($product_id, '_product_rating', true);
    return $rating ? floatval($rating) : 4.5;
}

function get_product_review_count($product_id) {
    $count = get_post_meta($product_id, '_product_reviews_count', true);
    return $count ? intval($count) : 0;
}
?>

<?php get_footer(); ?>

<script>
var ajax_object = {
    ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('product_filter_nonce'); ?>'
};
</script>