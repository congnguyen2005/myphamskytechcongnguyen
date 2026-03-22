<?php
/**
 * Taxonomy Product Category Template
 * @package MyPhamTheme
 */

get_header();

$term = get_queried_object();
$term_id = $term->term_id;
$term_name = $term->name;
$term_description = $term->description;
$term_count = $term->count;

// Get child categories
$child_categories = get_terms([
    'taxonomy' => 'product_category',
    'parent' => $term_id,
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
]);

// Get featured products in this category
$featured_products = new WP_Query([
    'post_type' => 'product',
    'posts_per_page' => 4,
    'tax_query' => [[
        'taxonomy' => 'product_category',
        'field' => 'term_id',
        'terms' => $term_id
    ]],
    'meta_key' => '_product_sale_price',
    'orderby' => 'meta_value_num',
    'order' => 'DESC'
]);
?>

<!-- Enhanced Category Hero Section -->
<section class="category-hero-enhanced">
    <div class="container">
        <div class="category-hero-content">
            <div class="category-hero-info">
                <div class="category-breadcrumb">
                    <a href="<?php echo home_url(); ?>">Trang chủ</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="<?php echo get_post_type_archive_link('product'); ?>">Sản phẩm</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span class="current"><?php echo esc_html($term_name); ?></span>
                </div>
                
                <h1 class="category-title-enhanced">
                    <?php echo esc_html($term_name); ?>
                    <span class="category-badge"><?php echo $term_count; ?> sản phẩm</span>
                </h1>
                
                <?php if ($term_description) : ?>
                    <div class="category-description-enhanced">
                        <?php echo wp_kses_post($term_description); ?>
                    </div>
                <?php endif; ?>
                
                <div class="category-stats-enhanced">
                    <div class="stat-item">
                        <i class="fas fa-boxes"></i>
                        <span><?php echo $term_count; ?> sản phẩm</span>
                    </div>
                    <?php if (!empty($child_categories)) : ?>
                        <div class="stat-item">
                            <i class="fas fa-folder-tree"></i>
                            <span><?php echo count($child_categories); ?> danh mục con</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($child_categories)) : ?>
            <div class="category-hero-image">
                <div class="subcategories-preview">
                    <h3>Danh mục con</h3>
                    <div class="subcategories-list">
                        <?php foreach ($child_categories as $index => $cat) : if ($index >= 3) break; ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="subcat-item">
                                <span class="subcat-icon">📁</span>
                                <span class="subcat-name"><?php echo esc_html($cat->name); ?></span>
                                <span class="subcat-count">(<?php echo $cat->count; ?>)</span>
                            </a>
                        <?php endforeach; ?>
                        <?php if (count($child_categories) > 3) : ?>
                            <a href="#" class="subcat-item view-all-subcats" id="viewAllSubcatsBtn">
                                <span>+<?php echo count($child_categories) - 3; ?> danh mục khác</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Products in Category -->
<?php if ($featured_products->have_posts()) : ?>
<section class="featured-category-products">
    <div class="container">
        <div class="section-header-enhanced">
            <h2>Sản phẩm nổi bật trong <span class="highlight"><?php echo esc_html($term_name); ?></span></h2>
            <p>Những sản phẩm được yêu thích nhất từ danh mục này</p>
        </div>
        
        <div class="featured-products-slider">
            <div class="slider-container" id="featured-products-slider">
                <?php while ($featured_products->have_posts()) : $featured_products->the_post(); 
                    $product_id = get_the_ID();
                    $price_data = mypham_get_product_price($product_id);
                ?>
                    <div class="featured-product-card">
                        <div class="card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : 
                                    the_post_thumbnail('product-medium');
                                else : ?>
                                    <img src="https://placehold.co/400x400/f0f0f0/999?text=Product" alt="<?php the_title(); ?>">
                                <?php endif; ?>
                            </a>
                            <?php if ($price_data['has_sale']) : ?>
                                <div class="sale-tag">-<?php echo round(($price_data['regular'] - $price_data['sale']) / $price_data['regular'] * 100); ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="price">
                                <?php if ($price_data['current'] > 0) : ?>
                                    <?php if ($price_data['has_sale']) : ?>
                                        <span class="price-old"><?php echo mypham_format_price($price_data['regular']); ?></span>
                                        <span class="price-current"><?php echo mypham_format_price($price_data['current']); ?></span>
                                    <?php else : ?>
                                        <span class="price-current"><?php echo mypham_format_price($price_data['current']); ?></span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="price-contact">Liên hệ</span>
                                <?php endif; ?>
                            </div>
                            <button class="btn-quick-add" data-product-id="<?php echo $product_id; ?>">
                                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <button class="slider-nav prev" id="featured-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-nav next" id="featured-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Main Category Products Section -->
