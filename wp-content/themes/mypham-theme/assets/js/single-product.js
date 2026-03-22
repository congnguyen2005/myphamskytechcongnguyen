document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail gallery
    const thumbnails = document.querySelectorAll('.thumbnail-gallery img');
    const mainImage = document.querySelector('.main-image img');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            mainImage.src = this.src.replace(/-\d+x\d+/, ''); // Update main image
        });
    });
    
    // Variant selector
    document.querySelectorAll('.variant-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Quantity controls
    const qtyInput = document.querySelector('.qty-input');
    const minusBtn = document.querySelector('.qty-btn.minus');
    const plusBtn = document.querySelector('.qty-btn.plus');
    
    minusBtn.addEventListener('click', () => {
        if (qtyInput.value > 1) qtyInput.value--;
    });
    
    plusBtn.addEventListener('click', () => qtyInput.value++);
    
    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });
    
    // Rating stars
    document.querySelectorAll('.stars-input i').forEach(star => {
        star.addEventListener('click', function() {
            const value = this.dataset.value;
            this.parentElement.querySelectorAll('i').forEach((s, index) => {
                s.classList.toggle('far', index >= value);
                s.classList.toggle('fas', index < value);
                s.classList.toggle('active', index < value);
            });
        });
    });
});