jQuery(document).ready(function($) {
    'use strict';
    
    // Card hover effect
    $('.news-card').on('mouseenter', function() {
        $(this).addClass('animate');
    }).on('mouseleave', function() {
        $(this).removeClass('animate');
    });
    
    // Search focus effect
    $('.compact-search input').on('focus', function() {
        $(this).closest('.compact-search').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.compact-search').removeClass('focused');
    });
    
    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src') || img.src;
                    if (img.src !== src) {
                        img.src = src;
                    }
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        $('.card-image img').each(function() {
            const img = $(this)[0];
            if (!img.src || img.src.includes('placehold.co')) {
                const dataSrc = img.getAttribute('data-src');
                if (dataSrc) {
                    img.setAttribute('data-src', img.src);
                    img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250"%3E%3Crect width="400" height="250" fill="%23f0f0f0"/%3E%3C/svg%3E';
                    imageObserver.observe(img);
                }
            }
        });
    }
    
    // Smooth scroll for pagination
    $('.pagination a').on('click', function(e) {
        const $this = $(this);
        if (!$this.parent().hasClass('current') && !$this.hasClass('dots')) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.news-grid').offset().top - 80
            }, 400);
        }
    });
    
    // Mobile sidebar toggle
    $('.widget-title').on('click', function(e) {
        if ($(window).width() < 768) {
            e.preventDefault();
            $(this).next().slideToggle(200);
            $(this).toggleClass('expanded');
        }
    });
});