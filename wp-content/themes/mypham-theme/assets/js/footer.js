/**
 * Footer JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // ========== NEWSLETTER FORM ==========
        var $newsletterForm = $('.newsletter-form');
        
        $newsletterForm.on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $email = $form.find('input[name="newsletter_email"]');
            var email = $email.val();
            
            if (!email) {
                showNotification('Vui lòng nhập email', 'error');
                return;
            }
            
            $.ajax({
                url: MyPhamData.ajax_url,
                type: 'POST',
                data: {
                    action: 'mypham_newsletter_subscribe',
                    email: email
                },
                beforeSend: function() {
                    $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data || 'Đăng ký thành công!', 'success');
                        $email.val('');
                    } else {
                        showNotification(response.data || 'Có lỗi xảy ra', 'error');
                    }
                },
                error: function() {
                    showNotification('Lỗi kết nối, vui lòng thử lại', 'error');
                },
                complete: function() {
                    $form.find('button').prop('disabled', false).html('<i class="fas fa-paper-plane"></i>');
                }
            });
        });
        
        // ========== NOTIFICATION FUNCTION ==========
        function showNotification(message, type) {
            var $notification = $('<div class="footer-notification ' + type + '">' + message + '</div>');
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.addClass('show');
            }, 10);
            
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 3000);
        }
        
        // Add notification styles
        var notificationStyles = `
            <style>
                .footer-notification {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 8px;
                    background: #333;
                    color: white;
                    z-index: 9999;
                    transform: translateX(400px);
                    transition: transform 0.3s ease;
                    font-size: 14px;
                    font-weight: 500;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                }
                .footer-notification.show {
                    transform: translateX(0);
                }
                .footer-notification.success {
                    background: #10b981;
                }
                .footer-notification.error {
                    background: #ef4444;
                }
                .footer-notification.info {
                    background: #3b82f6;
                }
            </style>
        `;
        $('head').append(notificationStyles);
        
    });
    
})(jQuery);