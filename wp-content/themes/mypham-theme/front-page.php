    <?php
    /**
     * Front Page Template
     * @package MyPhamTheme
     */

    get_header();
    ?>

<section class="hero-slider">
    <div class="slider">
        <div class="slide">
            <div class="container hero-flex">
                <div class="slide-content">
                    <span class="slide-badge">🔥 Deal sốc hôm nay</span>
                    <h1>Mỹ phẩm chính hãng <br><span class="highlight">Giảm đến 50%</span></h1>
                    <p>Hàng ngàn sản phẩm đang chờ bạn khám phá</p>
                    <div class="hero-buttons">
                        <a href="<?php echo home_url('/products'); ?>" class="btn btn-primary">Mua ngay</a>
                        <a href="<?php echo home_url('/products'); ?>" class="btn btn-outline">Xem sản phẩm</a>
                    </div>
                </div>
                <div class="slide-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero-cosmetic-new.png" 
                         alt="Mỹ phẩm chính hãng"
                         onerror="this.src='https://vn4u.vn/wp-content/uploads/2023/09/nhung-bai-viet-hay-ve-my-pham.jpg'">
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-truck-fast"></i>
                    <h3>Miễn phí vận chuyển</h3>
                    <p>Cho đơn hàng từ 500.000đ</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-undo-alt"></i>
                    <h3>Đổi trả dễ dàng</h3>
                    <p>Trong vòng 7 ngày</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Hàng chính hãng</h3>
                    <p>Cam kết 100%</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h3>Hỗ trợ 24/7</h3>
                    <p>Tư vấn miễn phí</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories-section">
        <div class="container">
          
            <div class="categories-grid">
                <?php
                $categories = get_terms([
                    'taxonomy' => 'product_category',
                    'hide_empty' => false,
                    'number' => 8
                ]);
                
                if (!empty($categories) && !is_wp_error($categories)) :
                    foreach ($categories as $category) :
                ?>
                <a href="<?php echo get_term_link($category); ?>" class="category-card">
        <div class="icon"><i class="fas fa-box"></i></div>
        <h3><?php echo esc_html($category->name); ?></h3>
        <span><?php echo $category->count; ?> sản phẩm</span>
    </a>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2>Sản phẩm nổi bật</h2>
                <p>Những sản phẩm được yêu thích nhất</p>
            </div>
            <div class="products-grid">
                <?php
                $featured = new WP_Query([
                    'post_type' => 'product',
                    'posts_per_page' => 8,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                
                if ($featured->have_posts()) :
                    while ($featured->have_posts()) : $featured->the_post();
                        $product_id = get_the_ID();
                        $price_data = mypham_get_product_price($product_id);
                        $has_sale = $price_data['has_sale'];
                ?>
                        <!-- Trong front-page.php, cập nhật phần product card -->
                        <div class="product-card">
                            <?php if ($has_sale) : ?>
                                <div class="product-badge sale">
                                    -<?php echo round(($price_data['regular'] - $price_data['sale']) / $price_data['regular'] * 100); ?>%
                                </div>
                            <?php endif; ?>
                            <div class="product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('product-medium'); ?>
                                    <?php else : ?>
                                        <img src="https://placehold.co/400x400/f0f0f0/999?text=No+Image" alt="<?php the_title(); ?>">
                                    <?php endif; ?>
                                </a>
                                <button class="quick-add" data-product-id="<?php echo $product_id; ?>" data-original-text="Thêm vào giỏ">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                            <div class="product-info">
                                <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
                                <div class="product-price">
                                    <?php if ($price_data['current'] > 0) : ?>
                                        <?php if ($has_sale) : ?>
                                            <span class="price-old"><?php echo mypham_format_price($price_data['regular']); ?></span>
                                            <span class="price-current"><?php echo mypham_format_price($price_data['current']); ?></span>
                                        <?php else : ?>
                                            <span class="price-current"><?php echo mypham_format_price($price_data['current']); ?></span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="price-contact">Liên hệ</span>
                                    <?php endif; ?>
                                </div>
                                <!-- Trong phần sản phẩm nổi bật, thay thế nút Mua ngay -->
                                <button class="btn-buy-now-small" 
                                    data-product-id="<?php echo $product_id; ?>" 
                                    data-price="<?php echo $price_data['current']; ?>"
                                    data-quantity="1">
                                    <i class="fas fa-bolt"></i> Mua ngay
                                </button>
                            </div>
                        </div>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <div class="section-footer">
                <a href="<?php echo home_url('/products'); ?>" class="btn btn-outline">Xem tất cả sản phẩm <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Banner -->
    <section class="banner-section">
        <div class="container">
            <div class="banner-box">
                <div class="banner-content">
                    <span class="banner-badge">Giảm giá lên đến 50%</span>
                    <h2>Ưu đãi cuối tuần</h2>
                    <p>Đặt hàng ngay để nhận ưu đãi hấp dẫn</p>
                    <a href="<?php echo home_url('/products'); ?>" class="btn btn-primary">Mua ngay <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="banner-image">
                    <i class="fas fa-gift"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section class="news-section">
        <div class="container">
            <div class="section-header">
                <h2>Bài viết mới</h2>
                <p>Cập nhật kiến thức làm đẹp</p>
            </div>
            <div class="news-grid">
                <?php
                $news = new WP_Query([
                    'post_type' => 'news',
                    'posts_per_page' => 3
                ]);
                
                if ($news->have_posts()) :
                    while ($news->have_posts()) : $news->the_post();
                ?>
                        <article class="news-card">
                            <div class="news-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <img src="https://placehold.co/400x300/f0f0f0/999?text=News" alt="<?php the_title(); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="news-info">
                                <div class="news-meta">
                                    <span><i class="far fa-calendar-alt"></i> <?php echo get_the_date('d/m/Y'); ?></span>
                                    <span><i class="far fa-user"></i> <?php echo get_the_author(); ?></span>
                                </div>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
                                <a href="<?php the_permalink(); ?>" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>

    <?php get_footer(); ?>
<script>
function handleBuyNowFromHome(button) {
    var productId = button.getAttribute('data-product-id');
    var quantity = 1;
    
    if (!productId) {
        alert('Lỗi: Không tìm thấy sản phẩm');
        return;
    }
    
    // Hiển thị loading
    var originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    button.disabled = true;
    
    // Gọi AJAX
    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
        action: 'mypham_clear_cart'
    }, function() {
        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'mypham_add_to_cart',
            product_id: productId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                // Chuyển hướng đến trang thanh toán
                window.location.href = '<?php echo home_url('/thanh-toan'); ?>';
            } else {
                alert(response.data.message || 'Có lỗi xảy ra');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }).fail(function() {
            alert('Lỗi kết nối');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
}
</script>