<?php
/* Template Name: Login Page */
get_header();
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-tabs">
                <button class="tab-btn active" data-tab="login">Đăng nhập</button>
                <button class="tab-btn" data-tab="register">Đăng ký</button>
            </div>
            
            <!-- Login Form -->
            <div class="auth-form active" id="login-form">
                <h2>Đăng nhập</h2>
                <form method="POST" action="<?php echo wp_login_url(); ?>">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập hoặc Email</label>
                        <input type="text" id="username" name="log" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" id="password" name="pwd" required>
                    </div>
                    
                    <div class="form-group remember">
                        <label>
                            <input type="checkbox" name="rememberme" value="forever">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    
                    <div class="auth-links">
                        <a href="<?php echo wp_lostpassword_url(); ?>">Quên mật khẩu?</a>
                    </div>
                    
                    <input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>">
                </form>
            </div>
            
            <!-- Register Form -->
            <div class="auth-form" id="register-form">
                <h2>Đăng ký tài khoản</h2>
                <form id="register-form-ajax">
                    <div class="form-group">
                        <label for="reg_username">Tên đăng nhập <span class="required">*</span></label>
                        <input type="text" id="reg_username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_email">Email <span class="required">*</span></label>
                        <input type="email" id="reg_email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_password">Mật khẩu <span class="required">*</span></label>
                        <input type="password" id="reg_password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_confirm_password">Xác nhận mật khẩu <span class="required">*</span></label>
                        <input type="password" id="reg_confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_phone">Số điện thoại</label>
                        <input type="tel" id="reg_phone" name="phone">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
?>