jQuery(document).ready(function($) {
    // Initialize slider
    $('.my-image-slider .slide:first-child').show();

    // Autoplay interval in milliseconds (e.g., 3000 = 3 seconds)
    var autoplayInterval = 4000;
    var autoplayTimer;

    // Function to switch to the next slide
    function nextSlide() {
        var currentSlide = $('.my-image-slider .slide:visible');
        var nextSlide = currentSlide.next('.slide');

        if (nextSlide.length === 0) {
            nextSlide = $('.my-image-slider .slide:first-child');
        }

        currentSlide.hide();
        nextSlide.show();
    }

    // Function to start autoplay
    function startAutoplay() {
        autoplayTimer = setInterval(nextSlide, autoplayInterval);
    }

    // Start autoplay
    startAutoplay();

    // Pause autoplay on hover (optional)
    $('.my-image-slider').hover(function() {
        clearInterval(autoplayTimer);
    }, function() {
        startAutoplay();
    });

    // Previous slide
    $('.my-image-slider .prev').click(function() {
        var currentSlide = $('.my-image-slider .slide:visible');
        var prevSlide = currentSlide.prev('.slide');

        if (prevSlide.length === 0) {
            prevSlide = $('.my-image-slider .slide:last-child');
        }

        currentSlide.hide();
        prevSlide.show();
    });

    // Next slide
    $('.my-image-slider .next').click(function() {
        nextSlide();
    });
});
