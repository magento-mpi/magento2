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

                drawerFixedHeader: function() {
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

                drawerOpen: function(tileCode) {
                    var bodyHeight = $('body').outerHeight() + 500;
                    methods.drawerMinHeight();

                    options.drawer
                        .css('top', '' + bodyHeight + 'px')
                        .show(1, function () {
                            options.drawer.animate({
                                top: '57px'
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
                },

                drawerAfterLoad: function(result, status) {
                    if (result.success) {
                        $('#drawer-item-header').text(result.tile_header);
                        methods.drawerOpen(result.tile_code);
                        $('.drawer-content > .content').html(result.tile_content);
                    } else {
                        alert(result.error_message);
                    }
                },

                drawerAfterSave: function(result, status) {
                    if (result.success) {
                        //Complete class should be added by condition for specified tile
                        $('#article-' + result.tile_code).before(result.tile_content).remove();
                        $('.drawer-open')
                            .on('click', methods.setButtonHandler);
                        isAllStepsCompleted();
                        methods.drawerClose();
                    } else {
                        alert(result.error_message);
                    }
                },

                setButtonHandler: function() {
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
                        success: methods.drawerAfterLoad
                    };
                    $.ajax(ajaxOptions);

                    $('.footer-inner .button-save-settins').attr('tile-code', tileCode);
                    $('.footer-inner .button-save-settins').attr('save-url', elem.attr('save-url'));
                    return false;
                }
            };

            options.btnOpenDrawer
                .on('click.openDrawer', methods.setButtonHandler);

            options.btnCloseDrawer
                .on('click.closeDrawer', methods.drawerClose);

            options.btnSaveDrawer
                .on('click.saveSettings', function () {
                    var drawerForm = $("#drawer-form"),
                        postData;

                    postData = drawerForm.serialize();
                    postData += '&tileCode=' + $('.footer-inner .button-save-settins').attr('tile-code');
                    var ajaxOptions = {
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        url: $('.footer-inner .button-save-settins').attr('save-url'),
                        success: methods.drawerAfterSave
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
