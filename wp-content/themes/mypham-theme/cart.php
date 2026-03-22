<?php
/**
 * Template Name: Trang giỏ hàng
 * Template Post Type: page
 * @package MyPhamTheme
 */

get_header();

if (!session_id()) { session_start(); }

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
?>

<div class="cart-page">
    <div class="container">
        <div class="page-header-breadcrumb">
            <h1 class="page-title">Giỏ hàng của bạn</h1>
        </div>

        <div class="cart-content">
            <div class="cart-items">
                <?php if (!empty($cart_items)) : ?>
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tạm tính</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $pid => $qty) :
                                    $price_data = mypham_get_product_price($pid);
                                    $subtotal = $price_data['current'] * $qty;
                                    $product = get_post($pid);
                                ?>
                                    <tr class="cart-item" data-product-id="<?php echo $pid; ?>">
                                        <td class="product-name">
                                            <div class="product-thumbnail">
                                                <?php echo get_the_post_thumbnail($pid, 'thumbnail'); ?>
                                            </div>
                                            <div class="product-title"><a href="<?php echo get_permalink($pid); ?>"><?php echo esc_html($product->post_title); ?></a></div>
                                        </td>
                                        <td class="product-price"><?php echo mypham_format_price($price_data['current']); ?></td>
                                        <td class="quantity-selector">
                                            <button class="qty-minus">-</button>
                                            <input type="number" class="qty-input" value="<?php echo $qty; ?>" min="1">
                                            <button class="qty-plus">+</button>
                                        </td>
                                        <td class="product-subtotal"><?php echo mypham_format_price($subtotal); ?></td>
                                        <td class="product-remove"><button class="remove-item" data-product-id="<?php echo $pid; ?>">Xóa</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-basket fa-3x"></i>
                        <h3>Giỏ hàng của bạn đang trống</h3>
                        <a href="<?php echo home_url('/products'); ?>" class="btn btn-primary">Tiếp tục mua sắm</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cart-summary">
                <h3>Tổng đơn hàng</h3>
                <div class="summary-row">
                    <span>Tổng phụ:</span>
                    <span class="cart-subtotal"><?php echo mypham_format_price(mypham_get_cart_total()); ?></span>
                </div>
                <div class="summary-row total">
                    <strong>Tổng cộng:</strong>
                    <strong class="cart-total"><?php echo mypham_format_price(mypham_get_cart_total()); ?></strong>
                </div>
                <div class="cart-actions">
                    <a href="<?php echo home_url('/thanh-toan'); ?>" class="btn btn-primary">Đi tới thanh toán</a>
                    <button id="clear-cart" class="btn btn-outline">Xóa giỏ hàng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cart-page { padding: 100px 0; }
.cart-table { width: 100%; border-collapse: collapse; }
.cart-table th, .cart-table td { padding: 12px; border-bottom: 1px solid #eee; }
.cart-summary { background: #fff; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); }
.quantity-selector { display: flex; gcart.phpap: 8px; align-items: center; }
.quantity-selector input { width: 60px; padding: 6px; }
.empty-cart { text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px; }
</style>

<script>
jQuery(document).ready(function($){
    // Remove item
    $(document).on('click', '.remove-item', function(e){
        e.preventDefault();
        var pid = $(this).data('product-id');
        $.post(MyPhamData.ajax_url, { action: 'mypham_remove_from_cart', product_id: pid }, function(res){ if (res.success) location.reload(); });
    });

    // Clear cart
    $('#clear-cart').on('click', function(e){ e.preventDefault(); $.post(MyPhamData.ajax_url, { action: 'mypham_clear_cart' }, function(){ location.reload(); }); });

    // Quantity change
    $(document).on('change', '.qty-input', function(){
        var $tr = $(this).closest('.cart-item');
        var pid = $tr.data('product-id');
        var qty = parseInt($(this).val() || 1, 10);
        $.post(MyPhamData.ajax_url, { action: 'mypham_update_cart', product_id: pid, quantity: qty }, function(res){ if (res.success) location.reload(); });
    });
});
</script>

<?php get_footer(); ?>