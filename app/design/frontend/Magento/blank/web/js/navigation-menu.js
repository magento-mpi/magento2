/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
/*jshint jquery:true*/

define([
    "jquery",
    "jquery/template",
    "matchMedia",
    "mage/dropdowns",
    "mage/terms"
],function($) {
    'use strict';

    $.widget('mage.navigationMenu', {
        options: {
            itemsContainer: '> ul',
            topLevel: 'li.level0',
            topLevelSubmenu: '> .submenu',
            topLevelHoverClass: 'hover',
            expandedTopLevel: '.more',
            hoverInTimeout: 300,
            hoverOutTimeout: 500,
            submenuAnimationSpeed: 200,
            collapsable: true,
            collapsableDropdownTemplate:
                '<script type="text/x-jquery-tmpl">' +
                    '<li class="level0 level-top more parent">' +
                        '<div class="submenu">' +
                            '<ul>{{html elems}}</ul>' +
                        '</div>' +
                    '</li>' +
                '</script>'
        },

        _create: function() {
            this.itemsContainer = $(this.options.itemsContainer, this.element);
            this.topLevel = $(this.options.topLevel, this.element);
            this.topLevelSubmenu = $(this.options.topLevelSubmenu, this.topLevel);

            this._bind();
        },

        _init: function() {
            if (this.options.collapsable) {
                setTimeout($.proxy(function() {
                    this._checkToCollapseOrExpand();
                }, this), 100);
            }
        },

        _bind: function() {
            this._on({
                'mouseenter > ul > li.level0': function(e) {
                    if (!this.entered) { // fix IE bug with 'mouseenter' event
                        this.timeoutId && clearTimeout(this.timeoutId);
                        this.timeoutId = setTimeout($.proxy(function() {
                            this._openSubmenu(e);
                        }, this), this.options.hoverInTimeout);
                        this.entered = true;
                    }
                },
                'mouseleave > ul > li.level0': function(e) {
                    this.entered = null;

                    this.timeoutId && clearTimeout(this.timeoutId);
                    this.timeoutId = setTimeout($.proxy(function() {
                        this._closeSubmenu(e.currentTarget);
                    }, this), this.options.hoverOutTimeout);
                },
                'click': function(e) {
                    e.stopPropagation();
                }
            });

            $(document)
                .on('click.hideMenu', $.proxy(function(e) {
                    var isOpened = this.topLevel.filter(function() {
                        return $(this).data('opened');
                    });

                    if (isOpened) {
                        this._closeSubmenu(null, false);
                    }
                }, this));

            $(window)
                .on('resize', $.proxy(function() {
                    this.timeoutOnResize && clearTimeout(this.timeoutOnResize);
                    this.timeoutOnResize = setTimeout($.proxy(function() {
                        if (this.options.collapsable) {
                            if ($(this.options.expandedTopLevel, this.element).length) {
                                this._expandMenu();
                            }
                            this._checkToCollapseOrExpand();
                        }
                    }, this), 300);
                }, this));
        },

        _openSubmenu: function(e) {
            var menuItem = e.currentTarget;

            if (!$(menuItem).data('opened')) {
                this._closeSubmenu(menuItem, true, true);

                $(this.options.topLevelSubmenu, menuItem)
                    .slideDown(this.options.submenuAnimationSpeed, $.proxy(function() {
                        $(menuItem).addClass(this.options.topLevelHoverClass);
                        $(menuItem).data('opened', true);
                    }, this));
            } else if ($(e.target).closest(this.options.topLevel)) {
                $(e.target)
                    .addClass(this.options.topLevelHoverClass)
                    .siblings(this.options.topLevel)
                        .removeClass(this.options.topLevelHoverClass);
            }
        },

        _closeSubmenu: function(menuItem, excludeCurrent, fast) {
            var topLevel = $(this.options.topLevel, this.element),
                activeSubmenu = $(this.options.topLevelSubmenu, menuItem || null);

            $(this.options.topLevelSubmenu, topLevel)
                .filter(function() {
                    return excludeCurrent ? $(this).not(activeSubmenu) : true;
                })
                .slideUp(fast ? 0 : this.options.submenuAnimationSpeed);

            topLevel
                .removeClass(this.options.topLevelHoverClass)
                .data('opened', false);
        },

        _checkToCollapseOrExpand: function() {
            if ($("html").hasClass("lt-640") || $("html").hasClass("w-640")) {
                return;
            }

            var navWidth = this.itemsContainer.width(),
                totalWidth = 0,
                startCollapseIndex = 0;

            $.each($(this.options.topLevel, this.element), function(index, item) {
                totalWidth = totalWidth + $(item).outerWidth(true);

                if (totalWidth > navWidth && !startCollapseIndex) {
                    startCollapseIndex = index - 2;
                }
            });

            this[startCollapseIndex ? '_collapseMenu' : '_expandMenu'](startCollapseIndex);
        },

        _collapseMenu: function(startCollapseIndex) {
            this.elemsToCollapse = this.topLevel.filter(function(index) {
                return index > startCollapseIndex;
            });
            this.elemsToCollapseClone = $('<div></div>').append(this.elemsToCollapse.clone()).html();

            this.collapsableDropdown = $(this.options.collapsableDropdownTemplate).tmpl({elems: this.elemsToCollapseClone});

            this.itemsContainer
                .append(this.collapsableDropdown);

            this.elemsToCollapse.detach();
        },

        _expandMenu: function() {
            if ($.browser.version != 8.0) {
                this.elemsToCollapse && this.elemsToCollapse.appendTo(this.itemsContainer);
                this.collapsableDropdown && this.collapsableDropdown.remove();
            } else {
                setTimeout($.proxy(function() {
                    var moreWrapper = $('.more', this.element);

                    if (moreWrapper.length > 1) {
                        moreWrapper.eq(moreWrapper.length - 1).remove();
                    }
                }, this), 1);
            }
        },

        _destroy: function() {
            this._expandMenu();
        }
    });

    /*
     * Provides "Continium" effect for submenu
     * */
    $.widget('mage.navigationMenu', $.mage.navigationMenu, {
        options: {
            parentLevel: '> ul > li.level0',
            submenuAnimationSpeed: 150,
            submenuContiniumEffect: false
        },

        _init: function() {
            this._super();
            this._applySubmenuStyles();
        },

        _applySubmenuStyles: function() {
            $(this.options.topLevelSubmenu, $(this.options.topLevel, this.element))
                .removeAttr('style');

            $(this.options.topLevelSubmenu, $(this.options.parentLevel, this.element))
                .css({
                    display: 'block',
                    height: 0,
                    overflow: 'hidden'
                });
        },

        _openSubmenu: function(e) {
            var menuItem = e.currentTarget,
                submenu = $(this.options.topLevelSubmenu, menuItem),
                openedItems = $(this.options.topLevel, this.element).filter(function() {
                    return $(this).data('opened');
                });

            if (submenu.length) {
                this.heightToAnimate = $(this.options.itemsContainer, submenu).outerHeight(true);

                if (openedItems.length) {
                    this._closeSubmenu(menuItem, true, this.heightToAnimate, $.proxy(function() {
                        submenu.css({
                            height: 'auto'
                        });
                        $(menuItem)
                            .addClass(this.options.topLevelHoverClass);
                    }, this), e);
                } else {
                    submenu.animate({
                        height: this.heightToAnimate
                    }, this.options.submenuAnimationSpeed, $.proxy(function() {
                        $(menuItem)
                            .addClass(this.options.topLevelHoverClass);
                    }, this));
                }

                $(menuItem)
                    .data('opened', true);
            } else {
                this._closeSubmenu(menuItem);
            }
        },

        _closeSubmenu: function(menuItem, excludeCurrent, heightToAnimate, callback, e) {
            var topLevel = $(this.options.topLevel, this.itemsContainer),
                expandedTopLevel = e && $(e.target).closest(this.options.expandedTopLevel);

            if (!excludeCurrent) {
                $(this.options.topLevelSubmenu, $(this.options.parentLevel, this.element))
                    .animate({
                        height: 0
                    });

                topLevel
                    .data('opened', false)
                    .removeClass(this.options.topLevelHoverClass);
            } else {
                var prevOpenedItem = topLevel.filter(function() {
                        return $(this).data('opened');
                    }),
                    prevOpenedSubmenu = $(this.options.topLevelSubmenu, prevOpenedItem);

                prevOpenedSubmenu.animate({
                    height: heightToAnimate
                }, this.options.submenuAnimationSpeed, 'linear', function() {
                    $(this).css({
                        height: 0
                    });
                    callback && callback();
                });

                prevOpenedItem
                    .data('opened', false)
                    .removeClass(this.options.topLevelHoverClass);
            }
        },

        _collapseMenu: function() {
            this._superApply(arguments);
            this._applySubmenuStyles();
        }
    });

    //  Responsive menu
    $.widget('mage.navigationMenu', $.mage.navigationMenu, {
        options: {
            responsive: false,
            origNavPlaceholder: '.page-header',
            mainContainer: 'body',
            pageWrapper: '.page-wrapper',
            openedMenuClass: 'opened',
            toggleActionPlaceholder: '.block-search',
            itemWithSubmenu: 'li.parent',
            titleWithSubmenu: 'li.parent > a',
            submenu: 'li.parent > .submenu',
            toggleActionTemplate:
                '<script type="text/x-jquery-tmpl">' +
                    '<span data-action="toggle-nav" class="action toggle nav">Toggle Nav</span>' +
                '</script>',
            submenuActionsTemplate:
                '<script type="text/x-jquery-tmpl">' +
                    '<li class="action all">' +
                        '<a href="${ categoryURL }"><span>All ${ category }</span></a>' +
                    '</li>' +
                '</script>',
            navigationSectionsWrapperTemplate:
                '<script type="text/x-jquery-tmpl">' +
                    '<dl class="navigation-tabs" data-sections="tabs">' +
                    '</dl>' +
                '</script>',
            navigationItemWrapperTemplate:
                '<script type="text/x-jquery-tmpl">' +
                    '<dt class="item title {{if active}}active{{/if}}" data-section="title">' +
                        '<a class="switch" data-toggle="switch" href="#TODO">${ title }</a>' +
                    '</dt>' +
                    '<dd class="item content {{if active}}active{{/if}}" data-section="content">' +
                    '</dd>' +
                '</script>'
        },

        _init: function() {
            this._super();

            this.mainContainer = $(this.options.mainContainer);
            this.pageWrapper = $(this.options.pageWrapper);
            this.toggleAction = $(this.options.toggleActionTemplate).tmpl({});

            if (this.options.responsive) {
                mediaCheck({
                    media: '(min-width: 768px)',
                    entry: $.proxy(function() {
                        this._toggleDesktopMode();
                    }, this),
                    exit: $.proxy(function() {
                        this._toggleMobileMode();
                    }, this)
                });
            }
        },

        _bind: function() {
            this._super();
            this._bindDocumentEvents();
        },

        _bindDocumentEvents: function() {
            if (!this.eventsBound) {
                $(document)
                    .on('click.toggleMenu', '.action.toggle.nav', $.proxy(function(e) {
                        if ($(this.element).data('opened')) {
                            this._hideMenu();
                        } else {
                            this._showMenu();
                        }
                        e.stopPropagation();
                        this.mobileNav.scrollTop(0);
                        this._fixedBackLink();
                    }, this))
                    .on('click.hideMenu', this.options.pageWrapper, $.proxy(function() {
                        if ($(this.element).data('opened')) {
                            this._hideMenu();
                            this.mobileNav.scrollTop(0);
                            this._fixedBackLink();
                        }
                    }, this))
                    .on('click.showSubmenu', this.options.titleWithSubmenu, $.proxy(function(e) {
                        this._showSubmenu(e);

                        e.preventDefault();
                        this.mobileNav.scrollTop(0);
                        this._fixedBackLink();
                    }, this))
                    .on('click.hideSubmenu', '.action.back', $.proxy(function(e) {
                        this._hideSubmenu(e);
                        this.mobileNav.scrollTop(0);
                        this._fixedBackLink();
                    }, this));

                this.eventsBound = true;
            }
        },

        _showMenu: function() {
            $(this.element).data('opened', true);
            this.mainContainer.add( "html" ).addClass(this.options.openedMenuClass);
        },

        _hideMenu: function() {
            $(this.element).data('opened', false);
            this.mainContainer.add( "html" ).removeClass(this.options.openedMenuClass);
        },

        _showSubmenu: function(e) {
            $(e.currentTarget).addClass('action back');
            var submenu = $(e.currentTarget).siblings('.submenu');

            submenu.addClass('opened');
        },

        _hideSubmenu: function(e) {
            var submenuSelector = '.submenu',
                submenu = $(e.currentTarget).next(submenuSelector);
            $(e.currentTarget).removeClass('action back');
            submenu.removeClass('opened');
        },

        _renderSubmenuActions: function() {
            $.each($(this.options.itemWithSubmenu), $.proxy(function(index, item) {
                var actions = $(this.options.submenuActionsTemplate).tmpl({
                        category: $('> a > span', item).text(),
                        categoryURL: $('> a', item).attr('href')
                    }),
                    submenu = $('> .submenu', item),
                    items = $('> ul', submenu);

                items.prepend(actions);
            }, this));
        },

        _toggleMobileMode: function() {
            this._expandMenu();

            $(this.options.topLevelSubmenu, $(this.options.topLevel, this.element))
                .removeAttr('style');

            this.toggleAction.insertBefore(this.options.toggleActionPlaceholder);
            this.mobileNav = $(this.element).detach().clone();
            this.mainContainer.prepend(this.mobileNav);
            this.mobileNav.find('> ul').addClass('nav');
            this._insertExtraItems();
            this._wrapItemsInSections();
            this.mobileNav.scroll($.proxy(
                function() {
                    this._fixedBackLink();
                }, this
            ));

            this._renderSubmenuActions();
            this._bindDocumentEvents();
        },

        _toggleDesktopMode: function() {
            this.mobileNav && this.mobileNav.remove();
            this.toggleAction.detach();
            $(this.element).insertAfter(this.options.origNavPlaceholder);

            $(document)
                .off('click.toggleMenu', '.action.toggle.nav')
                .off('click.hideMenu', this.options.pageWrapper)
                .off('click.showSubmenu', this.options.titleWithSubmenu)
                .off('click.hideSubmenu', '.action.back');

            this.eventsBound = false;

            this._applySubmenuStyles();
        },

        _insertExtraItems: function() {
            if ($('.header.panel .switcher').length) {
                var settings = $('.header.panel .switcher')
                    .clone()
                    .addClass('settings');

                this.mobileNav.prepend(settings);
            }

            if ($('.footer .switcher').length) {
                var footerSettings = $('.footer .switcher')
                    .clone()
                    .addClass('settings');

                this.mobileNav.prepend(footerSettings);
            }


            if ($('.header.panel .header.links li').length) {
                var account = $('.header.panel > .header.links')
                    .clone()
                    .addClass('account');

                this.mobileNav.prepend(account);
            }
        },

        _wrapItemsInSections: function() {
            var account = $('> .account', this.mobileNav),
                settings = $('> .settings', this.mobileNav),
                nav = $('> .nav', this.mobileNav),
                navigationSectionsWrapper = $(this.options.navigationSectionsWrapperTemplate).tmpl(),
                navigationItemWrapper;

            this.mobileNav.append(navigationSectionsWrapper);

            if (nav.length) {
                navigationItemWrapper = $(this.options.navigationItemWrapperTemplate).tmpl({
                    title: 'Menu'
                });
                navigationSectionsWrapper.append(navigationItemWrapper);
                navigationItemWrapper.eq(1).append(nav);
            }

            if (account.length) {
                navigationItemWrapper = $(this.options.navigationItemWrapperTemplate).tmpl({
                    title: 'Account'
                });
                navigationSectionsWrapper.append(navigationItemWrapper);
                navigationItemWrapper.eq(1).append(account);
            }

            if (settings.length) {
                navigationItemWrapper = $(this.options.navigationItemWrapperTemplate).tmpl({
                    title: 'Settings'
                });
                navigationSectionsWrapper.append(navigationItemWrapper);
                navigationItemWrapper.eq(1).append(settings);
            }

            navigationSectionsWrapper.addClass("navigation-tabs-" + navigationSectionsWrapper.find('[data-section="title"]').length);
            navigationSectionsWrapper.terms();
        },

        _fixedBackLink: function() {
            var linksBack = this.mobileNav.find('.submenu .action.back');
            var linkBack = this.mobileNav.find('.submenu.opened > ul > .action.back').last();

            linksBack.removeClass('fixed');

            if(linkBack.length) {
                var subMenu = linkBack.parent(),
                    navOffset = this.mobileNav.find('.nav').position().top,
                    linkBackHeight = linkBack.height();

                if (navOffset <= 0) {
                    linkBack.addClass('fixed');
                    subMenu.css({
                        paddingTop: linkBackHeight
                    })
                } else {
                    linkBack.removeClass('fixed');
                    subMenu.css({
                        paddingTop: 0
                    })
                }
            }
        }
    });

    return $.mage.navigationMenu;
});
