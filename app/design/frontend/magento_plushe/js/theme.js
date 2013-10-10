/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

;
(function($) {
    'use strict';

    function listScroll() {
        var list = $('[data-action="scroll"]').addClass('carousel');
        var listInner = $('> .minilist.items', list);
        var items = $('.item', list);
        var itemWidth = $(items[0]).outerWidth();
        var perpage = Math.floor(list.outerWidth()/itemWidth);
        var pages = Math.floor(items.length/perpage);
        var page=0;
        for (var i=0 ; i < perpage; i++) {
            $(items[i + page*perpage]).addClass('shown');
        };
        for (var i=perpage; i < items.length; i++) {
            $(items[i + page*perpage]).addClass('hidden');
        };
        if ( itemWidth*items.length > list.outerWidth() ) {
                var next = $('<button class="action next" type="button"><span>Next</span></button>');
                var previous = $('<button class="action previous" type="button"><span>Previous</span></button>').attr('disabled', 'disabled');
                list.append(previous);
                list.append(next);
                listInner.wrap('<div class="items-wrapper" />');
                $('.items-wrapper').css('width', itemWidth*perpage);
                next.on('click.itemsScroll', function() {
                            list.addClass('animation');
                            items.removeClass('shown');
                            items.removeClass('hidden');
                            listInner.animate({
                            left: '-=' + itemWidth*perpage,
                        }, 400, 'easeInOutCubic', function() {
                            // Animation complete.
                            page = page + 1;
                            for (var i=0 ; i < perpage; i++) {
                                $(items[i + page*perpage]).addClass('shown');
                            };
                            for (var i=perpage; i < items.length; i++) {
                                $(items[i + page*perpage]).addClass('hidden');
                            };
                            console.log(i);
                            previous.removeAttr('disabled');
                            if (page == pages) {
                                next.attr('disabled', 'disabled');
                            }
                            list.removeClass('animation');
                        });
                    });
                previous.on('click.itemsScroll', function() {
                            list.addClass('animation');
                            items.removeClass('shown');
                            items.removeClass('hidden');
                            listInner.animate({
                            left: '+=' + itemWidth*perpage,
                        }, 400, 'easeInOutCubic', function() {
                            // Animation complete.
                            page = page - 1;
                            for (var i=0 ; i < perpage; i++) {
                                $(items[i + page*perpage]).addClass('shown');
                            };
                            for (var i=perpage; i < items.length; i++) {
                                $(items[i + page*perpage]).addClass('hidden');
                            };
                            next.removeAttr('disabled');
                            if (page == 0) {
                                previous.attr('disabled', 'disabled');
                            }
                            list.removeClass('animation');
                        });
                    });

        }
    }

    $(document).ready(function() {
        listScroll();

        if ($('body').hasClass('checkout-cart-index')) {
            $('.cart.summary > .block > .title').dropdown({autoclose:false, menu:'.title + .content'});
            if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0 ) {
                $('.block.shipping > .title').addClass('active');
                $('.block.shipping').addClass('active');
            }
        }

        $('[role="navigation"]').navigationMenu({
            responsive: true,
            submenuContiniumEffect: true
        });
    });

})(window.jQuery);