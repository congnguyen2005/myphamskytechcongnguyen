/**
 * Category Product Page Scripts
 * Version: 1.0.0
 */

(function($) {
    'use strict';
    
    // DOM Ready
    $(document).ready(function() {
        
        // ==================== FEATURED PRODUCTS SLIDER ====================
        const slider = document.getElementById('featured-products-slider');
        const prevBtn = document.getElementById('featured-prev');
        const nextBtn = document.getElementById('featured-next');
        
        if (slider && prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                slider.scrollBy({ left: -300, behavior: 'smooth' });
            });
            
            nextBtn.addEventListener('click', () => {
                slider.scrollBy({ left: 300, behavior: 'smooth' });
            });
        }
        
        // ==================== VIEW TOGGLE (GRID/LIST) ====================
        const viewBtns = document.querySelectorAll('.view-btn');
        const productsGrid = document.getElementById('products-grid');
        
        if (viewBtns.length && productsGrid) {
            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    viewBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const view = this.dataset.view;
                    productsGrid.classList.remove('grid-view', 'list-view');
                    productsGrid.classList.add(view + '-view');
                    
                    // Save preference to localStorage
                    localStorage.setItem('categoryViewPreference', view);
                });
            });
            
            // Load saved view preference
            const savedView = localStorage.getItem('categoryViewPreference');
            if (savedView) {
                const activeBtn = document.querySelector(`.view-btn[data-view="${savedView}"]`);
                if (activeBtn) {
                    activeBtn.click();
                }
            }
        }
        
        // ==================== MOBILE FILTER TOGGLE ====================
        const filterToggle = document.getElementById('filterToggleBtn');
        const filterSidebar = document.getElementById('filterSidebar');
        
        if (filterToggle && filterSidebar) {
            filterToggle.addEventListener('click', () => {
                filterSidebar.classList.toggle('active');
                document.body.style.overflow = filterSidebar.classList.contains('active') ? 'hidden' : '';
            });
            
            // Close filter when clicking outside
            document.addEventListener('click', (e) => {
                if (filterSidebar.classList.contains('active') && 
                    !filterSidebar.contains(e.target) && 
                    !filterToggle.contains(e.target)) {
                    filterSidebar.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }
        
        // ==================== PRICE RANGE SLIDER ====================
        const minSlider = document.getElementById('price-min');
        const maxSlider = document.getElementById('price-max');
        const minValue = document.getElementById('price-min-value');
        const maxValue = document.getElementById('price-max-value');
        const minInput = document.getElementById('price-min-input');
        const maxInput = document.getElementById('price-max-input');
        
        function formatPrice(price) {
            return price.toLocaleString('vi-VN') + '₫';
        }
        
        function updatePriceValues() {
            if (minSlider && maxSlider) {
                let min = parseInt(minSlider.value);
                let max = parseInt(maxSlider.value);
                
                if (min > max) {
                    [min, max] = [max, min];
                }
                
                minValue.textContent = formatPrice(min);
                maxValue.textContent = formatPrice(max);
                
                if (minInput) minInput.value = min;
                if (maxInput) maxInput.value = max;
            }
        }
        
        if (minSlider && maxSlider) {
            minSlider.addEventListener('input', function() {
                if (parseInt(this.value) > parseInt(maxSlider.value)) {
                    maxSlider.value = this.value;
                }
                updatePriceValues();
            });
            
            maxSlider.addEventListener('input', function() {
                if (parseInt(this.value) < parseInt(minSlider.value)) {
                    minSlider.value = this.value;
                }
                updatePriceValues();
            });
            
            updatePriceValues();
        }
        
        // Price input sync
        if (minInput && maxInput) {
            minInput.addEventListener('change', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > parseInt(maxInput.value)) value = parseInt(maxInput.value);
                minSlider.value = value;
                updatePriceValues();
            });
            
            maxInput.addEventListener('change', function() {
                let value = parseInt(this.value) || 10000000;
                if (value > 10000000) value = 10000000;
                if (value < parseInt(minInput.value)) value = parseInt(minInput.value);
                maxSlider.value = value;
                updatePriceValues();
            });
        }
        
        // Apply price filter
        const applyPriceBtn = document.getElementById('apply-price-filter');
        if (applyPriceBtn) {
            applyPriceBtn.addEventListener('click', function() {
                const minPrice = parseInt(minSlider.value);
                const maxPrice = parseInt(maxSlider.value);
                const currentUrl = new URL(window.location.href);
                
                currentUrl.searchParams.set('min_price', minPrice);
                currentUrl.searchParams.set('max_price', maxPrice);
                currentUrl.searchParams.set('paged', '1');
                
                window.location.href = currentUrl.toString();
            });
        }
        
        // ==================== SORT PRODUCTS ====================
        const sortSelect = document.getElementById('sort-products');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('orderby', this.value);
                } else {
                    url.searchParams.delete('orderby');
                }
                url.searchParams.set('paged', '1');
                window.location.href = url.toString();
            });
            
            // Set selected value from URL
            const urlParams = new URLSearchParams(window.location.search);
            const orderby = urlParams.get('orderby');
            if (orderby && sortSelect.querySelector(`option[value="${orderby}"]`)) {
                sortSelect.value = orderby;
            }
        }
        
        // ==================== RESET FILTERS ====================
        const resetBtn = document.getElementById('resetFiltersBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                const url = new URL(window.location.href);
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
                url.searchParams.delete('orderby');
                url.searchParams.delete('paged');
                window.location.href = url.toString();
            });
        }
        
        // ==================== QUICK ADD TO CART ====================
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = 'category-notification-toast';
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            `;
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#3b82f6'};
                color: white;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 10000;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                animation: slideInRight 0.3s ease;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        // Add animation styles if not exists
        if (!document.querySelector('#category-animation-styles')) {
            const style = document.createElement('style');
            style.id = 'category-animation-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Handle quick add buttons
        $(document).on('click', '.quick-add, .btn-quick-add', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const productId = $btn.data('product-id');
            const originalHtml = $btn.html();
            
            if (!productId) return;
            
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Đang thêm...');
            $btn.prop('disabled', true);
            
            // Call AJAX add to cart
            $.post(MyPhamData.ajax_url, {
                action: 'mypham_add_to_cart',
                product_id: productId,
                quantity: 1
            }, function(response) {
                if (response.success) {
                    $btn.html('<i class="fas fa-check"></i> Đã thêm');
                    setTimeout(() => {
                        $btn.html(originalHtml);
                        $btn.prop('disabled', false);
                    }, 1500);
                    showNotification('Đã thêm vào giỏ hàng!', 'success');
                    
                    // Update cart count
                    if (typeof updateCartCount === 'function') {
                        updateCartCount(response.data.cart_count);
                    } else if (window.myphamUpdateCartCount) {
                        window.myphamUpdateCartCount(response.data.cart_count);
                    }
                } else {
                    $btn.html(originalHtml);
                    $btn.prop('disabled', false);
                    showNotification(response.data.message || 'Có lỗi xảy ra', 'error');
                }
            }).fail(function() {
                $btn.html(originalHtml);
                $btn.prop('disabled', false);
                showNotification('Lỗi kết nối', 'error');
            });
        });
        
        // ==================== WISHLIST TOGGLE ====================
        $(document).on('click', '.add-wishlist', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $icon = $btn.find('i');
            const isActive = $icon.hasClass('fas');
            
            if (isActive) {
                $icon.removeClass('fas').addClass('far');
                showNotification('Đã xóa khỏi danh sách yêu thích', 'info');
            } else {
                $icon.removeClass('far').addClass('fas');
                showNotification('Đã thêm vào danh sách yêu thích', 'success');
            }
            
            // Optional: Send AJAX to save wishlist
            const productId = $btn.data('product-id');
            if (productId && typeof mypham_add_to_wishlist === 'function') {
                // Call wishlist AJAX if available
            }
        });
        
        // ==================== QUICK VIEW ====================
        $(document).on('click', '.quick-view', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            if (productId) {
                // Open quick view modal (implement if needed)
                showNotification('Tính năng đang phát triển', 'info');
            }
        });
        
        // ==================== VIEW ALL SUBCATEGORIES ====================
        const viewAllBtn = document.getElementById('viewAllSubcatsBtn');
        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Scroll to filter sidebar and expand subcategories
                const filterSidebar = document.getElementById('filterSidebar');
                if (filterSidebar) {
                    filterSidebar.scrollIntoView({ behavior: 'smooth' });
                    if (window.innerWidth < 992) {
                        filterSidebar.classList.add('active');
                    }
                }
            });
        }
        
        // ==================== BRAND FILTERS ====================
        $('.brand-filter').on('change', function() {
            const selectedBrands = $('.brand-filter:checked').map(function() {
                return $(this).val();
            }).get();
            
            const url = new URL(window.location.href);
            if (selectedBrands.length) {
                url.searchParams.set('brands', selectedBrands.join(','));
            } else {
                url.searchParams.delete('brands');
            }
            url.searchParams.set('paged', '1');
            window.location.href = url.toString();
        });
        
        // Set checked brands from URL
        const urlParams = new URLSearchParams(window.location.search);
        const brandsParam = urlParams.get('brands');
        if (brandsParam) {
            const brands = brandsParam.split(',');
            $('.brand-filter').each(function() {
                if (brands.includes($(this).val())) {
                    $(this).prop('checked', true);
                }
            });
        }
        
        // ==================== LAZY LOAD IMAGES ====================
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('.product-image-wrapper img, .card-image img');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(img => imageObserver.observe(img));
        }
        
        // ==================== ADD TO CART FROM URL (MUA NGAY) ====================
        const urlAddToCart = urlParams.get('add-to-cart');
        if (urlAddToCart) {
            const buyNowBtn = document.querySelector(`.quick-add[data-product-id="${urlAddToCart}"], .btn-quick-add[data-product-id="${urlAddToCart}"]`);
            if (buyNowBtn) {
                setTimeout(() => buyNowBtn.click(), 500);
            }
        }
        
    });
    
})(jQuery);