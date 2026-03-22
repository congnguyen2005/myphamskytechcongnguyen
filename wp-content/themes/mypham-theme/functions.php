    <?php
    /**
     * MyPham Theme Functions
     * @package MyPhamTheme
     */

    // Khởi tạo session
    function mypham_start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    }
    add_action('init', 'mypham_start_session');



    // ==================== CUSTOM POST TYPES ====================
    function mypham_register_post_types() {
        // Products
        register_post_type('product', [
            'labels' => [
                'name' => __('Products', 'mypham-theme'),
                'singular_name' => __('Product', 'mypham-theme'),
                'add_new' => __('Add New Product', 'mypham-theme'),
                'add_new_item' => __('Add New Product', 'mypham-theme'),
                'edit_item' => __('Edit Product', 'mypham-theme'),
                'new_item' => __('New Product', 'mypham-theme'),
                'view_item' => __('View Product', 'mypham-theme'),
                'search_items' => __('Search Products', 'mypham-theme'),
                'not_found' => __('No products found', 'mypham-theme'),
                'not_found_in_trash' => __('No products found in trash', 'mypham-theme'),
            ],
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'products', 'with_front' => false],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-products',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments'],
            'show_in_rest' => true,
        ]);
        
        // News
        register_post_type('news', [
            'labels' => [
                'name' => __('News', 'mypham-theme'),
                'singular_name' => __('News', 'mypham-theme'),
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite' => ['slug' => 'news'],
            'menu_icon' => 'dashicons-megaphone',
            'show_in_rest' => true,
        ]);
        
        // Product Categories
        register_taxonomy('product_category', 'product', [
            'labels' => [
                'name' => __('Categories', 'mypham-theme'),
                'singular_name' => __('Category', 'mypham-theme'),
                'add_new_item' => __('Add New Category', 'mypham-theme'),
                'edit_item' => __('Edit Category', 'mypham-theme'),
            ],
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'product-category'],
            'show_in_rest' => true,
        ]);
        
        // Brands
        register_taxonomy('brand', 'product', [
            'labels' => ['name' => __('Brands', 'mypham-theme')],
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => 'brand'],
        ]);
    }   
    add_action('init', 'mypham_register_post_types');
            function mypham_get_product_price($product_id) {
                $regular = (float) get_post_meta($product_id, '_product_price', true);
                $sale = (float) get_post_meta($product_id, '_product_sale_price', true);
                
                $has_sale = !empty($sale) && $sale > 0 && $sale < $regular;
                $current = $has_sale ? $sale : $regular;
                
                return [
                    'regular' => $regular,
                    'sale' => $sale,
                    'current' => $current,
                    'has_sale' => $has_sale
                ];
            }
            

    function mypham_format_price($price) {
        if (empty($price) || $price == 0) {
            return 'Liên hệ';
        }
        return number_format($price, 0, ',', '.') . ' VND';
    }
    function mypham_get_cart_count() {
        if (!session_id()) {
            session_start();
        }
        return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }


    function mypham_get_cart_total() {
        if (!session_id()) {
            session_start();
        }
        $total = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $price_data = mypham_get_product_price($product_id);
                $total += $price_data['current'] * $quantity;
            }
        }
        return $total;
    }


    /**
     * AJAX handler for processing order - CHỈ GIỮ LẠI MỘT BẢN DUY NHẤT
     */
    function mypham_process_order() {
        // Verify nonce if exists
        if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'checkout_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        if (!session_id()) {
            session_start();
        }
        
        // Validate required fields
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $address = sanitize_textarea_field($_POST['address'] ?? '');
        $note = sanitize_textarea_field($_POST['note'] ?? '');
        $payment_method = sanitize_text_field($_POST['payment_method'] ?? 'cod');
        
        if (empty($name) || empty($phone) || empty($address)) {
            wp_send_json_error('Vui lòng điền đầy đủ thông tin bắt buộc');
            return;
        }
        
        // Get cart data
        $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
        if (empty($cart_items)) {
            wp_send_json_error('Giỏ hàng trống');
            return;
        }
        
        // Calculate totals
        $cart_total = mypham_get_cart_total();
        $shipping_fee = $cart_total >= 500000 ? 0 : 30000;
        $final_total = $cart_total + $shipping_fee;
        
        // Create order post
        $order_data = array(
            'post_title' => 'Đơn hàng #' . time(),
            'post_type' => 'shop_order',
            'post_status' => 'pending',
            'meta_input' => array(
                '_order_customer_name' => $name,
                '_order_customer_email' => $email,
                '_order_customer_phone' => $phone,
                '_order_customer_address' => $address,
                '_order_payment_method' => $payment_method,
                '_order_note' => $note,
                '_order_items' => $cart_items,
                '_order_subtotal' => $cart_total,
                '_order_shipping_fee' => $shipping_fee,
                '_order_total' => $final_total,
                '_order_date' => current_time('mysql'),
            )
        );
        
        $order_id = wp_insert_post($order_data);
        
        if (is_wp_error($order_id)) {
            wp_send_json_error('Không thể tạo đơn hàng');
            return;
        }
        
        // Generate order number
        $order_number = 'ORD-' . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        update_post_meta($order_id, '_order_number', $order_number);
        
        // Clear cart
        unset($_SESSION['cart']);
        
        // Send notification email
        mypham_send_order_confirmation_email($order_id);
        
        wp_send_json_success(array(
            'order_id' => $order_id,
            'order_number' => $order_number,
            'redirect_url' => home_url('/thank-you?order=' . $order_id)
        ));
    }
    add_action('wp_ajax_mypham_process_order', 'mypham_process_order');
    add_action('wp_ajax_nopriv_mypham_process_order', 'mypham_process_order');

    /**
     * Send order confirmation email
     */
    function mypham_send_order_confirmation_email($order_id) {
        $order = get_post($order_id);
        $customer_email = get_post_meta($order_id, '_order_customer_email', true);
        
        if (empty($customer_email)) {
            return;
        }
        
        $subject = 'Xác nhận đơn hàng #' . get_post_meta($order_id, '_order_number', true);
        $message = mypham_get_order_email_template($order_id);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail($customer_email, $subject, $message, $headers);
        
        // Also send to admin
        wp_mail(get_option('admin_email'), $subject, $message, $headers);
    }

    /**
     * Get order email template
     */
    function mypham_get_order_email_template($order_id) {
        $order_number = get_post_meta($order_id, '_order_number', true);
        $customer_name = get_post_meta($order_id, '_order_customer_name', true);
        $total = get_post_meta($order_id, '_order_total', true);
        
        ob_start();
        ?>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>Xác nhận đơn hàng #<?php echo $order_number; ?></h2>
            <p>Xin chào <strong><?php echo $customer_name; ?></strong>,</p>
            <p>Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi. Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
            <p><strong>Tổng giá trị đơn hàng:</strong> <?php echo mypham_format_price($total); ?></p>
            <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
            <p>Trân trọng,<br>Đội ngũ chăm sóc khách hàng</p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Clear cart AJAX handler
     */
    function mypham_clear_cart() {
        if (!session_id()) {
            session_start();
        }
        unset($_SESSION['cart']);
        wp_send_json_success();
    }
    add_action('wp_ajax_mypham_clear_cart', 'mypham_clear_cart');
    add_action('wp_ajax_nopriv_mypham_clear_cart', 'mypham_clear_cart');

    // Thêm tạm thời vào functions.php để chạy 1 lần, sau đó xóa

    function mypham_sync_category_counts() {
        $categories = get_terms([
            'taxonomy' => 'product_category',
            'hide_empty' => false
        ]);
        
        foreach ($categories as $cat) {
            $count = new WP_Query([
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => [[
                    'taxonomy' => 'product_category',
                    'field' => 'term_id',
                    'terms' => $cat->term_id
                ]]
            ]);
            
            wp_update_term($cat->term_id, 'product_category', [
                'count' => $count->found_posts
            ]);
            
            echo "Danh mục {$cat->name}: {$count->found_posts} sản phẩm<br>";
        }
    }
    add_action('init', 'mypham_sync_category_counts');
    // AJAX Handler cho filter sản phẩm với giá
    add_action('wp_ajax_filter_products', 'handle_product_filter_ajax');
    add_action('wp_ajax_nopriv_filter_products', 'handle_product_filter_ajax');

    function handle_product_filter_ajax() {
        check_ajax_referer('product_filter_nonce', 'nonce');
        
        $category = sanitize_text_field($_POST['category']);
        $tag = sanitize_text_field($_POST['tag']);
        $min_price = intval($_POST['min_price']);
        $max_price = intval($_POST['max_price']);
        $orderby = sanitize_text_field($_POST['orderby']);
        $paged = intval($_POST['paged']);
        
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            'paged' => $paged,
            'post_status' => 'publish'
        );
        
        // Category filter
        if ($category && $category !== 'all') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_category',
                    'field' => 'slug',
                    'terms' => $category
                )
            );
        }
        
        // Tag filter
        if ($tag) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_tag',
                'field' => 'slug',
                'terms' => $tag
            );
            $args['tax_query']['relation'] = 'AND';
        }
        
        // Price filter
        if ($min_price > 0 || $max_price < 100000000) {
            $args['meta_query'] = array(
                'relation' => 'AND'
            );
            if ($min_price > 0) {
                $args['meta_query'][] = array(
                    'key' => '_product_price',
                    'value' => $min_price,
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                );
            }
            if ($max_price < 100000000) {
                $args['meta_query'][] = array(
                    'key' => '_product_price',
                    'value' => $max_price,
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                );
            }
        }
        
        // Orderby
        switch ($orderby) {
            case 'date':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'price':
                $args['meta_key'] = '_product_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_product_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'title':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            case 'title-desc':
                $args['orderby'] = 'title';
                $args['order'] = 'DESC';
                break;
        }
        
        $query = new WP_Query($args);
        $total_products = $query->found_posts;
        $max_pages = $query->max_num_pages;
        
        ob_start();
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                include_partial_product_card();
            endwhile;
        else :
        ?>
            <div class="no-products">
                <i class="fas fa-box-open"></i>
                <h3>Chưa có sản phẩm</h3>
                <p>Không tìm thấy sản phẩm nào phù hợp với bộ lọc của bạn.</p>
            </div>
        <?php
        endif;
        wp_reset_postdata();
        
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'total' => $total_products,
            'current_page' => $paged,
            'max_pages' => $max_pages
        ));
    }



    // ==================== MINI CART FUNCTIONS ====================
    function mypham_get_mini_cart_html() {
        if (!session_id()) {
            session_start();
        }
        
        $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
        
        if (empty($cart_items)) {
            return '<div class="mini-cart-empty">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Giỏ hàng của bạn đang trống</p>
                        <a href="' . home_url('/products') . '" class="btn btn-primary btn-sm">Mua sắm ngay</a>
                    </div>';
        }
        
        $html = '<div class="mini-cart-items">';
        $total = 0;
        
        foreach ($cart_items as $product_id => $quantity) {
            $product = get_post($product_id);
            if (!$product) continue;
            
            $price_data = mypham_get_product_price($product_id);
            $subtotal = $price_data['current'] * $quantity;
            $total += $subtotal;
            
            $html .= '<div class="mini-cart-item" data-product-id="' . $product_id . '">
                        <div class="item-image">
                            ' . get_the_post_thumbnail($product_id, 'thumbnail') . '
                        </div>
                        <div class="item-details">
                            <h4><a href="' . get_permalink($product_id) . '">' . esc_html($product->post_title) . '</a></h4>
                            <div class="item-price">' . mypham_format_price($price_data['current']) . ' x ' . $quantity . '</div>
                            <div class="item-subtotal">' . mypham_format_price($subtotal) . '</div>
                        </div>
                        <button class="remove-item-mini" data-id="' . $product_id . '">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>';
        }
        
        $html .= '</div>';
        $html .= '<div class="mini-cart-footer">
                    <div class="mini-cart-total">
                        <strong>Tổng cộng:</strong> ' . mypham_format_price($total) . '
                    </div>
                    <a href="' . home_url('/cart') . '" class="btn-view-cart">Xem giỏ hàng</a>
                    <a href="' . home_url('/thanh-toan') . '" class="btn-checkout-mini">Thanh toán ngay</a>
                </div>';
        
        return $html;
    }
    // Tạo trang thanh toán nếu chưa có
    function mypham_create_missing_pages() {
        // Kiểm tra trang thanh toán
        $checkout_page = get_page_by_path('thanh-toan');
        if (!$checkout_page) {
            wp_insert_post([
                'post_title' => 'Thanh toán',
                'post_name' => 'thanh-toan',
                'post_content' => '[mypham_checkout_form]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'checkout.php'
            ]);
            echo 'Đã tạo trang thanh toán<br>';
        }
        
        // Kiểm tra trang giỏ hàng
        $cart_page = get_page_by_path('cart');
        if (!$cart_page) {
            wp_insert_post([
                'post_title' => 'Giỏ hàng',
                'post_name' => 'cart',
                'post_content' => '[mypham_cart]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'cart.php'
            ]);
            echo 'Đã tạo trang giỏ hàng<br>';
        }
    }
    add_action('admin_init', 'mypham_create_missing_pages');    
    // ==================== CART AJAX ====================
    function mypham_add_to_cart() {
        if (!session_id()) {
            session_start();
        }
        
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        wp_send_json_success([
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => array_sum($_SESSION['cart'])
        ]);
    }
    add_action('wp_ajax_mypham_add_to_cart', 'mypham_add_to_cart');
    add_action('wp_ajax_nopriv_mypham_add_to_cart', 'mypham_add_to_cart');

    function mypham_remove_from_cart() {
        if (!session_id()) {
            session_start();
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        wp_send_json_success([
            'cart_count' => array_sum($_SESSION['cart']),
            'cart_total' => mypham_get_cart_total()
        ]);
    }
    add_action('wp_ajax_mypham_remove_from_cart', 'mypham_remove_from_cart');
    add_action('wp_ajax_nopriv_mypham_remove_from_cart', 'mypham_remove_from_cart');

    function mypham_update_cart() {
        if (!session_id()) {
            session_start();
        }
        
        $product_id = intval($_POST['product_id']);
        $quantity = max(1, intval($_POST['quantity']));
        
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        wp_send_json_success([
            'cart_count' => array_sum($_SESSION['cart']),
            'cart_total' => mypham_get_cart_total()
        ]);
    }
    add_action('wp_ajax_mypham_update_cart', 'mypham_update_cart');
    add_action('wp_ajax_nopriv_mypham_update_cart', 'mypham_update_cart');



    // Demo payment API
    function mypham_demo_payment_api($params = []) {
        if (empty($params['amount']) || $params['amount'] <= 0) {
            return ['success' => false, 'message' => 'Giá trị đơn hàng không hợp lệ'];
        }
        sleep(1);
        return [
            'success' => true,
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'message' => 'Thanh toán giả lập thành công'
        ];
    }

    // Demo CRM webhook sender
    function mypham_send_crm_webhook($data = []) {
        $endpoint = 'https://example.com/demo-crm-webhook';
        $args = [
            'body' => wp_json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 3,
            'blocking' => false
        ];
        wp_remote_post($endpoint, $args);
    }

    // AJAX to return mini cart HTML
    function mypham_get_mini_cart_ajax() {
        echo mypham_get_mini_cart_html();
        wp_die();
    }
    add_action('wp_ajax_mypham_get_mini_cart', 'mypham_get_mini_cart_ajax');
    add_action('wp_ajax_nopriv_mypham_get_mini_cart', 'mypham_get_mini_cart_ajax');

    function mypham_enqueue_scripts() {
        // CSS
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
        wp_enqueue_style('slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css');
        wp_enqueue_style('slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css');
        wp_enqueue_style('mypham-style', get_template_directory_uri() . '/style.css', [], filemtime(get_template_directory() . '/style.css'));
        
        // Custom CSS
        if (file_exists(get_template_directory() . '/assets/css/custom.css')) {
            wp_enqueue_style('mypham-custom', get_template_directory_uri() . '/assets/css/custom.css', ['mypham-style'], filemtime(get_template_directory() . '/assets/css/custom.css'));
        }
        
        // JS
        wp_enqueue_script('jquery');
        wp_enqueue_script('slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', ['jquery'], null, true);
        wp_enqueue_script('mypham-script', get_template_directory_uri() . '/assets/js/custom.js', ['jquery', 'slick-js'], filemtime(get_template_directory() . '/assets/js/custom.js'), true);
        
        // Localize script cho custom-js
        wp_localize_script('mypham-script', 'MyPhamData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'checkout_url' => home_url('/thanh-toan')
        ]);
        
        // Localize script cho mypham-script
        wp_localize_script('mypham-script', 'MyPhamData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => home_url('/'),
            'checkout_url' => home_url('/thanh-toan'),
            'nonce' => wp_create_nonce('mypham_nonce')
        ]);
        
        // Localize cho home.js nếu riêng biệt
        if (is_front_page()) {
            wp_localize_script('mypham-home-js', 'MyPhamData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'checkout_url' => home_url('/thanh-toan'),
            ]);
        }
    }
    add_action('wp_enqueue_scripts', 'mypham_enqueue_scripts');


    // Tạo trang thanh toán
    function mypham_force_create_checkout_page() {
        $checkout_page = get_page_by_path('thanh-toan');
        
        if (!$checkout_page) {
            $page_id = wp_insert_post([
                'post_title' => 'Thanh toán',
                'post_name' => 'thanh-toan',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'checkout.php',
                'meta_input' => [
                    '_wp_page_template' => 'checkout.php'
                ]
            ]);
            
            if ($page_id) {
                echo 'Đã tạo trang thanh toán: <a href="' . get_permalink($page_id) . '">' . get_permalink($page_id) . '</a><br>';
            }
        } else {
            echo 'Trang thanh toán đã tồn tại: ' . get_permalink($checkout_page) . '<br>';
        }
        
        // Tạo trang giỏ hàng
        $cart_page = get_page_by_path('cart');
        if (!$cart_page) {
            wp_insert_post([
                'post_title' => 'Giỏ hàng',
                'post_name' => 'cart',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'cart.php'
            ]);
            echo 'Đã tạo trang giỏ hàng<br>';
        }
    }
    // Chạy 1 lần bằng cách truy cập: http://localhost/myphamskytechcongnguyen/wp-admin/
    // Sau đó comment lại dòng dưới
    add_action('admin_init', 'mypham_force_create_checkout_page');
    function mypham_enqueue_home_assets() {
        if (is_front_page()) {
            // Home CSS
            wp_enqueue_style(
                'mypham-home',
                get_template_directory_uri() . '/assets/css/home.css',
                [],
                '1.0.1'
            );
            
            // Home JS
            wp_enqueue_script(
                'mypham-home-js',
                get_template_directory_uri() . '/assets/js/home.js',
                [],
                '1.0.1',
                true
            );
        }
    }
    add_action('wp_enqueue_scripts', 'mypham_enqueue_home_assets');

    function mypham_enqueue_shop_assets() {
        if (is_post_type_archive('product') || is_tax('product_category')) {
            wp_enqueue_style('mypham-shop', get_template_directory_uri() . '/assets/css/archive-product.css', [], '1.0.0');
            wp_enqueue_script('mypham-shop-js', get_template_directory_uri() . '/assets/js/archive-product.js', [], '1.0.0', true);
        }
    }
    add_action('wp_enqueue_scripts', 'mypham_enqueue_shop_assets');

    function mypham_create_orders_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'orders';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            order_number varchar(50) NOT NULL,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255),
            customer_phone varchar(20),
            customer_address text,
            order_note text,
            items longtext NOT NULL,
            total decimal(15,2) NOT NULL,
            payment_method varchar(50) NOT NULL,
            status varchar(50) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY order_number (order_number)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    register_activation_hook(__FILE__, 'mypham_create_orders_table');

    // Thêm giá mặc định cho sản phẩm nếu chưa có
    function mypham_set_default_prices() {
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_product_price',
                    'value' => '',
                    'compare' => '='
                ),
                array(
                    'key' => '_product_price',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        
        foreach ($products as $product) {
            $current_price = get_post_meta($product->ID, '_product_price', true);
            if (empty($current_price)) {
                update_post_meta($product->ID, '_product_price', 100000);
                update_post_meta($product->ID, '_product_sale_price', '');
                echo 'Đã thêm giá cho sản phẩm: ' . $product->post_title . '<br>';
            }
        }
    }
    // Thêm vào functions.php
    function mypham_debug_session() {
        if (current_user_can('administrator') && isset($_GET['debug_session'])) {
            session_start();
            echo '<pre>';
            echo 'SESSION CART:<br>';
            print_r($_SESSION['cart']);
            echo '<br><br>';
            
            echo 'PRODUCTS IN CART:<br>';
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $qty) {
                    $price_data = mypham_get_product_price($product_id);
                    echo 'ID: ' . $product_id . ' - Tên: ' . get_the_title($product_id) . ' - Giá: ' . $price_data['current'] . ' - SL: ' . $qty . '<br>';
                }
            }
            echo '</pre>';
            die();
        }
    }
    // add_action('init', 'mypham_debug_session');


    // ==================== WISHLIST FUNCTIONS ====================
    function mypham_add_to_wishlist() {
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error('Vui lòng đăng nhập');
            return;
        }
        
        $product_id = intval($_POST['product_id']);
        $wishlist = get_user_meta($user_id, 'wishlist', true) ?: [];
        
        if (in_array($product_id, $wishlist)) {
            $wishlist = array_diff($wishlist, [$product_id]);
            $added = false;
        } else {
            $wishlist[] = $product_id;
            $added = true;
        }
        
        update_user_meta($user_id, 'wishlist', $wishlist);
        wp_send_json_success(['added' => $added, 'count' => count($wishlist)]);
    }
    add_action('wp_ajax_mypham_add_to_wishlist', 'mypham_add_to_wishlist');


    function mypham_is_in_wishlist($product_id) {
        $wishlist = mypham_get_wishlist();
        return in_array($product_id, $wishlist);
    }


    // ==================== REVIEW FUNCTIONS ====================
    function mypham_get_product_reviews($product_id) {
        $reviews = get_comments([
            'post_id' => $product_id,
            'status' => 'approve',
            'type' => 'review'
        ]);
        
        $review_data = [
            'reviews' => [],
            'avg_rating' => 0,
            'review_count' => 0
        ];
        
        $total_rating = 0;
        foreach ($reviews as $review) {
            $rating = get_comment_meta($review->comment_ID, 'rating', true);
            if ($rating) {
                $total_rating += $rating;
                $review_data['reviews'][] = [
                    'user_name' => $review->comment_author,
                    'rating' => (int) $rating,
                    'comment' => $review->comment_content,
                    'date' => $review->comment_date
                ];
            }
        }
        
        $review_data['review_count'] = count($review_data['reviews']);
        if ($review_data['review_count'] > 0) {
            $review_data['avg_rating'] = round($total_rating / $review_data['review_count'], 1);
        }
        
        return $review_data;
    }
    function mypham_enqueue_checkout_assets() {
        // Enqueue CSS
        wp_enqueue_style(
            'mypham-checkout',
            get_template_directory_uri() . '/assets/css/checkout.css',
            array(),
            '1.0.0'
        );
        
        // Enqueue JS
        wp_enqueue_script(
            'mypham-checkout',
            get_template_directory_uri() . '/assets/js/checkout.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Localize script
        wp_localize_script('mypham-checkout', 'MyPhamCheckout', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => home_url(),
            'template_url' => get_template_directory_uri(),
            'nonce' => wp_create_nonce('checkout_nonce'),
            'shipping_threshold' => 500000,
            'shipping_fee' => 30000,
        ));
    }


    function mypham_add_review() {
        if (!is_user_logged_in()) {
            wp_send_json_error('Vui lòng đăng nhập để đánh giá');
            return;
        }
        
        $product_id = intval($_POST['product_id']);
        $rating = intval($_POST['rating']);
        $comment = sanitize_text_field($_POST['comment']);
        
        if ($rating < 1 || $rating > 5) {
            wp_send_json_error('Vui lòng chọn số sao đánh giá');
            return;
        }
        
        $user = wp_get_current_user();
        
        $comment_data = [
            'comment_post_ID' => $product_id,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'comment_content' => $comment,
            'comment_type' => 'review',
            'comment_approved' => 1,
        ];
        
        $comment_id = wp_insert_comment($comment_data);
        
        if ($comment_id) {
            add_comment_meta($comment_id, 'rating', $rating);
            wp_send_json_success('Đánh giá của bạn đã được gửi!');
        } else {
            wp_send_json_error('Có lỗi xảy ra, vui lòng thử lại');
        }
    }
    add_action('wp_ajax_mypham_add_review', 'mypham_add_review');



    add_shortcode('cart_count', function() {
        return '<span class="cart-count">' . mypham_get_cart_count() . '</span>';
    });

    add_shortcode('cart_total', function() {
        return mypham_format_price(mypham_get_cart_total());
    });

    // ==================== META BOXES ====================
    function mypham_add_product_meta_boxes() {
        add_meta_box(
            'product_details',
            'Chi tiết sản phẩm',
            'mypham_product_details_callback',
            'product',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'mypham_add_product_meta_boxes');
    function mypham_register_order_post_type() {
        register_post_type('shop_order',
            array(
                'labels' => array(
                    'name' => 'Đơn hàng',
                    'singular_name' => 'Đơn hàng',
                    'menu_name' => 'Đơn hàng',
                    'add_new' => 'Thêm đơn hàng',
                    'add_new_item' => 'Thêm đơn hàng mới',
                    'edit_item' => 'Sửa đơn hàng',
                    'view_item' => 'Xem đơn hàng',
                    'search_items' => 'Tìm kiếm đơn hàng',
                    'not_found' => 'Không tìm thấy đơn hàng',
                ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'supports' => array('title', 'editor'),
                'menu_icon' => 'dashicons-cart',
                'menu_position' => 30,
                'capability_type' => 'post',
            )
        );
    }
    add_action('init', 'mypham_register_order_post_type');
    function mypham_product_details_callback($post) {
        wp_nonce_field('product_details_nonce', 'product_details_nonce');
        
        $price = get_post_meta($post->ID, '_product_price', true);
        $sale_price = get_post_meta($post->ID, '_product_sale_price', true);
        $sku = get_post_meta($post->ID, '_product_sku', true);
        $stock = get_post_meta($post->ID, '_product_stock', true);
        $ingredients = get_post_meta($post->ID, '_product_ingredients', true);
        $usage = get_post_meta($post->ID, '_product_usage', true);
        ?>
        <style>
            .product-meta-box p { margin-bottom: 15px; }
            .product-meta-box label { display: inline-block; width: 120px; font-weight: bold; }
            .product-meta-box input[type="text"],
            .product-meta-box input[type="number"],
            .product-meta-box textarea { width: 300px; padding: 5px 10px; }
            .product-meta-box textarea { vertical-align: top; }
        </style>
        <div class="product-meta-box">
            <p>
                <label for="product_price">Giá sản phẩm (VND):</label>
                <input type="number" id="product_price" name="product_price" value="<?php echo esc_attr($price); ?>" step="1000">
            </p>
            <p>
                <label for="product_sale_price">Giá khuyến mãi (VND):</label>
                <input type="number" id="product_sale_price" name="product_sale_price" value="<?php echo esc_attr($sale_price); ?>" step="1000">
            </p>
            <p>
                <label for="product_sku">Mã SKU:</label>
                <input type="text" id="product_sku" name="product_sku" value="<?php echo esc_attr($sku); ?>">
            </p>
            <p>
                <label for="product_stock">Số lượng tồn kho:</label>
                <input type="number" id="product_stock" name="product_stock" value="<?php echo esc_attr($stock); ?>">
            </p>
            <p>
                <label for="product_ingredients">Thành phần:</label>
                <textarea id="product_ingredients" name="product_ingredients" rows="5"><?php echo esc_textarea($ingredients); ?></textarea>
            </p>
            <p>
                <label for="product_usage">Công dụng:</label>
                <textarea id="product_usage" name="product_usage" rows="5"><?php echo esc_textarea($usage); ?></textarea>
            </p>
        </div>
        <?php
    }

    function mypham_save_product_meta($post_id) {
        if (!isset($_POST['product_details_nonce']) || !wp_verify_nonce($_POST['product_details_nonce'], 'product_details_nonce')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = ['product_price', 'product_sale_price', 'product_sku', 'product_stock', 'product_ingredients', 'product_usage'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    add_action('save_post_product', 'mypham_save_product_meta');



    // ==================== REGISTER MENUS ====================
    function mypham_register_menus() {
        register_nav_menus([
            'primary_menu' => __('Primary Menu', 'mypham-theme'),
            'footer_menu' => __('Footer Menu', 'mypham-theme'),
        ]);
    }
    add_action('init', 'mypham_register_menus');

    // ==================== ADD THEME SUPPORT ====================
    function mypham_theme_setup() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

        register_nav_menus(array(
            'primary_menu' => __('Primary Menu', 'mypham-theme'),
            'footer_menu'  => __('Footer Menu', 'mypham-theme'),
        ));
    }
    add_action('after_setup_theme', 'mypham_theme_setup');


    // Register widget areas (sidebar & footer)
    function mypham_widgets_init() {
        register_sidebar(array(
            'name'          => __('Sidebar', 'mypham-theme'),
            'id'            => 'sidebar-1',
            'description'   => __('Main sidebar that appears on blog and archive pages.', 'mypham-theme'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));

        register_sidebar(array(
            'name'          => __('Footer Column 1', 'mypham-theme'),
            'id'            => 'footer-1',
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));

        register_sidebar(array(
            'name'          => __('Footer Column 2', 'mypham-theme'),
            'id'            => 'footer-2',
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));

        register_sidebar(array(
            'name'          => __('Footer Column 3', 'mypham-theme'),
            'id'            => 'footer-3',
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
    add_action('widgets_init', 'mypham_widgets_init');

    // ==================== DEFAULT MENU FALLBACK ====================
    function mypham_default_menu() {
        ?>
        <ul class="primary-menu">
            <li class="menu-item <?php echo is_front_page() ? 'current-menu-item' : ''; ?>">
                <a href="<?php echo home_url(); ?>">Trang chủ</a>
            </li>
            <li class="menu-item <?php echo is_post_type_archive('product') ? 'current-menu-item' : ''; ?>">
                <a href="<?php echo home_url('/products'); ?>">Sản phẩm</a>
            </li>
            <?php
            $categories = get_terms([
                'taxonomy' => 'product_category',
                'hide_empty' => false,
                'number' => 8
            ]);
            
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    echo '<li class="menu-item"><a href="' . get_term_link($category) . '">' . $category->name . '</a></li>';
                }
            }
            ?>
            <li class="menu-item <?php echo is_post_type_archive('news') ? 'current-menu-item' : ''; ?>">
                <a href="<?php echo home_url('/news'); ?>">Tin tức / Blog</a>
            </li>
        </ul>
        <?php
    }
    // Fix rewrite rules for product archive
        function mypham_fix_rewrite_rules() {
            flush_rewrite_rules();
        }
    add_action('after_switch_theme', 'mypham_fix_rewrite_rules');
        // Ensure product archive is working
        function mypham_product_archive_query($query) {
            if (!is_admin() && $query->is_main_query() && is_post_type_archive('product')) {
                $query->set('posts_per_page', 12);
            }
        }
        add_action('pre_get_posts', 'mypham_product_archive_query');
        // Set default price for products if not set
        function mypham_set_default_product_price() {
            $products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_product_price',
                        'compare' => 'NOT EXISTS'
                    )
                )
            ));
            
            foreach ($products as $product) {
                update_post_meta($product->ID, '_product_price', 0);
            }
        }
        // add_action('init', 'mypham_set_default_product_price');

    // ✅ ĐÚNG - Sử dụng hàm đã có
    function mypham_get_product_price_display($product_id) {
        $price_data = mypham_get_product_price($product_id);
        return mypham_format_price($price_data['current']);
    }
        // Đăng ký page templates
    function mypham_register_page_templates($templates) {
        $templates['cart.php'] = 'Trang giỏ hàng';
        $templates['checkout.php'] = 'Trang thanh toán';
        $templates['my-account.php'] = 'Tài khoản của tôi';
        return $templates;
    }
    add_filter('theme_page_templates', 'mypham_register_page_templates');

    // Modify cart/checkout page creation to remove contact page if present
    function mypham_create_cart_page() {
        $cart_page = get_page_by_path('cart');
        if (!$cart_page) {
            wp_insert_post(array(
                'post_title' => 'Giỏ hàng',
                'post_name' => 'cart',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'cart.php'
            ));
        }
        
        $checkout_page = get_page_by_path('thanh-toan');
        if (!$checkout_page) {
            wp_insert_post(array(
                'post_title' => 'Thanh toán',
                'post_name' => 'thanh-toan',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'checkout.php'
            ));
        }

        // If a contact page exists at /lien-he remove it (user requested deletion)
        $contact_page = get_page_by_path('lien-he');
        if ($contact_page) {
            wp_delete_post($contact_page->ID, true);
        }
    }
    add_action('after_switch_theme', 'mypham_create_cart_page');

    // Redirect any lingering requests to /lien-he to home (safety)
    function mypham_redirect_contact_page() {
        if (is_page('lien-he')) {
            wp_redirect(home_url(), 301);
            exit;
        }
    }
    add_action('template_redirect', 'mypham_redirect_contact_page');

    // Live search AJAX (returns small JSON suggestions)
    function mypham_live_search() {
        $q = sanitize_text_field($_GET['q'] ?? '');
        if (empty($q)) {
            wp_send_json([]);
        }

        $args = array(
            's' => $q,
            'post_type' => array('product', 'news', 'post'),
            'posts_per_page' => 6,
            'post_status' => 'publish'
        );

        $query = new WP_Query($args);
        $results = array();

        while ($query->have_posts()) {
            $query->the_post();
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
            $results[] = array(
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'thumb' => $thumb ? $thumb : ''
            );
        }
        wp_reset_postdata();

        wp_send_json($results);
    }
    add_action('wp_ajax_mypham_live_search', 'mypham_live_search');
    add_action('wp_ajax_nopriv_mypham_live_search', 'mypham_live_search');

    // ==================== NEWS FUNCTIONS ====================
    // Đăng ký taxonomy cho news (danh mục tin tức)
    function mypham_register_news_taxonomy() {
        register_taxonomy('news_category', 'news', [
            'labels' => [
                'name' => __('Danh mục tin tức', 'mypham-theme'),
                'singular_name' => __('Danh mục tin tức', 'mypham-theme'),
                'add_new_item' => __('Thêm danh mục mới', 'mypham-theme'),
                'edit_item' => __('Sửa danh mục', 'mypham-theme'),
            ],
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'news-category'],
            'show_in_rest' => true,
        ]);
        
        // Tags cho tin tức
        register_taxonomy('news_tag', 'news', [
            'labels' => [
                'name' => __('Thẻ tin tức', 'mypham-theme'),
                'singular_name' => __('Thẻ', 'mypham-theme'),
            ],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'news-tag'],
            'show_in_rest' => true,
        ]);
    }
    add_action('init', 'mypham_register_news_taxonomy');

    // ==================== NEWS SHORTCODES ====================
    add_shortcode('latest_news', function($atts) {
        $atts = shortcode_atts([
            'limit' => 3,
            'category' => '',
            'show_image' => 'yes',
            'show_excerpt' => 'yes',
            'excerpt_length' => 20
        ], $atts);
        
        $args = [
            'post_type' => 'news',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish'
        ];
        
        if (!empty($atts['category'])) {
            $args['tax_query'] = [[
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => $atts['category']
            ]];
        }
        
        $news = new WP_Query($args);
        $output = '<div class="latest-news-grid">';
        
        if ($news->have_posts()) {
            while ($news->have_posts()) {
                $news->the_post();
                $output .= '<div class="news-item">';
                
                if ($atts['show_image'] == 'yes' && has_post_thumbnail()) {
                    $output .= '<div class="news-image">
                        <a href="' . get_permalink() . '">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</a>
                    </div>';
                }
                
                $output .= '<div class="news-content">
                    <h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> ' . get_the_date('d/m/Y') . '</span>
                        <span><i class="far fa-user"></i> ' . get_the_author() . '</span>
                    </div>';
                
                if ($atts['show_excerpt'] == 'yes') {
                    $excerpt = wp_trim_words(get_the_excerpt(), intval($atts['excerpt_length']), '...');
                    $output .= '<p>' . $excerpt . '</p>';
                }
                
                $output .= '<a href="' . get_permalink() . '" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                </div></div>';
            }
            wp_reset_postdata();
        } else {
            $output .= '<p>Chưa có bài viết nào.</p>';
        }
        
        $output .= '</div>';
        return $output;
    });

    // ==================== NEWS WIDGET ====================
    class MyPham_Recent_News_Widget extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'mypham_recent_news',
                'MyPham - Bài viết mới',
                ['description' => 'Hiển thị danh sách bài viết mới nhất']
            );
        }
        
        public function widget($args, $instance) {
            $title = apply_filters('widget_title', $instance['title']);
            $number = $instance['number'] ?: 5;
            
            echo $args['before_widget'];
            if (!empty($title)) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            
            $news = new WP_Query([
                'post_type' => 'news',
                'posts_per_page' => $number
            ]);
            
            if ($news->have_posts()) {
                echo '<ul class="recent-news-list">';
                while ($news->have_posts()) {
                    $news->the_post();
                    echo '<li>
                        <a href="' . get_permalink() . '">' . get_the_title() . '</a>
                        <span class="post-date">' . get_the_date('d/m/Y') . '</span>
                    </li>';
                }
                wp_reset_postdata();
                echo '</ul>';
            } else {
                echo '<p>Chưa có bài viết nào.</p>';
            }
            
            echo $args['after_widget'];
        }
        
        public function form($instance) {
            $title = !empty($instance['title']) ? $instance['title'] : 'Bài viết mới';
            $number = !empty($instance['number']) ? $instance['number'] : 5;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">Tiêu đề:</label>
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>" 
                    name="<?php echo $this->get_field_name('title'); ?>" 
                    value="<?php echo esc_attr($title); ?>" class="widefat">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>">Số bài viết:</label>
                <input type="number" id="<?php echo $this->get_field_id('number'); ?>" 
                    name="<?php echo $this->get_field_name('number'); ?>" 
                    value="<?php echo esc_attr($number); ?>" min="1" max="20" class="tiny-text">
            </p>
            <?php
        }
        
        public function update($new_instance, $old_instance) {
            $instance = [];
            $instance['title'] = sanitize_text_field($new_instance['title']);
            $instance['number'] = intval($new_instance['number']);
            return $instance;
        }
    }

    function mypham_register_widgets() {
        register_widget('MyPham_Recent_News_Widget');
    }
    add_action('widgets_init', 'mypham_register_widgets');
    // Thêm vào functions.php

    // Đăng ký sidebar cho tin tức
    function mypham_register_news_sidebar() {
        register_sidebar([
            'name' => __('Sidebar Tin tức', 'mypham-theme'),
            'id' => 'news-sidebar',
            'description' => __('Sidebar cho trang tin tức và bài viết', 'mypham-theme'),
            'before_widget' => '<div class="sidebar-widget">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]);
    }
    add_action('widgets_init', 'mypham_register_news_sidebar');

    // Thêm vào functions.php để tạo bài viết demo

    function mypham_create_demo_news() {
        // Kiểm tra nếu chưa có bài viết nào
        $news_count = wp_count_posts('news')->publish;
        
        if ($news_count == 0) {
            $demo_news = [
                [
                    'title' => 'Top 10 sản phẩm chăm sóc da bán chạy nhất 2024',
                    'content' => 'Chăm sóc da là một quy trình quan trọng để duy trì làn da khỏe đẹp...',
                    'excerpt' => 'Khám phá những sản phẩm chăm sóc da được yêu thích nhất hiện nay...',
                    'category' => 'Chăm sóc da'
                ],
                [
                    'title' => 'Hướng dẫn trang điểm tự nhiên cho ngày hè',
                    'content' => 'Trang điểm tự nhiên là xu hướng được nhiều chị em ưa chuộng...',
                    'excerpt' => 'Bí quyết để có lớp trang điểm nhẹ nhàng, tự nhiên trong những ngày hè nóng bức...',
                    'category' => 'Trang điểm'
                ],
                [
                    'title' => 'Lợi ích của việc sử dụng serum Vitamin C',
                    'content' => 'Vitamin C là thành phần không thể thiếu trong quy trình chăm sóc da...',
                    'excerpt' => 'Tìm hiểu về công dụng tuyệt vời của serum Vitamin C đối với làn da...',
                    'category' => 'Chăm sóc da'
                ]
            ];
            
            foreach ($demo_news as $item) {
                $post_id = wp_insert_post([
                    'post_title' => $item['title'],
                    'post_content' => $item['content'],
                    'post_excerpt' => $item['excerpt'],
                    'post_status' => 'publish',
                    'post_type' => 'news',
                    'post_author' => 1
                ]);
                
                if ($post_id && !empty($item['category'])) {
                    $cat = term_exists($item['category'], 'news_category');
                    if (!$cat) {
                        $cat = wp_insert_term($item['category'], 'news_category');
                    }
                    if (!is_wp_error($cat)) {
                        wp_set_post_terms($post_id, [$cat['term_id']], 'news_category');
                    }
                }
            }
        }
    }
    // Chạy 1 lần sau đó comment lại
    // add_action('init', 'mypham_create_demo_news');
    // ==================== REGISTER USER AJAX ====================
    function mypham_register_user() {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        
        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error('Vui lòng điền đầy đủ thông tin');
            return;
        }
        
        if (username_exists($username)) {
            wp_send_json_error('Tên đăng nhập đã tồn tại');
            return;
        }
        
        if (email_exists($email)) {
            wp_send_json_error('Email đã được sử dụng');
            return;
        }
        
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error($user_id->get_error_message());
            return;
        }
        
        if (!empty($phone)) {
            update_user_meta($user_id, 'phone', $phone);
        }
        
        wp_send_json_success('Đăng ký thành công');
    }
    add_action('wp_ajax_nopriv_mypham_register_user', 'mypham_register_user');

    // ==================== UPDATE PROFILE ====================
    function mypham_update_profile() {
        if (!is_user_logged_in()) {
            wp_send_json_error('Vui lòng đăng nhập');
            return;
        }
        
        $user_id = get_current_user_id();
        $display_name = sanitize_text_field($_POST['display_name']);
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $address = sanitize_textarea_field($_POST['address'] ?? '');
        
        if (!empty($display_name)) {
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $display_name
            ]);
        }
        
        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'address', $address);
        
        wp_send_json_success('Cập nhật thành công');
    }
    add_action('wp_ajax_mypham_update_profile', 'mypham_update_profile');

    // ==================== CHANGE PASSWORD ====================
    function mypham_change_password() {
        if (!is_user_logged_in()) {
            wp_send_json_error('Vui lòng đăng nhập');
            return;
        }
        
        $user = wp_get_current_user();
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        
        if (!wp_check_password($current_password, $user->data->user_pass, $user->ID)) {
            wp_send_json_error('Mật khẩu hiện tại không đúng');
            return;
        }
        
        wp_set_password($new_password, $user->ID);
        wp_send_json_success('Đổi mật khẩu thành công');
    }
    add_action('wp_ajax_mypham_change_password', 'mypham_change_password');

    // ==================== THÊM CSS VÀO ADMIN ====================
    function mypham_admin_styles() {
        echo '<style>
            .product-meta-box p { margin-bottom: 15px; }
            .product-meta-box label { display: inline-block; width: 120px; font-weight: bold; }
            .product-meta-box input[type="text"], 
            .product-meta-box input[type="number"],
            .product-meta-box textarea { width: 300px; padding: 5px 10px; }
        </style>';
    }
    add_action('admin_head', 'mypham_admin_styles');

    // ==================== FIX REWRITE RULES ====================
    function mypham_flush_rewrite_rules() {
        flush_rewrite_rules();
    }
    add_action('after_switch_theme', 'mypham_flush_rewrite_rules');



    // ==================== TẠO TRANG TỰ ĐỘNG ====================
    function mypham_create_pages() {
        $pages = [
            ['title' => 'Giỏ hàng', 'slug' => 'cart', 'template' => 'cart.php'],
            ['title' => 'Thanh toán', 'slug' => 'thanh-toan', 'template' => 'checkout.php'],
            ['title' => 'Tài khoản', 'slug' => 'my-account', 'template' => 'my-account.php'],
            ['title' => 'Đăng nhập', 'slug' => 'login', 'template' => 'login.php']
        ];
        
        foreach ($pages as $page) {
            $existing = get_page_by_path($page['slug']);
            if (!$existing) {
                wp_insert_post([
                    'post_title' => $page['title'],
                    'post_name' => $page['slug'],
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'page_template' => $page['template']
                ]);
            }
        }
    }
    add_action('after_switch_theme', 'mypham_create_pages');



    // Trong functions.php
    function mypham_enqueue_single_product_assets() {
        if (is_singular('product')) {
            wp_enqueue_style('mypham-single-product', get_template_directory_uri() . '/assets/css/single-product.css', [], '1.0.0');
            wp_enqueue_script('mypham-single-product-js', get_template_directory_uri() . '/assets/js/single-product.js', [], '1.0.0', true);
        }
    }
    add_action('wp_enqueue_scripts', 'mypham_enqueue_single_product_assets');

    // Enqueue category product page assets
    function mypham_enqueue_category_assets() {
        if (is_tax('product_category')) {
            // CSS
            wp_enqueue_style(
                'mypham-category-product',
                get_template_directory_uri() . '/assets/css/taxonomy-product_category.css',
                [],
                '1.0.0'
            );
            
            // JS
            wp_enqueue_script(
                'mypham-category-product',
                get_template_directory_uri() . '/assets/js/taxonomy-product_category.js',
                ['jquery'],
                '1.0.0',
                true
            );
            
            // Localize script with MyPhamData if not already localized
            wp_localize_script('mypham-category-product', 'MyPhamData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'home_url' => home_url('/'),
                'checkout_url' => home_url('/thanh-toan'),
                'nonce' => wp_create_nonce('mypham_nonce')
            ]);
        }
    }
    add_action('wp_enqueue_scripts', 'mypham_enqueue_category_assets');
    // Enqueue Header & Footer Assets
    function mypham_enqueue_header_footer_assets() {
        // Header CSS
        wp_enqueue_style('mypham-header', get_template_directory_uri() . '/assets/css/header.css', [], '1.0.0');
        
        // Footer CSS
        wp_enqueue_style('mypham-footer', get_template_directory_uri() . '/assets/css/footer.css', [], '1.0.0');
        
        // Header JS
        wp_enqueue_script('mypham-header-js', get_template_directory_uri() . '/assets/js/header.js', ['jquery'], '1.0.0', true);
        
        // Footer JS
        wp_enqueue_script('mypham-footer-js', get_template_directory_uri() . '/assets/js/footer.js', ['jquery'], '1.0.0', true);
        
        // Localize script
        wp_localize_script('mypham-header-js', 'MyPhamData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => home_url('/'),
            'checkout_url' => home_url('/thanh-toan')
        ]);
    }
    add_action('wp_enqueue_scripts', 'mypham_enqueue_header_footer_assets');

    // Newsletter AJAX Handler
    function mypham_newsletter_subscribe() {
        $email = sanitize_email($_POST['email']);
        
        if (empty($email) || !is_email($email)) {
            wp_send_json_error('Email không hợp lệ');
            return;
        }
        
        // Save to database or send to email service
        // For demo, just return success
        wp_send_json_success('Đăng ký thành công! Cảm ơn bạn.');
    }
    add_action('wp_ajax_mypham_newsletter_subscribe', 'mypham_newsletter_subscribe');
    add_action('wp_ajax_nopriv_mypham_newsletter_subscribe', 'mypham_newsletter_subscribe');
    // Get wishlist for current user
    function mypham_get_wishlist() {
        $user_id = get_current_user_id();
        if (!$user_id) return [];
        $wishlist = get_user_meta($user_id, 'wishlist', true);
        return $wishlist ?: [];
    }


    // Register wishlist page template
    function mypham_register_wishlist_template($templates) {
        $templates['wishlist.php'] = 'Wishlist Page';
        return $templates;
    }
    add_filter('theme_page_templates', 'mypham_register_wishlist_template');

    // Auto create wishlist page on theme activation
    function mypham_create_wishlist_page() {
        $wishlist_page = get_page_by_path('wishlist');
        if (!$wishlist_page) {
            wp_insert_post([
                'post_title' => 'Yêu thích',
                'post_name' => 'wishlist',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'wishlist.php'
            ]);
        }
    }
    add_action('after_switch_theme', 'mypham_create_wishlist_page');
    // AJAX Load More News
function mypham_load_more_news() {
    check_ajax_referer('news_load_more_nonce', 'nonce');
    
    $page = intval($_POST['page']);
    $args = array(
        'post_type' => 'news',
        'posts_per_page' => 10,
        'paged' => $page,
        'post_status' => 'publish'
    );
    
    // Add tax_query if on category page
    if (isset($_POST['category_id'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'news_category',
                'field'    => 'term_id',
                'terms'    => intval($_POST['category_id']),
            ),
        );
    }
    
    $news_query = new WP_Query($args);
    $has_more = $news_query->max_num_pages > $page;
    
    ob_start();
    if ($news_query->have_posts()) {
        while ($news_query->have_posts()) {
            $news_query->the_post();
            get_template_part('template-parts/news', 'card');
        }
    }
    $html = ob_get_clean();
    
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more
    ));
}
add_action('wp_ajax_load_more_news', 'mypham_load_more_news');
add_action('wp_ajax_nopriv_load_more_news', 'mypham_load_more_news');

// Localize script for AJAX
function news_page_scripts() {
    if (is_post_type_archive('news') || is_tax('news_category')) {
        wp_localize_script('news-page-script', 'news_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('news_load_more_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'news_page_scripts');