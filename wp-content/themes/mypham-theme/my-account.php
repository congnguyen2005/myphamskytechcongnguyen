<?php
/* Template Name: My Account */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}

global $wpdb;
$current_user = wp_get_current_user();
$table_name = $wpdb->prefix . 'orders';
$orders = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name WHERE customer_email = %s ORDER BY created_at DESC",
    $current_user->user_email
));
?>

<div class="my-account-page">
    <div class="container">
        <div class="account-header">
            <h1>Tài khoản của tôi</h1>
            <p>Xin chào, <strong><?php echo $current_user->display_name; ?></strong></p>
        </div>
        
        <div class="account-wrapper">
            <div class="account-sidebar">
                <ul class="account-menu">
                    <li class="active" data-tab="profile">Thông tin cá nhân</li>
                    <li data-tab="orders">Đơn hàng của tôi</li>
                    <li data-tab="change-password">Đổi mật khẩu</li>
                    <li><a href="<?php echo wp_logout_url(home_url()); ?>">Đăng xuất</a></li>
                </ul>
            </div>
            
            <div class="account-content">
                <!-- Profile Tab -->
                <div class="tab-content active" id="profile-tab">
                    <h3>Thông tin cá nhân</h3>
                    <form id="update-profile-form">
                        <div class="form-group">
                            <label for="display_name">Họ và tên</label>
                            <input type="text" id="display_name" name="display_name" 
                                   value="<?php echo $current_user->display_name; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo $current_user->user_email; ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo get_user_meta($current_user->ID, 'phone', true); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <textarea id="address" name="address" rows="3"><?php echo get_user_meta($current_user->ID, 'address', true); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                    </form>
                </div>
                
                <!-- Orders Tab -->
                <div class="tab-content" id="orders-tab">
                    <h3>Lịch sử đơn hàng</h3>
                    <?php if ($orders) : ?>
                        <div class="orders-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order) : ?>
                                        <tr>
                                            <td><?php echo $order->order_number; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                                            <td><?php echo number_format($order->total, 0, ',', '.'); ?> VND</td>
                                            <td>
                                                <?php 
                                                $methods = [
                                                    'cod' => 'COD',
                                                    'bank_transfer' => 'Chuyển khoản'
                                                ];
                                                echo $methods[$order->payment_method] ?? $order->payment_method;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_labels = [
                                                    'pending' => 'Đang xử lý',
                                                    'processing' => 'Đang xử lý',
                                                    'completed' => 'Đã giao',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                                $status_class = $order->status;
                                                echo '<span class="order-status ' . $status_class . '">' . ($status_labels[$order->status] ?? $order->status) . '</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn-view-order" data-order='<?php echo json_encode($order); ?>'>
                                                    Xem chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p>Bạn chưa có đơn hàng nào.</p>
                        <a href="<?php echo home_url('/products'); ?>" class="btn btn-primary">Mua sắm ngay</a>
                    <?php endif; ?>
                </div>
                
                <!-- Change Password Tab -->
                <div class="tab-content" id="change-password-tab">
                    <h3>Đổi mật khẩu</h3>
                    <form id="change-password-form">
                        <div class="form-group">
                            <label for="current_password">Mật khẩu hiện tại</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Mật khẩu mới</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div id="order-detail-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Chi tiết đơn hàng</h2>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body" id="order-detail-content">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<?php
get_footer();
?>