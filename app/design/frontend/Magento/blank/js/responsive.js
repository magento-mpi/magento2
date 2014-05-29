/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

;
(function($) {
    'use strict';

    $(document).ready(function(){
        mediaCheck({
            media: '(min-width: 768px)',
            // Switch to Desktop Version
            entry: function() {
                // minicart
                $('.action.showcart').removeClass('is-disabled');

                (function() {

                    var productInfoMain = $('.product.info.main'),
                        productInfoAdditional = $("#product-info-additional");

                    if(productInfoAdditional.length) {
                        productInfoAdditional.addClass("hidden");
                        productInfoMain.removeClass("responsive");
                    }

                })();

                var galleryElement = $('[data-role=media-gallery]');
                if (galleryElement.length && galleryElement.data('mageZoom')) {
                    galleryElement.zoom('enable');
                }
                if (galleryElement.length && galleryElement.data('mageGallery')) {
                    galleryElement.gallery("option","disableLinks",true);
                    galleryElement.gallery("option","showNav",false);
                    galleryElement.gallery("option","showThumbs",true);
                }

                setTimeout(function(){
                    $(".product.data.items").tabs("option","openOnFocus",true);
                }, 500);
            },
            // Switch to Mobile Version
            exit: function() {
                // minicart
                $('.action.showcart').addClass('is-disabled');

                $('.action.showcart').on( "click", function() {
                    if ($(this).hasClass('is-disabled')) {
                        window.location = $(this).attr("href");
                    }
                });

                $('.action.toggle.checkout.progress')
                    .on('click.gotoCheckoutProgress', function(e){
                        var myWrapper = '#checkout-progress-wrapper';
                        scrollTo(myWrapper + ' .title');
                        $(myWrapper + ' .title').addClass('active');
                        $(myWrapper + ' .content').show();
                    });

                $('body')
                    .on('click.checkoutProgress', '#checkout-progress-wrapper .title', function(e){
                        $(this).toggleClass('active');
                        $('#checkout-progress-wrapper .content').toggle();
                    });

                var galleryElement = $('[data-role=media-gallery]');
                setTimeout(function(){
                    if (galleryElement.length && galleryElement.data('mageZoom')) {
                        galleryElement.zoom('disable');
                    }
                    if (galleryElement.length && galleryElement.data('mageGallery')) {
                        galleryElement.gallery("option","disableLinks",false);
                        galleryElement.gallery("option","showNav",true);
                        galleryElement.gallery("option","showThumbs",false);
                    }
                }, 2000);

                setTimeout(function(){
                        $(".product.data.items").tabs("option","openOnFocus",false);
                }, 500);

            }

        });
    });
})(window.jQuery);
