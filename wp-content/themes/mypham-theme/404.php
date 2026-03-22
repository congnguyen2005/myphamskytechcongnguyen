<?php
/**
 * 404 Template
 * 
 * @package MyPhamTheme
 */

get_header();
?>

<div class="error-404-page">
    <div class="container">
        <div class="error-content">
            <div class="error-code">
                <span class="code-4">4</span>
                <span class="code-0">0</span>
                <span class="code-4">4</span>
            </div>
            <h1>Oops! Không tìm thấy trang</h1>
            <p>Trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.</p>
            <div class="error-actions">
                <a href="<?php echo home_url(); ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Về trang chủ
                </a>
                <a href="<?php echo home_url('/products'); ?>" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i> Xem sản phẩm
                </a>
            </div>
            
            <div class="error-search">
                <h3>Hoặc tìm kiếm sản phẩm</h3>
                <form role="search" method="get" action="<?php echo home_url('/'); ?>">
                    <input type="search" name="s" placeholder="Tìm kiếm sản phẩm...">
                    <input type="hidden" name="post_type" value="product">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
?>