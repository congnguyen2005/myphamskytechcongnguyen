jQuery(document).ready(function($) {
    console.log('MyPham JS loaded');
     console.log('Checkout URL:', MyPhamData.checkout_url);
    console.log('AJAX URL:', MyPhamData.ajax_url);
    
    // Kiểm tra nút Mua ngay
    $('.btn-buy-now, .btn-buy-now-small').each(function() {
        console.log('Found buy button:', $(this).data('product-id'));
    });
    // ==================== GIỎ HÀNG ====================
    // Thêm vào giỏ hàng
    function addToCart(productId, quantity, callback) {
        quantity = quantity || 1;
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_add_to_cart',
            product_id: productId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                updateCartCount(response.data.cart_count);
                showNotification('Đã thêm vào giỏ hàng!', 'success');
                refreshMiniCart();
                if (callback) callback(true);
            } else {
                showNotification(response.data.message || 'Có lỗi xảy ra', 'error');
                if (callback) callback(false);
            }
        }).fail(function() {
            showNotification('Lỗi kết nối', 'error');
            if (callback) callback(false);
        });
    }

    // Xóa khỏi giỏ hàng
    function removeFromCart(productId) {
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_remove_from_cart',
            product_id: productId
        }, function(response) {
            if (response.success) {
                updateCartCount(response.data.cart_count);
                refreshMiniCart();
                showNotification('Đã xóa sản phẩm', 'success');
                if (window.location.pathname.includes('/cart')) {
                    location.reload();
                }
            }
        });
    }

    // Cập nhật số lượng
    function updateCartQuantity(productId, quantity) {
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_update_cart',
            product_id: productId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                updateCartCount(response.data.cart_count);
                refreshMiniCart();
                if (window.location.pathname.includes('/cart')) {
                    location.reload();
                }
            }
        });
    }

    // Xóa toàn bộ giỏ hàng
    function clearCart() {
        if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
            $.post(MyPhamData.ajax_url, {
                action: 'mypham_clear_cart'
            }, function(response) {
                if (response.success) {
                    updateCartCount(0);
                    refreshMiniCart();
                    if (window.location.pathname.includes('/cart')) {
                        location.reload();
                    }
                }
            });
        }
    }

    // Cập nhật số lượng hiển thị
    function updateCartCount(count) {
        $('.cart-count, .cart-count-number').text(count);
    }

    // Refresh mini cart
    function refreshMiniCart() {
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_get_mini_cart'
        }, function(html) {
            $('.mini-cart .mini-cart-content').html(html);
        });
    }

    // Hiển thị thông báo
    function showNotification(message, type) {
        var $notify = $('<div class="notification ' + type + '">' + message + '</div>');
        $('body').append($notify);
        $notify.addClass('show');
        setTimeout(function() {
            $notify.removeClass('show');
            setTimeout(function() { $notify.remove(); }, 300);
        }, 3000);
    }
    
// Xử lý nút Mua ngay (quan trọng)
$(document).on('click', '.btn-buy-now, .btn-buy-now-small', function(e) {
    e.preventDefault();

    var $btn = $(this);
    var productId = $btn.data('product-id');
    var quantity = parseInt($btn.data('quantity'), 10) || 1;

    if (!productId) {
        console.error('Không tìm thấy product ID');
        showNotification('Lỗi sản phẩm', 'error');
        return;
    }

    // Lấy số lượng từ ô input nếu có (trang chi tiết sản phẩm)
    var $qtyInput = $btn.closest('.product-info, .product-card').find('.qty-input');
    if ($qtyInput.length) {
        quantity = parseInt($qtyInput.val(), 10) || 1;
    }

    console.log('Mua ngay:', productId, 'SL:', quantity);

    var originalHTML = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);

    // Xóa giỏ hàng hiện tại
    $.post(MyPhamData.ajax_url, { 
        action: 'mypham_clear_cart' 
    }, function() {
        // Thêm sản phẩm mới vào giỏ
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_add_to_cart',
            product_id: productId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                showNotification('Đã thêm vào giỏ hàng, chuyển đến thanh toán...', 'success');
                
                // Cập nhật số lượng giỏ hàng
                if (typeof updateCartCount === 'function') {
                    updateCartCount(response.data.cart_count);
                }
                if (typeof refreshMiniCart === 'function') {
                    refreshMiniCart();
                }
                
                // Chuyển đến trang thanh toán sau 0.5 giây
                setTimeout(function() {
                    window.location.href = MyPhamData.checkout_url;
                }, 500);
            } else {
                showNotification(response.data.message || 'Có lỗi xảy ra', 'error');
                $btn.html(originalHTML).prop('disabled', false);
            }
        }).fail(function() {
            showNotification('Lỗi kết nối', 'error');
            $btn.html(originalHTML).prop('disabled', false);
        });
    });
});

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + '₫';
}

