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

            },

            // Switch to Desktop Version
            exit: function() {
                // minicart
                $('.action.showcart').removeClass('is-disabled');
            }
        });
    });
})(window.jQuery);