<section class="category-products-enhanced">
    <div class="container">
        <div class="category-layout">
            
            <!-- Sidebar Filters -->
            <aside class="category-sidebar">
                <button class="filter-toggle-btn" id="filterToggleBtn">
                    <i class="fas fa-sliders-h"></i> Lọc sản phẩm
                </button>
                
                <div class="filter-sidebar" id="filterSidebar">
                    <!-- Subcategories Widget -->
                    <?php if (!empty($child_categories)) : ?>
                    <div class="filter-widget">
                        <h3 class="filter-widget-title">
                            <i class="fas fa-list-ul"></i>
                            Danh mục con
                        </h3>
                        <div class="subcategories-list-full">
                            <?php foreach ($child_categories as $child) : ?>
                                <a href="<?php echo get_term_link($child); ?>" class="subcategory-link <?php echo is_tax('product_category', $child->slug) ? 'active' : ''; ?>">
                                    <span class="subcat-name"><?php echo esc_html($child->name); ?></span>
                                    <span class="subcat-count">(<?php echo $child->count; ?>)</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Price Filter -->
                    <div class="filter-widget">
                        <h3 class="filter-widget-title">
                            <i class="fas fa-tag"></i>
                            Lọc theo giá
                        </h3>
                        <div class="price-filter-range">
                            <div class="price-range-slider">
                                <input type="range" id="price-min" min="0" max="10000000" step="100000" value="0">
                                <input type="range" id="price-max" min="0" max="10000000" step="100000" value="10000000">
                            </div>
                            <div class="price-values">
                                <div class="price-value">
                                    <span>Min:</span>
                                    <span id="price-min-value">0₫</span>
                                </div>
                                <div class="price-value">
                                    <span>Max:</span>
                                    <span id="price-max-value">10.000.000₫</span>
                                </div>
                            </div>
                            <div class="price-inputs">
                                <input type="number" id="price-min-input" placeholder="Từ" value="0">
                                <input type="number" id="price-max-input" placeholder="Đến" value="10000000">
                            </div>
                            <button class="btn-apply-filter" id="apply-price-filter">Áp dụng</button>
                        </div>
                    </div>
                    
                    <!-- Brands Filter -->
                    <div class="filter-widget">
                        <h3 class="filter-widget-title">
                            <i class="fas fa-building"></i>
                            Thương hiệu
                        </h3>
                        <div class="brands-list">
                            <?php
                            $brands = get_terms([
                                'taxonomy' => 'brand',
                                'hide_empty' => true,
                                'number' => 10
                            ]);
                            if (!empty($brands) && !is_wp_error($brands)) :
                                foreach ($brands as $brand) :
                            ?>
                                <label class="brand-checkbox">
                                    <input type="checkbox" value="<?php echo $brand->slug; ?>" class="brand-filter">
                                    <span><?php echo esc_html($brand->name); ?></span>
                                    <span class="brand-count">(<?php echo $brand->count; ?>)</span>
                                </label>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                    
                    <button class="btn-reset-filters" id="resetFiltersBtn">
                        <i class="fas fa-undo-alt"></i> Xóa tất cả bộ lọc
                    </button>
                </div>
            </aside>
            
            <!-- Products Content -->
            <div class="category-content">
                <div class="products-controls">
                    <div class="results-info">
                        <span class="products-count" id="products-count"><?php echo $term_count; ?></span>
                        <span class="products-label">sản phẩm</span>
                    </div>
                    
                    <div class="sorting-controls">
                        <select id="sort-products" class="sort-select">
                            <option value="">Sắp xếp mặc định</option>
                            <option value="newest">Mới nhất</option>
                            <option value="price-asc">Giá: Thấp đến cao</option>
                            <option value="price-desc">Giá: Cao đến thấp</option>
                            <option value="popular">Bán chạy nhất</option>
                            <option value="rating">Đánh giá cao nhất</option>
                        </select>
                        
                        <div class="view-options">
                            <button class="view-btn grid-view active" data-view="grid">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-btn list-view" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="products-container" id="products-container">
                    <div class="products-grid" id="products-grid">
                        <?php
                        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                        $orderby = get_query_var('orderby');
                        
                        $products_args = [
                            'post_type' => 'product',
                            'posts_per_page' => 12,
                            'paged' => $paged,
                            'tax_query' => [[
                                'taxonomy' => 'product_category',
                                'field' => 'term_id',
                                'terms' => $term_id
                            ]]
                        ];
                        
                        // Apply sorting
                        if ($orderby) {
                            switch ($orderby) {
                                case 'newest':
                                    $products_args['orderby'] = 'date';
                                    $products_args['order'] = 'DESC';
                                    break;
                                case 'price-asc':
                                    $products_args['meta_key'] = '_product_price';
                                    $products_args['orderby'] = 'meta_value_num';
                                    $products_args['order'] = 'ASC';
                                    break;
                                case 'price-desc':
                                    $products_args['meta_key'] = '_product_price';
                                    $products_args['orderby'] = 'meta_value_num';
                                    $products_args['order'] = 'DESC';
                                    break;
                            }
                        }
                        
                        $products_query = new WP_Query($products_args);
                        
                        if ($products_query->have_posts()) :
                            while ($products_query->have_posts()) : $products_query->the_post();
                                $product_id = get_the_ID();
                                $price_data = mypham_get_product_price($product_id);
                        ?>
                            <div class="product-card-enhanced" data-product-id="<?php echo $product_id; ?>">
                                <?php if ($price_data['has_sale']) : ?>
                                    <div class="product-badge-sale">
                                        -<?php echo round(($price_data['regular'] - $price_data['sale']) / $price_data['regular'] * 100); ?>%
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-image-wrapper">
                                    <a href="<?php the_permalink(); ?>" class="product-link">
                                        <?php if (has_post_thumbnail()) : 
                                            the_post_thumbnail('product-medium');
                                        else : ?>
                                            <img src="https://placehold.co/400x400/f0f0f0/999?text=Product" alt="<?php the_title(); ?>">
                                        <?php endif; ?>
                                    </a>
                                    <div class="product-actions-overlay">
                                        <button class="action-btn quick-view" data-product-id="<?php echo $product_id; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn add-wishlist" data-product-id="<?php echo $product_id; ?>">
                                            <i class="far fa-heart"></i>
                                        </button>
                                        <button class="action-btn quick-add" data-product-id="<?php echo $product_id; ?>">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="product-info-enhanced">
                                    <h3 class="product-title-enhanced">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="product-rating">
                                        <div class="stars">
                                            <?php
                                            $rating = get_post_meta($product_id, '_product_rating', true) ?: 4.5;
                                            $full_stars = floor($rating);
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $full_stars) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } elseif ($i - 0.5 <= $rating) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="review-count">(<?php echo get_comments_number($product_id); ?>)</span>
                                    </div>
                                    
                                    <div class="product-price-enhanced">
                                        <?php if ($price_data['current'] > 0) : ?>
                                            <?php if ($price_data['has_sale']) : ?>
                                                <span class="price-old"><?php echo mypham_format_price($price_data['regular']); ?></span>
                                                <span class="price-current"><?php echo mypham_format_price($price_data['current']); ?></span>
                                            <?php else : ?>
                                                <span class="price-current"><?php echo mypham_format_price($price_data['current']); ?></span>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="price-contact">Liên hệ</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else :
                        ?>
                            <div class="no-products-found">
                                <i class="fas fa-box-open"></i>
                                <h3>Không tìm thấy sản phẩm</h3>
                                <p>Hãy thử chọn danh mục khác hoặc xóa bộ lọc để xem thêm sản phẩm.</p>
                            </div>
                        <?php 
                        endif;
                        ?>
                    </div>
                    
                    <?php if ($products_query->max_num_pages > 1) : ?>
                    <div class="pagination-enhanced">
                        <?php
                        echo paginate_links([
                            'total' => $products_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '<i class="fas fa-chevron-left"></i>',
                            'next_text' => '<i class="fas fa-chevron-right"></i>',
                            'type' => 'list',
                            'prev_next' => true
                        ]);
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>