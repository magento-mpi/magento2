/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

;
(function($, document) {
    'use strict';

    $.widget('mage.globalSearch', {
        options: {
            header: '.header',
            headerActiveClass: 'active',
            form: '#form-search',
            input: 'input',
            inputDefaultWidth: 50,
            inputOpenedWidth: 350,
            submitButton: 'button[type="submit"]',
            timeoutId: null,
            actionSpeed: 500
        },

        _create: function() {
            this.header = $(this.options.header);
            this.form = $(this.options.form);
            this.input = $(this.options.input, this.form);
            this.submitButton = $(this.options.submitButton, this.form);

            this._events();
        },

        _events: function() {
            var self = this;

            this.form
                .on('submit.submitGlobalSearchRequest', function() {
                    if (!self.input.val()) {
                        self.header.addClass(self.options.headerActiveClass);
                        self.input
                            .animate({
                                width: self.options.inputOpenedWidth
                            }, self.options.actionSpeed)
                            .focus();
                    } else {
                        this.submit();
                    }

                    return false;
                });

            this.input
                .on('blur.resetGlobalSearchForm', function() {
                    if (!self.input.val()) {
                        self.timeoutId && clearTimeout(self.timeoutId);
                        self.timeoutId = setTimeout(function() {
                            self.input
                                .animate({
                                    width: self.options.inputDefaultWidth
                                }, 200, function() {
                                    var callbackTimeout = setTimeout(function() {
                                        self.header.removeClass(self.options.headerActiveClass);
                                    }, self.options.actionSpeed);
                                });
                        }, self.options.actionSpeed);
                    }
                });

            this.submitButton
                .on('click.activateGlobalSearch', function() {
                    self.timeoutId && clearTimeout(self.timeoutId);
                });
        }
    });

    $.widget('mage.globalNavigation', {
        options: {
            menuCategory: '.level-0',
            menuLinks: 'a',
            itemsConfig: null,
            hoverIntentConfig: {
                interval: 100,
                timeout: 700 // number = milliseconds delay before onMouseOut
            }
        },

        _create: function() {
            this.menu = this.element;
            this.menuCategory = $(this.options.menuCategory, this.menu);
            this.menuLinks = $(this.options.menuLinks, this.menuCategory);
            this._bind();
        },

        _menuCategoryBind: function(category, config) {
            category
                .hoverIntent($.extend({}, this.options.hoverIntentConfig, {
                    over: !config.open ? this._hoverEffects : $.noop,
                    out: !config.close ? this._leaveEffects : $.noop
                }));
            if (config.open) {
                category.on(config.open, this._hoverEffects);
            }
            if (config.close) {
                category.on(config.close, this._leaveEffects);
            }
        },

        _menuCategoryEvents: function() {
            this.menuCategory.each($.proxy(function(i, category) {
                var itemConfig = {};
                if (this.options.categoriesConfig) {
                    $.each(this.options.categoriesConfig, $.proxy(function(selector, conf) {
                        if ($(category).is(selector)) {
                            itemConfig = conf;
                        }
                    }, this));
                }
                this._menuCategoryBind($(category), itemConfig);
            }, this));
        },

        _bind: function() {
            this._menuCategoryEvents();
            this.menuLinks
                .on('focus.tabFocus', function(e) {
                    $(e.target).trigger('mouseenter');
                })
                .on('blur.tabFocus', function(e) {
                    $(e.target).trigger('mouseleave');
                });
        },

        _hoverEffects: function (e) {
            $(this)
                .addClass('hover recent')
                .siblings('.level-0').removeClass('recent hover');

            var targetSubmenu = $(e.target).closest('.submenu');
            if(targetSubmenu.length && targetSubmenu.is(':visible')) {
                return;
            }
            var availableWidth = parseInt($(this).parent().css('width')) - $(this).position().left,
                submenu = $('> .submenu', this),
                colsWidth = 0;

            submenu.show();

            $.each($('> .submenu > ul li.column', this), function() {
                colsWidth = colsWidth + parseInt($(this).css('width'));
            });

            var containerPaddings =  parseInt(submenu.css('padding-left')) + parseInt(submenu.css('padding-right'));

            $(this).toggleClass('reverse', (containerPaddings + colsWidth) > availableWidth);

            submenu
                .hide()
                .slideDown('fast');
        },

        _leaveEffects: function (e) {
            var targetSubmenu = $(e.target).closest('.submenu');
            if(targetSubmenu.length && targetSubmenu.is(':hidden')) {
                return;
            }
            var self = $(this);

            $('> .submenu', this)
                .slideUp('fast', function() {
                    self.removeClass('hover');
                });
        }
    });

    $.widget('mage.modalPopup', {
        options: {
            popup: '.popup',
            btnDismiss: '[data-dismiss="popup"]',
            btnHide: '[data-hide="popup"]'
        },

        _create: function() {
            this.fade = this.element;
            this.popup = $(this.options.popup, this.fade);
            this.btnDismiss = $(this.options.btnDismiss, this.popup);
            this.btnHide = $(this.options.btnHide, this.popup);

            this._events();
        },

        _events: function() {
            var self = this;

            this.btnDismiss
                .on('click.dismissModalPopup', function() {
                    self.fade.remove();
                });

            this.btnHide
                .on('click.hideModalPopup', function() {
                    self.fade.hide();
                });
        }
    });

    $.widget('mage.loadingPopup', {
        options: {
            message: 'Please wait...',
            timeout: 5000,
            timeoutId: null,
            callback: null,
            template: null
        },

        _create: function() {
            this.template =
                '<div class="popup popup-loading">' +
                    '<div class="popup-inner">' + this.options.message + '</div>' +
                '</div>';

            this.popup = $(this.template);

            this._show();
            this._events();
        },

        _events: function() {
            var self = this;

            this.element
                .on('showLoadingPopup', function() {
                    self._show();
                })
                .on('hideLoadingPopup', function() {
                    self._hide();
                });
        },

        _show: function() {
            var self = this;

            this.element.append(this.popup);

            if (this.options.timeout) {
                this.options.timeoutId = setTimeout(function() {
                    self._hide();

                    self.options.callback && self.options.callback();

                    self.options.timeoutId && clearTimeout(self.options.timeoutId);
                }, self.options.timeout);
            }
        },

        _hide: function() {
            this.popup.remove();
            this.destroy();
        }
    });

    $.widget('mage.useDefault', {
        options: {
            field: '.field',
            useDefault: '.use-default',
            checkbox: '.use-default-control',
            label: '.use-default-label'
        },

        _create: function() {
            this.el = this.element;
            this.field = $(this.el).closest(this.options.field);
            this.useDefault = $(this.options.useDefault, this.field);
            this.checkbox = $(this.options.checkbox, this.useDefault);
            this.label = $(this.options.label, this.useDefault);
            this.origValue = this.el.attr('data-store-label');

            this._events();
        },

        _events: function() {
            var self = this;

            this.el
                .on('change.toggleUseDefaultVisibility keyup.toggleUseDefaultVisibility', $.proxy(this._toggleUseDefaultVisibility, this))
                .trigger('change.toggleUseDefaultVisibility');

            this.checkbox
                .on('change.setOrigValue', function() {
                    if ($(this).prop('checked')) {
                        self.el
                            .val(self.origValue)
                            .trigger('change.toggleUseDefaultVisibility');

                        $(this).prop('checked', false);
                    }
                });
        },

        _toggleUseDefaultVisibility: function() {
            var curValue = this.el.val(),
                origValue = this.origValue;

            this[curValue != origValue ? '_show' : '_hide']();
        },

        _show: function() {
            this.useDefault.show();
        },

        _hide: function() {
            this.useDefault.hide();
        }
    });

    $.widget('mage.collapsable', {
        options: {
            parent: null,
            openedClass: 'opened',
            wrapper: '.fieldset-wrapper'
        },

        _create: function() {
            this._events();
        },

        _events: function() {
            var self = this;

            this.element
                .on('show', function (e) {
                    var fieldsetWrapper = $(this).closest(self.options.wrapper);

                    fieldsetWrapper.addClass(self.options.openedClass);
                    e.stopPropagation();
                })
                .on('hide', function (e) {
                    var fieldsetWrapper = $(this).closest(self.options.wrapper);

                    fieldsetWrapper.removeClass(self.options.openedClass);
                    e.stopPropagation();
                });
        }
    });

    var switcherForIe8 = function() {
        /* Switcher for IE8 */
        if ($.browser.msie && $.browser.version == '8.0') {
            $('.switcher input')
                .on('change.toggleSwitcher', function() {
                    $(this)
                        .closest('.switcher')
                        .toggleClass('checked', $(this).prop('checked'));
                })
                .trigger('change');
        }
    };
    var updateColorPickerValues = function() {
        $('.element-color-picker').each(function(){
            var _this = $(this);
            _this.find('.color-box.active').removeClass('active');
            if (_this.find('.farbtastic').is(':visible')) {
                _this
                    .find('.farbtastic').hide()
                    .end()
                    .find('input').trigger('change.quickStyleElement');
            }
        });
    };

    $(document).ready(function() {
        $('.header-panel .search').globalSearch();
        $('.navigation').globalNavigation({
            categoriesConfig: {
                '[data-ui-id="menu-mage-adminhtml-system"]': {
                    open: 'click'
                },
                '[data-ui-id="menu-mage-adminhtml-stores"]': {
                    open: 'click'
                }
            }
        });
        $('.fade').modalPopup();
        $('details').details();
        $('.page-actions').floatingHeader();
        $('[data-store-label]').useDefault();
        $('.collapse').collapsable();

        $.each($('.entry-edit'), function(i, entry) {
            $('.collapse:first', entry).collapse('show');
        });

        // TODO: Move to VDE js widjets
        $.each($('.color-box'), function(index, elem) {
            $(elem).farbtastic(function(color) {
                $(elem).css({
                    'backgroundColor': color
                });
                $(elem).siblings('input').val(color);
            });
        });

        $(document).on('click', function(e) {
            var target = $(e.target);
            if (target.closest('.control').find('.color-box').length < 1) {
                updateColorPickerValues();
            }
        });
        $('.color-box')
            .on('click.showColorPicker', function() {
                updateColorPickerValues();  // Update values is other color picker is not closed yet
                $(this)
                    .addClass('active')
                    .siblings('input').end()
                    .find('.farbtastic').show();
            });
        switcherForIe8();
    });

    $(document).on('ajaxComplete', function() {
        $('details').details();
        switcherForIe8();
    });
})(window.jQuery, document);
