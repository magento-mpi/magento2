/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

;
(function($) {
    'use strict';

    /* Navigation */
    $.widget('theme.navigation', {
        options: {
            topLinks: '> .level0',
            dropdown: null,
            dropdownLabel: 'More'
        },

        _create: function() {
            this.topLinks = $(this.options.topLinks, this.element);

            this._events();
        },

        _init: function() {
            this._checkIfShouldCollapse();
        },

        _events: function() {},

        _checkIfShouldCollapse: function() {
            if (this._getTopLinksWidth() > this.element.outerWidth(true)) {
                !this.dropdown && this._createDropdown();
            } else {
                this._destroyDropdown();
            }
        },

        _createDropdown: function() {
            this.dropdown = $('<li class="level0 level-top parent more"></li>');
            this.dropdownLabel = $('<a href="#" class="level-top"><span>' + $.mage.__(this.options.dropdownLabel) + '</span></a>');
            this.dropdownSubmenu = $('<div class="submenu"></div>');
            this.dropdownItems = $('<ul class="level0">' + this._getHiddenLinks() + '</ul>');

            this.dropdownSubmenu
                .append(this.dropdownItems);

            this.dropdown
                .append(this.dropdownLabel)
                .append(this.dropdownSubmenu)
                .appendTo(this.element);

            this.dropdown
                .hoverIntent(config)
                .find('> .submenu').hide();
        },

        _destroyDropdown: function() {
            this.dropdown && this.dropdown.remove();
            $('.level0.hidden', this.element)
                .removeClass('hidden')
                .show();
        },

        _getTopLinksWidth: function() {
            var totalWidth = 0;

            $.each(this.topLinks, function(index, item) {
                totalWidth += $(item).outerWidth(true);
            });

            return totalWidth;
        },

        _getHiddenLinks: function() {
            var totalWidth = 0;

            this.hiddenLinks = $('<div></div>');

            $.each(this.topLinks, $.proxy(function(index, item) {
                totalWidth += $(item).outerWidth(true);
                if (totalWidth + 50 >= this.element.outerWidth(true)) {
                    this.hiddenLinks.append($(item).clone().attr('class', 'column'));
                    $(item).addClass('hidden').hide();
                }
            }, this));

            return this.hiddenLinks.html();
        }
    });

    function showNav() {
        var self= $(this);
        self.closest('nav.navigation').addClass('hover');
        if(self.siblings('.hover').length) {

            self.siblings('.hover:not(.parent)').each(function(){
                clearTimeout($(this).prop('hoverIntent_t'));
                $(this).prop('hoverIntent_s', 0);
                $(this).removeClass('hover');
                self.addClass('hover');
                $('> .submenu', self).slideDown(200);
            });

            self.siblings('.hover.parent').each(function(){
                var previous = $(this);
                clearTimeout(previous.prop('hoverIntent_t'));
                previous.prop('hoverIntent_s', 0);
                $('> .submenu', previous).slideUp(200, function(){
                    previous.removeClass('hover');
                    self.addClass('hover');
                    $('> .submenu', self).slideDown(200);
                });
            });

        } else {
            self.addClass('hover');
            $('> .submenu', self).slideDown(200);
        }
    }


    function showNavContinium() {
        var self= $(this);
        self.closest('nav.navigation').addClass('hover');
        $('> .submenu', self).css('height','');
        if(self.siblings('.hover').length) {

            self.siblings('.hover:not(.parent)').each(function(){
                clearTimeout($(this).prop('hoverIntent_t'));
                $(this).prop('hoverIntent_s', 0);
                $(this).removeClass('hover');
                self.addClass('hover');
                $('> .submenu', self).slideDown(200);
            });

            self.siblings('.hover.parent').each(function(){
                var previous = $(this);
                clearTimeout(previous.prop('hoverIntent_t'));
                previous.prop('hoverIntent_s', 0);

                self.addClass('hover');
                if (!self.hasClass('parent')) {
                    $('> .submenu', previous).slideUp(200, function(){
                        previous.removeClass('hover');
                    });
                    return;
                }
                var originalHeight = $('> .submenu', self).css('height');

                $('> .submenu', self).css('height', function(){
                    $('> .submenu', previous).css('height','');
                    return $('> .submenu', previous).height();
                });
                previous.removeClass('hover');
                $('> .submenu', previous).hide();
                $('> .submenu', self).show();
                $('> .submenu', self).addClass();
                $('> .submenu', self).animate(
                    {'height': originalHeight},
                    200,
                    'linear',
                    function(){
                        $(this).css('height','');
                    });
            });

        } else {
            self.addClass('hover');
            $('> .submenu', self).slideDown(200);
        }
    }

    function showNavAlt() {
        var self = $(this);
        self.closest('nav.navigation').addClass('hover');
        $('> .submenu', self).css('height','');
        if(self.siblings('.hover')) {
            self.siblings().each(function () {
                clearTimeout($(this).prop('hoverIntent_t'));
                $(this).prop('hoverIntent_s', 0);
                var elem = $(this);
                if(self.hasClass('parent')) {
                    $('> .submenu', elem).css('height','');
                    var originalHeight = $('> .submenu', self).css('height');
                    $('> .submenu', self).css('height', function(){
                        return $('> .submenu', elem).height();
                    });
                    $('> .submenu', elem).hide();
                    elem.removeClass('hover');
                    self.addClass('hover');
                    $('> .submenu', self).show();

                } else {
                    $('> .submenu', elem).slideUp(200, function(){
                        elem.removeClass('hover');
                        self.addClass('hover');
                    });
                }
            });
        } else {
            $('> .submenu', self).slideDown(200);
        }
    }
    function hideNav() {
        var self = $(this);
        self.closest('nav.navigation').removeClass('hover');
        if (self.hasClass('parent')) {
            $('> .submenu', this).slideUp(200, function(){
                self.removeClass('hover');
            });
        } else {
            self.removeClass('hover');
        }
    }
    var config = {
        interval: 100,
        over: showNavContinium, // function = onMouseOver callback (REQUIRED)
        timeout: 2000, // number = milliseconds delay before onMouseOut
        out: hideNav // function = onMouseOut callback (REQUIRED)
    };

    function checkWrap() {
        var navWidth = $('.navigation > ul').width();
        var totalWidth=0;
        var stop = 0;
        $('.navigation > ul > li').each(function(index) {
            totalWidth = totalWidth + $(this).outerWidth();
            if(totalWidth > navWidth && stop == 0) {
                stop = index - 1;
            };
        });
        if (stop > 0) {
            var items = $('.navigation > ul > li');
            items
                .slice(stop,items.lenght)
                .wrapAll('<li class="level-top more parent">')
                .wrapAll('<div class="submenu"/>')
                .wrapAll('<ul />');
        }

    }

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

    /*    $(window).resize(function() {
     if(this.resizeTO) clearTimeout(this.resizeTO);
     this.resizeTO = setTimeout(function() {
     $(this).trigger('resizeEnd');
     }, 500);
     });
     $(window).bind('resizeEnd', function() {

     });*/

    $(document).ready(function() {
        //document.documentElement.clientWidth && screen.width
        listScroll();
        $('.navigation > ul').navigation();

        //$('.navigation .level-top > .submenu').hide();
        $('.navigation .level-top').hoverIntent(config).find('> .submenu').hide();
        if ($('.customer.welcome').length > 0) {
            $('.customer.welcome > .customer').dropdown({menu:'.customer.welcome > .menu'});
        };

        if ($('body').hasClass('checkout-cart-index')) {
            $('.cart.summary > .block > .title').dropdown({autoclose:false, menu:'.title + .content'});
            if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0 ) {
                $('.block.shipping > .title').addClass('active');
                $('.block.shipping').addClass('active');
            }
        }
    });

})(window.jQuery);