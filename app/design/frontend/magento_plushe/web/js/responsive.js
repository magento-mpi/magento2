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
            media: '(max-width: 640px)',

            // Switch to Mobile Version
            entry: function() {
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

                (function() {
                    var productInfoMain = $('.product.info.main'),
                        productInfoAdditional = $("#product-info-additional");

                    if (!productInfoAdditional.length) {

                        var productTitle = productInfoMain.find(".page.title.product").clone(),
                            productStock = productInfoMain.find(".stock:not(.alert)").clone();

                        productInfoAdditional = $("<div/>", {
                            id: "product-info-additional",
                            addClass: "product info additional"
                        });

                        $('.catalog-product-view .column.main')
                            .prepend(productInfoAdditional);

                        productInfoAdditional
                            .append(productTitle)
                            .append(productStock);

                    } else {
                        productInfoAdditional.removeClass("hidden");
                    }

                    productInfoMain.addClass("responsive");

                })();

                var galleryElement = $('[data-role=media-gallery]');
                if (galleryElement.length && galleryElement.data('zoom')) {
                    galleryElement.zoom('disable');
                }
            },

            // Switch to Desktop Version
            exit: function() {
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
                if (galleryElement.length && galleryElement.data('zoom')) {
                    galleryElement.zoom('enable');
                }
            }
        });
    });
})(window.jQuery);
