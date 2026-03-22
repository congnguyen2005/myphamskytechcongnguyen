/**
 * Header JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // ========== MOBILE MENU ==========
        var $mobileMenuToggle = $('.mobile-menu-toggle');
        var $mobileMenuPanel = $('.mobile-menu-panel');
        var $mobileMenuOverlay = $('.mobile-menu-overlay');
        var $mobileMenuClose = $('.mobile-menu-close');
        
        function openMobileMenu() {
            $mobileMenuPanel.addClass('active');
            $mobileMenuOverlay.addClass('active');
            $('body').css('overflow', 'hidden');
            $mobileMenuToggle.addClass('active');
        }
        
        function closeMobileMenu() {
            $mobileMenuPanel.removeClass('active');
            $mobileMenuOverlay.removeClass('active');
            $('body').css('overflow', '');
            $mobileMenuToggle.removeClass('active');
        }
        
        $mobileMenuToggle.on('click', function(e) {
            e.preventDefault();
            if ($mobileMenuPanel.hasClass('active')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
        
        $mobileMenuClose.on('click', closeMobileMenu);
        $mobileMenuOverlay.on('click', closeMobileMenu);
        
        // ========== MINI CART ==========
        var $miniCartToggle = $('.mini-cart-toggle');
        var $miniCartPanel = $('.mini-cart-panel');
        var $miniCartOverlay = $('.mini-cart-overlay');
        var $closeMiniCart = $('.close-mini-cart');
        
        function openMiniCart() {
            $miniCartPanel.addClass('active');
            $miniCartOverlay.addClass('active');
            $('body').css('overflow', 'hidden');
        }
        
        function closeMiniCart() {
            $miniCartPanel.removeClass('active');
            $miniCartOverlay.removeClass('active');
            $('body').css('overflow', '');
        }
        
        $miniCartToggle.on('click', function(e) {
            e.preventDefault();
            openMiniCart();
            refreshMiniCart();
        });
        
        $closeMiniCart.on('click', closeMiniCart);
        $miniCartOverlay.on('click', closeMiniCart);
        
        function refreshMiniCart() {
            $.ajax({
                url: MyPhamData.ajax_url,
                type: 'POST',
                data: {
                    action: 'mypham_get_mini_cart'
                },
                success: function(response) {
                    $('.mini-cart-content').html(response);
                }
            });
        }
        
        // ========== MOBILE SUBMENU ==========
        $('.mobile-menu .menu-item-has-children > a').on('click', function(e) {
            e.preventDefault();
            var $parent = $(this).parent();
            var $submenu = $parent.children('.sub-menu');
            
            if ($submenu.length) {
                $submenu.slideToggle(300);
                $parent.toggleClass('open');
            }
        });
        
        // ========== LIVE SEARCH ==========
        var searchTimer;
        var $searchField = $('.search-field');
        var $searchSuggestions = $('.search-suggestions');
        
        $searchField.on('input', function() {
            var query = $(this).val();
            
            clearTimeout(searchTimer);
            
            if (query.length < 2) {
                $searchSuggestions.removeClass('active').empty();
                return;
            }
            
            searchTimer = setTimeout(function() {
                $.ajax({
                    url: MyPhamData.ajax_url,
                    type: 'GET',
                    data: {
                        action: 'mypham_live_search',
                        q: query
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            var html = '<div class="suggestions-list">';
                            $.each(response, function(i, item) {
                                html += '<a href="' + item.permalink + '" class="suggestion-item">';
                                if (item.thumb) {
                                    html += '<img src="' + item.thumb + '" alt="' + item.title + '">';
                                } else {
                                    html += '<div class="suggestion-icon"><i class="fas fa-search"></i></div>';
                                }
                                html += '<span class="suggestion-title">' + item.title + '</span>';
                                html += '</a>';
                            });
                            html += '</div>';
                            $searchSuggestions.html(html).addClass('active');
                        } else {
                            $searchSuggestions.html('<div class="no-results">Không tìm thấy kết quả</div>').addClass('active');
                        }
                    }
                });
            }, 300);
        });
        
        // Close suggestions when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-form-wrapper').length) {
                $searchSuggestions.removeClass('active').empty();
            }
        });
        
        // ========== STICKY HEADER ==========
        var $header = $('#site-header');
        var headerOffset = $header.offset().top;
        var isSticky = false;
        
        function checkSticky() {
            if (window.scrollY > headerOffset && !isSticky) {
                $header.addClass('sticky');
                isSticky = true;
            } else if (window.scrollY <= headerOffset && isSticky) {
                $header.removeClass('sticky');
                isSticky = false;
            }
        }
        
        $(window).on('scroll', checkSticky);
        checkSticky();
        
        // ========== HEADER PADDING ==========
        function updateBodyPadding() {
            var headerHeight = $header.outerHeight();
            $('body').css('padding-top', headerHeight + 'px');
        }
        
        updateBodyPadding();
        $(window).on('resize', updateBodyPadding);
        
        // ========== BACK TO TOP ==========
        var $backToTop = $('#backToTop');
        
        function checkBackToTop() {
            if ($(window).scrollTop() > 300) {
                $backToTop.addClass('show');
            } else {
                $backToTop.removeClass('show');
            }
        }
        
        $backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 500);
        });
        
        $(window).on('scroll', checkBackToTop);
        checkBackToTop();
        
    });
    
})(jQuery);