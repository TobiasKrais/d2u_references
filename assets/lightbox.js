/**
 * D2U References Lightbox - Vanilla JS gallery lightbox.
 */
(function() {
    var currentGalleryItems = [];
    var currentIndex = 0;

    function ensureLightboxOverlay() {
        if (document.getElementById('d2u-lightbox-overlay')) {
            return;
        }

        var overlay = document.createElement('div');
        overlay.id = 'd2u-lightbox-overlay';
        overlay.innerHTML =
            '<div class="d2u-lightbox-close" aria-label="Close">&times;</div>' +
            '<div class="d2u-lightbox-prev" aria-label="Previous">&lsaquo;</div>' +
            '<div class="d2u-lightbox-next" aria-label="Next">&rsaquo;</div>' +
            '<img class="d2u-lightbox-image" src="" alt="">' +
            '<div class="d2u-lightbox-title"></div>';
        document.body.appendChild(overlay);

        overlay.querySelector('.d2u-lightbox-close').addEventListener('click', window.d2uLightboxClose);
        overlay.addEventListener('click', function(event) {
            if (event.target === overlay) {
                window.d2uLightboxClose();
            }
        });
        overlay.querySelector('.d2u-lightbox-prev').addEventListener('click', function(event) {
            event.stopPropagation();
            window.d2uLightboxNavigate(-1);
        });
        overlay.querySelector('.d2u-lightbox-next').addEventListener('click', function(event) {
            event.stopPropagation();
            window.d2uLightboxNavigate(1);
        });

        document.addEventListener('keydown', function(event) {
            var overlayElement = document.getElementById('d2u-lightbox-overlay');
            if (overlayElement && overlayElement.style.display === 'flex') {
                if (event.key === 'Escape') {
                    window.d2uLightboxClose();
                } else if (event.key === 'ArrowLeft') {
                    window.d2uLightboxNavigate(-1);
                } else if (event.key === 'ArrowRight') {
                    window.d2uLightboxNavigate(1);
                }
            }
        });
    }

    function showLightboxImage() {
        var item = currentGalleryItems[currentIndex];
        var overlay = document.getElementById('d2u-lightbox-overlay');
        overlay.querySelector('.d2u-lightbox-image').src = item.href;
        overlay.querySelector('.d2u-lightbox-image').alt = item.getAttribute('data-title') || '';
        overlay.querySelector('.d2u-lightbox-title').textContent = item.getAttribute('data-title') || '';

        var prevButton = overlay.querySelector('.d2u-lightbox-prev');
        var nextButton = overlay.querySelector('.d2u-lightbox-next');
        if (currentGalleryItems.length <= 1) {
            prevButton.style.display = 'none';
            nextButton.style.display = 'none';
        } else {
            prevButton.style.display = '';
            nextButton.style.display = '';
        }
    }

    window.d2uLightboxOpen = function(galleryId, element) {
        ensureLightboxOverlay();
        currentGalleryItems = Array.from(document.querySelectorAll('[data-d2u-gallery="' + galleryId + '"]'));
        currentIndex = currentGalleryItems.indexOf(element);
        if (currentIndex < 0) {
            currentIndex = 0;
        }
        showLightboxImage();
        document.getElementById('d2u-lightbox-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.d2uLightboxClose = function() {
        document.getElementById('d2u-lightbox-overlay').style.display = 'none';
        document.body.style.overflow = '';
    };

    window.d2uLightboxNavigate = function(direction) {
        currentIndex += direction;
        if (currentIndex < 0) {
            currentIndex = currentGalleryItems.length - 1;
        }
        if (currentIndex >= currentGalleryItems.length) {
            currentIndex = 0;
        }
        showLightboxImage();
    };
})();