jQuery(document).ready(function($) {
    // Mobile menu toggle
    $('.mobile-menu-toggle').click(function() {
        $(this).toggleClass('active');
        $('.main-navigation').toggleClass('active');
    });

    // Smooth scroll to booking section
    $('a[href="#booking"]').click(function(e) {
        e.preventDefault();
        
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top - 100
        }, 500);
    });

    // Fixed header on scroll
    $(window).scroll(function() {
        if ($(window).scrollTop() > 100) {
            $('.site-header').addClass('fixed');
        } else {
            $('.site-header').removeClass('fixed');
        }
    });
});
