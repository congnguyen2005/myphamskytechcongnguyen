/**
 * Home Page Interactive Features
 */
document.addEventListener('DOMContentLoaded', function() {
    // Quick Add to Cart
    const quickAddButtons = document.querySelectorAll('.quick-add');
    
    quickAddButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const originalText = this.dataset.originalText;
            
            // Add loading state
            const icon = this.querySelector('i');
            const originalIcon = icon.outerHTML;
            icon.className = 'fas fa-spinner fa-spin';
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
            this.style.opacity = '0.7';
            
            // Simulate AJAX add to cart
            setTimeout(() => {
                // Reset button
                this.innerHTML = originalIcon + ' Đã thêm!';
                this.style.background = 'linear-gradient(45deg, #4ecdc4, #44a08d)';
                this.style.color = 'white';
                
                setTimeout(() => {
                    this.innerHTML = originalIcon + ' ' + originalText;
                    this.style.background = 'rgba(255,255,255,0.95)';
                    this.style.color = '#333';
                    this.style.opacity = '1';
                }, 1500);
                
                // Show notification (optional)
                showNotification('Đã thêm vào giỏ hàng!');
            }, 1200);
        });
    });
    
    // Buy Now buttons
    const buyNowButtons = document.querySelectorAll('.btn-buy-now-small');
    buyNowButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            // Redirect to checkout with product
            window.location.href = `/checkout/?add-to-cart=${productId}`;
        });
    });
    
    // Notification function
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(78,205,196,0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transform: translateX(400px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 400);
        }, 3000);
    }
});