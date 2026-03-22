<?php
/**
 * Template Name: Wishlist Page
 * @package MyPhamTheme
 */

get_header();

if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

$wishlist = mypham_get_wishlist();
?>

<div class="wishlist-page">
    <div class="container">
        <div class="page-header-breadcrumb">
            <h1 class="page-title">Danh sách yêu thích</h1>
        </div>

        <div class="wishlist-content">
            <?php if (!empty($wishlist)) : ?>
                <div class="wishlist-grid">
                    <?php foreach ($wishlist as $product_id) : 
                        $product = get_post($product_id);
                        if (!$product) continue;
                        
                        $price_data = mypham_get_product_price($product_id);
                        $has_sale = $price_data['has_sale'];
                        $current_price = $price_data['current'];
                    ?>
                        <div class="wishlist-item" data-product-id="<?php echo $product_id; ?>">
                            <div class="wishlist-item-image">
                                <a href="<?php echo get_permalink($product_id); ?>">
                                    <?php if (has_post_thumbnail($product_id)) : ?>
                                        <?php echo get_the_post_thumbnail($product_id, 'medium'); ?>
                                    <?php else : ?>
                                        <img src="https://placehold.co/300x300/f0f0f0/999?text=Product" alt="<?php echo esc_attr($product->post_title); ?>">
                                    <?php endif; ?>
                                </a>
                                <button class="remove-wishlist" data-product-id="<?php echo $product_id; ?>">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="wishlist-item-info">
                                <h3>
                                    <a href="<?php echo get_permalink($product_id); ?>">
                                        <?php echo esc_html($product->post_title); ?>
                                    </a>
                                </h3>
                                <div class="product-price">
                                    <?php if ($current_price > 0) : ?>
                                        <?php if ($has_sale) : ?>
                                            <span class="price-old"><?php echo mypham_format_price($price_data['regular']); ?></span>
                                            <span class="price-current"><?php echo mypham_format_price($current_price); ?></span>
                                        <?php else : ?>
                                            <span class="price-current"><?php echo mypham_format_price($current_price); ?></span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="price-contact">Liên hệ</span>
                                    <?php endif; ?>
                                </div>
                                <div class="wishlist-item-actions">
                                    <button class="btn-add-to-cart-wishlist" data-product-id="<?php echo $product_id; ?>">
                                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                    </button>
                                    <button class="btn-buy-now-wishlist" data-product-id="<?php echo $product_id; ?>" data-price="<?php echo $current_price; ?>">
                                        <i class="fas fa-bolt"></i> Mua ngay
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-wishlist">
                    <i class="far fa-heart fa-4x"></i>
                    <h3>Danh sách yêu thích trống</h3>
                    <p>Hãy thêm sản phẩm vào danh sách yêu thích để dễ dàng tìm lại sau.</p>
                    <a href="<?php echo get_post_type_archive_link('product'); ?>" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> Khám phá sản phẩm
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.wishlist-page {
    padding: 100px 0;
    min-height: 60vh;
}

