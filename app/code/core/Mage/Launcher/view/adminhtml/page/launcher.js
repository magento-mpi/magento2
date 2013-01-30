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
                drawer: $('#drawer'),
                drawerHeader: $('.drawer-content > header'),
                drawerContent: $('.drawer-content > .content'),
                drawerFooter: $('.drawer-content > footer'),
                storeLauncher: $('#store-launcher'),
                btnOpenDrawer: $('.drawer-open'),
                btnCloseDrawer: $('.header-inner .btn-close'),
                btnSaveDrawer: $('.footer-inner .button-save-settins')
            }, opts || {});

            var methods = {
                drawerMinHeight: function () {
                    delay && clearTimeout(delay);

                    var delay = setTimeout(function () {
                        var bodyHeight = $('body').outerHeight(),
                            editFormHeight = $('#drawer').outerHeight(),
                            windowOffsetTop = $(window).scrollTop(),
                            newMinHeight = bodyHeight + windowOffsetTop - 200;
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
                    } else {
                        options.drawer.removeClass('fixed');
                    }
                },

                drawerOpen: function(tileCode) {
                    var bodyHeight = $('body').outerHeight() + 500,
                        offsetNavBar = $('.nav-bar').offset(),
                        offsetDrawer = '57';
                    methods.drawerMinHeight();
                    if (offsetNavBar && offsetNavBar.top) {
                        offsetDrawer = offsetNavBar.top
                    }

                    options.drawer
                        .css('top', '' + bodyHeight + 'px')
                        .show(1, function () {
                            options.drawer.animate({
                                top: offsetDrawer + 'px'
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
                    window.location.hash = '';
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
                    } else {
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
                    window.location.hash = '';
                    if (result.success) {
                        //Complete class should be added by condition for specified tile
                        $('#article-' + result.tile_code).before(result.tile_content).remove();
                        $('.drawer-open').on('click', methods.setButtonHandler);
                        handleLaunchStoreButton();
                        methods.drawerClose();
                    } else {
                        if (result && result.error_message) {
                            alert(result.error_message);
                        }
                    }
                },

                setButtonHandler: function() {
                    try {
                        var hashString = $(this).parent().parent().attr('id');
                        window.location.hash = hashString.replace('article-', '');
                    } catch(err) {
                        return false;
                    }
                    return true;
                },

                handleHash: function() {
                    if (window.location.hash == '') {
                        methods.drawerClose();
                        return false;
                    }
                    try {
                        var hashString = window.location.hash;
                        hashString = hashString.replace('#', '');
                        var $elem = $('#article-' + hashString + ' .drawer-open'),
                            tileCode = $elem.attr('data-drawer').replace('open-drawer-', '');

                        var postData = {
                            tileCode: tileCode
                        };
                        var ajaxOptions = {
                            type: 'POST',
                            showLoader: true,
                            data: postData,
                            dataType: 'json',
                            url: $elem.attr('data-load-url'),
                            success: methods.drawerAfterLoad
                        };
                        $.ajax(ajaxOptions);

                        $('.footer-inner .button-save-settins').attr('tile-code', tileCode);
                        $('.footer-inner .button-save-settins').attr('data-save-url', $elem.attr('data-save-url'));
                    } catch(err) {
                        return false;
                    }
                }
            };

            options.btnOpenDrawer.on('click.openDrawer', methods.setButtonHandler);

            options.btnCloseDrawer.on('click.closeDrawer', methods.drawerClose);

            options.btnSaveDrawer
                .on('click.saveSettings', function () {
                    var drawerForm = $("#drawer-form"),
                        buttonSave = $('.footer-inner .button-save-settins'),
                        postData;
                    if (!drawerForm.valid()) {
                        return false;
                    }

                    postData = drawerForm.serialize();
                    postData += '&tileCode=' + buttonSave.attr('tile-code');
                    var ajaxOptions = {
                        type: 'POST',
                        showLoader: true,
                        data: postData,
                        dataType: 'json',
                        url: buttonSave.attr('data-save-url'),
                        success: methods.drawerAfterSave
                    };
                    $.ajax(ajaxOptions);

                    return true;
                });

            $(window).scroll(function () {
                methods.drawerFixedHeader();
            });

            $(window).hashchange( methods.handleHash );

            methods.handleHash();

            return {
                options: options,
                open: methods.drawerOpen,
                close: methods.drawerClose
            }

        })();

        /**
         * Check if page has been completed (i.e. all related tiles are complete)
         *
         * @return boolean
         */
        var isPageComplete = function () {
            var completeSteps = $('#store-launcher').eq(0).find('.sl-step-complete').length;
            return completeSteps == $('#store-launcher article').size();
        }

        /**
         * Show 'Launch Store' button if needed
         */
        var handleLaunchStoreButton = function () {
            var launchStoreButton = $('.btn-launch-store');
            if (isPageComplete()) {
                launchStoreButton.removeClass('hidden');
            } else {
                launchStoreButton.addClass('hidden');
            }
        }
    });
})(window.jQuery);
