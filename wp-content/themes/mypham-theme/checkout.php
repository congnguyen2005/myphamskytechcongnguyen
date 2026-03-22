<?php
/**
 * Template Name: Checkout Page
 * @package MyPhamTheme
 */

get_header();

if (!session_id()) {
    session_start();
}

// Enqueue checkout assets
mypham_enqueue_checkout_assets();

$cart_total = mypham_get_cart_total();
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$shipping_fee = $cart_total >= 500000 ? 0 : 30000;
$final_total = $cart_total + $shipping_fee;
?>

<div class="checkout-page">
    <div class="container">
        <div class="page-header-breadcrumb">
            <h1 class="page-title">Thanh toán</h1>
        </div>

        <?php if (!empty($cart_items)) : ?>
            <div class="checkout-wrapper" data-cart-total="<?php echo esc_attr($cart_total); ?>" data-shipping-threshold="500000">
                <div class="checkout-form">
                    <form id="checkout-form" class="checkout-form-container">
                        <div class="form-group">
                            <label>Họ & Tên <span class="required">*</span></label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Điện thoại <span class="required">*</span></label>
                            <input type="text" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ giao hàng <span class="required">*</span></label>
                            <textarea name="address" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Phương thức thanh toán</label>
                            <select name="payment_method" id="payment-method">
                                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                <option value="momo">Ví MoMo</option>
                                <option value="zalopay">ZaloPay</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" rows="2" placeholder="Ghi chú về đơn hàng..."></textarea>
                        </div>

                        <div class="payment-details" id="payment-details" style="display: none;">
                            <!-- Payment method details will be inserted here -->
                        </div>

                        <button class="btn btn-primary btn-checkout-submit" type="submit">
                            <i class="fas fa-check-circle"></i> Đặt hàng
                        </button>
                    </form>
                </div>

                <div class="order-summary">
                    <h3>Đơn hàng của bạn</h3>
                    
                    <div class="order-items">
                        <?php foreach ($cart_items as $pid => $qty) :
                            $product = get_post($pid);
                            $price_data = mypham_get_product_price($pid);
                            $subtotal = $price_data['current'] * $qty;
                        ?>
                            <div class="order-item" data-product-id="<?php echo esc_attr($pid); ?>">
                                <div class="order-item-info">
                                    <span class="item-name"><?php echo esc_html($product->post_title); ?></span>
                                    <span class="item-qty">x <?php echo $qty; ?></span>
                                </div>
                                <div class="order-item-price">
                                    <?php echo mypham_format_price($subtotal); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-total">
                        <div class="total-row">
                            <span>Tạm tính</span>
                            <span class="cart-subtotal"><?php echo mypham_format_price($cart_total); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Phí vận chuyển</span>
                            <span class="shipping-fee">
                                <?php echo mypham_format_price($shipping_fee); ?>
                            </span>
                        </div>
                        <?php if ($cart_total < 500000) : ?>
                            <div class="total-row shipping-note">
                                <small><i class="fas fa-info-circle"></i> Miễn phí vận chuyển cho đơn từ 500.000đ</small>
                            </div>
                        <?php endif; ?>
                        <div class="total-row grand-total">
                            <strong>Tổng cộng</strong>
                            <strong class="grand-total-amount">
                                <?php echo mypham_format_price($final_total); ?>
                            </strong>
                        </div>
                    </div>

                    <div class="payment-info">
                        <div class="payment-methods-icons">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/visa.png" alt="Visa">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/mastercard.png" alt="Mastercard">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/momo.png" alt="MoMo">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/zalopay.png" alt="ZaloPay">
                        </div>
                        <p class="secure-payment">
                            <i class="fas fa-lock"></i> Thanh toán an toàn và bảo mật
                        </p>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-basket fa-3x"></i>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục thanh toán</p>
                <a href="<?php echo home_url('/products'); ?>" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Tiếp tục mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>