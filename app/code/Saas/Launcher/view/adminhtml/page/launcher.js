/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function($) {
    'use strict';
    $.widget("storeCreation.drawer", {
        options: {
            drawerHeader: '.drawer-header',
            drawerHeaderInner: '.drawer-header-inner',
            drawerContent: '.drawer-content',
            drawerFooter: '.drawer-footer',
            drawerDependencies: {},
            btnOpenDrawer: '.action-open-drawer',
            btnCloseDrawer: '.action-close-drawer',
            btnSaveDrawer: '.action-save-settings',
            drawerTopPosition: '.navigation',
            stickyHeaderClass: 'fixed',
            behaviorFixSelector: '#store-launcher-content,#nav,#system_messages,#action-launch-my-store'
        },

        _create: function() {
            this.drawerHeader = $(this.options.drawerHeader);
            this.drawerHeaderInner = $(this.options.drawerHeaderInner);
            this.drawerContent = $(this.options.drawerContent);
            this.drawerFooter = $(this.options.drawerFooter);
            this.btnOpenDrawer = $(this.options.btnOpenDrawer);
            this.btnCloseDrawer = $(this.options.btnCloseDrawer);
            this.btnSaveDrawer = $(this.options.btnSaveDrawer);
            this.drawerTopPosition = $(this.options.drawerTopPosition);
            this._startDrawerClose = false;
            this._bind();
        },

        _bind: function() {
            $('body')
                .on('click.openDrawer', this.options.btnOpenDrawer, this._setButtonHandler);

            this.btnCloseDrawer
                .on('click.closeDrawer', $.proxy(this.drawerClose, this));

            this.btnSaveDrawer
                .on('click.saveSettings', $.proxy(function(e) {
                var elem = $(e.currentTarget),
                    drawerForm = $("#drawer-form"),
                    postData;

                this.element.trigger('drawerBeforeSave');
                if (!drawerForm.valid()) {
                    return false;
                }

                postData = drawerForm.serialize() + '&' + $.param({tileCode: elem.attr('tile-code')});
                var ajaxOptions = {
                    type: 'POST',
                    showLoader: true,
                    data: postData,
                    dataType: 'json',
                    url: elem.attr('data-save-url'),
                    success: $.proxy(this._drawerAfterSave, this),
                    error: this._ajaxFailure
                };
                $.ajax(ajaxOptions);

                return true;
            }, this));

            $(window)
                .scroll($.proxy(this._drawerFixedHeader, this))
                .hashchange($.proxy(this.handleHash, this))
                .resize($.proxy(function(){
                delay && clearTimeout(delay);
                var delay = setTimeout($.proxy(this._drawerMinHeight, this), 100);
            }, this));
        },

        _drawerMinHeight: function() {
            var bodyHeight = $('body').outerHeight(),
                windowOffsetTop = $(window).scrollTop(),
                headerHeight = this.drawerTopPosition.offset().top,
                newMinHeight = bodyHeight + windowOffsetTop - headerHeight;

            this.element.css({
                'min-height': newMinHeight
            });
        },

        /**
         * Check if page has been completed (i.e. all related tiles are complete)
         *
         * @return boolean
         */
        _isPageComplete: function() {
            var completeSteps = $('#store-launcher-content').eq(0).find('.tile-complete').length;
            return completeSteps == $('#store-launcher-content article').size();
        },

        /**
         * Show 'Launch Store' button if needed
         */
        _handleLaunchStoreButton: function() {
            var launchStoreButton = $('.action-launch-store');
            if (this._isPageComplete()) {
                launchStoreButton.prop("disabled", false);
            } else {
                launchStoreButton.prop("disabled", true);
            }
        },

        _drawerFixedHeader: function() {
            var windowOffsetTop = $(window).scrollTop(),
                headerHeight = this.drawerTopPosition.offset().top;

            if (windowOffsetTop >=  headerHeight && this.drawerHeader.is(":visible")) {
                this.element.addClass(this.options.stickyHeaderClass);
            } else {
                this.element.removeClass(this.options.stickyHeaderClass);
            }
        },

        scrollToTop: function() {
          window.scrollTo(0, 0);
        },

        drawerOpen: function() {
            var elem = this.element,
                headerHeight = this.drawerTopPosition.offset().top,
                bodyHeight = $('body').outerHeight() + 500;

            elem.trigger('drawerRefresh');
            this._drawerMinHeight();
            this.scrollToTop();
            this._startDrawerClose = false;

            elem
                .css('top', bodyHeight)
                .show()
                .animate({
                    top: headerHeight
                }, 1000, 'easeOutExpo', $.proxy(function() {
                this._drawerFixedHeader();
                this.drawerFooter.animate({
                    bottom: 0
                }, 100, $.proxy(function() {
                    $(this.options.behaviorFixSelector).hide();
                }, this));
            }, this));
        },

        drawerClose: function() {
            if (this._startDrawerClose) {
                return;
            }
            $(this.options.behaviorFixSelector).show();
            this._startDrawerClose = true;
            window.location.hash = '';

            var elem = this.element,
                drawerFooter = this.drawerFooter,
                drawerFooterHeight = drawerFooter.height(),
                bodyHeight = $('body').outerHeight();

            elem.trigger('drawerClose');
            var hideDrawer = function() {
                elem.hide();
            };

            drawerFooter.animate({
                bottom: -drawerFooterHeight - 10
            }, 100);

            if (elem.hasClass(this.options.stickyHeaderClass)) {
                this.scrollToTop()
                elem.css({
                    top: 0
                });
                elem.removeClass(this.options.stickyHeaderClass)
                    .animate({
                        top: bodyHeight
                    }, 1000, hideDrawer);
            } else {
                var deltaTop = $(window).scrollTop();
                elem.animate({
                    top: deltaTop + bodyHeight
                }, 1000, hideDrawer);
            }
        },

        setDependencies: function(tileCode, dependencies) {
            this.options.drawerDependencies[tileCode] = dependencies;
        },

        _drawerPreLoad: function(dataLoadUrl, dataSaveUrl, tileCode) {
            var dependencies = this.options.drawerDependencies[tileCode];
            var callbackHandler = $.proxy(function() {
                    return this._drawerLoad(dataLoadUrl, dataSaveUrl, tileCode);
                }, this);
            if (typeof dependencies !== 'undefined') {
                var clonedDependencies = dependencies.slice(0);
                clonedDependencies.push(callbackHandler);
                head.js.apply(head.js, clonedDependencies);
            } else {
                callbackHandler();
            }
        },

        _drawerLoad: function(dataLoadUrl, dataSaveUrl, tileCode) {
            var postData = {
                    tileCode: tileCode
                },
                ajaxOptions = {
                    type: 'POST',
                    showLoader: true,
                    data: postData,
                    dataType: 'json',
                    url: dataLoadUrl,
                    success: $.proxy(this._drawerAfterLoad, this),
                    error: this._ajaxFailure
                };

            $.ajax(ajaxOptions);

            this.btnSaveDrawer
                .attr('tile-code', tileCode)
                .attr('data-save-url', dataSaveUrl);
        },

        _drawerAfterLoad: function(result, status) {
            if (result.success) {
                $('.title', this.drawerHeader).text(result.tile_header);
                this.drawerOpen();
                $('.drawer-content-inner', this.drawerContent).html(result.tile_content).trigger('contentUpdated');
                var drawerSwitcher = $('.drawer-content-inner').find('.drawer-switcher');
                if (drawerSwitcher.length) {
                    var drawerSwitcherCopy = drawerSwitcher.clone(),
                        drawerSwitcherCheckbox = drawerSwitcherCopy.find(':checkbox'),
                        drawerSwitcherLabel = drawerSwitcherCopy.find('.switcher-label'),
                        drawerSwitcherId = drawerSwitcherCheckbox.prop('id').replace('', 'copy-'),
                        formValidation = drawerSwitcher.data('form-validation');

                    drawerSwitcherLabel.prop('for', drawerSwitcherId);
                    drawerSwitcherCheckbox.prop('id', drawerSwitcherId);
                    this.drawerHeaderInner.append(drawerSwitcherCopy);
                    drawerSwitcherCopy.toggleStatus({'needValidate': formValidation});
                }
            } else if (result && result.error_message) {
                alert(result.error_message);
            }
        },

        _drawerAfterSave: function(result, status) {
            if (result.success) {
                //Complete class should be added by condition for specified tile
                $('#tile-' + result.tile_code).before(result.tile_content).remove();
                this._handleLaunchStoreButton();
                window.location.hash = '';
                this.drawerClose();
            } else if (result && result.error_message) {
                alert(result.error_message);
            }
        },

        _setButtonHandler: function() {
            var hashString = $(this).closest('.tile-store-settings').attr('id');
            window.location.hash = hashString.replace('tile-', '');
        },

        handleHash: function() {
            if (window.location.hash == '' || window.location.hash == '#') {
                this.drawerClose();
                return;
            }
            var tileCode = window.location.hash.replace('#', ''),
                tile = $('#tile-' + tileCode),
                elem = tile.find(this.options.btnOpenDrawer);

            if (elem.length == 0) {
                window.location.hash = '';
                return;
            } else if (elem.length > 1) {
                elem = elem.eq(tile.hasClass('tile-complete') ? 1 : 0);
            }
            this._drawerPreLoad(elem.attr('data-load-url'), elem.attr('data-save-url'), tileCode);
        },

        _ajaxFailure: function() {
            window.location.reload();
        }
    });

    $.widget('storeCreation.partiallyValidateDrawer', {
        options: {
            currentSection: ''
        },

        _create: function() {
            this.currentSection = $(this.options.currentSection);

            this.element.on('click.saveMethod', $.proxy(function(event) {
                event.preventDefault();
                var elem = this.element,
                    fieldset = elem.closest('.fieldset'),
                    fields = fieldset.find(':input'),
                    legend = fieldset.find('.legend'),
                    id = fieldset.attr('id'),
                    removeValidationMessage = function() {
                        setTimeout(function() {
                            $('.message-validation').slideUp(400);
                        }, 3000);
                    },
                    insertValidationMessage = function(messageText, messageType) {
                        $('<div class="message-validation ' + messageType + '">' + messageText + '</div>')
                            .insertAfter(legend);
                        removeValidationMessage();
                    };

                var validationNotPassed = fields.filter(function(index, element) {
                    return !$.validator.validateElement(element);
                }).length;

                if (validationNotPassed != 0) {
                    return;
                }

                var ajaxOptions = {
                    type: 'POST',
                    showLoader: true,
                    data: fields.serialize(),
                    dataType: 'json',
                    url: elem.attr('data-save-url'),
                    success: $.proxy(function(result, status) {
                        if (result.success) {
                            if (result && result.message) {
                                insertValidationMessage(result.message,'success');
                                this.currentSection.find('.active').addClass('configured');
                            }
                        } else {
                            if (result && result.error_message) {
                                insertValidationMessage(result.error_message,'error');
                            }
                        }
                    }, this),
                    error: function() {
                        window.location.reload();
                    }
                };
                $.ajax(ajaxOptions);
            }, this));
        }
    });

    $.widget('storeCreation.toggleStatus', {
        options: {
            formsToDisable: [],
            disabledMessage: '',
            needValidate: true,
            drawerForm: '#drawer-form'
        },

        _init: function() {
            this.drawerForm = $(this.options.drawerForm);
            this.disabledMessage = $(this.options.disabledMessage);
            this._toggleStatus();
        },

        destroy: function(e) {
            e.preventDefault();
            this.element.remove();
        },

        _create: function() {
            this.element.on('change.drawerStatus', $.proxy(this._toggleStatus, this));
            this._on('body', {
                'drawerRefresh.drawer': 'destroy'
            });
        },

        _toggleStatus: function() {
            var elem = this.element.find(':checkbox'),
                elemId = elem.prop('id').replace('copy-','');

            if (elem.is(':checked')) {
                $.each(this.options.formsToDisable, $.proxy(function(index, element) {
                    $(element).removeClass('disabled');
                }, this));
                if (this.options.needValidate) {
                    this.drawerForm.removeClass('ignore-validate');
                }
                this.disabledMessage.addClass('hidden');
                $('#' + elemId).prop('checked', true);
            } else {
                $.each(this.options.formsToDisable, $.proxy(function(index, element) {
                    $(element).addClass('disabled');
                }, this));
                if (this.options.needValidate) {
                    this.drawerForm.addClass('ignore-validate');
                }
                this.disabledMessage.removeClass('hidden');
                $('#' + elemId).prop('checked', false);
            }
        }
    });
})(window.jQuery);