function showNotification(message, type) {
    var notification = $('<div class="notification ' + type + '">' + message + '</div>');
    $('body').append(notification);
    setTimeout(function() { notification.addClass('show'); }, 10);
    setTimeout(function() {
        notification.removeClass('show');
        setTimeout(function() { notification.remove(); }, 300);
    }, 3000);
}
// ==================== XỬ LÝ NÚT MUA NGAY (QUAN TRỌNG) ====================
// Xử lý cho tất cả các nút có class btn-buy-now và btn-buy-now-small
$(document).on('click', '.btn-buy-now, .btn-buy-now-small', function(e) {
    e.preventDefault();

    var $btn = $(this);
    var productId = $btn.data('product-id');
    var price = $btn.data('price') || 0;
    var quantity = parseInt($btn.data('quantity'), 10) || 1;

    if (!productId) {
        console.error('Không có product ID');
        showNotification('Lỗi sản phẩm', 'error');
        return;
    }

    // Lấy số lượng từ ô input nếu có
    var $qtyInput = $btn.closest('.product-info, .product-card').find('.qty-input');
    if ($qtyInput.length) {
        quantity = parseInt($qtyInput.val(), 10) || 1;
    }

    console.log('Mua ngay:', productId, 'SL:', quantity, 'Giá:', price);

    var originalHTML = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);

    // Xóa giỏ hàng hiện tại
    $.post(MyPhamData.ajax_url, { 
        action: 'mypham_clear_cart' 
    }, function() {
        // Sau đó thêm sản phẩm đã chọn
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_add_to_cart',
            product_id: productId,
            quantity: quantity
        }, function(response) {
            if (response.success) {
                showNotification('Đã thêm vào giỏ hàng, chuyển đến thanh toán...', 'success');
                // Cập nhật số lượng giỏ hàng
                updateCartCount(response.data.cart_count);
                refreshMiniCart();
                
                // Chuyển hướng đến trang thanh toán sau 0.5 giây
                setTimeout(function() {
                    window.location.href = MyPhamData.checkout_url;
                }, 500);
            } else {
                showNotification(response.data.message || 'Có lỗi xảy ra', 'error');
                $btn.html(originalHTML).prop('disabled', false);
            }
        }).fail(function() {
            showNotification('Lỗi kết nối', 'error');
            $btn.html(originalHTML).prop('disabled', false);
        });
    });
});


    // Xử lý nút "Thêm vào giỏ"
    $(document).on('click', '.quick-add, .btn-add-to-cart', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        
        if (productId) {
            var $btn = $(this);
            var originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...').prop('disabled', true);
            
            addToCart(productId, 1, function(success) {
                $btn.html(originalText).prop('disabled', false);
                if (success) {
                    $btn.addClass('added');
                    setTimeout(function() { $btn.removeClass('added'); }, 1000);
                }
            });
        }
    });

    // Xóa item trong mini cart
    $(document).on('click', '.remove-item-mini', function(e) {
        e.preventDefault();
        var productId = $(this).data('id');
        if (productId) {
            removeFromCart(productId);
        }
    });

    // Xóa item trên trang cart
    $(document).on('click', '.remove-item', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        if (productId) {
            removeFromCart(productId);
        }
    });

    // Xóa toàn bộ giỏ hàng
    $(document).on('click', '#clear-cart', function(e) {
        e.preventDefault();
        clearCart();
    });

    // Cập nhật số lượng trên trang cart
    $(document).on('change', '.qty-input', function() {
        var $row = $(this).closest('.cart-item');
        var productId = $row.data('product-id');
        var quantity = parseInt($(this).val(), 10);
        if (productId && quantity > 0) {
            updateCartQuantity(productId, quantity);
        }
    });

    // Nút +/- số lượng
    $(document).on('click', '.qty-plus, .qty-minus', function(e) {
        e.preventDefault();
        var $input = $(this).siblings('.qty-input');
        var val = parseInt($input.val(), 10) || 1;
        if ($(this).hasClass('qty-plus')) {
            $input.val(val + 1);
        } else {
            $input.val(Math.max(1, val - 1));
        }
        $input.trigger('change');
    });

    // ==================== MINI CART TOGGLE ====================
    $('.cart-icon a, .mini-cart-toggle').on('click', function(e) {
        e.preventDefault();
        $('.mini-cart').addClass('active');
        $('.mini-cart-overlay').addClass('active');
        refreshMiniCart();
    });

    $('.close-mini-cart, .mini-cart-overlay').on('click', function() {
        $('.mini-cart').removeClass('active');
        $('.mini-cart-overlay').removeClass('active');
    });

    // ==================== MOBILE MENU ====================
    $('.mobile-menu-toggle').on('click', function(e) {
        e.preventDefault();
        $('.mobile-menu').addClass('active');
        $('.mobile-menu-overlay').addClass('active');
        $('body').addClass('mobile-menu-open');
    });

    $('.mobile-menu-close, .mobile-menu-overlay').on('click', function() {
        $('.mobile-menu').removeClass('active');
        $('.mobile-menu-overlay').removeClass('active');
        $('body').removeClass('mobile-menu-open');
    });

    // ==================== SLIDER ====================
    if ($('.slider').length && $.fn.slick) {
        $('.slider').slick({
            dots: true,
            arrows: true,
            infinite: true,
            autoplay: true,
            autoplaySpeed: 5000,
            speed: 600,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
            responsive: [
                { breakpoint: 768, settings: { arrows: false, dots: true } }
            ]
        });
    }

    // ==================== HEADER PADDING ====================
    function updateHeaderPadding() {
        var header = document.querySelector('.site-header');
        if (header) {
            document.body.style.paddingTop = header.offsetHeight + 'px';
        }
    }
    updateHeaderPadding();
    $(window).on('resize', updateHeaderPadding);

    // ==================== WISHLIST ====================
    $(document).on('click', '.btn-wishlist', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var productId = $btn.data('product-id');
        
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_add_to_wishlist',
            product_id: productId
        }, function(response) {
            if (response.success) {
                if (response.data.added) {
                    $btn.addClass('active');
                    showNotification('Đã thêm vào yêu thích', 'success');
                } else {
                    $btn.removeClass('active');
                    showNotification('Đã xóa khỏi yêu thích', 'info');
                }
            } else {
                showNotification(response.data || 'Vui lòng đăng nhập', 'error');
            }
        });
    });

    // ==================== REVIEW ====================
    $(document).on('submit', '#review-form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var data = $form.serializeArray();
        data.push({ name: 'action', value: 'mypham_add_review' });
        
        $.post(MyPhamData.ajax_url, data, function(response) {
            if (response.success) {
                showNotification(response.data, 'success');
                $form.trigger('reset');
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                showNotification(response.data, 'error');
            }
        });
    });

    // ==================== LIVE SEARCH ====================
    var searchTimer;
    $('.search-box input[name="s"], .search-form input[name="s"]').on('input', function() {
        var $input = $(this);
        var q = $input.val();
        clearTimeout(searchTimer);
        
        if (q.length < 2) {
            $('.search-suggestions').remove();
            return;
        }
        
        searchTimer = setTimeout(function() {
            $.get(MyPhamData.ajax_url, {
                action: 'mypham_live_search',
                q: q
            }, function(results) {
                var $container = $input.closest('.search-box, .sidebar-widget');
                $('.search-suggestions').remove();
                
                if (results && results.length) {
                    var html = '<div class="search-suggestions">';
                    $.each(results, function(i, item) {
                        html += '<a href="' + item.permalink + '" class="suggest-item">' +
                            (item.thumb ? '<img src="' + item.thumb + '" alt="">' : '') +
                            '<span>' + item.title + '</span>' +
                            '</a>';
                    });
                    html += '</div>';
                    $container.append(html);
                }
            });
        }, 300);
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box, .search-form').length) {
            $('.search-suggestions').remove();
        }
    });

    // ==================== TABS (Account/Login) ====================
    $('.tab-btn').on('click', function() {
        var tab = $(this).data('tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.auth-form').removeClass('active');
        $('#' + tab + '-form').addClass('active');
    });

    $('.account-menu li').on('click', function() {
        var tab = $(this).data('tab');
        $('.account-menu li').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tab + '-tab').addClass('active');
    });

    // ==================== REGISTER AJAX ====================
    $('#register-form-ajax').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var password = $('#reg_password').val();
        var confirm = $('#reg_confirm_password').val();
        
        if (password !== confirm) {
            showNotification('Mật khẩu xác nhận không khớp', 'error');
            return;
        }
        
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_register_user',
            username: $('#reg_username').val(),
            email: $('#reg_email').val(),
            password: password,
            phone: $('#reg_phone').val()
        }, function(response) {
            if (response.success) {
                showNotification('Đăng ký thành công! Vui lòng đăng nhập.', 'success');
                $('.tab-btn[data-tab="login"]').click();
                $form.trigger('reset');
            } else {
                showNotification(response.data, 'error');
            }
        });
    });

    // ==================== UPDATE PROFILE ====================
    $('#update-profile-form').on('submit', function(e) {
        e.preventDefault();
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_update_profile',
            display_name: $('#display_name').val(),
            phone: $('#phone').val(),
            address: $('#address').val()
        }, function(response) {
            if (response.success) {
                showNotification('Cập nhật thành công', 'success');
            } else {
                showNotification(response.data, 'error');
            }
        });
    });

    // ==================== CHANGE PASSWORD ====================
    $('#change-password-form').on('submit', function(e) {
        e.preventDefault();
        var newPass = $('#new_password').val();
        var confirmPass = $('#confirm_password').val();
        
        if (newPass !== confirmPass) {
            showNotification('Mật khẩu xác nhận không khớp', 'error');
            return;
        }
        
        $.post(MyPhamData.ajax_url, {
            action: 'mypham_change_password',
            current_password: $('#current_password').val(),
            new_password: newPass
        }, function(response) {
            if (response.success) {
                showNotification('Đổi mật khẩu thành công', 'success');
                $('#change-password-form').trigger('reset');
            } else {
                showNotification(response.data, 'error');
            }
        });
    });

    // ==================== VIEW ORDER DETAIL ====================
    $(document).on('click', '.btn-view-order', function() {
        var order = $(this).data('order');
        if (order) {
            var items = typeof order.items === 'string' ? JSON.parse(order.items) : order.items;
            var html = '<div class="order-detail">' +
                '<p><strong>Mã đơn hàng:</strong> ' + order.order_number + '</p>' +
                '<p><strong>Ngày đặt:</strong> ' + order.created_at + '</p>' +
                '<p><strong>Khách hàng:</strong> ' + order.customer_name + '</p>' +
                '<p><strong>Điện thoại:</strong> ' + order.customer_phone + '</p>' +
                '<p><strong>Địa chỉ:</strong> ' + order.customer_address + '</p>' +
                '<p><strong>Ghi chú:</strong> ' + (order.order_note || 'Không') + '</p>' +
                '<p><strong>Phương thức:</strong> ' + order.payment_method + '</p>' +
                '<p><strong>Trạng thái:</strong> ' + order.status + '</p>' +
                '<hr><h4>Chi tiết sản phẩm:</h4>' +
                '<ul>';
            
            $.each(items, function(i, item) {
                html += '<li>' + item.name + ' x ' + item.quantity + ' - ' + 
                    new Intl.NumberFormat().format(item.price) + ' VND</li>';
            });
            
            html += '</ul><hr><p><strong>Tổng cộng:</strong> ' + 
                new Intl.NumberFormat().format(order.total) + ' VND</p>' +
                '</div>';
            
            $('#order-detail-content').html(html);
            $('#order-detail-modal').addClass('active');
        }
    });
    
    $('.close-modal, .modal .close').on('click', function() {
        $('#order-detail-modal').removeClass('active');
    });
});

// Thêm CSS cho notification (thêm vào cuối file)
jQuery('head').append(`
<style>
.notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 12px 20px;
    background: #333;
    color: white;
    border-radius: 8px;
    z-index: 10000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}
.notification.show {
    opacity: 1;
    transform: translateX(0);
}
.notification.success { background: #28a745; }
.notification.error { background: #dc3545; }
.notification.info { background: #17a2b8; }
.btn-add-to-cart.loading, .quick-add.loading, .btn-buy-now-small.loading { 
    opacity: 0.7; 
    pointer-events: none; 
}
.btn-add-to-cart.added, .quick-add.added { 
    background: #28a745; 
}
</style>
`);