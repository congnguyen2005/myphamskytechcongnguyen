<?php
/**
 * Mini Cart Template
 */
?>

<div class="mini-cart">
    <div class="mini-cart-header">
        <h3>Giỏ hàng của bạn</h3>
        <button class="close-mini-cart"><i class="fas fa-times"></i></button>
    </div>
    <div class="mini-cart-content">
        <?php echo mypham_get_mini_cart_html(); ?>
    </div>
</div>
<div class="mini-cart-overlay"></div>

<style>
.mini-cart {
    position: fixed;
    top: 0;
    right: -400px;
    width: 380px;
    height: 100%;
    background: white;
    z-index: 9999;
    box-shadow: -2px 0 10px rgba(0,0,0,0.1);
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

.mini-cart.active {
    right: 0;
}

.mini-cart-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9998;
    display: none;
}

.mini-cart-overlay.active {
    display: block;
}

.mini-cart-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ff6b6b;
    color: white;
}

.mini-cart-header h3 {
    margin: 0;
    font-size: 18px;
}

.close-mini-cart {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

.mini-cart-content {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.mini-cart-items {
    margin-bottom: 20px;
}

.mini-cart-item {
    display: flex;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    position: relative;
}

.mini-cart-item .item-image {
    width: 60px;
    height: 60px;
    overflow: hidden;
    border-radius: 8px;
}

.mini-cart-item .item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mini-cart-item .item-details {
    flex: 1;
}

.mini-cart-item .item-details h4 {
    font-size: 14px;
    margin: 0 0 5px;
}

.mini-cart-item .item-price {
    font-size: 12px;
    color: #666;
}

.mini-cart-item .item-subtotal {
    font-size: 13px;
    font-weight: bold;
    color: #ff6b6b;
    margin-top: 5px;
}

.remove-item-mini {
    position: absolute;
    top: 15px;
    right: 0;
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
}

.mini-cart-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    background: #f8f9fa;
}

.mini-cart-total {
    margin-bottom: 15px;
    font-size: 16px;
}

.mini-cart-footer .btn-view-cart,
.mini-cart-footer .btn-checkout-mini {
    display: block;
    width: 100%;
    padding: 12px;
    text-align: center;
    border-radius: 8px;
    text-decoration: none;
    margin-bottom: 10px;
}

.btn-view-cart {
    background: #f8f9fa;
    border: 1px solid #ff6b6b;
    color: #ff6b6b;
}

.btn-checkout-mini {
    background: #ff6b6b;
    color: white;
}

.mini-cart-empty {
    text-align: center;
    padding: 40px;
    color: #999;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Mở mini cart khi click vào icon giỏ hàng
    $('.cart-icon a').on('click', function(e) {
        e.preventDefault();
        $('.mini-cart, .mini-cart-overlay').addClass('active');
    });
    
    // Đóng mini cart
    $('.close-mini-cart, .mini-cart-overlay').on('click', function() {
        $('.mini-cart, .mini-cart-overlay').removeClass('active');
    });
    
    // Xóa item trong mini cart
    $(document).on('click', '.remove-item-mini', function() {
        var button = $(this);
        var productId = button.data('id');
        
        $.ajax({
            url: mypham_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'mypham_remove_from_cart',
                product_id: productId,
                nonce: mypham_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
});
</script>