<footer class="site-footer">
    <!-- Footer Top -->
    <div class="footer-top">
        <div class="container">
            <div class="footer-widgets">
                <!-- About Widget -->
                <div class="footer-widget footer-about">
                    <div class="footer-logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-white.png" alt="<?php bloginfo('name'); ?>">
                    </div>
                    <p>Chuyên cung cấp mỹ phẩm chính hãng, chất lượng cao với giá tốt nhất. Cam kết hàng chính hãng 100%.</p>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" class="social-link" aria-label="Zalo"><i class="fab fa-zalo"></i></a>
                    </div>
                </div>

                <!-- Quick Links Widget -->
                <div class="footer-widget footer-links">
                    <h3 class="footer-widget-title">Liên kết nhanh</h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo home_url('/'); ?>">Trang chủ</a></li>
                        <li><a href="<?php echo get_post_type_archive_link('product'); ?>">Sản phẩm</a></li>
                        <li><a href="<?php echo get_post_type_archive_link('news'); ?>">Tin tức</a></li>
                        <li><a href="<?php echo home_url('/gioi-thieu'); ?>">Giới thiệu</a></li>
                        <li><a href="<?php echo home_url('/lien-he'); ?>">Liên hệ</a></li>
                    </ul>
                </div>

                <!-- Support Widget -->
                <div class="footer-widget footer-support">
                    <h3 class="footer-widget-title">Hỗ trợ khách hàng</h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo home_url('/huong-dan-mua-hang'); ?>">Hướng dẫn mua hàng</a></li>
                        <li><a href="<?php echo home_url('/chinh-sach-doi-tra'); ?>">Chính sách đổi trả</a></li>
                        <li><a href="<?php echo home_url('/chinh-sach-van-chuyen'); ?>">Chính sách vận chuyển</a></li>
                        <li><a href="<?php echo home_url('/chinh-sach-bao-mat'); ?>">Chính sách bảo mật</a></li>
                        <li><a href="<?php echo home_url('/cau-hoi-thuong-gap'); ?>">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <!-- Contact Widget -->
                <div class="footer-widget footer-contact">
                    <h3 class="footer-widget-title">Thông tin liên hệ</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Đường Nguyễn Văn A, Quận 1, TP. Hồ Chí Minh</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:0909123456">0909 123 456</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@mypham.com">info@mypham.com</a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Thứ 2 - Thứ 7: 8:00 - 21:00</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter Widget -->
                <div class="footer-widget footer-newsletter">
                    <h3 class="footer-widget-title">Đăng ký nhận tin</h3>
                    <p>Nhận thông tin khuyến mãi và sản phẩm mới nhất</p>
                    <form class="newsletter-form" method="post">
                        <div class="newsletter-form-group">
                            <input type="email" name="newsletter_email" placeholder="Email của bạn" required>
                            <button type="submit" class="newsletter-submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <div class="payment-methods">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/visa.png" alt="Visa">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/mastercard.png" alt="Mastercard">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/momo.png" alt="MoMo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment/zalopay.png" alt="ZaloPay">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-wrapper">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
                </div>
                <div class="footer-bottom-links">
                    <a href="<?php echo home_url('/dieu-khoan-su-dung'); ?>">Điều khoản sử dụng</a>
                    <span class="separator">|</span>
                    <a href="<?php echo home_url('/chinh-sach-bao-mat'); ?>">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>
</footer>

<?php wp_footer(); ?>
</body>
</html>