<?php
get_header();
$product_id = get_the_ID();
$regular_price = get_post_meta($product_id, '_product_price', true);
$sale_price = get_post_meta($product_id, '_product_sale_price', true);
$has_sale = $sale_price && $sale_price < $regular_price;
$current_price = $has_sale ? $sale_price : $regular_price;
?>

<!-- Product Hero -->
<section class="product-hero">
    <div class="container">
        <div class="product-hero-wrapper">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else : ?>
                        <img src="https://placehold.co/600x600/f0f0f0/999?text=Product" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </div>
                <div class="thumbnail-gallery">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('thumbnail'); ?>
                    <?php endif; ?>
                    <!-- Additional thumbnails -->
                    <div class="thumbnail-placeholder">
                        <img src="https://placehold.co/100x100/f0f0f0/999?text=1" alt="Thumbnail 1">
                    </div>
                    <div class="thumbnail-placeholder">
                        <img src="https://placehold.co/100x100/f0f0f0/999?text=2" alt="Thumbnail 2">
                    </div>
                    <div class="thumbnail-placeholder">
                        <img src="https://placehold.co/100x100/f0f0f0/999?text=3" alt="Thumbnail 3">
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info-section">
                <div class="product-header">
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    
                    <?php if ($has_sale) : ?>
                        <div class="product-badge sale-badge">
                            -<?php echo round(($regular_price - $sale_price) / $regular_price * 100); ?>%
                        </div>
                    <?php endif; ?>

                    <div class="product-price">
                        <?php if ($has_price) : ?>
                            <?php if ($has_sale) : ?>
                                <span class="price-old"><?php echo mypham_format_price($regular_price); ?></span>
                                <span class="price-current"><?php echo mypham_format_price($current_price); ?></span>
                            <?php else : ?>
                                <span class="price-current"><?php echo mypham_format_price($current_price); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="product-rating">
                        <div class="stars" data-rating="4.8">
                            ★★★★☆
                        </div>
                        <span>(127 đánh giá)</span>
                    </div>
                </div>

                <div class="product-description-short">
                    <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                </div>

                <form class="product-form" method="post">
                    <div class="product-variants">
                        <div class="variant-group">
                            <label>Dung tích:</label>
                            <div class="variant-options">
                                <button type="button" class="variant-btn active" data-variant="30ml">30ml</button>
                                <button type="button" class="variant-btn" data-variant="50ml">50ml</button>
                                <button type="button" class="variant-btn" data-variant="100ml">100ml</button>
                            </div>
                        </div>
                    </div>

                    <div class="quantity-selector">
                        <label>Số lượng:</label>
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn minus">-</button>
                            <input type="number" name="quantity" value="1" min="1" class="qty-input">
                            <button type="button" class="qty-btn plus">+</button>
                        </div>
                    </div>

                    <!-- In single-product.php, update the buy now button (around line 100-110) -->
<div class="product-actions">
    <button type="button" name="add-to-cart" class="btn-add-to-cart" data-product-id="<?php echo $product_id; ?>">
        <i class="fas fa-shopping-cart"></i>
        Thêm vào giỏ hàng
    </button>
    <button type="button" name="buy-now" class="btn-buy-now" 
            data-product-id="<?php echo $product_id; ?>" 
            data-price="<?php echo $current_price; ?>">
        <i class="fas fa-bolt"></i>
        <span class="buy-now-text">Mua ngay</span>
        <span class="buy-now-price"><?php echo mypham_format_price($current_price); ?></span>
    </button>
</div>
                </form>

                <div class="product-delivery">
                    <div class="delivery-item">
                        <i class="fas fa-truck"></i>
                        <span>Miễn phí vận chuyển cho đơn từ 500.000đ</span>
                    </div>
                    <div class="delivery-item">
                        <i class="fas fa-gift"></i>
                        <span>Quà tặng khi mua hàng</span>
                    </div>
                </div>

                <div class="product-share">
                    <span>Chia sẻ:</span>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Details -->
<section class="product-details">
    <div class="container">
        <div class="tabs-wrapper">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="description">Mô tả sản phẩm</button>
                <button class="tab-btn" data-tab="info">Thông tin chi tiết</button>
                <button class="tab-btn" data-tab="reviews">Đánh giá (127)</button>
            </div>
            
            <div class="tabs-content">
                <!-- Description Tab -->
                <div class="tab-content active" id="description">
                    <?php the_content(); ?>
                </div>
                
                <!-- Info Tab -->
                <div class="tab-content" id="info">
                    <div class="product-specs">
                        <div class="spec-item">
                            <span class="spec-label">Thương hiệu:</span>
                            <span class="spec-value">SkyTech Cosmetics</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Xuất xứ:</span>
                            <span class="spec-value">Hàn Quốc</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Loại da:</span>
                            <span class="spec-value">Mọi loại da</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Dung tích:</span>
                            <span class="spec-value">30ml / 50ml / 100ml</span>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-content" id="reviews">
                    <div class="reviews-list">
                        <!-- Review items will be loaded here -->
                        <div class="review-item">
                            <div class="review-header">
                                <img src="https://placehold.co/50x50/4ecdc4/fff?text=AN" class="review-avatar" alt="Avatar">
                                <div class="review-author">
                                    <h4>Nguyễn Anh Nam</h4>
                                    <div class="review-stars">★★★★★</div>
                                    <span class="review-date">19/03/2026</span>
                                </div>
                            </div>
                            <p class="review-text">"Sản phẩm tuyệt vời, da đẹp hẳn lên sau 1 tuần sử dụng!"</p>
                        </div>
                    </div>
                    <div class="review-form">
                        <h4>Viết đánh giá của bạn</h4>
                        <form>
                            <div class="rating-selector">
                                <span>Đánh giá của bạn:</span>
                                <div class="stars-input">
                                    <i class="far fa-star" data-value="1"></i>
                                    <i class="far fa-star" data-value="2"></i>
                                    <i class="far fa-star" data-value="3"></i>
                                    <i class="far fa-star" data-value="4"></i>
                                    <i class="far fa-star" data-value="5"></i>
                                </div>
                            </div>
                            <textarea placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                            <button type="submit" class="btn-submit-review">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="related-products">
    <div class="container">
        <div class="section-header">
            <h2>Sản phẩm liên quan</h2>
            <a href="<?php echo get_post_type_archive_link('product'); ?>" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="products-grid">
            <!-- Related products WP_Query here -->
        </div>
    </div>
</section>

<?php get_footer(); ?>