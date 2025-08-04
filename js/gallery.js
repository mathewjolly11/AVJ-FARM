$(document).ready(function() {
    // Initialize gallery filtering
    $('.gallery-filter-btn').on('click', function() {
        var filterValue = $(this).attr('data-filter');
        
        // Add active class to the clicked button
        $('.gallery-filter-btn').removeClass('active');
        $(this).addClass('active');
        
        if (filterValue === '*') {
            // Show all items
            $('.gallery-item').show();
        } else {
            // Hide all items then show only items with matching category
            $('.gallery-item').hide();
            $(filterValue).show();
        }
        
        return false;
    });

    // Initialize lightbox effect for gallery images
    $('.gallery-item-link').on('click', function(e) {
        e.preventDefault();
        const imgSrc = $(this).find('img').attr('src');
        const imgTitle = $(this).find('h5').text();
        
        // Create lightbox overlay
        const lightbox = $('<div class="gallery-lightbox"></div>');
        const lightboxContent = $('<div class="gallery-lightbox-content"></div>');
        const lightboxImg = $('<img src="' + imgSrc + '" alt="' + imgTitle + '">');
        const lightboxClose = $('<span class="gallery-lightbox-close">&times;</span>');
        const lightboxTitle = $('<div class="gallery-lightbox-title">' + imgTitle + '</div>');
        
        lightboxContent.append(lightboxImg);
        lightboxContent.append(lightboxTitle);
        lightbox.append(lightboxContent);
        lightbox.append(lightboxClose);
        
        $('body').append(lightbox);
        
        // Close lightbox when clicking close button or outside the image
        $('.gallery-lightbox, .gallery-lightbox-close').on('click', function() {
            $('.gallery-lightbox').remove();
        });
        
        // Prevent closing when clicking on the image itself
        $('.gallery-lightbox-content').on('click', function(e) {
            e.stopPropagation();
        });
    });

    // Animation script for elements with animate-on-scroll class
    $(window).scroll(function() {
        $('.animate-on-scroll').each(function() {
            var position = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();
            
            if (scroll > position - windowHeight + 100) {
                $(this).addClass('animated');
            }
        });
    }).scroll(); // Trigger scroll event to check elements on page load
});
