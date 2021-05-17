$(document).ready(function() {
    /**
     * Infinite..
     *
     * @type       {<type>}
     */
    var container = document.querySelector('.king-part-q-list');
    var msnry = new Masonry(container, {
        columnWidth: '.grid-sizer',
        itemSelector: '.box',
        percentPosition: true,
        visibleStyle: {
            transform: 'translateY(0)',
            opacity: 1
        },
        hiddenStyle: {
            transform: 'translateY(100px)',
            opacity: 0
        },
    });
    var ias = $.ias({
        container: ".container",
        item: ".box",
        pagination: ".king-page-links-list",
        next: ".king-page-next",
        delay: 300,
        negativeMargin: 200
    });
    ias.on('render', function(items) {
        $(items).css({
            opacity: 0
        });
    });
    ias.on('rendered', function(items) {
        msnry.appended(items);
        magnificPopup();
        $('[data-toggle="tooltip"]').tooltip();
    });
    ias.extension(new IASSpinnerExtension({
        html: '<div class="switch-loader"><span class="loader"></span></div>'
    }));
    ias.extension(new IASTriggerExtension({
        offset: '2',
        text: 'Load More'
    }));
    ias.extension(new IASNoneLeftExtension({
        html: '<div class="load-nomore"><span>End of the page.</span></div>'
    }));
    /**
     * Magnific popup.
     */
    function magnificPopup() {
        $('.ajax-popup-link').magnificPopup({
            type: 'ajax',
            closeOnBgClick: false,
            closeBtnInside: false,
            preloader: true,
            tLoading: '<div class="loader"></div>',
            removalDelay: 120,
            callbacks: {
                ajaxContentAdded: function() {
                    var video = document.getElementById('my-video');
                    if (video) {
                        videojs(video);
                    }
                },
                parseAjax: function(mfpResponse) {
                    mfpResponse.data = $(mfpResponse.data).find('.king-video, .rightview');
                },
            },
        });
        $('.ajax-popup-share').magnificPopup({
            type: 'ajax',
            closeOnBgClick: false,
            closeBtnInside: false,
            preloader: true,
            tLoading: '<div class="loader"></div>',
            removalDelay: 120,
            callbacks: {
                parseAjax: function(mfpResponse) {
                    mfpResponse.data = $(mfpResponse.data).find('.social-share');
                },
            },
        });
    }
    magnificPopup();
});