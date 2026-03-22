<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header">
    <!-- Top Bar -->
    <div class="header-top-bar">
        <div class="container">
            <div class="top-bar-left">
                <ul class="top-bar-list">
                    <li><i class="fas fa-truck"></i> Miễn phí vận chuyển đơn hàng từ 500k</li>
                    <li><i class="fas fa-gift"></i> Quà tặng hấp dẫn khi mua hàng</li>
                    <li><i class="fas fa-shield-alt"></i> Hàng chính hãng 100%</li>
                </ul>
            </div>
            <div class="top-bar-right">
                <?php if (is_user_logged_in()) : 
                    $current_user = wp_get_current_user();
                ?>
                    <div class="user-menu">
                        <a href="<?php echo home_url('/my-account'); ?>" class="user-name">
                            <i class="fas fa-user-circle"></i> <?php echo esc_html($current_user->display_name); ?>
                        </a>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </div>
                <?php else : ?>
                    <div class="auth-links">
                        <a href="<?php echo home_url('/login'); ?>" class="login-link">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                        <span class="separator">|</span>
                        <a href="<?php echo home_url('/login'); ?>?action=register" class="register-link">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <div class="header-main-wrapper">
                <!-- Logo -->
                <div class="header-logo">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>">
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Search Form -->
                <div class="header-search">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-form-wrapper">
                            <input type="search" 
                                   class="search-field" 
                                   placeholder="Tìm kiếm sản phẩm, bài viết..." 
                                   value="<?php echo get_search_query(); ?>" 
                                   name="s"
                                   autocomplete="off">
                            <button type="submit" class="search-submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <div class="search-suggestions"></div>
                        </div>
                        <input type="hidden" name="post_type" value="product">
                    </form>
                </div>

                <!-- Header Actions -->
                <div class="header-actions">
                    <!-- Hotline -->
                    <div class="header-hotline">
                        <div class="hotline-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="hotline-info">
                            <span>Hotline 24/7</span>
                            <a href="tel:0909123456" class="hotline-number">0909 123 456</a>
                        </div>
                    </div>

                    <!-- Wishlist -->
<!-- In header.php, update the wishlist link (around line 110) -->
<div class="header-wishlist">
    <a href="<?php echo home_url('/wishlist'); ?>" class="wishlist-link">
        <i class="far fa-heart"></i>
        <span class="wishlist-count"><?php echo count(mypham_get_wishlist()); ?></span>
    </a>
</div>

                    <!-- Cart -->
                    <div class="header-cart">
                        <a href="#" class="cart-link mini-cart-toggle">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="cart-count"><?php echo mypham_get_cart_count(); ?></span>
                        </a>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" aria-label="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="header-navigation">
        <div class="container">
            <nav class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary_menu',
                    'menu_class' => 'primary-menu',
                    'container' => false,
                    'fallback_cb' => 'mypham_default_menu',
                    'depth' => 3,
                ));
                ?>
            </nav>
        </div>
    </div>
</header>

<!-- Mobile Menu Panel -->
<div class="mobile-menu-panel">
    <div class="mobile-menu-header">
        <div class="mobile-menu-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="<?php bloginfo('name'); ?>">
        </div>
        <button class="mobile-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-menu-body">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary_menu',
            'menu_class' => 'mobile-menu',
            'container' => false,
            'fallback_cb' => 'mypham_default_menu',
        ));
        ?>
        
        <div class="mobile-menu-footer">
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo home_url('/my-account'); ?>" class="mobile-account-link">
                    <i class="fas fa-user-circle"></i> Tài khoản của tôi
                </a>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="mobile-logout-link">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            <?php else : ?>
                <a href="<?php echo home_url('/login'); ?>" class="mobile-login-link">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </a>
                <a href="<?php echo home_url('/login'); ?>?action=register" class="mobile-register-link">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </a>
            <?php endif; ?>
            
            <div class="mobile-hotline">
                <i class="fas fa-phone-alt"></i>
                <a href="tel:0909123456">0909 123 456</a>
            </div>
        </div>
    </div>
</div>
<div class="mobile-menu-overlay"></div>

<!-- Mini Cart -->
<div class="mini-cart-panel">
    <div class="mini-cart-header">
        <h3>Giỏ hàng của bạn</h3>
        <button class="close-mini-cart"><i class="fas fa-times"></i></button>
    </div>
    <div class="mini-cart-content">
        <?php echo mypham_get_mini_cart_html(); ?>
    </div>
</div>
<div class="mini-cart-overlay"></div>
<script>
    var MyPhamData = {
        ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
        home_url: '<?php echo home_url(); ?>',
        checkout_url: '<?php echo home_url('/thanh-toan'); ?>',
        nonce: '<?php echo wp_create_nonce('mypham_nonce'); ?>'
    };
    console.log('✅ MyPhamData loaded:', MyPhamData);
</script>
<?php wp_head(); ?>