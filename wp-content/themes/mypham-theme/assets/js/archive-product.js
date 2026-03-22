document.addEventListener('DOMContentLoaded', function() {
    // State Management
    let currentFilters = {
        category: '<?php echo $current_term_slug ?: "all"; ?>',
        tag: '',
        min_price: 0,
        max_price: 100000000,
        orderby: '',
        paged: 1
    };

    // DOM Elements
    const productsGrid = document.getElementById('products-grid');
    const loadingSkeleton = document.getElementById('loading-skeleton');
    const resultsCount = document.getElementById('products-count');
    const paginationWrapper = document.getElementById('pagination-wrapper');
    const activeFiltersDiv = document.getElementById('activeFilters');
    const filtersList = document.getElementById('filtersList');
    const clearAllBtn = document.getElementById('clearAllFilters');

    // ========== INITIALIZATION ==========
    function init() {
        initCategoryFilters();
        initPriceFilters();
        initSorting();
        initViewToggle();
        initMobileSidebar();
        initActiveFilters();
        reattachEventListeners();
        updatePriceDisplay();
    }

    // ========== CATEGORY FILTERS ==========
    function initCategoryFilters() {
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active state
                document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('active'));
                this.classList.add('active');
                
                // Update filter
                currentFilters.category = this.dataset.slug || 'all';
                currentFilters.paged = 1;
                
                // Add to active filters
                addActiveFilter('category', this.dataset.name || 'Tất cả sản phẩm');
                
                loadProducts();
            });
        });
    }

    // ========== PRICE FILTERS ==========
    const minPriceSlider = document.getElementById('min-price');
    const maxPriceSlider = document.getElementById('max-price');
    const minPriceInput = document.getElementById('min-price-input');
    const maxPriceInput = document.getElementById('max-price-input');
    const rangeFill = document.getElementById('rangeFill');

    function updatePriceDisplay() {
        const minVal = parseInt(minPriceSlider.value);
        const maxVal = parseInt(maxPriceSlider.value);
        
        document.getElementById('min-price-value').textContent = formatPrice(minVal);
        document.getElementById('max-price-value').textContent = formatPrice(maxVal);
        minPriceInput.value = minVal;
        maxPriceInput.value = maxVal;
        
        // Update range fill
        if (rangeFill) {
            const minPercent = (minVal / 100000000) * 100;
            const maxPercent = (maxVal / 100000000) * 100;
            rangeFill.style.left = minPercent + '%';
            rangeFill.style.right = (100 - maxPercent) + '%';
        }
    }

    function syncSliders() {
        const minVal = parseInt(minPriceSlider.value);
        const maxVal = parseInt(maxPriceSlider.value);
        
        if (minVal > maxVal) {
            minPriceSlider.value = maxVal;
        }
        if (maxVal < minVal) {
            maxPriceSlider.value = minVal;
        }
        
        currentFilters.min_price = parseInt(minPriceSlider.value);
        currentFilters.max_price = parseInt(maxPriceSlider.value);
        updatePriceDisplay();
    }

    if (minPriceSlider) {
        minPriceSlider.addEventListener('input', syncSliders);
        maxPriceSlider.addEventListener('input', syncSliders);
    }

    if (minPriceInput) {
        minPriceInput.addEventListener('input', function() {
            let value = parseInt(this.value) || 0;
            if (value > 100000000) value = 100000000;
            minPriceSlider.value = value;
            syncSliders();
        });
    }

    if (maxPriceInput) {
        maxPriceInput.addEventListener('input', function() {
            let value = parseInt(this.value) || 100000000;
            if (value > 100000000) value = 100000000;
            maxPriceSlider.value = value;
            syncSliders();
        });
    }

    const applyPriceBtn = document.getElementById('apply-price-filter');
    if (applyPriceBtn) {
        applyPriceBtn.addEventListener('click', function() {
            currentFilters.paged = 1;
            addActiveFilter('price', `${formatPrice(currentFilters.min_price)} - ${formatPrice(currentFilters.max_price)}`);
            loadProducts();
        });
    }

    const clearPriceBtn = document.getElementById('clear-price-filter');
    if (clearPriceBtn) {
        clearPriceBtn.addEventListener('click', function() {
            currentFilters.min_price = 0;
            currentFilters.max_price = 100000000;
            minPriceSlider.value = 0;
            maxPriceSlider.value = 100000000;
            updatePriceDisplay();
            
            // Remove price filter from active filters
            removeActiveFilter('price');
            
            loadProducts();
        });
    }

    // ========== SORTING ==========
    const orderbySelect = document.getElementById('orderby-select');
    if (orderbySelect) {
        orderbySelect.addEventListener('change', function() {
            currentFilters.orderby = this.value;
            currentFilters.paged = 1;
            loadProducts();
        });
    }

    // ========== VIEW TOGGLE ==========
    function initViewToggle() {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const productsGridEl = document.querySelector('.products-grid');
                if (productsGridEl) {
                    productsGridEl.classList.remove('grid-view', 'list-view');
                    productsGridEl.classList.add(this.dataset.view + '-view');
                }
                
                // Save preference to localStorage
                localStorage.setItem('productView', this.dataset.view);
            });
        });
        
        // Load saved view preference
        const savedView = localStorage.getItem('productView');
        if (savedView) {
            const viewBtn = document.querySelector(`.view-btn[data-view="${savedView}"]`);
            if (viewBtn) viewBtn.click();
        }
    }

    // ========== MOBILE SIDEBAR ==========
    function initMobileSidebar() {
        const toggleBtn = document.getElementById('mobileFilterToggle');
        const sidebar = document.querySelector('.shop-sidebar');
        const closeBtn = document.getElementById('closeSidebar');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.add('open');
                document.body.style.overflow = 'hidden';
            });
        }
        
        if (closeBtn && sidebar) {
            closeBtn.addEventListener('click', () => {
                sidebar.classList.remove('open');
                document.body.style.overflow = '';
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('open');
                    document.body.style.overflow = '';
                }
            }
        });
    }

    // ========== ACTIVE FILTERS MANAGEMENT ==========
    const activeFiltersMap = new Map();

    function addActiveFilter(type, value) {
        activeFiltersMap.set(type, { type, value });
        updateActiveFiltersDisplay();
    }

    function removeActiveFilter(type) {
        activeFiltersMap.delete(type);
        updateActiveFiltersDisplay();
    }

    function updateActiveFiltersDisplay() {
        if (activeFiltersMap.size === 0) {
            activeFiltersDiv.style.display = 'none';
            return;
        }
        
        activeFiltersDiv.style.display = 'block';
        filtersList.innerHTML = '';
        
        activeFiltersMap.forEach((filter, key) => {
            const filterTag = document.createElement('div');
            filterTag.className = 'filter-tag';
            filterTag.innerHTML = `
                <span>${filter.value}</span>
                <button class="remove-filter" data-type="${key}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            filtersList.appendChild(filterTag);
        });
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.remove-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.dataset.type;
                removeActiveFilter(type);
                
                // Reset corresponding filter
                if (type === 'category') {
                    currentFilters.category = 'all';
                    document.querySelectorAll('.category-item').forEach(cat => {
                        cat.classList.remove('active');
                        if (cat.dataset.slug === 'all') cat.classList.add('active');
                    });
                } else if (type === 'price') {
                    currentFilters.min_price = 0;
                    currentFilters.max_price = 100000000;
                    minPriceSlider.value = 0;
                    maxPriceSlider.value = 100000000;
                    updatePriceDisplay();
                }
                
                loadProducts();
            });
        });
    }

    function initActiveFilters() {
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', () => {
                activeFiltersMap.clear();
                updateActiveFiltersDisplay();
                
                // Reset all filters
                currentFilters = {
                    category: 'all',
                    tag: '',
                    min_price: 0,
                    max_price: 100000000,
                    orderby: '',
                    paged: 1
                };
                
                // Reset UI
                document.querySelectorAll('.category-item').forEach(cat => {
                    cat.classList.remove('active');
                    if (cat.dataset.slug === 'all') cat.classList.add('active');
                });
                
                if (minPriceSlider && maxPriceSlider) {
                    minPriceSlider.value = 0;
                    maxPriceSlider.value = 100000000;
                    updatePriceDisplay();
                }
                
                if (orderbySelect) orderbySelect.value = '';
                
                loadProducts();
            });
        }
    }

    // ========== CORE AJAX FUNCTION ==========
    function loadProducts() {
        if (!productsGrid) return;
        
        productsGrid.style.opacity = '0.3';
        if (loadingSkeleton) loadingSkeleton.style.display = 'grid';
        
        const params = new URLSearchParams({
            action: 'filter_products',
            category: currentFilters.category,
            tag: currentFilters.tag,
            min_price: currentFilters.min_price,
            max_price: currentFilters.max_price,
            orderby: currentFilters.orderby,
            paged: currentFilters.paged,
            nonce: ajax_object.nonce
        });
        
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productsGrid.style.opacity = '0';
                setTimeout(() => {
                    productsGrid.innerHTML = data.data.html;
                    if (resultsCount) resultsCount.textContent = data.data.total;
                    updatePagination(data.data);
                    reattachEventListeners();
                    productsGrid.style.opacity = '1';
                }, 200);
            } else {
                throw new Error(data.data || 'Lỗi tải sản phẩm');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            productsGrid.innerHTML = `
                <div class="no-products premium-no-products">
                    <div class="no-products-animation">
                        <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #f56565;"></i>
                    </div>
                    <h3>Đã có lỗi xảy ra</h3>
                    <p>${error.message}</p>
                    <button onclick="location.reload()" class="btn-explore">
                        <i class="fas fa-sync-alt"></i>
                        <span>Tải lại trang</span>
                    </button>
                </div>
            `;
        })
        .finally(() => {
            if (loadingSkeleton) loadingSkeleton.style.display = 'none';
        });
    }

    // ========== PAGINATION ==========
    function updatePagination(data) {
        if (!paginationWrapper) return;
        
        if (data.max_pages > 1) {
            paginationWrapper.style.display = 'flex';
            let paginationHtml = '<ul class="pagination">';
            
            // Previous button
            if (data.current_page > 1) {
                paginationHtml += `
                    <li>
                        <a href="#" class="page-link" data-page="${data.current_page - 1}">
                            <i class="fas fa-chevron-left"></i>
                            <span>Trước</span>
                        </a>
                    </li>
                `;
            }
            
            // Page numbers
            const startPage = Math.max(1, data.current_page - 2);
            const endPage = Math.min(data.max_pages, data.current_page + 2);
            
            if (startPage > 1) {
                paginationHtml += `<li><a href="#" class="page-link" data-page="1">1</a></li>`;
                if (startPage > 2) paginationHtml += `<li><span>...</span></li>`;
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === data.current_page) {
                    paginationHtml += `<li class="current"><span>${i}</span></li>`;
                } else {
                    paginationHtml += `<li><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
                }
            }
            
            if (endPage < data.max_pages) {
                if (endPage < data.max_pages - 1) paginationHtml += `<li><span>...</span></li>`;
                paginationHtml += `<li><a href="#" class="page-link" data-page="${data.max_pages}">${data.max_pages}</a></li>`;
            }
            
            // Next button
            if (data.current_page < data.max_pages) {
                paginationHtml += `
                    <li>
                        <a href="#" class="page-link" data-page="${data.current_page + 1}">
                            <span>Sau</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                `;
            }
            
            paginationHtml += '</ul>';
            paginationWrapper.innerHTML = paginationHtml;
            
            // Add event listeners to pagination links
            document.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentFilters.paged = parseInt(this.dataset.page);
                    loadProducts();
                    
                    // Scroll to top of products
                    const productsContainer = document.querySelector('.products-container');
                    if (productsContainer) {
                        productsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        } else {
            paginationWrapper.style.display = 'none';
        }
    }

    // ========== EVENT LISTENERS REATTACHMENT ==========
    function reattachEventListeners() {
        // Quick Add to Cart
        document.querySelectorAll('.quick-add, .ultra-quick-add').forEach(btn => {
            btn.removeEventListener('click', handleAddToCart);
            btn.addEventListener('click', handleAddToCart);
        });
        
        // Wishlist
        document.querySelectorAll('.wishlist-btn, .ultra-wishlist').forEach(btn => {
            btn.removeEventListener('click', handleWishlist);
            btn.addEventListener('click', handleWishlist);
        });
        
        // Buy Now
        document.querySelectorAll('.ultra-buy-btn, .btn-buy-now').forEach(btn => {
            btn.removeEventListener('click', handleBuyNow);
            btn.addEventListener('click', handleBuyNow);
        });
        
        // Quick View
        document.querySelectorAll('.quick-view-trigger').forEach(trigger => {
            trigger.removeEventListener('click', handleQuickView);
            trigger.addEventListener('click', handleQuickView);
        });
        
        // Compare
        document.querySelectorAll('.compare-btn').forEach(btn => {
            btn.removeEventListener('click', handleCompare);
            btn.addEventListener('click', handleCompare);
        });
    }
    
    function handleAddToCart(e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = this.dataset.productId;
        ultraAddToCart(productId, this);
    }
    
    function handleWishlist(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.toggle('active');
        const icon = this.querySelector('i');
        if (icon) {
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
        }
        
        if (this.classList.contains('active')) {
            showNotification('❤️ Đã thêm vào yêu thích!', 'success');
        } else {
            showNotification('💔 Đã xóa khỏi yêu thích', 'info');
        }
    }
    
    function handleBuyNow(e) {
        e.preventDefault();
        e.stopPropagation();
        createRipple(this, e);
        showNotification('🚀 Chuyển đến thanh toán...', 'info');
        setTimeout(() => {
            window.location.href = '/checkout';
        }, 1000);
    }
    
    function handleQuickView(e) {
        e.preventDefault();
        const productId = this.dataset.productId;
        showNotification('🔍 Đang tải thông tin sản phẩm...', 'info');
        // Implement quick view modal logic
    }
    
    function handleCompare(e) {
        e.preventDefault();
        const productId = this.dataset.productId;
        showNotification('📊 Đã thêm vào danh sách so sánh', 'success');
    }

    // ========== ULTRA EFFECTS ==========
    function ultraAddToCart(productId, button) {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<div class="spinner"><i class="fas fa-spinner fa-pulse"></i></div>';
        button.disabled = true;
        button.style.transform = 'scale(0.95)';
        
        createParticles(button);
        
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-check"></i> Đã thêm!';
            button.style.background = 'linear-gradient(135deg, #48bb78, #38a169)';
            button.style.transform = 'scale(1.05)';
            
            showNotification('🎉 Đã thêm vào giỏ hàng thành công!', 'success');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.style.background = '';
                button.style.transform = '';
                button.disabled = false;
            }, 1500);
        }, 800);
    }
    
    function createParticles(element) {
        const rect = element.getBoundingClientRect();
        for (let i = 0; i < 12; i++) {
            const particle = document.createElement('div');
            particle.className = 'ultra-particle';
            const angle = (Math.PI * 2 * i) / 12;
            const velocity = 80 + Math.random() * 60;
            const vx = Math.cos(angle) * velocity;
            const vy = Math.sin(angle) * velocity;
            
            particle.style.cssText = `
                position: fixed;
                width: 8px;
                height: 8px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 50%;
                pointer-events: none;
                z-index: 10000;
                top: ${rect.top + rect.height / 2}px;
                left: ${rect.left + rect.width / 2}px;
            `;
            
            document.body.appendChild(particle);
            
            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${vx}px, ${vy}px) scale(0)`, opacity: 0 }
            ], {
                duration: 800 + Math.random() * 400,
                easing: 'cubic-bezier(0.2, 0.9, 0.4, 1)'
            });
            
            setTimeout(() => particle.remove(), 1000);
        }
    }
    
    function createRipple(element, event) {
        const ripple = document.createElement('span');
        ripple.className = 'ultra-ripple';
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.backgroundColor = 'rgba(255,255,255,0.6)';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple 0.6s linear';
        ripple.style.pointerEvents = 'none';
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }
    
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = 'notification';
        const icons = {
            success: '✅',
            error: '❌',
            info: 'ℹ️',
            warning: '⚠️'
        };
        notification.innerHTML = `${icons[type] || '✅'} ${message}`;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 10);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + '₫';
    }

    // Add ripple animation style
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }
        .ultra-particle {
            box-shadow: 0 0 6px currentColor;
        }
        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.1));
            padding: 0.5rem 0.75rem;
            border-radius: 100px;
            font-size: 0.875rem;
        }
        .remove-filter {
            background: none;
            border: none;
            cursor: pointer;
            color: #f56565;
            display: flex;
            align-items: center;
            padding: 0;
        }
        .remove-filter:hover {
            transform: scale(1.1);
        }
        .products-grid.grid-view .product-card {
            grid-column: auto;
        }
        .products-grid.list-view {
            display: flex;
            flex-direction: column;
        }
        .products-grid.list-view .product-card {
            display: flex;
            flex-direction: row;
            gap: 2rem;
        }
        .products-grid.list-view .product-image {
            width: 280px;
            flex-shrink: 0;
        }
        .products-grid.list-view .product-info {
            flex: 1;
        }
        @media (max-width: 768px) {
            .products-grid.list-view .product-card {
                flex-direction: column;
            }
            .products-grid.list-view .product-image {
                width: 100%;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize
    init();
});