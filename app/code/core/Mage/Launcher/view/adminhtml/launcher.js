/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $(document).ready(function () {
        'use strict';

        var Drawer = (function(opts){

            var options = $.extend({
                drawer : $('#drawer'),
                drawerHeader : $('.drawer-content > header'),
                drawerContent : $('.drawer-content > .content'),
                drawerFooter : $('.drawer-content > footer'),
                storeLauncher : $('#store-launcher'),
                btnOpenDrawer : $('.drawer-open'),
                btnCloseDrawer : $('.header-inner .btn-close'),
                btnSaveDrawer :  $('.footer-inner .button-save-settins')
            },opts||{});

            var methods = {
                drawerMinHeight: function () {
                    delay && clearTimeout(delay);

                    var delay = setTimeout(function () {
                        var bodyHeight = $('body').outerHeight(),
                            editFormHeight = $('#drawer').outerHeight(),
                            windowOffsetTop = $(window).scrollTop(),
                            newMinHeight = bodyHeight+windowOffsetTop - 200;
                        if (editFormHeight < newMinHeight) {
                            options.drawer.css({
                                'min-height':newMinHeight + 'px'
                            });
                        }
                    }, 1);
                },

                drawerFixedHeader: function(){
                    var headerOffsetTop = options.drawerContent.offset().top,
                        windowOffsetTop = $(window).scrollTop(),
                        headerPositionTop = headerOffsetTop - windowOffsetTop;

                    if (headerPositionTop < 50 && options.drawerHeader.is(":visible")) {
                        options.drawer.addClass('fixed');
                    }
                    else {
                        options.drawer.removeClass('fixed');
                    }
                },

                drawerOpen: function(drawerToOpenID){
                    var bodyHeight = $('body').outerHeight()+500;

                    methods.drawerMinHeight();

                    $('#drawer-'+drawerToOpenID)
                        .add('#drawer-'+drawerToOpenID+'-header')
                        .add('#drawer-'+drawerToOpenID+'-footer')
                        .removeClass('hidden')
                        .siblings()
                        .addClass('hidden');

                    options.drawer
                        .css('top',''+bodyHeight+'px')
                        .show(1, function () {
                            options.drawer.animate({
                                top:'157px'
                            }, 1000, function () {
                                methods.drawerFixedHeader();
                                options.storeLauncher.hide();
                                options.drawerFooter.animate({
                                    bottom: 0
                                },100);
                            })
                        });
                },

                drawerClose: function(){
                    var bodyHeight = $('body').outerHeight();

                    options.drawerFooter.animate({
                        bottom: '-51px'
                    },100);

                    options.storeLauncher.show();

                    if (options.drawer.hasClass('fixed')) {
                        window.scrollTo(0, 0);
                        options.drawer.css({
                            top: '0'
                        });
                        options.drawer.removeClass('fixed');
                        options.drawer.animate({
                            top: bodyHeight+'px'
                        }, 1000, function () {
                            options.drawer.hide();
                        });
                    }
                    else {
                        var deltaTop = $(window).scrollTop();
                        options.drawer.animate({
                            top: deltaTop + bodyHeight+'px'
                        }, 1000, function () {
                            options.drawer.hide();
                        });
                    }
                }
            };

            options.btnOpenDrawer
                .on('click.openDrawer', function () {
                    var elem = $(this),
                        drawerToOpenID = elem
                            .attr('data-drawer')
                            .replace('open-drawer-','');

                    methods.drawerOpen(drawerToOpenID);

                    return false;
                });

            options.btnCloseDrawer
                .on('click.closeDrawer', function () {
                    methods.drawerClose();
                });

            options.btnSaveDrawer
                .on('click.saveSettings', function () {
                    var id  = $('.drawer-item:not(:hidden)').attr('id');

                    $('[data-drawer="open-'+id+'"]')
                        .closest('.sl-step')
                        .addClass('sl-step-complete');

                    isAllStepsCompleted();
                    methods.drawerClose();
                });

            $(window).scroll(function () {
                methods.drawerFixedHeader();
            });

            return {
                options: options,
                open : methods.drawerOpen,
                close : methods.drawerClose
            }

        })();

        function isAllStepsCompleted() {
            var completedSteps = $('#store-launcher').eq(0).find('.sl-step-complete').length,
                storeAction = $('.btn-launch-store');

            if (completedSteps == 6) {
                storeAction
                    .removeClass('hidden');
            }
            else {
                storeAction
                    .addClass('hidden');
            }
        }

    });
})(window.jQuery);
