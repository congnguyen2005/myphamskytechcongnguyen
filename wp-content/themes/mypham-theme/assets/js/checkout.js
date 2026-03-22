/**
 * Checkout Page JavaScript
 * @package MyPhamTheme
 */

(function($) {
    'use strict';

    class CheckoutManager {
        constructor() {
            this.form = $('#checkout-form');
            this.submitBtn = $('.btn-checkout-submit');
            this.paymentMethod = $('#payment-method');
            this.paymentDetails = $('#payment-details');
            this.wrapper = $('.checkout-wrapper');
            
            this.init();
        }

        init() {
            if (!this.form.length) return;
            
            this.bindEvents();
            this.loadSavedData();
        }

        bindEvents() {
            this.form.on('submit', this.handleSubmit.bind(this));
            this.paymentMethod.on('change', this.handlePaymentMethodChange.bind(this));
            this.form.find('input, textarea, select').on('blur', this.validateField.bind(this));
        }

        loadSavedData() {
            // Load saved customer data from localStorage
            const savedData = localStorage.getItem('checkout_customer_data');
            if (savedData) {
                try {
                    const data = JSON.parse(savedData);
                    Object.keys(data).forEach(key => {
                        const field = this.form.find(`[name="${key}"]`);
                        if (field.length && !field.val()) {
                            field.val(data[key]);
                        }
                    });
                } catch (e) {
                    console.error('Error loading saved data:', e);
                }
            }
        }

        saveCustomerData() {
            const customerData = {
                name: this.form.find('[name="name"]').val(),
                email: this.form.find('[name="email"]').val(),
                phone: this.form.find('[name="phone"]').val()
            };
            localStorage.setItem('checkout_customer_data', JSON.stringify(customerData));
        }

        handlePaymentMethodChange() {
            const method = this.paymentMethod.val();
            this.showPaymentDetails(method);
        }

        showPaymentDetails(method) {
            let html = '';
            
            switch(method) {
                case 'bank_transfer':
                    html = this.getBankTransferDetails();
                    break;
                case 'momo':
                    html = this.getMomoDetails();
                    break;
                case 'zalopay':
                    html = this.getZaloPayDetails();
                    break;
                default:
                    this.paymentDetails.hide();
                    return;
            }
            
            this.paymentDetails.html(html).show();
        }

        getBankTransferDetails() {
            return `
                <div class="bank-info">
                    <h4><i class="fas fa-university"></i> Thông tin chuyển khoản</h4>
                    <p><strong>Ngân hàng:</strong> Vietcombank</p>
                    <p><strong>Số tài khoản:</strong> 1234567890</p>
                    <p><strong>Chủ tài khoản:</strong> CÔNG TY MỸ PHẨM ABC</p>
                    <p><strong>Nội dung:</strong> [Mã đơn hàng] - [Họ tên]</p>
                    <div class="alert alert-info" style="margin-top: 10px; padding: 10px; background: #e3f2fd; border-radius: 8px;">
                        <i class="fas fa-info-circle"></i> Vui lòng chuyển khoản đúng nội dung để xác nhận đơn hàng nhanh chóng
                    </div>
                </div>
            `;
        }

        getMomoDetails() {
            return `
                <div class="bank-info">
                    <h4><i class="fas fa-mobile-alt"></i> Thanh toán qua Ví MoMo</h4>
                    <p><strong>Số điện thoại:</strong> 0987654321</p>
                    <p><strong>QR Code:</strong> Quét mã QR để thanh toán</p>
                    <div class="qr-placeholder" style="text-align: center; margin-top: 10px;">
                        <img src="${MyPhamCheckout.template_url}/assets/images/payment/momo-qr.png" alt="MoMo QR" style="max-width: 150px;">
                    </div>
                </div>
            `;
        }

        getZaloPayDetails() {
            return `
                <div class="bank-info">
                    <h4><i class="fas fa-comment-dots"></i> Thanh toán qua ZaloPay</h4>
                    <p><strong>Số điện thoại:</strong> 0987654321</p>
                    <p><strong>QR Code:</strong> Quét mã QR để thanh toán</p>
                    <div class="qr-placeholder" style="text-align: center; margin-top: 10px;">
                        <img src="${MyPhamCheckout.template_url}/assets/images/payment/zalopay-qr.png" alt="ZaloPay QR" style="max-width: 150px;">
                    </div>
                </div>
            `;
        }

        validateField(e) {
            const field = $(e.target);
            const value = field.val().trim();
            const name = field.attr('name');
            let isValid = true;
            let errorMessage = '';

            // Remove existing error
            field.removeClass('error');
            field.siblings('.validation-message').remove();

            if (field.prop('required') && !value) {
                isValid = false;
                errorMessage = 'Vui lòng nhập thông tin này';
            } else if (name === 'email' && value && !this.validateEmail(value)) {
                isValid = false;
                errorMessage = 'Email không hợp lệ';
            } else if (name === 'phone' && value && !this.validatePhone(value)) {
                isValid = false;
                errorMessage = 'Số điện thoại không hợp lệ';
            }

            if (!isValid) {
                field.addClass('error');
                field.after(`<div class="validation-message">${errorMessage}</div>`);
            }

            return isValid;
        }

        validateEmail(email) {
            const re = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
            return re.test(email);
        }

        validatePhone(phone) {
            const re = /^(0|\+84)(\d{9,10})$/;
            return re.test(phone);
        }

        validateForm() {
            let isValid = true;
            const fields = this.form.find('input[required], textarea[required], select[required]');
            
            fields.each((index, field) => {
                const $field = $(field);
                const value = $field.val().trim();
                
                if (!value) {
                    isValid = false;
                    $field.addClass('error');
                    if (!$field.siblings('.validation-message').length) {
                        $field.after('<div class="validation-message">Vui lòng nhập thông tin này</div>');
                    }
                } else {
                    $field.removeClass('error');
                    $field.siblings('.validation-message').remove();
                }
            });

            return isValid;
        }

        async handleSubmit(e) {
            e.preventDefault();
            
            if (!this.validateForm()) {
                this.showNotification('Vui lòng điền đầy đủ thông tin', 'error');
                return;
            }

            this.saveCustomerData();
            
            const formData = this.form.serializeArray();
            formData.push({ name: 'action', value: 'mypham_process_order' });
            
            this.setLoading(true);
            
            try {
                const response = await $.post(MyPhamCheckout.ajax_url, formData);
                
                if (response.success) {
                    this.handleSuccess(response);
                } else {
                    this.handleError(response.data || 'Có lỗi xảy ra khi xử lý đơn hàng');
                }
            } catch (error) {
                console.error('Checkout error:', error);
                this.handleError('Lỗi kết nối, vui lòng thử lại');
            } finally {
                this.setLoading(false);
            }
        }

        handleSuccess(response) {
            this.showNotification(`Đặt hàng thành công! Mã đơn: ${response.data.order_number}`, 'success');
            
            // Clear cart data from session
            $.post(MyPhamCheckout.ajax_url, { action: 'mypham_clear_cart' });
            
            // Redirect after delay
            setTimeout(() => {
                if (response.data.redirect_url) {
                    window.location.href = response.data.redirect_url;
                } else {
                    window.location.href = MyPhamCheckout.home_url;
                }
            }, 2000);
        }

        handleError(message) {
            this.showNotification(message, 'error');
        }

        setLoading(loading) {
            if (loading) {
                const originalText = this.submitBtn.html();
                this.submitBtn.data('original-text', originalText);
                this.submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);
                this.showOverlay();
            } else {
                this.submitBtn.html(this.submitBtn.data('original-text')).prop('disabled', false);
                this.hideOverlay();
            }
        }

        showOverlay() {
            if ($('.checkout-loading-overlay').length) return;
            
            const overlay = $(`
                <div class="checkout-loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Đang xử lý đơn hàng...</div>
                </div>
            `);
            $('body').append(overlay);
        }

        hideOverlay() {
            $('.checkout-loading-overlay').fadeOut(300, function() {
                $(this).remove();
            });
        }

        showNotification(message, type = 'info') {
            const notification = $(`
                <div class="checkout-notification ${type}">
                    ${message}
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(() => notification.addClass('show'), 10);
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }

    // Initialize checkout when document is ready
    $(document).ready(function() {
        if (typeof MyPhamCheckout !== 'undefined') {
            window.checkoutManager = new CheckoutManager();
        }
    });

})(jQuery);