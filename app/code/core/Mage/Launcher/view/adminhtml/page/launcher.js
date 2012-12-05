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

        var Drawer = (function(opts) {

            var options = $.extend({
                drawer : $('#drawer'),
                drawerHeader : $('.drawer-content > header'),
                drawerContent : $('.drawer-content > .content'),
                drawerFooter : $('.drawer-content > footer'),
                storeLauncher : $('#store-launcher'),
                btnOpenDrawer : $('.drawer-open'),
                btnCloseDrawer : $('.header-inner .btn-close'),
                btnSaveDrawer : $('.footer-inner .button-save-settins')
            }, opts || {});

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
                                'min-height': newMinHeight + 'px'
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

                drawerOpen: function(tileCode){
                    var bodyHeight = $('body').outerHeight() + 500;

                    methods.drawerMinHeight();

                    $('#drawer-' + tileCode)
                        .add('#drawer-' + tileCode + '-header')
                        .add('#drawer-' + tileCode + '-footer')
                        .removeClass('hidden')
                        .siblings()
                        .addClass('hidden');

                    options.drawer
                        .css('top', '' + bodyHeight + 'px')
                        .show(1, function () {
                            options.drawer.animate({
                                top: '157px'
                            }, 1000, function () {
                                methods.drawerFixedHeader();
                                options.storeLauncher.hide();
                                options.drawerFooter.animate({
                                    bottom: 0
                                }, 100);
                            })
                        });
                },

                drawerClose: function() {
                    var bodyHeight = $('body').outerHeight();

                    options.drawerFooter.animate({
                        bottom: '-51px'
                    }, 100);

                    options.storeLauncher.show();

                    if (options.drawer.hasClass('fixed')) {
                        window.scrollTo(0, 0);
                        options.drawer.css({
                            top: '0'
                        });
                        options.drawer.removeClass('fixed');
                        options.drawer.animate({
                            top: bodyHeight + 'px'
                        }, 1000, function () {
                            options.drawer.hide();
                        });
                    }
                    else {
                        var deltaTop = $(window).scrollTop();
                        options.drawer.animate({
                            top: deltaTop + bodyHeight + 'px'
                        }, 1000, function () {
                            options.drawer.hide();
                        });
                    }
                    $('#drawer-item-header').text('');
                    $('.footer-inner .button-save-settins').attr('save-url', '');
                    $('.drawer-content > .content').html('');
                },

                drawerLoad: function(result, status) {
                    if (result.success) {
                        $('#drawer-item-header').text(result.tileHeader);
                        methods.drawerOpen(result.tileCode);
                        $('.drawer-content > .content').html(result.tileContent);
                    } else {
                        alert(result.error_message);
                    }
                },

                drawerSaved: function(result, status) {
                    if (result.success) {
                        //Complete class should be added by condition for specified tile
                        //.addClass('sl-step-complete');

                        isAllStepsCompleted();
                        methods.drawerClose();
                    } else {
                        alert(result.error_message);
                    }
                }
            };

            options.btnOpenDrawer
                .on('click.openDrawer', function () {
                    var elem = $(this),
                        tileCode = elem
                            .attr('data-drawer')
                            .replace('open-drawer-', '');

                    var postData = {
                        form_key: FORM_KEY,
                        tileCode: tileCode
                    };
                    var ajaxOptions = {
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        url: elem.attr('load-url'),
                        success: methods.drawerLoad
                    };
                    $.ajax(ajaxOptions);

                    $('.footer-inner .button-save-settins').attr('save-url', elem.attr('save-url'));
                    return false;
                });

            options.btnCloseDrawer
                .on('click.closeDrawer', function () {
                    methods.drawerClose();
                });

            options.btnSaveDrawer
                .on('click.saveSettings', function () {

                    var postData = {
                        form_key: FORM_KEY
                    };
                    var ajaxOptions = {
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        url: $('.footer-inner .button-save-settins').attr('save-url'),
                        success: methods.drawerSaved
                    };
                    $.ajax(ajaxOptions);

                    return true;
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
                storeAction.removeClass('hidden');
            }
            else {
                storeAction.addClass('hidden');
            }
        }

    });
})(window.jQuery);