.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.wishlist-item {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.wishlist-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.wishlist-item-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.wishlist-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.wishlist-item:hover .wishlist-item-image img {
    transform: scale(1.05);
}

.remove-wishlist {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 32px;
    height: 32px;
    background: rgba(0,0,0,0.6);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-wishlist:hover {
    background: #ff6b6b;
    transform: scale(1.1);
}

.wishlist-item-info {
    padding: 20px;
}

.wishlist-item-info h3 {
    font-size: 1rem;
    margin-bottom: 10px;
    line-height: 1.4;
}

.wishlist-item-info h3 a {
    color: #2d3748;
    text-decoration: none;
    transition: color 0.3s;
}

.wishlist-item-info h3 a:hover {
    color: #ff6b6b;
}

.product-price {
    margin-bottom: 15px;
}

.price-old {
    text-decoration: line-through;
    color: #a0aec0;
    font-size: 0.85rem;
    margin-right: 8px;
}

.price-current {
    font-size: 1.2rem;
    font-weight: 700;
    color: #ff6b6b;
}

.price-contact {
    color: #4ecdc4;
    font-weight: 600;
}

.wishlist-item-actions {
    display: flex;
    gap: 10px;
}

.wishlist-item-actions button {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.btn-add-to-cart-wishlist {
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    color: #2d3748;
}

.btn-add-to-cart-wishlist:hover {
    background: #4ecdc4;
    color: white;
    border-color: #4ecdc4;
}

.btn-buy-now-wishlist {
    background: linear-gradient(135deg, #ff6b6b, #ff5252);
    color: white;
}

.btn-buy-now-wishlist:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,107,107,0.4);
}

.empty-wishlist {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 20px;
}

.empty-wishlist i {
    color: #cbd5e0;
    margin-bottom: 20px;
    display: inline-block;
}

.empty-wishlist h3 {
    margin-bottom: 10px;
    color: #2d3748;
}

.empty-wishlist p {
    color: #718096;
    margin-bottom: 25px;
}

@media (max-width: 768px) {
    .wishlist-page {
        padding: 60px 0;
    }
    
    .wishlist-grid {
        grid-template-columns: 1fr;
    }
    
    .wishlist-item-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Remove from wishlist
    $('.remove-wishlist').on('click', function() {
        var $btn = $(this);
        var productId = $btn.data('product-id');
        
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_add_to_wishlist',
            product_id: productId
        }, function(response) {
            if (response.success) {
                $btn.closest('.wishlist-item').fadeOut(300, function() {
                    $(this).remove();
                    if ($('.wishlist-item').length === 0) {
                        location.reload();
                    }
                });
                showNotification('Đã xóa khỏi danh sách yêu thích', 'info');
            }
        });
    });
    
    // Add to cart from wishlist
    $('.btn-add-to-cart-wishlist').on('click', function() {
        var $btn = $(this);
        var productId = $btn.data('product-id');
        var originalText = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...').prop('disabled', true);
        
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_add_to_cart',
            product_id: productId,
            quantity: 1
        }, function(response) {
            if (response.success) {
                $btn.html('<i class="fas fa-check"></i> Đã thêm');
                showNotification('Đã thêm vào giỏ hàng!', 'success');
                $('.cart-count').text(response.data.cart_count);
                setTimeout(function() {
                    $btn.html(originalText).prop('disabled', false);
                }, 1500);
            } else {
                $btn.html(originalText).prop('disabled', false);
                showNotification(response.data.message || 'Có lỗi xảy ra', 'error');
            }
        }).fail(function() {
            $btn.html(originalText).prop('disabled', false);
            showNotification('Lỗi kết nối', 'error');
        });
    });
    
    // Buy now from wishlist
    $('.btn-buy-now-wishlist').on('click', function() {
        var $btn = $(this);
        var productId = $btn.data('product-id');
        
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);
        
        $.post(MyPhamData.ajax_url, { action: 'mypham_clear_cart' }, function() {
            $.post(MyPhamData.ajax_url, {
                action: 'mypham_add_to_cart',
                product_id: productId,
                quantity: 1
            }, function(response) {
                if (response.success) {
                    window.location.href = MyPhamData.checkout_url;
                } else {
                    $btn.html('<i class="fas fa-bolt"></i> Mua ngay').prop('disabled', false);
                    showNotification(response.data.message || 'Có lỗi xảy ra', 'error');
                }
            });
        });
    });
    
    function showNotification(message, type) {
        var notification = $('<div class="wishlist-notification ' + type + '">' + message + '</div>');
        $('body').append(notification);
        setTimeout(function() { notification.addClass('show'); }, 10);
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() { notification.remove(); }, 300);
        }, 3000);
    }
    
    // Add notification styles
    var style = $('<style>.wishlist-notification{position:fixed;bottom:20px;right:20px;padding:12px 20px;border-radius:12px;background:#333;color:white;z-index:9999;transform:translateX(400px);transition:transform 0.3s ease;}.wishlist-notification.show{transform:translateX(0)}.wishlist-notification.success{background:#10b981}.wishlist-notification.error{background:#ef4444}.wishlist-notification.info{background:#3b82f6}</style>');
    $('head').append(style);
});
</script>

<?php get_footer(); ?>